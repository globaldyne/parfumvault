<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/calcPerc.php');


$defCatClass = $settings['defCatClass'];

// Fetch formula data with owner_id condition
$query1 = "SELECT ingredient, quantity FROM formulas WHERE fid = ? AND owner_id = ?";
$stmt1 = $conn->prepare($query1);
$stmt1->bind_param('ss', $_POST['fid'], $userID);
$stmt1->execute();
$result1 = $stmt1->get_result();

if (!$result1) {
    error_log("PV error: Failed to fetch formula data: " . $stmt1->error);
    echo json_encode(["error" => "Internal server error"]);
    return;
}

$formula_data = [];
$total_quantity = 0;

while ($rq = mysqli_fetch_assoc($result1)) {
    $formula_data[] = $rq;
    $total_quantity += $rq['quantity'];
}

if (empty($formula_data)) {
    $response = ['data' => []];
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    return;
}

// Fetch ingredient compound data with owner_id condition
$ingredients = array_column($formula_data, 'ingredient');
$ingredients_escaped = array_map(function($ingredient) use ($conn) {
    return mysqli_real_escape_string($conn, $ingredient);
}, $ingredients);

$ingredient_list = implode("','", $ingredients_escaped);
$query2 = "SELECT id, ing, name, cas, min_percentage, max_percentage 
           FROM ingredient_compounds 
           WHERE owner_id = ? AND ing IN (" . implode(',', array_fill(0, count($ingredients), '?')) . ")";
           
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param(str_repeat('s', count($ingredients) + 1), $userID, ...$ingredients);
$stmt2->execute();
$result2 = $stmt2->get_result();

if (!$result2) {
    error_log("PV error: Failed to fetch ingredient compounds: " . mysqli_error($conn));
    echo json_encode(["error" => "Internal server error"]);
    return;
}

$get_data_ings = [];
while ($res = mysqli_fetch_assoc($result2)) {
    foreach ($formula_data as $data) {
        if ($res['ing'] === $data['ingredient']) {
            $res['quantity'] = $data['quantity'];
            $get_data_ings[] = $res;
            break;
        }
    }
}

// Prepare response data
$response = ['data' => []];
$ingredientIds = [];

foreach ($get_data_ings as $get_data_ing) {
    $r = [];

    // Fetch ingredient ID with owner_id condition
    if (!isset($ingredientIds[$get_data_ing['ing']])) {
        $query3 = "SELECT id FROM ingredients WHERE name = ? AND owner_id = ?";
        $stmt3 = $conn->prepare($query3);
        $stmt3->bind_param('ss', $get_data_ing['ing'], $userID);
        $stmt3->execute();
        $result3 = $stmt3->get_result();

        if (!$result3) {
            error_log("PV error: Failed to fetch ingredient ID: " . mysqli_error($conn));
            echo json_encode(["error" => "Internal server error"]);
            return;
        }

        $ingID = mysqli_fetch_assoc($result3);
        $ingredientIds[$get_data_ing['ing']] = isset($ingID['id']) ? (int)$ingID['id'] : null;
    }

    $r['id'] = $ingredientIds[$get_data_ing['ing']];
    $r['main_ing'] = (string)$get_data_ing['ing'];
    $r['sub_ing'] = (string)$get_data_ing['name'];
    $r['cas'] = (string)($get_data_ing['cas'] ?? '-');
    $r['min_percentage'] = isset($get_data_ing['min_percentage']) ? (float)$get_data_ing['min_percentage'] : 0.0;
    $r['max_percentage'] = isset($get_data_ing['max_percentage']) ? (float)$get_data_ing['max_percentage'] : 0.0;

    // Calculate average percentage
    if ($r['min_percentage'] > 0 && $r['max_percentage'] > 0) {
        $r['avg_percentage'] = ($r['min_percentage'] + $r['max_percentage']) / 2.0;
    } elseif ($r['max_percentage'] > 0) {
        $r['avg_percentage'] = $r['max_percentage'];
    } elseif ($r['min_percentage'] > 0) {
        $r['avg_percentage'] = $r['min_percentage'];
    } else {
        $r['avg_percentage'] = 0.0;
    }

    // Calculate formula percentage (ingredient's share in the formula)
    $quantity = isset($get_data_ing['quantity']) ? (float)$get_data_ing['quantity'] : 0.0;
    $r['formula_percentage'] = ($total_quantity > 0) ? ($quantity / $total_quantity) * 100.0 : 0.0;

    // Calculate contained percentage (how much of the compound is in the formula)
    // This assumes avg_percentage is the percentage of the compound in the ingredient
    // and formula_percentage is the percentage of the ingredient in the formula.
    $contained_percentage = ($r['avg_percentage'] / 100.0) * $r['formula_percentage'];

    // If multi_dim_perc is enabled, add its result
    if ($settings['multi_dim_perc'] == '1') {
        $multi_dim_result = multi_dim_perc($conn, $formula_data, $get_data_ing['cas'], $settings['qStep'], $settings['defPercentage']);
        if (isset($multi_dim_result[$get_data_ing['cas']])) {
            $contained_percentage += (float)$multi_dim_result[$get_data_ing['cas']];
        }
    }

    $r['contained_percentage'] = round($contained_percentage, 5);

    // IFRA search and limits
    $u = searchIFRA($get_data_ing['cas'], $get_data_ing['name'], null, $defCatClass);
    $r['max_allowed_val'] = $u['val'] ?? ($u['type'] ?? 'No value');
    $r['max_allowed_reason'] = $u['risk'] ?? '';

    $response['data'][] = $r;
}

// Output the response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;

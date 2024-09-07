<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/calcPerc.php');

$defCatClass = $settings['defCatClass'];

$stmt = $conn->prepare("SELECT ingredient, quantity FROM formulas WHERE fid = ?");
$stmt->bind_param("s", $_POST["fid"]);
$stmt->execute();
$result = $stmt->get_result();

$formula_data = array();
$total_quantity = 0;

while ($rq = $result->fetch_assoc()) {
    $formula_data[] = $rq;
    $total_quantity += $rq['quantity'];
}

$stmt->close();

if (empty($formula_data)) {
    $response = ['data' => []];
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    return;
}

$ingredients = array_column($formula_data, 'ingredient');
$placeholders = implode(',', array_fill(0, count($ingredients), '?'));
$stmt2 = $conn->prepare("SELECT id, ing, name, cas, min_percentage, max_percentage FROM ingredient_compounds WHERE ing IN ($placeholders)");
$stmt2->bind_param(str_repeat('s', count($ingredients)), ...$ingredients);
$stmt2->execute();
$result2 = $stmt2->get_result();

$get_data_ings = array();
while ($res = $result2->fetch_assoc()) {
    foreach ($formula_data as $data) {
        if ($res['ing'] === $data['ingredient']) {
            $res['quantity'] = $data['quantity'];
            $get_data_ings[] = $res;
            break;
        }
    }
}

$stmt2->close();

$response = ['data' => []];
$ingredientIds = [];

foreach ($get_data_ings as $get_data_ing) {
    $r = array();

    // Cache ingredient ID lookups to reduce redundant queries
    if (!isset($ingredientIds[$get_data_ing['ing']])) {
        $stmt3 = $conn->prepare("SELECT id FROM ingredients WHERE name = ?");
        $stmt3->bind_param("s", $get_data_ing['ing']);
        $stmt3->execute();
        $ingredientIdResult = $stmt3->get_result();
        $ingID = $ingredientIdResult->fetch_assoc();
        $ingredientIds[$get_data_ing['ing']] = (int)$ingID['id'];
        $stmt3->close();
    }

    $r['id'] = $ingredientIds[$get_data_ing['ing']];
    $r['main_ing'] = (string)$get_data_ing['ing'];
    $r['sub_ing'] = (string)$get_data_ing['name'];
    $r['cas'] = (string)$get_data_ing['cas'] ?: 'N/A';
    $r['min_percentage'] = (float)$get_data_ing['min_percentage'] ?: 0;
    $r['max_percentage'] = (float)$get_data_ing['max_percentage'] ?: 0;
    $r['avg_percentage'] = ($r['min_percentage'] + $r['max_percentage']) / 2;
    $r['formula_percentage'] = ($get_data_ing['quantity'] / $total_quantity) * 100;

    $conc_p = number_format(($r['avg_percentage'] / 100 * $get_data_ing['quantity'] * $r['formula_percentage']) / 100, 5);

    if ($settings['multi_dim_perc'] == '1') {
        $multi_dim_result = multi_dim_perc($conn, $formula_data, $get_data_ing['cas'], $settings['qStep'], $settings['defPercentage']);
        $conc_p += $multi_dim_result[$get_data_ing['cas']] ?? 0;
    }

    $r['contained_percentage'] = $conc_p;

    $u = searchIFRA($get_data_ing['cas'], $get_data_ing['name'], null, $defCatClass);
    $r['max_allowed_val'] = $u['val'] ?? $u['type'] ?? 'No value';
    $r['max_allowed_reason'] = $u['risk'] ?? '';

    $response['data'][] = $r;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;

?>
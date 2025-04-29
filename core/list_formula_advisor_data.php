<?php
/**
 * File: list_formula_analysis_data.php
 * Description: Advisory backend for formula.
 * Author: John B.
 * License: MIT License
 * Copyright: 2025 John B.
 */

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


// Fetch formula data with owner_id condition
$formula_query = "SELECT ingredient, quantity FROM formulas WHERE fid = ? AND owner_id = ?";
$formula_stmt = $conn->prepare($formula_query);
$formula_stmt->bind_param('ss', $_POST['fid'], $userID);
$formula_stmt->execute();
$formula_result = $formula_stmt->get_result();

if (!$formula_result) {
    error_log("PV error: Failed to fetch advisor formula data: " . $formula_stmt->error);
    echo json_encode(["error" => "Internal server error"]);
    return;
}

$formula_data = [];
$total_quantity = 0;

while ($rq = mysqli_fetch_assoc($formula_result)) {
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
$query = "SELECT ing, name FROM ingredient_compounds WHERE owner_id = ? AND name IN (" . implode(',', array_fill(0, count($ingredients), '?')) . ")";
$stmt = $conn->prepare($query);
$stmt->bind_param(str_repeat('s', count($ingredients) + 1), $userID, ...$ingredients);
$stmt->execute();
$result = $stmt->get_result();


if (!$result) {
    error_log("PV error: Failed to fetch ingredient compounds: " . mysqli_error($conn));
    echo json_encode(["error" => "Internal server error"]);
    return;
}

$get_data_ings = [];
while ($res = mysqli_fetch_assoc($result)) {
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

    $r['ingredient'] = (string)$get_data_ing['name'];
    $r['quantity'] = (double)$get_data_ing['quantity'];

    // Check for duplicate ingredient name
    if (in_array($get_data_ing['ing'], $ingredients)) {
        $r['advisory'] = "duplicate";
        $r['recommendation'] = "Consider removing";
    } else {
        $r['advisory'] = "-";
        $r['recommendation'] = "-";
    }

    $response['data'][] = $r;
}

// Output the response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;

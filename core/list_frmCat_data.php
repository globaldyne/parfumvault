<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$cat_q = mysqli_query($conn, "SELECT id, name, cname, type, colorKey FROM formulaCategories WHERE owner_id = '$userID'");

$response = ['data' => []];

while ($category = mysqli_fetch_assoc($cat_q)) {
    $response['data'][] = [
        'id' => (int)$category['id'],
        'name' => $category['name'],
        'cname' => $category['cname'],
        'type' => $category['type'],
        'colorKey' => 'rgba(' . $category['colorKey'] . ')'
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;

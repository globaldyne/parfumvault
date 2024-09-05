<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/countElement.php');

$cat_q = mysqli_query($conn, "SELECT id, name, cname, type, colorKey FROM formulaCategories");

$cats = [];
$response = ['data' => []];

while ($cats_res = mysqli_fetch_assoc($cat_q)) {
    $cats[] = $cats_res;
}

foreach ($cats as $category) { 
    $r = [
        'id' => (int)$category['id'],
        'name' => (string)$category['name'],
        'cname' => (string)$category['cname'],
        'type' => (string)$category['type'],
        'count' => (int)countElement("formulasMetaData WHERE profile = '" . mysqli_real_escape_string($conn, $category['cname']) . "'", $conn) ?: 0,
        'colorKey' => 'rgba(' . $category['colorKey'] . ')',
        'borderColor' => 'rgba(255, 99, 132, 1)'
    ];
    
    $response['data'][] = $r;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>
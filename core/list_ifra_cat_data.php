<?php
define('__ROOT__', dirname(dirname(__FILE__))); 
//PUBLIC ACCESS
require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');


$data = [];

$q = mysqli_query($conn, "SELECT id, name, description FROM IFRACategories");
while ($category = mysqli_fetch_assoc($q)){
    $c = [
        'id' => (int)$category['id'],
        'name' => (string)$category['name'],
        'description' => (string)$category['description']
    ];
    $data[] = $c;
}

$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM IFRACategories"));

$response = array(
    "draw" => (int)$_POST['draw'],
    "recordsTotal" => (int)$total['entries'],
    "recordsFiltered" => (int)$total['entries'],
    "data" => $data
);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;


?>

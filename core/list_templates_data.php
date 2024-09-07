<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');


$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$order_by = isset($_POST['order_by']) ? mysqli_real_escape_string($conn, $_POST['order_by']) : 'name';
$order_as = isset($_POST['order_as']) ? mysqli_real_escape_string($conn, $_POST['order_as']) : 'ASC';
$search_value = isset($_POST['search']['value']) ? trim(mysqli_real_escape_string($conn, $_POST['search']['value'])) : '';

$table = "templates";
$extra = "ORDER BY $order_by $order_as";

$filter = $search_value !== '' ? "WHERE name LIKE '%$search_value%'" : '';

$query = mysqli_query($conn, "SELECT * FROM $table $filter $extra LIMIT $row, $limit");

$rs = [];
while ($res = mysqli_fetch_assoc($query)) {
    $rs[] = $res;
}

$rx = [];
foreach ($rs as $rq) { 
    $rx[] = [
        'id' => (int)$rq['id'],
        'name' => (string)$rq['name'],
        'content' => (string)$rq['content'],
        'created' => (string)$rq['created'],
        'updated' => (string)$rq['updated'],
        'description' => (string)$rq['description']
    ];
}

$total_query = mysqli_query($conn, "SELECT COUNT(id) AS entries FROM $table");
$total = mysqli_fetch_assoc($total_query)['entries'];

$filtered_query = mysqli_query($conn, "SELECT COUNT(id) AS entries FROM $table $filter");
$filtered = mysqli_fetch_assoc($filtered_query)['entries'];

$response = [
    "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
    "recordsTotal" => (int)$total,
    "recordsFiltered" => (int)$filtered,
    "data" => $rx
];

if (empty($rx)) {
    $response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

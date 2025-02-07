<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$order_by = isset($_POST['order_by']) ? mysqli_real_escape_string($conn, $_POST['order_by']) : 'product_name';
$order = isset($_POST['order_as']) ? mysqli_real_escape_string($conn, $_POST['order_as']) : 'ASC';
$search_value = isset($_POST['search']['value']) ? trim(mysqli_real_escape_string($conn, $_POST['search']['value'])) : '';

$table = 'sds_data';
$extra = "ORDER BY $order_by $order";

$filter = $search_value !== '' ? "WHERE product_name LIKE '%$search_value%' AND owner_id = '$userID' " : " WHERE owner_id = '$userID' ";

$query = mysqli_query($conn, "SELECT * FROM $table $filter $extra LIMIT $row, $limit");

$rs = [];
while ($res = mysqli_fetch_assoc($query)) {
    $rs[] = $res;
}

$rx = [];
foreach ($rs as $rq) { 
    $rx[] = [
        'id' => (int)$rq['id'],
        'product_name' => (string)$rq['product_name'],
        'product_use' => (string)$rq['product_use'],
        'country' => (string)$rq['country'],
        'language' => (string)$rq['language'],
        'product_type' => (string)$rq['product_type'],
        'state_type' => (string)$rq['state_type'],
        'supplier_id' => (int)$rq['supplier_id'],
        'docID' => (int)$rq['id'],
        'updated_at' => (string)($rq['updated_at'] ?: '00:00:00'),
        'created_at' => (string)($rq['created_at'] ?: '00:00:00'),
    ];
}

$total_query = mysqli_query($conn, "SELECT COUNT(id) AS entries FROM $table WHERE owner_id = '$userID' ");
$total = mysqli_fetch_assoc($total_query)['entries'];

$filtered_query = mysqli_query($conn, "SELECT COUNT(id) AS entries FROM $table $filter");
$filtered = mysqli_fetch_assoc($filtered_query)['entries'];

$response = [
    "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
    "recordsTotal" => (int)$total,
    "recordsFiltered" => (int)$filtered,
    "data" => $rx
];

if(empty($rx)){
    $response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>


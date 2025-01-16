<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 20;
$order_by = isset($_POST['order_by']) ? mysqli_real_escape_string($conn, $_POST['order_by']) : 'name';
$order = isset($_POST['order_as']) ? mysqli_real_escape_string($conn, $_POST['order_as']) : 'ASC';
$search_value = isset($_POST['search']['value']) ? trim(mysqli_real_escape_string($conn, $_POST['search']['value'])) : '';

$table = "ingSuppliers";
$extra = "ORDER BY $order_by $order";

$filter = $search_value !== '' ? "WHERE name LIKE '%$search_value%' AND owner_id = '$userID' " : " WHERE owner_id = '$userID' ";

$query = mysqli_query($conn, "SELECT * FROM $table $filter $extra LIMIT $row, $limit");

$rs = [];
while ($res = mysqli_fetch_assoc($query)) {
    $rs[] = $res;
}

$rx = [];
foreach ($rs as $rq) {
    $mt_query = mysqli_query($conn, "SELECT COUNT(id) AS mt FROM suppliers WHERE ingSupplierID = '" . (int)$rq['id'] . "' AND owner_id = '$userID'");
    $mt = mysqli_fetch_assoc($mt_query);

    $rx[] = [
        'id' => (int)$rq['id'],
        'name' => (string)$rq['name'],
        'materials' => (int)$mt['mt'] ?: 0,
        'address' => (string)($rq['address'] ?: '-'),
        'po' => (string)($rq['po'] ?: '-'),
        'country' => (string)$rq['country'],
        'telephone' => (string)($rq['telephone'] ?: '-'),
        'url' => (string)($rq['url'] ?: '-'),
        'email' => (string)($rq['email'] ?: '-'),
        'platform' => (string)($rq['platform'] ?: '-'),
        'price_tag_start' => (string)base64_encode($rq['price_tag_start'] ?: '-'),
        'price_tag_end' => (string)base64_encode($rq['price_tag_end'] ?: '-'),
        'add_costs' => (float)($rq['add_costs'] ?: 0),
        'notes' => (string)($rq['notes'] ?: '-'),
        'min_ml' => (float)($rq['min_ml'] ?: 0),
        'min_gr' => (float)($rq['min_gr'] ?: 0),
        'price_per_size' => $rq['price_per_size'] == '0' ? 'Product' : 'Volume',
    ];
}

$total_query = mysqli_query($conn, "SELECT COUNT(id) AS entries FROM $table WHERE owner_id = '$userID'");
$total = mysqli_fetch_assoc($total_query)['entries'];

$filtered_query = mysqli_query($conn, "SELECT COUNT(id) AS entries FROM $table $filter");
$filtered = mysqli_fetch_assoc($filtered_query)['entries'];

$response = [
    "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
    "recordsTotal" => (int)$total,
    "recordsFiltered" => (int)$filtered,
    "data" => $rx ?: []
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>
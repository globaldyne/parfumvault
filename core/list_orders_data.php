<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 20;
$order_by = isset($_POST['order_by']) ? mysqli_real_escape_string($conn, $_POST['order_by']) : 'order_id';
$order = isset($_POST['order_as']) ? mysqli_real_escape_string($conn, $_POST['order_as']) : 'ASC';
$search_value = isset($_POST['search']['value']) ? trim(mysqli_real_escape_string($conn, $_POST['search']['value'])) : '';

$table = "orders";
$extra = "ORDER BY $order_by $order";

$filter = $search_value !== '' ? "WHERE order_id LIKE '%$search_value%' AND owner_id = '$userID' " : " WHERE owner_id = '$userID' ";

$query = mysqli_query($conn, "SELECT *, (SELECT COUNT(*) FROM order_items WHERE order_items.order_id = orders.id) AS items FROM $table $filter $extra LIMIT $row, $limit");

$rs = [];
while ($res = mysqli_fetch_assoc($query)) {
    $rs[] = $res;
}

$rx = [];
foreach ($rs as $rq) {
    // Calculate total cost
    $order_id = (int)$rq['id'];
    $shipping = (float)$rq['shipping'];
    $tax_percent = (float)$rq['tax'];
    $discount = (float)$rq['discount'];

    // Get the sum of prices from order_items
    $items_query = mysqli_query($conn, "SELECT SUM(unit_price) AS items_total FROM order_items WHERE order_id = $order_id");
    $items_total = mysqli_fetch_assoc($items_query)['items_total'] ?: 0;

    // Calculate total cost
    $subtotal = $items_total + $shipping;
    $tax = ($tax_percent / 100) * $subtotal;
    $total = $subtotal + $tax - $discount;

    $rx[] = [
        'id' => $order_id,
        'order_id' => (string)$rq['order_id'],
        'reference_number' => (string)$rq['reference_number'] ?: '-',
        'supplier' => (string)$rq['supplier'],
        'status' => (string)($rq['status'] ?: 'pending'),
        'total' => $total,
        'currency' => (string)($rq['currency'] ?: $settings['currency']),
        'items' => (int)($rq['items'] ?: 0),
        'placed' => (string)($rq['placed'] ?: '-'),
        'received' => (string)($rq['received'] ?: '-'),
        'notes' => (string)($rq['notes'] ?: '-'),
    ];
}

$total_query = mysqli_query($conn, "SELECT COUNT(order_id) AS entries FROM $table WHERE owner_id = '$userID'");
$total = mysqli_fetch_assoc($total_query)['entries'];

$filtered_query = mysqli_query($conn, "SELECT COUNT(order_id) AS entries FROM $table $filter");
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
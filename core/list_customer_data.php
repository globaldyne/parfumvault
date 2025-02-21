<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = isset($_POST['start']) ? max(0, (int)$_POST['start']) : 0;
$limit = isset($_POST['length']) ? max(1, (int)$_POST['length']) : 10;

$allowed_columns = ['id', 'name', 'address', 'phone', 'email', 'web', 'created_at', 'updated_at'];
$order_by = isset($_POST['order_by']) && in_array($_POST['order_by'], $allowed_columns) ? $_POST['order_by'] : 'name';
$order = isset($_POST['order_as']) && in_array(strtoupper($_POST['order_as']), ['ASC', 'DESC']) ? strtoupper($_POST['order_as']) : 'ASC';

$extra = "ORDER BY `$order_by` $order";
$s = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';

$f = "WHERE owner_id = '$userID'";
if ($s !== '') {
    $s = mysqli_real_escape_string($conn, $s);
    $f .= " AND (name LIKE '%$s%' OR address LIKE '%$s%' OR phone LIKE '%$s%' OR email LIKE '%$s%')";
}

// Fetch paginated results
$query = "SELECT id, name, address, phone, email, web, created_at, updated_at FROM customers $f $extra LIMIT $row, $limit";
$q = mysqli_query($conn, $query);
if (!$q) {
    error_log("PV error: Query execution failed: " . mysqli_error($conn));
    echo json_encode(["error" => "Internal server error"]);
    return;
}

$rs = [];
while ($res = mysqli_fetch_assoc($q)) {
    $rs[] = [
        'id' => (int)$res['id'],
        'name' => $res['name'] ?: '-',
        'address' => $res['address'] ?: '-',
        'phone' => $res['phone'] ?: '-',
        'email' => $res['email'] ?: '-',
        'web' => $res['web'] ?: '-',
        'created_at' => $res['created_at'] ?: '00:00:00',
        'updated_at' => $res['updated_at'] ?: '00:00:00',
    ];
}

// Total records query
$total_query = "SELECT COUNT(id) AS entries FROM customers WHERE owner_id = '$userID'";
$total_result = mysqli_query($conn, $total_query);
$total = ($total_result) ? mysqli_fetch_assoc($total_result)['entries'] ?? 0 : 0;

// Filtered records query
$filtered_query = "SELECT COUNT(id) AS entries FROM customers $f";
$filtered_result = mysqli_query($conn, $filtered_query);
$filtered = ($filtered_result) ? mysqli_fetch_assoc($filtered_result)['entries'] ?? 0 : 0;

// Build the response
$response = [
    "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
    "recordsTotal" => (int)$total,
    "recordsFiltered" => (int)$filtered,
    "data" => $rs ?: []
];

// Output the response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;

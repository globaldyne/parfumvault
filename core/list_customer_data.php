<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;

$defCatClass = $settings['defCatClass'];
$defImage = base64_encode(file_get_contents(__ROOT__ . '/img/pv_molecule.png'));

$order_by = isset($_POST['order_by']) ? $_POST['order_by'] : 'name';
$order = isset($_POST['order_as']) && in_array(strtoupper($_POST['order_as']), ['ASC', 'DESC']) ? strtoupper($_POST['order_as']) : 'ASC';
$extra = "ORDER BY `$order_by` $order";

$s = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
$f = "WHERE owner_id = '$userID'";
if ($s !== '') {
    $s = mysqli_real_escape_string($conn, $s);
    $f .= " AND (name LIKE '%$s%')";
}

// Fetch paginated results
$query = "SELECT * FROM customers $f $extra LIMIT $row, $limit";
$q = mysqli_query($conn, $query);
if (!$q) {
    error_log("PV error: Query execution failed: " . mysqli_error($conn));
    echo json_encode(["error" => "Internal server error"]);
    return;
}

$rs = [];
while ($res = mysqli_fetch_assoc($q)) {
    $rs[] = $res;
}

// Process results
$rx = [];
foreach ($rs as $rq) {
    $rx[] = [
        'id' => (int)$rq['id'],
        'name' => (string)$rq['name'] ?: '-',
        'address' => (string)$rq['address'] ?: '-',
        'phone' => (string)$rq['phone'] ?: '-',
        'email' => (string)$rq['email'] ?: '-',
        'web' => (string)$rq['web'] ?: '-',
        'created_at' => (string)$rq['created_at'] ?: '00:00:00',
        'updated_at' => (string)$rq['updated_at'] ?: '00:00:00',
    ];
}

// Total records query
$total_query = "SELECT COUNT(id) AS entries FROM customers WHERE owner_id = '$userID'";
$total_result = mysqli_query($conn, $total_query);
if (!$total_result) {
    error_log("PV error: Total query failed: " . mysqli_error($conn));
    echo json_encode(["error" => "Internal server error"]);
    return;
}
$total = mysqli_fetch_assoc($total_result)['entries'] ?? 0;

// Filtered records query
$filtered_query = "SELECT COUNT(id) AS entries FROM customers $f";
$filtered_result = mysqli_query($conn, $filtered_query);
if (!$filtered_result) {
    error_log("PV error: Filtered query failed: " . mysqli_error($conn));
    echo json_encode(["error" => "Internal server error"]);
    return;
}
$filtered = mysqli_fetch_assoc($filtered_result)['entries'] ?? 0;

// Build the response
$response = [
    "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
    "recordsTotal" => (int)$total,
    "recordsFiltered" => (int)$filtered,
    "data" => $rx ?: []
];

// Output the response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;

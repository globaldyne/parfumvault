<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$order_by = isset($_POST['order_by']) ? $_POST['order_by'] : 'created';
$order = isset($_POST['order_as']) && in_array(strtoupper($_POST['order_as']), ['ASC', 'DESC']) ? strtoupper($_POST['order_as']) : 'ASC';
$search_value = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';

// Construct SQL parts
$extra = "ORDER BY `$order_by` $order";
$where = "WHERE owner_id = '$userID'";

if (!empty($search_value)) {
    $search_value = mysqli_real_escape_string($conn, $search_value);
    $where .= " AND (id LIKE '%$search_value%' OR product_name LIKE '%$search_value%')";
}

// Main query with pagination
$query = "SELECT * FROM batchIDHistory $where $extra LIMIT $row, $limit";
$result = mysqli_query($conn, $query);

if (!$result) {
    error_log("PV error: Query execution failed: " . mysqli_error($conn));
    echo json_encode(["error" => "Internal server error"]);
    return;
}

// Process results
$rs = [];
while ($res = mysqli_fetch_assoc($result)) {
    $state = file_exists(__ROOT__ . '/' . $res['pdf']) ? 1 : 0;
    $rs[] = [
        'id' => (string)$res['id'],
        'fid' => (string)$res['fid'],
        'product_name' => $res['product_name'] ?: '-',
        'pdf' => (string)$res['pdf'],
        'state' => (int)$state,
        'created' => (string)$res['created']
    ];
}

// Get total records
$total_query = "SELECT COUNT(id) AS entries FROM batchIDHistory WHERE owner_id = '$userID'";
$total_result = mysqli_query($conn, $total_query);
if (!$total_result) {
    error_log("PV error: Total query failed: " . mysqli_error($conn));
    echo json_encode(["error" => "Internal server error"]);
    return;
}
$total_entries = mysqli_fetch_assoc($total_result)['entries'] ?? 0;

// Get filtered records
$filtered_query = "SELECT COUNT(id) AS entries FROM batchIDHistory $where";
$filtered_result = mysqli_query($conn, $filtered_query);
if (!$filtered_result) {
    error_log("PV error: Filtered query failed: " . mysqli_error($conn));
    echo json_encode(["error" => "Internal server error"]);
    return;
}
$filtered_entries = mysqli_fetch_assoc($filtered_result)['entries'] ?? 0;

// Response
$response = [
    "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
    "recordsTotal" => (int)$total_entries,
    "recordsFiltered" => (int)$filtered_entries,
    "data" => $rs
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);

return;

?>

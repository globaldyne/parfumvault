<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$order_by = isset($_POST['order_by']) ? mysqli_real_escape_string($conn, $_POST['order_by']) : 'name';
$order_as = isset($_POST['order_as']) ? mysqli_real_escape_string($conn, $_POST['order_as']) : 'ASC';
$search_value = isset($_POST['search']['value']) ? trim(mysqli_real_escape_string($conn, $_POST['search']['value'])) : '';

$table = "templates";
$extra = "ORDER BY $order_by $order_as";

// Sanitize and validate the search value
$filter = $search_value !== '' ? "WHERE name LIKE ? AND owner_id = ?" : "WHERE owner_id = ?";

// Assign values to variables for binding
$search_value_param = "%$search_value%";
$user_id_param = $userID;
$row_param = (int)$row;
$limit_param = (int)$limit;

// Prepare the SQL query to select data
$stmt = $conn->prepare("SELECT * FROM $table $filter $extra LIMIT ?, ?");
if ($search_value !== '') {
    $stmt->bind_param("ssii", $search_value_param, $user_id_param, $row_param, $limit_param);
} else {
    $stmt->bind_param("sii", $user_id_param, $row_param, $limit_param);
}

// Execute the query
$stmt->execute();
$query = $stmt->get_result();


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
        'created' => (string)$rq['created_at'],
        'updated' => (string)$rq['updated_at'],
        'description' => (string)$rq['description']
    ];
}

// Get total records count
$stmtTotal = $conn->prepare("SELECT COUNT(id) AS entries FROM $table WHERE owner_id = ?");
$stmtTotal->bind_param("s", $userID);
$stmtTotal->execute();
$total_query = $stmtTotal->get_result();
$total = $total_query->fetch_assoc()['entries'];

// Get filtered records count
$stmtFiltered = $conn->prepare("SELECT COUNT(id) AS entries FROM $table $filter");
$stmtFiltered->bind_param("s", $userID);
if ($search_value !== '') {
    $stmtFiltered->bind_param("ss", "%$search_value%", $userID);
}
$stmtFiltered->execute();
$filtered_query = $stmtFiltered->get_result();
$filtered = $filtered_query->fetch_assoc()['entries'];

// Prepare the final response
$response = [
    "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
    "recordsTotal" => (int)$total,
    "recordsFiltered" => (int)$filtered,
    "data" => $rx
];

// If no data, return an empty array
if (empty($rx)) {
    $response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
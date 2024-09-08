<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;

$order_by = isset($_POST['order_by']) ? $_POST['order_by'] : 'created';
$order = isset($_POST['order_as']) && in_array(strtoupper($_POST['order_as']), ['ASC', 'DESC']) ? strtoupper($_POST['order_as']) : 'ASC';

$extra = "ORDER BY $order_by $order";

$search_value = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
$f = '';

if (!empty($search_value)) {
    $f = "WHERE id LIKE ? OR product_name LIKE ?";
}

$query = "SELECT * FROM batchIDHistory $f $extra LIMIT ?, ?";
$stmt = $conn->prepare($query);

if (!empty($search_value)) {
    $search_param = "%$search_value%";
    $stmt->bind_param('ssii', $search_param, $search_param, $row, $limit);
} else {
    $stmt->bind_param('ii', $row, $limit);
}

$stmt->execute();
$result = $stmt->get_result();

$rs = [];
while ($res = $result->fetch_assoc()) {
    $rs[] = $res;
}

$rx = [];
foreach ($rs as $rq) {
    $state = 0;
    if (file_exists(__ROOT__.'/'.$rq['pdf'])) {
        $state = 1;
    }

    $r = [
        'id' => (string)$rq['id'],
        'fid' => (string)$rq['fid'],
        'product_name' => (string)$rq['product_name'] ?: 'N/A',
        'pdf' => (string)$rq['pdf'],
        'state' => (int)$state,
        'created' => (string)$rq['created']
    ];

    $rx[] = $r;
}

$total_query = "SELECT COUNT(id) AS entries FROM batchIDHistory";
$total_result = mysqli_fetch_assoc(mysqli_query($conn, $total_query));

$filtered_query = "SELECT COUNT(id) AS entries FROM batchIDHistory $f";
$filtered_stmt = $conn->prepare($filtered_query);

if (!empty($search_value)) {
    $filtered_stmt->bind_param('ss', $search_param, $search_param);
}
$filtered_stmt->execute();
$filtered_result = $filtered_stmt->get_result();
$filtered = $filtered_result->fetch_assoc();

$response = [
    "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
    "recordsTotal" => (int)$total_result['entries'],
    "recordsFiltered" => (int)$filtered['entries'],
    "data" => !empty($rx) ? $rx : []
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);

return;

?>

<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;

$order_by = isset($_POST['order_by']) ? $_POST['order_by'] : 'name';
$order = isset($_POST['order_as']) && in_array(strtoupper($_POST['order_as']), ['ASC', 'DESC']) ? strtoupper($_POST['order_as']) : 'ASC';
$extra = "ORDER BY `$order_by` $order";

$defCatClass = $settings['defCatClass'];
$defImage = base64_encode(file_get_contents(__ROOT__ . '/img/pv_molecule.png'));

$s = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
$f = "WHERE owner_id = '$userID'";
if ($s !== '') {
    $s = mysqli_real_escape_string($conn, $s);
    $f .= " AND (name LIKE '%$s%')";
}

$q = mysqli_query($conn, "SELECT * FROM bottles $f $extra LIMIT $row, $limit");
if (!$q) {
    error_log("PV error: Query execution failed: " . mysqli_error($conn));
    echo json_encode(["error" => "Internal server error"]);
    return;
}

$rs = [];
while ($res = mysqli_fetch_assoc($q)) {
    $rs[] = $res;
}

$rx = [];
foreach ($rs as $rq) {
    $r = [
        'id' => (int)$rq['id'],
        'name' => (string)$rq['name'] ?: '-',
        'price' => (double)$rq['price'] ?: '-',
        'ml' => (double)$rq['ml'] ?: 0,
        'height' => (double)$rq['height'] ?: 0,
        'width' => (double)$rq['width'] ?: 0,
        'diameter' => (double)$rq['diameter'] ?: 0,
        'weight' => (double)$rq['weight'] ?: 0,
        'supplier' => (string)$rq['supplier'] ?: '-',
        'supplier_link' => (string)$rq['supplier_link'] ?: '-',
        'notes' => (string)$rq['notes'] ?: '-',
        'pieces' => (int)$rq['pieces'] ?: 0,
        'created_at' => (string)$rq['created_at'] ?: '00:00:00',
        'updated_at' => (string)$rq['updated_at'] ?: '00:00:00',
    ];

    $photo_query = "SELECT docData FROM documents WHERE type = '4' AND ownerID = '" . $r['id'] . "' AND owner_id = '$userID'";
    $photo_result = mysqli_query($conn, $photo_query);
    if (!$photo_result) {
        error_log("PV error: Photo query failed: " . mysqli_error($conn));
        $r['photo'] = 'data:image/png;base64,' . $defImage;
    } else {
        $photo = mysqli_fetch_assoc($photo_result);
        $r['photo'] = (string)$photo['docData'] ?: 'data:image/png;base64,' . $defImage;
    }

    $rx[] = $r;
}

$total_query = "SELECT COUNT(id) AS entries FROM bottles WHERE owner_id = '$userID'";
$total_result = mysqli_query($conn, $total_query);
if (!$total_result) {
    error_log("PV error: Total query failed: " . mysqli_error($conn));
    echo json_encode(["error" => "Internal server error"]);
    return;
}
$total = mysqli_fetch_assoc($total_result)['entries'] ?? 0;

$filtered_query = "SELECT COUNT(id) AS entries FROM bottles $f";
$filtered_result = mysqli_query($conn, $filtered_query);
if (!$filtered_result) {
    error_log("PV error: Filtered query failed: " . mysqli_error($conn));
    echo json_encode(["error" => "Internal server error"]);
    return;
}
$filtered = mysqli_fetch_assoc($filtered_result)['entries'] ?? 0;

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

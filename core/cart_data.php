<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/getIngSupplier.php');

//session_start();
$userID = isset($_SESSION['userID']) ? (int)$_SESSION['userID'] : 0;
$role = isset($_SESSION['role']) ? (int)$_SESSION['role'] : 0;

// Ensure the user is authenticated
if (!$userID) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'User not authenticated']);
    return;
}

$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$order_by = in_array($_POST['order_by'], ['name', 'quantity', 'purity']) ? $_POST['order_by'] : 'name';
$order = ($_POST['order_as'] === 'DESC') ? 'DESC' : 'ASC';
$s = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';

// Build the query filter
if ($role === 1) {
    // Admins: no userID filter
    $f = $s !== '' ? "WHERE name LIKE ?" : "";
} else {
    // Non-admins: filter by userID
    $f = "WHERE owner_id = ?";
    $f .= $s !== '' ? " AND name LIKE ?" : "";
}

// Prepare the main query
$query = "SELECT SQL_CALC_FOUND_ROWS * FROM cart $f ORDER BY $order_by $order LIMIT ?, ?";
$stmt = $conn->prepare($query);

if ($role === 1) {
    // Admins: bind search term, row, and limit
    if ($s !== '') {
        $search = "%$s%";
        $stmt->bind_param('sii', $search, $row, $limit);
    } else {
        $stmt->bind_param('ii', $row, $limit);
    }
} else {
    // Non-admins: bind userID, search term, row, and limit
    if ($s !== '') {
        $search = "%$s%";
        $stmt->bind_param('isii', $userID, $search, $row, $limit);
    } else {
        $stmt->bind_param('iii', $userID, $row, $limit);
    }
}

$stmt->execute();
$result = $stmt->get_result();

$rs = [];
while ($rq = $result->fetch_assoc()) {
    $r = [
        'id' => (int)$rq['id'],
        'name' => $rq['name'] ?: 'N/A',
        'quantity' => (float)$rq['quantity'] ?: 0,
        'purity' => (float)$rq['purity'] ?: 0,
    ];

    $suppliers = getIngSupplier($rq['ingID'], 0, $conn);
    $r['supplier'] = [];
    if ($suppliers) {
        foreach ($suppliers as $b) {
            $r['supplier'][] = [
                'name' => $b['name'],
                'link' => $b['supplierLink'],
            ];
        }
    }

    $rs[] = $r;
}

// Fetch total and filtered counts
if ($role === 1) {
    // Admins: count all entries
    $total = $conn->query("SELECT COUNT(id) AS entries FROM cart")->fetch_assoc()['entries'];
    $filtered = $conn->query("SELECT FOUND_ROWS() AS entries")->fetch_assoc()['entries'];
} else {
    // Non-admins: count only user-specific entries
    $total = $conn->query("SELECT COUNT(id) AS entries FROM cart WHERE owner_id = $userID")->fetch_assoc()['entries'];
    $filtered = $conn->query("SELECT FOUND_ROWS() AS entries")->fetch_assoc()['entries'];
}

$response = [
    "draw" => (int)$_POST['draw'],
    "recordsTotal" => (int)$total,
    "recordsFiltered" => (int)$filtered,
    "data" => $rs,
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);

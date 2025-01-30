<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if($role !== 1){
    header('Location: /');
    return;
}

$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$order_by = isset($_POST['order_by']) ? mysqli_real_escape_string($conn, $_POST['order_by']) : 'name';
$order = isset($_POST['order_as']) ? mysqli_real_escape_string($conn, $_POST['order_as']) : 'ASC';

$extra = "ORDER BY $order_by $order";

$filters = [];

$s = trim($_POST['search']['value'] ?? '');
if ($s !== '') {
    $searchTerm = mysqli_real_escape_string($conn, $s);
    $filters[] = "(email LIKE '%$searchTerm%' OR user_id LIKE '%$searchTerm%')";
}

$f = !empty($filters) ? 'WHERE ' . implode(' AND ', $filters) : '';

$Query = "SELECT * FROM users LIMIT $row, $limit";

$users = mysqli_query($conn, $Query);

$userData = [];
while ($allFormulas = mysqli_fetch_assoc($users)) {
    $userData[] = $allFormulas;
}

$rx = [];
foreach ($userData as $user) {
    $r = [
        'id' => $user['id'],
        'email' => $user['email'],
        'full_name' => $user['fullName'],
        'provider' => $user['provider'],
        'role' => $user['role'],
        'status' => $user['isActive'],
        'country' => $user['country'],
        'is_api_active' => $user['isAPIActive'],
        'api_key' => $user['API_key'],
        'is_verified' => $user['isVerified'],
        'created_at' => $user['created_at'],
        'updated_at' => $user['updated_at']
    ];
    
    $rx[] = $r;
}

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM users"));
$filtered = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM users $f"));

$response = [
    "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 0,
    "recordsTotal" => (int)$total['entries'],
    "recordsFiltered" => (int)$filtered['entries'],
    "data" => $rx,
    "debug" => $Query
];

if (empty($rx)) {
    $response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;

?>
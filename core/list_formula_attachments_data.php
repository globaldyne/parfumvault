<?php

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/formatBytes.php');

$ingID = mysqli_real_escape_string($conn, $_POST["id"]);

$stmt = $conn->prepare("SELECT * FROM documents WHERE ownerID = ? AND type = ? AND owner_id = ?");
$type = 5;
$stmt->bind_param("iii", $ingID, $type, $userID);
$stmt->execute();
$result = $stmt->get_result();

$response = ['data' => []];

while ($doc = $result->fetch_assoc()) {
    $r = [
        'id' => (int)$doc['id'],
        'ownerID' => (int)$doc['ownerID'],
        'type' => (int)$doc['type'],
        'name' => (string)$doc['name'] ?: '-',
        'notes' => (string)$doc['notes'] ?: '-',
        'created_at' => (string)$doc['created_at'] ?: '-',
        'docData' => (string)$doc['docData'],
        'docSize' => formatBytes(strlen($doc['docData']))
    ];
    
    $response['data'][] = $r;
}

$stmt->close();

if (empty($response['data'])) {
    $response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>
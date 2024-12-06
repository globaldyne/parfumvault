<?php

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/formatBytes.php');

$ingID = mysqli_real_escape_string($conn, $_POST["id"]);

$stmt = $conn->prepare("SELECT * FROM documents WHERE ownerID = ? AND type = ?");
$type = 5;
$stmt->bind_param("ii", $ingID, $type);
$stmt->execute();
$result = $stmt->get_result();

$response = ['data' => []];

while ($doc = $result->fetch_assoc()) {
    $r = [
        'id' => (int)$doc['id'],
        'ownerID' => (int)$doc['ownerID'],
        'type' => (int)$doc['type'],
        'name' => (string)$doc['name'] ?: 'N/A',
        'notes' => (string)$doc['notes'] ?: 'N/A',
        'created_at' => (string)$doc['created_at'] ?: 'N/A',
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
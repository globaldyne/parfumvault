<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$ingID = base64_decode($_GET["id"]);

$stmt = $conn->prepare("SELECT id,cid,synonym,source FROM synonyms WHERE ing = ? AND owner_id = ?");
$stmt->bind_param("ss", $ingID, $userID);
$stmt->execute();
$result = $stmt->get_result();

$syns = [];
while($res = $result->fetch_assoc()){
	$syns[] = $res;
}

$response = ['data' => []];
foreach ($syns as $syn) { 
	$r['id'] = (int)$syn['id'];
	$r['cid'] = (int)$syn['cid'];
	$r['synonym'] = (string)$syn['synonym'];
	$r['source'] = (string)$syn['source'] ?: '-';

	$response['data'][] = $r;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

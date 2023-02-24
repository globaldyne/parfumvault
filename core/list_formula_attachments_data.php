<?php

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/formatBytes.php');

$ingID = mysqli_real_escape_string($conn, $_POST["id"]);

$q = mysqli_query($conn, "SELECT * FROM documents WHERE ownerID = '$ingID' AND type = '5'");
while($res = mysqli_fetch_array($q)){
    $docs[] = $res;
}

foreach ($docs as $doc) { 

	$r['id'] = (int)$doc['id'];
	$r['ownerID'] = (int)$doc['ownerID'];
	$r['type'] = (int)$doc['type'];
	$r['name'] = (string)$doc['name'];
	$r['notes'] = (string)$doc['notes']?:'N/A';
	$r['docData'] = (string)$doc['docData'];
	$r['docSize'] = (string)formatBytes(strlen($doc['docData']));

	$response['data'][] = $r;
}

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

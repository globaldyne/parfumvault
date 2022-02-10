<?php
require('../inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

$ingID = base64_decode($_GET["id"]);

$q = mysqli_query($conn, "SELECT id,cid,synonym,source FROM synonyms WHERE ing = '$ingID'");
while($res = mysqli_fetch_array($q)){
    $syns[] = $res;
}

foreach ($syns as $syn) { 
	$r['id'] = (int)$syn['id'];
	$r['cid'] = (int)$syn['cid'];
	$r['synonym'] = (string)$syn['synonym'];
	$r['source'] = (string)$syn['source']?: 'N/A';

	$response['data'][] = $r;
}

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$ingID = base64_decode($_GET["id"]);

$q = mysqli_query($conn, "SELECT id,cid,synonym,source FROM synonyms WHERE ing = '$ingID' AND owner_id = '$userID'");
while($res = mysqli_fetch_array($q)){
    $syns[] = $res;
}

foreach ($syns as $syn) { 
	$r['id'] = (int)$syn['id'];
	$r['cid'] = (int)$syn['cid'];
	$r['synonym'] = (string)$syn['synonym'];
	$r['source'] = (string)$syn['source']?: '-';

	$response['data'][] = $r;
}

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

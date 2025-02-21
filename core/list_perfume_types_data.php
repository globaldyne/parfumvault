<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$q = mysqli_query($conn, "SELECT id,name,concentration,description FROM perfumeTypes WHERE owner_id = '$userID' ");
while($res = mysqli_fetch_array($q)){
    $data[] = $res;
}

foreach ($data as $d) { 
	$r['id'] = (int)$d['id'];
	$r['name'] = (string)$d['name'] ?: '-';
	$r['concentration'] = (int)$d['concentration'] ?: 100;
	$r['description'] = (string)$d['description'] ?: '-';

	$response['data'][] = $r;
}

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

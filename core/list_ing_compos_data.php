<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

$ingID = base64_decode($_GET["id"]);

$q = mysqli_query($conn, "SELECT id,ing,name,cas,ec,percentage FROM allergens WHERE ing = '$ingID'");
while($res = mysqli_fetch_array($q)){
    $compos[] = $res;
}

foreach ($compos as $compo) { 
	$r['id'] = (int)$compo['id'];
	$r['ing'] = (string)$compo['ing'];
	$r['name'] = (string)$compo['name'];
	$r['cas'] = (string)$compo['cas']?: 'N/A';
	$r['ec'] = (string)$compo['ec']?: 'N/A';
	$r['percentage'] = (float)$compo['percentage']?: '0';	

	$response['data'][] = $r;
}

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

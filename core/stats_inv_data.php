<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$q = mysqli_query($conn, "SELECT SUM(price) AS total_worth FROM suppliers");
while($res = mysqli_fetch_array($q)){
    $data[] = $res;
}

foreach ($data as $d) { 
	$r['ingredients']['total_worth'] = (string)number_format($d['total_worth'],2);
	$r['currency'] = (string)$settings['currency'];

	$response['data'][] = $r;
}

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

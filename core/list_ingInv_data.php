<?php
require('../inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

$q = mysqli_query($conn, "SELECT * FROM suppliers");
while($res = mysqli_fetch_array($q)){
    $sup[] = $res;
}

foreach ($sup as $suppliers) { 
	$r['id'] = (int)$suppliers['id'];
	$r['ingSupplierID'] = (int)$suppliers['ingSupplierID'];
	$r['ingID'] = (int)$suppliers['ingID'];
	$r['supplierLink'] = (string)$suppliers['supplierLink'];
	$r['price'] = (float)$suppliers['price'];
	$r['size'] = (float)$suppliers['size'];
	$r['manufacturer'] = (string)$suppliers['manufacturer'];
	$r['preferred'] = (int)$suppliers['preferred'];
	$r['manufactured'] = (string)$suppliers['manufactured'];
	$r['mUnit'] = (string)$suppliers['mUnit'];
	$r['stock'] = (int)$suppliers['stock'];

	$response['data'][] = $r;
}

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

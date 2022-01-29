<?php
require('../inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
$id = mysqli_real_escape_string($conn, $_GET['id']);

$q = mysqli_query($conn, "SELECT * FROM suppliers WHERE ingID='$id'");
while($res = mysqli_fetch_array($q)){
    $sup[] = $res;
}

foreach ($sup as $suppliers) { 
	$supplier = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '".$suppliers['ingSupplierID']."'"));

	$r['id'] = (int)$suppliers['id'];
	$r['ingSupplierID'] = (int)$suppliers['ingSupplierID'];
	$r['ingID'] = (int)$suppliers['ingID'];
	$r['supplierName'] = (string)$supplier['name'];
	$r['supplierLink'] = (string)$suppliers['supplierLink']?:'N/A';
	$r['price'] = (float)$suppliers['price'];
	$r['size'] = (float)$suppliers['size'];
	$r['manufacturer'] = (string)$suppliers['manufacturer']?:'N/A';
	$r['batch'] = (string)$suppliers['batch']?:'N/A';
	$r['preferred'] = (int)$suppliers['preferred'];
	$r['purchased'] = (string)$suppliers['purchased']?:'N/A';
	$r['mUnit'] = (string)$suppliers['mUnit'];
	$r['stock'] = (int)$suppliers['stock']?:0;

	$response['data'][] = $r;
}

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/libs/qrcode.php');

$id = mysqli_real_escape_string($conn, $_GET["id"]);
$qr = new QRCode();
if($_GET['type'] == "ingredient"){
	
	$data = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingredients WHERE id = '$id'"));
	$qrData = base64_encode($data["name"]);
	
} else {
	
	$data = mysqli_fetch_array(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE fid = '$id'"));
	$qrData = base64_encode($data["fid"]);
	
}


$qr->setErrorCorrectLevel(QR_ERROR_CORRECT_LEVEL_M);
$qr->setTypeNumber(40);
$qr->addData($qrData);
$qr->make();
$qr->printHTML();
?>
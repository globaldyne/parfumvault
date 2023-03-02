<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

$id = $_GET['id'];
$type = $_GET['type'] == 'internal';


switch($type){
	case 'internal':
		$q = mysqli_fetch_array(mysqli_query($conn, "SELECT name, docData FROM documents WHERE id = '$id'"));
		header('Content-Type: application/pdf');
		echo $q['docData'];
		break;
	default:
		$q = mysqli_fetch_array(mysqli_query($conn, "SELECT name, docData FROM documents WHERE id = '$id'"));
		$d = explode('base64,', $q['docData']);
		$c = explode('data:', $d['0']);
		header("Content-Type: ".$c['1']."");
		echo base64_decode($d['1']);
}

return;
?>
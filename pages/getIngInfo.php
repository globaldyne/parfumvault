<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/searchIFRA.php');

if($_GET['id'] && $_GET['filter']){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	$filter = mysqli_real_escape_string($conn, $_GET['filter']);
	$info = mysqli_fetch_array(mysqli_query($conn, "SELECT $filter FROM ingredients WHERE id = '$id'"));
	
	switch ($filter) {
	  case "solvent":
		$response[$filter] = $info[$filter] ?: "None";
		break;
	  case "purity":
		$response[$filter] = $info[$filter] ?: 100;
		break;
	  
	  default:
		echo $response[$filter] = "No data";
	}
}



header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);

return;
?>
<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function goShopping($ingredient, $conn){
	$url = mysqli_fetch_array(mysqli_query($conn, "SELECT supplier_link FROM ingredients WHERE name = '$ingredient'"));
	if(empty($url['supplier_link'])){
		$suppUrl = '#';
	}else{
		$suppUrl = $url['supplier_link'];
	}

return $suppUrl;
}
?>
<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function getCatByID($id,$conn){
	$cat = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingCategory WHERE id = '$id'"));
	return $cat['name'];
}

?>

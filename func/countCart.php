<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function countCart($conn ){

	$c = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM cart"));
	
	if($c == '0'){
		return NULL;
	}else{
		return $c;
	}
}

?>
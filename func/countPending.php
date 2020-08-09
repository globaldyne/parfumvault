<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function countPending($conn ){
	$c = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula"));
	return $c;			  
}

?>
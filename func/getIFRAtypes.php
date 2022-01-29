<?php
if (!defined('pvault_panel')){ die('Not Found');}
function getIFRAtypes($type,$conn){
	
	$type = mysqli_num_rows(mysqli_query($conn, "SELECT type FROM IFRALibrary WHERE type = '$type'"));
	
	echo $type;
}
?>

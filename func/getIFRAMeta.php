<?php

if (!defined('pvault_panel')){ die('Not Found');}

function getIFRAMeta($q, $conn){
	$ifra = mysqli_fetch_array(mysqli_query($conn, "SELECT $q FROM IFRALibrary"));
	return $ifra[$q];
}
?>
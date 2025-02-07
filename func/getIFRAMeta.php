<?php

if (!defined('pvault_panel')){ die('Not Found');}

function getIFRAMeta($q, $conn){
	global $userID;
	
	$ifra = mysqli_fetch_array(mysqli_query($conn, "SELECT $q FROM IFRALibrary WHERE owner_id = '$userID'"));
	return $ifra[$q];
}
?>
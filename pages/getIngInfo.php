<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/searchIFRA.php');

if($_GET['id'] && $_GET['filter']){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	$filter = mysqli_real_escape_string($conn, $_GET['filter']);
	$info = mysqli_fetch_array(mysqli_query($conn, "SELECT $filter FROM ingredients WHERE id = '$id'"));
	
	echo $info[$filter];
}

return;
?>
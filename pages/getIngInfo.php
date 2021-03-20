<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/searchIFRA.php');

if($_GET['name'] && $_GET['filter']){
	$name = mysqli_real_escape_string($conn, $_GET['name']);
	$filter = mysqli_real_escape_string($conn, $_GET['filter']);
	$info = mysqli_fetch_array(mysqli_query($conn, "SELECT $filter FROM ingredients WHERE name = '$name'"));
	
	echo $info[$filter];
}

return;
?>
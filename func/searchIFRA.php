<?php if (!defined('pvault_panel')){ die('Not Found');}?>

<?php

function searchIFRA($cas,$name,$dbhost,$dbuser,$dbpass,$dbname){
	
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Error connecting to database');
	$res = mysqli_fetch_array(mysqli_query($conn, "SELECT cat4 FROM IFRALibrary WHERE cas = '$cas' OR name = '$name'"));
	
	return $res['cat4'];
}
?>
<?php if (!defined('pvault_panel')){ die('Not Found');}?>

<?php
function getIFRAtypes($type,$dbhost,$dbuser,$dbpass,$dbname){
	
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Error connecting to database');
	$type = mysqli_num_rows(mysqli_query($conn, "SELECT type FROM IFRALibrary WHERE type = '$type'"));
	
	echo $type;
}
?>
<?php if (!defined('pvault_panel')){ die('Not Found');}?>

<?php
function getIngUsage($ingredient,$dbhost,$dbuser,$dbpass,$dbname){
	
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Error connecting to database');
	$ing = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulas WHERE ingredient = '$ingredient'"));
	
	echo $ing;
}
?>
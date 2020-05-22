<?php if (!defined('pvault_panel')){ die('Not Found');}?>

<?php
function getFormula($formula,$profile,$dbhost,$dbuser,$dbpass,$dbname){
	
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Error connecting to database');
	$fname = mysqli_real_escape_string($conn, $formula);
	$ing = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE name = '$fname'");
	
	while($q = mysqli_fetch_array($ing)){
		$chk = mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$q[ingredient]' AND profile = '$profile'");
		while ($qValues=mysqli_fetch_array($chk)){
			echo $qValues['name'].'\n';
		}
	}
}
?>
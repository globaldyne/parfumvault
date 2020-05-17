<?php
function getIngUsage($ingredient,$dbhost,$dbuser,$dbpass,$dbname){
	
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Error connecting to database');
	$ing = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulas WHERE ingredient = '$ingredient'"));
	
	//while($q = mysqli_fetch_array($ing)){
	//	$chk = mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$q[ingredient]' AND profile = '$profile'");
	//	while ($qValues=mysqli_fetch_array($chk)){
			echo $ing;
	//	}
	//}
}
?>
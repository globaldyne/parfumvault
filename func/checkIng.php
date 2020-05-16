<?php

function checkIng($ingredient,$dbhost, $dbuser, $dbpass, $dbname){
	
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Error connecting to database');

	$chk = mysqli_query($conn, "SELECT IFRA, price, profile FROM ingredients WHERE name = '$ingredient'");
	while ($qValues=mysqli_fetch_array($chk)){
		if (!($qValues["IFRA"]) || !($qValues["price"]) || !($qValues["profile"])){
			return '<a href="#" class="fas fa-exclamation" rel="tipsy" title="Missing ingredient data"></a>';
			}
	}

}
?>
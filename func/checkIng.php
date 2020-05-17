<?php

function checkIng($ingredient,$dbhost, $dbuser, $dbpass, $dbname){
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Error connecting to database');
	$chk = mysqli_query($conn, "SELECT IFRA, price, profile FROM ingredients WHERE name = '$ingredient'");
	if(mysqli_num_rows($chk)){
		while ($qValues=mysqli_fetch_array($chk)){
			if (!($qValues['IFRA'])){
				return '<a href="#" class="fas fa-exclamation" rel="tipsy" title="Missing IFRA data"></a>';
			}elseif(!($qValues['price'])){
				return '<a href="#" class="fas fa-exclamation" rel="tipsy" title="Missing price data"></a>';
			}elseif(!($qValues['profile'])){
				return '<a href="#" class="fas fa-exclamation" rel="tipsy" title="Missing profile data"></a>';
			}
		}
	}else{
		return '<a href="#" class="fas fa-exclamation" rel="tipsy" title="Ingredient is missing from the database"></a>';
	}
}
?>
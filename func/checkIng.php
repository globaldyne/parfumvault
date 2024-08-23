<?php

function checkIng($ingredient, $defCatClass, $conn){
	$chk = mysqli_query($conn, "SELECT id, name, $defCatClass, profile, cas FROM ingredients WHERE name = '$ingredient' OR chemical_name = '$ingredient'");	
	if(mysqli_num_rows($chk)){
		while ($qValues=mysqli_fetch_array($chk)){
			if($qValues['cas']){
				$casQ = "OR cas LIKE '%".$qValues['cas']."%'";
			}
			$chkIFRA = mysqli_fetch_array(mysqli_query($conn, "SELECT name, $defCatClass FROM IFRALibrary WHERE  name = '".$qValues['name']."' OR synonyms LIKE '%".$qValues['name']."%' $casQ"));
			$chkPrice = mysqli_fetch_array(mysqli_query($conn, "SELECT price FROM suppliers WHERE ingID = '".$qValues['id']."'"));
			
			if(empty($chkIFRA[$defCatClass]) && !isset($qValues[$defCatClass])){
				return ['text' => 'Missing usage data', 'code' => 1];
			}
			if(empty($chkPrice['price'])){
				return ['text' => 'Missing pricing data', 'code' => 2];
			}
			if(!($qValues['profile'])){
				return ['text' => 'Missing profile data', 'code' => 3];
			}
		}
	}else{
        return ['text' => 'Ingredient is missing from the database', 'code' => 4];
	}
}


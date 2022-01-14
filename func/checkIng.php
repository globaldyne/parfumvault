<?php if (!defined('pvault_panel')){ die('Not Found');}?>
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
			
			if(empty($chkIFRA[$defCatClass]) && empty($qValues[$defCatClass])){
				return 'Missing usage data';
			}
			if(empty($chkPrice['price'])){
				return 'Missing pricing data';
			}
			if(!($qValues['profile'])){
				return 'Missing profile data';
			}
		}
	}else{
		return 'Ingredient is missing from the database';
	}
}
?>

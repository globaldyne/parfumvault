<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<?php
function checkIng($ingredient, $defCatClass, $conn){
	$chk = mysqli_query($conn, "SELECT name, $defCatClass, price, profile, cas FROM ingredients WHERE name = '$ingredient' OR chemical_name = '$ingredient'");	
	if(mysqli_num_rows($chk)){
		while ($qValues=mysqli_fetch_array($chk)){
			if($qValues['cas']){
				$casQ = "OR cas LIKE '%$qValues[cas]%'";
			}
			$chkIFRA = mysqli_fetch_array(mysqli_query($conn, "SELECT name, $defCatClass FROM IFRALibrary WHERE  name = '$qValues[name]' OR synonyms LIKE '%$qValues[name]%' $casQ"));
						
			if(empty($chkIFRA[$defCatClass]) && empty($qValues[$defCatClass])){
				return '<a href="#" class="fas fa-exclamation" rel="tipsy" title="Missing usage data"></a>';
			}elseif(empty($qValues['price'])){
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

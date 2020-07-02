<?php
if (!defined('pvault_panel')){ die('Not Found');}

function checkAllergen($ingredient,$conn){
	
	$chk = mysqli_query($conn, "SELECT allergen FROM ingredients WHERE name = '$ingredient' AND allergen = '1' OR chemical_name = '$ingredient' AND allergen = '1'");	
	if(mysqli_num_rows($chk)){
		return '<a href="#" class="fas fa-exclamation-triangle" rel="tipsy" title="Allergen"></a>';
	}

}

?>
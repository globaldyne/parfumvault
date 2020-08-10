<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function calcCosts($price, $quantity, $concentration, $ml = 10 ){
	$sub = $price / $ml * $quantity;
	$total = $concentration / 100 * $sub;
	return number_format($total,3,);
	
}
?>
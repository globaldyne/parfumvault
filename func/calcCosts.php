<?php
function calcCosts($price, $quantity, $ml = 10){
	$total = $price / $ml * $quantity;
	
	return number_format($total,2);
	
}
?>
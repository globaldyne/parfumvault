<?php if (!defined('pvault_panel')){ die('Not Found');}?>

<?php
function calcCosts($price, $quantity, $concentration, $ml = 10 ){
	$sub = $price / $ml * $quantity;
	$total = $concentration / 100 * $sub;
	return number_format($total,2);
	
}
?>
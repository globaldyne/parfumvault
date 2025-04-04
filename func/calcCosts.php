<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function calcCosts($price, $quantity, $concentration, $ml = 10) {
    if (!is_numeric($price) || !is_numeric($quantity) || !is_numeric($concentration) || !is_numeric($ml)) {
        return '0';
    }

    // Prevent $ml from being 0
    if ($ml == 0) {
        $ml = 10;
    }

    $sub = ($price / $ml) * $quantity;
    $total = ($concentration / 100) * $sub;
    return number_format($total, 3);
}
?>
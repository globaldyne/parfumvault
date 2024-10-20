<?php
if (!defined('pvault_panel')){ die('Not Found');}

function convert_to_decimal_point($number, $decimal = TRUE) {
    $decimalPlaces = (int)$number;
    
    if ($decimalPlaces > 0) {
		if($decimal){
			
        	return '.' . str_repeat('0', $decimalPlaces);
		}else {
			return  str_repeat('0', $decimalPlaces);
		}
    } else {
        return '0'; // If 0 or negative, return plain 0
    }
}

?>
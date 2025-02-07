<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function validateInput($str ){
	$str = preg_replace("/[^0-9.]/", "", $str);
	return $str;
}

function isPasswordComplex(string $password): bool {
    // Regular expression to check password criteria
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[a-zA-Z\d\W_]{8,}$/';

    // Validate the password using the regex pattern
    return preg_match($pattern, $password) === 1;   
}

?>

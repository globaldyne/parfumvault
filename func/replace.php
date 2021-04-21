<?php

function replace($a, $b, $c) { 

    $output = str_replace($a, $b, strtolower(htmlentities(trim($c))));    
	
	return $output;
}
?>

<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function validateInput($str ){
	$str = preg_replace("/[^0-9.]/", "", $str);
	return $str;
}

?>

<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function sanChar($str) {
	$result = str_replace( array("`", "/", "\\", "'", ";", "\"" ), '', $str);
	return $result;

}

?>
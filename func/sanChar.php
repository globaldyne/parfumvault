<?php 
if (!defined('pvault_panel')){ die('Not Found');}
//DEPRECATED
function sanChar($str) {
	$result = str_replace( array("`", "/", "\\", "'", ";", "\"" ), '', $str);
	return $str;

}

?>
<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function loadModules($kind){
	define('__ROOT__', dirname(dirname(__FILE__))); 
	foreach (glob(__ROOT__.'/modules/'.$kind.'/*.json') as $module){
	
		$m[] = json_decode(file_get_contents($module), true);
	
	}
	return $m;
}
?>
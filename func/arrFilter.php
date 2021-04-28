<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function arrFilter($arr){
	$result = array();
	
	foreach($arr as $value){
		$name = $value['name'];
		$result[$name]['name'] = $name;
		$result[$name]['image'] = $value['image'];
	}
	
	return array_values($result);
}

?>

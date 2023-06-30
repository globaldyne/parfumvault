<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function arrFilter($arr){
	$result = array();

	foreach($arr as $value){
		if($value['name']){
			$name = $value['name'];
			$result[$name]['name'] = $name;
			$result[$name]['image'] = $value['image'];
			$result[$name]['ing'] = $value['ing'];
		}
	}
	
	return array_values($result);
}

?>

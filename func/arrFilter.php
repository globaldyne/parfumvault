<?php 
if (!defined('pvault_panel')){ die('Not Found');}

/**
 * Filters an array of associative arrays by 'name' key and restructures the result.
 *
 * @param array $arr The input array to filter.
 * @return array The filtered and restructured array.
 */
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

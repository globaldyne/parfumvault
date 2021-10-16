<?php if (!defined('pvault_panel')){ die('Not Found');}?>

<?php
function getIngState($physical_state, $class = 'img_ing', $style = ''){
	if($physical_state == '1'){ 
		$physical_state = '<img src="../img/liquid.png" class="'.$class.'"'. $style .'/>';
	}elseif($physical_state == '2'){ 
		$physical_state = '<img src="../img/solid.png" class="'.$class.'"'. $style .'/>';
	}else{ 
		$physical_state = 'N/A'; 
	}
	return $physical_state;
}

function getIngType($type, $class = 'img_ing', $style = 'style="border: solid 3px rgba(234, 95, 0, 0.8);"'){
	if($type == 'EO'){ 
		$type = '<img src="../img/pv_naturals.png" class="'.$class.'"'. $style .'/>';
	}elseif($type == 'AC'){ 
		$type = '<img src="../img/pv_molecule.png" class="'.$class.'"'. $style .'/>';
	}else{ 
		$type = '<img src="../img/pv_molecule.png" class="'.$class.'"'. $style .'/>';
	}
	return $type;
}


?>


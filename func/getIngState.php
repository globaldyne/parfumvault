<?php if (!defined('pvault_panel')){ die('Not Found');}?>

<?php
function getIngState($physical_state, $class = 'img_ing'){
	if($physical_state == '1'){ 
		$physical_state = '<img src="../img/liquid.png" class="'.$class.'"/>';
	}elseif($physical_state == '2'){ 
		$physical_state = '<img src="../img/solid.png" class="'.$class.'"/>';
	}else{ 
		$physical_state = 'N/A'; 
	}
	return $physical_state;
}

function getIngType($type, $class = 'img_ing'){
	if($type == 'EO'){ 
		$type = '<img src="../img/pv_naturals.png" class="'.$class.'"/>';
	}elseif($type == 'AC'){ 
		$type = '<img src="../img/pv_molecule.png" class="'.$class.'"/>';
	}else{ 
		$type = 'N/A'; 
	}
	return $type;
}


?>


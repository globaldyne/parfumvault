<?php
if (!defined('pvault_panel')){ die('Not Found');}

function profileImg($profile){
	switch ($profile) {
	  case "Top":
		return  '/img/Pyramid/Pyramid_Slice_Top.png';
		break;
	  case "Heart":
		return '/img/Pyramid/Pyramid_Slice_Heart.png';
		break;
	  case "Base":
		return '/img/Pyramid/Pyramid_Slice_Base.png';
		break;
	  default:
		return '/img/pv_molecule.png';
	}	
}
?>

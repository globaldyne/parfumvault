<?php

if (!defined('pvault_panel')){ die('Not Found');}

function profileImg($profile){
	if($profile == 'Top'){
		return  '../img/Pyramid/Pyramid_Slice_Top.png';
	}
	if($profile == 'Heart'){
		return '../img/Pyramid/Pyramid_Slice_Heart.png';
	}
	if($profile == 'Base'){
		return '../img/Pyramid/Pyramid_Slice_Base.png';
	}
}
?>

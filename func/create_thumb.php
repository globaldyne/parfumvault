<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function create_thumb($file, $x, $y){
	$thumb = new Imagick();
	$thumb->readImage($file);    
	$thumb->resizeImage($x,$y,Imagick::FILTER_LANCZOS,1);
	$thumb->writeImage($file);
	$thumb->clear();
	$thumb->destroy();
}

?>

<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function create_thumb($file, $w, $h){
	
	list($width, $height, $type) = getimagesize($file);
	
	if ($type == IMAGETYPE_JPEG){
  		$image = imagecreatefromjpeg($file);
	}else if ($type == IMAGETYPE_PNG){
  		$image = imagecreatefrompng($file);
 	}else if($type == IMAGETYPE_GIF) {
 		$image = imagecreatefromgif($file);
 	}
   
   	$conv_image = imagecreatetruecolor($w, $h);
 	imagecopyresampled($conv_image, $image, 0, 0, 0, 0, $w, $h, $width, $height);
 	return $conv_image;

}

?>

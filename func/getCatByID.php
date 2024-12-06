<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function getCatByID($id, $image = FALSE, $style = "img_ing"){
	global $conn;
	$cat = mysqli_fetch_array(mysqli_query($conn, "SELECT name,image,notes FROM ingCategory WHERE id = '$id'"));
	
	$title = $cat['notes'] ?: $cat['name'];
	$image = $cat['image']?:'/img/uknown_generic.png';
	
	$result = '<i rel="tip" title="'.$title.'"><img class="'.$style.' ing_ico_list" src="'.$image.'" /></i>';

	return $result;
}


function getCatByIDRaw($id, $filter = '*'){
	global $conn;
	return mysqli_fetch_array(mysqli_query($conn, "SELECT $filter FROM ingCategory WHERE id = '$id'"));
}


?>

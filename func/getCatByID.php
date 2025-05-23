<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function getCatByID($id, $image = FALSE, $style = "img_ing"){
	global $conn, $userID;

	$cat = mysqli_fetch_array(mysqli_query($conn, "SELECT name,image,notes FROM ingCategory WHERE id = '$id' AND owner_id = '$userID'"));
	
	$title = $cat['notes'] ?: $cat['name'];
	$image = $cat['image']?:'/img/uknown_generic.png';
	
	$result = '<i rel="tip" title="'.$title.'"><img class="'.$style.' ing_ico_list" src="'.$image.'" /></i>';

	return $result;
}


function getCatByIDRaw($id, $filter = '*'){
	global $conn, $userID;
	return mysqli_fetch_array(mysqli_query($conn, "SELECT $filter FROM ingCategory WHERE id = '$id' AND owner_id = '$userID'"));
}


?>

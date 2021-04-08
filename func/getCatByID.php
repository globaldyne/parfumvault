<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function getCatByID($id, $image = FALSE, $conn){
	
	$cat = mysqli_fetch_array(mysqli_query($conn, "SELECT name,image,notes FROM ingCategory WHERE id = '$id'"));
	if($cat['notes']){
		$title = $cat['notes'];
	}else{
		$title = $cat['name'];
	}
	
	if($image == TRUE && $cat['image']){
		$result = '<a href="#" rel="tipsy" title="'.$title.'"><img class="img_ing" src="uploads/categories/'.$cat['image'].'" /></a>';
	}else{
		$result =  '<a href="#" rel="tipsy" title="'.$title.'">'.$cat['name'].'</a>';
	}

	return $result;
}

?>

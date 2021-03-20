<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/inc/settings.php');


if($_GET['type'] == 'SDS' && $_GET['ingredient_id']){
	
	$ingID = mysqli_real_escape_string($conn, $_GET['ingredient_id']);
	
	if(isset($_FILES['SDS']['name'])){
      $file_name = $_FILES['SDS']['name'];
      $file_size = $_FILES['SDS']['size'];
      $file_tmp = $_FILES['SDS']['tmp_name'];
      $file_type = $_FILES['SDS']['type'];
      $file_ext = strtolower(end(explode('.',$_FILES['SDS']['name'])));
	  
	  if (file_exists('../'.$uploads_path.'SDS/') === FALSE) {
    	mkdir('../'.$uploads_path.'SDS/', 0740, true);
	  }

	  $ext = explode(', ', $allowed_ext);
	  
      if(in_array($file_ext,$ext)=== false){
		 echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file.</div>';
      }elseif($file_size > $max_filesize){
		 echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>File upload error: </strong>File size must not exceed '.formatBytes($max_filesize).'</div>';
      }else{
	  
         if(move_uploaded_file($file_tmp,'../'.$uploads_path.'SDS/'.base64_encode($file_name))){
		 	$SDSF = $uploads_path.'SDS/'.base64_encode($file_name);
		 	if(mysqli_query($conn, "UPDATE ingredients SET SDS = '$SDSF' WHERE name='$ingID'")){
		 		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>SDS File uploaded</strong></div>';
			}
		 }
	  }
   }
	
	return;	
}
?>
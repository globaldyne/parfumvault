<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/formatBytes.php');

$id = mysqli_real_escape_string($conn, $_GET['id']);
	
		
if(!empty($_FILES['photo']['name'])){
	$file_name = $_FILES['photo']['name'];
    $file_size = $_FILES['photo']['size'];
    $file_tmp =  $_FILES['photo']['tmp_name'];
    $file_type = $_FILES['photo']['type'];
    $file_ext = strtolower(end(explode('.',$_FILES['photo']['name'])));
    
	$path = __ROOT__.'/uploads/categories/';
	
	if (!file_exists($path)) {
		mkdir($path, 0740, true);
	}
	  
    $ext = explode(', ',strtolower($allowed_ext));

 	if(in_array($file_ext,$ext)=== false){
		$msg = '<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>Extension '.$file_ext.' not allowed, please choose a '.$allowed_ext.' file.</div>';
    }elseif($file_size > $max_filesize){
		$msg = '<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>File size must not exceed '.formatBytes($max_filesize).'</div>';
    }else{
    	$data = 'data:image/' . $file_ext . ';base64,' . base64_encode(file_get_contents($file_tmp));
		//move_uploaded_file($file_tmp,$path.base64_encode($file_name));
		if(mysqli_query($conn, "UPDATE ingCategory SET image = '".$data."' WHERE id = '$id'")or die(mysqli_error($conn))){
			$msg = '<div class="alert alert-success alert-dismissible"><strong>File uploaded</div>';
		}
    }
}

$cat = mysqli_fetch_array(mysqli_query($conn, "SELECT image,name FROM ingCategory WHERE id = '$id'")); 

?>
<link href="../css/sb-admin-2.css" rel="stylesheet">
<link href="../css/bootstrap.min.css" rel="stylesheet">

<style>
.form-inline .form-control {
    display: inline-block;
    width: 500px;
    vertical-align: middle;
}
</style>
<form action="?id=<?=$id?>" method="post" enctype="multipart/form-data" name="form">
<table class="table table-bordered" cellspacing="0">
    <tr>
      <td colspan="2" class="badge-primary">Edit <?=$cat['name']?></td>
    </tr>
    <tr>
      <td colspan="2"><?=$msg?></td>
    </tr>
    <tr>
      <td width="12%">Image:</td>
      <td width="88%"><input type="file" name="photo" id="photo" /></td>
    </tr>
    <tr>
      <td colspan="2">Recommended size: 50x50px</td>
    </tr>
    <tr>
      <td colspan="2"><input name="update" type="submit" class="btn-dark" id="update" value="Submit"></td>
    </tr>
  </table>
</form>
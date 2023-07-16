<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/formatBytes.php');
require_once(__ROOT__.'/func/imageResize.php');

$id = mysqli_real_escape_string($conn, $_GET['id']);
	
$max_height = 200;
$max_width = 200;

if(isset($_FILES['photo']['name'])){
	$file_name = $_FILES['photo']['name'];
    $file_size = $_FILES['photo']['size'];
    $file_tmp =  $_FILES['photo']['tmp_name'];
    $file_type = $_FILES['photo']['type'];
    $file_ext = strtolower(end(explode('.',$_FILES['photo']['name'])));

	
	$tmp_path = __ROOT__.'/tmp/';
	
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0740, true);
	}
	  
    $ext = explode(', ',strtolower($allowed_ext));


 	if(in_array($file_ext,$ext)=== false){
		$msg = '<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>Extension '.$file_ext.' not allowed, please choose a '.$allowed_ext.' file.</div>';
    }elseif($file_size > $max_filesize){
		$msg = '<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>File size must not exceed '.formatBytes($max_filesize).'</div>';
    }else{
		imageResize($tmp_path, $file_tmp, $file_name, $max_height, $max_width);
    	$data = 'data:image/' . $file_ext . ';base64,' . base64_encode(file_get_contents($tmp_path.$file_name));
		if(mysqli_query($conn, "UPDATE ingCategory SET image = '".$data."' WHERE id = '$id'")or die(mysqli_error($conn))){
			unlink($tmp_path.$file_name);
			$msg = '<div class="alert alert-success alert-dismissible"><strong>File uploaded</div>';
		}
    }
}

$cat = mysqli_fetch_array(mysqli_query($conn, "SELECT image,name FROM ingCategory WHERE id = '$id'")); 

?>

<form action="/pages/editCat.php?id=<?=$id?>" method="post" enctype="multipart/form-data" name="form">
<table class="table table-bordered" cellspacing="0">
    <tr>
      <td colspan="2" class="badge-primary">Upload image for  <?=$cat['name']?></td>
    </tr>
    <tr>
      <td colspan="2"><?=$msg?></td>
    </tr>
    <tr>
      <td width="12%">Image:</td>
      <td width="88%"><input type="file" name="photo" id="photo" /></td>
    </tr>
    <tr>
      <td colspan="2">Recommended size: <?=$max_height?>x<?=$max_width?> pixels</td>
    </tr>
    <tr>
      <td colspan="2"><input name="update" type="submit" class="btn-dark" id="update" value="Upload"></td>
    </tr>
  </table>
</form>
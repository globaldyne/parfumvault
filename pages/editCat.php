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
<div id="cat-msg"></div>


	<div class="row">
      <div class="col-md">
        <div class="text-center">
          <div id="cat-pic"><div class="loader"></div></div>
          <h6>Upload a photo...</h6>
          <input type="file" name="cat-pic-file" id="cat-pic-file" class="form-control">
        </div>
        <div class="dropdown-divider"></div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
			<div class="text-right">
        		<input type="submit" class="btn-dark" id="update-cat" value="Upload" />
        	</div>
        </div>
      </div>
    </div>
    <div class="dropdown-divider"></div>
    <div class="alert alert-info">Recommended size: <?=$max_height?>x<?=$max_width?> pixels</div>


<script>
$(document).ready(function () {

	$('#cat-pic').html('<img class="img-profile-avatar" src="<?=$cat['image']?: '/img/molecule.png'; ?>">');
	
	$('#update-cat').click(function() {
		var fd = new FormData();
		var files = $('#cat-pic-file')[0].files;
	
		if(files.length > 0 ){
			fd.append('cat-pic-file',files[0]);
		}
		$.ajax({ 
			url: '/pages/upload.php?upload_ing_cat_pic=1&catID=<?=$id?>', 
			type: 'POST',
			data: fd,
			contentType: false,
			processData: false,
			cache: false,
			dataType: 'json',
			success: function (data) {
				if(data.success){
					$('#cat-pic').html('<img class="img-profile-avatar" src="'+data.success.pic+'">');
	
				}else if( data.error){
					$('#cat-msg').html('<div class="alert alert-danger">'+data.error+'</div>');
				}
			}
		  });
	});
});
</script>
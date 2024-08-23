<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/formatBytes.php');
require_once(__ROOT__.'/func/imageResize.php');

$id = mysqli_real_escape_string($conn, $_GET['id']);
	
$max_height = 200;
$max_width = 200;

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
			<div class="text-right mt-3 mb-3">
        		<input type="submit" class="btn btn-primary" id="update-cat" value="Upload" />
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
					$('#tdDataCat').DataTable().ajax.reload(null, true);
				}else if( data.error){
					$('#cat-msg').html('<div class="alert alert-danger">'+data.error+'</div>');
				}
			}
		  });
	});
});
</script>
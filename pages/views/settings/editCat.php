<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/formatBytes.php');
require_once(__ROOT__.'/func/imageResize.php');

$id = mysqli_real_escape_string($conn, $_GET['id']);
	
$max_height = 200;
$max_width = 200;

$cat = mysqli_fetch_array(mysqli_query($conn, "SELECT image,name FROM ingCategory WHERE id = '$id' AND owner_id = '$userID'")); 

?>
<div id="cat-msg"></div>


<div class="row">
  <div class="col-md">
    <div class="text-center">
      <div id="cat-pic">
        <div class="loader"></div>
      </div>
      <input type="file" name="cat-pic-file" id="cat-pic-file" class="mt-4 form-control">
    </div>
    <div class="divider"></div>
    <div class="col-12">
      <div class="text-end mt-3 mb-3">
        <input type="submit" class="btn btn-primary" id="update-cat" value="Upload" />
      </div>
    </div>
  </div>
</div>
<div class="divider"></div>
<div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i>Recommended size: <?=$max_height?>x<?=$max_width?> pixels</div>



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
					$('#cat-msg').html('');
					$('#tdDataCat').DataTable().ajax.reload(null, true);
				}else if( data.error){
					$('#cat-msg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>'+data.error+'</div>');
				}
			},
			error: function (xhr, status, error) {
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		  });
	});
});
</script>
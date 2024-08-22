<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/formatBytes.php');
require_once(__ROOT__.'/func/imageResize.php');

$id = mysqli_real_escape_string($conn, $_GET['id']);
$prof = mysqli_fetch_array(mysqli_query($conn, "SELECT image,name FROM ingProfiles WHERE id = '$id'")); 

?>
<div id="prof-msg"></div>


	<div class="row">
      <div class="col-md">
        <div class="text-center">
          <div id="prof-pic"><div class="loader"></div></div>
          <h6>Upload a photo...</h6>
          <input type="file" name="prof-pic-file" id="prof-pic-file" class="form-control">
        </div>
        <div class="dropdown-divider"></div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
			<div class="text-right mt-3 mb-3">
        		<input type="submit" class="btn btn-primary" id="update-prof" value="Upload" />
        	</div>
        </div>
      </div>
    </div>
    <div class="dropdown-divider"></div>
    <div class="alert alert-info">Recommended size: 200x200 pixels</div>


<script>
$(document).ready(function () {

	$('#prof-pic').html('<img class="img-profile-avatar" src="<?=$prof['image']?: '/img/molecule.png'; ?>">');
	
	$('#update-prof').click(function() {
		var fd = new FormData();
		var files = $('#prof-pic-file')[0].files;
	
		if(files.length > 0 ){
			fd.append('prof-pic-file',files[0]);
		}
		$.ajax({ 
			url: '/pages/upload.php?upload_ing_prof_pic=1&profID=<?=$id?>', 
			type: 'POST',
			data: fd,
			contentType: false,
			processData: false,
			cache: false,
			dataType: 'json',
			success: function (data) {
				if(data.success){
					$('#prof-pic').html('<img class="img-profile-avatar" src="'+data.success.pic+'">');
					$('#ingDataProf').DataTable().ajax.reload(null, true);
				}else if( data.error){
					$('#prof-msg').html('<div class="alert alert-danger">'+data.error+'</div>');
				}
			},
			error: function (xhr, status, error) {
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		  });
	});
});
</script>
<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

?>

<div class="card-body">
	<div class="row pt-4">
      
      <div class="col-md-8">
        <div id="brandMsg"></div>
        
          <div class="row mb-3">
            <label class="col-sm-1 control-label">Brand Name</label>
            <div class="col-sm-8">
              <input name="brandName" type="text" class="form-control" id="brandName" value="<?php echo $settings['brandName'];?>" />
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-1 control-label">Address</label>
            <div class="col-md-8">
              <input name="brandAddress" type="text" class="form-control" id="brandAddress" value="<?php echo $settings['brandAddress'];?>"/>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-1 control-label">Email</label>
            <div class="col-md-8">
              <input name="brandEmail" type="text" class="form-control" id="brandEmail" value="<?php echo $settings['brandEmail'];?>" />
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-1 control-label">Contact No</label>
            <div class="col-md-8">
              <input name="brandPhone" type="text" class="form-control" id="brandPhone" value="<?php echo $settings['brandPhone'];?>" />
            </div>
          </div>          
          <div class="dropdown-divider"></div>
          <div class="form-row">
			<div class="col-sm-1">
				<button type="button" id="save-brand" name="save-brand" class="btn btn-primary">Update</button>
			</div>
          </div>
      </div>
      <div class="col-md-4">
        <div class="text-center">
          <div id="brandLogo_pic" class="mb-3"><div class="loader"></div></div>
          <input type="file" id="brandLogo" name="brandLogo" class="form-control" />
        </div>
        <div class="dropdown-divider"></div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
			<div class="text-right mt-3">
        		<input type="button" class="btn btn-primary" value="Upload" id="brandLogo_upload" />
        	</div>
        </div>
      </div>
  </div>
</div>

<script>
$(document).ready(function() {

	$('#brandLogo_pic').html('<img class="img-profile-avatar" src="<?=$settings['brandLogo']?: '/img/logo_def.png'; ?>">');
	
	$('#save-brand').click(function() {
		$.ajax({ 
			url: '/pages/update_settings.php', 
			type: 'POST',
			data: {
				manage: 'brand',
				brandName: $("#brandName").val(),
				brandAddress: $("#brandAddress").val(),
				brandEmail: $("#brandEmail").val(),
				brandPhone: $("#brandPhone").val()
			},
			dataType: 'json',
			success: function (data) {
				if(data.success) {
					var msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
				}else{
					var msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
				}
				$('#brandMsg').html(msg);
			}
		});
	});

	$("#brandLogo_upload").click(function(){
		$("#brandMsg").html('<div class="alert alert-info">Please wait, file upload in progress....</div>');
		$("#brandLogo_upload").prop("disabled", true);
    	$("#brandLogo_upload").prop('value', 'Please wait...');
		
		var fd = new FormData();
    	var files = $('#brandLogo')[0].files;
        
    	if(files.length > 0 ){
    		fd.append('brandLogo',files[0]);
        	$.ajax({
		  		url: '/pages/upload.php?type=brand',
				type: 'POST',
				data: fd,
				dataType: 'json',
				contentType: false,
				processData: false,
				success: function(response){
			 		if(response.success){
						$("#brandMsg").html(response);
						$("#brandLogo_upload").prop("disabled", false);
						$("#brandLogo_upload").prop('value', 'Upload');
						$('#brandLogo_pic').html('<img class="img-profile-avatar" src="'+response.success.pic+'">');

					}else{
						$("#brandMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+response.error+'</div>');
						$("#brandLogo_upload").prop("disabled", false);
						$("#brandLogo_upload").prop('value', 'Upload');
				    }
		  		},
	   	});
    	}else{
			$("#brandMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><i class="fa-solid fa-triangle-exclamation mx-2"></i>Please select a file to upload</div>');
			$("#brandLogo_upload").prop("disabled", false);
			$("#brandLogo_upload").prop('value', 'Upload');
		}
	});
		
});	

</script>
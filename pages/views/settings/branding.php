<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$branding = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM branding WHERE owner_id = '$userID'"));
?>

<div class="card-body">
	<div class="row pt-4">
	  
	  <div class="col-md-8">
		<div id="brandMsg"></div>
		
		  <div class="row mb-3 form-floating">
			<input name="brandName" type="text" class="form-control" id="brandName" placeholder="Brand Name" value="<?php echo $branding['brandName'];?>" />
			<label for="brandName">Brand Name</label>
		  </div>
		  <div class="row mb-3 form-floating">
			<input name="brandAddress" type="text" class="form-control" id="brandAddress" placeholder="Address" value="<?php echo $branding['brandAddress'];?>"/>
			<label for="brandAddress">Address</label>
		  </div>
		  <div class="row mb-3 form-floating">
			<input name="brandEmail" type="text" class="form-control" id="brandEmail" placeholder="Email" value="<?php echo $branding['brandEmail'];?>" />
			<label for="brandEmail">Email</label>
		  </div>
		  <div class="row mb-3 form-floating">
			<input name="brandPhone" type="text" class="form-control" id="brandPhone" placeholder="Contact No" value="<?php echo $branding['brandPhone'];?>" />
			<label for="brandPhone">Contact No</label>
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

	$('#brandLogo_pic').html('<img class="img-profile-avatar" src="<?=$branding['brandLogo']?: '/img/logo_def.png'; ?>">');
	
	$('#save-brand').click(function() {
		var brandName = $("#brandName").val().trim();
		var brandAddress = $("#brandAddress").val().trim();
		var brandEmail = $("#brandEmail").val().trim();
		var brandPhone = $("#brandPhone").val().trim();

		if (brandName === "" || brandAddress === "" || brandEmail === "" || brandPhone === "") {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>All fields are required.');
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
			$('.toast').toast('show');
			return;
		}

		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				action: 'branding',
				brandName: brandName,
				brandAddress: brandAddress,
				brandEmail: brandEmail,
				brandPhone: brandPhone
			},
			dataType: 'json',
			success: function (data) {
				if(data.success) {
					$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.success);
					$('.toast-header').removeClass().addClass('toast-header alert-success');
				}else{
					$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error);
					$('.toast-header').removeClass().addClass('toast-header alert-danger');
				}
				$('.toast').toast('show');
			},
			error: function (xhr, status, error) {
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
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

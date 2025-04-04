<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


$doc = mysqli_fetch_array(mysqli_query($conn,"SELECT docData AS avatar FROM documents WHERE ownerID = '".$userID."' AND name = 'avatar' AND type = '3' AND owner_id = '$userID'"));

?>

<div class="container">
	<div class="row">
		<div class="col-md-4">
			<div class="text-center">
				<div id="profile_pic"><div class="loader"></div></div>
				<input type="file" name="avatar" id="avatar" class="mt-2 form-control">
			</div>
			<div class="dropdown-divider"></div>
			<div class="col-12">
				<div class="text-end">
					<input name="upload-avatar" type="submit" class="btn btn-warning mt-2" id="upload-avatar" value="Upload" />
				</div>
			</div>
		</div>
		<div class="col-md-8 personal-info">
			<div id="msgU"></div>
			<div class="form-floating mb-3">
				<input type="text" class="form-control" id="fullName" name="fullName" placeholder="Full name" <?php if (getenv('USER_NAME')){?>disabled<?php } ?> value="<?php echo $user['fullName']; ?>">
				<label for="fullName">Full name</label>
			</div>
			<div class="form-floating mb-3">
				<input type="email" class="form-control" id="email" name="email" placeholder="Email" <?php if (getenv('USER_EMAIL')){?>disabled<?php } ?> value="<?php echo $user['email']; ?>">
				<label for="email">Email</label>
			</div>
			<div class="form-floating mb-3 position-relative">
				<input type="password" class="form-control password-input" id="password" name="password" placeholder="Password" <?php if (getenv('USER_PASSWORD')){?>disabled<?php } ?> value="">
				<label for="password">Password</label>
				<i class="toggle-password fa fa-eye position-absolute top-50 end-0 translate-middle-y me-3"></i>
			</div>
			<div class="form-floating mb-3">
				<select class="form-select" name="country" id="country">
					<option value="">Choose your country</option>
					<?php foreach ($countries as $country): ?>
						<option value="<?php echo htmlspecialchars($country['isoAlpha2']); ?>" <?php echo $user['country'] == $country['isoAlpha2'] ? 'selected' : ''; ?>>
							<?php echo htmlspecialchars($country['name']); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<label for="country">Country</label>
			</div>
			<div class="dropdown-divider"></div>
			<div class="form-row">
				<div class="col-sm-auto">
					<div class="mt-2 text-end">
						<button type="button" id="save-profile" name="save-profile" <?php if (getenv('USER_EMAIL')){?>disabled<?php } ?> class="btn btn-primary">Update</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="col-12 text-end mt-3">
	<a href="#" id="export-my-data" class="text-primary">Export my data</a>
	<div id="export-loading" class="text-center mt-2" style="display: none;">
		<i class="fa fa-spinner fa-spin"></i> Exporting data, please wait...
	</div>	
</div>
<div class="col-12 text-end mt-3">
	<a href="#" id="delete-profile" class="text-danger">Delete my profile</a>
</div>

<script>
$(document).ready(function () {
	$('#delete-profile').click(function(e) {
		e.preventDefault();
		bootbox.confirm({
			title: "Delete Profile",
			message: "Are you sure you want to delete your profile? This action cannot be reverted and all data will be lost.",
			buttons: {
				confirm: {
					label: 'DELETE PROFILE',
					className: 'btn-danger'
				},
				cancel: {
					label: 'Cancel',
					className: 'btn-secondary'
				}
			},
			callback: function (result) {
				if (result) {
					$.ajax({
						url: '/core/core.php',
						type: 'GET',
						data: { 
							action: 'deleteprofile'
						},
						dataType: 'json',
						success: function (data) {
							if (data.success) {
								window.location.href = '/logout.php';
							} else if (data.error) {
								$('#msgU').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error + '</div>');
							}
						},
						error: function (xhr, status, error) {
							$('#msgU').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. ' + error + '</div>');
						}
					});
				}
			}
		});
	});

	$("#password").val('');
    $(".toggle-password").click(function () {
        var passwordInput = $($(this).siblings(".password-input"));
        var icon = $(this);
        if (passwordInput.attr("type") == "password") {
            passwordInput.attr("type", "text");
            icon.removeClass("fa-eye").addClass("fa-eye-slash");
        } else {
            passwordInput.attr("type", "password");
            icon.removeClass("fa-eye-slash").addClass("fa-eye");
        }
    });

	$('#profile_pic').html('<img class="img-profile-avatar" src="<?=$doc['avatar']?: '/img/logo_def.png'; ?>">');
	
	$('#save-profile').click(function() {
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				action: 'update_user_profile',
				user_fname: $("#fullName").val(),			
				user_email: $("#email").val(),
				user_pass: $("#password").val(),
				user_country: $("#country").val()
			},
			dataType: 'json',
			success: function (data) {
				if(data.success){
					var msg = '<div class="alert alert-success"><i class="fa-solid fa-check mx-2"></i>'+data.success+'</div>';
				}else if( data.error){
					var msg = '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>'+data.error+'</div>';
				}
				$('#msgU').html(msg);
			},
			error: function (xhr, status, error) {
				$('#msgU').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
			}
		});
	});
	
	$('#upload-avatar').click(function() {
		var fd = new FormData();
		var files = $('#avatar')[0].files;
	
		if(files.length > 0 ){
			fd.append('avatar',files[0]);
		}
		$.ajax({ 
			url: '/core/core.php?update_user_avatar=1', 
			type: 'POST',
			data: fd,
			contentType: false,
			processData: false,
			cache: false,
			dataType: 'json',
			success: function (data) {
				if(data.success){
					var msg = '<div class="alert alert-success"><i class="fa-solid fa-check mx-2"></i>'+data.success.msg+'</div>';
					$('#profile_pic').html('<img class="img-profile-avatar" src="'+data.success.avatar+'">');
	
				}else if( data.error){
					var msg = '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>'+data.error+'</div>';
				}
				$('#msgU').html(msg);
			},
			error: function (xhr, status, error) {
				$('#msgU').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
			}
		  });
	});

	$('#export-my-data').click(function (e) {
        e.preventDefault();

        // Show the loading icon
        $('#export-loading').show();

        // Start the export process
        $.ajax({
            url: '/pages/export.php',
            type: 'GET',
            data: { kind: 'user-data' },
            xhrFields: {
                responseType: 'blob' // Handle binary data for file download
            },
            success: function (data, status, xhr) {
                // Create a download link for the exported file
                const blob = new Blob([data], { type: xhr.getResponseHeader('Content-Type') });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = xhr.getResponseHeader('Content-Disposition').split('filename=')[1];
                link.click();

                // Hide the loading icon
                $('#export-loading').hide();
            },
            error: function (xhr, status, error) {
                // Hide the loading icon and show an error message
                $('#export-loading').hide();
                alert('An error occurred while exporting data. Please try again.');
            }
        });
    });
});
</script>

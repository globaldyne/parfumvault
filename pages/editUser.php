<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');


$user = mysqli_fetch_array(mysqli_query($conn, "SELECT email,fullName FROM users")); 
$doc = mysqli_fetch_array(mysqli_query($conn,"SELECT docData AS avatar FROM documents WHERE ownerID = '".$_SESSION['userID']."' AND name = 'avatar' AND type = '3'"));

?>


<style>

.container {
  max-width: 100%;
  width: 100%;

}

</style>
<div class="container">

	<div class="row">
      <div class="col-md-4">
        <div class="text-center">
          <div id="profile_pic"><div class="loader"></div></div>
          <h6>Upload a different photo...</h6>
          <input type="file" name="avatar" id="avatar" class="form-control">
        </div>
        <div class="dropdown-divider"></div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
			<div class="text-right">
        		<input name="upload-avatar" type="submit" class="btn btn-dark" id="upload-avatar" value="Upload" />
        	</div>
        </div>
      </div>
      <div class="col-md-8 personal-info">
        <div id="msgU"></div>
        
          <div class="form-row">
            <label class="col-sm-3 control-label">Full name:</label>
            <div class="col-sm-8">
              <input name="fullName" type="text" id="fullName" class="mb-2 form-control" value="<?php echo $user['fullName']; ?>">
            </div>
          </div>
          <div class="form-row">
            <label class="col-sm-3 control-label">Email:</label>
            <div class="col-md-8">
              <input name="email" type="text" id="email" class="mb-2 form-control" value="<?php echo $user['email']; ?>">
            </div>
          </div>
          <div class="form-row">
            <label class="col-sm-3 control-label">Password:</label>
            <div class="col-md-8 password-input-container">
              <input name="password" type="password" id="password" class="mb-2 form-control password-input" value="">
              <i class="toggle-password fa fa-eye"></i>
            </div>
          </div>
          <div class="dropdown-divider"></div>
          <div class="form-row">
			<div class="col-sm-12">
				<div class="mt-2 text-right">
					<button type="button" id="save-profile" name="save-profile" class="btn btn-primary">Update</button>
				</div>
			</div>
          </div>

      </div>
  </div>
</div>
<hr>
<script>
$(document).ready(function () {
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
		url: '/pages/update_settings.php', 
		type: 'POST',
		data: {
			update_user_profile: 1,
			user_fname: $("#fullName").val(),			
			user_email: $("#email").val(),
			user_pass: $("#password").val()
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success">'+data.success+'</div>';
			}else if( data.error){
				var msg = '<div class="alert alert-danger">'+data.error+'</div>';
			}
			$('#msgU').html(msg);
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
		url: '/pages/update_settings.php?update_user_avatar=1', 
		type: 'POST',
		data: fd,
		contentType: false,
      	processData: false,
		cache: false,
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success">'+data.success.msg+'</div>';
				$('#profile_pic').html('<img class="img-profile-avatar" src="'+data.success.avatar+'">');

			}else if( data.error){
				var msg = '<div class="alert alert-danger">'+data.error+'</div>';
			}
			$('#msgU').html(msg);
		}
	  });
});
});
</script>
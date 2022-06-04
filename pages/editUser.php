<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');


$user = mysqli_fetch_array(mysqli_query($conn, "SELECT email,fullName,avatar FROM users")); 

?>
<script src="/js/jquery/jquery.min.js"></script>

<link href="/css/sb-admin-2.css" rel="stylesheet">
<link href="/css/bootstrap.min.css" rel="stylesheet">
<link href="/css/vault.css" rel="stylesheet">

<style>
.form-inline .form-control {
    display: inline-block;
    width: 500px;
    vertical-align: middle;
}
</style>
<table width="100%" border="0" cellspacing="0" class="table table-bordered">
    <tr>
      <td colspan="4" class="badge-primary">Edit user profile</td>
    </tr>
    <tr>
      <td colspan="4"><div id="msg"></div></td>
    </tr>
    <tr>
      <td width="11%">Full Name:</td>
      <td colspan="2"><input name="fullName" type="text" id="fullName" value="<?php echo $user['fullName']; ?>"></td>
      <td width="62%" rowspan="4"><div id="profile_pic"></div></td>
    </tr>
    <tr>
      <td>Email:</td>
      <td colspan="2"><input name="email" type="text" id="email" value="<?php echo $user['email']; ?>"></td>
    </tr>
    <tr>
      <td>Password:</td>
      <td colspan="2"><input name="password" type="password" id="password"> 
        Min 5 chars</td>
    </tr>
    <tr>
      <td>Avatar:</td>
      <td width="21%"><input type="file" name="avatar" id="avatar" /></td>
      <td width="6%"><input name="upload-avatar" type="submit" class="btn-dark" id="upload-avatar" value="Upload" /></td>
    </tr>
    <tr>
      <td colspan="4"><input name="save-profile" type="submit" class="btn-dark" id="save-profile" value="Update"></td>
    </tr>
  </table>

<script>
$('#profile_pic').html('<img class="img-profile-avatar" src="<?=$user['avatar']?: '/img/logo_def.png'; ?>">');

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
			$('#msg').html(msg);
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
			$('#msg').html(msg);
		}
	  });
});
</script>
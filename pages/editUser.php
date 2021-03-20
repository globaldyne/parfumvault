<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
if($_GET['id']){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	
	if($_POST['fullName'] && $_POST['email']){
		$fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
		$email = mysqli_real_escape_string($conn, $_POST['email']);
		
		$filename = $_FILES["avatar"]["tmp_name"];  
    	$file_ext = strtolower(end(explode('.',$_FILES['avatar']['name'])));
		$file_tmp = $_FILES['avatar']['tmp_name'];
    	$ext = explode(', ',strtolower($allowed_ext));
		
		if($filename){
			if (!file_exists("../uploads/logo/")) {
				mkdir("../uploads/logo/", 0740, true);
	  	 	}
			if(in_array($file_ext,$ext)===false){
				echo '<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file.</div>';
			}else{
				if($_FILES["avatar"]["size"] > 0){
					move_uploaded_file($file_tmp,"../uploads/logo/".base64_encode($filename));
					$avatar = "uploads/logo/".base64_encode($filename);
					if(mysqli_query($conn, "UPDATE users SET avatar = '$avatar' WHERE id = '$id'")){
						$msg = '<div class="alert alert-success alert-dismissible">User avatar updated!</div>';
					}
				}
			}
				
		}
		
		if(mysqli_num_rows(mysqli_query($conn, "SELECT email FROM users WHERE email = '$email' AND NOT id = '$id'"))){
			$msg.= '<div class="alert alert-danger alert-dismissible">Email is already in use.</div>';
		}else{
			if($password = mysqli_real_escape_string($conn, $_POST['password'])){
				if(strlen($password) < '5'){
					$msg.='<div class="alert alert-danger alert-dismissible"><strong>Error: </strong>Password must be at least 5 chars long!</div>';
				}else{
					$p = ",password=PASSWORD('$password')";
				}
			}
			if(mysqli_query($conn, "UPDATE users SET fullName = '$fullName', email = '$email' $p WHERE id='$id'")){
				$msg.= '<div class="alert alert-success alert-dismissible">User updated!</div>';
			}else{
				$msg.= '<div class="alert alert-danger alert-dismissible">Failed to update user details! ('.mysqli_error($conn).')</div>';
			}
		}
	}
$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'")); 
}
?>
<link href="../css/sb-admin-2.css" rel="stylesheet">
<link href="../css/bootstrap.min.css" rel="stylesheet">
<script src="../js/jquery/jquery.min.js"></script>

<script>
$(function() {
  $("#password").val(null);
});
</script>
<style>
.form-inline .form-control {
    display: inline-block;
    width: 500px;
    vertical-align: middle;
}
</style>
<form action="?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data" name="form1">
<table class="table table-bordered" cellspacing="0">
    <tr>
      <td colspan="2" class="badge-primary">Edit <?php echo $user['username']; ?></td>
    </tr>
    <tr>
      <td colspan="2"><?php echo $msg; ?></td>
    </tr>
    <tr>
      <td width="12%">Full Name:</td>
      <td width="88%"><input name="fullName" type="text" id="fullName" value="<?php echo $user['fullName']; ?>"></td>
    </tr>
    <tr>
      <td>Email:</td>
      <td><input name="email" type="text" id="email" value="<?php echo $user['email']; ?>"></td>
    </tr>
    <tr>
      <td>Password:</td>
      <td><input name="password" type="password" id="password"> 
        Min 5 chars</td>
    </tr>
    <tr>
      <td>Avatar:</td>
      <td><input type="file" name="avatar" id="avatar" /></td>
    </tr>
    <tr>
      <td colspan="2"><input name="update" type="submit" class="btn-dark" id="update" value="Submit"></td>
    </tr>
  </table>

</form>

<?php
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
if($_GET['id']){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	
	if($_POST['name']){
		$name = mysqli_real_escape_string($conn, $_POST['name']);
		$email = mysqli_real_escape_string($conn, $_POST['email']);
		$address = mysqli_real_escape_string($conn, $_POST['address']);
		$web = mysqli_real_escape_string($conn, $_POST['web']);

		if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM customers WHERE name = '$name' AND NOT id = '$id'"))){
			$msg.= '<div class="alert alert-danger alert-dismissible">Customer name already exists.</div>';
		}else{
			if(mysqli_query($conn, "UPDATE customers SET name = '$name', email = '$email', address = '$address', web = '$web' WHERE id='$id'")){
				$msg.= '<div class="alert alert-success alert-dismissible">Customer details updated!</div>';
			}else{
				$msg.= '<div class="alert alert-danger alert-dismissible">Failed to update customer details! ('.mysqli_error($conn).')</div>';
			}
		}
	}
$customer = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM customers WHERE id = '$id'")); 
}
?>
<link href="../css/sb-admin-2.css" rel="stylesheet">
<link href="../css/bootstrap.min.css" rel="stylesheet">

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
      <td colspan="2" class="badge-primary">Edit <?php echo $customer['name']; ?></td>
    </tr>
    <tr>
      <td colspan="2"><?php echo $msg; ?></td>
    </tr>
    <tr>
      <td width="12%">Name:</td>
      <td width="88%"><input name="name" type="text" id="name" value="<?php echo $customer['name']; ?>"></td>
    </tr>
    <tr>
      <td>Email:</td>
      <td><input name="email" type="text" id="email" value="<?php echo $customer['email']; ?>"></td>
    </tr>
    <tr>
      <td>Web Site:</td>
      <td><input name="web" type="text" id="web" value="<?php echo $customer['web']; ?>"></td>
    </tr>
    <tr>
      <td>Address:</td>
      <td><textarea name="address" id="address" cols="45" rows="5"><?php echo $customer['address']; ?></textarea></td>
    </tr>
    <tr>
      <td colspan="2"><input name="update" type="submit" class="btn-dark" id="update" value="Submit"></td>
    </tr>
  </table>

</form>
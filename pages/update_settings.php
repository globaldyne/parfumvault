<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/pvOnline.php');

if($_POST['manage'] == 'general'){
	$currency = utf8_encode(htmlentities($_POST['currency']));
	$top_n = mysqli_real_escape_string($conn, $_POST['top_n']);
	$heart_n = mysqli_real_escape_string($conn, $_POST['heart_n']);
	$base_n = mysqli_real_escape_string($conn, $_POST['base_n']);
	$qStep = mysqli_real_escape_string($conn, $_POST['qStep']);
	$defCatClass = mysqli_real_escape_string($conn, $_POST['defCatClass']);
	$pubchem_view = mysqli_real_escape_string($conn, $_POST['pubchem_view']);

	if($_POST["chem_vs_brand"] == 'true') {
		$chem_vs_brand = '1';
	}else{
		$chem_vs_brand = '0';
	}
	
	if($_POST["grp_formula"] == 'true') {
		$grp_formula = '1';
	}else{
		$grp_formula = '0';
	}
	
	if($_POST["pubChem"] == 'true') {
		$pubChem = '1';
	}else{
		$pubChem = '0';
	}
	
	if($_POST["chkVersion"] == 'true') {
		$chkVersion = '1';
	}else{
		$chkVersion = '0';
	}
		
	if(mysqli_query($conn, "UPDATE settings SET currency = '$currency', top_n = '$top_n', heart_n = '$heart_n', base_n = '$base_n', chem_vs_brand = '$chem_vs_brand', grp_formula = '$grp_formula', pubChem='$pubChem', chkVersion='$chkVersion', qStep = '$qStep', defCatClass = '$defCatClass', pubchem_view = '$pubchem_view'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Settings updated!</div>';	
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>An error occured.</div>';	
	}
	return;
}

//PERFUME TYPES
if($_POST['manage'] == 'perfume_types'){
	$edp = utf8_encode(htmlentities($_POST['edp']));
	$edc = mysqli_real_escape_string($conn, $_POST['edc']);
	$edt = mysqli_real_escape_string($conn, $_POST['edt']);
	$parfum = mysqli_real_escape_string($conn, $_POST['parfum']);
	
	if(mysqli_query($conn, "UPDATE settings SET EDP = '$edp', EDT = '$edt', EDC = '$edc', Parfum = '$parfum'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Settings updated!</div>';	
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>An error occured.</div>';	
	}
	return;
}
	
if($_POST['manage'] == 'print'){
	$label_printer_addr = mysqli_real_escape_string($conn, $_POST['label_printer_addr']);
	$label_printer_model = mysqli_real_escape_string($conn, $_POST['label_printer_model']);
	$label_printer_size = mysqli_real_escape_string($conn, $_POST['label_printer_size']);
	$label_printer_font_size = mysqli_real_escape_string($conn, $_POST['label_printer_font_size']);

	if(mysqli_query($conn, "UPDATE settings SET label_printer_addr='$label_printer_addr', label_printer_model='$label_printer_model', label_printer_size='$label_printer_size', label_printer_font_size='$label_printer_font_size'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Settings updated!</div>';	
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>An error occured.</div>';	
	}
	return;
}

//PV ONLINE
if($_POST['manage'] == 'pvonline'){
	$pv_online_email = mysqli_real_escape_string($conn, $_POST['pv_online_email']);
	$pv_online_pass = mysqli_real_escape_string($conn, $_POST['pv_online_pass']);
	
	if(empty($pv_online_email) || empty($pv_online_pass)){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Missing fields.</div>';
		return;
	}
	$valAcc = pvOnlineValAcc($pvOnlineAPI, $pv_online_email, $pv_online_pass, $ver);

    if($valAcc == 'Failed'){
       	echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Invalid credentials or your PV Online account is inactive.</div>';
		return;
	}
	if(mysqli_query($conn, "INSERT pv_online (id,email,password) VALUES ('1','$pv_online_email', '$pv_online_pass') ON DUPLICATE KEY UPDATE id = '1', email = '$pv_online_email', password = '$pv_online_pass'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>PV Online details updated!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error updating PV Online info.</div>';
	}

	return;
}

//BRAND
if($_POST['manage'] == 'brand'){
	$brandName = mysqli_real_escape_string($conn, $_POST['brandName']);
	$brandAddress = mysqli_real_escape_string($conn, $_POST['brandAddress']);
	$brandEmail = mysqli_real_escape_string($conn, $_POST['brandEmail']);
	$brandPhone = mysqli_real_escape_string($conn, $_POST['brandPhone']);

	if(mysqli_query($conn, "UPDATE settings SET brandName = '$brandName', brandAddress = '$brandAddress', brandEmail = '$brandEmail', brandPhone = '$brandPhone'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Brand details updated!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error updating brand info.</div>';
	}
	return;
}

//ADD USERS
if($_POST['manage'] == 'user'){
	$username = mysqli_real_escape_string($conn, $_POST['username']);
	$password = mysqli_real_escape_string($conn, $_POST['password']);
	$fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	
	if(empty($username) || empty($fullName) || empty($email)){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Missing fields.</div>';
		return;
	}
	if (strlen($password) < '5') {
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>Password must be at least 5 chars long!</div>';
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT username FROM users WHERE username = '$username' OR email = '$email' "))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$username.' already exists or email is re-used!</div>';
		return;
	}
	
	if(mysqli_query($conn, "INSERT INTO users (username,password,fullName,email) VALUES ('$username', PASSWORD('$password'), '$fullName', '$email')")){
		echo '<div class="alert alert-success alert-dismissible">User added!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible">Error adding user.</div>';
	}
	return;	
}

//ADD CATEGORY
if($_POST['manage'] == 'category'){
	$cat = mysqli_real_escape_string($conn, $_POST['category']);
	$notes = mysqli_real_escape_string($conn, $_POST['cat_notes']);
	
	if(empty($cat)){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Category namae is required.</div>';
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingCategory WHERE name = '$cat'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$cat.' already exists!</div>';
		return;
	}
	if(mysqli_query($conn, "INSERT INTO ingCategory (name,notes) VALUES ('$cat', '$notes')")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Category added!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error adding category</div>';
	}
	return;
}					

//DELETE CATEGORY
if($_POST['action'] == 'delete' && $_POST['catId']){
	$catId = mysqli_real_escape_string($conn, $_POST['catId']);
	if(mysqli_query($conn, "DELETE FROM ingCategory WHERE id = '$catId'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Category deleted!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error deleting category.</div>';
	}
	return;
}

//DELETE USER
if($_POST['action'] == 'delete' && $_POST['userId']){
	$userId = mysqli_real_escape_string($conn, $_POST['userId']);
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users")) <= 1){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error, at least one user needs to exist.</div>';
		return;	
	}
	if(mysqli_query($conn, "DELETE FROM users WHERE id = '$userId'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>User deleted!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error deleting user.</div>';
	}
	
	return;
}



?>

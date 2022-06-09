<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/pvOnline.php');
require_once(__ROOT__.'/func/create_thumb.php');

if($_POST['update_pvonline_profile']){
	
	if(!$_POST['nickname'] || !$_POST['intro']){
		$response["error"] = "All fields are required";
		echo json_encode($response);
		return;
	}
	
	$doc = mysqli_fetch_array(mysqli_query($conn,"SELECT docData AS avatar FROM documents WHERE ownerID = '".$_SESSION['userID']."' AND name = 'avatar' AND type = '3'"));

	$intro = base64_encode(mysqli_real_escape_string($conn, $_POST['intro']));
	
	$data = [ 'username' => strtolower($pv_online['email']), 'password' => $pv_online['password'],'do' => 'updateProfile','nickname' => base64_encode($_POST['nickname']), 'intro' => $intro, 'avatar' => $doc['avatar'] ];

    $req = json_decode(pvPost($pvOnlineAPI, $data));

	if($req->success){
		$response['success'] = $req->success;
	}else{
		$response['error'] = $req->error;
	}
	
	echo json_encode($response);
	
	

	return;
}

if($_GET['update_user_avatar']){
	$allowed_ext = "png, jpg, jpeg, gif, bmp";

	$filename = $_FILES["avatar"]["tmp_name"];  
    $file_ext = strtolower(end(explode('.',$_FILES['avatar']['name'])));
	$file_tmp = $_FILES['avatar']['tmp_name'];
    $ext = explode(', ',strtolower($allowed_ext));

	
	if(!$filename){
		$response["error"] = 'Please choose a file to upload';
		echo json_encode($response);
		return;
	}	
	
	if (!file_exists(__ROOT__."/uploads/logo/")) {
		mkdir(__ROOT__."/uploads/logo/", 0740, true);
	}
		
	if(in_array($file_ext,$ext)===false){
		$response["error"] = '<strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file';
		echo json_encode($response);
		return;
	}
		
	if($_FILES["avatar"]["size"] > 0){
		move_uploaded_file($file_tmp,__ROOT__."/uploads/logo/".base64_encode($filename));
		$avatar = "/uploads/logo/".base64_encode($filename);		
		create_thumb(__ROOT__.$avatar,250,250); 
		$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents(__ROOT__.$avatar));
		
		mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '".$user['id']."' AND type = '3' AND name = 'avatar'");
		if(mysqli_query($conn, "INSERT INTO documents (ownerID,type,name,notes,docData) VALUES ('".$user['id']."','3','avatar','Main Profile Avatar','$docData')")){	
			unlink(__ROOT__.$avatar);
			$response["success"] = array( "msg" => "User avatar updated!", "avatar" => $docData);
			echo json_encode($response);
			return;
		}
	}

	return;
}

if($_POST['update_user_profile']){
	
	if(!$_POST['user_fname'] || !$_POST['user_email'] || !$_POST['user_pass']){
		$response["error"] = "All fields are required";
		echo json_encode($response);
		return;
	}
	
	$fullName = mysqli_real_escape_string($conn, $_POST['user_fname']);
	$email = mysqli_real_escape_string($conn, $_POST['user_email']);
	
	if($password = mysqli_real_escape_string($conn, $_POST['user_pass'])){
		if(strlen($password) < '5'){
			$response["error"] = "Password must be at least 5 characters long";
			echo json_encode($response);
			return;
		}else{
			$p = ",password='$password'";
		}
	}
	
	if(mysqli_query($conn, "UPDATE users SET fullName = '$fullName', email = '$email' $p")){
		$response["success"] = "User details updated!";
		echo json_encode($response);
	}else{
		$response["error"] = 'Failed to update user details! '.mysqli_error($conn);
		echo json_encode($response);
	}
	

	return;
}

if($_POST['manage'] == 'general'){
	$currency = utf8_encode(htmlentities($_POST['currency']));
	$top_n = mysqli_real_escape_string($conn, $_POST['top_n']);
	$heart_n = mysqli_real_escape_string($conn, $_POST['heart_n']);
	$base_n = mysqli_real_escape_string($conn, $_POST['base_n']);
	$qStep = mysqli_real_escape_string($conn, $_POST['qStep']);
	$defCatClass = mysqli_real_escape_string($conn, $_POST['defCatClass']);
	$grp_formula = mysqli_real_escape_string($conn, $_POST['grp_formula']);
	$pubchem_view = mysqli_real_escape_string($conn, $_POST['pubchem_view']);
	$mUnit = mysqli_real_escape_string($conn, $_POST['mUnit']);

	if($_POST["chem_vs_brand"] == 'true') {
		$chem_vs_brand = '1';
	}else{
		$chem_vs_brand = '0';
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
	
	if($_POST["multi_dim_perc"] == 'true') {
		$multi_dim_perc = '1';
	}else{
		$multi_dim_perc = '0';
	}
	
	if(mysqli_query($conn, "UPDATE settings SET currency = '$currency', top_n = '$top_n', heart_n = '$heart_n', base_n = '$base_n', chem_vs_brand = '$chem_vs_brand', grp_formula = '$grp_formula', pubChem='$pubChem', chkVersion='$chkVersion', qStep = '$qStep', defCatClass = '$defCatClass', pubchem_view = '$pubchem_view', multi_dim_perc = '$multi_dim_perc', mUnit = '$mUnit'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Settings updated!</div>';	
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>An error occured.</div>';	
	}
	return;
}

if($_POST['manage'] == 'api'){
	
	$api = $_POST['api'];
	$api_key = mysqli_real_escape_string($conn, $_POST['api_key']);
	if(strlen($api_key) < 8){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>API key must be at least 8 characters long.</div>';	
		return;
	}
	if($_POST["api"] == 'true') {
		$api = '1';
	}else{
		$api = '0';
	}
	
	if(mysqli_query($conn, "UPDATE settings SET api = '$api', api_key='$api_key'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>API settings updated!</div>';	
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

	if($_POST['state_update']) {
		
		$pv_online_state = (int)$_POST['pv_online_state'];	
	
		if(mysqli_query($conn, "UPDATE pv_online SET enabled = '$pv_online_state'")){
			if($pv_online_state == '1'){
				$response['success'] = 'active';
			}elseif($pv_online_state == '0'){
				$response['success'] = 'in-active';
			}else{
				$response['error'] = mysqli_error();
			}
			echo json_encode($response);
		}
		return;
	}
	
	if($_POST['share_update']) {
		$pv_online_share = (int)$_POST['pv_online_share'];
		
		$params = "?username=".$pv_online['email']."&password=".$pv_online['password']."&do=formulaSharingState&state=$pv_online_share";
        $req = json_decode(pvUploadData($pvOnlineAPI.$params, null));

		if($req){
			$response['success'] = $req->success;
		}else{
			$response['error'] = 'Unable to update '.$req->error;
		}
		
		echo json_encode($response);
		return;	
	}


	echo json_encode($response);
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

//ADD CATEGORY
if($_POST['manage'] == 'category'){
	$cat = mysqli_real_escape_string($conn, $_POST['category']);
	$notes = mysqli_real_escape_string($conn, $_POST['cat_notes']);
	
	if(empty($cat)){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Category name is required.</div>';
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

//ADD FORMULA CATEGORY
if($_POST['manage'] == 'add_frmcategory'){
	$cat = mysqli_real_escape_string($conn, $_POST['category']);
	$type = mysqli_real_escape_string($conn, $_POST['cat_type']);
	
	if(empty($cat)){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Category name is required.</div>';
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulaCategories WHERE name = '$cat'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$cat.' already exists!</div>';
		return;
	}
	if(mysqli_query($conn, "INSERT INTO formulaCategories (name,cname,type) VALUES ('$cat', '".strtolower(str_replace(' ', '',$cat))."', '$type')")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Category added!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error adding category</div>';
	}
	return;
}					

//DELETE FORMULA CATEGORY
if($_POST['action'] == 'del_frmcategory' && $_POST['catId']){
	$catId = mysqli_real_escape_string($conn, $_POST['catId']);
	if(mysqli_query($conn, "DELETE FROM formulaCategories WHERE id = '$catId'")){
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

//DELETE LID
if($_POST['action'] == 'delete' && $_POST['lidId']){
	$id = mysqli_real_escape_string($conn, $_POST['lidId']);
	
	if(mysqli_query($conn, "DELETE FROM lids WHERE id = '$id'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="?do=lids" class="close" data-dismiss="alert" aria-label="close">x</a>Item removed!</div>';
	}
	return;	
}

//DELETE BOTTLE
if($_POST['action'] == 'delete' && $_POST['btlId']){
	$id = mysqli_real_escape_string($conn, $_POST['btlId']);
	
	if(mysqli_query($conn, "DELETE FROM bottles WHERE id = '$id'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="?do=bottles" class="close" data-dismiss="alert" aria-label="close">x</a>Item removed!</div>';
	}
	return;	
}

//Update ingredients view
if($_GET['ingView']){
	$v = $_GET['ingView'];
	mysqli_query($conn, "UPDATE settings SET defIngView = '$v'");
	return;	
}
?>

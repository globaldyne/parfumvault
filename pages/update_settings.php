<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/pvOnline.php');
require_once(__ROOT__.'/func/create_thumb.php');


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
	
	if (!file_exists($tmp_path."/uploads/logo/")) {
		mkdir($tmp_path."/uploads/logo/", 0740, true);
	}
		
	if(in_array($file_ext,$ext)===false){
		$response["error"] = '<strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file';
		echo json_encode($response);
		return;
	}
		
	if($_FILES["avatar"]["size"] > 0){
		move_uploaded_file($file_tmp,$tmp_path."/uploads/logo/".base64_encode($filename));
		$avatar = "/uploads/logo/".base64_encode($filename);		
		create_thumb($tmp_path.$avatar,250,250); 
		$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents($tmp_path.$avatar));
		
		mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '".$user['id']."' AND type = '3' AND name = 'avatar'");
		if(mysqli_query($conn, "INSERT INTO documents (ownerID,type,name,notes,docData) VALUES ('".$user['id']."','3','avatar','Main Profile Avatar','$docData')")){	
			unlink($tmp_path.$avatar);
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
	
	if(strlen($_POST['user_fname']) < '5'){
		$response['error'] = "Full name must be at least 5 characters long!";
		echo json_encode($response);
		return;
	}
	
	$fullName = mysqli_real_escape_string($conn, $_POST['user_fname']);
	$email = mysqli_real_escape_string($conn, $_POST['user_email']);
	
//	$password = password_hash($password, PASSWORD_DEFAULT);

	if($password){
		if(strlen($password) < '5'){
			$response["error"] = "Password must be at least 5 characters long";
			echo json_encode($response);
			return;
		}else{
			$p = ",password=PASSWORD('$password')";
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
	$currency = $_POST['currency'];
	$currency_code = $_POST['currency_code'];

	$top_n = mysqli_real_escape_string($conn, $_POST['top_n']);
	$heart_n = mysqli_real_escape_string($conn, $_POST['heart_n']);
	$base_n = mysqli_real_escape_string($conn, $_POST['base_n']);
	
	$qStep = mysqli_real_escape_string($conn, $_POST['qStep']);
	$defCatClass = mysqli_real_escape_string($conn, $_POST['defCatClass']);
	$grp_formula = mysqli_real_escape_string($conn, $_POST['grp_formula']);
	$pubchem_view = mysqli_real_escape_string($conn, $_POST['pubchem_view']);
	$mUnit = mysqli_real_escape_string($conn, $_POST['mUnit']);
	$editor = mysqli_real_escape_string($conn, $_POST['editor']);
	$user_pref_eng = mysqli_real_escape_string($conn, $_POST['user_pref_eng']);
	$defPercentage =  $_POST['defPercentage'];
	$bs_theme = $_POST['bs_theme'];
	$temp_sys = $_POST['temp_sys'];
	
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
		
	if(empty($_POST['pv_host']) || empty($_POST['currency'])){
		$response["error"] = 'Fields cannot be empty';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn, "UPDATE settings SET currency = '$currency', currency_code = '$currency_code', top_n = '$top_n', heart_n = '$heart_n', base_n = '$base_n', chem_vs_brand = '$chem_vs_brand', grp_formula = '$grp_formula', pubChem='$pubChem', chkVersion='$chkVersion', qStep = '$qStep', defCatClass = '$defCatClass', pubchem_view = '$pubchem_view', multi_dim_perc = '$multi_dim_perc', mUnit = '$mUnit', editor = '$editor', user_pref_eng = '$user_pref_eng', pv_host = '".$_POST['pv_host']."', defPercentage = '$defPercentage', bs_theme = '$bs_theme', temp_sys = '$temp_sys' ")){
		$response["success"] = 'Settings updated';
	}else{
		$response["error"] = 'An error occured '.mysqli_error($conn);	
	}
	echo json_encode($response);
	return;
}

if($_POST['manage'] == 'api'){
	
	$api = $_POST['api'];
	$api_key = mysqli_real_escape_string($conn, $_POST['api_key']);
	if(strlen($api_key) < 8){
		$response['error'] =  'API key must be at least 8 characters long';	
		echo json_encode($response);
		return;
	}
	if($_POST["api"] == 'true') {
		$api = '1';
	}else{
		$api = '0';
	}
	
	if(mysqli_query($conn, "UPDATE settings SET api = '$api', api_key='$api_key'")){
		$response['success'] = 'API settings updated!';	
	}else{
		$response['error'] = 'An error occured '.mysqli_error($conn);	
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
		$response['success'] = 'Brand details updated';
	}else{
		$response['error'] = 'Error updating brand info';
	}
	echo json_encode($response);
	return;
}

//ADD CATEGORY
if($_POST['manage'] == 'category'){
	$cat = mysqli_real_escape_string($conn, $_POST['category']);
	$notes = mysqli_real_escape_string($conn, $_POST['cat_notes']);
	
	if(empty($cat)){
		$response["error"] = 'Category name is required.';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingCategory WHERE name = '$cat'"))){
		$response["error"] = 'Category name '.$cat.' already exists!';
		echo json_encode($response);
		return;
	}
	if(mysqli_query($conn, "INSERT INTO ingCategory (name,notes) VALUES ('$cat', '$notes')")){
		$response["success"] = 'Category '.$cat.' added!';
		echo json_encode($response);
	}else{
		$response["error"] = 'Something went wrong, '.mysqli_error($conn);
		echo json_encode($response);
	}
	return;
}					

//DELETE CATEGORY
if($_POST['action'] == 'delete' && $_POST['catId']){
	$catId = mysqli_real_escape_string($conn, $_POST['catId']);
	if(mysqli_query($conn, "DELETE FROM ingCategory WHERE id = '$catId'")){
		$response["success"] = 'Category deleted';
	}else{
		$response["error"] = 'Error deleting category';
	}
	echo json_encode($response);
	return;
}

//ADD INGREDIENT PROFILE
if($_POST['manage'] == 'add_ingprof'){
	$profile = mysqli_real_escape_string($conn, $_POST['profile']);
	$description = mysqli_real_escape_string($conn, $_POST['description']);
	
	if(empty($profile)){
		$response["error"] = 'Profile name is required.';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingProfiles WHERE name = '$profile'"))){
		$response["error"] = 'Profile name '.$profile.' already exists';
		echo json_encode($response);
		return;
	}
	if(mysqli_query($conn, "INSERT INTO ingProfiles (name,notes) VALUES ('$profile', '$description')")){
		$response["success"] = 'Profile '.$profile.' added';
		echo json_encode($response);
	}else{
		$response["error"] = 'Something went wrong, '.mysqli_error($conn);
		echo json_encode($response);
	}
	return;
}					

//DELETE INGREDIENT PROFILE
if($_POST['action'] == 'ingProfile' && $_POST['profId']){
	$profId = mysqli_real_escape_string($conn, $_POST['profId']);
	if(mysqli_query($conn, "DELETE FROM ingProfiles WHERE id = '$profId'")){
		$response["success"] = 'Profile deleted';
	}else{
		$response["error"] = 'Error deleting profile';
	}
	echo json_encode($response);
	return;
}

//ADD FORMULA CATEGORY
if($_POST['manage'] == 'add_frmcategory'){
	$cat = mysqli_real_escape_string($conn, $_POST['category']);
	$type = mysqli_real_escape_string($conn, $_POST['cat_type']);
	
	if(empty($cat)){
		$response["error"] = 'Category name is required.';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulaCategories WHERE name = '$cat'"))){
		$response["error"] = 'Category name '.$cat.' already exists!';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn, "INSERT INTO formulaCategories (name,cname,type) VALUES ('$cat', '".strtolower(str_replace(' ', '',$cat))."', '$type')")){
		$response["success"] = 'Category '.$cat.' created!';
	}else{
		$response["error"] = 'Something went wrong, '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}					

//DELETE FORMULA CATEGORY
if($_POST['action'] == 'del_frmcategory' && $_POST['catId']){
	$catId = mysqli_real_escape_string($conn, $_POST['catId']);
	if(mysqli_query($conn, "DELETE FROM formulaCategories WHERE id = '$catId'")){
		$response["success"] = 'Category deleted';
	}else{
		$response["error"] = 'Error deleting category';
	}
	echo json_encode($response);
	return;
}


?>

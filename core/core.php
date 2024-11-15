<?php 
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/func/labelMap.php');
require_once(__ROOT__.'/func/get_formula_notes.php');
require_once(__ROOT__.'/func/pvOnline.php');
require_once(__ROOT__.'/func/pvFileGet.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/func/sanChar.php');
require_once(__ROOT__.'/func/priceScrape.php');
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
		$response["success"] = "User details updated";
		echo json_encode($response);
	}else{
		$response["error"] = 'Failed to update user details '.mysqli_error($conn);
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


//DELETE BATCH
if($_POST['action'] == 'batch' && $_POST['bid'] && $_POST['remove'] == true){
	$id = mysqli_real_escape_string($conn, $_POST['bid']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "DELETE FROM batchIDHistory WHERE id = '$id'")){
	
		$response["success"] = 'Batch '.$id.' for product '.$name.' deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//UPDATE SDS DISCLAIMER
if($_POST['action'] == 'sdsDisclaimerContent'){
	$sds_disc_content = mysqli_real_escape_string($conn, $_POST['sds_disc_content']);
	
	if(empty($sds_disc_content)){
		$response["error"] = 'Disclaimer text is required.';
		echo json_encode($response);
		return;
	}


	if(mysqli_query($conn, "UPDATE settings SET  sds_disclaimer = '$sds_disc_content'")){
		$response["success"] = 'SDS Disclaimer text updated';
	}else{
		$response["error"] = 'Error '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//DELETE SDS
if($_POST['action'] == 'delete' && $_POST['SDSID'] && $_POST['type'] == 'SDS'){
	$id = mysqli_real_escape_string($conn, $_POST['SDSID']);
	
	if(mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '$id' AND isSDS = '1'")){
		mysqli_query($conn, "DELETE FROM sds_data WHERE id = '$id'");
		
		$response["success"] = 'SDS deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//ADD INVENTORY COMPOUND
if($_POST['action'] == 'add' && $_POST['type'] == 'invCmp'){
	$name = mysqli_real_escape_string($conn, $_POST['cmp_name']);
	$size = mysqli_real_escape_string($conn, $_POST['cmp_size']);
	
	if(empty($name)){
		$response["error"] = 'Name is required.';
		echo json_encode($response);
		return;
	}
	if(!is_numeric($size)){
		$response["error"] = 'Size can only be numeric';
		echo json_encode($response);
		return;
	}
	$batch_id = mysqli_real_escape_string($conn, $_POST['cmp_batch']);
	$location = mysqli_real_escape_string($conn, $_POST['cmp_location']);
	$description = mysqli_real_escape_string($conn, $_POST['cmp_desc']);
	$label_info = mysqli_real_escape_string($conn, $_POST['cmp_label_info']);

	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM inventory_compounds WHERE name = '$name'"))){
		$response["error"] = 'Error: '.$name.' already exists';
		
	}elseif(mysqli_query($conn, "INSERT INTO inventory_compounds (name,description,batch_id,size,owner_id,location,label_info) VALUES ('$name', '$description', '$batch_id', '$size', '".$user['id']."', '$location', '$label_info' )")){
		$response["success"] = 'Compound '.$name.' added';
	}else{
		$response["error"] = 'Error adding compound '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//UPDATE COMPOUND DATA
if($_POST['update_inv_compound_data']){
	
	if(!$_POST['name']){
		$response["error"] = "Name is required";
		echo json_encode($response);
		return;
	}
	
	$id = $_POST['cmp_id'];
	$name = $_POST['name'];
	$description = $_POST['description'];
	$batch_id = $_POST['batch_id'];
	$size = $_POST['size'];
	$location  = $_POST['location'] ?: '-';
	$label_info  = $_POST['label_info'] ?: '-';

	$q = mysqli_query($conn,"UPDATE inventory_compounds SET name = '$name', description = '$description', batch_id = '$batch_id', size = '$size', location = '$location', label_info = '$label_info' WHERE id = '$id'");
	

	if($q){
		$response['success'] = "Compound updated";
	}else{
		$response['error'] = "Error updating data ".mysqli_error($conn);
	}
	
	echo json_encode($response);
	
	

	return;
}

//DELETE COMPOUND
if($_POST['action'] == 'delete' && $_POST['compoundId'] && $_POST['type'] == 'invCmp'){
	$id = mysqli_real_escape_string($conn, $_POST['compoundId']);
	
	if(mysqli_query($conn, "DELETE FROM inventory_compounds WHERE id = '$id'")){
		$response["success"] = 'Compound deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}



//WIPE OUT FROMULAS
if($_POST['formulas_wipe'] == 'true'){
	
	if(mysqli_query($conn, "TRUNCATE formulas")){
		mysqli_query($conn, "TRUNCATE formulasMetaData");
		mysqli_query($conn, "TRUNCATE formula_history");
		mysqli_query($conn, "TRUNCATE formulasTags");
		mysqli_query($conn, "TRUNCATE ingredient_compounds");
		mysqli_query($conn, "TRUNCATE formulaCategories");
		mysqli_query($conn, "TRUNCATE formulasRevisions");
		mysqli_query($conn, "TRUNCATE makeFormula");

		$response["success"] = 'Formulas and related data deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//WIPE OUT INGREDIENTS
if($_POST['ingredient_wipe'] == 'true'){
	
	if(mysqli_query($conn, "TRUNCATE ingredients")){
		mysqli_query($conn, "TRUNCATE ingCategory");
		mysqli_query($conn, "TRUNCATE ingProfiles");
		mysqli_query($conn, "TRUNCATE ingReplacements");
		mysqli_query($conn, "TRUNCATE ingSafetyInfo");
		mysqli_query($conn, "TRUNCATE suppliers");
		mysqli_query($conn, "TRUNCATE synonyms");

		$response["success"] = 'Ingredients and related data deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}


//UPDATE CAS IFRA ENTRY
if($_REQUEST['IFRA'] == 'edit'){
	$type = $_REQUEST['type'];
	if(mysqli_query($conn, "UPDATE IFRALibrary SET $type = '".$_REQUEST['value']."' WHERE id = '".$_REQUEST['pk']."'")){
		$response["success"] = 'IFRA entry updated';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//DELETE IFRA ENTRY
if($_POST['IFRA'] == 'delete' && $_POST['ID'] && $_POST['type'] == 'IFRA'){
	
	if(mysqli_query($conn, "DELETE FROM IFRALibrary WHERE id = '".$_POST['ID']."'")){
		$response["success"] = 'IFRA entry deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//Merge ingredients
if($_POST['merge'] && $_POST['ingSrcID'] &&  $_POST['ingSrcName']  && $_POST['fid']){
	if(!$_POST['dest']){
		$response['error'] = 'Please select ingedient';
		echo json_encode($response);
    	return;
	}
	
	$dest = mysqli_fetch_array(mysqli_query($conn,"SELECT ingredient FROM formulas WHERE id = '".$_POST['dest']."'"));
	
	if($dest['ingredient'] == $_POST['ingSrcName']){
		$response['error'] = 'Source and destination ingredients cannot be the same';
		echo json_encode($response);
    	return;
	}
	
	$mq = "UPDATE formulas SET quantity = quantity + (SELECT quantity FROM formulas WHERE id ='".$_POST['ingSrcID']."' AND fid = '".$_POST['fid']."') WHERE id = '".$_POST['dest']."' AND fid = '".$_POST['fid']."'";
	
	if(mysqli_query($conn,$mq)){
		mysqli_query($conn,"DELETE FROM formulas WHERE id = '".$_POST['ingSrcID']."' AND fid = '".$_POST['fid']."'");
		$response['success'] = $_POST['ingSrcName'].' merged with '.$dest['ingredient'];
	}else{
		$response['error'] = 'Something went wrong..'.mysqli_error($conn);
	}
	
	echo json_encode($response);
    return;

}

//PVOnline Single Import						
if($_POST['action'] == 'import' && $_POST['source'] == 'PVOnline' && $_POST['kind'] == 'ingredient' && $_POST['ing_id']){
	$id = mysqli_real_escape_string($conn, $_POST['ing_id']);
	
	$jAPI = $pvOnlineAPI.'?request=ingredients&src=PV_PRO&id='.$id;
    $jsonData = json_decode(pv_file_get_contents($jAPI), true);

    if($jsonData['error']){
		$response['error'] = 'Error: '.$jsonData['error']['msg'];
		echo json_encode($response);
        return;
    }

    $array_data = $jsonData['ingredients'];
	
    foreach ($array_data as $id=>$row) {
      	$insertPairs = array();
		unset($row['structure']);
		unset($row['techData']);
		unset($row['ifra']);
		unset($row['IUPAC']);
		unset($row['id']);
			
         foreach ($row as $key=>$val){ 
		 
          	$insertPairs[addslashes($key)] = addslashes($val);
         }
		 
         $insertKeys = '`' . implode('`,`', array_keys($insertPairs)) . '`';
         $insertVals = '"' . implode('","', array_values($insertPairs)) . '"';
    
       	 $query = "SELECT name FROM ingredients WHERE name = '".$insertPairs['name']."'";
    
         if(!mysqli_num_rows(mysqli_query($conn, $query))){
           	$jsql = "INSERT INTO ingredients ({$insertKeys}) VALUES ({$insertVals});";
            if( $qIns = mysqli_query($conn,$jsql)){
				
             	$response["success"] = 'Ingredient data imported';
			}else{
				$response["error"] = 'Error: '.mysqli_error($conn);
			}
		 }else{
			 $response["error"] = 'Error: ingredient already exists';
		 }
	}
	

	
	echo json_encode($response);
	return;
}


//UPDATE BK PROVIDER
if ($_REQUEST['bkProv'] == 'update') {
    if ( empty($_POST['creds']) || empty($_POST['schedule']) || empty($_POST['bkDesc']) || empty($_POST['gdrive_name'])) {
        $response["error"] = 'Missing fields';
        echo json_encode($response);
        return;
    }
    
    $enabled = mysqli_real_escape_string($conn, $_POST['enabled']);
    $schedule = mysqli_real_escape_string($conn, $_POST['schedule']);
    $bkDesc = mysqli_real_escape_string($conn, $_POST['bkDesc']);
    $creds = mysqli_real_escape_string($conn, $_POST['creds']);
    $gdrive_name = mysqli_real_escape_string($conn, $_POST['gdrive_name']);
	$bk_srv_host = $_POST['bk_srv_host'];


    if (mysqli_query($conn, "UPDATE backup_provider SET credentials = '$creds', enabled = '$enabled', schedule = '$schedule', description = '$bkDesc', gdrive_name = '$gdrive_name' WHERE id = '1'")) {
		mysqli_query($conn, "UPDATE settings SET bk_srv_host = '$bk_srv_host'");
        $response["success"] = 'Settings updated';
    } else {
        $response["error"] = 'Error: ' . mysqli_error($conn);
    }

    echo json_encode($response);
    return;
}


//UPDATE HTML TEMPLATE
if($_REQUEST['action'] == 'htmlTmplUpdate'){
	$value = mysqli_real_escape_string($conn,$_POST['value']);
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE templates SET $name = '$value' WHERE id = '$id'")){
		$response["success"] = 'Template updated';
	}else{
		$response["error"] = mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

//DELETE HTML TEMPLATE
if($_POST['action'] == 'htmlTmplDelete' && $_POST['tmplID']){
	$id = $_POST['tmplID'];
	$name = $_POST['tmplName'];

	if(mysqli_query($conn, "DELETE FROM templates WHERE id = '$id'")){
		$response["success"] = 'Template '.$name.' deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//ADD NEW TEMPLATE
if($_POST['action'] == 'htmlTmplAdd'){
	
	if(empty($_POST['tmpl_name'])){
		$response["error"] = 'Name is required';
		echo json_encode($response);
		return;
	}
	
	if(empty($_POST['tmpl_content'])){
		$response["error"] = 'HTML Content is required';
		echo json_encode($response);
		return;
	}

	if(empty($_POST['tmpl_desc'])){
		$response["error"] = 'Description is required.';
		echo json_encode($response);
		return;
	}
	
	$name = mysqli_real_escape_string($conn,$_POST['tmpl_name']);
	$html = mysqli_real_escape_string($conn,$_POST['tmpl_content']);
	$desc = mysqli_real_escape_string($conn,$_POST['tmpl_desc']);

	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM templates WHERE name = '$name'"))){
		$response["error"] = $name.' already exists!';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn, "INSERT INTO templates (name,content,description) VALUES ('$name','$html','$desc')")){
		$response["success"] = $name.' created!';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}



//UPDATE PERFUME TYPES
if($_GET['perfType'] == 'update'){
	$value = trim(mysqli_real_escape_string($conn, $_POST['value']));
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE perfumeTypes SET $name = '$value' WHERE id = '$id'")){
		$response["success"] = 'Perfume type updated';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

//DELETE PERFUME TYPE
if($_POST['perfType'] == 'delete' && $_POST['pID']){
	$id = $_POST['pID'];
	$name = $_POST['pName'];

	if(mysqli_query($conn, "DELETE FROM perfumeTypes WHERE id = '$id'")){
		$response["success"] = 'Pefume type '.$name.' deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//ADD PERFUME TYPE
if($_POST['perfType'] == 'add'){
	
	if(empty($_POST['perfType_name'])){
		$response["error"] = 'Name is required';
		echo json_encode($response);
		return;
	}
	
	if(empty($_POST['perfType_conc'])){
		$response["error"] = 'Concentration is required';
		echo json_encode($response);
		return;
	}

	if(!is_numeric($_POST['perfType_conc'])){
		$response["error"] = 'Concentration must be integer.';
		echo json_encode($response);
		return;
	}
	
	$name = $_POST['perfType_name'];
	$conc = $_POST['perfType_conc'];
	$desc = $_POST['perfType_desc'];

	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM perfumeTypes WHERE name = '$name'"))){
		$response["error"] = $name.' already exists!';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn, "INSERT INTO perfumeTypes (name,concentration,description) VALUES ('$name','$conc','$desc')")){
		$response["success"] = $name.' created!';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//UPDATE ACCESSORY PIC
if($_GET['update_accessory_pic']){
	$allowed_ext = "png, jpg, jpeg, gif, bmp";

	$filename = $_FILES["accessory_pic"]["tmp_name"];  
    $file_ext = strtolower(end(explode('.',$_FILES['accessory_pic']['name'])));
	$file_tmp = $_FILES['accessory_pic']['tmp_name'];
    $ext = explode(', ',strtolower($allowed_ext));

	if(!$filename){
		return;
	}	
	
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0740, true);
	}
		
	if(in_array($file_ext,$ext)===false){
		$response["error"] = 'Extension not allowed, please choose a '.$allowed_ext.' file';
		echo json_encode($response);
		return;
	}
	$accessory = $_GET['accessory_id'];
	if($_FILES["accessory_pic"]["size"] > 0){
		move_uploaded_file($file_tmp,$tmp_path.base64_encode($filename));
		$pic = base64_encode($filename);		
		create_thumb($tmp_path.$pic,250,250); 
		$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents($tmp_path.$pic));
		
		mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '".$accessory."' AND type = '5'");
		if(mysqli_query($conn, "INSERT INTO documents (ownerID,name,type,notes,docData) VALUES ('".$accessory."','-','5','-','$docData')")){	
			unlink($tmp_path.$pic);
			$response["success"] = array( "msg" => "Photo updated", "accessory_pic" => $docData);
			echo json_encode($response);
			return;
		}
		$response["error"] = mysqli_error($conn);
		echo json_encode($response);
		return;
	}

	return;
}

//UPDATE BOTTLE PIC
if($_GET['update_bottle_pic']){
	$allowed_ext = "png, jpg, jpeg, gif, bmp";

	$filename = $_FILES["bottle_pic"]["tmp_name"];  
    $file_ext = strtolower(end(explode('.',$_FILES['bottle_pic']['name'])));
	$file_tmp = $_FILES['bottle_pic']['tmp_name'];
    $ext = explode(', ',strtolower($allowed_ext));

	
	if(!$filename){
		//$response["error"] = 'Please choose a file to upload';
		//echo json_encode($response);
		return;
	}	
	
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0740, true);
	}
		
	if(in_array($file_ext,$ext)===false){
		$response["error"] = 'Extension not allowed, please choose a '.$allowed_ext.' file';
		echo json_encode($response);
		return;
	}
	$bottle = $_GET['bottle_id'];
	if($_FILES["bottle_pic"]["size"] > 0){
		move_uploaded_file($file_tmp,$tmp_path.base64_encode($filename));
		$bottle_pic = base64_encode($filename);		
		create_thumb($tmp_path.$bottle_pic,250,250); 
		$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents($tmp_path.$bottle_pic));
		
		mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '".$bottle."' AND type = '4'");
		if(mysqli_query($conn, "INSERT INTO documents (ownerID,name,type,notes,docData) VALUES ('".$bottle."','-','4','-','$docData')")){	
			unlink($tmp_path.$bottle_pic);
			$response["success"] = array( "msg" => "File uploaded", "file" => $docData);
			echo json_encode($response);
			return;
		}
		$response["error"] = mysqli_error($conn);
		echo json_encode($response);
		return;
	}

	return;
}

//UPDATE BOTTLE DATA
if($_POST['update_bottle_data']){
	
	if(!$_POST['name']){
		$response["error"] = "Name is required";
		echo json_encode($response);
		return;
	}
	
	if(!is_numeric($_POST['size']) || $_POST['size'] <= 0) {
		$response["error"] = "Size is invalid";
		echo json_encode($response);
		return;
	}
	
	if (!is_numeric($_POST['price']) || $_POST['price'] <= 0) {
		$response["error"] = "Price is invalid";
		echo json_encode($response);
		return;
	}
		
	$id = $_POST['bottle_id'];
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ml = $_POST['size'];
	$price = $_POST['price'];
	$height = $_POST['height'];
	$width = $_POST['width'];
	$diameter = $_POST['diameter'];
	$supplier = $_POST['supplier'];
	$supplier_link = $_POST['supplier_link'];
	$notes = $_POST['notes'];
	$pieces = $_POST['pieces']?:0;
	$weight = $_POST['weight']?:0;

	$q = mysqli_query($conn,"UPDATE bottles SET name= '$name', ml = '$ml', price = '$price', height = '$height', width = '$width', diameter = '$diameter', supplier = '$supplier', supplier_link = '$supplier_link', notes = '$notes', pieces = '$pieces', weight = '$weight' WHERE id = '$id'");
	

	if($q){
		$response['success'] = "Bottle updated";
	}else{
		$response['error'] = "Error updating bottle data ".mysqli_error($conn);
	}
	
	echo json_encode($response);
	
	

	return;
}

//UPDATE ACCESSORY DATA
if($_POST['update_accessory_data']){
	
	if(!$_POST['name']){
		$response["error"] = "Name is required";
		echo json_encode($response);
		return;
	}
	$id = $_POST['accessory_id'];
	$name = $_POST['name'];
	$accessory = $_POST['accessory'];
	$supplier = $_POST['supplier'];
	$supplier_link = $_POST['supplier_link'];
	$pieces = $_POST['pieces']?:0;
	
	if (!is_numeric($_POST['price']) || $_POST['price'] <= 0 ) {
    	$response["error"] = 'Price cannot be empty or 0';
    	echo json_encode($response);
    	return;
	}
	
	$price = $_POST['price'];
	
	$q = mysqli_query($conn,"UPDATE inventory_accessories SET name = '$name', accessory = '$accessory', price = '$price', supplier = '$supplier', supplier_link = '$supplier_link', pieces = '$pieces' WHERE id = '$id'");
	

	if($q){
		$response['success'] = "Accessory updated";
	}else{
		$response['error'] = "Error updating accessory data ".mysqli_error($conn);
	}
	
	echo json_encode($response);
	
	

	return;
}
//DELETE BOTTLE
if($_POST['action'] == 'delete' && $_POST['btlId'] && $_POST['type'] == 'bottle'){
	$id = mysqli_real_escape_string($conn, $_POST['btlId']);
	
	if(mysqli_query($conn, "DELETE FROM bottles WHERE id = '$id'")){
		mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '$id' AND type = '4'");
		$response["success"] = 'Bottle deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//DELETE ACCESSORY
if($_POST['action'] == 'delete' && $_POST['accessoryId'] && $_POST['type'] == 'accessory'){
	$id = mysqli_real_escape_string($conn, $_POST['accessoryId']);
	
	if(mysqli_query($conn, "DELETE FROM inventory_accessories WHERE id = '$id'")){
		mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '$id' AND type = '5'");
		$response["success"] = 'Accessory deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//IMPORT IMAGES FROM PUBCHEM
if(isset($_GET['IFRA_PB']) && $_GET['IFRA_PB'] === 'import'){
    require_once(__ROOT__.'/func/pvFileGet.php');
    
    $i = 0;
    $response = [];

    $qCas = mysqli_query($conn, "SELECT cas FROM IFRALibrary WHERE image IS NULL OR image = '' OR image = '-'");

    if(!$qCas || mysqli_num_rows($qCas) == 0){
        $response["error"] = 'IFRA Database is currently empty or there was a problem with the query.';
        echo json_encode($response);
        return;
    }

    $view = $settings['pubchem_view'];

    while($cas = mysqli_fetch_assoc($qCas)){
        $casNumber = trim($cas['cas']);
        $imageUrl = $pubChemApi.'/pug/compound/name/'.$casNumber.'/PNG?record_type='.$view.'&image_size=small';
        $imageContent = pv_file_get_contents($imageUrl);
        
        if($imageContent !== false){
            $image = base64_encode($imageContent);
            $stmt = $conn->prepare("UPDATE IFRALibrary SET image = ? WHERE cas = ?");
            $stmt->bind_param('ss', $image, $casNumber);

            if($stmt->execute()){
                $i++;
            } else {
                $response["error"] = 'Error updating image for CAS: '.$casNumber.' - '.mysqli_error($conn);
                echo json_encode($response);
                return;
            }

            $stmt->close();
        }

        usleep(100000); // 0.1 seconds delay
    }

    $response["success"] = $i.' images updated!';
    echo json_encode($response);
    return;
}


//Update data FROM PubChem
if($_POST['pubChemData'] == 'update' && $_POST['cas']){
	$cas = trim($_POST['cas']);
	$molecularWeight = $_POST['molecularWeight'];
	$logP = trim($_POST['logP']);
	$molecularFormula = $_POST['molecularFormula'];
	$InChI = $_POST['InChI'];
	$CanonicalSMILES = $_POST['CanonicalSMILES'];
	$ExactMass = trim($_POST['ExactMass']);
	
	if($molecularWeight){
		$q = mysqli_query($conn, "UPDATE ingredients SET molecularWeight = '$molecularWeight' WHERE cas='$cas'");
	}
	if($logP){
		$q.= mysqli_query($conn, "UPDATE ingredients SET logp = '$logP' WHERE cas='$cas'");
	}
	if($molecularFormula){
		$q.= mysqli_query($conn, "UPDATE ingredients SET formula = '$molecularFormula' WHERE cas='$cas'");
	}
	if($InChI){
		$q.= mysqli_query($conn, "UPDATE ingredients SET INCI = '$InChI' WHERE cas='$cas'");
	}
	if($q){
		$response["success"] = 'Local data updated';
	}else{
		$response["error"] = 'Unable to update data';
	}
	echo json_encode($response);
	return;
}

//IMPORT SYNONYMS FROM PubChem
if($_POST['synonym'] == 'import' && $_POST['method'] == 'pubchem'){
	$ing = base64_decode($_POST['ing']);
	$cas = trim($_POST['cas']);

	$u = $pubChemApi.'/pug/compound/name/'.$cas.'/synonyms/JSON';
	$json = file_get_contents($u);
	$json = json_decode($json);
	$data = $json->InformationList->Information[0]->Synonym;
	$cid = $json->InformationList->Information[0]->CID;
	$source = 'PubChem';
	if(empty($data)){
		$response["error"] = 'No data found';
		echo json_encode($response);
		return;
	}
	$i=0;
	foreach($data as $d){
		$einecs = explode('EINECS ',$d);
		if($einecs['1']){
			mysqli_query($conn, "UPDATE ingredients SET einecs = '".$einecs['1']."' WHERE cas = '$cas'");
		}
		$fema = explode('FEMA ',$d);
		if($fema['1']){
			mysqli_query($conn, "UPDATE ingredients SET FEMA = '".preg_replace("/[^0-9]/", "", $fema['1'])."' WHERE cas = '$cas'");
		}
		$sql = mysqli_query($conn, "SELECT synonym FROM synonyms WHERE synonym = '$d' AND ing = '$ing'");
		if(!$sql || !mysqli_num_rows($sql)){
			$r = mysqli_query($conn, "INSERT INTO synonyms (ing,cid,synonym,source) VALUES ('$ing','$cid','$d','$source')");		
		 	$i++;
		}
	}
	if($r){
		$response["success"] = $i.' synonym(s) imported';
	}else{
		$response["error"] = 'Data already in sync';
	}
	echo json_encode($response);
	return;
}

//ADD SYNONYM
if($_POST['synonym'] == 'add'){
	$synonym = mysqli_real_escape_string($conn, $_POST['sName']);
	$source = mysqli_real_escape_string($conn, $_POST['source']);
	
	$ing = base64_decode($_POST['ing']);

	if(empty($synonym)){
		$response["error"] = 'Synonym name is required';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT synonym FROM synonyms WHERE synonym = '$synonym' AND ing = '$ing'"))){
		$response["error"] = $synonym.' already exists';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn, "INSERT INTO synonyms (synonym,source,ing) VALUES ('$synonym','$source','$ing')")){
		$response["success"] = $synonym.' added to the list';
		echo json_encode($response);
	}
	
	return;
}


//UPDATE SYNONYM
if($_GET['synonym'] == 'update'){
	$value = trim(mysqli_real_escape_string($conn, $_POST['value']));
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ing = base64_decode($_GET['ing']);

	if(mysqli_query($conn, "UPDATE synonyms SET $name = '$value' WHERE id = '$id' AND ing='$ing'")){
		$response["success"] = 'Synonym updated';
	} else {
		$response["error"] = mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//DELETE ING SYNONYM	
if($_GET['synonym'] == 'delete'){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	if(mysqli_query($conn, "DELETE FROM synonyms WHERE id = '$id'")){
		$response["success"] = 'Synonym deleted';	
	} else {
		$response["error"] = mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//ADD REPLACEMENT
if($_POST['replacement'] == 'add'){
	
	$ing_name = base64_decode($_POST['ing_name']);
	$ing_cas = trim($_POST['ing_cas']);

	if(empty($_POST['rName'])){
		$response["error"] = 'Name is required';
		echo json_encode($response);
		return;
	}
	
	if(empty($_POST['rCAS'])){
		$response["error"] = 'CAS is required';
		echo json_encode($response);
		return;
	}

	if(mysqli_num_rows(mysqli_query($conn, "SELECT ing_rep_name FROM ingReplacements WHERE ing_name = '$ing_name' AND ing_rep_name = '".$_POST['rName']."'"))){
		$response["error"] = $_POST['rName'].' already exists!';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn, "INSERT INTO ingReplacements (ing_id,ing_name,ing_cas,ing_rep_id,ing_rep_name,ing_rep_cas,notes) VALUES ('".$_POST['ing_id']."','$ing_name','$ing_cas','".$_POST['rIngId']."','".$_POST['rName']."','".$_POST['rCAS']."','".$_POST['rNotes']."')")){
		$response["success"] = $_POST['rName'].' added to the list!';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//UPDATE ING REPLACEMENT
if($_GET['replacement'] == 'update'){
	$value = trim(mysqli_real_escape_string($conn, $_POST['value']));
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ing = base64_decode($_GET['ing']);

	if(mysqli_query($conn, "UPDATE ingReplacements SET $name = '$value' WHERE id = '$id' AND ing_name='$ing'")){
		$response["success"] = $ing.' replacement updated';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}


//DELETE ING REPLACEMENT	
if($_POST['replacement'] == 'delete'){
	$id = mysqli_real_escape_string($conn, $_POST['id']);
	if(mysqli_query($conn, "DELETE FROM ingReplacements WHERE id = '$id'")){
		$response["success"] = $_POST['name'].' replacement removed';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//UPDATE ING DOCUMENT
if($_GET['action'] == 'updateDocument'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ownerID = mysqli_real_escape_string($conn, $_GET['ingID']);

	if(mysqli_query($conn, "UPDATE documents SET $name = '$value' WHERE ownerID = '$ownerID' AND id='$id'")){
		$response["success"] = 'Document updated';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//DELETE DOCUMENT	
if($_GET['action'] == 'deleteDocument'){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	$ownerID = mysqli_real_escape_string($conn, $_GET['ownerID']);
							
	if(mysqli_query($conn, "DELETE FROM documents WHERE id = '$id' AND ownerID='$ownerID'")){
		$response["success"] = 'Document deletetd';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//GET SUPPLIER PRICE
if($_POST['ingSupplier'] == 'getPrice'){
	$ingID = mysqli_real_escape_string($conn, $_POST['ingID']);
	$ingSupplierID = mysqli_real_escape_string($conn, $_POST['ingSupplierID']);
	$size = mysqli_real_escape_string($conn, $_POST['size']);
	$supplier_link = urldecode($_POST['sLink']);
	
	$supp_data = mysqli_fetch_array(mysqli_query($conn, "SELECT price_tag_start,price_tag_end,add_costs,price_per_size FROM ingSuppliers WHERE id = '$ingSupplierID'"));
	
	if($newPrice = priceScrape($supplier_link,$size,$supp_data['price_tag_start'],$supp_data['price_tag_end'],$supp_data['add_costs'],$supp_data['price_per_size'])){
		if(mysqli_query($conn, "UPDATE suppliers SET price = '$newPrice' WHERE ingSupplierID = '$ingSupplierID' AND ingID='$ingID'")){
			$response["success"] = '<strong>Price updated</strong>';
			echo json_encode($response);
		}
	}else{
	 	$response["error"] = '<strong>Error getting the price from the supplier. Previous value has been retained.</strong>';
		echo json_encode($response);
	}
	return;
}

//ADD ING SUPPLIER
if($_POST['ingSupplier'] == 'add'){
	if(empty($_POST['supplier_id']) || empty($_POST['supplier_link']) || empty($_POST['supplier_size']) || empty($_POST['supplier_price'])){
		$response["error"] = 'Error: Missing fields!';
		echo json_encode($response);
		return;
	}
	
	if(!is_numeric($_POST['supplier_size']) || !is_numeric($_POST['stock']) || !is_numeric($_POST['supplier_price'])){
		$response["error"] = 'Error: Only numeric values allowed in size, stock and price fields!';
		echo json_encode($response);
		return;
	}
	
	$ingID = mysqli_real_escape_string($conn, $_POST['ingID']);
	$supplier_id = mysqli_real_escape_string($conn, $_POST['supplier_id']);
	$supplier_link = mysqli_real_escape_string($conn, $_POST['supplier_link']);	
	$supplier_size = mysqli_real_escape_string($conn, $_POST['supplier_size']);
	$supplier_price = mysqli_real_escape_string($conn, $_POST['supplier_price']);
	$supplier_manufacturer = mysqli_real_escape_string($conn, $_POST['supplier_manufacturer']);
	$supplier_name = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '$supplier_id'"));
	$supplier_batch = mysqli_real_escape_string($conn, $_POST['supplier_batch']);
	$purchased = mysqli_real_escape_string($conn, $_POST['purchased'] ?: date('Y-m-d'));
	$stock = mysqli_real_escape_string($conn, $_POST['stock'] ?: 0);
	$mUnit = $_POST['mUnit'];
	$status = $_POST['status'];
	$supplier_sku = mysqli_real_escape_string($conn, $_POST['supplier_sku']);
	$internal_sku = mysqli_real_escape_string($conn, $_POST['internal_sku']);
	$storage_location = mysqli_real_escape_string($conn, $_POST['storage_location']);


	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingSupplierID FROM suppliers WHERE ingSupplierID = '$supplier_id' AND ingID = '$ingID'"))){
		$response["error"] = $supplier_name['name'].' already exists';
		echo json_encode($response);
		return;
	}
		
	if(!mysqli_num_rows(mysqli_query($conn, "SELECT ingSupplierID FROM suppliers WHERE ingID = '$ingID'"))){
	   $preferred = '1';
	}else{
		$preferred = '0';
	}
		
	if(mysqli_query($conn, "INSERT INTO suppliers (ingSupplierID,ingID,supplierLink,price,size,manufacturer,preferred,batch,purchased,stock,mUnit,status,supplier_sku,internal_sku,storage_location) VALUES ('$supplier_id','$ingID','$supplier_link','$supplier_price','$supplier_size','$supplier_manufacturer','$preferred','$supplier_batch','$purchased','$stock','$mUnit','$status','$supplier_sku','$internal_sku','$storage_location')")){
		$response["success"] = $supplier_name['name'].' added';
	}else{
		$response["error"] = mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

//UPDATE ING SUPPLIER
if($_GET['ingSupplier'] == 'update'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ingID = mysqli_real_escape_string($conn, $_GET['ingID']);

	if(mysqli_query($conn, "UPDATE suppliers SET $name = '$value' WHERE id = '$id' AND ingID='$ingID'")){
		$response["success"] = 'Supplier updated';
	} else {
		$response["error"] = mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

//UPDATE PREFERRED SUPPLIER
if($_GET['ingSupplier'] == 'preferred'){
	$sID = mysqli_real_escape_string($conn, $_GET['sID']);
	$ingID = mysqli_real_escape_string($conn, $_GET['ingID']);
	$status = mysqli_real_escape_string($conn, $_GET['status']);
	
	mysqli_query($conn, "UPDATE suppliers SET preferred = '0' WHERE ingID='$ingID'");
	if(mysqli_query($conn, "UPDATE suppliers SET preferred = '$status' WHERE ingSupplierID = '$sID' AND ingID='$ingID'")){
		$response["success"] = 'Supplier set to prefered';
	} else {
		$response["error"] = mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//DELETE ING SUPPLIER	
if($_GET['ingSupplier'] == 'delete'){

	$sID = mysqli_real_escape_string($conn, $_GET['sID']);
	$ingID = mysqli_real_escape_string($conn, $_GET['ingID']);
	
	$supplierCount = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM suppliers WHERE ingID = '$ingID'"));
    if ($supplierCount <= 1) {
        $response["error"] = 'Cannot delete the last supplier for this ingredient';
		echo json_encode($response);
		return;
    }
								
	if(mysqli_query($conn, "DELETE FROM suppliers WHERE id = '$sID' AND ingID='$ingID'")){
		$response["success"] = 'Supplier deleted';
	} else {
		$response["error"] = mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//FORMULA QUANTITY MANAGEMENT
if($_POST['updateQuantity'] && $_POST['ingQuantityID'] &&  $_POST['ingQuantityName']  && $_POST['fid']){
	$fid = $_POST['fid'];
	$value = $_POST['ingQuantity'];
	$ingredient = $_POST['ingQuantityID'];
	$ing_name = $_POST['ingQuantityName'];
	
	if(empty($_POST['ingQuantity'])){
		$response["error"] = 'Quantity cannot be empty';
		echo json_encode($response);
		return;
	}
	if(!is_numeric($_POST['ingQuantity'])){
		$response["error"] = 'Quantity must be numeric only';
		echo json_encode($response);
		return;
	}
	
	if($_POST['curQuantity'] == $_POST['ingQuantity']){
		$response["error"] = 'Quantity is already the same';
		echo json_encode($response);
		return;
	}
	
	
	if($_POST['ingReCalc'] == 'true'){
		$ingID = $_POST['ingID'];
		if(!$_POST['formulaSolventID']){
			$response["error"] = 'Please select a solvent';
			echo json_encode($response);
			return;
		}
		$formulaSolventID = $_POST['formulaSolventID'];
		
		if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM ingredients WHERE id = '".$ingID."' AND profile='solvent'"))){
			$response["error"] = 'You cannot exchange a solvent with a solvent';
			echo json_encode($response);
			return;
		}
		
		$slv = mysqli_fetch_array(mysqli_query($conn,"SELECT quantity FROM formulas WHERE ingredient_id = '".$formulaSolventID."' AND fid = '".$fid."'"));
		
		$fq = $_POST['ingQuantity'] - $_POST['curQuantity'];
		
		if($slv['quantity'] < $fq){
			$response["error"] = 'Not enough solvent, available: '.number_format($slv['quantity'],$settings['qStep']).$settings['mUnit'].', Requested: '.number_format($fq,$settings['qStep']).$settings['mUnit'];
			echo json_encode($response);
			return;
		}
		
		//UPDATE SOLVENT
		function formatVal($num){
    		return sprintf("%+.4f",$num);
		}
		
		$curV = mysqli_fetch_array(mysqli_query($conn, "SELECT quantity FROM formulas WHERE fid = '$fid' AND id = '".$ingredient."'"));
		$diff = number_format($curV['quantity'] -  $value  , 4);
		$v = formatVal($diff);
		
		$qs ="UPDATE formulas SET quantity = quantity $v WHERE fid = '$fid' AND ingredient_id = '".$formulaSolventID."'";
		if(!mysqli_query($conn, $qs)){
			$response["error"] = 'Error updating solvent: '.mysqli_error($conn);
			$response["query"] = $qs;
			echo json_encode($response);
			return;
		}
	}
		
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '".$_POST['fid']."'"));
	
	if($meta['isProtected'] == FALSE){
		
		if(mysqli_query($conn, "UPDATE formulas SET quantity = '$value' WHERE fid = '$fid' AND id = '$ingredient'")){
			
			$lg = "CHANGED: ".$ing_name." Set $name to $value";
			mysqli_query($conn, "INSERT INTO formula_history (fid,ing_id,change_made,user) VALUES ('".$meta['id']."','$ingredient','$lg','".$user['fullName']."')");
			
			$response["success"] = 'Quantity updated';
			echo json_encode($response);
		
		}else{
			$response["error"] = mysqli_error($conn);
			echo json_encode($response);
		}
		
	}
	return;
}

if($_POST['value'] && $_GET['formula'] && $_POST['pk']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, $_GET['formula']);
	$ingredient = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	$ing_name =  mysqli_fetch_array(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE id = '$ingredient' AND fid = '".$_GET['formula']."'"));
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '".$_GET['formula']."'"));
	if($meta['isProtected'] == FALSE){
					
		if(mysqli_query($conn, "UPDATE formulas SET $name = '$value' WHERE fid = '$formula' AND id = '$ingredient'")){
			
			$lg = "CHANGED: ".$ing_name['ingredient']." Set $name to $value";
			mysqli_query($conn, "INSERT INTO formula_history (fid,ing_id,change_made,user) VALUES ('".$meta['id']."','$ingredient','$lg','".$user['fullName']."')");
			$response["success"] = 'Formula updated';
		} else {
			$response["error"] = mysqli_error($conn);
		}
	}
	echo json_encode($response);
	return;
}

if($_GET['formulaMeta']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, base64_decode($_GET['formulaMeta']));
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE formulasMetaData SET $name = '$value' WHERE id = '$id'")){
		$response["success"] = 'Formula meta updated';
	} else {
		$response["error"] = mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

if($_GET['createRev'] == 'man'){
	require_once(__ROOT__.'/func/createFormulaRevision.php');
	$fid = $_GET['fid'];
	
	if($l = createFormulaRevision($fid,'Manually',$conn)){
		$response["success"] = 'Revision created (If changes detected)';
		echo json_encode($response);
	}else{
		$response["error"] = 'Unable to create revision, please make sure formula exists and contains at least one ingredient.';
		echo json_encode($response);
	}
	return;
}

if($_GET['protect']){
	require_once(__ROOT__.'/func/createFormulaRevision.php');
	$fid = mysqli_real_escape_string($conn, $_GET['protect']);
	
	if($_GET['isProtected'] == 'true'){
		$isProtected = '1';
		$l = 'locked';
		createFormulaRevision($fid,'Automatic',$conn);
	}else{
		$isProtected = '0';
		$l = 'unlocked';
	}
	if(mysqli_query($conn, "UPDATE formulasMetaData SET isProtected = '$isProtected' WHERE fid = '$fid'")){
		$response["success"] = 'Formula '.$l;
		echo json_encode($response);
	}else{
		$response["error"] = 'Something went wrong';
		echo json_encode($response);
	}
	return;
}

if($_POST['formulaSettings'] &&  $_POST['set']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$set = mysqli_real_escape_string($conn, $_POST['set']);
	$val = mysqli_real_escape_string($conn, $_POST['val']);

	if(mysqli_query($conn, "UPDATE formulasMetaData SET $set = '$val' WHERE fid = '$fid'")){
		$response["success"] = "Formula settings updated";
		echo json_encode($response);
	}else{
		$response["error"] = 'Something went wrong';
		echo json_encode($response);
	}
	return;
}



if($_GET['action'] == 'rename' && $_GET['fid']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$fid = mysqli_real_escape_string($conn, $_GET['fid']);
	$id = $_POST['pk'];
	if(!$value){
		$response["error"] = 'Formula name cannot be empty';
		echo json_encode($response);
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE name = '$value'"))){
		$response["error"] = 'Name already exists';
		echo json_encode($response);
	
	}else{
		mysqli_query($conn, "UPDATE formulasMetaData SET name = '$value' WHERE id = '$id'");
		if(mysqli_query($conn, "UPDATE formulas SET name = '$value' WHERE fid = '$fid'")){
			$response["success"] = 'Formula renamed';
			$response["msg"] = $value;
			echo json_encode($response);
		}
	
	}
	
	return;	
}

if($_GET['action'] == 'ingredientCategories'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$cat_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE ingCategory SET $name = '$value' WHERE id = '$cat_id'")){
		$response["success"] = 'Ingredient category updated';
	}else{
		$response["error"] = mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

if($_GET['settings'] == 'prof'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	mysqli_query($conn, "UPDATE ingProfiles SET $name = '$value' WHERE id = '$id'");
	return;
}

if($_GET['settings'] == 'fcat' && $_GET['action'] == 'updateFormulaCategory' ){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$cat_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE formulaCategories SET $name = '$value' WHERE id = '$cat_id'")){
		$response["success"] = 'Formula actegory updated';
	} else {
		$response["error"] = mysqli_error($conn);
	}
	echo json_encode($response);
	return;	
}

if($_GET['settings'] == 'sup'){
	$value = htmlentities($_POST['value']);
	$sup_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	mysqli_query($conn, "UPDATE ingSuppliers SET $name = '$value' WHERE id = '$sup_id'");
	return;	
}

if($_POST['supp'] == 'edit'){
	$id = $_POST['id'];

	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$address = mysqli_real_escape_string($conn, $_POST['address']);
	$po = mysqli_real_escape_string($conn, $_POST['po']);
	$country = mysqli_real_escape_string($conn, $_POST['country']);
	$telephone = mysqli_real_escape_string($conn, $_POST['telephone']);
	$url = mysqli_real_escape_string($conn, $_POST['url']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	
	
	if(mysqli_query($conn, "UPDATE ingSuppliers SET address = '$address', po='$po', country='$country', telephone='$telephone', url='$url', email='$email' WHERE id = '$id'")){
		$response["success"] = 'Supplier '.$name.' updated!';
		echo json_encode($response);
	}else{
		$response["error"] = 'Something went wrong: '.mysqli_error($conn);
		echo json_encode($response);
	}
	return;
}


if($_POST['supp'] == 'add'){
	if(!is_numeric($_POST['min_ml']) || !is_numeric($_POST['min_gr'])){
		$response["error"] = 'Only numeric values allowed in ml and grams fields!';
		echo json_encode($response);
		return;
	}
	$description = mysqli_real_escape_string($conn, $_POST['description']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$address = mysqli_real_escape_string($conn, $_POST['address']);
	$po = mysqli_real_escape_string($conn, $_POST['po']);
	$country = mysqli_real_escape_string($conn, $_POST['country']);
	$telephone = mysqli_real_escape_string($conn, $_POST['telephone']);
	$url = mysqli_real_escape_string($conn, $_POST['url']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$platform = mysqli_real_escape_string($conn, $_POST['platform']);
	$price_tag_start = htmlentities($_POST['price_tag_start']);
	$price_tag_end = htmlentities($_POST['price_tag_end']);
	$add_costs = is_numeric($_POST['add_costs']);
	$min_ml = mysqli_real_escape_string($conn, $_POST['min_ml']);
	$min_gr = mysqli_real_escape_string($conn, $_POST['min_gr']);

	if(empty($min_ml)){
		$min_ml = 0;
	}
	if(empty($min_gr)){
		$min_gr = 0;
	}		 
	
	if(empty($name)){
		$response["error"] = '<strong>Error: </strong> Supplier name required';
		echo json_encode($response);
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE name = '$name'"))){
		$response["error"] = '<strong>'.$name.'</strong> Supplier already exists!';
		echo json_encode($response);
		return;
	}

	if(mysqli_query($conn, "INSERT INTO ingSuppliers (name,address,po,country,telephone,url,email,platform,price_tag_start,price_tag_end,add_costs,notes,min_ml,min_gr) VALUES ('$name','$address','$po','$country','$telephone','$url','$email','$platform','$price_tag_start','$price_tag_end','$add_costs','$description','$min_ml','$min_gr')")){
		$response["success"] = 'Supplier '.$name.' added!';
		echo json_encode($response);
	}else{
		$response["error"] = 'Something went wrong: '.mysqli_error($conn);
		echo json_encode($response);
	}
	return;
}

if($_GET['supp'] == 'delete' && $_GET['ID']){
	$ID = mysqli_real_escape_string($conn, $_GET['ID']);
	$supplier = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '$ID'"));

	if(mysqli_query($conn, "DELETE FROM ingSuppliers WHERE id = '$ID'")){
		$response["success"] = 'Supplier <strong>'.$supplier['name'].'</strong> deleted!';
	}else{
		$response["error"] = 'Something went wrong: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//ADD composition
if($_POST['composition'] == 'add'){
	$allgName = mysqli_real_escape_string($conn, $_POST['allgName']);
	$allgCAS = mysqli_real_escape_string($conn, $_POST['allgCAS']);
	$allgEC = mysqli_real_escape_string($conn, $_POST['allgEC']);	
	$minPerc = rtrim(mysqli_real_escape_string($conn, $_POST['minPerc']),'%');
	$maxPerc = rtrim(mysqli_real_escape_string($conn, $_POST['maxPerc']),'%');
	$GHS = rtrim(mysqli_real_escape_string($conn, $_POST['GHS']));

	$ing = base64_decode($_POST['ing']);
	
	if($_POST['addToDeclare'] == 'true'){
		$declare = '1';
	}else{
		$declare = '0';
	}
	
	if(empty($allgName)){
		$response["error"] = 'Name is required';
		echo json_encode($response);
		return;
	}
	
	if(empty($allgCAS)){
		$response["error"] = 'CAS number is required';
		echo json_encode($response);
		return;
	}
	
	if(empty($minPerc)){
		$response["error"] = 'Minimum percentage is required';
		echo json_encode($response);
		return;
	}
	
	if(empty($maxPerc)){
		$response["error"] = 'Max percentage is required';
		echo json_encode($response);
		return;
	}
	
	if(!is_numeric($minPerc)){
		$response["error"] = 'Minimum percentage value needs to be numeric';
		echo json_encode($response);
		return;
	}
	
	if(!is_numeric($maxPerc)){
		$response["error"] = 'Maximum percentage value needs to be numeric';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredient_compounds WHERE name = '$allgName' AND ing = '$ing'"))){
		$response["error"] = $allgName.' already exists';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn, "INSERT INTO ingredient_compounds (name, cas, ec, min_percentage, max_percentage, GHS, toDeclare, ing) VALUES ('$allgName','$allgCAS','$allgEC','$minPerc','$maxPerc','$GHS','$declare','$ing')")){
		$response["success"] = $allgName.' added to the composition';
	}else{
		$response["error"] = mysqli_error($conn);
	}
	
	if($_POST['addToIng'] == 'true'){
		if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$allgName'")))){
			mysqli_query($conn, "INSERT INTO ingredients (name,cas,einecs) VALUES ('$allgName','$allgCAS','$allgEC')");		
		}
	}
	
	echo json_encode($response);
	return;
}

//UPDATE composition
if($_GET['composition'] == 'update'){
	$value = rtrim(mysqli_real_escape_string($conn, $_POST['value']),'%');
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE ingredient_compounds SET $name = '$value' WHERE id = '$id'")){
		$response["success"] = 'Ingredient updated';
	} else {
		$response["error"] = mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

//DELETE composition	
if($_POST['composition'] == 'delete'){

	$id = mysqli_real_escape_string($conn, $_POST['allgID']);
	//$ing = base64_decode($_POST['ing']);

	$delQ = mysqli_query($conn, "DELETE FROM ingredient_compounds WHERE id = '$id'");	
	if($delQ){
		$response["success"] = $ing.' deleted';
	}else {
		$response["error"] = mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

//DELETE INGREDIENT	
if($_POST['ingredient'] == 'delete' && $_POST['ing_id']){

	$id = mysqli_real_escape_string($conn, $_POST['ing_id']);
	$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingredients WHERE id = '$id'"));
	
	if($_POST['forceDelIng'] == "false"){

			if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '".$ing['name']."'"))){
			$response["error"] = '<strong>'.$ing['name'].'</strong> is in use by at least one formula and cannot be removed!</div>';
			echo json_encode($response);
			return;
		}
	}
	if(mysqli_query($conn, "DELETE FROM ingredients WHERE id = '$id'")){
		mysqli_query($conn,"DELETE FROM ingredient_compounds WHERE ing = '".$ing['name']."'");
		$response["success"] = 'Ingredient <strong>'.$ing['name'].'</strong> removed from the database!';
	}
	
	echo json_encode($response);
	return;

}

//CUSTOMERS - ADD
if($_POST['customer'] == 'add'){
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	if(empty($name)){
		$response["error"] = 'Customer name is required.';
		echo json_encode($response);
		return;
	}
	$address = mysqli_real_escape_string($conn, $_POST['address']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$web = mysqli_real_escape_string($conn, $_POST['web']);
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM customers WHERE name = '$name'"))){
		$response["error"] = 'Error: '.$name.' already exists!';
	}elseif(mysqli_query($conn, "INSERT INTO customers (name,address,email,web,owner_id) VALUES ('$name', '$address', '$email', '$web', '".$user['id']."')")){
		$response["success"] = 'Customer '.$name.' added!';
	}else{
		$response["error"] = 'Error adding customer.';
	}
	echo json_encode($response);
	return;
}

//CUSTOMERS - DELETE
if($_POST['action'] == 'delete' && $_POST['type'] == 'customer' && $_POST['customer_id']){
	$customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
	if(mysqli_query($conn, "DELETE FROM customers WHERE id = '$customer_id'")){
		$response["success"] = 'Customer deleted!';
	}else{
		$response["error"] = 'Error deleting customer '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}
	
//CUSTOMERS - UPDATE
if($_POST['update_customer_data'] && $_POST['customer_id']){
	$id = $_POST['customer_id'];
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	if(empty($name)){
		$response["error"] = 'Name cannot be empty ';
		echo json_encode($response);
		return;
	}
	$address = mysqli_real_escape_string($conn, $_POST['address'])?:'N/A';
	$email = mysqli_real_escape_string($conn, $_POST['email'])?:'N/A';
	$web = mysqli_real_escape_string($conn, $_POST['web'])?:'N/A';
	$phone = mysqli_real_escape_string($conn, $_POST['phone'])?:'N/A';

	if(mysqli_query($conn, "UPDATE customers SET name = '$name', address = '$address', email = '$email', web = '$web', phone = '$phone' WHERE id = '$id'")){
		$response["success"] = 'Customer details updated!';
	}else{
		$response["error"] = 'Error updating customer '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;	
}

//MGM INGREDIENT
if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'general'){
	$ing = mysqli_real_escape_string($conn, $_POST['ing']);

	$INCI = trim(mysqli_real_escape_string($conn, $_POST["INCI"]));
	$cas = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["cas"])));
	$einecs = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["einecs"])));
	$reach = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["reach"])));
	$fema = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["fema"])));
	$purity = validateInput($_POST["purity"]);
	$profile = mysqli_real_escape_string($conn, $_POST["profile"]);
	$type = mysqli_real_escape_string($conn, $_POST["type"]);	
	$strength = mysqli_real_escape_string($conn, $_POST["strength"]);
	$category = mysqli_real_escape_string($conn, $_POST["category"] ? $_POST['category']: '1');
	$physical_state = mysqli_real_escape_string($conn, $_POST["physical_state"]);
	$odor = ucfirst(trim(mysqli_real_escape_string($conn, $_POST["odor"])));
	$notes = ucfirst(trim(mysqli_real_escape_string($conn, $_POST["notes"])));
	
//	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '".$_POST['name']."'"))){
	if(empty($_POST['name'])){
	
		$query = "UPDATE ingredients SET INCI = '$INCI',cas = '$cas',solvent='".$_POST["solvent"]."', einecs = '$einecs', reach = '$reach',FEMA = '$fema',purity='$purity',profile='$profile',type = '$type',strength = '$strength', category='$category',physical_state = '$physical_state',odor = '$odor',notes = '$notes' WHERE name='$ing'";
		
		if(mysqli_query($conn, $query)){
			$response["success"] = 'General details have been updated!';
		}else{
			$response["error"] = 'Unable to update database: '.mysqli_error($conn);
		}
	}else{
		$name = sanChar(mysqli_real_escape_string($conn, $_POST["name"]));

		$query = "INSERT INTO ingredients (name, INCI, cas, einecs, reach, FEMA, type, strength, category, profile, notes, odor, purity, solvent, physical_state) VALUES ('$name', '$INCI', '$cas', '$einecs', '$reach', '$fema', '$type', '$strength', '$category', '$profile',  '$notes', '$odor', '$purity', '$solvent', '1')";
		
		if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$name'"))){
			$response["error"] = $name.' already exists!';
		}else{
			if(mysqli_query($conn, $query)){
				$response["success"] = 'Ingredient '.$name.' created';
				$response["ingid"] = mysqli_insert_id($conn);
			}else{
				$response["error"] = 'Failed to create ingredient';
			}
		}
	}
	echo json_encode($response);
	return;	
}


if ($_POST['manage'] === 'ingredient' && $_POST['tab'] === 'usage_limits') {
    $ingID = (int) $_POST['ingID'];

    $flavor_use = ($_POST['flavor_use'] === 'true') ? 1 : 0;
    $noUsageLimit = ($_POST['noUsageLimit'] === 'true') ? 1 : 0;
    $byPassIFRA = ($_POST['byPassIFRA'] === 'true') ? 1 : 0;
    $allergen = ($_POST['isAllergen'] === 'true') ? 1 : 0;

    $usage_type = mysqli_real_escape_string($conn, trim($_POST['usage_type']));

    $categories = [
        'cat1' => (float) $_POST['cat1'],
        'cat2' => (float) $_POST['cat2'],
        'cat3' => (float) $_POST['cat3'],
        'cat4' => (float) $_POST['cat4'],
        'cat5A' => (float) $_POST['cat5A'],
        'cat5B' => (float) $_POST['cat5B'],
        'cat5C' => (float) $_POST['cat5C'],
        'cat5D' => (float) $_POST['cat5D'],
        'cat6' => (float) $_POST['cat6'],
        'cat7A' => (float) $_POST['cat7A'],
        'cat7B' => (float) $_POST['cat7B'],
        'cat8' => (float) $_POST['cat8'],
        'cat9' => (float) $_POST['cat9'],
        'cat10A' => (float) $_POST['cat10A'],
        'cat10B' => (float) $_POST['cat10B'],
        'cat11A' => (float) $_POST['cat11A'],
        'cat11B' => (float) $_POST['cat11B'],
        'cat12' => (float) $_POST['cat12'],
    ];

    $stmt = $conn->prepare(
        "UPDATE ingredients SET byPassIFRA = ?, noUsageLimit = ?, flavor_use = ?, 
        usage_type = ?, allergen = ?, cat1 = ?, cat2 = ?, cat3 = ?, cat4 = ?, 
        cat5A = ?, cat5B = ?, cat5C = ?, cat5D = ?, cat6 = ?, cat7A = ?, cat7B = ?, 
        cat8 = ?, cat9 = ?, cat10A = ?, cat10B = ?, cat11A = ?, cat11B = ?, 
        cat12 = ? WHERE id = ?"
    );

    $stmt->bind_param(
        'iiisiddddddddddddddddddi',
        $byPassIFRA, $noUsageLimit, $flavor_use, $usage_type, $allergen, 
        $categories['cat1'], $categories['cat2'], $categories['cat3'], 
        $categories['cat4'], $categories['cat5A'], $categories['cat5B'], 
        $categories['cat5C'], $categories['cat5D'], $categories['cat6'], 
        $categories['cat7A'], $categories['cat7B'], $categories['cat8'], 
        $categories['cat9'], $categories['cat10A'], $categories['cat10B'], 
        $categories['cat11A'], $categories['cat11B'], $categories['cat12'], $ingID
    );

    if ($stmt->execute()) {
        $response["success"] = 'Usage limits have been updated!';
    } else {
        $response["error"] = 'Something went wrong: ' . $stmt->error;
    }

    $stmt->close();

    echo json_encode($response);
    return;
}


if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'tech_data'){
	$ingID = (int)$_POST['ingID'];
	$tenacity = mysqli_real_escape_string($conn, $_POST["tenacity"]);
	$flash_point = mysqli_real_escape_string($conn, $_POST["flash_point"]);
	$chemical_name = mysqli_real_escape_string($conn, $_POST["chemical_name"]);
	$formula = mysqli_real_escape_string($conn, $_POST["formula"]);
	$logp = mysqli_real_escape_string($conn, $_POST["logp"]);
	$soluble = mysqli_real_escape_string($conn, $_POST["soluble"]);
	$molecularWeight = mysqli_real_escape_string($conn, $_POST["molecularWeight"]);
	$appearance = mysqli_real_escape_string($conn, $_POST["appearance"]);
	$rdi = (int)$_POST["rdi"]?:0;
	$shelf_life = mysqli_real_escape_string($conn, $_POST["shelf_life"]) ?: 0;

	
	$query = "UPDATE ingredients SET tenacity='$tenacity',flash_point='$flash_point',chemical_name='$chemical_name',formula='$formula',logp = '$logp',soluble = '$soluble',molecularWeight = '$molecularWeight',appearance='$appearance',rdi='$rdi', shelf_life = '$shelf_life' WHERE id='$ingID'";
	if(mysqli_query($conn, $query)){
		$response["success"] = 'Technical data has been updated';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}	
	echo json_encode($response);
	return;
}

if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'note_impact'){
	$ingID = (int)$_POST['ingID'];
	$impact_top = mysqli_real_escape_string($conn, $_POST["impact_top"]);
	$impact_base = mysqli_real_escape_string($conn, $_POST["impact_base"]);
	$impact_heart = mysqli_real_escape_string($conn, $_POST["impact_heart"]);

	$query = "UPDATE ingredients SET impact_top = '$impact_top',impact_heart = '$impact_heart',impact_base = '$impact_base' WHERE id='$ingID'";
	if(mysqli_query($conn, $query)){
		$response["success"] = 'Note impact has been updated';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'privacy'){
	$ingID = (int)$_POST['ingID'];
	if($_POST['isPrivate'] == 'true'){ $isPrivate = '1'; }else{ $isPrivate = '0'; }
	
	$query = "UPDATE ingredients SET isPrivate = '$isPrivate' WHERE id='$ingID'";
	if(mysqli_query($conn, $query)){
		$response["success"] = 'Privacy settings has been updated!';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//ADD PICTOGRAM
if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'safety_info' && $_POST['action'] == 'add'){
	$ingID = (int)$_POST['ingID'];
	$GHS = (int)$_POST['pictogram'];

	if(mysqli_query($conn, "INSERT INTO ingSafetyInfo (GHS, ingID) VALUES ('$GHS','$ingID') ON DUPLICATE KEY UPDATE GHS = VALUES(GHS), ingID = VALUES(ingID)")){
		$response["success"] = 'Safety data has been updated!';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//REMOVE PICTOGRAM
if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'safety_info' && $_POST['pictogram_id'] && $_POST['action'] == 'remove'){
	$ingID = (int)$_POST['ingID'];
	$GHS = (int)$_POST['pictogram_id'];
	
	if(mysqli_query($conn, "DELETE FROM ingSafetyInfo WHERE GHS = '$GHS' AND ingID = '$ingID'")){
		$response["success"] = 'Safety data has been updated!';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


if($_GET['import'] == 'ingredient'){
		$name = sanChar(mysqli_real_escape_string($conn, base64_decode($_GET["name"])));
		$query = "INSERT INTO ingredients (name, INCI, cas, notes, odor) VALUES ('$name', '$INCI', '$cas', 'Auto Imported', '$odor')";
		
		if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$name'"))){
			echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$name.' already exists!</div>';
		}else{
			if(mysqli_query($conn, $query)){
				echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>Ingredient <strong>'.$name.'</strong> added!</div>';
			}else{
				echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Failed to add '.mysqli_error($conn).'</div>';
			}
		}	
	return;
}

//CLONE INGREDIENT
if($_POST['action'] == 'clone' && $_POST['old_ing_name'] && $_POST['ing_id']){
	$ing_id = mysqli_real_escape_string($conn, $_POST['ing_id']);

	$old_ing_name = mysqli_real_escape_string($conn, $_POST['old_ing_name']);
	$new_ing_name = mysqli_real_escape_string($conn, $_POST['new_ing_name']);
	if(empty($new_ing_name)){
		$response['error'] = 'Please provide a name';
		echo json_encode($response);
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$new_ing_name'"))){
		$response['error'] = $new_ing_name.' already exists';
		echo json_encode($response);
		return;
	}
	
	$sql.=mysqli_query($conn, "INSERT INTO ingredient_compounds (ing,name,cas,min_percentage,max_percentage) SELECT '$new_ing_name',name,cas,min_percentage,max_percentage FROM ingredient_compounds WHERE ing = '$old_ing_name'");

	$sql.=mysqli_query($conn, "INSERT INTO ingredients (name,INCI,type,strength,category,purity,cas,FEMA,reach,tenacity,chemical_name,formula,flash_point,appearance,notes,profile,solvent,odor,allergen,flavor_use,soluble,logp,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12,impact_top,impact_heart,impact_base,created,usage_type,noUsageLimit,isPrivate,molecularWeight,physical_state) SELECT '$new_ing_name',INCI,type,strength,category,purity,cas,FEMA,reach,tenacity,chemical_name,formula,flash_point,appearance,notes,profile,solvent,odor,allergen,flavor_use,soluble,logp,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12,impact_top,impact_heart,impact_base,created,usage_type,noUsageLimit,isPrivate,molecularWeight,physical_state FROM ingredients WHERE id = '$ing_id'");

	if($nID = mysqli_fetch_array(mysqli_query($conn, "SELECT id,name FROM ingredients WHERE name = '$new_ing_name'"))){
		
		$response['success'] = $old_ing_name.' duplicated as <a href="/pages/mgmIngredient.php?id='.$nID['id'].'" >'.$new_ing_name.'</a>';
		echo json_encode($response);
		return;
	}
	
	return;
}



//RENAME INGREDIENT
if($_POST['action'] == 'rename' && $_POST['old_ing_name'] && $_POST['ing_id']){
	$ing_id = mysqli_real_escape_string($conn, $_POST['ing_id']);

	$old_ing_name = mysqli_real_escape_string($conn, $_POST['old_ing_name']);
	$new_ing_name = mysqli_real_escape_string($conn, $_POST['new_ing_name']);
	if(empty($new_ing_name)){
		$response['error'] = 'Please provide a name';
		echo json_encode($response);
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$new_ing_name'"))){
		$response['error'] = $new_ing_name.' already exists';
		echo json_encode($response);
		return;
	}
	
	$sql.=mysqli_query($conn, "UPDATE ingredient_compounds SET ing = '$new_ing_name' WHERE ing = '$old_ing_name'");

	$sql.=mysqli_query($conn, "UPDATE ingredients SET name = '$new_ing_name' WHERE name = '$old_ing_name' AND id = '$ing_id'");
	$sql.=mysqli_query($conn, "UPDATE formulas SET ingredient = '$new_ing_name' WHERE ingredient = '$old_ing_name' AND ingredient_id = '$ing_id'");

	if($nID = mysqli_fetch_array(mysqli_query($conn, "SELECT id,name FROM ingredients WHERE name = '$new_ing_name'"))){
		
		$response['success']['msg'] = $old_ing_name.' renamed to <a href="/pages/mgmIngredient.php?id='.$nID['id'].'" >'.$new_ing_name.'</a>';
		$response['success']['id'] = $nID['id'];
		echo json_encode($response);
		return;
	}
	
	return;
}


//FIRST AID INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'faid_info') {
    $ingID = (int)$_POST['ingID'];
    
    $first_aid_general = $_POST['first_aid_general'];
    $first_aid_inhalation = $_POST['first_aid_inhalation'];
    $first_aid_skin = $_POST['first_aid_skin'];
    $first_aid_eye = $_POST['first_aid_eye'];
    $first_aid_ingestion = $_POST['first_aid_ingestion'];
    $first_aid_self_protection = $_POST['first_aid_self_protection'];
    $first_aid_symptoms = $_POST['first_aid_symptoms'];
    $first_aid_dr_notes = $_POST['first_aid_dr_notes'];
    
	 // Check if all fields are populated
    if (
        empty($ingID) || empty($first_aid_general) || empty($first_aid_inhalation) ||
        empty($first_aid_skin) || empty($first_aid_eye) || empty($first_aid_ingestion) ||
        empty($first_aid_self_protection) || empty($first_aid_symptoms) || empty($first_aid_dr_notes)
    ) {
        $response["error"] = 'All fields are required.';
        echo json_encode($response);
        return;
    }
	
    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, first_aid_general, first_aid_inhalation, first_aid_skin, first_aid_eye, 
            first_aid_ingestion, first_aid_self_protection, first_aid_symptoms, first_aid_dr_notes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            first_aid_general = VALUES(first_aid_general),
            first_aid_inhalation = VALUES(first_aid_inhalation),
            first_aid_skin = VALUES(first_aid_skin),
            first_aid_eye = VALUES(first_aid_eye),
            first_aid_ingestion = VALUES(first_aid_ingestion),
            first_aid_self_protection = VALUES(first_aid_self_protection),
            first_aid_symptoms = VALUES(first_aid_symptoms),
            first_aid_dr_notes = VALUES(first_aid_dr_notes)"
    );
    
    // Bind the parameters
    $stmt->bind_param(
        'issssssss', $ingID, $first_aid_general, $first_aid_inhalation, $first_aid_skin, $first_aid_eye, 
        $first_aid_ingestion, $first_aid_self_protection, $first_aid_symptoms, $first_aid_dr_notes
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'First aid data has been updated!';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }
    
    // Close the statement
    $stmt->close();
    
    echo json_encode($response);
    return;
}

//FIREFIGHTING
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'fire_info') {
    $ingID = (int)$_POST['ingID'];
    
    $firefighting_suitable_media = $_POST['firefighting_suitable_media'];
    $firefighting_non_suitable_media = $_POST['firefighting_non_suitable_media'];
    $firefighting_special_hazards = $_POST['firefighting_special_hazards'];
    $firefighting_advice = $_POST['firefighting_advice'];
    $firefighting_other_info = $_POST['firefighting_other_info'];
    
    // Check if all fields are populated
    if (
        empty($ingID) || empty($firefighting_suitable_media) || empty($firefighting_non_suitable_media) ||
        empty($firefighting_special_hazards) || empty($firefighting_advice) || empty($firefighting_other_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }
    
    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, firefighting_suitable_media, firefighting_non_suitable_media, 
            firefighting_special_hazards, firefighting_advice, firefighting_other_info
        ) VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            firefighting_suitable_media = VALUES(firefighting_suitable_media),
            firefighting_non_suitable_media = VALUES(firefighting_non_suitable_media),
            firefighting_special_hazards = VALUES(firefighting_special_hazards),
            firefighting_advice = VALUES(firefighting_advice),
            firefighting_other_info = VALUES(firefighting_other_info)"
    );
    
    // Bind the parameters
    $stmt->bind_param(
        'isssss', $ingID, $firefighting_suitable_media, $firefighting_non_suitable_media, 
        $firefighting_special_hazards, $firefighting_advice, $firefighting_other_info
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Firefighting data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }
    
    // Close the statement
    $stmt->close();
    
    echo json_encode($response);
    return;
}


//ACCIDENTAL RELEASE
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'save_acc_rel') {
    $ingID = (int)$_POST['ingID'];
    
    $accidental_release_per_precautions = $_POST['accidental_release_per_precautions'];
    $accidental_release_env_precautions = $_POST['accidental_release_env_precautions'];
    $accidental_release_cleaning = $_POST['accidental_release_cleaning'];
	$accidental_release_refs =  $_POST['accidental_release_refs'];
    $accidental_release_other_info = $_POST['accidental_release_other_info'];
    
    // Check if all fields are populated
    if (
        empty($ingID) || empty($accidental_release_per_precautions) || empty($accidental_release_env_precautions) ||
        empty($accidental_release_cleaning) ||  empty($accidental_release_refs) || empty($accidental_release_other_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }
    
    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, accidental_release_per_precautions, accidental_release_env_precautions, 
            accidental_release_cleaning, accidental_release_refs, accidental_release_other_info
        ) VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            accidental_release_per_precautions = VALUES(accidental_release_per_precautions),
            accidental_release_env_precautions = VALUES(accidental_release_env_precautions),
            accidental_release_cleaning = VALUES(accidental_release_cleaning),
			accidental_release_refs = VALUES(accidental_release_refs),
            accidental_release_other_info = VALUES(accidental_release_other_info)"
    );
    
    // Bind the parameters
    $stmt->bind_param(
        'isssss', $ingID, $accidental_release_per_precautions, $accidental_release_env_precautions, 
        $accidental_release_cleaning, $accidental_release_refs, $accidental_release_other_info
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Accidental release data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }
    
    // Close the statement
    $stmt->close();
    
    echo json_encode($response);
    return;
}

//HANDLING & STORAGE
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'HS') {
    $ingID = (int)$_POST['ingID'];
    
    $handling_protection = $_POST['handling_protection'];
    $handling_hygiene = $_POST['handling_hygiene'];
    $handling_safe_storage = $_POST['handling_safe_storage'];
	$handling_joint_storage =  $_POST['handling_joint_storage'];
    $handling_specific_uses = $_POST['handling_specific_uses'];
    
    // Check if all fields are populated
    if (
        empty($ingID) || empty($handling_protection) || empty($handling_hygiene) ||
        empty($handling_safe_storage) ||  empty($handling_joint_storage) || empty($handling_specific_uses)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }
    
    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, handling_protection, handling_hygiene, 
            handling_safe_storage, handling_joint_storage, handling_specific_uses
        ) VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            handling_protection = VALUES(handling_protection),
            handling_hygiene = VALUES(handling_hygiene),
            handling_safe_storage = VALUES(handling_safe_storage),
			handling_joint_storage = VALUES(handling_joint_storage),
            handling_specific_uses = VALUES(handling_specific_uses)"
    );
    
    // Bind the parameters
    $stmt->bind_param(
        'isssss', $ingID, $handling_protection, $handling_hygiene, 
        $handling_safe_storage, $handling_joint_storage, $handling_specific_uses
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Handling and storage data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }
    
    // Close the statement
    $stmt->close();
    
    echo json_encode($response);
    return;
}

//EXPOSURE DATA
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'exposure_data') {
    $ingID = (int)$_POST['ingID'];

    // New fields for exposure data
    $exposure_occupational_limits = $_POST['exposure_occupational_limits'];
    $exposure_biological_limits = $_POST['exposure_biological_limits'];
    $exposure_intented_use_limits = $_POST['exposure_intented_use_limits'];
    $exposure_other_remarks = $_POST['exposure_other_remarks'];
    $exposure_face_protection = $_POST['exposure_face_protection'];
    $exposure_skin_protection = $_POST['exposure_skin_protection'];
    $exposure_respiratory_protection = $_POST['exposure_respiratory_protection'];
    $exposure_env_exposure = $_POST['exposure_env_exposure'];
    $exposure_consumer_exposure = $_POST['exposure_consumer_exposure'];
    $exposure_other_info = $_POST['exposure_other_info'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($exposure_occupational_limits) || empty($exposure_biological_limits) || 
        empty($exposure_intented_use_limits) || empty($exposure_other_remarks) || empty($exposure_face_protection) || 
        empty($exposure_skin_protection) || empty($exposure_respiratory_protection) || empty($exposure_env_exposure) || 
        empty($exposure_consumer_exposure) || empty($exposure_other_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, exposure_occupational_limits, exposure_biological_limits, 
            exposure_intented_use_limits, exposure_other_remarks, 
            exposure_face_protection, exposure_skin_protection, 
            exposure_respiratory_protection, exposure_env_exposure, 
            exposure_consumer_exposure, exposure_other_info
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            exposure_occupational_limits = VALUES(exposure_occupational_limits),
            exposure_biological_limits = VALUES(exposure_biological_limits),
            exposure_intented_use_limits = VALUES(exposure_intented_use_limits),
            exposure_other_remarks = VALUES(exposure_other_remarks),
            exposure_face_protection = VALUES(exposure_face_protection),
            exposure_skin_protection = VALUES(exposure_skin_protection),
            exposure_respiratory_protection = VALUES(exposure_respiratory_protection),
            exposure_env_exposure = VALUES(exposure_env_exposure),
            exposure_consumer_exposure = VALUES(exposure_consumer_exposure),
            exposure_other_info = VALUES(exposure_other_info)"
    );

    // Bind the parameters
    $stmt->bind_param(
        'issssssssss', $ingID, $exposure_occupational_limits, $exposure_biological_limits, 
        $exposure_intented_use_limits, $exposure_other_remarks, 
        $exposure_face_protection, $exposure_skin_protection, 
        $exposure_respiratory_protection, $exposure_env_exposure, 
        $exposure_consumer_exposure, $exposure_other_info
    );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Exposure data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//PCP
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'pcp') {
    $ingID = (int)$_POST['ingID'];

    $odor_threshold = $_POST['odor_threshold'];
    $pH = $_POST['pH'];
    $melting_point = $_POST['melting_point'];
    $boiling_point = $_POST['boiling_point'];
    $flash_point = $_POST['flash_point'];
    $evaporation_rate = $_POST['evaporation_rate'];
    $solubility = $_POST['solubility'];
    $auto_infl_temp = $_POST['auto_infl_temp'];
    $decomp_temp = $_POST['decomp_temp'];
    $viscosity = $_POST['viscosity'];
    $explosive_properties = $_POST['explosive_properties'];
    $oxidising_properties = $_POST['oxidising_properties'];
    $particle_chars = $_POST['particle_chars'];
    $flammability = $_POST['flammability'];
    $logP = $_POST['logP'];
    $soluble = $_POST['soluble'];
    $color = $_POST['color'];
    $low_flammability_limit = $_POST['low_flammability_limit'];
    $vapour_pressure = $_POST['vapour_pressure'];
    $vapour_density = $_POST['vapour_density'];
    $relative_density = $_POST['relative_density'];
    $pcp_other_info = $_POST['pcp_other_info'];
    $pcp_other_sec_info = $_POST['pcp_other_sec_info'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($odor_threshold) || empty($pH) || 
        empty($melting_point) || empty($boiling_point) || empty($flash_point) || 
        empty($evaporation_rate) || empty($solubility) || empty($auto_infl_temp) || 
        empty($decomp_temp) || empty($viscosity) || empty($explosive_properties) || 
        empty($oxidising_properties) || empty($particle_chars) || empty($flammability) || 
        empty($logP) || empty($soluble) || empty($color) || empty($low_flammability_limit) || 
        empty($vapour_pressure) || empty($vapour_density) || empty($relative_density) || 
        empty($pcp_other_info) || empty($pcp_other_sec_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, odor_threshold, pH, melting_point, boiling_point, flash_point, 
            evaporation_rate, solubility, auto_infl_temp, decomp_temp, viscosity, 
            explosive_properties, oxidising_properties, particle_chars, flammability, 
            logP, soluble, color, low_flammability_limit, vapour_pressure, vapour_density, 
            relative_density, pcp_other_info, pcp_other_sec_info
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            odor_threshold = VALUES(odor_threshold),
            pH = VALUES(pH),
            melting_point = VALUES(melting_point),
            boiling_point = VALUES(boiling_point),
            flash_point = VALUES(flash_point),
            evaporation_rate = VALUES(evaporation_rate),
            solubility = VALUES(solubility),
            auto_infl_temp = VALUES(auto_infl_temp),
            decomp_temp = VALUES(decomp_temp),
            viscosity = VALUES(viscosity),
            explosive_properties = VALUES(explosive_properties),
            oxidising_properties = VALUES(oxidising_properties),
            particle_chars = VALUES(particle_chars),
            flammability = VALUES(flammability),
            logP = VALUES(logP),
            soluble = VALUES(soluble),
            color = VALUES(color),
            low_flammability_limit = VALUES(low_flammability_limit),
            vapour_pressure = VALUES(vapour_pressure),
            vapour_density = VALUES(vapour_density),
            relative_density = VALUES(relative_density),
            pcp_other_info = VALUES(pcp_other_info),
            pcp_other_sec_info = VALUES(pcp_other_sec_info)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param(
        'isssssssssssssssssssssss', $ingID, $odor_threshold, $pH, $melting_point, $boiling_point, 
        $flash_point, $evaporation_rate, $solubility, $auto_infl_temp, $decomp_temp, 
        $viscosity, $explosive_properties, $oxidising_properties, $particle_chars, 
        $flammability, $logP, $soluble, $color, $low_flammability_limit, $vapour_pressure, 
        $vapour_density, $relative_density, $pcp_other_info, $pcp_other_sec_info
    );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Ingredient safety data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}

//SR INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'sr_info') {
    $ingID = (int)$_POST['ingID'];


    $stabillity_reactivity = $_POST['stabillity_reactivity'];
    $stabillity_chemical = $_POST['stabillity_chemical'];
    $stabillity_reactions = $_POST['stabillity_reactions'];
    $stabillity_avoid = $_POST['stabillity_avoid'];
    $stabillity_incompatibility = $_POST['stabillity_incompatibility'];
   

    // Check if all fields are populated
    if (
        empty($ingID) || empty($stabillity_reactivity) || empty($stabillity_chemical) || 
        empty($stabillity_reactions) || empty($stabillity_avoid) || empty($stabillity_incompatibility)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, stabillity_reactivity, stabillity_chemical, stabillity_reactions, stabillity_avoid, stabillity_incompatibility
        ) VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            stabillity_reactivity = VALUES(stabillity_reactivity),
            stabillity_chemical = VALUES(stabillity_chemical),
            stabillity_reactions = VALUES(stabillity_reactions),
            stabillity_avoid = VALUES(stabillity_avoid),
            stabillity_incompatibility = VALUES(stabillity_incompatibility)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param( 'isssss', $ingID, $stabillity_reactivity, $stabillity_chemical, $stabillity_reactions, $stabillity_avoid, $stabillity_incompatibility );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Stability and reactivity data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//TOXICOLOGICAL INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'tx_info') {
    $ingID = (int)$_POST['ingID'];

    $toxicological_acute_oral = $_POST['toxicological_acute_oral'];
    $toxicological_acute_dermal = $_POST['toxicological_acute_dermal'];
    $toxicological_acute_inhalation = $_POST['toxicological_acute_inhalation'];
    $toxicological_skin = $_POST['toxicological_skin'];
    $toxicological_eye = $_POST['toxicological_eye'];
    $toxicological_sensitisation = $_POST['toxicological_sensitisation'];
    $toxicological_organ_repeated = $_POST['toxicological_organ_repeated'];
    $toxicological_organ_single = $_POST['toxicological_organ_single'];
    $toxicological_carcinogencity = $_POST['toxicological_carcinogencity'];
    $toxicological_reproductive = $_POST['toxicological_reproductive'];
    $toxicological_cell_mutation = $_POST['toxicological_cell_mutation'];
    $toxicological_resp_tract = $_POST['toxicological_resp_tract'];
    $toxicological_other_info = $_POST['toxicological_other_info'];
    $toxicological_other_hazards = $_POST['toxicological_other_hazards'];


    // Check if all fields are populated
    if (
        empty($ingID) || empty($toxicological_acute_oral) || empty($toxicological_acute_dermal) || 
        empty($toxicological_acute_inhalation) || empty($toxicological_skin) || empty($toxicological_eye) || 
        empty($toxicological_sensitisation) || empty($toxicological_organ_repeated) || empty($toxicological_organ_single) || 
        empty($toxicological_carcinogencity) || empty($toxicological_reproductive) || empty($toxicological_cell_mutation) || 
        empty($toxicological_resp_tract) || empty($toxicological_other_info) || empty($toxicological_other_hazards)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, toxicological_acute_oral, toxicological_acute_dermal, toxicological_acute_inhalation, toxicological_skin, toxicological_eye, toxicological_sensitisation, toxicological_organ_repeated, toxicological_organ_single, toxicological_carcinogencity, toxicological_reproductive, toxicological_cell_mutation, toxicological_resp_tract, toxicological_other_info, toxicological_other_hazards
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            toxicological_acute_oral = VALUES(toxicological_acute_oral),
            toxicological_acute_dermal = VALUES(toxicological_acute_dermal),
            toxicological_acute_inhalation = VALUES(toxicological_acute_inhalation),
            toxicological_skin = VALUES(toxicological_skin),
            toxicological_eye = VALUES(toxicological_eye),
            toxicological_sensitisation = VALUES(toxicological_sensitisation),
            toxicological_organ_repeated = VALUES(toxicological_organ_repeated),
            toxicological_organ_single = VALUES(toxicological_organ_single),
            toxicological_carcinogencity = VALUES(toxicological_carcinogencity),
            toxicological_reproductive = VALUES(toxicological_reproductive),
            toxicological_cell_mutation = VALUES(toxicological_cell_mutation),
            toxicological_resp_tract = VALUES(toxicological_resp_tract),
            toxicological_other_info = VALUES(toxicological_other_info),
            toxicological_other_hazards = VALUES(toxicological_other_hazards)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param(
        'issssssssssssss', $ingID, $toxicological_acute_oral, $toxicological_acute_dermal, $toxicological_acute_inhalation, $toxicological_skin, $toxicological_eye, $toxicological_sensitisation, $toxicological_organ_repeated, $toxicological_organ_single, $toxicological_carcinogencity, $toxicological_reproductive, $toxicological_cell_mutation, $toxicological_resp_tract, $toxicological_other_info, 
        $toxicological_other_hazards
    );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Toxicology data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//ECOLOGICAL INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'ec_info') {
    $ingID = (int)$_POST['ingID'];

    $ecological_toxicity = $_POST['ecological_toxicity'];
    $ecological_persistence = $_POST['ecological_persistence'];
    $ecological_bioaccumulative = $_POST['ecological_bioaccumulative'];
    $ecological_soil_mobility = $_POST['ecological_soil_mobility'];
    $ecological_PBT_vPvB = $_POST['ecological_PBT_vPvB'];
    $ecological_endocrine_properties = $_POST['ecological_endocrine_properties'];
    $ecological_other_adv_effects = $_POST['ecological_other_adv_effects'];
    $ecological_additional_ecotoxicological_info = $_POST['ecological_additional_ecotoxicological_info'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($ecological_toxicity) || empty($ecological_persistence) || empty($ecological_bioaccumulative) || empty($ecological_soil_mobility) || empty($ecological_PBT_vPvB) || 
        empty($ecological_endocrine_properties) || empty($ecological_other_adv_effects) || empty($ecological_additional_ecotoxicological_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, ecological_toxicity, ecological_persistence, ecological_bioaccumulative, ecological_soil_mobility, ecological_PBT_vPvB, ecological_endocrine_properties, ecological_other_adv_effects, ecological_additional_ecotoxicological_info
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            ecological_toxicity = VALUES(ecological_toxicity),
            ecological_persistence = VALUES(ecological_persistence),
            ecological_bioaccumulative = VALUES(ecological_bioaccumulative),
            ecological_soil_mobility = VALUES(ecological_soil_mobility),
            ecological_PBT_vPvB = VALUES(ecological_PBT_vPvB),
            ecological_endocrine_properties = VALUES(ecological_endocrine_properties),
            ecological_other_adv_effects = VALUES(ecological_other_adv_effects),
            ecological_additional_ecotoxicological_info = VALUES(ecological_additional_ecotoxicological_info)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param(
        'issssssss', $ingID, $ecological_toxicity, $ecological_persistence, $ecological_bioaccumulative, $ecological_soil_mobility, $ecological_PBT_vPvB, $ecological_endocrine_properties, $ecological_other_adv_effects, $ecological_additional_ecotoxicological_info
    );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Ecology data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//DISPOSE INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'dis_info') {
    $ingID = (int)$_POST['ingID'];


    $disposal_product = $_POST['disposal_product'];
    $disposal_remarks = $_POST['disposal_remarks'];


    // Check if all fields are populated
    if ( empty($ingID) || empty($disposal_product) || empty($disposal_remarks) ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data ( ingID, disposal_product, disposal_remarks ) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            disposal_product = VALUES(disposal_product),
            disposal_remarks = VALUES(disposal_remarks)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param( 'iss', $ingID, $disposal_product, $disposal_remarks );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Dispose data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}




//TRANSPORTATION INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'trans_info') {
    $ingID = (int)$_POST['ingID'];

    $transport_un_number = $_POST['transport_un_number'];
    $transport_shipping_name = $_POST['transport_shipping_name'];
    $transport_hazard_class = $_POST['transport_hazard_class'];
    $transport_packing_group = $_POST['transport_packing_group'];
    $transport_env_hazards = $_POST['transport_env_hazards'];
    $transport_precautions = $_POST['transport_precautions'];
    $transport_bulk_shipping = $_POST['transport_bulk_shipping'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($transport_un_number) || empty($transport_shipping_name) || empty($transport_hazard_class) || empty($transport_packing_group) || empty($transport_env_hazards) || 
        empty($transport_precautions) || empty($transport_bulk_shipping)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, transport_un_number, transport_shipping_name, transport_hazard_class, transport_packing_group, transport_env_hazards, transport_precautions, transport_bulk_shipping
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            transport_un_number = VALUES(transport_un_number),
            transport_shipping_name = VALUES(transport_shipping_name),
            transport_hazard_class = VALUES(transport_hazard_class),
            transport_packing_group = VALUES(transport_packing_group),
            transport_env_hazards = VALUES(transport_env_hazards),
            transport_precautions = VALUES(transport_precautions),
            transport_bulk_shipping = VALUES(transport_bulk_shipping)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param(
        'isssssss', $ingID, $transport_un_number, $transport_shipping_name, $transport_hazard_class, $transport_packing_group, $transport_env_hazards, $transport_precautions, $transport_bulk_shipping
    );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Transportation data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}

//LEGISLATION INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'leg_info') {
    $ingID = (int)$_POST['ingID'];

    $legislation_safety = $_POST['legislation_safety'];
    $legislation_eu = $_POST['legislation_eu'];
    $legislation_chemical_safety_assessment = $_POST['legislation_chemical_safety_assessment'];
    $legislation_other_info = $_POST['legislation_other_info'];


    // Check if all fields are populated
    if (
        empty($ingID) || empty($legislation_safety) || empty($legislation_eu) || empty($legislation_chemical_safety_assessment) || empty($legislation_other_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, legislation_safety, legislation_eu, legislation_chemical_safety_assessment, legislation_other_info
        ) VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            legislation_safety = VALUES(legislation_safety),
            legislation_eu = VALUES(legislation_eu),
            legislation_chemical_safety_assessment = VALUES(legislation_chemical_safety_assessment),
            legislation_other_info = VALUES(legislation_other_info)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param( 'issss', $ingID, $legislation_safety, $legislation_eu, $legislation_chemical_safety_assessment, $legislation_other_info );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Legislation data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}



//ADDITIONAL INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'add_info') {
    $ingID = (int)$_POST['ingID'];

    $add_info_changes = $_POST['add_info_changes'];
    $add_info_acronyms = $_POST['add_info_acronyms'];
    $add_info_references = $_POST['add_info_references'];
    $add_info_HazCom = $_POST['add_info_HazCom'];
	$add_info_GHS = $_POST['add_info_GHS'];
	$add_info_training = $_POST['add_info_training'];
	$add_info_other = $_POST['add_info_other'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($add_info_changes) || empty($add_info_acronyms) || empty($add_info_references) || empty($add_info_HazCom) || empty($add_info_GHS) || empty($add_info_training) || empty($add_info_other) 
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, add_info_changes, add_info_acronyms, add_info_references, add_info_HazCom, add_info_GHS, add_info_training, add_info_other
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            add_info_changes = VALUES(add_info_changes),
            add_info_acronyms = VALUES(add_info_acronyms),
            add_info_references = VALUES(add_info_references),
            add_info_HazCom = VALUES(add_info_HazCom),
            add_info_GHS = VALUES(add_info_GHS),
            add_info_training = VALUES(add_info_training),
            add_info_other = VALUES(add_info_other)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param( 'isssssss', $ingID, $add_info_changes, $add_info_acronyms, $add_info_references, $add_info_HazCom, $add_info_GHS , $add_info_training , $add_info_other );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Additional info data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//IMPORT MARKETPLACE FORMULA
if($_POST['action'] == 'import' && $_POST['kind'] == 'formula'){
	
	$id = mysqli_real_escape_string($conn, $_POST['fid']);
	
	$jAPI = $pvOnlineAPI.'?request=MarketPlace&action=get&id='.$id;
    $jsonData = json_decode(pv_file_get_contents($jAPI), true);

    if($jsonData['error']){
		$response['error'] = 'Error: '.$jsonData['error']['msg'];
		echo json_encode($response);
        return;
    }

	if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE fid = '".$jsonData['meta']['fid']."' AND src = '1'"))){
	  $response['error'] = 'Formula name '.$jsonData['meta']['name'].' already downloaded. If you want to re-download it, please remove it first.';
	  echo json_encode($response);
	  return;
	}

	$q = "INSERT INTO formulasMetaData (name,product_name,fid,profile,gender,notes,defView,catClass,finalType,status,src) VALUES ('".$jsonData['meta']['name']."','".$jsonData['meta']['product_name']."','".$jsonData['meta']['fid']."','".$jsonData['meta']['profile']."','".$jsonData['meta']['gender']."','".$jsonData['meta']['notes']."','".$jsonData['meta']['defView']."','".$jsonData['meta']['catClass']."','".$jsonData['meta']['finalType']."','".$jsonData['meta']['status']."','1')";
	
    $qIns = mysqli_query($conn,$q);
	$last_id = mysqli_insert_id($conn);
	$source = $jsonData['meta']['source'];
	mysqli_query($conn, "INSERT INTO formulasTags (formula_id, tag_name) VALUES ('$last_id','$source')");
		
   $array_data = $jsonData['formula'];
   foreach ($array_data as $id=>$row) {
	  $insertPairs = array();
      	foreach ($row as $key=>$val) {
      		$insertPairs[addslashes($key)] = addslashes($val);
      	}
		$getIng = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '".$row['ingredient']."'"));
		if(!$getIng['id']){
			mysqli_query($conn, "INSERT INTO ingredients (name) VALUES ('".$row['ingredient']."')");
			$getIng['id'] = mysqli_insert_id($conn);
		}
		
      	$insertVals = '"'.$jsonData['meta']['fid'].'",'.'"'.$jsonData['meta']['name'].'",'.'"'.$getIng['id'].'",'.'"' . implode('","', array_values($insertPairs)) . '"';
   
      $jsql = "INSERT INTO formulas (`fid`,`name`,`ingredient_id`,`ingredient`,`concentration`,`dilutant`,`quantity`,`notes`) VALUES ({$insertVals});";
       $qIns.= mysqli_query($conn,$jsql);
    
	}
	
    if($qIns){
		$response['success'] = $jsonData['meta']['name'].' formula imported!';
    }else{
		$response['error'] = 'Unable to import the formula '.mysqli_error($conn);
    }
	echo json_encode($response);
	return;
}

//CONTACT MARKETPLACE AUTHOR
if($_POST['action'] == 'contactAuthor'){
	$fname = $_POST['fname'];
	$fid= $_POST['fid'];
	
	if(empty($contactName = $_POST['contactName'])){
		$response['error'] = 'Please provide your full name';
		echo json_encode($response);
		return;
	}
	if(empty($contactEmail = $_POST['contactEmail'])){
		$response['error'] = 'Please provide your email';
		echo json_encode($response);
		return;
	}
	if(empty($contactReason = $_POST['contactReason'])){
		$response['error'] = 'Please provide report details';
		echo json_encode($response);
		return;
	}
	

	$data = [ 
		 'request' => 'MarketPlace',
		 'action' => 'contactAuthor',
		 'src' => 'marketplace',
		 'fname' => $fname, 
		 'fid' => $fid,
		 'contactName' => $contactName,
		 'contactEmail' => $contactEmail,
		 'contactReason' => $contactReason
		 ];
	
    $req = json_decode(pvPost($pvOnlineAPI, $data));
	if($req->success){
		$response['success'] = $req->success;
	}else{
		$response['error'] = $req->error;
	}
	echo json_encode($response);
	return;
	
}

//REPORT MARKETPLACE FORMULA
if($_POST['action'] == 'report' && $_POST['src'] == 'pvMarket'){
	$fname = $_POST['fname'];
	$fid= $_POST['fid'];
	
	if(empty($reporterName = $_POST['reporterName'])){
		$response['error'] = 'Please provide your full name';
		echo json_encode($response);
		return;
	}
	if(empty($reporterEmail = $_POST['reporterEmail'])){
		$response['error'] = 'Please provide your email';
		echo json_encode($response);
		return;
	}
	if(empty($reportReason = $_POST['reportReason'])){
		$response['error'] = 'Please provide report details';
		echo json_encode($response);
		return;
	}
	

	$data = [ 
		 'request' => 'MarketPlace',
		 'action' => 'report',
		 'src' => 'marketplace',
		 'fname' => $fname, 
		 'fid' => $fid,
		 'reporterName' => $reporterName,
		 'reporterEmail' => $reporterEmail,
		 'reportReason' => $reportReason
		 ];
	
    $req = json_decode(pvPost($pvOnlineAPI, $data));
	if($req->success){
		$response['success'] = $req->success;
	}else if($req->error){
		$response['error'] = $req->error;
	}else{
		$response['error'] = "Uknown error";
	}
	echo json_encode($response);
	return;
	
}	


if($_GET['do'] == 'userPerfClear'){

	if(mysqli_query($conn, "DELETE FROM user_prefs WHERE owner = '".$_SESSION['userID']."'")){
		$result['success'] = "User prefernces removed";
	}else{
		$result['error'] = 'Something went wrong, '.mysqli_error($conn);
		
	}
	unset($_SESSION['user_prefs']);
	echo json_encode($result);
	return;
}


if($_GET['do'] == 'db_update'){

	$a_ver = trim(file_get_contents(__ROOT__.'/VERSION.md'));
	$n_ver = trim(file_get_contents(__ROOT__.'/db/schema.ver'));
	$c_ver = trim($pv_meta['schema_ver']);
	$script = __ROOT__.'/db/scripts/update_'.$c_ver.'-'.$n_ver.'.php';

	if(file_exists($script) == TRUE){
		require_once($script);
	}
  	if($c_ver == $n_ver){
		$result['error'] = "No update is needed";
		echo json_encode($result);
		return;
    }

	foreach ( range(round($c_ver*100), round($n_ver*100),  0.1*100) as $i ) {
		$c_ver = mysqli_fetch_array(mysqli_query($conn, "SELECT schema_ver FROM pv_meta"));
		$u_ver = number_format($i/100,1);
		$sql = __ROOT__.'/db/updates/update_'.$c_ver['schema_ver'].'-'.$u_ver.'.sql';
	
		if(file_exists($sql) == TRUE){	
			$cmd = "mysql -u$dbuser -p$dbpass -h$dbhost $dbname < $sql";
			passthru($cmd,$e);
		}
		
		$q = mysqli_query($conn, "UPDATE pv_meta SET schema_ver = '$u_ver'");
	}

	if($q){
		$result['success'] = "Your database has been updated";
		echo json_encode($result);
	}
	
	return;
}


if($_GET['do'] == 'backupDB'){
	if( getenv('DB_BACKUP_PARAMETERS') ){
		$bkparams = getenv('DB_BACKUP_PARAMETERS');
	}
	
	if($_GET['column_statistics'] === 'true'){
		$bkparams = '--column-statistics=1';
	}
	
	$file = 'backup_'.$ver.'_'.date("d-m-Y").'.sql.gz';
	
	header( 'Content-Type: application/x-gzip' );
	header( 'Content-Disposition: attachment; filename="' .$file. '"' );
	$cmd = "mysqldump $bkparams -u $dbuser --password=$dbpass -h $dbhost $dbname | gzip --best";
	passthru($cmd);
	
	return;
}

if($_GET['restore'] == 'db_bk'){
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0777, true);
	}
	
	$target_path = $tmp_path.basename($_FILES['backupFile']['name']); 

	if(move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    	$gz_tmp = basename($_FILES['backupFile']['name']);
		preg_match('/_(.*?)_/', $gz_tmp, $v);

		if($ver !== $v['1']){
			$result['error'] = "Backup file is taken from a different version ".$v['1'];
			echo json_encode($result);
			return;
		}
		
		system("gunzip -c $target_path > ".$tmp_path.'restore.sql');
		$cmd = "mysql -u$dbuser -p$dbpass -h$dbhost $dbname < ".$tmp_path.'restore.sql'; 
		passthru($cmd,$e);
		
		unlink($target_path);
		unlink($tmp_path.'restore.sql');
		
		if(!$e){
			$result['success'] = 'Database has been restored. Please refresh the page for the changes to take effect.';
			unset($_SESSION['parfumvault']);
			session_unset();
		}else{
			$result['error'] = "Something went wrong...";
		}
	} else {
		$result['error'] = "There was an error processing backup file $target_path, please try again!";
	}
	
	echo json_encode($result);
	return;
}

if($_GET['action'] == 'exportIFRA'){
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM IFRALibrary")))){
		$msg['error'] = 'No data found to export.';
		echo json_encode($msg);
		return;
	}
	$IFRA_Data = 0;
	$q = mysqli_query($conn, "SELECT * FROM IFRALibrary");
	while($ifra = mysqli_fetch_assoc($q)){
		
		$r['id'] = (int)$ifra['id'];
		$r['ifra_key'] = (string)$ifra['ifra_key']?: "-";
		$r['image'] = (string)$ifra['image']?: "-";
		$r['amendment'] = (string)$ifra['amendment']?: "-";
		$r['prev_pub'] = (string)$ifra['prev_pub']?: "-";
		$r['last_pub'] = (string)$ifra['last_pub']?: "-";
		$r['deadline_existing'] = (string)$ifra['deadline_existing']?: "-";
		$r['deadline_new'] = (string)$ifra['deadline_new']?: "-";
		$r['name'] = (string)$ifra['name']?: "-";
		$r['cas'] = (string)$ifra['cas']?: "-";
		$r['cas_comment'] = (string)$ifra['cas_comment']?: "-";
		$r['synonyms'] = (string)$ifra['synonyms']?: "-";
		$r['formula'] = (string)$ifra['formula']?: "-";//DEPRECATED IN IFRA 51
		$r['flavor_use'] = (string)$ifra['flavor_use']?: "-";
		$r['prohibited_notes'] = (string)$ifra['prohibited_notes'] ?: "-";
		$r['restricted_photo_notes'] = (string)$ifra['restricted_photo_notes']?: "-";
		$r['restricted_notes'] = (string)$ifra['restricted_notes']?: "-";
		$r['specified_notes'] = (string)$ifra['specified_notes']?: "-";
		$r['type'] = (string)$ifra['type']?: "-";
		$r['risk'] = (string)$ifra['risk']?: "-";
		$r['contrib_others'] = (string)$ifra['contrib_others']?: "-";
		$r['contrib_others_notes'] = (string)$ifra['contrib_others_notes']?: "-";
		$r['cat1'] = (double)$ifra['cat1']?: 100;
		$r['cat2'] = (double)$ifra['cat2']?: 100;
		$r['cat3'] = (double)$ifra['cat3']?: 100;
		$r['cat4'] = (double)$ifra['cat4']?: 100;
		$r['cat5A'] = (double)$ifra['cat5A']?: 100;
		$r['cat5B'] = (double)$ifra['cat5B']?: 100;
		$r['cat5C'] = (double)$ifra['cat5C']?: 100;
		$r['cat5D'] = (double)$ifra['cat5D']?: 100;
		$r['cat6'] = (double)$ifra['cat6']?: 100;
		$r['cat7A'] = (double)$ifra['cat7A']?: 100;
		$r['cat7B'] = (double)$ifra['cat7B']?: 100;
		$r['cat8'] = (double)$ifra['cat8']?: 100;
		$r['cat9'] = (double)$ifra['cat9']?: 100;
		$r['cat10A'] = (double)$ifra['cat10A']?: 100;
		$r['cat10B'] = (double)$ifra['cat10B']?: 100;
		$r['cat11A'] = (double)$ifra['cat11A']?: 100;
		$r['cat11B'] = (double)$ifra['cat11B']?: 100;
		$r['cat12'] = (double)$ifra['cat12']?: 100;

		$IFRA_Data++;
		$if[] = $r;
	}
	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	$vd['ifra_entries'] = $IFRA_Data;
	$vd['timestamp'] = date('d/m/Y H:i:s');

	
	$result['IFRALibrary'] = $if;
	$result['pvMeta'] = $vd;
	
	header('Content-disposition: attachment; filename=IFRALibrary.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;
}

if($_GET['action'] == 'exportFormulas'){
	if($_GET['fid']){
		$filter = " WHERE fid ='".$_GET['fid']."'";
	}
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData")))){
		$msg['error'] = 'No formulas found to export.';
		echo json_encode($msg);
		return;
	}
	$formulas = 0;
	$ingredients = 0;
	
	$qfmd = mysqli_query($conn, "SELECT * FROM formulasMetaData $filter");
	while($meta = mysqli_fetch_assoc($qfmd)){
		
		$r['id'] = (int)$meta['id'];
		$r['name'] = (string)$meta['name'];
		$r['product_name'] = (string)$meta['product_name'];
		$r['fid'] = (string)$meta['fid'];
		$r['profile'] = (string)$meta['profile'];
		$r['category'] = (string)$meta['profile'] ?: 'Default';
		$r['gender'] = (string)$meta['gender'];
		$r['notes'] = (string)$meta['notes'] ?: 'None';
		$r['created'] = (string)$meta['created'];
		$r['isProtected'] = (int)$meta['isProtected'] ?: 0;
		$r['defView'] = (int)$meta['defView'];
		$r['catClass'] = (string)$meta['catClass'];
		$r['revision'] = (int)$meta['revision'] ?: 0;
		$r['finalType'] = (int)$meta['finalType'] ?: 100;
		$r['isMade'] = (int)$meta['isMade'];
		$r['madeOn'] = (string)$meta['madeOn'] ?: "0000-00-00 00:00:00";
		$r['scheduledOn'] = (string)$meta['scheduledOn'];
		$r['customer_id'] = (int)$meta['customer_id'];
		$r['status'] = (int)$meta['status'];
		$r['toDo'] = (int)$meta['toDo'];
		$r['rating'] = (int)$meta['rating'] ?: 0;
		
		$formulas++;
		$fm[] = $r;
	}
	
	$qfm = mysqli_query($conn, "SELECT * FROM formulas $filter");
	while($formula = mysqli_fetch_assoc($qfm)){
		
		
		$f['id'] = (int)$formula['id'];
		$f['fid'] = (string)$formula['fid'];
		$f['name'] = (string)$formula['name'];
		$f['ingredient'] = (string)$formula['ingredient'];
		$f['ingredient_id'] = (int)$formula['ingredient_id'] ?: 0;
		$f['concentration'] = (float)$formula['concentration'] ?: 100;
		$f['dilutant'] = (string)$formula['dilutant'] ?: 'None';
		$f['quantity'] = (float)$formula['quantity'];
		$f['exclude_from_summary'] = (int)$formula['exclude_from_summary'];
		$f['exclude_from_calculation'] = (int)$formula['exclude_from_calculation'];
		$f['notes'] = (string)$formula['notes'] ?: 'None';
		$f['created'] = (string)$formula['created'];
		$f['updated'] = (string)$formula['updated'];
		
		$ingredients++;
		$fd[] = $f;
	}
	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	$vd['formulas'] = $formulas;
	$vd['ingredients'] = $ingredients;
	$vd['timestamp'] = date('d/m/Y H:i:s');

	
	$result['formulasMetaData'] = $fm;
	$result['formulas'] = $fd;
	$result['pvMeta'] = $vd;

	if(!$_GET['fid']){
		$f['name'] = "All_formulas";
	}
	
	header('Content-disposition: attachment; filename='.$f['name'].'.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;
}

if($_GET['action'] == 'restoreFormulas'){
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0777, true);
	}
	
	if (!is_writable($tmp_path)) {
		$result['error'] = "Upload directory not writable. Make sure you have write permissions.";
		echo json_encode($result);
		return;
	}
	
	$target_path = $tmp_path.basename($_FILES['backupFile']['name']); 

	if(move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    	$data = json_decode(file_get_contents($target_path), true);
		
		if(!$data['formulasMetaData']){
			$result['error'] = "JSON File seems invalid. Please make sure you importing the right file";
			echo json_encode($result);
			return;
		}
		
		foreach ($data['formulasMetaData'] as $meta ){				
			$name = mysqli_real_escape_string($conn, $meta['name']);
			$product_name = mysqli_real_escape_string($conn, $meta['product_name']);
			$notes = mysqli_real_escape_string($conn, $meta['notes']);
			
			$sql = "INSERT IGNORE INTO formulasMetaData(name,product_name,fid,profile,gender,notes,created,isProtected,defView,catClass,revision,finalType,isMade,madeOn,scheduledOn,customer_id,status,toDo,rating) VALUES('".$name."','".$product_name."','".$meta['fid']."','".$meta['profile']."','".$meta['gender']."','".$notes."','".$meta['created']."','".$meta['isProtected']."','".$meta['defView']."','".$meta['catClass']."','".$meta['revision']."','".$meta['finalType']."','".$meta['isMade']."','".$meta['madeOn']."','".$meta['scheduledOn']."','".$meta['customer_id']."','".$meta['status']."','".$meta['toDo']."','".$meta['rating']."')";
			
			if(mysqli_query($conn,$sql)){
				mysqli_query($conn,"DELETE FROM formulas WHERE fid = '".$meta['fid']."'");
			}else{
				$result['error'] = "There was an error importing your JSON file ".mysqli_error($conn);
				echo json_encode($result);
				return;
			}
		}
		
		foreach ($data['formulas'] as $formula ){	
	
			$name = mysqli_real_escape_string($conn, $formula['name']);
			$notes = mysqli_real_escape_string($conn, $formula['notes']);
			$ingredient = mysqli_real_escape_string($conn, $formula['ingredient']);
			$exclude_from_summary = $formula['exclude_from_summary'] ?: 0;
			$exclude_from_calculation = $formula['exclude_from_calculation'] ?: 0;
			$created = $formula['created'] ?:  date('Y-m-d H:i:s');
			$updated = $formula['updated'] ?:  date('Y-m-d H:i:s');

			$sql = "INSERT INTO formulas(fid,name,ingredient,ingredient_id,concentration,dilutant,quantity,exclude_from_summary,exclude_from_calculation,notes,created,updated) VALUES('".$formula['fid']."','".$name."','".$ingredient."','".$formula['ingredient_id']."','".$formula['concentration']."','".$formula['dilutant']."','".$formula['quantity']."','".$exclude_from_summary."','".$exclude_from_calculation."','".$notes."','".$created."','".$updated."')";
			
			if(mysqli_query($conn,$sql)){
				$result['success'] = "Import complete";
				unlink($target_path);
			}else{
				$result['error'] = "There was an error importing your JSON file ".mysqli_error($conn);
				
			}
		}
		
	} else {
		$result['error'] = "There was an error processing backup file $target_path, please try again!";
		echo json_encode($result);

	}
	echo json_encode($result);
	return;

}

if($_GET['action'] == 'restoreIngredients'){
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0777, true);
	}
	
	if (!is_writable($tmp_path)) {
		$result['error'] = "Upload directory not writable. Make sure you have write permissions.";
		echo json_encode($result);
		return;
	}
	
	$target_path = $tmp_path.basename($_FILES['backupFile']['name']); 

	if(move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    	$data = json_decode(file_get_contents($target_path), true);
		
		if(!$data['ingredients']){
			$result['error'] = "JSON File seems invalid. Please make sure you importing the right file";
			echo json_encode($result);
			return;
		}
		/*
		foreach ($data['suppliers'] as $sup) {
			$id = mysqli_real_escape_string($conn, $sup['id']);
			$ingSupplierID = mysqli_real_escape_string($conn, $sup['ingSupplierID']);
			$ingID = mysqli_real_escape_string($conn, $sup['ingID']);
			$supplierLink = mysqli_real_escape_string($conn, $sup['supplierLink']);
			$price = mysqli_real_escape_string($conn, $sup['price']);
			$size = mysqli_real_escape_string($conn, $sup['size']);
			$manufacturer = mysqli_real_escape_string($conn, $sup['manufacturer']);
			$preferred = mysqli_real_escape_string($conn, $sup['preferred']);
			$batch = mysqli_real_escape_string($conn, $sup['batch']);
			$purchased = mysqli_real_escape_string($conn, $sup['purchased']);
			$mUnit = mysqli_real_escape_string($conn, $sup['mUnit']);
			$stock = mysqli_real_escape_string($conn, $sup['stock']);
			$status = mysqli_real_escape_string($conn, $sup['status']);
			$supplier_sku = mysqli_real_escape_string($conn, $sup['supplier_sku']);
			$internal_sku = mysqli_real_escape_string($conn, $sup['internal_sku']);
			$storage_location = mysqli_real_escape_string($conn, $sup['storage_location']);
		
			$sql = "INSERT IGNORE INTO `suppliers` (`id`,`ingSupplierID`,`ingID`,`supplierLink`,`price`,`size`,`manufacturer`,`preferred`,`batch`,`purchased`,`mUnit`,`stock`,`status`,`supplier_sku`,`internal_sku`,`storage_location`,`created_at`) 
					VALUES ('$id','$ingSupplierID','$ingID','$supplierLink','$price','$size','$manufacturer','$preferred','$batch','$purchased','$mUnit','$stock','$status','$supplier_sku','$internal_sku','$storage_location',current_timestamp())";
		
			mysqli_query($conn, $sql);
		}

		*/
		foreach ($data['compositions'] as $cmp) {
			$stmt = mysqli_prepare($conn, "INSERT IGNORE INTO `ingredient_compounds` (`ing`, `name`, `cas`, `ec`, `min_percentage`, `max_percentage`, `GHS`, `toDeclare`, `created`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, current_timestamp())");
		
			if ($stmt === false) {
				$result['error'] = 'Prepare failed: ' . mysqli_error($conn);
				echo json_encode($result);
				return;
			}
		
			mysqli_stmt_bind_param($stmt, "ssssdsss", $cmp['ing'], $cmp['name'], $cmp['cas'], $cmp['ec'], $cmp['min_percentage'], $cmp['max_percentage'], $cmp['GHS'], $cmp['toDeclare']);
		
			$execute_result = mysqli_stmt_execute($stmt);
			
			if ($execute_result === false) {
				$result['error'] = 'Execute failed: ' . mysqli_stmt_error($stmt);
				echo json_encode($result);
				return;
			}
		
			mysqli_stmt_close($stmt);
		}


		foreach ($data['suppliers'] as $sup) {
			$id = mysqli_real_escape_string($conn, $sup['id']);
			$ingSupplierID = mysqli_real_escape_string($conn, $sup['ingSupplierID']);
			$ingID = mysqli_real_escape_string($conn, $sup['ingID']);
			$supplierLink = mysqli_real_escape_string($conn, $sup['supplierLink']);
			$price = mysqli_real_escape_string($conn, $sup['price']);
			$size = mysqli_real_escape_string($conn, $sup['size']);
			$manufacturer = mysqli_real_escape_string($conn, $sup['manufacturer']);
			$preferred = mysqli_real_escape_string($conn, $sup['preferred']);
			$batch = mysqli_real_escape_string($conn, $sup['batch']);
			$purchased = mysqli_real_escape_string($conn, $sup['purchased']);
			$mUnit = mysqli_real_escape_string($conn, $sup['mUnit']);
			$stock = mysqli_real_escape_string($conn, $sup['stock']);
			$status = mysqli_real_escape_string($conn, $sup['status']);
			$supplier_sku = mysqli_real_escape_string($conn, $sup['supplier_sku']);
			$internal_sku = mysqli_real_escape_string($conn, $sup['internal_sku']);
			$storage_location = mysqli_real_escape_string($conn, $sup['storage_location']);
		
			// Validate that price is numeric, non-empty, and non-zero
			if (!is_numeric($price) || empty($price) || $price == 0) {
				$warn.="Invalid price for supplier ID $id - Ignoring<br/>";
				continue; // Skip to the next entry
			}
			$sql = "INSERT IGNORE INTO `suppliers` (`id`,`ingSupplierID`,`ingID`,`supplierLink`,`price`,`size`,`manufacturer`,`preferred`,`batch`,`purchased`,`mUnit`,`stock`,`status`,`supplier_sku`,`internal_sku`,`storage_location`,`created_at`) 
					VALUES ('$id','$ingSupplierID','$ingID','$supplierLink','$price','$size','$manufacturer','$preferred','$batch','$purchased','$mUnit','$stock','$status','$supplier_sku','$internal_sku','$storage_location',current_timestamp())";
		
			mysqli_query($conn, $sql);
		}

		
		foreach ($data['ingSuppliers'] as $is) {
			$id = mysqli_real_escape_string($conn, $is['id']);
			$name = mysqli_real_escape_string($conn, $is['name']);
			$address = mysqli_real_escape_string($conn, $is['address']);
			$po = mysqli_real_escape_string($conn, $is['po']);
			$country = mysqli_real_escape_string($conn, $is['country']);
			$telephone = mysqli_real_escape_string($conn, $is['telephone']);
			$url = mysqli_real_escape_string($conn, $is['url']);
			$email = mysqli_real_escape_string($conn, $is['email']);
		
			$sql = "INSERT IGNORE INTO `ingSuppliers` (`id`,`name`,`address`,`po`,`country`,`telephone`,`url`,`email`) 
					VALUES ('$id','$name','$address','$po','$country','$telephone','$url','$email')";
		
			if (!mysqli_query($conn, $sql)) {
				$result['error'] = mysqli_error($conn);
				echo json_encode($result);
				return;
			}
		}

		
		foreach ($data['ingredients'] as $ingredient) {
			$id = mysqli_real_escape_string($conn, $ingredient['id']);
			$name = mysqli_real_escape_string($conn, $ingredient['name']);
			$INCI = mysqli_real_escape_string($conn, $ingredient['INCI']);
			$cas = mysqli_real_escape_string($conn, $ingredient['cas']);
			$FEMA = mysqli_real_escape_string($conn, $ingredient['FEMA']);
			$type = mysqli_real_escape_string($conn, $ingredient['type']);
			$strength = mysqli_real_escape_string($conn, $ingredient['strength']);
			$category = mysqli_real_escape_string($conn, $ingredient['category']);
			$purity = mysqli_real_escape_string($conn, $ingredient['purity']);
			$einecs = mysqli_real_escape_string($conn, $ingredient['einecs']);
			$reach = mysqli_real_escape_string($conn, $ingredient['reach']);
			$tenacity = mysqli_real_escape_string($conn, $ingredient['tenacity']);
			$chemical_name = mysqli_real_escape_string($conn, $ingredient['chemical_name']);
			$formula = mysqli_real_escape_string($conn, $ingredient['formula']);
			$flash_point = mysqli_real_escape_string($conn, $ingredient['flash_point']);
			$notes = mysqli_real_escape_string($conn, $ingredient['notes']);
			$flavor_use = mysqli_real_escape_string($conn, $ingredient['flavor_use']);
			$soluble = mysqli_real_escape_string($conn, $ingredient['soluble']);
			$logp = mysqli_real_escape_string($conn, $ingredient['logp']);
			$cat1 = mysqli_real_escape_string($conn, $ingredient['cat1']);
			$cat2 = mysqli_real_escape_string($conn, $ingredient['cat2']);
			$cat3 = mysqli_real_escape_string($conn, $ingredient['cat3']);
			$cat4 = mysqli_real_escape_string($conn, $ingredient['cat4']);
			$cat5A = mysqli_real_escape_string($conn, $ingredient['cat5A']);
			$cat5B = mysqli_real_escape_string($conn, $ingredient['cat5B']);
			$cat5C = mysqli_real_escape_string($conn, $ingredient['cat5C']);
			$cat6 = mysqli_real_escape_string($conn, $ingredient['cat6']);
			$cat7A = mysqli_real_escape_string($conn, $ingredient['cat7A']);
			$cat7B = mysqli_real_escape_string($conn, $ingredient['cat7B']);
			$cat8 = mysqli_real_escape_string($conn, $ingredient['cat8']);
			$cat9 = mysqli_real_escape_string($conn, $ingredient['cat9']);
			$cat10A = mysqli_real_escape_string($conn, $ingredient['cat10A']);
			$cat10B = mysqli_real_escape_string($conn, $ingredient['cat10B']);
			$cat11A = mysqli_real_escape_string($conn, $ingredient['cat11A']);
			$cat11B = mysqli_real_escape_string($conn, $ingredient['cat11B']);
			$cat12 = mysqli_real_escape_string($conn, $ingredient['cat12']);
			$profile = mysqli_real_escape_string($conn, $ingredient['profile']);
			$physical_state = mysqli_real_escape_string($conn, $ingredient['physical_state']);
			$allergen = mysqli_real_escape_string($conn, $ingredient['allergen']);
			$odor = mysqli_real_escape_string($conn, $ingredient['odor']);
			$impact_top = mysqli_real_escape_string($conn, $ingredient['impact_top']);
			$impact_heart = mysqli_real_escape_string($conn, $ingredient['impact_heart']);
			$impact_base = mysqli_real_escape_string($conn, $ingredient['impact_base']);
			$created = mysqli_real_escape_string($conn, $ingredient['created']);
			$usage_type = mysqli_real_escape_string($conn, $ingredient['usage_type']);
			$noUsageLimit = mysqli_real_escape_string($conn, $ingredient['noUsageLimit']);
			$byPassIFRA = mysqli_real_escape_string($conn, $ingredient['byPassIFRA']);
			$isPrivate = mysqli_real_escape_string($conn, $ingredient['isPrivate']);
			$molecularWeight = mysqli_real_escape_string($conn, $ingredient['molecularWeight']);
		
			$sql = "INSERT IGNORE INTO ingredients(id,name,INCI,cas,FEMA,type,strength,category,purity,einecs,reach,tenacity,chemical_name,formula,flash_point,notes,flavor_use,soluble,logp,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12,profile,physical_state,allergen,odor,impact_top,impact_heart,impact_base,created,usage_type,noUsageLimit,byPassIFRA,isPrivate,molecularWeight) 
					VALUES ('$id','$name','$INCI','$cas','$FEMA','$type','$strength','$category','$purity','$einecs','$reach','$tenacity','$chemical_name','$formula','$flash_point','$notes','$flavor_use','$soluble','$logp','$cat1','$cat2','$cat3','$cat4','$cat5A','$cat5B','$cat5C','$cat6','$cat7A','$cat7B','$cat8','$cat9','$cat10A','$cat10B','$cat11A','$cat11B','$cat12','$profile','$physical_state','$allergen','$odor','$impact_top','$impact_heart','$impact_base','$created','$usage_type','$noUsageLimit','$byPassIFRA','$isPrivate','$molecularWeight')";
		
			if (mysqli_query($conn, $sql)) {
				$result['success'] = "Import complete";
			
				if ($warn) {
					$result['warning'] = $warn;  // Set warning message if $err is not empty
				}
			
				unlink($target_path);
			} else {
				$result['error'] = "There was an error importing your JSON file: " . mysqli_error($conn);
				echo json_encode($result);
				return;
			}

		}

		
		
		
	} else {
		$result['error'] = "There was an error processing json file $target_path, please try again!";
		echo json_encode($result);

	}
	echo json_encode($result);
	return;

}


if($_GET['action'] == 'restoreIFRA'){
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0777, true);
	}
	
	if (!is_writable($tmp_path)) {
		$result['error'] = "Upload directory not writable. Make sure you have write permissions.";
		echo json_encode($result);
		return;
	}
	
	$target_path = $tmp_path.basename($_FILES['backupFile']['name']); 

	if(move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    	$data = json_decode(file_get_contents($target_path), true);
		if(!$data['IFRALibrary']){
			$result['error'] = "JSON File seems invalid. Please make sure you importing the right file";
			echo json_encode($result);
			return;
		}
		mysqli_query($conn, "TRUNCATE IFRALibrary");
		
		foreach ($data['IFRALibrary'] as $d ){				
		$ifra_key = mysqli_real_escape_string($conn, $d['ifra_key']);
		$image = mysqli_real_escape_string($conn, $d['image']);
		$amendment = mysqli_real_escape_string($conn, $d['amendment']);
		$prev_pub = mysqli_real_escape_string($conn, $d['prev_pub']);
		$last_pub = mysqli_real_escape_string($conn, $d['last_pub']);
		$deadline_existing = mysqli_real_escape_string($conn, $d['deadline_existing']);
		$deadline_new = mysqli_real_escape_string($conn, $d['deadline_new']);
		$name = mysqli_real_escape_string($conn, $d['name']);
		$cas = mysqli_real_escape_string($conn, $d['cas']);
		$cas_comment = mysqli_real_escape_string($conn, $d['cas_comment']);
		$synonyms = mysqli_real_escape_string($conn, $d['synonyms']);
		$formula = mysqli_real_escape_string($conn, $d['formula']);
		$flavor_use = mysqli_real_escape_string($conn, $d['flavor_use']);
		$prohibited_notes = mysqli_real_escape_string($conn, $d['prohibited_notes']);
		$restricted_photo_notes = mysqli_real_escape_string($conn, $d['restricted_photo_notes']);
		$restricted_notes = mysqli_real_escape_string($conn, $d['restricted_notes']);
		$specified_notes = mysqli_real_escape_string($conn, $d['specified_notes']);
		$type = mysqli_real_escape_string($conn, $d['type']);
		$risk = mysqli_real_escape_string($conn, $d['risk']);
		$contrib_others = mysqli_real_escape_string($conn, $d['contrib_others']);
		$contrib_others_notes = mysqli_real_escape_string($conn, $d['contrib_others_notes']);
	
		$cat1 = isset($d['cat1']) && $d['cat1'] !== '' ? floatval($d['cat1']) : 100;
		$cat2 = isset($d['cat2']) && $d['cat2'] !== '' ? floatval($d['cat2']) : 100;
		$cat3 = isset($d['cat3']) && $d['cat3'] !== '' ? floatval($d['cat3']) : 100;
		$cat4 = isset($d['cat4']) && $d['cat4'] !== '' ? floatval($d['cat4']) : 100;
		$cat5A = isset($d['cat5A']) && $d['cat5A'] !== '' ? floatval($d['cat5A']) : 100;
		$cat5B = isset($d['cat5B']) && $d['cat5B'] !== '' ? floatval($d['cat5B']) : 100;
		$cat5C = isset($d['cat5C']) && $d['cat5C'] !== '' ? floatval($d['cat5C']) : 100;
		$cat5D = isset($d['cat5D']) && $d['cat5D'] !== '' ? floatval($d['cat5D']) : 100;
		$cat6 = isset($d['cat6']) && $d['cat6'] !== '' ? floatval($d['cat6']) : 100;
		$cat7A = isset($d['cat7A']) && $d['cat7A'] !== '' ? floatval($d['cat7A']) : 100;
		$cat7B = isset($d['cat7B']) && $d['cat7B'] !== '' ? floatval($d['cat7B']) : 100;
		$cat8 = isset($d['cat8']) && $d['cat8'] !== '' ? floatval($d['cat8']) : 100;
		$cat9 = isset($d['cat9']) && $d['cat9'] !== '' ? floatval($d['cat9']) : 100;
		$cat10A = isset($d['cat10A']) && $d['cat10A'] !== '' ? floatval($d['cat10A']) : 100;
		$cat10B = isset($d['cat10B']) && $d['cat10B'] !== '' ? floatval($d['cat10B']) : 100;
		$cat11A = isset($d['cat11A']) && $d['cat11A'] !== '' ? floatval($d['cat11A']) : 100;
		$cat11B = isset($d['cat11B']) && $d['cat11B'] !== '' ? floatval($d['cat11B']) : 100;
		$cat12 = isset($d['cat12']) && $d['cat12'] !== '' ? floatval($d['cat12']) : 100;
	
		$s = mysqli_query($conn, "
			INSERT INTO `IFRALibrary` (
				`ifra_key`, `image`, `amendment`, `prev_pub`, `last_pub`, 
				`deadline_existing`, `deadline_new`, `name`, `cas`, `cas_comment`, 
				`synonyms`, `formula`, `flavor_use`, `prohibited_notes`, `restricted_photo_notes`, 
				`restricted_notes`, `specified_notes`, `type`, `risk`, `contrib_others`, 
				`contrib_others_notes`, `cat1`, `cat2`, `cat3`, `cat4`, `cat5A`, 
				`cat5B`, `cat5C`, `cat5D`, `cat6`, `cat7A`, `cat7B`, `cat8`, `cat9`, 
				`cat10A`, `cat10B`, `cat11A`, `cat11B`, `cat12`
			) VALUES (
				'$ifra_key', '$image', '$amendment', '$prev_pub', '$last_pub', 
				'$deadline_existing', '$deadline_new', '$name', '$cas', '$cas_comment', 
				'$synonyms', '$formula', '$flavor_use', '$prohibited_notes', '$restricted_photo_notes', 
				'$restricted_notes', '$specified_notes', '$type', '$risk', '$contrib_others', 
				'$contrib_others_notes', $cat1, $cat2, $cat3, $cat4, $cat5A, 
				$cat5B, $cat5C, $cat5D, $cat6, $cat7A, $cat7B, $cat8, $cat9, 
				$cat10A, $cat10B, $cat11A, $cat11B, $cat12
			)
		");
	}

				
		if($s){
			$result['success'] = "Import complete";
			unlink($target_path);
		}else{
			$result['error'] = "There was an error importing your JSON file ".mysqli_error($conn);
			echo json_encode($result);
			return;
		}
			
	} else {
		$result['error'] = "There was an error processing json file $target_path, please try again!";
		echo json_encode($result);

	}
	echo json_encode($result);
	return;

}

//IMPORT SUPPLIERS
if ($_GET['action'] == 'importSuppliers') {
    if (!file_exists($tmp_path)) {
        mkdir($tmp_path, 0777, true);
    }

    if (!is_writable($tmp_path)) {
        $result['error'] = "Upload directory not writable. Make sure you have write permissions.";
        echo json_encode($result);
        return;
    }

    $target_path = $tmp_path . basename($_FILES['jsonFile']['name']); 

    if (move_uploaded_file($_FILES['jsonFile']['tmp_name'], $target_path)) {
        $data = json_decode(file_get_contents($target_path), true);
        if (!$data['inventory_suppliers']) {
            $result['error'] = "JSON File seems invalid. Please make sure you are importing the right file";
            echo json_encode($result);
            return;
        }

        foreach ($data['inventory_suppliers'] as $d) {				
			$s = mysqli_query($conn, "
				INSERT INTO `ingSuppliers` 
				(`name`, `address`, `po`, `country`, `telephone`, `url`, `email`, `platform`, `price_tag_start`, `price_tag_end`, `add_costs`, `price_per_size`, `notes`, `min_ml`, `min_gr`) 
				VALUES 
				('" . $d['name'] . "', '" . $d['address'] . "', '" . $d['po'] . "', '" . $d['country'] . "', '" . $d['telephone'] . "', '" . $d['url'] . "', '" . $d['email'] . "', '" . $d['platform'] . "', '" . $d['price_tag_start'] . "', '" . $d['price_tag_end'] . "', '" . $d['add_costs'] . "', '" . $d['price_per_size'] . "', '" . $d['notes'] . "', '" . $d['min_ml'] . "', '" . $d['min_gr'] . "')
				ON DUPLICATE KEY UPDATE
				`address` = VALUES(`address`), 
				`po` = VALUES(`po`), 
				`country` = VALUES(`country`), 
				`telephone` = VALUES(`telephone`), 
				`url` = VALUES(`url`), 
				`email` = VALUES(`email`), 
				`platform` = VALUES(`platform`), 
				`price_tag_start` = VALUES(`price_tag_start`), 
				`price_tag_end` = VALUES(`price_tag_end`), 
				`add_costs` = VALUES(`add_costs`), 
				`price_per_size` = VALUES(`price_per_size`), 
				`notes` = VALUES(`notes`), 
				`min_ml` = VALUES(`min_ml`), 
				`min_gr` = VALUES(`min_gr`)
			");
        }

        if ($s) {
            $result['success'] = "Import complete";
            unlink($target_path);
        } else {
            $result['error'] = "There was an error importing your JSON file " . mysqli_error($conn);
            echo json_encode($result);
            return;
        }
    } else {
        $result['error'] = "There was an error processing the JSON file $target_path, please try again!";
        echo json_encode($result);
    }

    echo json_encode($result);
    return;
}

//IMPORT CUSTOMERS
if ($_GET['action'] == 'importCustomers') {
    if (!file_exists($tmp_path)) {
        mkdir($tmp_path, 0777, true);
    }

    if (!is_writable($tmp_path)) {
        $result['error'] = "Upload directory not writable. Make sure you have write permissions.";
        echo json_encode($result);
        return;
    }

    $target_path = $tmp_path . basename($_FILES['jsonFile']['name']); 

    if (move_uploaded_file($_FILES['jsonFile']['tmp_name'], $target_path)) {
        $data = json_decode(file_get_contents($target_path), true);
        if (!$data['inventory_customers']) {
            $result['error'] = "JSON File seems invalid. Please make sure you are importing the right file";
            echo json_encode($result);
            return;
        }

        foreach ($data['inventory_customers'] as $d) {				
			$s = mysqli_query($conn, "
				INSERT INTO `customers` 
				(`name`, `address`, `email`, `phone`, `web`, `owner_id`) 
				VALUES 
				('" . $d['name'] . "', '" . $d['address'] . "', '" . $d['email'] . "', '" . $d['phone'] . "', '" . $d['web'] . "', '" . $d['owner_id'] . "')
				ON DUPLICATE KEY UPDATE
				`address` = VALUES(`address`), 
				`email` = VALUES(`email`), 
				`phone` = VALUES(`phone`), 
				`web` = VALUES(`web`), 
				`owner_id` = VALUES(`owner_id`)
			");
        }

        if ($s) {
            $result['success'] = "Import complete";
            unlink($target_path);
        } else {
            $result['error'] = "There was an error importing your JSON file " . mysqli_error($conn);
            echo json_encode($result);
            return;
        }
    } else {
        $result['error'] = "There was an error processing the JSON file $target_path, please try again!";
        echo json_encode($result);
    }

    echo json_encode($result);
    return;
}

//IMPORT BOTTLES
if ($_GET['action'] == 'importBottles') {
    if (!file_exists($tmp_path)) {
        mkdir($tmp_path, 0777, true);
    }

    if (!is_writable($tmp_path)) {
        $result['error'] = "Upload directory not writable. Make sure you have write permissions.";
        echo json_encode($result);
        return;
    }

    $target_path = $tmp_path . basename($_FILES['jsonFile']['name']); 

    if (move_uploaded_file($_FILES['jsonFile']['tmp_name'], $target_path)) {
        $data = json_decode(file_get_contents($target_path), true);
        if (!$data['inventory_bottles']) {
            $result['error'] = "JSON File seems invalid. Please make sure you are importing the right file";
            echo json_encode($result);
            return;
        }

        foreach ($data['inventory_bottles'] as $d) {				
			$s = mysqli_query($conn, "
				INSERT INTO `bottles` 
				(`name`, `ml`, `price`, `height`, `width`, `diameter`, `weight`, `supplier`, `supplier_link`, `notes`, `pieces`) 
				VALUES 
				('" . $d['name'] . "', '" . $d['ml'] . "', '" . $d['price'] . "', '" . $d['height'] . "', '" . $d['width'] . "', '" . $d['diameter'] . "', '" . $d['weight'] . "', '" . $d['supplier'] . "', '" . $d['supplier_link'] . "', '" . $d['notes'] . "', '" . $d['pieces'] . "')
				ON DUPLICATE KEY UPDATE
				`ml` = VALUES(`ml`), 
				`price` = VALUES(`price`), 
				`height` = VALUES(`height`), 
				`width` = VALUES(`width`), 
				`diameter` = VALUES(`diameter`), 
				`weight` = VALUES(`weight`), 
				`supplier` = VALUES(`supplier`), 
				`supplier_link` = VALUES(`supplier_link`), 
				`notes` = VALUES(`notes`), 
				`pieces` = VALUES(`pieces`)
			");
        }

        if ($s) {
            $result['success'] = "Import complete";
            unlink($target_path);
        } else {
            $result['error'] = "There was an error importing your JSON file " . mysqli_error($conn);
            echo json_encode($result);
            return;
        }
    } else {
        $result['error'] = "There was an error processing the JSON file $target_path, please try again!";
        echo json_encode($result);
    }

    echo json_encode($result);
    return;
}

//IMPORT ACCESSORIES
if ($_GET['action'] == 'importAccessories') {
    if (!file_exists($tmp_path)) {
        mkdir($tmp_path, 0777, true);
    }

    if (!is_writable($tmp_path)) {
        $result['error'] = "Upload directory not writable. Make sure you have write permissions.";
        echo json_encode($result);
        return;
    }

    $target_path = $tmp_path . basename($_FILES['jsonFile']['name']); 

    if (move_uploaded_file($_FILES['jsonFile']['tmp_name'], $target_path)) {
        $data = json_decode(file_get_contents($target_path), true);
        if (!$data['inventory_accessories']) {
            $result['error'] = "JSON File seems invalid. Please make sure you are importing the right file";
            echo json_encode($result);
            return;
        }

        foreach ($data['inventory_accessories'] as $d) {				
            $s = mysqli_query($conn, "
                INSERT INTO `inventory_accessories` 
                (`name`, `accessory`, `price`, `supplier`, `supplier_link`, `pieces`) 
                VALUES 
                ('" . $d['name'] . "', '" . $d['accessory'] . "', '" . $d['price'] . "', '" . $d['supplier'] . "', '" . $d['supplier_link'] . "', '" . $d['pieces'] . "')
                ON DUPLICATE KEY UPDATE
                `accessory` = VALUES(`accessory`), 
                `price` = VALUES(`price`), 
                `supplier` = VALUES(`supplier`), 
                `supplier_link` = VALUES(`supplier_link`), 
                `pieces` = VALUES(`pieces`)
            ");
        }

        if ($s) {
            $result['success'] = "Import complete";
            unlink($target_path);
        } else {
            $result['error'] = "There was an error importing your JSON file " . mysqli_error($conn);
            echo json_encode($result);
            return;
        }
    } else {
        $result['error'] = "There was an error processing the JSON file $target_path, please try again!";
        echo json_encode($result);
    }

    echo json_encode($result);
    return;
}


//IMPORT COMPOUNDS
if ($_GET['action'] == 'importCompounds') {
    if (!file_exists($tmp_path)) {
        mkdir($tmp_path, 0777, true);
    }

    if (!is_writable($tmp_path)) {
        $result['error'] = "Upload directory not writable. Make sure you have write permissions.";
        echo json_encode($result);
        return;
    }

    $target_path = $tmp_path . basename($_FILES['jsonFile']['name']); 

    if (move_uploaded_file($_FILES['jsonFile']['tmp_name'], $target_path)) {
        $data = json_decode(file_get_contents($target_path), true);
        if (!$data['inventory_compounds']) {
            $result['error'] = "JSON File seems invalid. Please make sure you are importing the right file";
            echo json_encode($result);
            return;
        }

        foreach ($data['inventory_compounds'] as $d) {				
            $s = mysqli_query($conn, "
                INSERT INTO `inventory_compounds` 
                (`name`, `description`, `batch_id`, `size`, `updated`, `created`, `location`, `label_info`) 
                VALUES 
                ('" . $d['name'] . "', '" . $d['description'] . "', '" . $d['batch_id'] . "', '" . $d['size'] . "', '" . $d['updated'] . "', '" . $d['created'] . "', '" . $d['location'] . "', '" . $d['label_info'] . "')
                ON DUPLICATE KEY UPDATE
                `description` = VALUES(`description`), 
                `batch_id` = VALUES(`batch_id`), 
                `size` = VALUES(`size`), 
                `updated` = VALUES(`updated`), 
                `created` = VALUES(`created`), 
                `location` = VALUES(`location`), 
                `label_info` = VALUES(`label_info`)
            ");
        }

        if ($s) {
            $result['success'] = "Import complete";
            unlink($target_path);
        } else {
            $result['error'] = "There was an error importing your JSON file " . mysqli_error($conn);
            echo json_encode($result);
            return;
        }
    } else {
        $result['error'] = "There was an error processing the JSON file $target_path, please try again!";
        echo json_encode($result);
    }

    echo json_encode($result);
    return;
}


// IMPORT CATEGORIES
if ($_GET['action'] == 'importCategories') {
    if (!file_exists($tmp_path)) {
        mkdir($tmp_path, 0777, true);
    }

    if (!is_writable($tmp_path)) {
        $result['error'] = "Upload directory not writable. Make sure you have write permissions.";
        echo json_encode($result);
        return;
    }

    $target_path = $tmp_path . basename($_FILES['jsonFile']['name']);

    if (move_uploaded_file($_FILES['jsonFile']['tmp_name'], $target_path)) {
        $data = json_decode(file_get_contents($target_path), true);

        if (!$data['ingCategory'] && !$data['formulaCategories'] && !$data['ingProfiles']) {
            $result['error'] = "JSON File seems invalid. Please make sure you are importing the right file";
            echo json_encode($result);
            return;
        }

        $conn->autocommit(FALSE); // Turn off auto-commit for transaction

        $success = true;

        if ($data['ingCategory']) {
            $stmt = $conn->prepare("INSERT INTO `ingCategory` (`name`, `notes`, `image`, `colorKey`) VALUES (?, ?, ?, ?)");
            foreach ($data['ingCategory'] as $d) {
                $stmt->bind_param("ssss", $d['name'], $d['notes'], $d['image'], $d['colorKey']);
                if (!$stmt->execute()) {
                    $success = false;
                    $result['error'] = "Error inserting into ingCategory: " . $stmt->error;
                    break;
                }
            }
            $stmt->close();
        }

        if ($data['formulaCategories']) {
            $stmt = $conn->prepare("INSERT INTO `formulaCategories` (`name`, `cname`, `type`, `colorKey`) VALUES (?, ?, ?, ?)");
            foreach ($data['formulaCategories'] as $d) {
                $stmt->bind_param("ssss", $d['name'], $d['cname'], $d['type'], $d['colorKey']);
                if (!$stmt->execute()) {
                    $success = false;
                    $result['error'] = "Error inserting into formulaCategories: " . $stmt->error;
                    break;
                }
            }
            $stmt->close();
        }
		
		if ($data['ingProfiles']) {
            $stmt = $conn->prepare("INSERT INTO `ingProfiles` (`name`, `notes`, `image`) VALUES (?, ?, ?)");
            foreach ($data['ingProfiles'] as $d) {
                $stmt->bind_param("sss", $d['name'], $d['notes'], $d['image']);
                if (!$stmt->execute()) {
                    $success = false;
                    $result['error'] = "Error inserting into ingProfiles: " . $stmt->error;
                    break;
                }
            }
            $stmt->close();
        }

        if ($success) {
            $conn->commit(); // Commit the transaction
            $result['success'] = "Import complete";
            unlink($target_path);
        } else {
            $conn->rollback(); // Rollback the transaction on error
            echo json_encode($result);
            return;
        }

        $conn->autocommit(TRUE); // Turn auto-commit back on
    } else {
        $result['error'] = "There was an error processing json file $target_path, please try again!";
    }

    echo json_encode($result);
    return;
}


// IMPORT MAKING
if ($_GET['action'] == 'importMaking') {
    if (!file_exists($tmp_path)) {
        mkdir($tmp_path, 0777, true);
    }

    if (!is_writable($tmp_path)) {
        $result['error'] = "Upload directory not writable. Make sure you have write permissions.";
        echo json_encode($result);
        return;
    }

    $target_path = $tmp_path . basename($_FILES['jsonFile']['name']);

    if (move_uploaded_file($_FILES['jsonFile']['tmp_name'], $target_path)) {
        $data = json_decode(file_get_contents($target_path), true);

        if (!$data || !isset($data['makeFormula'])) {
            $result['error'] = "JSON File seems invalid. Please make sure you are importing the right file";
            echo json_encode($result);
            return;
        }

        $conn->autocommit(FALSE); // Turn off auto-commit for transaction

        $success = true;

        if (!empty($data['makeFormula'])) {
            $stmt = $conn->prepare("INSERT INTO `makeFormula` (`fid`, `name`, `ingredient`, `ingredient_id`, `replacement_id`, `concentration`, `dilutant`, `quantity`, `overdose`, `originalQuantity`, `notes`, `skip`, `toAdd`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($data['makeFormula'] as $d) {
                $stmt->bind_param("sssssssssssss", $d['fid'], $d['name'], $d['ingredient'], $d['ingredient_id'], $d['replacement_id'], $d['concentration'], $d['dilutant'], $d['quantity'], $d['overdose'], $d['originalQuantity'], $d['notes'], $d['skip'], $d['toAdd']);
                if (!$stmt->execute()) {
                    $success = false;
                    $result['error'] = "Error inserting into makeFormula: " . $stmt->error;
                    break;
                }
            }
            $stmt->close();
        }

        if ($success) {
			//CREATE A FORMULA ENTRY
			$stmtMeta = $conn->prepare("INSERT INTO formulasMetaData (name, fid, todo) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name), todo=VALUES(todo)");

            $todo = 1;
            $stmtMeta->bind_param("ssi", $data['makeFormula'][0]['name'], $data['makeFormula'][0]['fid'], $todo);
            if (!$stmtMeta->execute()) {
                $success = false;
                $result['error'] = "Error inserting into formulasMetaData " . $stmtMeta->error;
            }
            $stmtMeta->close();
        }
/*
		if ($success) {
            $stmtFormula = $conn->prepare("INSERT IGNORE INTO formulas (name, fid, ingredient, ingredient_id, concentration, dilutant, quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($data['makeFormula'] as $d) {
                $stmtFormula->bind_param("sssssss", $d['name'], $d['fid'], $d['ingredient'], $d['ingredient_id'], $d['concentration'], $d['dilutant'], $d['quantity']);
                if (!$stmtFormula->execute()) {
                    $success = false;
                    $result['error'] = "Error inserting into formulas: " . $stmtFormula->error;
                    break;
                }
            }
            $stmtFormula->close();
        }
*/
        if (!empty($data['makeFormula'])) {
            $stmt = $conn->prepare("INSERT INTO `makeFormula` (`fid`, `name`, `ingredient`, `ingredient_id`, `replacement_id`, `concentration`, `dilutant`, `quantity`, `overdose`, `originalQuantity`, `notes`, `skip`, `toAdd`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($data['makeFormula'] as $d) {
                // Fetch ingredient_id
                $stmtIngredient = $conn->prepare("SELECT id FROM `ingredients` WHERE name = ?");
                $stmtIngredient->bind_param("s", $d['ingredient_id']);
                $stmtIngredient->execute();
                $stmtIngredient->bind_result($ingredient_id);
                $stmtIngredient->fetch();
                $stmtIngredient->close();

                // If ingredient not found, insert it and fetch the new id
                if (empty($ingredient_id)) {
                    $stmtInsertIngredient = $conn->prepare("INSERT INTO `ingredients` (name) VALUES (?)");
                    $stmtInsertIngredient->bind_param("s", $d['ingredient']);
                    if (!$stmtInsertIngredient->execute()) {
                        $success = false;
                        $result['error'] = "Error inserting into ingredients: " . $stmtInsertIngredient->error;
                        break;
                    }
                    $ingredient_id = $stmtInsertIngredient->insert_id;
                    $stmtInsertIngredient->close();
                }

                // Insert into makeFormula
                $stmt->bind_param("sssssssssssss", $d['fid'], $d['name'], $d['ingredient'], $ingredient_id, $d['replacement_id'], $d['concentration'], $d['dilutant'], $d['quantity'], $d['overdose'], $d['originalQuantity'], $d['notes'], $d['skip'], $d['toAdd']);
                if (!$stmt->execute()) {
                    $success = false;
                    $result['error'] = "Error inserting into makeFormula: " . $stmt->error;
                    break;
                }
            }
            $stmt->close();
        }
		if ($success) {
            // Insert ignore logic for `formulas`
            $stmtFormula = $conn->prepare("INSERT IGNORE INTO formulas (name, fid, ingredient, ingredient_id, concentration, dilutant, quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($data['makeFormula'] as $d) {
                // Fetch ingredient_id again in case it was updated during the loop
                $stmtIngredient = $conn->prepare("SELECT id FROM `ingredients` WHERE name = ?");
                $stmtIngredient->bind_param("s", $d['ingredient']);
                $stmtIngredient->execute();
                $stmtIngredient->bind_result($ingredient_id);
                $stmtIngredient->fetch();
                $stmtIngredient->close();

                $stmtFormula->bind_param("sssssss", $d['name'], $d['fid'], $d['ingredient'], $ingredient_id, $d['concentration'], $d['dilutant'], $d['quantity']);
                if (!$stmtFormula->execute()) {
                    $success = false;
                    $result['error'] = "Error inserting into formulas: " . $stmtFormula->error;
                    break;
                }
            }
            $stmtFormula->close();
        }

        if ($success) {
            $conn->commit(); // Commit the transaction
            $result['success'] = "Import complete";
            unlink($target_path);
        } else {
            $conn->rollback(); // Rollback the transaction on error
        }

        $conn->autocommit(TRUE); // Turn auto-commit back on
    } else {
        $result['error'] = "There was an error processing the JSON file $target_path, please try again!";
    }

    echo json_encode($result);
    return;
}


//EXPORT INGREDIENT CATEGORIES
if($_GET['action'] == 'exportIngCat'){
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingCategory")))){
		$msg['error'] = 'No data found to export.';
		echo json_encode($msg);
		return;
	}
	$data = 0;
	$q = mysqli_query($conn, "SELECT * FROM ingCategory");
	while($resData = mysqli_fetch_assoc($q)){
		
		$r['id'] = (int)$resData['id'];
		$r['name'] = (string)$resData['name']?: "-";
		$r['notes'] = (string)$resData['notes']?: "-";
		$r['image'] = (string)$resData['image'] ?: "-";
		$r['colorKey'] = (string)$resData['colorKey']?: "-";
		
		$data++;
		$cat[] = $r;
	}
	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	$vd['ingCategory'] = $data;
	$vd['timestamp'] = date('d/m/Y H:i:s');

	
	$result['ingCategory'] = $cat;
	$result['pvMeta'] = $vd;
	
	header('Content-disposition: attachment; filename=IngCategories.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;
}

//EXPORT FORMULA CATEGORIES
if($_GET['action'] == 'exportFrmCat'){
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulaCategories")))){
		$msg['error'] = 'No data found to export.';
		echo json_encode($msg);
		return;
	}
	$data = 0;
	$q = mysqli_query($conn, "SELECT * FROM formulaCategories");
	while($resData = mysqli_fetch_assoc($q)){
		
		$r['id'] = (int)$resData['id'];
		$r['name'] = (string)$resData['name']?: "-";
		$r['cname'] = (string)$resData['cname']?: "-";
		$r['type'] = (string)$resData['type'] ?: "-";
		$r['colorKey'] = (string)$resData['colorKey']?: "-";
		
		$data++;
		$cat[] = $r;
	}
	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	$vd['formulaCategories'] = $data;
	$vd['timestamp'] = date('d/m/Y H:i:s');

	
	$result['formulaCategories'] = $cat;
	$result['pvMeta'] = $vd;
	
	header('Content-disposition: attachment; filename=FormulaCategories.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;
}

//EXPORT PERFUME TYPES
if($_GET['action'] == 'exportPerfTypes'){
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM perfumeTypes")))){
		$msg['error'] = 'No data found to export.';
		echo json_encode($msg);
		return;
	}
	$data = 0;
	$q = mysqli_query($conn, "SELECT * FROM perfumeTypes");
	while($resData = mysqli_fetch_assoc($q)){
		
		$r['id'] = (int)$resData['id'];
		$r['name'] = (string)$resData['name']?: "-";
		$r['concentration'] = (int)$resData['concentration']?: 100;
		$r['description'] = (string)$resData['description'] ?: "-";
		
		$data++;
		$cat[] = $r;
	}
	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	$vd['perfumeTypes'] = $data;
	$vd['timestamp'] = date('d/m/Y H:i:s');

	
	$result['perfumeTypes'] = $cat;
	$result['pvMeta'] = $vd;
	
	header('Content-disposition: attachment; filename=PerfumeTypes.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;
}



//EXPORT MAKING FORMULA
if($_GET['action'] == 'exportMaking'){
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula")))){
		$msg['error'] = 'No data found to export.';
		echo json_encode($msg);
		return;
	}
	$data = 0;
	if($fid = $_GET['fid']){
		 
		$filter = " WHERE fid = '$fid' ";	
	}
	
	$q = mysqli_query($conn, "SELECT * FROM makeFormula $filter");
	while($resData = mysqli_fetch_assoc($q)){
		
		$r['id'] = (int)$resData['id'];
		$r['fid'] = (string)$resData['fid'];
		$r['name'] = (string)$resData['name'];
		$r['ingredient'] = (string)$resData['ingredient'];
		$r['ingredient_id'] = (int)$resData['ingredient_id'];
		$r['replacement_id'] = (int)$resData['replacement_id'];		
		$r['concentration'] = (double)$resData['concentration'];
		$r['dilutant'] = (string)$resData['dilutant'];
		$r['quantity'] = (double)$resData['quantity'];
		$r['overdose'] = (double)$resData['overdose'];
		$r['originalQuantity'] = (double)$resData['originalQuantity'];
		$r['notes'] = (string)$resData['notes'];
		$r['skip'] = (int)$resData['skip'];
		$r['toAdd'] = (int)$resData['toAdd'];

		$data++;
		$dat_arr[] = $r;
	}
	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	$vd['makeFormula'] = $data;
	$vd['timestamp'] = date('d/m/Y H:i:s');

	
	$result['makeFormula'] = $dat_arr;
	$result['pvMeta'] = $vd;
	
	header('Content-disposition: attachment; filename=MakeFormula.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;
}

//EXPORT INGREDIENT PROFILES
if($_GET['action'] == 'exportIngProf'){
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingProfiles")))){
		$msg['error'] = 'No data found to export.';
		echo json_encode($msg);
		return;
	}
	$data = 0;
	$q = mysqli_query($conn, "SELECT * FROM ingProfiles");
	while($resData = mysqli_fetch_assoc($q)){
		
		$r['id'] = (int)$resData['id'];
		$r['name'] = (string)$resData['name']?: "-";
		$r['notes'] = (string)$resData['notes']?: "-";
		$r['image'] = (string)$resData['image'] ?: "-";
		
		$data++;
		$cat[] = $r;
	}
	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	$vd['ingCategory'] = $data;
	$vd['timestamp'] = date('d/m/Y H:i:s');

	
	$result['ingProfiles'] = $cat;
	$result['pvMeta'] = $vd;
	
	header('Content-disposition: attachment; filename=IngProfiles.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;
}


if($_POST['do'] == 'tagadd' && $_POST['fid'] && $_POST['tag']){
	if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM formulasTags WHERE formula_id='".$_POST['fid']."' AND tag_name = '".$_POST['tag']."'"))){
		$response[] = '';
		echo json_encode($response);
		return;
	}
	mysqli_query($conn,"INSERT INTO formulasTags (formula_id,tag_name) VALUES('".$_POST['fid']."','".$_POST['tag']."')" );
	$response[] = '';
	echo json_encode($response);
	return;
}

if($_POST['do'] == 'tagremove' && $_POST['fid'] && $_POST['tag']){
	mysqli_query($conn,"DELETE FROM formulasTags WHERE formula_id='".$_POST['fid']."' AND tag_name = '".$_POST['tag']."'" );
	$response[] = '';
	echo json_encode($response);
	return;
}

if($_POST['update_rating'] == '1' && $_POST['fid'] && is_numeric($_POST['score'])){
	mysqli_query($conn,"UPDATE formulasMetaData SET rating = '".$_POST['score']."' WHERE id = '".$_POST['fid']."'");
}

//EXCLUDE/INCLUDE INGREDIENT
if($_POST['action'] == 'excIng' && $_POST['ingID']){
	$id = mysqli_real_escape_string($conn, $_POST['ingID']);
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$ing = mysqli_real_escape_string($conn, $_POST['ingName']);

	$status = (int)$_POST['status'];
	if($status == 1){
		$st = 'excluded from calclulations';
	}else{
		$st = 'included in calculations';
	}
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '$fid'"));
	if($meta['isProtected'] == FALSE){
		if(mysqli_query($conn, "UPDATE formulas SET exclude_from_calculation = '$status' WHERE id  = '$id'")){
			$response['success'] = $ing.' is now '. $st;
		}else{
			$response['error'] = $ing.' cannot be '.$st.' from the formula!';
		}
	}
	
	echo json_encode($response);
	return;
}

//IS MADE
if($_POST['isMade'] && $_POST['fid']){
	$fid = mysqli_real_escape_string($conn,$_POST['fid']);
	
	$quant = mysqli_query($conn, "SELECT ingredient,quantity FROM formulas WHERE fid = '$fid'");
	while($get_quant = mysqli_fetch_array($quant)){
		$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '".$get_quant['ingredient']."'"));
		$q = "UPDATE suppliers SET stock = GREATEST(0, stock - '".$get_quant['quantity']."') WHERE ingID = '".$ing['id']."' AND stock = GREATEST(stock, '".$get_quant['quantity']."')";
		$upd = mysqli_query($conn, $q);	
		
	}
	if($upd){
		mysqli_query($conn,"UPDATE formulasMetaData SET isMade = '1', madeOn = NOW() WHERE fid = '$fid'");
		$response['success'] = 'Inventory updated';
	}else{
		$response['error'] = mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}


//CREATE ACCORD
if($_POST['accordName'] && $_POST['accordProfile'] && $_POST['fid']){
	require_once(__ROOT__.'/func/genFID.php');

	$fid = mysqli_real_escape_string($conn,$_POST['fid']);
	$accordProfile = mysqli_real_escape_string($conn,$_POST['accordProfile']);
	$accordName = mysqli_real_escape_string($conn,$_POST['accordName']);
	$nfid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');
	
	if(mysqli_num_rows(mysqli_query($conn,"SELECT name FROM formulasMetaData WHERE name = '$accordName'"))){
		$response['error'] = 'A formula with name <strong>'.$accordName.'</strong> already exists, please choose a different name!';
		echo json_encode($response);
		return;
	}
									
	$get_formula = mysqli_query($conn,"SELECT ingredient FROM formulas WHERE fid = '$fid'");
	while($formula = mysqli_fetch_array($get_formula)){
        if($i = mysqli_fetch_array(mysqli_query($conn,"SELECT name,profile FROM ingredients WHERE profile = '$accordProfile' AND name ='".$formula['ingredient']."'"))){
        	mysqli_query($conn, "INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes) SELECT '$nfid', '$accordName', ingredient, ingredient_id, concentration, dilutant, quantity, notes FROM formulas WHERE fid = '$fid' AND ingredient = '".$i['name']."'");
		}
	}
	if(mysqli_query($conn,"INSERT INTO formulasMetaData (fid,name) VALUES ('$nfid','$accordName')")){
		$response['success'] =  'Accord <a href="/?do=Formula&id='.mysqli_insert_id($conn).'" target="_blank">'.$accordName.'</a> created!';
	}
	echo json_encode($response);
	return;
}

//RESTORE REVISION
if($_GET['restore'] == 'rev' && $_GET['revision'] && $_GET['fid']){
	$fid = mysqli_real_escape_string($conn,$_GET['fid']);
	$revision = $_GET['revision'];
	
	mysqli_query($conn, "DELETE FROM formulas WHERE fid = '$fid'");
	if(mysqli_query($conn, "INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes) SELECT fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes FROM formulasRevisions WHERE fid = '$fid' AND revision = '$revision'")){
		mysqli_query($conn, "UPDATE formulasMetaData SET revision = '$revision' WHERE fid = '$fid'");
		$response['success'] = 'Formula revision restored!';
	}else{
		$response['error'] = 'Unable to restore revision! '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//DELETE REVISION
if($_GET['delete'] == 'rev' && $_GET['revision'] && $_GET['fid']){
	$fid = mysqli_real_escape_string($conn,$_GET['fid']);
	$revision = $_GET['revision'];
	
	if(mysqli_query($conn,"DELETE FROM formulasRevisions WHERE fid = '$fid' AND revision = '$revision'")){
		$response['success'] = 'Formula revision deleted!';
	}else{
		$response['error'] = 'Unable to delete revision! '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//MANAGE VIEW
if($_GET['manage_view'] == '1'){
	$ing = mysqli_real_escape_string($conn,str_replace('_', ' ',$_GET['ex_ing']));
	
	if($_GET['ex_status'] == 'true'){
		$status = '0';
	}elseif($_GET['ex_status'] == 'false'){
		$status = '1';
	}
	$fid = $_GET['fid'];
	
	$q = mysqli_query($conn, "UPDATE formulas SET exclude_from_summary = '$status' WHERE fid = '$fid' AND ingredient = '$ing'");
	if($q){
		$response['success'] = 'View updated!';
	}else{
		$response['error'] = 'Something went wrong';
	}
	
	echo json_encode($response);
	return;
}

//SCALE FORMULA
if ($_POST['fid'] && $_POST['action'] == 'advancedScale' && $_POST['SG'] && $_POST['amount']) {
    $fid = mysqli_real_escape_string($conn, $_POST['fid']);
    $SG = mysqli_real_escape_string($conn, $_POST['SG']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);

    $new_amount = $amount * $SG;
    $mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '$fid'"));

    $q = mysqli_query($conn, "SELECT quantity, ingredient FROM formulas WHERE fid = '$fid'");
    $all_success = true;

    while ($cur = mysqli_fetch_array($q)) {
        $nq = $cur['quantity'] / $mg['total_mg'] * $new_amount;
        
        if (empty($nq)) {
            $response['error'] = 'Something went wrong...';
            echo json_encode($response);
            return;
        }

        $update = mysqli_query($conn, "UPDATE formulas SET quantity = '$nq' WHERE fid = '$fid' AND quantity = '" . $cur['quantity'] . "' AND ingredient = '" . $cur['ingredient'] . "'");
        
        if (!$update) {
            $all_success = false;
            $error_message = mysqli_error($conn);
            break; 
        }
    }

    if ($all_success) {
        $response['success'] = 'Formula scaled';
    } else {
        $response['error'] = 'Something went wrong... ' . $error_message;
    }

    echo json_encode($response);
    return;
}


//DIVIDE - MULTIPLY
if ($_POST['formula'] && $_POST['action'] == 'simpleScale') {
    $fid = mysqli_real_escape_string($conn, $_POST['formula']);
    $q = mysqli_query($conn, "SELECT quantity, ingredient FROM formulas WHERE fid = '$fid'");
    $all_success = true;
	
    while ($cur = mysqli_fetch_array($q)) {
        // Calculate the new quantity based on the scale action
        if ($_POST['scale'] == 'multiply') {
            $nq = $cur['quantity'] * 2;
        } elseif ($_POST['scale'] == 'divide') {
            $nq = $cur['quantity'] / 2;
        } else {
            $all_success = false;
            $error_message = "Invalid scale action.";
            break; 
        }

        $update = mysqli_query($conn, "UPDATE formulas SET quantity = '$nq' WHERE fid = '$fid' AND quantity = '" . $cur['quantity'] . "' AND ingredient = '" . $cur['ingredient'] . "'");
        
        if (!$update) {
            $all_success = false;
            $error_message = mysqli_error($conn); 
            break; 
        }
    }

    if ($all_success) {
        $response['success'] = 'Formula scaled successfully';
    } else {
        $response['error'] = 'Error during scaling: ' . $error_message;
    }

    echo json_encode($response);
    return;
}


//DELETE INGREDIENT
if($_POST['action'] == 'deleteIng' && $_POST['ingID'] && $_POST['ing']){
	$id = mysqli_real_escape_string($conn, $_POST['ingID']);
	$ing = mysqli_real_escape_string($conn, $_POST['ing']);
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$ingredient_id = mysqli_real_escape_string($conn, $_POST['ingredient_id']);
	
	if($_POST['reCalc'] == 'true'){
		if(!$_POST['formulaSolventID']){
			$response["error"] = 'Please select solvent';
			echo json_encode($response);
			return;
		}
		$formulaSolventID = $_POST['formulaSolventID'];
		
		if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM ingredients WHERE id = '".$ingredient_id."' AND profile='solvent'"))){
			$response["error"] = 'You cannot deduct a solvent from a solvent';
			echo json_encode($response);
			return;
		}
		
		$qs = mysqli_fetch_array(mysqli_query($conn,"SELECT quantity FROM formulas WHERE id = '$id' AND fid = '$fid'"));
		$v = $qs['quantity'];
		mysqli_query($conn,"UPDATE formulas SET quantity = quantity + $v WHERE fid = '$fid' AND ingredient_id = '".$formulaSolventID."'");

	}
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '$fid'"));

	if($meta['isProtected'] == FALSE){
		
		if(mysqli_query($conn, "DELETE FROM formulas WHERE id = '$id' AND fid = '$fid'")){
			$response['success'] = $ing.' removed from the formula';
			$lg = "REMOVED: $ing removed";
			mysqli_query($conn, "INSERT INTO formula_history (fid,ing_id,change_made,user) VALUES ('".$meta['id']."','".$ingredient_id."','$lg','".$user['fullName']."')");
		}else{
			$response['error'] = $ing.' cannot be removed from the formula';
		}
	}
	echo json_encode($response);
	return;
}

//ADD INGREDIENT
if($_POST['action'] == 'addIngToFormula'){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$id = mysqli_real_escape_string($conn, $_POST['id']);
	$ingredient_id = mysqli_real_escape_string($conn, $_POST['ingredient']);
	$quantity = preg_replace("/[^0-9.]/", "", mysqli_real_escape_string($conn, $_POST['quantity']));
	$concentration = preg_replace("/[^0-9.]/", "", mysqli_real_escape_string($conn, $_POST['concentration']));
	$dilutant = mysqli_real_escape_string($conn, $_POST['dilutant']);
	$ingredient = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingredients WHERE id = '$ingredient_id'"));
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected,name FROM formulasMetaData WHERE fid = '$fid'"));
	if($meta['isProtected'] == TRUE){
		$response["error"] = 'Formula is protected and cannot be modified';
		echo json_encode($response);
		return;
	}
	
	if (empty($quantity) || empty($concentration)){
		$response['error'] = 'Missing required fields';
		echo json_encode($response);
		return;
	}
			
	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient_id FROM formulas WHERE ingredient_id = '$ingredient_id' AND fid = '$fid'"))){
		$response['error'] = $ingredient['name'].' already exists in formula';
		echo json_encode($response);
		return;
	}
	
	if($_POST['reCalc'] == 'true'){
		if(!$_POST['formulaSolventID']){
			$response["error"] = 'Please select solvent';
			echo json_encode($response);
			return;
		}
		
		$formulaSolventID = $_POST['formulaSolventID'];
		
		if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM ingredients WHERE id = '".$ingredient_id."' AND profile='solvent'"))){
			$response["error"] = 'You cannot add a solvent to a solvent';
			echo json_encode($response);
			return;
		}
		
		$slv = mysqli_fetch_array(mysqli_query($conn,"SELECT quantity FROM formulas WHERE ingredient_id = '".$formulaSolventID."' AND fid = '".$fid."'"));

        if($slv['quantity'] < $quantity){
        	$response["error"] = 'Not enough solvent, available: '.number_format($slv['quantity'],$settings['qStep']).$settings['mUnit'];
            echo json_encode($response);
            return;
        }
				
		mysqli_query($conn,"UPDATE formulas SET quantity = quantity - $quantity WHERE fid = '$fid' AND ingredient_id = '".$formulaSolventID."'");

	}
	
	if(mysqli_query($conn,"INSERT INTO formulas(fid,name,ingredient,ingredient_id,concentration,quantity,dilutant) VALUES('$fid','".$meta['name']."','".$ingredient['name']."','".$ingredient_id."','$concentration','$quantity','$dilutant')")){
			
		$lg = "ADDED: ".$ingredient['name']." $quantity".$settings['mUnit']." @$concentration% $dilutant";
		mysqli_query($conn, "INSERT INTO formula_history (fid,ing_id,change_made,user) VALUES ('".$id."','$ingredient_id','$lg','".$user['fullName']."')");
		mysqli_query($conn, "UPDATE formulasMetaData SET status = '1' WHERE fid = '".$fid."' AND status = '0' AND isProtected = '0'");
			
		$response['success'] = '<strong>'.$quantity.$settings['mUnit'].'</strong> of <strong>'.$ingredient['name'].'</strong> added to the formula';
		echo json_encode($response);
		return;
	} else {
		$response['error'] = 'Something went wrong '.mysqli_error($conn);
		echo json_encode($response);
	}
		
 	if(mysqli_error($conn)){
		$response['error'] = 'Something went wrong '.mysqli_error($conn);
		echo json_encode($response);
	}
	return;
}

//REPLACE INGREDIENT
if($_POST['action'] == 'repIng' && $_POST['fid']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	
	if(!$_POST['dest']){
		$response['error'] = 'Please select ingredient';
		echo json_encode($response);
		return;
	}
	
	$ingredient = mysqli_real_escape_string($conn, $_POST['dest']);
	$oldIngredient = mysqli_real_escape_string($conn, $_POST['ingSrcName']);
	$ingredient_id = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$ingredient'"));
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '$fid'"));
	if($meta['isProtected'] == FALSE){
		if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '$ingredient' AND fid = '$fid'"))){
			$response['error'] = $ingredient.' already exists in formula!';
			echo json_encode($response);
			return;
		}
		
		if(mysqli_query($conn, "UPDATE formulas SET ingredient = '$ingredient', ingredient_id = '".$ingredient_id['id']."' WHERE ingredient = '$oldIngredient' AND id = '".$_POST['ingSrcID']."' AND fid = '$fid'")){
			$response['success'] = $oldIngredient.' replaced by '.$ingredient;
			$lg = "REPLACED: $oldIngredient WITH $ingredient";
			mysqli_query($conn, "INSERT INTO formula_history (fid,ing_id,change_made,user) VALUES ('".$meta['id']."','".$ingredient_id['id']."','$lg','".$user['fullName']."')");
		}else{
			$response['error'] = 'Error replacing '.$oldIngredient;
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode($response);
	return;
}

//Convert to ingredient
if($_POST['action'] == 'conv2ing' && $_POST['ingName'] && $_POST['fid']){
	$name = mysqli_real_escape_string($conn, $_POST['ingName']);
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$fname = mysqli_real_escape_string($conn, $_POST['fname']);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$name'"))){
		$response['error'] = '<a href="/?do=ingredients&search='.$name.'" target="_blank">'.$name.'</a> already exists';
		echo json_encode($response);
		return;
	}

	$formula_q = mysqli_query($conn, "SELECT ingredient,quantity,concentration FROM formulas WHERE fid = '$fid'");
	while ($formula = mysqli_fetch_array($formula_q)){
		$ing_data = mysqli_fetch_array(mysqli_query($conn,"SELECT cas FROM ingredients WHERE name = '".$formula['ingredient']."'"));
		$conc = number_format($formula['quantity']/100 * 100, $settings['qStep']);
		$conc_p = number_format($formula['concentration'] / 100 * $conc, $settings['qStep']);
						
		mysqli_query($conn, "INSERT INTO ingredient_compounds (ing, name, cas, min_percentage, max_percentage) VALUES ('$name','".$formula['ingredient']."','".$ing_data['cas']."','".$conc_p."','".$conc_p."')");
	}
			
	if(mysqli_query($conn, "INSERT INTO ingredients (name, type, cas, notes) VALUES ('$name','Base','Mixture','Converted from formula $fname')")){
		$response['success'] = '<a href="/?do=ingredients&search='.$name.'" target="_blank">'.$name.'</a> converted to ingredient';
		echo json_encode($response);
	}
	return;

}

//DUPLICATE FORMULA
if($_POST['action'] == 'clone' && $_POST['fid']){
	require_once(__ROOT__.'/func/genFID.php');

	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$fname = mysqli_real_escape_string($conn, $_POST['fname']);

	$newName = $fname.' - (Copy)';
	$newFid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE name = '$newName'"))){
		$response['error'] = $newName.' already exists, please remove or rename it first!</div>';
		echo json_encode($response);
        return;
    }
	$sql1 = "INSERT INTO formulasMetaData (fid, name, notes, profile, gender, defView, product_name, catClass) SELECT '$newFid', '$newName', notes, profile, gender, defView, '$newName', catClass FROM formulasMetaData WHERE fid = '$fid'";
    $sql2 = "INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes) SELECT '$newFid', '$newName', ingredient, ingredient_id, concentration, dilutant, quantity, notes FROM formulas WHERE fid = '$fid'";
    
    if(mysqli_query($conn, $sql1) && mysqli_query($conn, $sql2)) {
        // Fetch the id of the newly inserted record
        $nID = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$newFid'"));
        if($nID){
            $response['success'] = $fname.' cloned as <a href="/?do=Formula&id='.$nID['id'].'" target="_blank">'.$newName.'</a>!</div>';
        } else {
            $response['error'] = "Failed to fetch ID of cloned record!";
			echo json_encode($response);
			return;
        }
    } else {
        $response['error'] = "Failed to clone formula!";
		echo json_encode($response);
		return;
    }
	echo json_encode($response);
	return;
}

//ADD NEW FORMULA
if($_POST['action'] == 'addFormula'){
	if(empty($_POST['name'])){
		$response['error'] = 'Formula name is required.';
		echo json_encode($response);
		return;
	}
	
	if(strlen($_POST['name']) > '100'){
		$response['error'] = 'Formula name is too big. Max 100 chars allowed.';
		echo json_encode($response);
		return;
	}
	
	require_once(__ROOT__.'/func/genFID.php');
	
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$notes = mysqli_real_escape_string($conn, $_POST['notes']);
	$profile = mysqli_real_escape_string($conn, $_POST['profile']);
	$catClass = mysqli_real_escape_string($conn, $_POST['catClass']);
	$finalType = mysqli_real_escape_string($conn, $_POST['finalType']);
	$customer_id = $_POST['customer']?:0;
	$fid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE name = '$name'"))){
		$response['error'] = $name.' already exists!';
	}else{
		if(mysqli_query($conn, "INSERT INTO formulasMetaData (fid, name, notes, profile, catClass, finalType, customer_id) VALUES ('$fid', '$name', '$notes', '$profile', '$catClass', '$finalType', '$customer_id')")){
			$last_id = mysqli_insert_id($conn);
			$fullver = $product.' '.$ver;
			mysqli_query($conn, "INSERT INTO formulasTags (formula_id, tag_name) VALUES ('$last_id','$fullver')");
			$response = array(
				"success" => array(
				"id" => (int)$last_id,
				"msg" => "$name added!",
				)
			);
		}else{
			$response['error'] = 'Something went wrong...'.mysqli_error($conn);
		}
	}

	echo json_encode($response);
	return;
}
	
//DELETE FORMULA
if($_POST['action'] == 'deleteFormula' && $_POST['fid']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$fname = mysqli_real_escape_string($conn, $_POST['fname']);

	if($_POST['archiveFormula'] == "true"){
		require_once(__ROOT__.'/libs/fpdf.php');
		require_once(__ROOT__.'/func/genBatchPDF.php');
		require_once(__ROOT__.'/func/ml2L.php');
		
		define('FPDF_FONTPATH',__ROOT__.'/fonts');
		
		$defCatClass = $settings['defCatClass'];
		$arcID = "Archived-".$fname.$fid;
		
		$rs = genBatchPDF($fid,$arcID,'100','100','100',$defCatClass,$settings['qStep'],$settings['defPercentage'],'formulas');
		
		if($rs !== true){
			$response['error'] = 'Error archiving the formula, '.$rs['error'];
			echo json_encode($response);
			return;
		}

	}
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid' AND isProtected = '1'"))){
		$response['error'] = 'Error deleting formula '.$fname.' is protected.</div>';
		echo json_encode($response);
		return;
	}
		
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid'"));

	if(mysqli_query($conn, "DELETE FROM formulas WHERE fid = '$fid'")){
		mysqli_query($conn, "DELETE FROM formulasMetaData WHERE fid = '$fid'");
		mysqli_query($conn, "DELETE FROM formulasRevisions WHERE fid = '$fid'");
		mysqli_query($conn, "DELETE FROM formula_history WHERE fid = '".$meta['id']."'");
		mysqli_query($conn, "DELETE FROM formulasTags WHERE formula_id = '".$meta['id']."'");
		mysqli_query($conn, "DELETE FROM makeFormula WHERE fid = '$fid'");
		$response['success'] = 'Formula '.$fname.' deleted!';
	}else{
		$response['error'] = 'Error deleting '.$fname.' formula!';
	}
	echo json_encode($response);
	return;
}

//RESET ING IN MAKE FORMULA
if($_POST['action'] == 'makeFormula' && $_POST['undo'] == '1'){
	$q = trim($_POST['originalQuantity']);
	$ingID = mysqli_real_escape_string($conn, $_POST['ingID']);
	$repName = $_POST['repName'];

	if(mysqli_query($conn, "UPDATE makeFormula SET replacement_id = '0', toAdd = '1', skip = '0', overdose = '0', quantity = '".$_POST['originalQuantity']."' WHERE id = '".$_POST['ID']."'")){
		if(!empty($repName)) {
			$msg = $repName."'s quantity reset";
		}else{
			$msg = $_POST['ing']."'s quantity reset";
		}
		$response['success'] = $msg;
		
		if($_POST['resetStock'] == "true"){
			if(!($_POST['supplier'])){
				$response['error'] = 'Please select a supplier';
				echo json_encode($response);
				return;
			}
			$nIngID = $_POST['repID'] ?: $ingID;
			mysqli_query($conn, "UPDATE suppliers SET stock = stock + $q WHERE ingID = '$nIngID' AND ingSupplierID = '".$_POST['supplier']."'");
			$response['success'] .= "<br/><strong>Stock increased by ".$q.$settings['mUnit']."</strong>";
		}
		echo json_encode($response);
	}
	return;
}

//MAKE FORMULA
if($_POST['action'] == 'makeFormula' && $_POST['fid'] && $_POST['qr'] && $_POST['id']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$id = mysqli_real_escape_string($conn, $_POST['id']);
	

	if($_POST['repID']) {
		$repID = $_POST['repID'];
		$ingID = $_POST['repID'];
	} else {
		$repID = 0;
		$ingID = $_POST['ingId'];
	}

	$ingredient =  mysqli_real_escape_string($conn, $_POST['repName'] ?: $_POST['ing']);
	
	$notes = mysqli_real_escape_string($conn, $_POST['notes']) ?: "-";

	$qr = trim($_POST['qr']);
	$q = trim($_POST['q']);
	
	
	if(!is_numeric($_POST['q'])){
		$response['error'] = 'Invalid amount value';
		echo json_encode($response);
		return;
	}
						 
	
	if($_POST['updateStock'] == "true"){
		if(!($_POST['supplier'])){
			$response['error'] = 'Please select a supplier';
			echo json_encode($response);
			return;
		}
		$getStock = mysqli_fetch_array(mysqli_query($conn, "SELECT stock,mUnit FROM suppliers WHERE ingID = '$ingID' AND ingSupplierID = '".$_POST['supplier']."'"));
		if($getStock['stock'] < $q){
			$w = "<p>Amount exceeds quantity available in stock (".$getStock['stock'].$getStock['mUnit']."). The maximum available will be deducted from stock</p>";
			
			$q = $getStock['stock'];
		}
		mysqli_query($conn, "UPDATE suppliers SET stock = stock - $q WHERE ingID = '$ingID' AND ingSupplierID = '".$_POST['supplier']."'");
		$response['success'] .= "<br/><strong>Stock deducted by ".$q.$settings['mUnit']."</strong>";
	}
	
	$q = trim($_POST['q']); //DIRTY HACK - TODO
	
	if($qr == $q){
		if(mysqli_query($conn, "UPDATE makeFormula SET replacement_id = '$repID', toAdd = '0', notes = '$notes' WHERE fid = '$fid' AND id = '$id'")){
			$response['success'] = $ingredient.' added in the formula.'.$w;
		} else {
			$response['error'] = mysqli_error($conn);
		}
	}else{
		$sub_tot = $qr - $q;
		if(mysqli_query($conn, "UPDATE makeFormula SET  replacement_id = '$repID', quantity='$sub_tot', notes = '$notes' WHERE fid = '$fid' AND id = '$id'")){
			$response['success'] = 'Formula updated';
		}
	}

		
	if($qr < $q){
		if(mysqli_query($conn, "UPDATE makeFormula SET overdose = '$q' WHERE fid = '$fid' AND id = '$id'")){
			$response['success'] = $_POST['ing'].' is overdosed, <strong>'.$q.'<strong> added';
		}
	}
	
	if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid' AND toAdd = '1'"))){
		$response['success'] = '<strong>All materials added. You should mark formula as complete now!</strong>';
	}
	
	
	echo json_encode($response);
	return;
}



//SKIP MATERIAL FROM MAKE FORMULA
if($_POST['action'] == 'skipMaterial' && $_POST['fid'] &&  $_POST['id']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$id = mysqli_real_escape_string($conn, $_POST['id']);
	$ingID = mysqli_real_escape_string($conn, $_POST['ingId']);
	$notes = mysqli_real_escape_string($conn, $_POST['notes']) ?: "-";

	if(mysqli_query($conn, "UPDATE makeFormula SET skip = '1', notes = '$notes' WHERE fid = '$fid' AND id = '$id'")){
		$response['success'] = $_POST['ing'].' skipped from the formulation';
	} else {
		$response['error'] = 'Error skipping the ingredient';
	}
	
	echo json_encode($response);
	return;
}



//MARK COMPLETE
if($_POST['action'] == 'todo' && $_POST['fid'] && $_POST['markComplete']){
	require_once(__ROOT__.'/libs/fpdf.php');
	require_once(__ROOT__.'/func/genBatchID.php');
	require_once(__ROOT__.'/func/genBatchPDF.php');
	require_once(__ROOT__.'/func/ml2L.php');

	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$total_quantity = mysqli_real_escape_string($conn, $_POST['totalQuantity']);

	define('FPDF_FONTPATH',__ROOT__.'/fonts');
	$defCatClass = $settings['defCatClass'];
	
		
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid' AND toAdd = '1' AND skip = '0'"))){
		$response['error'] = '<strong>Formula is pending materials to add, cannot be marked as complete.</strong>';
		echo json_encode($response);
		return;
	}
	if(mysqli_query($conn,"UPDATE formulasMetaData SET isMade = '1', toDo = '0', madeOn = NOW(), status = '2' WHERE fid = '$fid'")){
		$batchID = genBatchID();
		genBatchPDF($fid,$batchID,$total_quantity,'100',$total_quantity,$defCatClass,$settings['qStep'],$settings['defPercentage'],'makeFormula');

		mysqli_query($conn, "DELETE FROM makeFormula WHERE fid = '$fid'");
		
		$response['success'] = '<strong>Formula is complete</strong>';
	}
	
	echo json_encode($response);
	return;
}


//TODO ADD FORMULA
if($_POST['action'] == 'todo' && $_POST['fid'] && $_POST['add']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$fname = mysqli_real_escape_string($conn, $_POST['fname']);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid' AND toDo = '1'"))){
		$response['error'] = 'Formula '.$fname.' is already scheduled';
		echo json_encode($response);
		return;
	}
								
	if(mysqli_query($conn, "INSERT INTO makeFormula (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, originalQuantity, toAdd) SELECT fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, quantity, '1' FROM formulas WHERE fid = '$fid' AND exclude_from_calculation = '0'")){


		mysqli_query($conn, "UPDATE formulasMetaData SET toDo = '1', status = '1', isMade = '0', scheduledOn = NOW() WHERE fid = '$fid'");
		$response['success'] = 'Formula <a href="/?do=scheduledFormulas">'.$fname.'</a> scheduled to make!';		
	}else{
		$response['error'] = 'An error occured '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

//TODO REMOVE FORMULA
if($_POST['action'] == 'todo' && $_POST['fid'] && $_POST['remove']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "DELETE FROM makeFormula WHERE fid = '$fid'")){
		mysqli_query($conn, "UPDATE formulasMetaData SET toDo = '0', status = '0', isMade = '0' WHERE fid = '$fid'");
		$response['success'] = $name.' removed';
		echo json_encode($response);
	}
	return;
}

//CART MANAGE
if($_POST['action'] == 'addToCart' && $_POST['material'] && $_POST['quantity']){
	$material = mysqli_real_escape_string($conn, $_POST['material']);
	$quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
	$purity = mysqli_real_escape_string($conn, $_POST['purity']);
	$ingID = mysqli_real_escape_string($conn, $_POST['ingID']);

	$qS = mysqli_fetch_array(mysqli_query($conn, "SELECT ingSupplierID, supplierLink FROM suppliers WHERE ingID = '$ingID'"));
	
	if(empty($qS['supplierLink'])){
		$response['error'] = $material.' cannot be added to cart as missing supplier info. Please update material supply details first.';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM cart WHERE name = '$material'"))){
		if(mysqli_query($conn, "UPDATE cart SET quantity = quantity + '$quantity' WHERE name = '$material'")){
			$response['success'] = 'Additional '.$quantity.$settings['mUnit'].' of '.$material.' added to the cart.';
		}
		echo json_encode($response);
		return;
	}
									
	if(mysqli_query($conn, "INSERT INTO cart (ingID,name,quantity,purity) VALUES ('$ingID','$material','$quantity','$purity')")){
		$response['success'] = $material.' added to the cart!';
		echo json_encode($response);
		return;
	}
	
	return;
}

if($_POST['action'] == 'removeFromCart' && $_POST['materialId']){
	$materialId = mysqli_real_escape_string($conn, $_POST['materialId']);

	if(mysqli_query($conn, "DELETE FROM cart WHERE id = '$materialId'")){
		$response['success'] = $_POST['materialName'].' removed from cart!';
		echo json_encode($response);
	}
}


//VIEW BOX BACK LABEL
if($_GET['action'] == 'viewBoxLabel' && $_GET['fid']){
	$fid = $_GET['fid'];
	
	$q = mysqli_fetch_array(mysqli_query($conn, "SELECT name,product_name FROM formulasMetaData WHERE fid = '".$fid."'"));
	$name = $q['name'];
	$qIng = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '".$fid."'");

	while($ing = mysqli_fetch_array($qIng)){
		$chName = mysqli_fetch_array(mysqli_query($conn, "SELECT chemical_name,name FROM ingredients WHERE name = '".$ing['ingredient']."' AND allergen = '1'"));
		
		if($qCMP = mysqli_query($conn, "SELECT name FROM ingredient_compounds WHERE ing = '".$ing['ingredient']."' AND toDeclare = '1'")){
			while($cmp = mysqli_fetch_array($qCMP)){
				$allergen[] = $cmp['name'];
			}
		}
		$allergen[] = $chName['chemical_name']?:$chName['name'];
	}
	$allergen[] = 'Denatured Ethyl Alcohol '.$_GET['carrier'].'% Vol, Fragrance, DPG, Distilled Water';
	
	if($_GET['batchID']){
		$bNo = $_GET['batchID'];
	}else{
		$bNO = 'N/A';
	}
	if($settings['brandName']){
		$brand = $settings['brandName'];
	}else{
		$brand = 'PV Pro';
	}
	$allergenFinal = implode(", ",array_filter(array_unique($allergen)));
	$info = "FOR EXTERNAL USE ONLY. \nKEEP AWAY FROM HEAT AND FLAME. \nKEEP OUT OF REACH OF CHILDREN. \nAVOID SPRAYING IN EYES. \n \nProduction: ".date("d/m/Y")." \nB. NO: ".$bNo." \n$brand";


	echo "<pre>";
	echo "<strong>".$name."</strong>\n\n";
	echo 'INGREDIENTS'."\n\n";
	echo wordwrap ($allergenFinal, 90)."\n\n";
	echo wordwrap ($info, 50)."\n\n";
	echo '</pre>';


	return;
}

<?php
define('pvault_panel', TRUE);
define('__ROOT__', dirname(__FILE__));

require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/rgbToHex.php');

$settings = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM settings")); 

$defCatClass = $settings['defCatClass'];
$defImage = base64_encode(file_get_contents(__ROOT__.'/img/pv_molecule.png'));

$log_path = $tmp_path.'/logs/';
$log_api_file = 'pv-api.log';


if (!file_exists($log_path)) {
	mkdir($log_path, 0740, true);
}

if (!file_exists($tmp_path.$log_api_file)) {
    touch($log_path.$log_api_file);
}

$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen($tmp_path.$log_api_file, 'a');
fwrite($fp, $req_dump);
fclose($fp);


function apiCheckAuth($key, $conn){
   if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM settings WHERE api = '1' AND api_key='$key'"))){
	   return true;
   }else{
	   return false;
   }
}

if (isset($_REQUEST['login']) && isset($_REQUEST['key'])){	
	$key = mysqli_real_escape_string($conn, $_REQUEST['key']);
	$client = mysqli_real_escape_string($conn, $_REQUEST['client']);
	if(apiCheckAuth($key, $conn) == true){
		$response['status'] = "Success";
	}else{
		$response['status'] = "Auth failed";
	}
	header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
	return;
}

if(!$_REQUEST['key'] || !$_REQUEST['do']){
	$response['status'] = "Invalid API call";
	header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
	return;
}
	
$key = mysqli_real_escape_string($conn, $_REQUEST['key']);
$_REQUEST['do'] = strtolower(mysqli_real_escape_string($conn, $_REQUEST['do']));

if(apiCheckAuth($key, $conn) == false){
	$response['status'] = "Auth failed";
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}
	
	
//FORMULAS UPLOAD
if($_REQUEST['do'] === 'upload' && $_REQUEST['type'] === 'formula'){
	require_once(__ROOT__.'/api-functions/formulas_upload.php');
	return;
}
	
//FORMULAS GET
if($_REQUEST['do'] === 'get' && $_REQUEST['type'] === 'formula'){
	require_once(__ROOT__.'/api-functions/formulas_get.php');
	return;
}

//INGREDIENTS GET
if($_REQUEST['do'] === 'get' && $_REQUEST['type'] === 'ingredients'){
	require_once(__ROOT__.'/api-functions/ingredients_get.php');
	return;
}

//FORMULAS UPLOAD
if($_REQUEST['do'] === 'upload' && $_REQUEST['type'] === 'ingredients'){
	require_once(__ROOT__.'/api-functions/ingredients_upload.php');
	return;
}

//CATEGORIES GET
if($_REQUEST['do'] === 'get' && $_REQUEST['type'] === 'categories'){
	require_once(__ROOT__.'/api-functions/categories_get.php');
	return;
}

//SUPPLIERS GET
if($_REQUEST['do'] === 'get' && $_REQUEST['type'] === 'suppliers'){
	require_once(__ROOT__.'/api-functions/suppliers_get.php');
	return;
}

//DOCUMENTS GET
if($_REQUEST['do'] === 'get' && $_REQUEST['type'] === 'documents'){
	require_once(__ROOT__.'/api-functions/documents_get.php');
	return;
}

//TODO - CATEGORIES UPLOAD
//TODO - SUPPLIERS UPLOAD
//TODO - DOCS UPLOAD



//PEDRO CALLBACKS
if($_REQUEST['do'] === 'manage' && $_REQUEST['type'] === 'makeformula'){
	require_once(__ROOT__.'/api-functions/manage_makeformula.php');
	return;
}

?>
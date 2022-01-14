<?php
require('../inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/countElement.php');


$cats_q = mysqli_query($conn, "SELECT id,name,description,type FROM IFRACategories ORDER BY id ASC");
while($cats_res = mysqli_fetch_array($cats_q)){
    $cats[] = $cats_res;
}

if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients")))){
	$response['Error'] = (string)'<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> no ingredients yet, click <a href="?do=ingredients">here</a> to add.</div>';    
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}
if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData")))){
	$response['Error'] = (string)'<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> no formulas yet, click <a href="#" data-toggle="modal" data-target="#add_formula">here</a> to add.</div>';    
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}

if($_GET['filter'] && $_GET['profile'] || $_GET['sex']){
	$f = "WHERE profile = '".$_GET['profile']."' OR sex = '".$_GET['sex']."'";
}
$formulas = mysqli_query($conn, "SELECT id,fid,name,product_name,isProtected,profile,sex,created,catClass FROM formulasMetaData $f");

while ($allFormulas = mysqli_fetch_array($formulas)){
	    $formula[] = $allFormulas;
}
foreach ($formula as $formula) { 
	$r['id'] = (int)$formula['id'];
	$r['fid'] = (string)$formula['fid'];
	$r['product_name'] = (string)$formula['product_name']?:'N/A';
	$r['name'] = (string)$formula['name'];
	$r['isProtected'] = (int)$formula['isProtected'];
	$r['profile'] = (string)$formula['profile']?:'N/A';
	$r['sex'] = (string)$formula['sex']?:'N/A';
	$r['created'] = (string)$formula['created'];
	$r['catClass'] = (string)$formula['catClass']?:'N/A';
	$r['ingredients'] = (int)countElement("formulas WHERE fid = '".$formula['fid']."'",$conn)?:'0';

	$response['data'][] = $r;
	
}

if(empty($r)){
	$response['data'] = [];
}


header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>
<?php
require('../inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulaCategories")))){
	$response['Error'] = (string)'<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> no categories has been created yet</div>';    
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}

$cat_q = mysqli_query($conn, "SELECT id,name,cname,type FROM formulaCategories");
while($cats_res = mysqli_fetch_array($cat_q)){
    $cats[] = $cats_res;
}

foreach ($cats as $category) { 
	$r['id'] = (int)$category['id'];
	$r['name'] = (string)$category['name'];
	$r['cname'] = (string)$category['cname'];
	$r['type'] = (string)$category['type'];

	$response['data'][] = $r;
}

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>
<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$cat_q = mysqli_query($conn, "SELECT id,name,notes,image,colorKey FROM ingCategory WHERE owner_id = '$userID'");
while($cats_res = mysqli_fetch_array($cat_q)){
    $cats[] = $cats_res;
}

foreach ($cats as $category) { 
	$r['id'] = (int)$category['id'];
	$r['name'] = (string)$category['name'];
	$r['notes'] = (string)$category['notes']?:'-';
	$r['name'] = (string)$category['name'];
	$r['image'] = (string)$category['image']?: null;
	$r['colorKey'] = (string)$category['colorKey']?:'-';

	$response['data'][] = $r;
}

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

<?php
require('../inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$defCatClass = $settings['defCatClass'];

$q = mysqli_query($conn, "SELECT id,name,INCI,cas,profile,category,odor,$defCatClass FROM ingredients LIMIT 10");
while($res = mysqli_fetch_array($q)){
    $ingredients[] = $res;
}

foreach ($ingredients as $ingredient) { 
	$r['id'] = (int)$ingredient['id'];
	$r['name'] = (string)$ingredient['name'];
	$r['INCI'] = (string)$ingredient['INCI']?: 'N/A';
	$r['cas'] = (string)$ingredient['cas']?: 'N/A';
	$r['profile'] = (string)$ingredient['profile']?: 'N/A';
	$r['category'] = (int)$ingredient['category']?: 1;
	$r['odor'] = (string)$ingredient['odor']?: 'N/A';
	$r['usageLimit'] = (float)$ingredient[$defCatClass]?: 100;

	$response['data'][] = $r;
}

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

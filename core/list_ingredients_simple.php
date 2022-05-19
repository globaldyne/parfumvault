<?php
require('../inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if($s = $_GET['search']){
	
	$query = "WHERE name LIKE '%$s%' OR cas LIKE '%$s%'";
}

$q = mysqli_query($conn, "SELECT id,name,INCI,cas,type,odor FROM ingredients $query ORDER BY name ASC");
while($res = mysqli_fetch_array($q)){
    $ingredients[] = $res;
}

foreach ($ingredients as $ingredient) { 

	
	$r['id'] = (int)$ingredient['id'];
	$r['name'] = (string)$ingredient['name'];
	$r['IUPAC'] = (string)$ingredient['INCI']?: 'N/A';
	$r['cas'] = (string)$ingredient['cas']?: 'N/A';
	$r['type'] = (string)$ingredient['type'] ?: 'Unknown';
	$r['description'] = (string)$ingredient['odor'] ?: 'N/A';


	$rx[]=$r;
}

$response = array(
  "data" => $rx
);

if(empty($rx)){
	$response['data'] = array("No results");
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

<?php

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/getIngSupplier.php');

if(!$_POST['search']){
	$response['data'] = array("What you looking for here??");
	echo json_encode($response);
	return;
}

$s = trim($_POST['search']['term']);

if ($_POST['isDeepQ'] == "true"){
	$t = "synonyms,ingredients";
	$filter = "WHERE synonym LIKE '%$s%' AND ing = name GROUP BY name";

} else if($_POST['isAbsolute'] == "true"){
	$t = "ingredients";	
	$filter = "WHERE name = '$s' OR cas = '$s' OR einecs = '$s' OR INCI = '$s'";
	
}else{
	$t = "ingredients";	
	$filter = "WHERE name LIKE '%$s%' OR cas LIKE '%$s%' OR INCI LIKE '%$s%'";
}

$q = mysqli_query($conn, "SELECT ingredients.id,name,INCI,cas,einecs,type,odor,physical_state,profile FROM $t $filter ORDER BY name ASC");
while($res = mysqli_fetch_array($q)){
    $ingredients[] = $res;
}
$i = 0;
foreach ($ingredients as $ingredient) { 

	
	$r['id'] = (int)$ingredient['id'];
	$r['name'] = (string)$ingredient['name'];
	$r['IUPAC'] = (string)$ingredient['INCI']?: 'N/A';
	$r['cas'] = (string)$ingredient['cas']?: 'N/A';
	$r['einecs'] = (string)$ingredient['einecs']?: 'N/A';
	$r['type'] = (string)$ingredient['type'] ?: 'Unknown';
	$r['description'] = (string)$ingredient['odor'] ?: 'N/A';
	$r['physical_state'] = (int)$ingredient['physical_state'] ?: 1;
	$r['profile'] = (string)$ingredient['profile'] ?: 'Uknwown';
	$r['stock'] = (float)number_format(getIngSupplier($ingredient['id'],1,$conn)['stock'], $settings['qStep']) ?: 0;

	$rx[]=$r;
	$i++;
}

$response = array(
  "data" => $rx
);

$response = array(
  "recordsTotal" => (int)$i,
  "data" => $rx
);

if(empty($rx)){
	$response['data'] = array("No results");
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

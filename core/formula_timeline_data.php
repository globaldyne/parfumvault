<?php

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

header('Content-Type: application/json; charset=utf-8');

if(!$_GET['id']){		
	$response['data'] = [];
	$response['Error'] = (string)'Request is not valid.';    
	echo json_encode($response);
	return;
}

$id = $_GET['id'];
$i = 0;

$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,name FROM formulasMetaData WHERE id = '$id'"));

if(!$meta['id']){	
	$response['data'] = [];
	$response['Error'] = (string)'Requested id is not valid.';    
	echo json_encode($response);
	return;
}

$sql = "SELECT * FROM formula_history WHERE fid = '".$id."' ORDER BY date_time DESC";

$q = mysqli_query($conn, $sql);
while ($res = mysqli_fetch_array($q)){
	    $his[] = $res;
}

foreach ($his as $h){
	
	$r['id'] = (int)$h['id'];
	$r['fid'] = (string)$h['fid'];
	$r['ing_id'] = (int)$h['ing_id'];
	$r['change_made'] = (string)$h['change_made'];
	$r['date_time'] = (string)$h['date_time'];
	$r['user'] = (string)$h['user'];
	
	$i++;
	$rx[]=$r;

}

$response = array(
  "draw" => (int)$_POST['draw'],
  "recordsTotal" => (int)$i,
  "recordsFiltered" => (int)$i,
  "debug" => $sql,
  "data" => $rx
);

if(empty($r)){
	$response['data'] = [];
}


echo json_encode($response);
return;
?>
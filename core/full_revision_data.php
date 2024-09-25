<?php
$starttime = microtime(true);

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

header('Content-Type: application/json; charset=utf-8');

if(!$_GET['fid'] || !$_GET['revID']){		
	$response['data'] = [];
	$response['Error'] = (string)'Request is not valid.';    
	echo json_encode($response);
	return;
}

$fid = $_GET['fid'];
$revID = $_GET['revID'];
$i = 0;

$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE fid = '$fid'"));

if(!$meta['fid']){		
	$response['Error'] = (string)'Requested id is not valid.';    
	echo json_encode($response);
	return;
}

$sql = "SELECT * FROM formulasRevisions WHERE fid = '".$fid."' AND revision = '".$revID."'";

$q = mysqli_query($conn, $sql);
while ($res = mysqli_fetch_array($q)){
	    $rev[] = $res;
}

foreach ($rev as $revision){
	$mg['total_mg'] += $revision['quantity'];
	$conc = $revision['concentration'] / 100 * $revision['quantity']/$mg['total_mg'] * 100;

	$r['id'] = (int)$revision['id'];
	$r['fid'] = (string)$revision['fid'];
	$r['name'] = (string)$revision['name'];
	$r['ingredient']['name'] = (string)$revision['ingredient'];
	$r['ingredient']['id'] = (int)$revision['ingredient_id'];
	$r['purity'] = (float)$revision['concentration'];
	
    $r['concentration'] = number_format($conc, $settings['qStep']) ?: 0.000;

	$r['dilutant'] = (string)$revision['dilutant'];
	$r['quantity'] = number_format((float)$revision['quantity'], $settings['qStep'],'.', '') ?: 0;
	$r['notes'] = (string)$revision['notes'];
	$r['exclude_from_summary'] = (int)$revision['exclude_from_summary'];
	$r['revision']['id'] = (int)$revision['revision'];
	$r['revision']['revisionDate'] = (string)$revision['revisionDate'];
	$r['revision']['revisionMethod'] = (string)$revision['revisionMethod'];

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
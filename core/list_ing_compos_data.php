<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
$defCatClass = $settings['defCatClass'];

$ingID = base64_decode($_GET["id"]);

$q = mysqli_query($conn, "SELECT id,ing,name,cas,ec,percentage,GHS,toDeclare FROM ingredient_compounds WHERE ing = '$ingID'");
while($res = mysqli_fetch_array($q)){
    $compos[] = $res;
}

foreach ($compos as $compo) {
	 
	$chkIFRA = mysqli_fetch_array(mysqli_query($conn, "SELECT ifra_key, $defCatClass, risk FROM IFRALibrary WHERE cas = '".$compo['cas']."'"));
	
	$r['id'] = (int)$compo['id'];
	$r['ing'] = (string)$compo['ing'];
	$r['name'] = (string)$compo['name'];
	$r['cas'] = (string)$compo['cas']?: 'N/A';
	$r['ec'] = (string)$compo['ec']?: 'N/A';
	$r['GHS'] = (string)$compo['GHS']?: '-';
	$r['percentage'] = (float)$compo['percentage'] ?: 0;	
	$r['toDeclare'] = (int)$compo['toDeclare'] ?: 0;	
	
	$r['isIFRA'] = 0;
	if($chkIFRA[$defCatClass]){
		$r['isIFRA'] = 1;
	}
	$r['IFRA'] = (float)$chkIFRA[$defCatClass] ?: '-';
	$response['data'][] = $r;
}

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
$defCatClass = $settings['defCatClass'];
$defPercentage = $settings['defPercentage'];

$ingID = base64_decode($_GET["id"]);

$q = mysqli_query($conn, "SELECT id,ing,name,cas,ec,min_percentage,max_percentage,GHS,toDeclare FROM ingredient_compounds WHERE ing = '$ingID'");
while($res = mysqli_fetch_array($q)){
    $compos[] = $res;
}

foreach ($compos as $compo) {
	 
	$chkIFRA = mysqli_fetch_array(mysqli_query($conn, "SELECT ifra_key, $defCatClass, risk FROM IFRALibrary WHERE cas = '".$compo['cas']."'"));
	
    $r = [
        'id' => (int)$compo['id'],
        'ing' => (string)$compo['ing'],
        'name' => (string)$compo['name'],
        'cas' => (string)($compo['cas'] ?: 'N/A'),
        'ec' => (string)($compo['ec'] ?: 'N/A'),
        'GHS' => (string)($compo['GHS'] ?: '-'),
        'min_percentage' =>  (float)($compo['min_percentage'] ?: 0),
		'max_percentage' =>  (float)($compo['max_percentage'] ?: 0),
		'avg_percentage' =>  $compo['min_percentage'] + $compo['max_percentage'] / 2,
        'toDeclare' => (int)($compo['toDeclare'] ?: 0),
        'isIFRA' => 0
    ];

    if ($chkIFRA && isset($chkIFRA[$defCatClass])) {
        $r['isIFRA'] = 1;
        $r['IFRA'] = (float)$chkIFRA[$defCatClass];
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

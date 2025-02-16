<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$defCatClass = $settings['defCatClass'];
$defPercentage = $settings['defPercentage'];

$ingID = base64_decode($_GET["id"]);

$stmt = $conn->prepare("SELECT id,ing,name,cas,ec,min_percentage,max_percentage,GHS,toDeclare FROM ingredient_compounds WHERE ing = ? AND owner_id = ?");
$stmt->bind_param("ss", $ingID, $userID);
$stmt->execute();
$result = $stmt->get_result();
while($res = $result->fetch_array(MYSQLI_ASSOC)){
    $compos[] = $res;
}
$stmt->close();

foreach ($compos as $compo) {
    $stmt = $conn->prepare("SELECT ifra_key, $defCatClass, risk FROM IFRALibrary WHERE cas = ? AND owner_id = ?");
    $stmt->bind_param("ss", $compo['cas'], $userID);
    $stmt->execute();
    $chkIFRA = $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
    $stmt->close();
	
    $r = [
        'id' => (int)$compo['id'],
        'ing' => (string)$compo['ing'],
        'name' => (string)$compo['name'],
        'cas' => (string)($compo['cas'] ?: '-'),
        'ec' => (string)($compo['ec'] ?: '-'),
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

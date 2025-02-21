<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = $_POST['start'] ?: 0;
$limit = $_POST['length'] ?: 10;
$defBtlSize = $_POST['btlSize'] ?: 100;

$order_by  = $_POST['order_by'] ?: 'name';
$order  = $_POST['order_as'] ?: 'ASC';
$extra = "ORDER BY ".$order_by." ".$order;

$s = trim(mysqli_real_escape_string($conn,$_POST['search']['value']));
$t = 'inventory_compounds';

if($s != ''){
   $f = "WHERE 1 AND (name LIKE '%".$s."%') AND owner_id = '$userID'";
}

$q = mysqli_query($conn, "SELECT * FROM $t $f $extra LIMIT $row, $limit");

while($res = mysqli_fetch_array($q)){
    $rs[] = $res;
}

function calculateBottles($totalVolume, $alcoholPercentage, $defBtlSize) {
    $alcoholVolume = $defBtlSize * ($alcoholPercentage / 100); // volume of alcohol in each bottle
    $numBottles = floor($totalVolume / $alcoholVolume); // total number of bottles
    return $numBottles;
}


$pt = mysqli_query($conn, "SELECT id,name,concentration,description FROM perfumeTypes WHERE owner_id = '$userID' ");
while($rt = mysqli_fetch_array($pt)){
    $types[] = $rt;
}

$rx = [];

foreach ($rs as $rq) { 
    $r['id'] = (int)$rq['id'];
    $r['name'] = (string)$rq['name'] ?: '-';
    $r['description'] = (string)$rq['description'] ?: '-';
    $r['batch_id'] = (int)$rq['batch_id'];
    $r['size'] = (double)$rq['size'] ?: 0;
    $r['updated_at'] = (string)$rq['updated_at'] ?: '00:00:00';
    $r['created_at'] = (string)$rq['created_at'] ?: '00:00:00';
    $r['label_info'] = (string)$rq['label_info'] ?: '-';
    $r['location'] = (string)$rq['location'] ?: '-';
    $r['owner_id'] = (int)$rq['owner_id'] ?: 0;
	$r['btlSize'] = (double)$defBtlSize;
    
	$rt = [];
    foreach ($types as $type) {
        $bottleSize = $r['size']; // Bottle size calculated here
        $rt[] = [
            'name' => $type['name'],
            'concentration' => $type['concentration'],
            'bottles_total' => calculateBottles($bottleSize, $type['concentration'], $defBtlSize)
        ];
    }
    
    $r['breakDown'] = $rt;
    $rx[] = $r;
}

$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM $t WHERE owner_id = '$userID'"));
$filtered = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM $t ".$f));

$response = array(
  "draw" => (int)$_POST['draw'],
  "recordsTotal" => (int)$total['entries'],
  "recordsFiltered" => (int)$filtered['entries'],
  "data" => $rx
);

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

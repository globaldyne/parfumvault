<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = $_POST['start'] ?: 0;
$limit = $_POST['length'] ?: 10;

$order_by  = $_POST['order_by'] ?: 'product_name';
$order  = $_POST['order_as'] ?: 'ASC';
$extra = "ORDER BY ".$order_by." ".$order;

$s = trim($_POST['search']['value']);
$t = 'sds_data';

if($s != ''){
   $f = "WHERE 1 AND (product_name LIKE '%".$s."%')";
}
$q = mysqli_query($conn, "SELECT * FROM $t $f $extra LIMIT $row, $limit");
while($res = mysqli_fetch_array($q)){
    $rs[] = $res;
}




$rx = [];

foreach ($rs as $rq) { 
    $r['id'] = (int)$rq['id'];
    $r['product_name'] = (string)$rq['product_name'];
    $r['product_use'] = (string)$rq['product_use'];
    $r['country'] = (string)$rq['country'];
	$r['language'] = (string)$rq['language'];
    $r['product_type'] = (string)$rq['product_type'];
    $r['state_type'] = (string)$rq['state_type'];
    $r['supplier_id'] = (int)$rq['supplier_id'];
    $r['docID'] = (int)$rq['id'];

    $r['updated'] = (string)$rq['updated'] ?: '00:00:00';
    $r['created'] = (string)$rq['created'] ?: '00:00:00';
    
    $rx[] = $r;
}

$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM $t"));
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

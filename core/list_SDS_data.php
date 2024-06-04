<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = $_POST['start'] ?: 0;
$limit = $_POST['length'] ?: 10;

$order_by  = $_POST['order_by'] ?: 'name';
$order  = $_POST['order_as'] ?: 'ASC';
$extra = "ORDER BY ".$order_by." ".$order;

$s = trim($_POST['search']['value']);
$t = 'documents';

if($s != ''){
   $f = "AND (name LIKE '%".$s."%')";
}
$q = mysqli_query($conn, "SELECT id,name,ownerID,notes,updated,created FROM $t WHERE isSDS='1' $f $extra LIMIT $row, $limit");
while($res = mysqli_fetch_array($q)){
    $rs[] = $res;
}




$rx = [];

foreach ($rs as $rq) { 
    $r['id'] = (int)$rq['id'];
    $r['name'] = (string)$rq['name'] ?: 'N/A';
    $r['ownerID'] = (int)$rq['ownerID'];
    $r['description'] = (string)$rq['notes'] ?: 'N/A';
    $r['updated'] = (string)$rq['updated'] ?: '00:00:00';
    $r['created'] = (string)$rq['created'] ?: '00:00:00';
    
    $rx[] = $r;
}

$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM $t WHERE isSDS='1'"));
$filtered = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM $t WHERE isSDS='1' ".$f));

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

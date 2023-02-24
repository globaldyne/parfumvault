<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');


$row = $_POST['start']?:0;
$limit = $_POST['length']?:10;


$order_by  = $_POST['order_by']?:'name';
$order  = $_POST['order_as']?:'ASC';
$extra = "ORDER BY ".$order_by." ".$order;

$s = trim($_POST['search']['value']);
$t = "templates";

if($s != ''){
   $f = "WHERE 1 AND (name LIKE '%".$s."%')";
}
$q = mysqli_query($conn, "SELECT * FROM $t $f $extra LIMIT $row, $limit");
while($res = mysqli_fetch_array($q)){
    $rs[] = $res;
}

foreach ($rs as $rq) { 
	$r['id'] = (int)$rq['id'];
	$r['name'] = (string)$rq['name'];
	$r['content'] = (string)$rq['content'];
	$r['created'] = (string)$rq['created'];
	$r['updated'] = (string)$rq['updated'];
	$r['description'] = (string)$rq['description'];

	$rx[]=$r;
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

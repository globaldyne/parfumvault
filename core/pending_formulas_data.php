<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

$meta = $_GET['meta'];

$row = $_POST['start']?:0;
$limit = $_POST['length']?:10;


$order_by  = $_POST['order_by']?:'name';
$order  = $_POST['order_as']?:'ASC';
$extra = "ORDER BY ".$order_by." ".$order;

$s = trim($_POST['search']['value']);
$t = "makeFormula";

if($s != ''){
   $f = "WHERE 1 AND (name LIKE '%".$s."%')";
}
if($meta == 0){
	$q = mysqli_query($conn, "SELECT * FROM $t $f $extra LIMIT $row, $limit");
	while($res = mysqli_fetch_array($q)){
    	$rs[] = $res;
	}

	foreach ($rs as $rq) { 
		$r['id'] = (int)$rq['id'];
		$r['fid'] = (string)$rq['fid'];
		$r['name'] = (string)$rq['name'];
		$r['ingredient'] = (string)$rq['ingredient'];
		$r['concentration'] = (float)$rq['concentration'];
		$r['dilutant'] = (string)$rq['dilutant'] ?: 'None';
		$r['quantity'] = (float)$rq['quantity'];
		$r['toAdd'] = (int)$rq['toAdd'];

		$rx[]=$r;
	}

}else{
	$q = mysqli_query($conn, "SELECT fid, name, SUM(toadd) AS toAdd FROM $t GROUP BY name $f $extra LIMIT $row, $limit");
	while($res = mysqli_fetch_array($q)){
    	$rs[] = $res;
	}

	foreach ($rs as $rq) { 
		$r['id'] = (int)$rq['id'];
		$r['fid'] = (string)$rq['fid'];
		$r['name'] = (string)$rq['name'];
		$r['toAdd'] = (int)$rq['toAdd'];

		$rx[]=$r;
	}
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

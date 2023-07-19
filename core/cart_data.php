<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/getIngSupplier.php');

$row = $_POST['start']?:0;
$limit = $_POST['length']?:10;


$order_by  = $_POST['order_by']?:'name';
$order  = $_POST['order_as']?:'ASC';
$extra = "ORDER BY ".$order_by." ".$order;

$s = trim($_POST['search']['value']);
$t = "cart";

if($s != ''){
   $f = "WHERE 1 AND (name LIKE '%".$s."%')";
}

$q = mysqli_query($conn, "SELECT * FROM $t $f $extra LIMIT $row, $limit");
while($res = mysqli_fetch_array($q)){
    $rs[] = $res;
}

foreach ($rs as $rq) { 
	$r['id'] = (int)$rq['id'];
	$r['name'] = (string)$rq['name'] ?: 'N/A';
	$r['quantity'] = (float)$rq['quantity'] ?: 0;
	$r['purity'] = (float)$rq['purity'] ?: 0;
	
	if($a = getIngSupplier($rq['ingID'],0,$conn)){ 
		$j = 0;
		unset($r['supplier']);
		foreach ($a as $b){
			$r['supplier'][$j]['name'] = (string)$b['name'];
			$r['supplier'][$j]['link'] = (string)$b['supplierLink'];
			$j++;
		}
	}else{
		$r['supplier'] = null;
	}	
	
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

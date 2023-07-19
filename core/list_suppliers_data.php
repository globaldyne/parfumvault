<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

$row = $_POST['start']?:0;
$limit = $_POST['length']?:20;

$order_by  = $_POST['order_by']?:'name';
$order  = $_POST['order_as']?:'ASC';
$extra = "ORDER BY ".$order_by." ".$order;

$s = trim($_POST['search']['value']);
$t = "ingSuppliers";

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
	$r['address'] = (string)$rq['address'];
	$r['po'] = (string)$rq['po']?:'N/A';
	$r['country'] = (string)$rq['country'];
	$r['telephone'] = (string)$rq['telephone']?:'N/A';
	$r['url'] = (string)$rq['url']?:'N/A';
	$r['email'] = (string)$rq['email']?:'N/A';
 	$r['platform'] = (string)$rq['platform']?:'N/A';
	$r['price_tag_start'] = (string)base64_encode($rq['price_tag_start']?:'N/A');
 	$r['price_tag_end'] = (string)base64_encode($rq['price_tag_end']?:'N/A');
 	$r['add_costs'] = (double)$rq['add_costs']?:0;
 	$r['notes'] = (string)$rq['notes']?:'N/A';
 	$r['min_ml'] = (double)$rq['min_ml']?:0;
 	$r['min_gr'] = (double)$rq['min_gr']?:0;
	if($rq['price_per_size'] == '0'){
		$r['price_per_size'] = (string)'Product';
	}else{
		$r['price_per_size'] = (string)'Volume';
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
<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = $_POST['start']?:0;
$limit = $_POST['length']?:10;

$order_by  = $_POST['order_by']?:'created';
$order  = $_POST['order_as']?:'ASC';
$extra = "ORDER BY ".$order_by." ".$order;

$defCatClass = $settings['defCatClass'];
$defImage = base64_encode(file_get_contents(__ROOT__.'/img/pv_molecule.png'));

$s = trim($_POST['search']['value']);

if($s != ''){
   $f = "WHERE 1 AND (id LIKE '%".$s."%')";
}

$q = mysqli_query($conn, "SELECT * FROM batchIDHistory $f $extra LIMIT $row, $limit");
while($res = mysqli_fetch_array($q)){
    $rs[] = $res;
}

foreach ($rs as $rq) { 
	
	if(file_exists(__ROOT__.'/'.$rq['pdf']) === TRUE){
		$s = 1;
	}
	
	$r['id'] = (string)$rq['id'];
	$r['fid'] = (string)$rq['fid'];
	$r['product_name'] = (string)$rq['product_name']?:'N/A';
	$r['pdf'] = (string)$rq['pdf'];
	$r['state'] = (int)$s?:0;
	$r['created'] = (string)$rq['created'];

	$rx[]=$r;
}
$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM batchIDHistory"));
$filtered = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM batchIDHistory ".$f));

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

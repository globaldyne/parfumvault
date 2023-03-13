<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/ml2L.php');
require_once(__ROOT__.'/func/countElement.php');

$meta = $_GET['meta'];

$row = $_POST['start']?:0;
$limit = $_POST['length']?:10;


$order_by  = "toAdd DESC, ".$_POST['order_by']?:'ingredient';
$order  = $_POST['order_as']?:'ASC';
$extra = "ORDER BY ".$order_by." ".$order;

$s = trim($_POST['search']['value']);
$t = "makeFormula";


if($meta == 0){
	if($s != ''){
 	  $f = "  AND (ingredient LIKE '%".$s."%')";
	}
	$q = mysqli_query($conn, "SELECT * FROM $t WHERE fid = '".$_GET['fid']."' $f $extra LIMIT $row, $limit");
	while($res = mysqli_fetch_array($q)){
    	$rs[] = $res;
	}

	$q = mysqli_query($conn, "SELECT quantity FROM $t WHERE fid = '".$_GET['fid']."'");
	while($res = mysqli_fetch_array($q)){
    	$rsq[] = $res;
	}
	
	$q = mysqli_query($conn, "SELECT quantity FROM $t WHERE fid = '".$_GET['fid']."' AND toAdd = '1'");
	while($res = mysqli_fetch_array($q)){
    	$rsL[] = $res;
	}
	
	foreach ($rs as $rq) {
		$gING = mysqli_fetch_array(mysqli_query($conn, "SELECT id,cas FROM ingredients WHERE name = '".$rq['ingredient']."'"));

		$r['id'] = (int)$rq['id'];
		$r['fid'] = (string)$rq['fid'];
		$r['name'] = (string)$rq['name'];
		$r['ingredient'] = (string)$rq['ingredient'];		
		$r['ingID'] = (int)$gING['id'];
		$r['cas'] = (string)$gING['cas']?:'N/A';

		$r['concentration'] = (float)$rq['concentration'];
		$r['dilutant'] = (string)$rq['dilutant'] ?: 'None';
		$r['quantity'] = number_format((float)$rq['quantity'], $settings['qStep'],'.', '') ?: 0;
		$r['originalQuantity'] = number_format((float)$rq['originalQuantity'], $settings['qStep'],'.', '') ?: 0;
		$r['overdose'] = number_format((float)$rq['overdose'], $settings['qStep'],'.', '') ?: 0;

		$r['toAdd'] = (int)$rq['toAdd'];
		
		
		
		$rx[]=$r;
	}
	foreach ($rsq as $rq) {
		$mg['total_mg'] += $rq['quantity'];
	}
	
	foreach ($rsL as $rq) {
		$mg['total_mg_left'] += $rq['quantity'];
	}
	
	$m['total_ingredients'] = (int)countElement("$t WHERE fid = '".$_GET['fid']."'",$conn);
	$m['total_ingredients_left'] = (int)countElement("$t WHERE fid = '".$_GET['fid']."' AND toAdd = '1'",$conn);	
	$m['total_quantity'] =  (float)ml2l($mg['total_mg'], $settings['qStep'], $settings['mUnit']);
	$m['total_quantity_left'] =  (float)ml2l($mg['total_mg_left'], $settings['qStep'], $settings['mUnit']);
	$m['quantity_unit'] = (string)$settings['mUnit'];
	
}else{
	if($s != ''){
 	  $f = "  AND (name LIKE '%".$s."%')";
	}
	$q = mysqli_query($conn, "SELECT id, fid, name, madeOn, toDo AS toAdd FROM formulasMetaData WHERE toDo = '1' $f $extra LIMIT $row, $limit");
	

	while($res = mysqli_fetch_array($q)){
    	$rs[] = $res;
	}
	
	foreach ($rs as $rq) { 
		$r['id'] = (int)$rq['id'];
		$r['fid'] = (string)$rq['fid'];
		$r['name'] = (string)$rq['name'];
		$q2 = mysqli_fetch_array(mysqli_query($conn, "SELECT toAdd FROM $t WHERE fid = '".$rq['fid']."'"));
		$r['total_ingredients'] = (int)countElement("$t WHERE fid = '".$rq['fid']."'",$conn);
		$r['total_ingredients_left'] = (int)countElement("$t WHERE fid = '".$rq['fid']."' AND toAdd = '1'",$conn);	
		$r['toAdd'] = (int)$q2['toAdd'];
		$r['madeOn'] = (string)$rq['madeOn'] ?: 'In progress';

		$rx[]=$r;
	}
}

$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM formulasMetaData WHERE toDo = '1'"));
$filtered = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM formulasMetaData WHERE todo = '1' ".$f));

$response = array(
  "draw" => (int)$_POST['draw'],
  "recordsTotal" => (int)$total['entries'],
  "recordsFiltered" => (int)$filtered['entries'],
  "data" => $rx
);

if(empty($r)){
	$response['data'] = [];
}


if($m){
	$response['meta'] = $m;
}else{
	$response['meta'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

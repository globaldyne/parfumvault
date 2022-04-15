<?php
require('../inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/countElement.php');


$row = $_POST['start']?:0;
$limit = $_POST['length']?:10;
$order_by  = $_POST['order_by']?:'name';
$order  = $_POST['order_as']?:'ASC';

$extra = "ORDER BY ".$order_by." ".$order;

$cats_q = mysqli_query($conn, "SELECT id,name,description,type FROM IFRACategories ORDER BY id ASC");
while($cats_res = mysqli_fetch_array($cats_q)){
    $cats[] = $cats_res;
}


if($_GET['filter'] && $_GET['profile'] || $_GET['sex']){
	$f = "WHERE profile = '".$_GET['profile']."' OR sex = '".$_GET['sex']."'";
}
$s = trim($_POST['search']['value']);

if($s != ''){
   $f = "WHERE 1 AND (name LIKE '%".$s."%' OR product_name LIKE '%".$s."%' OR notes LIKE '%".$s."%')";
}

$formulas = mysqli_query($conn, "SELECT id,fid,name,product_name,isProtected,profile,sex,created,catClass,isMade,madeOn,status FROM formulasMetaData $f $extra LIMIT $row, $limit");

while ($allFormulas = mysqli_fetch_array($formulas)){
	    $formula[] = $allFormulas;
}
foreach ($formula as $formula) { 
	$r['id'] = (int)$formula['id'];
	$r['fid'] = (string)$formula['fid'];
	$r['product_name'] = (string)$formula['product_name']?:'N/A';
	$r['name'] = (string)$formula['name'];
	$r['isProtected'] = (int)$formula['isProtected'];
	$r['profile'] = (string)$formula['profile']?:'N/A';
	$r['sex'] = (string)$formula['sex']?:'N/A';
	$r['created'] = (string)$formula['created'];
	$r['catClass'] = (string)$formula['catClass']?:'N/A';
	$r['ingredients'] = (int)countElement("formulas WHERE fid = '".$formula['fid']."'",$conn)?:'0';
	$r['isMade'] = (int)$formula['isMade']?:0;
	$r['madeOn'] = (string)$formula['madeOn']?:'N/A';
	$r['status'] = (int)$formula['status']?:'0';

	$rx[]=$r;
	
}

$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM formulasMetaData"));
$filtered = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM formulasMetaData ".$f));

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

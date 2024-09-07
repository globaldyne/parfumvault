<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$order_by = isset($_POST['order_by']) ? mysqli_real_escape_string($conn, $_POST['order_by']) : 'revisionDate';
$order = isset($_POST['order_as']) && in_array(strtoupper($_POST['order_as']), ['ASC', 'DESC']) ? strtoupper($_POST['order_as']) : 'ASC';

$extra = "ORDER BY ".$order_by." ".$order;

$current_rev = mysqli_fetch_array(mysqli_query($conn, "SELECT id,revision FROM formulasMetaData WHERE fid = '".$_GET['fid']."'"));

$f = "WHERE fid = '".$_GET['fid']."' GROUP BY revision";
$q = "SELECT id,name,fid,revision,revisionDate,revisionMethod FROM formulasRevisions $f $extra LIMIT $row, $limit";

$sql = mysqli_query($conn, $q);

while($res = mysqli_fetch_array($sql)){
    $revs[] = $res;
}
$i=0;
foreach ($revs as $rev) { 
	$r['id'] = (int)$rev['id'];
	$r['fid'] = (string)$rev['fid'];
	$r['name'] = (string)$rev['name'];
	$r['revision'] = (int)$rev['revision'];
	$r['revisionDate'] = (string)$rev['revisionDate'];
	$r['revisionMethod'] = (string)$rev['revisionMethod'] ?: '-';
	if($r['revision'] == $current_rev['revision']){
		$r['isCurrent'] = (bool)true;
	}else{
		$r['isCurrent'] = (bool)false;
	}
	$r['formulaID'] = (int)$current_rev['id'];

	$i++;

	$rx[]=$r;
}

$response = array(
  "draw" => (int)$_POST['draw'],
  "recordsTotal" => (int)$i,
  "debug" => $q,
  "data" => $rx
);

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

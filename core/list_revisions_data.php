<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = $_POST['start']?:0;
$limit = $_POST['length']?:10;
$order_by  = $_POST['order_by']?:'revisionDate';
$order  = $_POST['order_as']?:'ASC';
$extra = "ORDER BY ".$order_by." ".$order;

$current_rev = mysqli_fetch_array(mysqli_query($conn, "SELECT id,revision FROM formulasMetaData WHERE fid = '".$_GET['fid']."'"));

$f = "WHERE fid = '".$_GET['fid']."' GROUP BY revision";
$q = mysqli_query($conn, "SELECT id,name,fid,revision,revisionDate FROM formulasRevisions $f $extra LIMIT $row, $limit");
while($res = mysqli_fetch_array($q)){
    $revs[] = $res;
}
$i=0;
foreach ($revs as $rev) { 
	$r['id'] = (int)$rev['id'];
	$r['fid'] = (string)$rev['fid'];
	$r['name'] = (string)$rev['name'];
	$r['revision'] = (int)$rev['revision'];
	$r['revisionDate'] = (string)$rev['revisionDate'];
	if($r['revision'] == $current_rev['revision']){
		$r['isCurrent'] = (bool)true;
		
	}else{
		$r['isCurrent'] = (bool)false;
	}
	$r['formulaID'] = (int)$current_rev['id'];

	$i++;

	$rx[]=$r;
}
$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM formulasRevisions ".$f));
$filtered = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM formulasRevisions ".$f));

$response = array(
  "draw" => (int)$_POST['draw'],
  "recordsTotal" => (int)$i,
  "recordsFiltered" => (int)$i,
  "data" => $rx
);

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

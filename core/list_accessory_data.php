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

$defCatClass = $settings['defCatClass'];
$defImage = base64_encode(file_get_contents(__ROOT__.'/img/pv_molecule.png'));

$s = trim($_POST['search']['value']);
$safe_search = mysqli_real_escape_string($conn, $s);

if($safe_search != ''){
	$f = " WHERE 1 AND (name LIKE '%".$safe_search."%') AND owner_id = '$userID'";
} else {
	$f = " WHERE owner_id = '$userID'";
}

$q = mysqli_query($conn, "SELECT * FROM inventory_accessories $f $extra LIMIT $row, $limit");
while($res = mysqli_fetch_array($q)){
    $rs[] = $res;
}

foreach ($rs as $rq) { 
	$r['id'] = (int)$rq['id'];
	$r['name'] = (string)$rq['name'] ?: 'N/A';
	$r['price'] = (double)$rq['price'] ?: 'N/A';
	$r['accessory'] = (string)$rq['accessory'];
	$r['supplier'] = (string)$rq['supplier'] ?: 'N/A';
	$r['supplier_link'] = (string)$rq['supplier_link'] ?: 'N/A';
	$r['pieces'] = (int)$rq['pieces'] ?: 0;
	
	$photo = mysqli_fetch_array(mysqli_query($conn,"SELECT docData FROM documents WHERE type = '5'  AND owner_id = '$userID' AND ownerID = '".$r['id']."'"));
 	$r['photo'] = (string)$photo['docData']?:'data:image/png;base64,'.$defImage;

	$rx[]=$r;
}
$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM inventory_accessories WHERE owner_id = '$userID'"));
$filtered = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM inventory_accessories ".$f));

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

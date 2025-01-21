<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = $_POST['start']?:0;
$limit = $_POST['length']?:10;
$order_by  = $_POST['order_by']?:'name';
$order  = $_POST['order_as']?:'ASC';
$extra = "ORDER BY ".$order_by." ".$order;

$defCatClass = $settings['defCatClass'];
$defImage = base64_encode(file_get_contents(__ROOT__.'/img/pv_molecule.png'));

$s = trim($_POST['search']['value']);

if($s != ''){
	$f = "WHERE 1 AND (name LIKE '%".$s."%' OR cas LIKE '%".$s."%' OR synonyms LIKE '%".$s."%' OR risk LIKE '%".$s."%')  AND owner_id = '$userID'";
} else {
	$f = "WHERE 1 AND owner_id = '$userID'";
}

$q = mysqli_query($conn, "SELECT * FROM IFRALibrary $f $extra LIMIT $row, $limit");
while($res = mysqli_fetch_array($q)){
    $ifra[] = $res;
}

foreach ($ifra as $IFRA) { 
	$r['id'] = (int)$IFRA['id'];
	$r['ifra_key'] = (string)$IFRA['ifra_key']?:'-';
	$r['amendment'] = (int)$IFRA['amendment']?:0;
	$r['prev_pub'] = (string)$IFRA['prev_pub']?:'-';
	$r['last_pub'] = (string)$IFRA['last_pub']?:'-';
	$r['deadline_existing'] = (string)$IFRA['deadline_existing']?:'-';
	$r['deadline_new'] = (string)$IFRA['deadline_new']?:'-';
	$r['name'] = (string)$IFRA['name']?:'-';
	$r['cas'] = (string)$IFRA['cas']?:'-';
	$r['cas_comment'] = (string)$IFRA['cas_comment']?:'-';
	$r['synonyms'] = (string)$IFRA['synonyms']?:'-';
	$r['formula'] = (string)$IFRA['formula']?:'-';
	$r['flavor_use'] = (string)$IFRA['flavor_use']?:'-';
	$r['prohibited_notes'] = (string)$IFRA['prohibited_notes']?:'-';
	$r['restricted_photo_notes'] = (string)$IFRA['restricted_photo_notes']?:'-';
	$r['restricted_notes'] = (string)$IFRA['restricted_notes']?:'-';
	$r['specified_notes'] = (string)$IFRA['specified_notes']?:'-';
	$r['type'] = (string)$IFRA['type']?:'-';
	$r['risk'] = (string)$IFRA['risk']?:'-';
	$r['contrib_others'] = (string)$IFRA['contrib_others']?:'-';
	$r['contrib_others_notes'] = (string)$IFRA['contrib_others_notes']?:'-';
	$r['cat1'] = (float)$IFRA['cat1'];
	$r['cat2'] = (float)$IFRA['cat2'];
	$r['cat3'] = (float)$IFRA['cat3'];
	$r['cat4'] = (float)$IFRA['cat4'];
	$r['cat5A'] = (float)$IFRA['cat5A'];
	$r['cat5B'] = (float)$IFRA['cat5B'];
	$r['cat5C'] = (float)$IFRA['cat5C'];
	$r['cat5D'] = (float)$IFRA['cat5D'];
	$r['cat6'] = (float)$IFRA['cat6'];
	$r['cat7A'] = (float)$IFRA['cat7A'];
	$r['cat7B'] = (float)$IFRA['cat7B'];
	$r['cat8'] = (float)$IFRA['cat8'];
	$r['cat9'] = (float)$IFRA['cat9'];
	$r['cat10A'] = (float)$IFRA['cat10A'];
	$r['cat10B'] = (float)$IFRA['cat10B'];
	$r['cat11A'] = (float)$IFRA['cat11A'];
	$r['cat11B'] = (float)$IFRA['cat11B'];
	$r['cat12'] = (float)$IFRA['cat12'];

	$r['defCat']['class'] = (string)$defCatClass?:'-';
	$r['defCat']['limit'] = (float)$IFRA[$defCatClass]?:'0';
 	$r['image'] = (string)$IFRA['image']?:$defImage;


	$rx[]=$r;
}
$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM IFRALibrary WHERE owner_id = '$userID' "));
$filtered = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM IFRALibrary ".$f));

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

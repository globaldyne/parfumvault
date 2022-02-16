<?php
require('../inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$row = $_POST['start']?:0;
$limit = $_POST['length']?:10;

$defCatClass = $settings['defCatClass'];

$s = trim($_POST['search']['value']);

if($s != ''){
   $f = "WHERE 1 AND (name LIKE '%".$s."%' OR cas LIKE '%".$s."%' OR synonyms LIKE '%".$s."%')";
}

$q = mysqli_query($conn, "SELECT * FROM IFRALibrary $f LIMIT $row, $limit");
while($res = mysqli_fetch_array($q)){
    $ifra[] = $res;
}

foreach ($ifra as $IFRA) { 
	$r['id'] = (int)$IFRA['id'];
	$r['ifra_key'] = (string)$IFRA['ifra_key']?:'N/A';
	$r['image'] = (string)$IFRA['image']?:'N/A';
	$r['amendment'] = (int)$IFRA['amendment']?:0;
	$r['prev_pub'] = (string)$IFRA['prev_pub']?:'N/A';
	$r['last_pub'] = (string)$IFRA['last_pub']?:'N/A';
	$r['deadline_existing'] = (string)$IFRA['deadline_existing']?:'N/A';
	$r['deadline_new'] = (string)$IFRA['deadline_new']?:'N/A';
	$r['name'] = (string)$IFRA['name']?:'N/A';
	$r['cas'] = (string)$IFRA['cas']?:'N/A';
	$r['cas_comment'] = (string)$IFRA['cas_comment']?:'N/A';
	$r['synonyms'] = (string)$IFRA['synonyms']?:'N/A';
	$r['formula'] = (string)$IFRA['formula']?:'N/A';
	$r['flavor_use'] = (string)$IFRA['flavor_use']?:'N/A';
	$r['prohibited_notes'] = (string)$IFRA['prohibited_notes']?:'N/A';
	$r['restricted_photo_notes'] = (string)$IFRA['restricted_photo_notes']?:'N/A';
	$r['restricted_notes'] = (string)$IFRA['restricted_notes']?:'N/A';
	$r['specified_notes'] = (string)$IFRA['specified_notes']?:'N/A';
	$r['type'] = (string)$IFRA['type']?:'N/A';
	$r['risk'] = (string)$IFRA['risk']?:'N/A';
	$r['contrib_others'] = (string)$IFRA['contrib_others']?:'N/A';
	$r['contrib_others_notes'] = (string)$IFRA['contrib_others_notes']?:'N/A';
	$r['cat1'] = (float)$IFRA['cat1']?:'N/A';
	$r['cat2'] = (float)$IFRA['cat2']?:'N/A';
	$r['cat3'] = (float)$IFRA['cat3']?:'N/A';
	$r['cat4'] = (float)$IFRA['cat4']?:'N/A';
	$r['cat5A'] = (float)$IFRA['cat5A']?:'N/A';
	$r['cat5B'] = (float)$IFRA['cat5B']?:'N/A';
	$r['cat5C'] = (float)$IFRA['cat5C']?:'N/A';
	$r['cat5D'] = (float)$IFRA['cat5D']?:'N/A';
	$r['cat6'] = (float)$IFRA['cat6']?:'N/A';
	$r['cat7A'] = (float)$IFRA['cat7A']?:'N/A';
	$r['cat7B'] = (float)$IFRA['cat7B']?:'N/A';
	$r['cat8'] = (float)$IFRA['cat8']?:'N/A';
	$r['cat9'] = (float)$IFRA['cat9']?:'N/A';
	$r['cat10A'] = (float)$IFRA['cat10A']?:'N/A';
	$r['cat10B'] = (float)$IFRA['cat10B']?:'N/A';
	$r['cat11A'] = (float)$IFRA['cat11A']?:'N/A';
	$r['cat11B'] = (float)$IFRA['cat11B']?:'N/A';
	$r['cat12'] = (float)$IFRA['cat12']?:'N/A';

	$r['defCat']['class'] = (string)$defCatClass?:'N/A';
	$r['defCat']['limit'] = (float)$IFRA[$defCatClass]?:'0';

	$rx[]=$r;
}
$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM IFRALibrary"));
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

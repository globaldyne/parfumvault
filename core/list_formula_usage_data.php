<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/validateFormula.php');
require_once(__ROOT__.'/func/searchIFRA.php');

$fid = $_POST['fid'];

$categories = [
    'cat1', 'cat2', 'cat3', 'cat4', 'cat5A', 'cat5B', 'cat5C', 'cat5D',
    'cat6', 'cat7A', 'cat7B', 'cat8', 'cat9', 'cat10A', 'cat11A', 'cat11B', 'cat12'
];

$m = [];
$mg['total_mg'] = 0;
$formula_q = mysqli_query($conn, "SELECT concentration,quantity,exclude_from_calculation FROM formulas WHERE fid = '".$fid."' AND owner_id = '$userID'");
while ($formula = mysqli_fetch_array($formula_q)){
	    $form[] = $formula;
		if ( $formula['exclude_from_calculation'] != 1 ){
			$mg['total_mg'] += $formula['quantity'];
		}
}
foreach ($categories as $category) {
    $lastValAccepted = null;

    for ($c = 1; $c <= 100; $c++) {
        $result = validateFormula($fid, 100, $c, $mg['total_mg'], $category, $settings['qStep'], 1);

        if ($result === 0) {
            $lastValAccepted = $c;
        } else {
            break;
        }
    }

    if ($lastValAccepted !== null) {
        $m[$category] = $lastValAccepted;
    } else {
        $m[$category] = '-';
    }
}
$data = [];
$data[] = $m;

$response = array(
  "draw" => (int)$_POST['draw'],
  "recordsTotal" => 1,
  "recordsFiltered" => 1,
  "data" => $data
);

if(empty($m)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

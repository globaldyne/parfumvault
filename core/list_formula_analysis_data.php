<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/calcPerc.php');

$defCatClass = $settings['defCatClass'];

$q = mysqli_query($conn, "SELECT ingredient, quantity FROM formulas WHERE fid = '".$_POST["fid"]."'");
$total_quantity = 0;
$formula_data = array();

while ($rq = mysqli_fetch_array($q)) {
    $formula_data[] = $rq;
    $total_quantity += $rq['quantity']; // Calculate total quantity
}

$get_data_ings = array();
foreach ($formula_data as $data) {
    $q2 = mysqli_query($conn, "SELECT id, ing, name, cas, min_percentage, max_percentage FROM ingredient_compounds WHERE ing = '".$data['ingredient']."'");
    
    while ($res = mysqli_fetch_array($q2)) {
        $res['quantity'] = $data['quantity']; // Include quantity in the ingredient data
        $get_data_ings[] = $res;
    }
}

$response = array();
foreach ($get_data_ings as $get_data_ing) {
    $r = array();
    $r['main_ing'] = (string)$get_data_ing['ing'];
    $r['sub_ing'] = (string)$get_data_ing['name'];
    $r['cas'] = (string)$get_data_ing['cas'] ?: 'N/A';
    $r['min_percentage'] = (float)$get_data_ing['min_percentage'] ?: 0;
    $r['max_percentage'] = (float)$get_data_ing['max_percentage'] ?: 0;
    $r['avg_percentage'] = ($r['min_percentage'] + $r['max_percentage']) / 2;

    $r['formula_percentage'] = ($get_data_ing['quantity'] / $total_quantity) * 100;

    $conc_p = number_format(($r['avg_percentage'] / 100 * $get_data_ing['quantity'] * $r['formula_percentage']  ) / 100, 5);
	if($settings['multi_dim_perc'] == '1'){

		$conc_p   += multi_dim_perc($conn, $formula_data, $get_data_ing['cas'], $settings['qStep'], $settings['defPercentage'])[$cas['cas']];
	}
	
	$r['contained_percentage'] = $conc_p;
    $u = searchIFRA($get_data_ing['cas'], $get_data_ing['name'], null, $defCatClass);

    $r['max_allowed_val'] = $u['val'] ?: 'No value';
    $r['max_allowed_reason'] = $u['risk'];

    $response['data'][] = $r;
}

if (empty($response['data'])) {
    $response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>
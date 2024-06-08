<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/searchIFRA.php');

$defCatClass = $settings['defCatClass'];

	$q = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '".$_POST["fid"]."'");
	while($rq = mysqli_fetch_array($q)){
		
		$q2 = mysqli_query($conn, "SELECT id,ing,name,cas,min_percentage,max_percentage FROM ingredient_compounds WHERE ing = '".$rq['ingredient']."'");

	while($res = mysqli_fetch_array($q2)){
			$get_data_ings[] = $res;
		}
	}
	
	foreach ($get_data_ings as $get_data_ing) { 
		$r['main_ing'] = (string)$get_data_ing['ing'];
		$r['sub_ing'] = (string)$get_data_ing['name'];
		$r['cas'] = (string)$get_data_ing['cas'] ?: 'N/A';
		$r['min_percentage'] = (float)$get_data_ing['min_percentage'] ?: 0;
		$r['max_percentage'] = (float)$get_data_ing['max_percentage'] ?: 0;

    	$r['avg_percentage'] = ($r['min_percentage'] + $r['max_percentage']) / 2;
		
		$u = explode(' - ',searchIFRA($get_data_ing['cas'],$get_data_ing['name'],null,$conn,$defCatClass));
		$r['max_allowed'] = $u[0];
		
		
		$response['data'][] = $r;
	}



if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

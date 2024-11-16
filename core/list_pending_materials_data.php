<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/getIngSupplier.php');


if($_GET['kind'] === 'overall'){ 
	$q = mysqli_query($conn, "SELECT name,ingredient,ingredient_id,quantity FROM makeFormula WHERE toAdd = '1' AND skip = '0'");
	while($res = mysqli_fetch_array($q)){
		$m[] = $res;
	}
	
	foreach ($m as $material) { 
		$ing = mysqli_fetch_array(mysqli_query($conn,"SELECT cas FROM ingredients WHERE id = '".$material['ingredient_id']."'"));
	
		$inventory = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(stock) OVER() AS stock, mUnit FROM suppliers WHERE ingID = '".$material['ingredient_id']."'"));
	
		$r['formula'] = (string)$material['name'];
		$r['ingredient'] = (string)$material['ingredient'];
		$r['quantity'] = (string)$material['quantity'];
		$r['cas'] = (string)$ing['cas'] ?: "N/A";
	
		$r['inventory']['stock'] = (float)$inventory['stock'] ?: 0;
		$r['inventory']['mUnit'] = (string)$inventory['mUnit'] ?: $settings['mUnit'];
		
		if($a = getIngSupplier($material['ingredient_id'],0,$conn)){ 
			$j = 0;
			unset($r['supplier']);
			foreach ($a as $b){
				$r['supplier'][$j]['name'] = (string)$b['name'];
				$r['supplier'][$j]['link'] = (string)$b['supplierLink'];
				$r['supplier'][$j]['status'] = (int)$b['status'];
				$j++;
			}
		}else{
			$r['supplier'] = null;
		}
		
		$response['data'][] = $r;
	}

} else if($_GET['kind'] === 'summary') {

	$q = mysqli_query($conn, "SELECT ingredient,ingredient_id,SUM(quantity) AS total_quantity FROM makeFormula WHERE toAdd = '1' AND skip = '0' GROUP BY ingredient, ingredient_id");
	while($res = mysqli_fetch_array($q)){
		$m[] = $res;
	}
	
	foreach ($m as $material) { 
		$ing = mysqli_fetch_array(mysqli_query($conn,"SELECT cas FROM ingredients WHERE id = '".$material['ingredient_id']."'"));
	
		$inventory = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(stock) OVER() AS stock, mUnit FROM suppliers WHERE ingID = '".$material['ingredient_id']."'"));
	
		$r['ingredient'] = (string)$material['ingredient'];
		$r['quantity'] = (string)$material['total_quantity'];
		$r['cas'] = (string)$ing['cas'] ?: "N/A";
	
		$r['inventory']['stock'] = (float)$inventory['stock'] ?: 0;
		$r['inventory']['mUnit'] = (string)$inventory['mUnit'] ?: $settings['mUnit'];
		
		if($a = getIngSupplier($material['ingredient_id'],0,$conn)){ 
			$j = 0;
			unset($r['supplier']);
			foreach ($a as $b){
				$r['supplier'][$j]['name'] = (string)$b['name'];
				$r['supplier'][$j]['link'] = (string)$b['supplierLink'];
				$r['supplier'][$j]['status'] = (int)$b['status'];
				$j++;
			}
		}else{
			$r['supplier'] = null;
		}
		
		$response['data'][] = $r;
	}
}


if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

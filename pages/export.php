<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');

if($_GET['format'] == 'csv' && $_GET['kind'] == 'ingredients'){
	$defCatClass = $settings['defCatClass'];
	$r = mysqli_query($conn, "SELECT name,INCI,cas,FEMA,type,strength,profile,physical_state,allergen,odor,impact_top,impact_heart,impact_base FROM ingredients");
	
	$ing = array();
	if (mysqli_num_rows($r) > 0) {
		while ($row = mysqli_fetch_assoc($r)) {
			$ing[] = $row;
		}
	}

	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename='.$_GET['kind'].'.csv');
	$output = fopen('php://output', 'w');
	fputcsv($output, array('Name', 'INCI', 'CAS', 'FEMA', 'Type', 'Strength', 'Profile', 'Physical State', 'Allergen', 'Odor Description', 'Top Note Impact', 'Heart Note Impact', 'Base Note Impact'));
	
	if (count($ing) > 0) {
		foreach ($ing as $row) {
			fputcsv($output, $row);
		}
	}
	
	return;	
}

if($_GET['format'] == 'json' && $_GET['kind'] == 'ingredients'){
		
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData")))){
		$msg['error'] = 'No ingredients found to export.';
		echo json_encode($msg);
		return;
	}
	$ingredients = 0;
	
	$q = mysqli_query($conn, "SELECT * FROM ingredients");
	while($res = mysqli_fetch_assoc($q)){
		
		$r['id'] = (int)$res['id'];
		$r['name'] = (string)$res['name'];
		$r['INCI'] = (string)$res['INCI'];
		$r['cas'] = (string)$res['cas'];
		$r['FEMA'] = (string)$res['FEMA'];
		$r['type'] = (string)$res['type'];
		$r['strength'] = (string)$res['strength'];
		$r['category'] = (int)$res['category'];
		$r['purity'] = (int)$res['purity'];
		$r['einecs'] = (string)$res['einecs'];
		$r['reach'] = (string)$res['reach'];
		$r['tenacity'] = (string)$res['tenacity'];
		$r['chemical_name'] = (string)$res['chemical_name'];
		$r['formula'] = (string)$res['formula'];
		$r['flash_point'] = (string)$res['flash_point'];
		$r['notes'] = (string)$res['notes'];
		$r['flavor_use'] = (int)$res['flavor_use'];
		$r['soluble'] = (string)$res['soluble'];
		$r['logp'] = (string)$res['logp'];
		$r['cat1'] = (double)$res['cat1'];
		$r['cat2'] = (double)$res['cat2'];
		$r['cat3'] = (double)$res['cat3'];
		$r['cat4'] = (double)$res['cat4'];
		$r['cat5A'] = (double)$res['cat5A'];
		$r['cat5B'] = (double)$res['cat5B'];
		$r['cat5C'] = (double)$res['cat5C'];
		$r['cat6'] = (double)$res['cat6'];
		$r['cat7A'] = (double)$res['cat7A'];
		$r['cat7B'] = (double)$res['cat7B'];
		$r['cat8'] = (double)$res['cat8'];
		$r['cat9'] = (double)$res['cat9'];
		$r['cat10A'] = (double)$res['cat10A'];
		$r['cat10B'] = (double)$res['cat10B'];
		$r['cat11A'] = (double)$res['cat11A'];
		$r['cat11B'] = (double)$res['cat11B'];
		$r['cat12'] = (double)$res['cat12'];
		$r['profile'] = (string)$res['profile'];
		$r['physical_state'] = (int)$res['physical_state'];
		$r['allergen'] = (int)$res['allergen'];
		$r['odor'] = (string)$res['odor'];
		$r['impact_top'] = (int)$res['impact_top'];
		$r['impact_heart'] = (int)$res['impact_heart'];
		$r['impact_base'] = (int)$res['impact_base'];
		$r['created'] = (string)$res['created'];
		$r['usage_type'] = (string)$res['usage_type'];
		$r['noUsageLimit'] = (int)$res['noUsageLimit'];
		$r['byPassIFRA'] = (int)$res['byPassIFRA'];
		$r['isPrivate'] = (int)$res['isPrivate'];
		$r['molecularWeight'] = (string)$res['molecularWeight'];

		
		$ingredients++;
		$ing[] = $r;
	}
	
	
	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	//$vd['formulas'] = $formulas;
	$vd['ingredients'] = $ingredients;
	$vd['timestamp'] = date('d/m/Y H:i:s');

	
	$result['ingredients'] = $ing;
	//$result['formulas'] = $fd;
	$result['pvMeta'] = $vd;

	header('Content-disposition: attachment; filename=ingredients.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;
	
	/*
	$file = __ROOT__.'/tmp/pv_formulas.json';
	unlink($file);
	
	$fp = fopen($file, 'w');
	fwrite($fp, json_encode($result, JSON_PRETTY_PRINT));
	fclose($fp);

	if(file_exists($file)){
		$msg['success'] ='<a href="/tmp/pv_formulas.json" target="_blank">JSON File is ready, right click to save it to your computer.</a>';
	}else{
		$msg['error'] = 'Error generating JSON file';
	}
	
	echo json_encode($msg);
	return;
	*/
}





?>

<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');

//EXPORT INGREDIENTS CSV
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

//EXPORT SUPPLIERS CSV
if($_GET['format'] == 'csv' && $_GET['kind'] == 'suppliers'){
	$r = mysqli_query($conn, "SELECT id,name,address,po,country,telephone,url,email,platform,price_tag_start,price_tag_end,add_costs,price_per_size,notes,min_ml,min_gr FROM ingSuppliers");
	
	$res = array();
	if (mysqli_num_rows($r) > 0) {
		while ($row = mysqli_fetch_assoc($r)) {
			$mt = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(id) AS mt FROM suppliers WHERE ingSupplierID = '".$row['id']."'"));

			unset($row['id']);
			$row['materials'] = $mt['mt'];
			$res[] = $row;
			
		}
	}

	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename='.$_GET['kind'].'.csv');
	$output = fopen('php://output', 'w');
	fputcsv($output, array('Name', 'Address', 'PO', 'Country', 'Telephone', 'URL', 'Email', 'Platform', 'Price Tag Start', 'Price Tag End', 'Added Costs', 'Price Per Size', 'Notes', 'Min ml', 'Min gr', 'Materials'));
	
	if (count($res) > 0) {
		foreach ($res as $row) {
			fputcsv($output, $row);
		}
	}
	
	return;	
}

//EXPORT INGREDIENTS JSON
if($_GET['format'] == 'json' && $_GET['kind'] == 'ingredients'){
		
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients")))){
		$msg['error'] = 'No ingredients found to export.';
		echo json_encode($msg);
		return;
	}
	$ingredients = 0;
	$suppliers_count = 0;

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
	$q = mysqli_query($conn, "SELECT * FROM allergens");
	while($res = mysqli_fetch_assoc($q)){

		$c['id'] = (string)$res['id'];
		$c['ing'] = (string)$res['ing'];
		$c['name'] = (string)$res['name'];
		$c['cas'] = (string)$res['cas'] ?: 'N/A';
		$c['ec'] = (string)$res['ec'] ?: 'N/A';
		$c['percentage'] = (double)$res['percentage'];
		$c['toDeclare'] = (int)$res['toDeclare'];
		$c['created'] = (string)$res['created'];

		$cmp[] = $c;
	}
	
	$q = mysqli_query($conn, "SELECT * FROM suppliers");
	while($res = mysqli_fetch_assoc($q)){
	   $sd = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '".$s['ingSupplierID']."'"));


		$s['id'] = (int)$res['id'];
		$s['name'] = (string)$sd['name'] ?: "Unknown";
		$s['ingSupplierID'] = (int)$res['ingSupplierID'];
		$s['ingID'] = (int)$res['ingID'];
		$s['supplierLink'] = (string)$res['supplierLink'] ?: 'N/A';
		$s['price'] = (double)$res['price'] ?: 0;
		$s['size'] = (double)$res['size'] ?: 10;
		$s['manufacturer'] = (string)$res['manufacturer']?: 'N/A';
		$s['preferred'] = (int)$res['preferred'] ?: 0;
		$s['batch'] = (string)$res['batch'] ?: 'N/A';
		$s['purchased'] = (string)$res['purchased'] ?: 'N/A';
		$s['mUnit'] = (string)$res['mUnit'] ?: 'N/A';
		$s['stock'] = (double)$res['stock'] ?: 0;
		$s['status'] = (int)$res['status'] ?: 1;
		$s['created_at'] = (string)$res['created_at'];
		$s['updated_at'] = (string)$res['updated_at'];
		$s['supplier_sku'] = (string)$res['supplier_sku'] ?: 'N/A';
		$s['internal_sku'] = (string)$res['internal_sku'] ?: 'N/A';
		$s['storage_location'] = (string)$res['storage_location'] ?: 'N/A';

		$sup[] = $s;
		$suppliers_count++;
	}
	$qs = mysqli_query($conn, "SELECT * FROM ingSuppliers");
	while($res_sup = mysqli_fetch_assoc($qs)){

		$is['id'] = (int)$res_sup['id'];
		$is['name'] = (string)$res_sup['name'];
		$is['address'] = (string)$res_sup['address'] ?: 'N/A';
		$is['po'] = (string)$res_sup['po'] ?: 'N/A';
		$is['country'] = (string)$res_sup['country'] ?: 'N/A';
		$is['telephone'] = (string)$res_sup['telephone']?: 'N/A';
		$is['url'] = (string)$res_sup['url']?: 'N/A';
		$is['email'] = (string)$res_sup['email']?: 'N/A';

		$ingSup[] = $is;
	}
	
	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	$vd['ingredients'] = $ingredients;
	$vd['suppliers'] = $suppliers_count;
	$vd['timestamp'] = date('d/m/Y H:i:s');

	
	$result['ingredients'] = $ing;
	$result['compositions'] = $cmp;
	$result['suppliers'] = $sup;
	$result['ingSuppliers'] = $ingSup;
	$result['pvMeta'] = $vd;

	header('Content-disposition: attachment; filename=ingredients.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;	

}

if($_GET['format'] == 'json' && $_GET['kind'] == 'single-ingredient' && $_GET['id']){
		
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE id=".$_GET['id']."")))){
		$msg['error'] = 'No ingredients found to export.';
		echo json_encode($msg);
		return;
	}
	$ingredient = 0;
	$suppliers_count = 0;
	
	$q = mysqli_query($conn, "SELECT * FROM ingredients WHERE id=".$_GET['id']."");
	while($res = mysqli_fetch_assoc($q)){
		
		$r['id'] = (int)$res['id'];
		$r['name'] = (string)$res['name'];
		$r['INCI'] = (string)$res['INCI']?: 'N/A';
		$r['cas'] = (string)$res['cas'];
		$r['FEMA'] = (string)$res['FEMA']?: 'N/A';
		$r['type'] = (string)$res['type'];
		$r['strength'] = (string)$res['strength'];
		$r['category'] = (int)$res['category'];
		$r['purity'] = (int)$res['purity'];
		$r['einecs'] = (string)$res['einecs']?: 'N/A';
		$r['reach'] = (string)$res['reach']?: 'N/A';
		$r['tenacity'] = (string)$res['tenacity']?: 'N/A';
		$r['chemical_name'] = (string)$res['chemical_name']?: 'N/A';
		$r['formula'] = (string)$res['formula']?: 'N/A';
		$r['flash_point'] = (string)$res['flash_point']?: 'N/A';
		$r['notes'] = (string)$res['notes']?: 'N/A';
		$r['flavor_use'] = (int)$res['flavor_use'];
		$r['soluble'] = (string)$res['soluble']?: 'N/A';
		$r['logp'] = (string)$res['logp']?: 'N/A';
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
		$r['molecularWeight'] = (string)$res['molecularWeight']?: 'N/A';

		
		$ingredient++;
		$ing[] = $r;
	}
	
	$q = mysqli_query($conn, "SELECT * FROM allergens WHERE ing ='".$ing['0']['name']."'");
	while($res = mysqli_fetch_assoc($q)){

		$c['id'] = (int)$res['id'];
		$c['ing'] = (string)$res['ing'];
		$c['name'] = (string)$res['name'];
		$c['cas'] = (string)$res['cas'] ?: 'N/A';
		$c['ec'] = (string)$res['ec'] ?: 'N/A';
		$c['percentage'] = (double)$res['percentage'];
		$c['toDeclare'] = (int)$res['toDeclare'];
		$c['created'] = (string)$res['created'];

		$cmp[] = $c;
	}
	
	
	$q = mysqli_query($conn, "SELECT * FROM suppliers WHERE ingID ='".$ing['0']['id']."'");
	while($res = mysqli_fetch_assoc($q)){

		$s['id'] = (int)$res['id'];
		$s['ingSupplierID'] = (int)$res['ingSupplierID'];
		$s['ingID'] = (int)$res['ingID'];
		$s['supplierLink'] = (string)$res['supplierLink'] ?: 'N/A';
		$s['price'] = (double)$res['price'] ?: 0;
		$s['size'] = (double)$res['size'] ?: 10;
		$s['manufacturer'] = (string)$res['manufacturer']?: 'N/A';
		$s['preferred'] = (int)$res['preferred'] ?: 0;
		$s['batch'] = (string)$res['batch'] ?: 'N/A';
		$s['purchased'] = (string)$res['purchased'] ?: 'N/A';
		$s['mUnit'] = (string)$res['mUnit'] ?: 'N/A';
		$s['stock'] = (double)$res['stock'] ?: 0;
		$s['status'] = (int)$res['status'] ?: 1;
		$s['created_at'] = (string)$res['created_at'];
		$s['updated_at'] = (string)$res['updated_at'];
		$s['supplier_sku'] = (string)$res['supplier_sku'] ?: 'N/A';
		$s['internal_sku'] = (string)$res['internal_sku'] ?: 'N/A';
		$s['storage_location'] = (string)$res['storage_location'] ?: 'N/A';

		$sup[] = $s;
		$suppliers_count++;
	
		$qs = mysqli_query($conn, "SELECT * FROM ingSuppliers WHERE id ='".$s['ingSupplierID']."'");
		while($res_sup = mysqli_fetch_assoc($qs)){
	
			$is['id'] = (int)$res_sup['id'];
			$is['name'] = (string)$res_sup['name'];
			$is['address'] = (string)$res_sup['address'] ?: 'N/A';
			$is['po'] = (string)$res_sup['po'] ?: 'N/A';
			$is['country'] = (string)$res_sup['country'] ?: 'N/A';
			$is['telephone'] = (string)$res_sup['telephone'] ?: 'N/A';
			$is['url'] = (string)$res_sup['url'] ?: 'N/A';
			$is['email'] = (string)$res_sup['email'] ?: 'N/A';
	
			$ingSup[] = $is;
		}
	}
	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	$vd['ingredients'] = $ingredient;
	$vd['suppliers'] = $suppliers_count;
	$vd['timestamp'] = date('d/m/Y H:i:s');

	
	$result['ingredients'] = $ing;
	$result['compositions'] = $cmp;	
	$result['suppliers'] = $sup;
	$result['ingSuppliers'] = $ingSup;
	$result['pvMeta'] = $vd;

	header('Content-disposition: attachment; filename='.$ing['0']['name'].'.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;

}

//EXPORT SUPPLIERS JSON
if($_GET['format'] == 'json' && $_GET['kind'] == 'suppliers'){
		
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingSuppliers")))){
		$msg['error'] = 'No suppliers found to export.';
		echo json_encode($msg);
		return;
	}
	$suppliers_count = 0;

	$q = mysqli_query($conn, "SELECT * FROM ingSuppliers");
	while($res = mysqli_fetch_assoc($q)){
		$mt = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(id) AS mt FROM suppliers WHERE ingSupplierID = '".$res['id']."'"));

		$r['id'] = (int)$res['id'];
		$r['name'] = (string)$res['name'];
		$r['address'] = (string)$res['address'];
		$r['po'] = (string)$res['po'];
		$r['country'] = (string)$res['country'];
		$r['telephone'] = (string)$res['telephone'];
		$r['url'] = (string)$res['url'];
		$r['email'] = (string)$res['email'];
		$r['platform'] = (string)$res['platform'];
		$r['price_tag_start'] = (string)$res['price_tag_start'];
		$r['price_tag_end'] = (string)$res['price_tag_end'];		
		$r['add_costs'] = (double)$res['add_costs'];
		$r['price_per_size'] = (int)$res['price_per_size'];
		$r['notes'] = (string)$res['notes'];
		$r['min_ml'] = (double)$res['min_ml'];
		$r['min_gr'] = (double)$res['min_gr'];

		$r['materials'] = (int)$mt['mt'];
		
		$suppliers_count++;
		$sup[] = $r;

	}
	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	$vd['suppliers'] = $suppliers_count;
	$vd['timestamp'] = date('d/m/Y H:i:s');

	$result['suppliers'] = $sup;
	$result['pvMeta'] = $vd;

	header('Content-disposition: attachment; filename=suppliers.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;	

}

//EXPORT SUPPLIERS MATERIALS
if($_GET['format'] == 'json' && $_GET['kind'] == 'supplier-materials' && $_GET['id']){
		
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT ingID FROM suppliers WHERE ingSupplierID=".$_GET['id']."")))){
		$msg['error'] = 'No data found to export.';
		echo json_encode($msg);
		return;
	}
	$ingredient = 0;
	
	$q = mysqli_query($conn, "SELECT ingID FROM suppliers WHERE ingSupplierID=".$_GET['id']."");
	while($res = mysqli_fetch_array($q)){
		
		$i = mysqli_fetch_array(mysqli_query($conn, "SELECT id,name,cas,created,odor FROM ingredients WHERE id=".$res['ingID'].""));
		
		$r['id'] = (int)$i['id'];
		$r['name'] = (string)$i['name'];
		$r['cas'] = (string)$i['cas'] ?: 'N/A';
		$r['created'] = (string)$i['created'] ?: 'N/A';
		$r['odor'] = (string)$i['odor'] ?: 'N/A';

		$ingredient++;
		$ing[] = $r;
	}

	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	$vd['ingredients'] = $ingredient;
	$vd['supplier'] = $_GET['supplier-name'];
	$vd['timestamp'] = date('d/m/Y H:i:s');

	
	$result['supplier_materials'] = $ing;
	$result['pvMeta'] = $vd;

	header('Content-disposition: attachment; filename='.$_GET['supplier-name'].'_materials.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;

}

?>

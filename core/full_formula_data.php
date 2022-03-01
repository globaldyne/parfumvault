<?php
$starttime = microtime(true);

require('../inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

require_once(__ROOT__.'/func/calcCosts.php');
require_once(__ROOT__.'/func/calcPerc.php');
require_once(__ROOT__.'/func/checkIng.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/ml2L.php');
require_once(__ROOT__.'/func/countElement.php');
require_once(__ROOT__.'/func/getIngSupplier.php');
require_once(__ROOT__.'/func/getCatByID.php');

if(!$_GET['id']){		
	$response['data'] = [];    
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT name,fid,catClass,finalType,defView,isProtected FROM formulasMetaData WHERE id = '$id'"));

if(!$meta['fid']){		
	$response['Error'] = (string)'Requested id is not valid.';    
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}

if(isset($_GET['stats_only'])){
	
	$s['top'] = (float)calcPerc($id, 'Top', $settings['top_n'], $conn);
	$s['top_max'] = (float)$settings['top_n'];
	$s['heart'] = (float)calcPerc($id, 'Heart', $settings['heart_n'], $conn);
	$s['heart_max'] = (float)$settings['heart_n'];
	$s['base'] = (float)calcPerc($id, 'Base', $settings['base_n'], $conn);
	$s['base_max'] = (float)$settings['base_n'];

	$response['stats'] = $s;

	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}
$defCatClass = $meta['catClass'] ?: $settings['defCatClass'];

$formula_q = mysqli_query($conn, "SELECT id,ingredient,concentration,quantity,dilutant,notes,exclude_from_calculation FROM formulas WHERE fid = '".$meta['fid']."' ORDER BY ingredient ASC");
while ($formula = mysqli_fetch_array($formula_q)){
	    $form[] = $formula;
		if ( $formula['exclude_from_calculation'] != 1 ){
			$mg['total_mg'] += $formula['quantity'];
		}
}

foreach ($form as $formula){
	
	$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT id, name, cas, $defCatClass, profile, odor, category, physical_state,usage_type AS classification FROM ingredients WHERE name = '".$formula['ingredient']."'"));
	 
	$inventory = mysqli_fetch_array(mysqli_query($conn, "SELECT stock,mUnit,batch,purchased FROM suppliers WHERE ingID = '".$ing_q['id']."' AND preferred = '1'"));
	
	$conc = $formula['concentration'] / 100 * $formula['quantity']/$mg['total_mg'] * 100;
  	$conc_final = $formula['concentration'] / 100 * $formula['quantity']/$mg['total_mg'] * $meta['finalType'];
	
	if($settings['multi_dim_perc'] == '1'){
		$compos = mysqli_query($conn, "SELECT name,percentage,cas FROM allergens WHERE ing = '".$formula['ingredient']."'");
		
		while($compo = mysqli_fetch_array($compos)){
			$cmp[] = $compo;
		}
		
		foreach ($cmp as $a){
			$arrayLength = count($a);
			$i = 0;
			while ($i < $arrayLength){
				$c = multi_dim_search($a, 'cas', $ing_q['cas'])[$i];
				$conc_a[$a['cas']] += $c['percentage']/100 * $formula['quantity'] * $formula['concentration'] / 100;
				$conc_b[$a['cas']] += $c['percentage']/100 * $formula['quantity'] * $formula['concentration'] / $mg['total_mg']* $meta['finalType']/100 ;
				$i++;
			}
		}
		$conc+=$conc_a[$a['cas']];
		$conc_final+=$conc_b[$a['cas']];

	}
						
 	if($settings['chem_vs_brand'] == '1'){
		$chName = mysqli_fetch_array(mysqli_query($conn,"SELECT chemical_name FROM ingredients WHERE name = '".$formula['ingredient']."'"));
		$ingName = $chName['chemical_name'];
	}
	$r['formula_ingredient_id'] = (int)$formula['id'];       
	$r['fid'] = (string)$meta['name'];
	
	
	
	if($settings['grp_formula'] == '1'){
		$r['ingredient']['profile'] = (string)$ing_q['profile'] ?: 'Unknown';
	}elseif($settings['grp_formula'] == '2'){
		$r['ingredient']['profile'] = (string)getCatByIDRaw($ing_q['category'], 'name,colorKey', $conn)['name']?:'Unknown Notes';
	}elseif($settings['grp_formula'] == '0'){
		$r['ingredient']['profile'] = null;
		$r['ingredient']['profile_plain'] = (string)$ing_q['profile'].'_notes'?: 'Unknown';
	}
	
	$r['purity'] = (float)$formula['concentration'] ?: 100;
	$r['dilutant'] = (string)$formula['dilutant'] ?: 'None';
	if($formula['exclude_from_calculation'] == 1){
			
		$r['quantity'] = 0;
		$r['concentration'] = 0;
		$r['final_concentration'] = 0;
		$r['cost'] = 0;
	}else{
		$r['quantity'] = number_format((float)$formula['quantity'], $settings['qStep'],'.', '') ?: 0;
    	$r['concentration'] = number_format($conc, $settings['qStep']) ?: 0.000;
    	$r['final_concentration'] = number_format((float)$conc_final, $settings['qStep']) ?: 0;
		$r['cost'] = (float)calcCosts(getPrefSupplier($ing_q['id'],$conn)['price'],$formula['quantity'], $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']) ?: 0;

	}
	
	$u = explode(' - ',searchIFRA($ing_q['cas'],$formula['ingredient'],null,$conn,$defCatClass));
	
	if(($u['0'])){
		$r['usage_limit'] = number_format((float)$u['0'], $settings['qStep']);
		$r['usage_restriction'] = (string)$u['1'] ?: 'N/A';
		$r['usage_regulator'] = (string)"IFRA";
	}else{
		$r['usage_limit'] = number_format((float)$ing_q["$defCatClass"], $settings['qStep']) ?: 100;
		$r['usage_restriction'] = (int)$ing_q['classification'];
		$r['usage_regulator'] = (string)'PV';
	}
	
	
	if($meta['defView'] == '1'){
		$desc = $ing_q['odor'];
	}elseif($meta['defView'] == '2'){
		$desc = $formula['notes'];
	}
	
	$r['ingredient']['enc_id'] = (string)base64_encode($ing_q['name']);
	$r['ingredient']['id'] = (int)$ing_q['id'];
   	$r['ingredient']['name'] = (string)$ingName ?: $formula['ingredient'];
	$r['ingredient']['cas'] = (string)$ing_q['cas'];
	$r['ingredient']['physical_state'] = (int)$ing_q['physical_state'];
	$r['ingredient']['classification'] = (int)$ing_q['classification'] ?: 1;

	$r['ingredient']['desc'] = (string)$desc ?: '-';
	$r['ingredient']['pref_supplier'] = (string)getPrefSupplier($ing_q['id'],$conn)['name'] ?: 'N/A';
	$r['ingredient']['pref_supplier_link'] = (string)getPrefSupplier($ing_q['id'],$conn)['supplierLink'] ?: 'N/A';
	
	
	
	$r['ingredient']['inventory']['stock'] = (int)$inventory['stock'] ?: 0;
	$r['ingredient']['inventory']['mUnit'] = (string)$inventory['mUnit'] ?: $settings['mUnit'];
	$r['ingredient']['inventory']['batch'] = (string)$inventory['batch'] ?: 'N/A';
	$r['ingredient']['inventory']['purchased'] = (string)$inventory['purchased'] ?: 'N/A';
	
	$r['chk_ingredient'] = (string)checkIng($formula['ingredient'],$defCatClass,$conn) ?: null;
	$r['exclude_from_calculation'] = (int)$formula['exclude_from_calculation'] ?: 0;

	$response['data'][] = $r;
	
	$conc_f[] = $conc;
	$total_cost[] = calcCosts(getPrefSupplier($ing_q['id'],$conn)['price'],$formula['quantity'], $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']);

}

if(empty($r)){
	$response['data'] = [];
}

$m['total_ingredients'] = (int)countElement("formulas WHERE fid = '".$meta['fid']."'",$conn);	
$m['total_quantity'] =  ml2l($mg['total_mg'], $settings['qStep'], $settings['mUnit']);
$m['quantity_unit'] = (string)$settings['mUnit'];
$m['cat_class'] = (string)$defCatClass;
$m['currency'] = (string)$settings['currency'];
$m['total_cost'] = number_format((float)array_sum($total_cost), $settings['qStep']);
$m['concentration'] = number_format((float)array_sum($conc_f), $settings['qStep']);
$m['product_concentration'] = (int)$meta['finalType'];
$m['formula_name'] = (string)$meta['name'];
$m['formula_fid'] = (string)$meta['fid'];
$m['protected'] = (bool)$meta['isProtected'];



$response['meta'] = $m;

$s['load_time'] = microtime(true) - $starttime;
$response['sys'] = $s;

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>
<?php
$starttime = microtime(true);

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
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

require_once(__ROOT__.'/func/validateFormula.php');

if(!$_REQUEST['id']){		
	$response['data'] = [];    
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}

$id = mysqli_real_escape_string($conn, $_REQUEST['id']);

$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT name,fid,catClass,finalType,defView,isProtected,notes,product_name FROM formulasMetaData WHERE id = '$id'"));

if(!$meta['fid']){		
	$response['Error'] = (string)'Requested id is not valid.';    
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}

if($_POST['solvents_only'] === 'true'){
	
	$q = mysqli_query($conn,"SELECT formulas.ingredient,formulas.ingredient_id,formulas.quantity,ingredients.profile FROM formulas,ingredients WHERE fid = '".$meta['fid']."' AND ingredients.id = formulas.ingredient_id AND ingredients.profile='solvent'");
	while($res = mysqli_fetch_array($q)){
    	$solvents[] = $res;
	}
	$i = 0;
	foreach ($solvents as $solvent) { 

		$r['ingredient_id'] = (int)$solvent['ingredient_id'];
		$r['ingredient'] = (string)$solvent['ingredient'];
		$r['profile'] = (string)$solvent['profile']?: '-';
		$r['quantity'] = (float)$solvent['quantity']?: '0';
	
		$rx[]=$r;
		$i++;
		
	}
	
	$response = array(
	  "data" => $rx
	);
	
	$response = array(
	  "recordsTotal" => (int)$i,
	  "data" => $rx
	);
	
	if(empty($rx)){
		$response['data'] = array("No results");
	}
	
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}

if($_POST['search']){
	$q = "AND ingredient LIKE '%".$_POST['search']."%'";
}
	
if(isset($_GET['stats_only'])){
	
	$s['formula_name'] = (string)$meta['name'];
	$s['formula_description'] = (string)$meta['notes'];
	$s['top'] = (float)calcPerc($id, 'Top', $settings['top_n'], $conn)?: 0;
	$s['top_max'] = (float)$settings['top_n']?: 0;
	$s['heart'] = (float)calcPerc($id, 'Heart', $settings['heart_n'], $conn)?: 0;
	$s['heart_max'] = (float)$settings['heart_n'];
	$s['base'] = (float)calcPerc($id, 'Base', $settings['base_n'], $conn)?: 0;
	$s['base_max'] = (float)$settings['base_n'] ?: 0;

	$response['stats'] = $s;

	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}
$defCatClass = $meta['catClass'] ?: $settings['defCatClass'];

$formula_q = mysqli_query($conn, "SELECT id,ingredient,concentration,quantity,dilutant,notes,exclude_from_calculation FROM formulas WHERE fid = '".$meta['fid']."' $q ORDER BY ingredient ASC");
while ($formula = mysqli_fetch_array($formula_q)){
	    $form[] = $formula;
		if ( $formula['exclude_from_calculation'] != 1 ){
			$mg['total_mg'] += $formula['quantity'];
		}
}

foreach ($form as $formula){
	
	$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT id, name, cas, $defCatClass, profile, odor, category, physical_state,usage_type AS classification, type, byPassIFRA FROM ingredients WHERE name = '".$formula['ingredient']."'"));
	$reps = mysqli_query($conn,"SELECT ing_rep_name FROM ingReplacements WHERE ing_name = '".$formula['ingredient']."'");
	if (mysqli_num_rows($reps)==0) { 
		$reps = mysqli_query($conn,"SELECT ing_name FROM ingReplacements WHERE ing_rep_name = '".$formula['ingredient']."'");
	}
	while($replacements = mysqli_fetch_array($reps)){
		$replacement[] = $replacements;
	}
	
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
				$c = multi_dim_search($a, 'cas', $ing_q['cas']?:'N/A')[$i];
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
	
	if(($u['0'] && $ing_q['byPassIFRA'] == 0)){
		$r['usage_limit'] = number_format((float)$u['0']?:100, $settings['qStep']);
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
	$r['ingredient']['cas'] = (string)$ing_q['cas'] ?: 'N/A';
	$r['ingredient']['physical_state'] = (int)$ing_q['physical_state'];
	$r['ingredient']['classification'] = (int)$ing_q['classification'] ?: 1;
	$r['ingredient']['type'] = (string)$ing_q['type'] ?: 'Unknown';

	$r['ingredient']['desc'] = (string)$desc ?: '-';
	$r['ingredient']['pref_supplier'] = (string)getPrefSupplier($ing_q['id'],$conn)['name'] ?: 'N/A';
	$r['ingredient']['pref_supplier_link'] = (string)getPrefSupplier($ing_q['id'],$conn)['supplierLink'] ?: 'N/A';
	
	$r['ingredient']['inventory']['stock'] = (float)$inventory['stock'] ?: 0;
	$r['ingredient']['inventory']['mUnit'] = (string)$inventory['mUnit'] ?: $settings['mUnit'];
	$r['ingredient']['inventory']['batch'] = (string)$inventory['batch'] ?: 'N/A';
	$r['ingredient']['inventory']['purchased'] = (string)$inventory['purchased'] ?: 'N/A';
	
	$totalReplacements = 0;
	foreach ($replacement as $rp){
		$totalReplacements++;
		$r['ingredient'][]['replacement']['name'] = (string)$rp['ing_rep_name'] ?: (string)$rp['ing_name'] ?: 'N/A';
	}
	$r['ingredient']['replacement']['total'] = $totalReplacements ?: 0;
	
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
if($total_cost){
	$m['total_cost'] = number_format((float)array_sum($total_cost)?: 0, $settings['qStep']);
	$m['concentration'] = number_format((float)array_sum($conc_f)?: 0, $settings['qStep']);
}else{
	$m['total_cost'] = 0;
	$m['concentration'] = 0;
}
$m['product_concentration'] = (int)$meta['finalType'];
$m['formula_name'] = (string)$meta['name'];
$m['product_name'] = (string)$meta['product_name'] ?: '-';
$m['formula_fid'] = (string)$meta['fid'];
$m['formula_description'] = (string)$meta['notes'];
$m['protected'] = (bool)$meta['isProtected'];

$new_conc = $_GET['final_total_ml'] ?: 100/100*$_GET['final_type_conc'] ?: 100;
$carrier = $_GET['final_total_ml'] ?: 100 - $new_conc;


if($m['total_ingredients'] != 0 && !$_POST['search']){	
	if( validateFormula($meta['fid'], $_GET['final_total_ml'] ?: 100, $new_conc, $mg['total_mg'], $_GET['val_cat']?:	$defCatClass, $settings['qStep'], $conn) == TRUE){
		$val_status = 1;
		$val_msg = 'Your formula contains materials, exceeding and/or missing IFRA standards. Please alter your formula.';
	}
}

$compliance['checked_for'] = (string)$_GET['val_cat'] ?: $defCatClass;
$compliance['final_total_ml'] = (int)$_GET['final_total_ml'] ?: 100;
$compliance['final_type_conc'] = (int)$_GET['final_type_conc'] ?: 100;
$compliance['carier'] = (int)$carier ?: 100;
$compliance['status'] = (int)$val_status ?: 0;
$compliance['message'] = (string)$val_msg ?: 'Formula is IFRA compliant';
	
$response['compliance'] = $compliance;
$response['meta'] = $m;

$s['load_time'] = microtime(true) - $starttime;
$response['sys'] = $s;

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>
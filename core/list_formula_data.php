<?php
require('../inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/countElement.php');

//require_once(__ROOT__.'/func/formulaProfile.php');


$cats_q = mysqli_query($conn, "SELECT id,name,description,type FROM IFRACategories ORDER BY id ASC");
while($cats_res = mysqli_fetch_array($cats_q)){
    $cats[] = $cats_res;
}

if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients")))){
	$response['Error'] = (string)'<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> no ingredients yet, click <a href="?do=ingredients">here</a> to add.</div>';    
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}
if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData")))){
	$response['Error'] = (string)'<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> no formulas yet, click <a href="#" data-toggle="modal" data-target="#add_formula">here</a> to add.</div>';    
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}

	
foreach ($form as $formula){
	$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT id, name, cas, $defCatClass, profile, odor, category FROM ingredients WHERE name = '".$formula['ingredient']."'"));
	  	
	$conc = $formula['concentration'] / 100 * $formula['quantity']/$mg['total_mg'] * 100;
  	$conc_final = $formula['concentration'] / 100 * $formula['quantity']/$mg['total_mg'] * $meta['finalType'];
	
	if($settings['multi_dim_perc'] == '1'){
		$conc   += multi_dim_perc($conn, $form, $ing_q['cas'], $settings['qStep'])[$ing_q['cas']];
		$conc_final += multi_dim_perc($conn, $form, $ing_q['cas'], $settings['qStep'])[$ing_q['cas']];
	}
						
 	if($settings['chem_vs_brand'] == '1'){
		$chName = mysqli_fetch_array(mysqli_query($conn,"SELECT chemical_name FROM ingredients WHERE name = '".$formula['ingredient']."'"));
		$ingName = $chName['chemical_name'];
	}
		              
	$r['enc_id'] = (string)base64_encode($ing_q['name']);
	$r['id'] = (int)$ing_q['id'];
   	$r['ingredient'] = (string)$ingName ?: $formula['ingredient'];
   	$r['formula_ingredient_id'] = (int)$formula['id'];
	$r['cas'] = (string)$ing_q['cas'];
	if($settings['grp_formula'] == '1'){
		$r['profile'] = (string)$ing_q['profile'] ?: 'Unknown';
	}elseif($settings['grp_formula'] == '2'){
		$r['profile'] = (string)getCatByIDRaw($ing_q['category'], 'name,colorKey', $conn)['name']?:'Unknown Notes';
	}elseif($settings['grp_formula'] == '0'){
		$r['profile'] = null;
		$r['profile_plain'] = (string)$ing_q['profile'].'_notes'?: 'Unknown';
	}
	
	$r['purity'] = (int)$formula['concentration'] ?: 100;
	$r['dilutant'] = (string)$formula['dilutant'] ?: 'None';
	
	$r['quantity'] = number_format((float)$formula['quantity'], $settings['qStep']) ?: 0.000;
    $r['concentration'] = number_format($conc, $settings['qStep']) ?: 0.000;
    $r['final_concentration'] = number_format((float)$conc_final, $settings['qStep']) ?: 0.000;
	$u = explode(' - ',searchIFRA($ing_q['cas'],$formula['ingredient'],null,$conn,$defCatClass));
	
	if(($u['0'])){
		$r['usage_limit'] = number_format((float)$u['0'], $settings['qStep']);
		$r['usage_restriction'] = (string)$u['1'] ?: 'N/A';
		$r['usage_regulator'] = (string)"IFRA";
	}else{
		$r['usage_limit'] = number_format((float)$ing_q["$defCatClass"], $settings['qStep']) ?: 100;
		$r['usage_restriction'] = (string)'REC';
		$r['usage_regulator'] = (string)"PV";
	}
	
	$r['cost'] = (float)calcCosts(getPrefSupplier($ing_q['id'],$conn)['price'],$formula['quantity'], $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']) ?: 0.000;
	
	if($meta['defView'] == '1'){
		$desc = $ing_q['odor'];
	}elseif($meta['defView'] == '2'){
		$desc = $formula['notes'];
	}
	
	$r['desc'] = (string)$desc ?: '-';
	$r['pref_supplier'] = (string)getPrefSupplier($ing_q['id'],$conn)['name'] ?: 'N/A';
	$r['pref_supplier_link'] = (string)getPrefSupplier($ing_q['id'],$conn)['supplierLink'] ?: 'N/A';
	$r['fid'] = (string)$meta['name'];
	$r['chk_ingredient'] = (string)checkIng($formula['ingredient'],$defCatClass,$conn) ?: null;
	
	$response['data'][] = $r;
	
	$conc_f[] = $conc;
	$total_cost[] = calcCosts(getPrefSupplier($ing_q['id'],$conn)['price'],$formula['quantity'], $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']);

}

if(empty($r)){
	$response['data'] = [];
}


header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>
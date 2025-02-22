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
	$response['error'] = "Invalid or missing ID.";  
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}
$id = mysqli_real_escape_string($conn, $_REQUEST['id']);


$query = "SELECT name, fid, catClass, finalType, defView, isProtected, notes, product_name, owner_id FROM formulasMetaData WHERE owner_id = ? AND id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $userID, $id);
$stmt->execute();
$result = $stmt->get_result();
$meta = $result->fetch_array(MYSQLI_ASSOC);

if (!$meta || !$meta['fid']) {
	$response['error'] = "Requested ID is not valid or you do not have access.";
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}

if($_GET['qStep']){
	$settings['qStep'] = $_GET['qStep'];
}

$defPercentage = $settings['defPercentage'];


if(!$meta['fid']){		
	$response['Error'] = (string)'Requested id is not valid.';    
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}

if($_POST['solvents_only'] === 'true'){
	$sQuery = "SELECT formulas.ingredient, formulas.ingredient_id, formulas.quantity, ingredients.profile 
			   FROM formulas, ingredients 
			   WHERE fid = ? AND ingredients.id = formulas.ingredient_id 
			   AND ingredients.profile = 'solvent' AND ingredients.owner_id = ?";
	$stmt = $conn->prepare($sQuery);
	$stmt->bind_param("ss", $meta['fid'], $userID);
	$stmt->execute();
	$q = $stmt->get_result();
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

	$formulaName = (string) $meta['name'];
	$formulaDescription = (string) $meta['notes'];
	
	$stats['top']['current'] = (float) calcPerc($id, 'Top', $settings['top_n'], $conn) ?: 0;
	$stats['top']['max'] = (float) $settings['top_n'] ?: 0;
	
	$stats['heart']['current'] = (float) calcPerc($id, 'Heart', $settings['heart_n'], $conn) ?: 0;
	$stats['heart']['max'] = (float) $settings['heart_n'];

	$stats['base']['current'] = (float) calcPerc($id, 'Base', $settings['base_n'], $conn) ?: 0;
	$stats['base']['max'] = (float) $settings['base_n'] ?: 0;

	$response['stats']['formula_name'] = $formulaName;
	$response['stats']['formula_description'] = $formulaDescription;
	$response['stats']['data'] = $stats;
	
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}

$defCatClass = $meta['catClass'] ?: $settings['defCatClass'];

$formula_q = mysqli_query($conn, "SELECT id,ingredient,concentration,quantity,dilutant,notes,exclude_from_calculation FROM formulas WHERE fid = '".$meta['fid']."' $q  AND owner_id = '$userID' ORDER BY ingredient ASC");
while ($formula = mysqli_fetch_array($formula_q)){
	    $form[] = $formula;
		if ( $formula['exclude_from_calculation'] != 1 ){
			$mg['total_mg'] += $formula['quantity'];
		}
}

foreach ($form as $formula){
	
	$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT id, name, cas, $defCatClass, profile, odor, category, physical_state,usage_type AS classification, type, byPassIFRA FROM ingredients WHERE name = '".$formula['ingredient']."' AND owner_id = '$userID'"));
	$totalcontainsOthers = mysqli_num_rows(mysqli_query($conn, "SELECT name,$defPercentage,cas FROM ingredient_compounds WHERE ing = '".$formula['ingredient']."' AND owner_id = '$userID'"));
	$inventory = mysqli_fetch_array(mysqli_query($conn, "SELECT stock,mUnit,batch,purchased FROM suppliers WHERE ingID = '".$ing_q['id']."' AND preferred = '1' AND owner_id = '$userID'"));
	$conc = $formula['concentration'] / 100 * $formula['quantity']/$mg['total_mg'] * 100;
  	$conc_final = $formula['concentration'] / 100 * $formula['quantity']/$mg['total_mg'] * $meta['finalType'];
	
	if($settings['multi_dim_perc'] == '1'){
		$compos = mysqli_query($conn, "SELECT name,$defPercentage,cas FROM ingredient_compounds WHERE ing = '".$formula['ingredient']."' AND owner_id = '$userID'");
		
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
		$chName = mysqli_fetch_array(mysqli_query($conn,"SELECT chemical_name FROM ingredients WHERE name = '".$formula['ingredient']."' AND owner_id = '$userID'"));
		$ingName = $chName['chemical_name'];
	}
	$r['formula_ingredient_id'] = (int)$formula['id'];  
	$r['formula_name'] = (string)$meta['name'];
	$r['fid'] = (string)$meta['fid']; //TODO
		
	if($settings['grp_formula'] == '1'){
		$r['ingredient']['profile'] = (string)$ing_q['profile'] ?: 'Unknown';
	}elseif($settings['grp_formula'] == '2'){
		$r['ingredient']['profile'] = (string)getCatByIDRaw($ing_q['category'], 'name,colorKey', $conn)['name']?:'Unknown Notes';
	}elseif($settings['grp_formula'] == '3'){
		$r['ingredient']['profile'] = ($ing_q['physical_state'] == 1) ? 'Liquid' : (($ing_q['physical_state'] == 2) ? 'Solid' : 'Unknown');
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

	$u = searchIFRA($ing_q['cas'],$formula['ingredient'],null,$defCatClass);
	
	if (($u['val'] || $u['type']) && $ing_q['byPassIFRA'] === '0' && $formula['exclude_from_calculation'] == '0') {
		$r['usage_limit'] = number_format((float)$u['val'], $settings['qStep']);
		$r['usage_restriction'] = isset($u['risk']) ? (string)$u['risk'] : 'N/A';
		$r['usage_restriction_type'] = isset($u['type']) ? (string)$u['type'] : 'N/A';
		$r['usage_regulator'] = 'IFRA';
		

	} else {
		$r['usage_limit'] = number_format((float)($ing_q["$defCatClass"] ?? 100), $settings['qStep']);
		$r['usage_restriction'] = (int)($ing_q['classification'] ?? 1);
		

		switch ($ing_q['classification']) {
			case 1:
				$r['usage_restriction_type'] = 'RECOMMENDATION';
				break;
			case 2:
				$r['usage_restriction_type'] = 'RESTRICTION';
				break;
			case 3:
				$r['usage_restriction_type'] = 'SPECIFICATION';
				break;
			case 4:
				$r['usage_restriction_type'] = 'PROHIBITION';
				break;
			default:
				$r['usage_restriction_type'] = 'RECOMMENDATION';
				break;
		}
	
		$r['usage_regulator'] = 'PV';
		$r['ingredient']['classification'] = (int)isset($ing_q['classification']) ?: 1;
	}
	if($ing_q['byPassIFRA'] === '0') {
		$r['isIFRAbyPass'] = (int)0;
	} else {
		$r['isIFRAbyPass'] = (int)1;
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
	$r['ingredient']['type'] = (string)$ing_q['type'] ?: 'Unknown';

	$r['ingredient']['desc'] = (string)$desc ?: '-';
	$r['ingredient']['pref_supplier'] = (string)getPrefSupplier($ing_q['id'],$conn)['name'] ?: 'N/A';
	$r['ingredient']['pref_supplier_link'] = (string)getPrefSupplier($ing_q['id'],$conn)['supplierLink'] ?: 'N/A';
	
	$r['ingredient']['inventory']['stock'] = (float)$inventory['stock'] ?: 0;
	$r['ingredient']['inventory']['mUnit'] = (string)$inventory['mUnit'] ?: $settings['mUnit'];
	$r['ingredient']['inventory']['batch'] = (string)$inventory['batch'] ?: 'N/A';
	$r['ingredient']['inventory']['purchased'] = (string)$inventory['purchased'] ?: 'N/A';
	
	
	$r['ingredient']['containsOthers']['total'] = $totalcontainsOthers ?: 0;
	
	$r['chk_ingredient'] = (string)checkIng($formula['ingredient'],$defCatClass,$conn)['text'];
	$r['chk_ingredient_code'] = (int)checkIng($formula['ingredient'],$defCatClass,$conn)['code'];
	$r['exclude_from_calculation'] = (int)$formula['exclude_from_calculation'] ?: 0;
	
	
	$response['data'][] = $r;
	
	$conc_f[] = $conc;
	$total_cost[] = calcCosts(getPrefSupplier($ing_q['id'],$conn)['price'],$formula['quantity'], $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']);

}

if(empty($r)){
	$response['data'] = [];
	$response['meta'] = [];
	echo json_encode($response);
	return;
}

$m['total_ingredients'] = (int)countElement("formulas", "fid = '".$meta['fid']."'");	
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

$lastValAccepted = null;

for ($c = 1; $c <= 100; $c++) {
	$result = validateFormula($meta['fid'], 100, $c, $mg['total_mg'], $defCatClass, $settings['qStep'],1);

    if ($result === 0) {
        $lastValAccepted = $c;
    } else {
        break;
    }
}
if( $lastValAccepted !== null) {

	$m['max_usage'] = $lastValAccepted;
} else {
	$m['max_usage'] = 'Unable to calculate ';
}

$response['meta'] = $m;

$s['load_time'] = microtime(true) - $starttime;
$response['sys'] = $s;

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>
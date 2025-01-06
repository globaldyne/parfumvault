<?php

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

require_once(__ROOT__.'/libs/fpdf.php');
require_once(__ROOT__.'/func/genBatchID.php');
require_once(__ROOT__.'/func/genBatchPDF.php');
require_once(__ROOT__.'/func/calcCosts.php');
require_once(__ROOT__.'/func/calcPerc.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/getIngSupplier.php');
require_once(__ROOT__.'/func/validateFormula.php');

$role = (int)$user['role'];
$userID = isset($_SESSION['userID']) ? (int)$_SESSION['userID'] : 0;

if (!$_POST['id']) {		
	$response['data'] = [];    
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}

// Secure input
$id = mysqli_real_escape_string($conn, $_POST['id']);

// Adjust query based on role
if ($role === 1) {
    // Admin can fetch all data
    $metaQuery = "SELECT name, fid, catClass, finalType, defView, isProtected, notes, product_name 
                  FROM formulasMetaData WHERE id = '$id'";
} else {
    // Non-admin users can only access their own data
    $metaQuery = "SELECT name, fid, catClass, finalType, defView, isProtected, notes, product_name 
                  FROM formulasMetaData 
                  WHERE id = '$id' AND owner_id = '$userID'";
}

// Fetch metadata
$metaResult = mysqli_query($conn, $metaQuery);
$meta = mysqli_fetch_array($metaResult);

if (!$meta || !$meta['fid']) {		
	$response['error'] = 'Requested id is not valid or unauthorized access.';    
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}


$defPercentage = $settings['defPercentage'];

$defCatClass = $_POST['defCatClass'] ?: $settings['defCatClass'];		 
$id = mysqli_real_escape_string($conn, $_POST['id']);

$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT name,fid,catClass,finalType,defView,isProtected,notes,product_name FROM formulasMetaData WHERE id = '$id'"));

if(!$meta['fid']){		
	$response['Error'] = (string)'Requested id is not valid.';    
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
}

	
$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '".$meta['fid']."'"));
	
$bottle_id = $_POST['bottle_id'];
$concentration =  $_POST['concentration'];
$carrier_id = $_POST['carrier_id'];
$accessory_id = $_POST['accessory_id'];

$bottle = mysqli_fetch_array(mysqli_query($conn, "SELECT price,ml,name FROM bottles WHERE id = '$bottle_id' AND price != 0 "));
	
$carrier_cost = mysqli_fetch_array(mysqli_query($conn, "SELECT price,size FROM suppliers WHERE ingID = '$carrier_id'"));

if($_POST['accessory_id']){
	if(!$accessory = mysqli_fetch_array(mysqli_query($conn, "SELECT name, price, accessory FROM inventory_accessories WHERE id = '$accessory_id'"))){
	//}else{
		$accessory['price'] = 0;
		$accessory['accessory'] = 'none';
	}
}
$new_conc = $bottle['ml'] / 100 * $concentration;
$carrier = $bottle['ml'] - $new_conc;
	
	
if($_POST['supplier_id']){
	$sid = $_POST['supplier_id'];
}

if($_POST['batch_id'] == '1'){

	define('FPDF_FONTPATH',__ROOT__.'/fonts');
	$batchID = genBatchID();
	
	genBatchPDF($meta['fid'],$batchID,$bottle['ml'],$new_conc,$mg['total_mg'],$defCatClass,$settings['qStep'], $defPercentage);

	
}


$formulaQuery = ($role === 1)
    ? "SELECT id, ingredient, concentration, quantity FROM formulas WHERE fid = '".$meta['fid']."' ORDER BY ingredient ASC"
    : "SELECT id, ingredient, concentration, quantity FROM formulas WHERE fid = '".$meta['fid']."' AND owner_id = '$userID' ORDER BY ingredient ASC";

$formula_q = mysqli_query($conn, $formulaQuery);

while ($formula = mysqli_fetch_array($formula_q)){
	    $form[] = $formula;
}

foreach ($form as $formula){
	
	$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT id, name, cas, $defCatClass, profile, odor, category, physical_state,usage_type AS classification, type, byPassIFRA FROM ingredients WHERE name = '".$formula['ingredient']."'"));
	
	$new_quantity = $formula['quantity'] / $mg['total_mg'] * $new_conc;

	$conc = $formula['concentration'] / 100 * $formula['quantity']/$mg['total_mg'] * 100;
  	$conc_final = $formula['concentration'] / 100 * $formula['quantity']/$mg['total_mg'] * $concentration;
	
	if($settings['multi_dim_perc'] == '1'){
		$compos = mysqli_query($conn, "SELECT name,$defPercentage,cas FROM ingredient_compounds WHERE ing = '".$formula['ingredient']."'");
		
		while($compo = mysqli_fetch_array($compos)){
			$cmp[] = $compo;
		}
		
		foreach ($cmp as $a){
			$arrayLength = count($a);
			$i = 0;
			while ($i < $arrayLength){
				$c = multi_dim_search($a, 'cas', $ing_q['cas']?:'N/A')[$i];
				$conc_a[$a['cas']] += $c['percentage']/100 * $formula['quantity'] * $formula['concentration'] / 100;
				$conc_b[$a['cas']] += $c['percentage']/100 * $formula['quantity'] * $formula['concentration'] / $mg['total_mg']* $concentration / 100 ;
				$i++;
			}
		}
		$conc+=$conc_a[$a['cas']];
		$conc_final+=$conc_b[$a['cas']];

	}
 	
	$r['formula_ingredient_id'] = (int)$formula['id'];  
	$r['formula_name'] = (string)$meta['name'];
	$r['fid'] = (string)$meta['fid'];
	
	$r['ingredient']['name'] = (string)$ingName ?: $formula['ingredient'];
	$r['ingredient']['cas'] = (string)$ing_q['cas'] ?: 'N/A';
	$r['quantity'] = number_format((float)$new_quantity, $settings['qStep'],'.', '') ?: 0;
	$r['final_concentration'] = number_format((float)$conc_final, $settings['qStep']) ?: 0;
	
	if($sid){
		$r['cost'] = (float)calcCosts(getSingleSupplier($sid,$ing_q['id'],$conn)['price'],$new_quantity, $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']);
	}else{
		$r['cost'] = (float)calcCosts(getPrefSupplier($ing_q['id'],$conn)['price'],$new_quantity, $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']);
    } 

	//$u = explode(' - ',searchIFRA($ing_q['cas'],$formula['ingredient'],null,$defCatClass));
	$u = searchIFRA($ing_q['cas'],$formula['ingredient'],null,$defCatClass);

	//if(($u['0'] && $ing_q['byPassIFRA'] == 0)){
	if(($u['val'] || $u['type'] && $ing_q['byPassIFRA'] == 0)){	
		$r['usage_limit'] = number_format((float)$u['val']?:100, $settings['qStep']);
		$r['usage_restriction'] = (string)$u['risk'] ?: 'N/A';
		$r['usage_restriction_type'] = (string)$u['type'] ?: 'N/A';
		$r['usage_regulator'] = (string)"IFRA";
	}else{
		$r['usage_limit'] = number_format((float)$ing_q["$defCatClass"], $settings['qStep']) ?: 100;
		$r['usage_restriction'] = (int)$ing_q['classification'];
		if ($ing_q['classification'] == 1) {
			$r['usage_restriction_type'] = 'RECOMMENDATION';
		} elseif ($ing_q['classification'] == 2) {
			$r['usage_restriction_type'] = 'RESTRICTION';
		} elseif ($ing_q['classification'] == 3) {
			$r['usage_restriction_type'] = 'SPECIFICATION';
		} elseif ($ing_q['classification'] == 4) {
			$r['usage_restriction_type'] = 'PROHIBITION';
		} else {
			$r['usage_restriction_type'] = 'RECOMMENDATION';
		}
		$r['usage_regulator'] = (string)"PV";
		$r['ingredient']['classification'] = (int)$ing_q['classification'] ?: 1;
	}
		

	$response['data'][] = $r;
	
	$conc_tot[] = $conc_final;
	$new_tot[] = $new_quantity;
	
	if($sid){
       $tot[] = calcCosts(getSingleSupplier($sid,$ing_q['id'],$conn)['price'],$new_quantity, $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']);
	}else{
		$tot[] = calcCosts(getPrefSupplier($ing_q['id'],$conn)['price'],$new_quantity, $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']);
	}
	
}
if($carrier_cost['price'] && $carrier_cost['size'] && $carrier){
	$carrier_sub_cost = number_format($carrier_cost['price'] / $carrier_cost['size'] * $carrier, $settings['qStep']);
} else {
	$carrier_sub_cost = 0;
}

$m['sub_total_quantity'] = number_format(array_sum($new_tot), $settings['qStep']);
$m['carrier_quantity'] = number_format($carrier, $settings['qStep']);
$m['carrier_quantity'] = number_format($carrier, $settings['qStep']);
$m['carrier_cost'] =  (float)$carrier_sub_cost;
$m['bottle_quantity'] = (float)$bottle['ml'];
$m['accessory_cost'] = (float)$accessory['price'];
$m['accessory'] = (string)$accessory['name'].' ('.$accessory['accessory'].')';
$m['batchNo'] = $batchID;
$m['total_quantity'] = (float)number_format( array_sum($new_tot) + $carrier,$settings['qStep']);
$m['quantity_unit'] = (string)$settings['mUnit'];
$m['sub_concentration'] = (float)number_format(array_sum($conc_tot),$settings['qStep']);
$m['carrier_concentration'] = (float)number_format($carrier ?? 100 * 100 / $bottle['ml'],$settings['qStep']);
$m['sub_cost'] = (float)number_format(array_sum($tot),$settings['qStep']);
$m['bottle_cost'] = (float)number_format($bottle['price'],$settings['qStep']);
$m['total_cost'] = (float)number_format(array_sum($tot) + $accessory['price'] + $carrier_sub_cost + $bottle['price'], $settings['qStep']);


$m['cat_class'] = (string)$defCatClass;
$m['currency'] = (string)$settings['currency'];
$m['product_concentration'] = (int)$concentration;
$m['formula_name'] = (string)$meta['name'];
$m['product_name'] = (string)$meta['product_name'] ?: '-';
$m['fid'] = (string)$meta['fid'];



if($m['sub_total_quantity'] != 0){
	$rs = validateFormula($meta['fid'], $bottle['ml'], $new_conc, $mg['total_mg'], $defCatClass, $settings['qStep']);
	if($rs !== 0){
		foreach ($rs as $error) {
        	$inval_materials[] = $error;
    	}
		$val_status = 1;
		$val_slug = "danger";
		$val_msg .= "Your formula contains materials not compatible with IFRA standards";
	}
}

$compliance['checked_for'] = $defCatClass;
$compliance['final_total_ml'] = (float)$mg['total_mg'];
$compliance['final_type_conc'] = (int)$concentration;
$compliance['carrier'] = (float)$carrier;
$compliance['status'] = (int)$val_status ?: 0;
$compliance['slug'] = (string)$val_slug ?: "success";
$compliance['message'] = (string)$val_msg ?: 'Formula is IFRA compliant';

$compliance['inval_materials'] = array( "data" => $inval_materials );

$response['compliance'] = $compliance;
$response['meta'] = $m;


if(empty($r)){
	$response['meta'] = [];
	$response['data'] = [];
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>
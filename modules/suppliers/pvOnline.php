<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/pvOnline.php');



$row = $_POST['start']?:0;
$limit = $_POST['length']?:10;
$order_by  = $_POST['order_by']?:'name';
$order  = $_POST['order_as']?:'ASC';


$s = trim($_POST['search']['value']);
$data = [ 
		 'request' => 'ingredients',
		 'start' => $row,
		 'length' => $limit,
		 'order_by' => $order_by,
		 'order_as' => $order,
		 'src' => 'PV_PRO',
		 'search[value]=' => $s 
		 ];
		
$output = json_decode(pvPost($pvOnlineAPI, $data));

$rx = array();
foreach ($output->ingredients as $ingredient){
  	$r['id'] = (int)$ingredient->id;
	$r['name'] = (string)filter_var ( $ingredient->name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   	$r['cas'] = (string)$ingredient->cas ?: 'N/A';
	$r['odor'] = (string)$ingredient->odor ?: 'N/A';
 	$r['profile'] = (string)$ingredient->profile ?: 'Uknown';
 	$r['physical_state'] = $ingredient->physical_state ?: 1;
 	$r['category'] = $ingredient->category ?: 0;
 	$r['type'] = (string)$ingredient->type ?: 'N/A';
	$r['IUPAC'] = (string)filter_var ( $ingredient->INCI, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
 	$r['strength'] = (string)$ingredient->strength ?: 'N/A';
 	$r['purity'] = $ingredient->purity ?: 100;
 	$r['FEMA'] = (string)$ingredient->FEMA ?: 'N/A';
 	$r['tenacity'] = (string)$ingredient->tenacity ?: 'N/A';
 	$r['chemical_name'] = (string)$ingredient->chemical_name ?: 'N/A';
 	$r['formula'] = (string)$ingredient->formula ?: 'N/A';
 	$r['flash_point'] = (string)$ingredient->flash_point ?: 'N/A';
 	$r['appearance'] = (string)$ingredient->appearance ?: 'N/A';
 	$r['notes'] = (string)$ingredient->notes ?: 'N/A';
 	$r['allergen'] = $ingredient->allergen ?: 0;
 	$r['flavor_use'] = $ingredient->flavor_use ?: 0;
 	$r['einecs'] = (string)$ingredient->einecs ?: 'N/A' ;
 	$r['usage']['limit'] = $ingredient->cat4 ?: 100;
	$r['usage']['reason'] = (string)$ingredient->risk ?: 'N/A';
 	$r['impact_top'] = $ingredient->impact_top ?: 0;
 	$r['impact_heart'] = $ingredient->impact_heart ?: 0;
	$r['impact_base'] = $ingredient->impact_base ?: 0;
	
		
	$r['stock'] = (double)0; //Not available in online
	$r['info']['byPassIFRA'] = (int)0;//Not available in online
	

  $rx[]=$r;
}

$response = array(
  "source" => 'PVOnline',
  "draw" => (int)$_POST['draw'],
  "recordsTotal" => (int)$output->ingredientsTotal,
  "recordsFiltered" => (int)$output->ingredientsFiltered,
  "data" => $rx
);

if(empty($rx)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response,JSON_UNESCAPED_UNICODE);
return;
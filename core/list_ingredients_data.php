<?php

require('../inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

require_once(__ROOT__.'/func/getIngSupplier.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/getCatByID.php');
require_once(__ROOT__.'/func/getDocument.php');

$defCatClass = $settings['defCatClass'];

$row = $_REQUEST['start']?:0;
$limit = $_REQUEST['length']?:10;

if($_REQUEST['adv']){
	if($name = mysqli_real_escape_string($conn, $_REQUEST['name'])){
		$n = $name;
	}else{
		$n = '%';
	}
	
	$name = "name LIKE '%$n%'";
	
	if($cas = mysqli_real_escape_string($conn, $_REQUEST['cas'])){
		$cas = "AND cas LIKE '%$cas%'";
	}
	
	if($odor = mysqli_real_escape_string($conn, $_REQUEST['odor'])){
		$odor = "AND odor LIKE '%$odor%'";
	}
	
	if($profile = mysqli_real_escape_string($conn, $_REQUEST['profile'])){
		$profile = "AND profile = '$profile'";
	}
	
	if($category = mysqli_real_escape_string($conn, $_REQUEST['cat'])){
		$category = "AND category = '$category'";	
	}

	$filter = "WHERE $name $cas $odor $profile $category";
	$extra = "ORDER BY name";
}

$s = $_REQUEST['search']['value'];

if($s != ''){
   $filter = "WHERE 1 AND (name LIKE '%".$s."%' OR cas LIKE '%".$s."%' OR odor LIKE '%".$s."%' )";
}

$q = mysqli_query($conn, "SELECT id,name,INCI,cas,profile,category,odor,$defCatClass,allergen,usage_type FROM ingredients $filter $extra LIMIT $row, $limit");
while($res = mysqli_fetch_array($q)){
    $ingredients[] = $res;
}

foreach ($ingredients as $ingredient) { 
	$cat = mysqli_fetch_array(mysqli_query($conn, "SELECT name,image FROM ingCategory WHERE id = '".$ingredient['category']."'"));
	
	
	$r['id'] = (int)$ingredient['id'];
	$r['name'] = (string)$ingredient['name'];
	$r['INCI'] = (string)$ingredient['INCI']?: 'N/A';
	$r['cas'] = (string)$ingredient['cas']?: 'N/A';
	$r['profile'] = (string)$ingredient['profile']?: null;
	$r['odor'] = (string)$ingredient['odor']?: 'N/A';
	
	$r['allergen'] = (int)$ingredient['allergen']?: 0;
	
	$r['category']['id'] = (int)$ingredient['category']?: 1;
	$r['category']['name'] = (string)$cat['name']?: 'N/A';
	$r['category']['image'] = (string)$cat['image']?: '/img/pv_molecule.png';
	
	if($limit = searchIFRA($ingredient['cas'],$ingredient['name'],null,$conn,$defCatClass)){
		$limit = explode(' - ', $limit);
		$r['usage']['limit'] = (float)$limit['0'];
		$r['usage']['reason'] = (string)$limit['1'];
	}else{
		$r['usage']['limit'] = (float)$ingredient[$defCatClass]?: 100;
		$r['usage']['reason'] = (int)$ingredient['usage_type'];
	}
	
	if($a = getIngSupplier($ingredient['id'],$conn)){ 
		$j = 0;
		foreach ($a as $b){
			$r['supplier'][$j]['name'] = (string)$b['name'];
			$r['supplier'][$j]['link'] = (string)$b['supplierLink'];
			$j++;
		}
	}else{
		$r['supplier'] = null;
	}	
	
	if($d = getDocument($ingredient['id'],1,$conn)){
		$i=0;
		foreach($d as $x ){
			$r['document'][$i]['name'] = (string)$x['name'];
			$r['document'][$i]['id'] = (int)$x['id'];
			$i++;
		}
	}else{
		$r['document'] = null;
	}

	$rx[]=$r;
}

$filtered = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM ingredients ".$filter));

$response = array(
  "draw" => (int)$_POST['draw'],
  "recordsTotal" => (int)$total['entries'],
  "recordsFiltered" => (int)$filtered['entries'],
  "data" => $rx
);

if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

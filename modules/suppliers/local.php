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

$row = $_POST['start']?:0;
$limit = $_POST['length']?:10;

if($_POST['adv']){
	if($name = trim(mysqli_real_escape_string($conn, $_POST['name']))){
		$n = $name;
	}else{
		$n = '%';
	}
	
	$name = "name LIKE '%$n%'";
	
	if($cas = trim(mysqli_real_escape_string($conn, $_POST['cas']))){
		$cas = "AND cas LIKE '%$cas%'";
	}
	
	if($odor = trim(mysqli_real_escape_string($conn, $_POST['odor']))){
		$odor = "AND odor LIKE '%$odor%'";
	}
	
	if($profile = mysqli_real_escape_string($conn, $_POST['profile'])){
		$profile = "AND profile = '$profile'";
	}
	
	if($category = mysqli_real_escape_string($conn, $_POST['cat'])){
		$category = "AND category = '$category'";	
	}

		
	if($synonym = mysqli_real_escape_string($conn, $_POST['synonym'])){
		$t = "synonyms,";
		$syn = "synonym LIKE '%$synonym%' AND ing = name GROUP BY name";
	}
	

	if($synonym){
		$filter = "WHERE $syn $cas $odor $profile $category";
	}else{
		$filter = "WHERE $name $cas $odor $profile $category";
	}
	$extra = "ORDER BY name";
}

$s = trim($_POST['search']['value']);

if($s != ''){
   $filter = "WHERE 1 AND (name LIKE '%".$s."%' OR cas LIKE '%".$s."%' OR odor LIKE '%".$s."%' OR INCI LIKE '%".$s."%')";
}

$q = mysqli_query($conn, "SELECT ingredients.id,name,INCI,cas,einecs,profile,category,odor,$defCatClass,allergen,usage_type,logp,formula,flash_point,molecularWeight FROM $t  ingredients $filter $extra LIMIT $row, $limit");
while($res = mysqli_fetch_array($q)){
    $ingredients[] = $res;
}

foreach ($ingredients as $ingredient) { 
	$cat = mysqli_fetch_array(mysqli_query($conn, "SELECT name,image FROM ingCategory WHERE id = '".$ingredient['category']."'"));
	
	
	$r['id'] = (int)$ingredient['id'];
	$r['name'] = (string)$ingredient['name'];
	$r['IUPAC'] = (string)$ingredient['INCI']?: 'N/A';
	$r['cas'] = (string)$ingredient['cas']?: 'N/A';
	$r['einecs'] = (string)$ingredient['einecs']?: 'N/A';
	$r['profile'] = (string)$ingredient['profile']?: null;
	$r['odor'] = (string)$ingredient['odor']?: 'N/A';
	$r['allergen'] = (int)$ingredient['allergen']?: 0;
	
	$r['techData']['LogP'] = (float)$ingredient['logp']?: 0;
	$r['techData']['formula'] = (string)$ingredient['formula']?: 'N/A';
	$r['techData']['flash_point'] = (string)$ingredient['flash_point']?: 'N/A';
	$r['techData']['molecula_weight'] = (float)$ingredient['molecularWeight']?: 0;

	
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
		unset($r['supplier']);
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
		unset($r['document']);
		foreach($d as $x ){
			$r['document'][$i]['name'] = (string)$x['name'];
			$r['document'][$i]['id'] = (int)$x['id'];
			$i++;
		}
	}else{
		unset($r['document']);// = null;
	}

	$rx[]=$r;
}

$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS entries FROM ingredients"));
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

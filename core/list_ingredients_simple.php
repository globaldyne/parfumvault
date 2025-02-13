<?php

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/getIngSupplier.php');

if(!$_POST['search']){
	$response['data'] = array("What you looking for here??");
	echo json_encode($response);
	return;
}

$s = trim($_POST['search']['term']);
$s = preg_replace("/[^a-zA-Z0-9\s\-]/", "", $s); // Remove illegal characters

if ($_POST['isDeepQ'] == "true"){
	$t = "synonyms,ingredients";
	$filter = "WHERE synonym LIKE '%$s%' AND ing = name AND synonyms.owner_id = '$userID' GROUP BY name";

} else if($_POST['isAbsolute'] == "true"){
	$t = "ingredients";	
	$filter = "WHERE name = '$s' OR cas = '$s' OR einecs = '$s' OR INCI = '$s'";
	
}else{
	$t = "ingredients";	
	$filter = " WHERE (name LIKE '%$s%' OR cas LIKE '%$s%' OR INCI LIKE '%$s%') ";
}

try {
	$query = "SELECT ingredients.id,name,INCI,cas,einecs,type,odor,physical_state,profile FROM $t $filter AND ingredients.owner_id = ? ORDER BY name ASC";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("s", $userID);
	//error_log("Executing query: " . $query);
	$stmt->execute();
	$q = $stmt->get_result();
	if (!$q) {
		throw new Exception("Database Query Failed: " . $stmt->error);
	}
} catch (Exception $e) {
	error_log($e->getMessage());
	error_log("Executing query: " . $query);
}
while($res = mysqli_fetch_array($q)){
    $ingredients[] = $res;
}
$i = 0;
foreach ($ingredients as $ingredient) { 
	if(!$settings['allow_incomplete_ingredients']) {
		$supp = getIngSupplier($ingredient['id'],1,$conn);
		if($supp['price']){
			$r['id'] = (int)$ingredient['id'];
			$r['name'] = (string)$ingredient['name'];
			$r['IUPAC'] = (string)$ingredient['INCI']?: '-';
			$r['cas'] = (string)$ingredient['cas']?: '-';
			$r['einecs'] = (string)$ingredient['einecs']?: '-';
			$r['type'] = (string)$ingredient['type'] ?: 'Unknown';
			$r['description'] = (string)$ingredient['odor'] ?: '-';
			$r['physical_state'] = (int)$ingredient['physical_state'] ?: 1;
			$r['profile'] = (string)$ingredient['profile'] ?: 'Unknown';
			$r['stock'] = (float)number_format($supp['stock'], $settings['qStep']) ?: 0;
			$r['mUnit'] = (string)$supp['mUnit'];
			
			$rx[]=$r;
			$i++;
		}
	} else {
		$r['id'] = (int)$ingredient['id'];
		$r['name'] = (string)$ingredient['name'];
		$r['IUPAC'] = (string)$ingredient['INCI']?: '-';
		$r['cas'] = (string)$ingredient['cas']?: '-';
		$r['einecs'] = (string)$ingredient['einecs']?: '-';
		$r['type'] = (string)$ingredient['type'] ?: 'Unknown';
		$r['description'] = (string)$ingredient['odor'] ?: '-';
		$r['physical_state'] = (int)$ingredient['physical_state'] ?: 1;
		$r['profile'] = (string)$ingredient['profile'] ?: 'Unknown';
		$r['stock'] = 0; // Default stock value
		$r['mUnit'] = (string)$settings['mUnit'] ?? 'ml'; // Default measurement unit
		
		$rx[]=$r;
		$i++;
	}
}


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
?>

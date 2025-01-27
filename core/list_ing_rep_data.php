<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if ($_POST['view'] == 'ingredients') {
	$ing_name = mysqli_real_escape_string($conn, base64_decode($_POST["ing_name"]));
	$ing_cas = mysqli_real_escape_string($conn, base64_decode($_POST["ing_cas"]));
	
	$q = mysqli_query($conn, "SELECT id, ing_rep_name, ing_rep_cas, notes FROM ingReplacements WHERE ing_name = '$ing_name' AND owner_id = '$userID'");
	if (mysqli_num_rows($q) == 0) { 
		$q = mysqli_query($conn, "SELECT id, ing_name, ing_cas, notes FROM ingReplacements WHERE ing_rep_name = '$ing_name' AND owner_id = '$userID'");
	}
	
	$reps = [];
	while ($res = mysqli_fetch_array($q)) {
		$reps[] = $res;
	}
	
	foreach ($reps as $rep) { 
		$r['id'] = (int)$rep['id'];
		$r['ing_rep_name'] = (string)$rep['ing_rep_name'] ?: $rep['ing_name'];
		$r['ing_rep_cas'] = (string)$rep['ing_rep_cas'] ?: $rep['ing_cas'];
		$r['notes'] = (string)$rep['notes'] ?: '-';
	
		$response['data'][] = $r;
	}
}



if ($_POST['view'] == 'formula') {
	$fid = mysqli_real_escape_string($conn, $_POST["fid"]);
	$q = mysqli_query($conn, "SELECT ingredient, ingredient_id FROM formulas WHERE fid = '$fid' AND owner_id = '$userID'");
	
	$get_rep_ings = [];
	while ($res = mysqli_fetch_array($q)) {
		$ingredient = mysqli_real_escape_string($conn, $res['ingredient']);
		$ingredient_id = (int)$res['ingredient_id'];
		
		$q2 = mysqli_query($conn, "SELECT id, ing_name, ing_rep_name, ing_rep_id, ing_rep_cas, notes FROM ingReplacements WHERE ing_name = '$ingredient' AND owner_id = '$userID'");
		$q3 = mysqli_fetch_array(mysqli_query($conn, "SELECT notes, odor FROM ingredients WHERE id = '$ingredient_id'"));
		
		while ($reps = mysqli_fetch_array($q2)) {
			$reps['ingredient_id'] = $ingredient_id;
			$get_rep_ings[] = $reps;
		}
	}
	
	foreach ($get_rep_ings as $get_rep_ing) {
		$r['original_id'] = (int)$get_rep_ing['ingredient_id'];
		$r['replacement_id'] = (int)$get_rep_ing['ing_rep_id'];
		$r['ing_name'] = (string)$get_rep_ing['ing_name'];
		$r['ing_rep_name'] = (string)$get_rep_ing['ing_rep_name'];
		$r['notes'] = (string)$get_rep_ing['notes'] ?: $q3['notes'] ?: 'No information available';
		$r['odor'] = (string)$q3['odor'] ?: 'No information available';
		
		$response['data'][] = $r;
	}
}

if (empty($response['data'])) {
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if($_POST['view'] == 'ingredients'){
	$ing_name = base64_decode($_POST["ing_name"]);
	$ing_cas = base64_decode($_POST["ing_cas"]);
	
	$q = mysqli_query($conn,"SELECT id,ing_rep_name,ing_rep_cas,notes FROM ingReplacements WHERE ing_name = '$ing_name' AND owner_id = '$userID'");
	if (mysqli_num_rows($q)==0) { 
		$q = mysqli_query($conn,"SELECT id,ing_name,ing_cas,notes FROM ingReplacements WHERE ing_rep_name = '$ing_name' AND owner_id = '$userID'");
	}
	while($res = mysqli_fetch_array($q)){
		$reps[] = $res;
	}
	
	foreach ($reps as $rep) { 
		$r['id'] = (int)$rep['id'];
		$r['ing_rep_name'] = (string)$rep['ing_rep_name'] ?: $rep['ing_name'];
		$r['ing_rep_cas'] = (string)$rep['ing_rep_cas'] ?: $rep['ing_cas'];
		$r['notes'] = (string)$rep['notes']?: '-';
	
		$response['data'][] = $r;
	}
}



if($_POST['view'] == 'formula'){
	$q = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '".$_POST["fid"]."' AND owner_id = '$userID'");
	while($res = mysqli_fetch_array($q)){
		
		$q2 = mysqli_query($conn, "SELECT id,ing_name,ing_rep_name,ing_rep_cas,notes FROM ingReplacements WHERE ing_name = '".$res['ingredient']."' AND owner_id = '$userID'");

	while($reps = mysqli_fetch_array($q2)){
			$get_rep_ings[] = $reps;
		}
	}
	
	foreach ($get_rep_ings as $get_rep_ing) { 
		$r['ing_name'] = (string)$get_rep_ing['ing_name'];
		$r['ing_rep_name'] = (string)$get_rep_ing['ing_rep_name'];
		$r['notes'] = (string)$get_rep_ing['notes'] ?: 'No info available';

		$response['data'][] = $r;
	}
}


if(empty($r)){
	$response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

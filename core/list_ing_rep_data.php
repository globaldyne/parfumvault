<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

if($_POST['view'] == 'ingredients'){
	$ing_name = base64_decode($_POST["ing_name"]);
	$ing_cas = base64_decode($_POST["ing_cas"]);
	
	$q = mysqli_query($conn, "SELECT id,ing_rep_name,ing_rep_cas,notes FROM ingReplacements WHERE ing_name = '$ing_name' OR ing_cas = '$ing_cas'");
	while($res = mysqli_fetch_array($q)){
		$reps[] = $res;
	}
	
	foreach ($reps as $rep) { 
		$r['id'] = (int)$rep['id'];
		$r['ing_rep_name'] = (string)$rep['ing_rep_name'];
		$r['ing_rep_cas'] = (string)$rep['ing_rep_cas'];
		$r['notes'] = (string)$rep['notes']?: 'N/A';
	
		$response['data'][] = $r;
	}
}



if($_POST['view'] == 'formula'){
	$q = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '".$_POST["fid"]."'");
	while($res = mysqli_fetch_array($q)){
		
		$q2 = mysqli_query($conn, "SELECT id,ing_name,ing_rep_name,ing_rep_cas FROM ingReplacements WHERE ing_name = '".$res['ingredient']."'");

	while($reps = mysqli_fetch_array($q2)){
			$get_rep_ings[] = $reps;
		}
	}
	
	foreach ($get_rep_ings as $get_rep_ing) { 
		$r['ing_name'] = (string)$get_rep_ing['ing_name'];
		$r['ing_rep_name'] = (string)$get_rep_ing['ing_rep_name'];
		
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

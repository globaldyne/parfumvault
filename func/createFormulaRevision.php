<?php 
if (!defined('pvault_panel')){ die('Not Found');}
require_once(__ROOT__.'/func/compareFormulas.php');

function createFormulaRevision($fid, $conn){
	$name = base64_decode($fid);
	$q_a = mysqli_query($conn, "SELECT name, ingredient, concentration, dilutant, quantity, notes FROM formulas WHERE fid = '$fid'");
	if(empty(mysqli_num_rows($q_a))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>unable to create revision for formula '.$name.', please make sure formula exists and contains at least one ingredient.</div>';
		return;
	}
	$current_rev = mysqli_fetch_array(mysqli_query($conn, "SELECT revision FROM formulasMetaData WHERE fid = '$fid'"));
	$nr = $current_rev['revision']+1;
	$q_b = mysqli_query($conn, "SELECT name, ingredient, concentration, dilutant, quantity, notes FROM formulasRevisions WHERE fid = '$fid' AND revision = '".$current_rev['revision']."'");
	$q = "INSERT INTO formulasRevisions (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes,revision) SELECT fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes, '$nr' FROM formulas WHERE fid = '$fid'";
	$q_meta = "UPDATE formulasMetaData SET revision = '$nr' WHERE fid = '$fid'";
	
	
	while ($formula = mysqli_fetch_array($q_a)){
	    $formula_a[] = $formula;
	}
	
	while ($formula = mysqli_fetch_array($q_b)){
	    $formula_rev[] = $formula;
	}
	if($formula_rev[0]['name']){
		if(compareFormula($formula_rev, $formula_a, array('ingredient','concentration','dilutant','quantity','notes'),null, null)){
			mysqli_query($conn, $q);
			mysqli_query($conn, $q_meta);
		}
	}else{
		mysqli_query($conn, $q);
		mysqli_query($conn, $q_meta);
	}
	return;
	
}
?>

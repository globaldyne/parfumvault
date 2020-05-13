<?php
require_once('../inc/config.php');
require_once('../inc/opendb.php');

/*
$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen('request.log', 'a');
fwrite($fp, $req_dump);
fclose($fp);
*/

if($_POST['value'] && $_GET['formula'] && $_POST['pk'] && !$_GET['settings']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, $_GET['formula']);
	$ingredient = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	mysqli_query($conn, "UPDATE formulas SET $name = '$value' WHERE name = '$formula' AND ingredient = '$ingredient'");

	
}elseif($_GET['settings'] == 'cat'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$cat_id = mysqli_real_escape_string($conn, $_POST['pk']);

	if($_POST['name'] == 'cname'){
		mysqli_query($conn, "UPDATE ingCategory SET name = '$value' WHERE id = '$cat_id'");
	}elseif($_POST['name'] == 'cnotes'){
		mysqli_query($conn, "UPDATE ingCategory SET notes = '$value' WHERE id = '$cat_id'");
	}

}elseif($_GET['settings'] == 'sup'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$sup_id = mysqli_real_escape_string($conn, $_POST['pk']);

	if($_POST['name'] == 'sname'){
		mysqli_query($conn, "UPDATE ingSuppliers SET name = '$value' WHERE id = '$sup_id'");
	}elseif($_POST['name'] == 'snotes'){
		mysqli_query($conn, "UPDATE ingSuppliers SET notes = '$value' WHERE id = '$sup_id'");
	}
	
}elseif($_GET['settings'] == 'profile'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$prof_id = mysqli_real_escape_string($conn, $_POST['pk']);

	if($_POST['name'] == 'pname'){
		mysqli_query($conn, "UPDATE ingProfiles SET name = '$value' WHERE id = '$prof_id'");
	}elseif($_POST['name'] == 'pnotes'){
		mysqli_query($conn, "UPDATE ingProfiles SET notes = '$value' WHERE id = '$prof_id'");
	}
	
	
}else{
	header('Location: /');
}
?>
<?php
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');


$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen('../tmp/pvault.log', 'a');
fwrite($fp, $req_dump);
fclose($fp);


if($_POST['value'] && $_GET['formula'] && $_POST['pk'] && !$_GET['settings']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, $_GET['formula']);
	$ingredient = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	mysqli_query($conn, "UPDATE formulas SET $name = '$value' WHERE name = '$formula' AND ingredient = '$ingredient'");

}elseif($_GET['formulaMeta']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, $_GET['formulaMeta']);
	$ingredient = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	//if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE fid = '$fid'"))){

		mysqli_query($conn, "UPDATE formulasMetaData SET $name = '$value' WHERE name = '$formula'");
	
	//}
	
}elseif($_GET['rename']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, $_GET['rename']);
	$fid = base64_encode($value);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE fid = '$fid'"))){
		echo 'Name already exists';
	}else{
		mysqli_query($conn, "UPDATE formulasMetaData SET name = '$value', fid = '$fid' WHERE name = '$formula'");
		mysqli_query($conn, "UPDATE formulas SET name = '$value', fid = '$fid' WHERE name = '$formula'");
	}
	
}elseif($_GET['settings'] == 'cat'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$cat_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	mysqli_query($conn, "UPDATE ingCategory SET $name = '$value' WHERE id = '$cat_id'");

}elseif($_GET['settings'] == 'sup'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$sup_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	mysqli_query($conn, "UPDATE ingSuppliers SET $name = '$value' WHERE id = '$sup_id'");
	
}elseif($_GET['bottle']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$bottle = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	mysqli_query($conn, "UPDATE bottles SET $name = '$value' WHERE id = '$bottle'");
	
}elseif($_GET['lid']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$lid = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	

	mysqli_query($conn, "UPDATE lids SET $name = '$value' WHERE id = '$lid'");
	
	
}else{
	header('Location: /');
}
?>

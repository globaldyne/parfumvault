<?php
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');


if($_POST['value'] && $_GET['formula'] && $_POST['pk'] && !$_GET['settings']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, $_GET['formula']);
	$ingredient = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	mysqli_query($conn, "UPDATE formulas SET $name = '$value' WHERE name = '$formula' AND ingredient = '$ingredient'");
	return;
	
}elseif($_GET['formulaMeta']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, $_GET['formulaMeta']);
	$ingredient = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	mysqli_query($conn, "UPDATE formulasMetaData SET $name = '$value' WHERE name = '$formula'");
	return;
	
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
	return;
	
}elseif($_GET['settings'] == 'cat'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$cat_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	mysqli_query($conn, "UPDATE ingCategory SET $name = '$value' WHERE id = '$cat_id'");
	return;

}elseif($_GET['settings'] == 'sup'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$sup_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	mysqli_query($conn, "UPDATE ingSuppliers SET $name = '$value' WHERE id = '$sup_id'");
	return;

}elseif($_GET['settings'] == 'ucategories'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$pk = mysqli_real_escape_string($conn, $_POST['pk']);

	mysqli_query($conn, "UPDATE IFRACategories SET description = '$value' WHERE id = '$pk'");
	return;
	
}elseif($_GET['supp'] == 'add'){
	$description = mysqli_real_escape_string($conn, $_GET['description']);
	$name = mysqli_real_escape_string($conn, $_GET['name']);
	
	if(empty($name)){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong> Supplier name required</div>';
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE name = '$name'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$name.'</strong> Supplier already exists!</div>';
		return;
	}

	if(mysqli_query($conn, "INSERT INTO ingSuppliers (name,notes) VALUES ('$name','$description')")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Supplier '.$name.' added!</div>';
	}
	return;

}elseif($_GET['supp'] == 'delete' && $_GET['ID']){
	$ID = mysqli_real_escape_string($conn, $_GET['ID']);
	$supplier = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '$ID'"));

	if(mysqli_query($conn, "DELETE FROM ingSuppliers WHERE id = '$ID'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Supplier <strong>'.$supplier['name'].'</strong> removed!</div>';
	}
	return;
	
}elseif($_GET['bottle']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$bottle = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	mysqli_query($conn, "UPDATE bottles SET $name = '$value' WHERE id = '$bottle'");
	return;
	
}elseif($_GET['lid']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$lid = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	mysqli_query($conn, "UPDATE lids SET $name = '$value' WHERE id = '$lid'");
	return;
	
//ADD ALLERGEN
}elseif($_GET['allergen'] == 'add'){
	$allgName = mysqli_real_escape_string($conn, $_GET['allgName']);
	$allgCAS = mysqli_real_escape_string($conn, $_GET['allgCAS']);	
	$allgPerc = rtrim(mysqli_real_escape_string($conn, $_GET['allgPerc']),'%');
	$ing = mysqli_real_escape_string($conn, $_GET['ing']);

	if(empty($allgName)){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Name is required!</div>';
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM allergens WHERE name = '$allgName' AND ing = '$ing'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$allgName.' already exists!</div>';
	}else{
		mysqli_query($conn, "INSERT INTO allergens (name,cas,percentage,ing) VALUES ('$allgName','$allgCAS','$allgPerc','$ing')");
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$allgName.'</strong> added to the list!</div>';
	}
	
	if($_GET['addToIng'] == 'true'){
		if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$allgName'")))){
			mysqli_query($conn, "INSERT INTO ingredients (name,cas) VALUES ('$allgName','$allgCAS')");
		}
	}

	return;

//UPDATE ALLERGEN
}elseif($_GET['allergen'] == 'update'){
	$value = rtrim(mysqli_real_escape_string($conn, $_POST['value']),'%');
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ing = mysqli_real_escape_string($conn, $_GET['ing']);

	mysqli_query($conn, "UPDATE allergens SET $name = '$value' WHERE id = '$id' AND ing='$ing'");
	return;

//DELETE ALLERGEN	
}elseif($_GET['allergen'] == 'delete'){

	$id = mysqli_real_escape_string($conn, $_GET['allgID']);
	$ing = mysqli_real_escape_string($conn, $_GET['ing']);

	$delQ = mysqli_query($conn, "DELETE FROM allergens WHERE id = '$id' AND ing='$ing'");	
	if($delQ){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$ing.'</strong> removed!</div>';
	}
	return;
	
	
//CUSTOMERS - ADD
}elseif($_POST['customer'] == 'add'){
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	if(empty($name)){
		echo '<div class="alert alert-danger alert-dismissible">Customer name is required.</div>';
		return;
	}
	$address = mysqli_real_escape_string($conn, $_POST['address']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$web = mysqli_real_escape_string($conn, $_POST['web']);
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM customers WHERE name = '$name'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$name.' already exists!</div>';
	}elseif(mysqli_query($conn, "INSERT INTO customers (name,address,email,web) VALUES ('$name', '$address', '$email', '$web')")){
		echo '<div class="alert alert-success alert-dismissible">Customer '.$name.' added!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible">Error adding customer.</div>';
	}
	return;
	
//CUSTOMERS - DELETE
}elseif($_GET['customer'] == 'delete' && $_GET['customer_id']){
	$customer_id = mysqli_real_escape_string($conn, $_GET['customer_id']);
	if(mysqli_query($conn, "DELETE FROM customers WHERE id = '$customer_id'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Customer deleted!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error deleting customer.</div>';
	}
	return;
	
	
//CUSTOMERS - UPDATE
}elseif($_GET['customer'] == 'update'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	mysqli_query($conn, "UPDATE customers SET $name = '$value' WHERE id = '$id'");
	return;
	
	
}else{
	header('Location: /');
	exit;
}
?>

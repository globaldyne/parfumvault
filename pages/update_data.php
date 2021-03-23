<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/inc/settings.php');


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

//DELETE INGREDIENT	
}elseif($_GET['ingredient'] == 'delete' && $_GET['ing_id']){

	$id = mysqli_real_escape_string($conn, $_GET['ing_id']);
	$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingredients WHERE id = '$id'"));
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '".$ing['name']."'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$ing['name'].'</strong> is in use by at least one formula and cannot be removed!</div>';
	}elseif(mysqli_query($conn, "DELETE FROM ingredients WHERE id = '$id'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Ingredient <strong>'.$ing['name'].'</strong> removed from the database!</div>';
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
	
}elseif($_POST['manage'] == 'ingredient'){
	$ing = mysqli_real_escape_string($conn, $_POST['ing']);

	$INCI = trim(mysqli_real_escape_string($conn, $_POST["INCI"]));
	$cas = trim(mysqli_real_escape_string($conn, $_POST["cas"]));
	$fema = trim(mysqli_real_escape_string($conn, $_POST["fema"]));
	$type = mysqli_real_escape_string($conn, $_POST["type"]);
	$strength = mysqli_real_escape_string($conn, $_POST["strength"]);
	$category = mysqli_real_escape_string($conn, $_POST["category"]);
	$supplier = mysqli_real_escape_string($conn, $_POST["supplier"]);
	$supplier_link = mysqli_real_escape_string($conn, $_POST["supplier_link"]);
	$profile = mysqli_real_escape_string($conn, $_POST["profile"]);
	$price = validateInput($_POST["price"]);
	$tenacity = mysqli_real_escape_string($conn, $_POST["tenacity"]);
	$formula = mysqli_real_escape_string($conn, $_POST["formula"]);
	$chemical_name = mysqli_real_escape_string($conn, $_POST["chemical_name"]);
	$flash_point = mysqli_real_escape_string($conn, $_POST["flash_point"]);
	$appearance = mysqli_real_escape_string($conn, $_POST["appearance"]);
	$ml = validateInput($_POST["ml"]);
	$solvent = mysqli_real_escape_string($conn, $_POST["solvent"]);
	$odor = ucfirst(trim(mysqli_real_escape_string($conn, $_POST["odor"])));
	$notes = ucfirst(trim(mysqli_real_escape_string($conn, $_POST["notes"])));
	$purity = validateInput($_POST["purity"]);
	$soluble = mysqli_real_escape_string($conn, $_POST["soluble"]);
	$logp = mysqli_real_escape_string($conn, $_POST["logp"]);
	
	$cat1 = validateInput($_POST["cat1"]);
	$cat2 = validateInput($_POST["cat2"]);
	$cat3 = validateInput($_POST["cat3"]);
	$cat4 = validateInput($_POST["cat4"]);
	$cat5A = validateInput($_POST["cat5A"]);
	$cat5B = validateInput($_POST["cat5B"]);
	$cat5C = validateInput($_POST["cat5C"]);
	$cat5D = validateInput($_POST["cat5D"]);
	$cat6 = validateInput($_POST["cat6"]);
	$cat7A = validateInput($_POST["cat7A"]);
	$cat7B = validateInput($_POST["cat7B"]);
	$cat8 = validateInput($_POST["cat8"]);
	$cat9 = validateInput($_POST["cat9"]);
	$cat10A = validateInput($_POST["cat10A"]);
	$cat10B = validateInput($_POST["cat10B"]);
	$cat11A = validateInput($_POST["cat11A"]);
	$cat11B = validateInput($_POST["cat11B"]);
	$cat12 = validateInput($_POST["cat12"]);
	
	$manufacturer = mysqli_real_escape_string($conn, $_POST["manufacturer"]);
	$impact_top = mysqli_real_escape_string($conn, $_POST["impact_top"]);
	$impact_base = mysqli_real_escape_string($conn, $_POST["impact_base"]);
	$impact_heart = mysqli_real_escape_string($conn, $_POST["impact_heart"]);
	$usage_type = mysqli_real_escape_string($conn, $_POST["usage_type"]);

	if($_POST["isAllergen"] == 'true') {
		$allergen = '1';
	}else{
		$allergen = '0';
	}
	if($_POST["flavor_use"] == 'true') {
		$flavor_use = '1';
	}else{
		$flavor_use = '0';
	}
	if(empty($ml)){
		$ml = '10';
	}
	
	if($_POST['noUsageLimit'] == 'true'){
		$noUsageLimit = '1';
	}else{
		$noUsageLimit = '0';
	}
	
	if($_POST['isPrivate'] == 'true'){
		$isPrivate = '1';
	}else{
		$isPrivate = '0';
	}
	
	if(empty($_POST['name'])){
		$query = "UPDATE ingredients SET cas = '$cas', FEMA = '$fema', type = '$type', strength = '$strength', category='$category', supplier='$supplier', supplier_link='$supplier_link', profile='$profile', price='$price', tenacity='$tenacity', chemical_name='$chemical_name', flash_point='$flash_point', appearance='$appearance', notes='$notes', ml='$ml', odor='$odor', purity='$purity', allergen='$allergen', formula='$formula', flavor_use='$flavor_use', cat1 = '$cat1', cat2 = '$cat2', cat3 = '$cat3', cat4 = '$cat4', cat5A = '$cat5A', cat5B = '$cat5B', cat5C = '$cat5C', cat5D = '$cat5D', cat6 = '$cat6', cat7A = '$cat7A', cat7B = '$cat7B', cat8 = '$cat8', cat9 = '$cat9', cat10A = '$cat10A', cat10B = '$cat10B', cat11A = '$cat11A', cat11B = '$cat11B', cat12 = '$cat12', soluble = '$soluble', logp = '$logp', manufacturer = '$manufacturer', impact_top = '$impact_top', impact_heart = '$impact_heart', impact_base = '$impact_base', usage_type = '$usage_type', solvent = '$solvent', INCI = '$INCI', noUsageLimit = '$noUsageLimit', isPrivate = '$isPrivate' WHERE name='$ing'";
		if(mysqli_query($conn, $query)){
			echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Ingredient <strong>'.$ing.'</strong> updated!</div>';
		}else{
			echo '<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> Failed to update!</div>';
		}
	}else{
		$name = mysqli_real_escape_string($conn, $_POST["name"]);

		$query = "INSERT INTO ingredients (name, INCI, cas, FEMA, type, strength, category, profile, notes, odor, purity, solvent, allergen) VALUES ('$name', '$INCI', '$cas', '$fema', '$type', '$strength', '$category', '$profile',  '$notes', '$odor', '$purity', '$solvent', '$allergen')";
		
		if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$name'"))){
			echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$name.' already exists!</div>';
		}else{
			if(mysqli_query($conn, $query)){
				echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Ingredient <strong>'.$name.'</strong> added!</div>';
			}else{
				echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Failed to add!</div>';
			}
		}
	}


	return;

	
}else{
	header('Location: /');
	exit;
}


?>

<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/sanChar.php');
require_once(__ROOT__.'/func/priceScrape.php');

//UPDATE ING DOCUMENT
if($_GET['ingDoc'] == 'update'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ownerID = mysqli_real_escape_string($conn, $_GET['ingID']);

	mysqli_query($conn, "UPDATE documents SET $name = '$value' WHERE ownerID = '$ownerID' AND id='$id'");
	return;
}


//DELETE ING DOCUMENT	
if($_GET['doc'] == 'delete'){

	$id = mysqli_real_escape_string($conn, $_GET['id']);
	$ownerID = mysqli_real_escape_string($conn, $_GET['ingID']);
							
	mysqli_query($conn, "DELETE FROM documents WHERE id = '$id' AND ownerID='$ownerID'");
	return;
}

//GET SUPPLIER PRICE
if($_POST['ingSupplier'] == 'getPrice'){
	$ingID = mysqli_real_escape_string($conn, $_POST['ingID']);
	$ingSupplierID = mysqli_real_escape_string($conn, $_POST['ingSupplierID']);
	$size = mysqli_real_escape_string($conn, $_POST['size']);
	$supplier_link = urldecode($_POST['sLink']);
	
	$supp_data = mysqli_fetch_array(mysqli_query($conn, "SELECT price_tag_start,price_tag_end,add_costs,price_per_size FROM ingSuppliers WHERE id = '$ingSupplierID'"));
	
	if($newPrice = priceScrape($supplier_link,$size,$supp_data['price_tag_start'],$supp_data['price_tag_end'],$supp_data['add_costs'],$supp_data['price_per_size'])){
		if(mysqli_query($conn, "UPDATE suppliers SET price = '$newPrice' WHERE ingSupplierID = '$ingSupplierID' AND ingID='$ingID'")){
			echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Price updated</strong></div>';
		}
	}else{
	 		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error getting the price from the supplier</strong></div>';
	}
	return;
}
//ADD ING SUPPLIER
if($_POST['ingSupplier'] == 'add'){
	if(empty($_POST['supplier_id']) || empty($_POST['supplier_link']) || empty($_POST['supplier_size'])){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Missing fields!</div>';
		return;
	}
	if(!is_numeric($_POST['supplier_size'])){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Only numeric values allowed in size and price fields!</div>';
		return;
	}
	$ingID = mysqli_real_escape_string($conn, $_POST['ingID']);
	$supplier_id = mysqli_real_escape_string($conn, $_POST['supplier_id']);
	$supplier_link = mysqli_real_escape_string($conn, $_POST['supplier_link']);	
	$supplier_size = mysqli_real_escape_string($conn, $_POST['supplier_size']);
	$supplier_price = mysqli_real_escape_string($conn, $_POST['supplier_price']);
	$supplier_manufacturer = mysqli_real_escape_string($conn, $_POST['supplier_manufacturer']);
	$supplier_name = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '$supplier_id'"));

	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingSupplierID FROM suppliers WHERE ingSupplierID = '$supplier_id' AND ingID = '$ingID'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$allgName.' already exists!</div>';
	}else{
		
		if(!mysqli_num_rows(mysqli_query($conn, "SELECT ingSupplierID FROM suppliers WHERE ingID = '$ingID'"))){
		   $preferred = '1';
		}else{
			$preferred = '0';
		}
		
		if(mysqli_query($conn, "INSERT INTO suppliers (ingSupplierID,ingID,supplierLink,price,size,manufacturer,preferred) VALUES ('$supplier_id','$ingID','$supplier_link','$supplier_price','$supplier_size','$supplier_manufacturer','$preferred')")){
			echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$supplier_name['name'].'</strong> added to the list!</div>';
		}else{
			echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> '.mysqli_error($conn).'</div>';
		}
	}
	return;
}

//UPDATE ING SUPPLIER
if($_GET['ingSupplier'] == 'update'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ingID = mysqli_real_escape_string($conn, $_GET['ingID']);

	mysqli_query($conn, "UPDATE suppliers SET $name = '$value' WHERE ingSupplierID = '$id' AND ingID='$ingID'");
	return;
}

//UPDATE PREFERRED SUPPLIER
if($_GET['ingSupplier'] == 'preferred'){
	$sID = mysqli_real_escape_string($conn, $_GET['sID']);
	$ingID = mysqli_real_escape_string($conn, $_GET['ingID']);
	$status = mysqli_real_escape_string($conn, $_GET['status']);
	
	mysqli_query($conn, "UPDATE suppliers SET preferred = '0' WHERE ingID='$ingID'");
	mysqli_query($conn, "UPDATE suppliers SET preferred = '$status' WHERE ingSupplierID = '$sID' AND ingID='$ingID'");
	return;
}

//DELETE ING SUPPLIER	
if($_GET['ingSupplier'] == 'delete'){

	$sID = mysqli_real_escape_string($conn, $_GET['sID']);
	$ingID = mysqli_real_escape_string($conn, $_GET['ingID']);
	/*
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM suppliers WHERE id = '$sID' AND ingID = '$ingID' AND preferred = '1'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Preferred supplier cannot be removed. Set as preferred another one first!</div>';
		return;
	}
	*/							
	mysqli_query($conn, "DELETE FROM suppliers WHERE id = '$sID' AND ingID='$ingID'");
	return;
}

if($_POST['value'] && $_GET['formula'] && $_POST['pk']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, $_GET['formula']);
	$ingredient = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	mysqli_query($conn, "UPDATE formulas SET $name = '$value' WHERE name = '$formula' AND ingredient = '$ingredient'");
	return;
}

if($_GET['formulaMeta']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, $_GET['formulaMeta']);
	$ingredient = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	mysqli_query($conn, "UPDATE formulasMetaData SET $name = '$value' WHERE name = '$formula'");
	return;
}

if($_GET['protect']){
	$fid = mysqli_real_escape_string($conn, $_GET['protect']);
	
	if($_GET['isProtected'] == 'true'){
		$isProtected = '1';
	}else{
		$isProtected = '0';
	}
	if(mysqli_query($conn, "UPDATE formulasMetaData SET isProtected = '$isProtected' WHERE fid = '$fid'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Success!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Something went wrong.</div>';
	}
	return;
}

if($_GET['formula'] &&  $_GET['defView']){
	$fid = mysqli_real_escape_string($conn, $_GET['formula']);
	$defView = mysqli_real_escape_string($conn, $_GET['defView']);
	
	if(mysqli_query($conn, "UPDATE formulasMetaData SET defView = '$defView' WHERE fid = '$fid'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Default formula view changed!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Something went wrong.</div>';
	}
	return;
}

if($_GET['formula'] &&  $_GET['catClass']){
	$fid = mysqli_real_escape_string($conn, $_GET['formula']);
	$catClass = mysqli_real_escape_string($conn, $_GET['catClass']);
	
	if(mysqli_query($conn, "UPDATE formulasMetaData SET catClass = '$catClass' WHERE fid = '$fid'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Purpose changed!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Something went wrong.</div>';
	}
	return;
}

if($_GET['rename']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, $_GET['rename']);
	$fid = base64_encode($value);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE fid = '$fid'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Name already exists</a>';
	}else{
		mysqli_query($conn, "UPDATE formulasMetaData SET name = '$value', fid = '$fid' WHERE name = '$formula'");
		if(mysqli_query($conn, "UPDATE formulas SET name = '$value', fid = '$fid' WHERE name = '$formula'")){
			echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Formula renamed.</a>';
		}
	
	}
	return;	
}

if($_GET['settings'] == 'cat'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$cat_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	mysqli_query($conn, "UPDATE ingCategory SET $name = '$value' WHERE id = '$cat_id'");
	return;
}

if($_GET['settings'] == 'sup'){
	$value = htmlentities($_POST['value']);
	$sup_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	mysqli_query($conn, "UPDATE ingSuppliers SET $name = '$value' WHERE id = '$sup_id'");
	return;	
}

if($_POST['supp'] == 'add'){
	$description = mysqli_real_escape_string($conn, $_POST['description']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$platform = mysqli_real_escape_string($conn, $_POST['platform']);
	$price_tag_start = htmlentities($_POST['price_tag_start']);
	$price_tag_end = htmlentities($_POST['price_tag_end']);
	$add_costs = is_numeric($_POST['add_costs']);
	$min_ml = mysqli_real_escape_string($conn, $_POST['min_ml']);
	$min_gr = mysqli_real_escape_string($conn, $_POST['min_gr']);

	if(empty($min_ml)){
		$min_ml = 0;
	}
	if(empty($min_gr)){
		$min_gr = 0;
	}		 
	
	if(empty($name)){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong> Supplier name required</div>';
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE name = '$name'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$name.'</strong> Supplier already exists!</div>';
		return;
	}

	if(mysqli_query($conn, "INSERT INTO ingSuppliers (name,platform,price_tag_start,price_tag_end,add_costs,notes,min_ml,min_gr) VALUES ('$name','$platform','$price_tag_start','$price_tag_end','$add_costs','$description','$min_ml','$min_gr')")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Supplier '.$name.' added!</div>';
	}else{
		echo mysqli_error($conn);
	}
	return;
}

if($_GET['supp'] == 'delete' && $_GET['ID']){
	$ID = mysqli_real_escape_string($conn, $_GET['ID']);
	$supplier = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '$ID'"));

	if(mysqli_query($conn, "DELETE FROM ingSuppliers WHERE id = '$ID'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Supplier <strong>'.$supplier['name'].'</strong> removed!</div>';
	}
	return;
}

if($_GET['bottle']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$bottle = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	mysqli_query($conn, "UPDATE bottles SET $name = '$value' WHERE id = '$bottle'");
	return;	
}

if($_GET['lid']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$lid = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	mysqli_query($conn, "UPDATE lids SET $name = '$value' WHERE id = '$lid'");
	return;
}

//ADD ALLERGEN
if($_GET['allergen'] == 'add'){
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
}

//UPDATE ALLERGEN
if($_GET['allergen'] == 'update'){
	$value = rtrim(mysqli_real_escape_string($conn, $_POST['value']),'%');
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ing = mysqli_real_escape_string($conn, $_GET['ing']);

	mysqli_query($conn, "UPDATE allergens SET $name = '$value' WHERE id = '$id' AND ing='$ing'");
	return;
}

//DELETE ALLERGEN	
if($_GET['allergen'] == 'delete'){

	$id = mysqli_real_escape_string($conn, $_GET['allgID']);
	$ing = mysqli_real_escape_string($conn, $_GET['ing']);

	$delQ = mysqli_query($conn, "DELETE FROM allergens WHERE id = '$id' AND ing='$ing'");	
	if($delQ){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$ing.'</strong> removed!</div>';
	}
	return;
}

//DELETE INGREDIENT	
if($_GET['ingredient'] == 'delete' && $_GET['ing_id']){

	$id = mysqli_real_escape_string($conn, $_GET['ing_id']);
	$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingredients WHERE id = '$id'"));
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '".$ing['name']."'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$ing['name'].'</strong> is in use by at least one formula and cannot be removed!</div>';
	}elseif(mysqli_query($conn, "DELETE FROM ingredients WHERE id = '$id'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Ingredient <strong>'.$ing['name'].'</strong> removed from the database!</div>';
	}

	return;
}

//CUSTOMERS - ADD
if($_POST['customer'] == 'add'){
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
}

//CUSTOMERS - DELETE
if($_GET['customer'] == 'delete' && $_GET['customer_id']){
	$customer_id = mysqli_real_escape_string($conn, $_GET['customer_id']);
	if(mysqli_query($conn, "DELETE FROM customers WHERE id = '$customer_id'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Customer deleted!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error deleting customer.</div>';
	}
	return;
}
	
//CUSTOMERS - UPDATE
if($_GET['customer'] == 'update'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	mysqli_query($conn, "UPDATE customers SET $name = '$value' WHERE id = '$id'");
	return;	
}

if($_POST['manage'] == 'ingredient'){
	$ing = mysqli_real_escape_string($conn, $_POST['ing']);

	$INCI = trim(mysqli_real_escape_string($conn, $_POST["INCI"]));
	
	$cas = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["cas"])));
	$reach = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["reach"])));
	$fema = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["fema"])));
																						
	$type = mysqli_real_escape_string($conn, $_POST["type"]);
	$strength = mysqli_real_escape_string($conn, $_POST["strength"]);
	$category = mysqli_real_escape_string($conn, $_POST["category"]);
	$profile = mysqli_real_escape_string($conn, $_POST["profile"]);
	$tenacity = mysqli_real_escape_string($conn, $_POST["tenacity"]);
	$formula = mysqli_real_escape_string($conn, $_POST["formula"]);
	$chemical_name = mysqli_real_escape_string($conn, $_POST["chemical_name"]);
	$flash_point = mysqli_real_escape_string($conn, $_POST["flash_point"]);
	$appearance = mysqli_real_escape_string($conn, $_POST["appearance"]);
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
	
	$impact_top = mysqli_real_escape_string($conn, $_POST["impact_top"]);
	$impact_base = mysqli_real_escape_string($conn, $_POST["impact_base"]);
	$impact_heart = mysqli_real_escape_string($conn, $_POST["impact_heart"]);
	$usage_type = mysqli_real_escape_string($conn, $_POST["usage_type"]);
	$molecularWeight = mysqli_real_escape_string($conn, $_POST["molecularWeight"]);
	$physical_state = mysqli_real_escape_string($conn, $_POST["physical_state"]);


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
		$query = "UPDATE ingredients SET cas = '$cas', reach = '$reach', FEMA = '$fema', type = '$type', strength = '$strength', category='$category', profile='$profile', tenacity='$tenacity', chemical_name='$chemical_name', flash_point='$flash_point', appearance='$appearance', notes='$notes', odor='$odor', purity='$purity', allergen='$allergen', formula='$formula', flavor_use='$flavor_use', cat1 = '$cat1', cat2 = '$cat2', cat3 = '$cat3', cat4 = '$cat4', cat5A = '$cat5A', cat5B = '$cat5B', cat5C = '$cat5C', cat5D = '$cat5D', cat6 = '$cat6', cat7A = '$cat7A', cat7B = '$cat7B', cat8 = '$cat8', cat9 = '$cat9', cat10A = '$cat10A', cat10B = '$cat10B', cat11A = '$cat11A', cat11B = '$cat11B', cat12 = '$cat12', soluble = '$soluble', logp = '$logp', impact_top = '$impact_top', impact_heart = '$impact_heart', impact_base = '$impact_base', usage_type = '$usage_type', solvent = '$solvent', INCI = '$INCI', noUsageLimit = '$noUsageLimit', isPrivate = '$isPrivate', molecularWeight = '$molecularWeight', physical_state = '$physical_state' WHERE name='$ing'";
		if(mysqli_query($conn, $query)){
			echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Ingredient <strong>'.$ing.'</strong> updated!</div>';
		}else{
			echo '<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> '.mysqli_error($conn).'</div>';
		}
	}else{
		$name = sanChar(mysqli_real_escape_string($conn, $_POST["name"]));

		$query = "INSERT INTO ingredients (name, INCI, cas, reach, FEMA, type, strength, category, profile, notes, odor, purity, solvent, allergen, physical_state) VALUES ('$name', '$INCI', '$cas', '$reach', '$fema', '$type', '$strength', '$category', '$profile',  '$notes', '$odor', '$purity', '$solvent', '$allergen', '1')";
		
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
}


header('Location: /');
exit;

?>

<?php 
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/labelMap.php');
require_once(__ROOT__.'/func/get_formula_notes.php');

if($_POST['update_rating'] == '1' && $_POST['fid'] && is_numeric($_POST['score'])){
	mysqli_query($conn,"UPDATE formulasMetaData SET rating = '".$_POST['score']."' WHERE id = '".$_POST['fid']."'");
}

//EXCLUDE/INCLUDE INGREDIENT
if($_POST['action'] == 'excIng' && $_POST['ingID']){
	$id = mysqli_real_escape_string($conn, $_POST['ingID']);
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$ing = mysqli_real_escape_string($conn, $_POST['ingName']);

	$status = (int)$_POST['status'];
	if($status == 1){
		$st = 'excluded';
	}else{
		$st = 'included';
	}
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '$fid'"));
	if($meta['isProtected'] == FALSE){
		if(mysqli_query($conn, "UPDATE formulas SET exclude_from_calculation = '$status' WHERE id  = '$id'")){
			$response['success'] = $ing.' is now '. $st;
		}else{
			$response['error'] = $ing.' cannot be '.$st.' from the formula!';
		}
	}
	
	echo json_encode($response);
	return;
}

//IS MADE
if($_POST['isMade'] && $_POST['fid']){
	$fid = mysqli_real_escape_string($conn,$_POST['fid']);
	
	$quant = mysqli_query($conn, "SELECT ingredient,quantity FROM formulas WHERE fid = '$fid'");
	while($get_quant = mysqli_fetch_array($quant)){
		$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '".$get_quant['ingredient']."'"));
		$q = "UPDATE suppliers SET stock = GREATEST(0, stock - '".$get_quant['quantity']."') WHERE ingID = '".$ing['id']."' AND stock = GREATEST(stock, '".$get_quant['quantity']."')";
		$upd = mysqli_query($conn, $q);	
		
	}
	if($upd){
		mysqli_query($conn,"UPDATE formulasMetaData SET isMade = '1', madeOn = NOW() WHERE fid = '$fid'");
		$response['success'] = 'Inventory updated';
	}else{
		$response['error'] = mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}


//CREATE ACCORD
if($_POST['accordName'] && $_POST['accordProfile'] && $_POST['fid']){
	require_once(__ROOT__.'/func/genFID.php');

	$fid = mysqli_real_escape_string($conn,$_POST['fid']);
	$accordProfile = mysqli_real_escape_string($conn,$_POST['accordProfile']);
	$accordName = mysqli_real_escape_string($conn,$_POST['accordName']);
	$nfid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');
	
	if(mysqli_num_rows(mysqli_query($conn,"SELECT name FROM formulasMetaData WHERE name = '$accordName'"))){
		$response['error'] = 'A formula with name <strong>'.$accordName.'</strong> already exists, please choose a different name!';
		echo json_encode($response);
		return;
	}
									
	$get_formula = mysqli_query($conn,"SELECT ingredient FROM formulas WHERE fid = '$fid'");
	while($formula = mysqli_fetch_array($get_formula)){
        if($i = mysqli_fetch_array(mysqli_query($conn,"SELECT name,profile FROM ingredients WHERE profile = '$accordProfile' AND name ='".$formula['ingredient']."'"))){
        	mysqli_query($conn, "INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes) SELECT '$nfid', '$accordName', ingredient, ingredient_id, concentration, dilutant, quantity, notes FROM formulas WHERE fid = '$fid' AND ingredient = '".$i['name']."'");
		}
	}
	if(mysqli_query($conn,"INSERT INTO formulasMetaData (fid,name) VALUES ('$nfid','$accordName')")){
		$response['success'] =  'Accord <a href="/?do=Formula&id='.mysqli_insert_id($conn).'" target="_blank">'.$accordName.'</a> created!';
	}
	echo json_encode($response);
	return;
}

//RESTORE REVISION
if($_GET['restore'] == 'rev' && $_GET['revision'] && $_GET['fid']){
	$fid = mysqli_real_escape_string($conn,$_GET['fid']);
	$revision = $_GET['revision'];
	
	mysqli_query($conn,"DELETE FROM formulas WHERE fid = '$fid'");
	if(mysqli_query($conn, "INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes) SELECT fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes FROM formulasRevisions WHERE fid = '$fid' AND revision = '$revision'")){
		mysqli_query($conn, "UPDATE formulasMetaData SET revision = '$revision' WHERE fid = '$fid'");
		echo  '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Formula revision restored!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Unable to restore revision! '.mysqli_error($conn).'</div>';
	}
	return;
}

//DELETE REVISION
if($_GET['delete'] == 'rev' && $_GET['revision'] && $_GET['fid']){
	$fid = mysqli_real_escape_string($conn,$_GET['fid']);
	$revision = $_GET['revision'];
	
	if(mysqli_query($conn,"DELETE FROM formulasRevisions WHERE fid = '$fid' AND revision = '$revision'")){
		echo  '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Formula revision deleted!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Unable to delete revision! '.mysqli_error($conn).'</div>';
	}
	return;
}

//MANAGE VIEW
if($_GET['manage_view'] == '1'){
	$ing = mysqli_real_escape_string($conn,str_replace('_', ' ',$_GET['ex_ing']));
	
	if($_GET['ex_status'] == 'true'){
		$status = '0';
	}elseif($_GET['ex_status'] == 'false'){
		$status = '1';
	}
	$fid = urldecode($_GET['fid']);
	
	$q = mysqli_query($conn, "UPDATE formulas SET exclude_from_summary = '$status' WHERE fid = '$fid' AND ingredient = '$ing'");
	if($q){
		echo  '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>View updated!</div>';
	}else{
		echo  '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Something went wrong</div>';
	}
	return;
}

//AMOUNT TO MAKE
if($_POST['fid'] && $_POST['SG'] && $_POST['amount']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$SG = mysqli_real_escape_string($conn, $_POST['SG']);
	$amount = mysqli_real_escape_string($conn, $_POST['amount']);

	$new_amount = $amount * $SG;
	$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '$fid'"));

	$q = mysqli_query($conn, "SELECT quantity,ingredient FROM formulas WHERE fid = '$fid'");
	while($cur =  mysqli_fetch_array($q)){
		$nq = $cur['quantity']/$mg['total_mg']*$new_amount;		
		if(empty($nq)){
			$response['error'] = 'Something went wrong...';
			echo json_encode($response);
			return;
		}
		mysqli_query($conn,"UPDATE formulas SET quantity = '$nq' WHERE fid = '$fid' AND quantity = '".$cur['quantity']."' AND ingredient = '".$cur['ingredient']."'");
	}
	return;
}


//DIVIDE - MULTIPLY
if($_POST['formula'] && $_POST['do'] == 'scale'){
	$fid = mysqli_real_escape_string($conn, $_POST['formula']);
	
	$q = mysqli_query($conn, "SELECT quantity,ingredient FROM formulas WHERE fid = '$fid'");
	while($cur =  mysqli_fetch_array($q)){
		if($_POST['scale'] == 'multiply'){
			$nq = $cur['quantity']*2;
		}elseif($_POST['scale'] == 'divide'){
			$nq = $cur['quantity']/2;
		}
		
		mysqli_query($conn,"UPDATE formulas SET quantity = '$nq' WHERE fid = '$fid' AND quantity = '".$cur['quantity']."' AND ingredient = '".$cur['ingredient']."'");
	}	
	
	return;
}

//DELETE INGREDIENT
if($_POST['action'] == 'deleteIng' && $_POST['ingID'] && $_POST['ing']){
	$id = mysqli_real_escape_string($conn, $_POST['ingID']);
	$ing = mysqli_real_escape_string($conn, $_POST['ing']);
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '$fid'"));

	if($meta['isProtected'] == FALSE){
		if(mysqli_query($conn, "DELETE FROM formulas WHERE id = '$id' AND fid = '$fid'")){
			$response['success'] = $ing.' removed from the formula';
			$lg = "REMOVED: $ing removed";
			mysqli_query($conn, "INSERT INTO formula_history (fid,change_made,user) VALUES ('".$meta['id']."','$lg','".$user['fullName']."')");
		}else{
			$response['error'] = $ing.' cannot be removed from the formula';
		}
	}
	echo json_encode($response);
	return;
}

//ADD INGREDIENT
if($_POST['action'] == 'addIng' && $_POST['fid']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$ingredient_id = mysqli_real_escape_string($conn, $_POST['ingredient']);
	$quantity = preg_replace("/[^0-9.]/", "", mysqli_real_escape_string($conn, $_POST['quantity']));
	$concentration = preg_replace("/[^0-9.]/", "", mysqli_real_escape_string($conn, $_POST['concentration']));
	$dilutant = mysqli_real_escape_string($conn, $_POST['dilutant']);
	$ingredient = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingredients WHERE id = '$ingredient_id'"));
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected,name FROM formulasMetaData WHERE fid = '$fid'"));
	if($meta['isProtected'] == FALSE){
		
		if (empty($quantity) || empty($concentration)){
			$response['error'] = 'Missing required fields';
			echo json_encode($response);
			return;
		}
			
		if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient_id FROM formulas WHERE ingredient_id = '$ingredient_id' AND fid = '$fid'"))){
			$response['error'] = $ingredient['name'].' already exists in formula!';
			echo json_encode($response);
			return;
		}
	
		if(mysqli_query($conn,"INSERT INTO formulas(fid,name,ingredient,ingredient_id,concentration,quantity,dilutant) VALUES('$fid','".$meta['name']."','".$ingredient['name']."','".$ingredient_id."','$concentration','$quantity','$dilutant')")){
			$response['success'] = '<strong>'.$quantity.$settings['mUnit'].'</strong> of <strong>'.$ingredient['name'].'</strong> added to the formula!';
			$lg = "ADDED: ".$ingredient['name']." $quantity".$settings['mUnit']." @$concentration% $dilutant";
			mysqli_query($conn, "INSERT INTO formula_history (fid,change_made,user) VALUES ('".$meta['id']."','$lg','".$user['fullName']."')");
			mysqli_query($conn, "UPDATE formulasMetaData SET status = '1' WHERE fid = '".$meta['fid']."' AND status = '0' AND isProtected = '0'");
			echo json_encode($response);
			return;
		}
		
	}
	return;
}

//REPLACE INGREDIENT
if($_GET['action'] == 'repIng' && $_GET['fid']){
	$fid = mysqli_real_escape_string($conn, $_GET['fid']);
	$ingredient = mysqli_real_escape_string($conn, $_POST['value']);
	$oldIngredient = mysqli_real_escape_string($conn, $_POST['pk']);
	$ingredient_id = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$ingredient'"));
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '$fid'"));
	if($meta['isProtected'] == FALSE){
		if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '$ingredient' AND fid = '$fid'"))){
			$response['error'] = '<strong>Error: </strong>'.$ingredient.' already exists in formula!';
			header('Content-Type: application/json');
			echo json_encode($response);
			return;
		}
		
		if(mysqli_query($conn, "UPDATE formulas SET ingredient = '$ingredient', ingredient_id = '".$ingredient_id['id']."' WHERE ingredient = '$oldIngredient' AND fid = '$fid'")){
			$response['success'] = $oldIngredient.' replaced by '.$ingredient;
			$lg = "REPLACED: $oldIngredient WITH $ingredient";
			mysqli_query($conn, "INSERT INTO formula_history (fid,change_made,user) VALUES ('".$meta['id']."','$lg','".$user['fullName']."')");
		}else{
			$response['error'] = 'Error replacing '.$oldIngredient;
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode($response);
	return;
}

//Convert to ingredient
if($_POST['action'] == 'conv2ing' && $_POST['ingName'] && $_POST['fid']){
	$name = mysqli_real_escape_string($conn, $_POST['ingName']);
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$fmame = mysqli_real_escape_string($conn, $_POST['fname']);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$name'"))){
		$response['error'] = '<a href="/?do=ingredients&search='.$name.'" target="_blank">'.$name.'</a> already exists';
		echo json_encode($response);
		return;
	}

	$formula_q = mysqli_query($conn, "SELECT ingredient,quantity,concentration FROM formulas WHERE fid = '$fid'");
	while ($formula = mysqli_fetch_array($formula_q)){
		$ing_data = mysqli_fetch_array(mysqli_query($conn,"SELECT cas FROM ingredients WHERE name = '".$formula['ingredient']."'"));
		$conc = number_format($formula['quantity']/100 * 100, $settings['qStep']);
		$conc_p = number_format($formula['concentration'] / 100 * $conc, $settings['qStep']);
						
		mysqli_query($conn, "INSERT INTO allergens (ing, name, cas, percentage) VALUES ('$name','".$formula['ingredient']."','".$ing_data['cas']."','".$conc_p."')");
	}
			
	if(mysqli_query($conn, "INSERT INTO ingredients (name, type, cas, notes) VALUES ('$name','Base','Mixture','Converted from formula $fname')")){
		$response['success'] = '<a href="/?do=ingredients&search='.$name.'" target="_blank">'.$name.'</a> converted to ingredient';
		echo json_encode($response);
	}
	return;

}

//CLONE FORMULA
if($_POST['action'] == 'clone' && $_POST['fid']){
	require_once(__ROOT__.'/func/genFID.php');

	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$fname = mysqli_real_escape_string($conn, $_POST['fname']);

	$newName = $fname.' - (Copy)';
	$newFid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE fid = '$newFid'"))){
		$response['success'] = $newName.' already exists, please remove or rename it first!</div>';
	}else{
		$sql.=mysqli_query($conn, "INSERT INTO formulasMetaData (fid, name, notes, profile, sex, defView, product_name, catClass) SELECT '$newFid', '$newName', notes, profile, sex, defView, '$newName', catClass FROM formulasMetaData WHERE fid = '$fid'");
		$sql.=mysqli_query($conn, "INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes) SELECT '$newFid', '$newName', ingredient, ingredient_id, concentration, dilutant, quantity, notes FROM formulas WHERE fid = '$fid'");
	}
	if($nID = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$newFid'"))){
		$response['success'] = $fname.' cloned as <a href="?do=Formula&id='.$nID['id'].'" target="_blank">'.$newName.'</a>!</div>';
	}
	echo json_encode($response);
	return;
}

//ADD NEW FORMULA
if($_POST['action'] == 'addFormula'){
	if(empty($_POST['name'])){
		$response['error'] = 'Formula name is required.';
		echo json_encode($response);
		return;
	}
	require_once(__ROOT__.'/func/genFID.php');
	
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$notes = mysqli_real_escape_string($conn, $_POST['notes']);
	$profile = mysqli_real_escape_string($conn, $_POST['profile']);
	$catClass = mysqli_real_escape_string($conn, $_POST['catClass']);
	$finalType = mysqli_real_escape_string($conn, $_POST['finalType']);
	$customer_id = $_POST['customer']?:0;
	$fid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE name = '$name'"))){
		$response['error'] = $name.' already exists!';
	}else{
		if(mysqli_query($conn, "INSERT INTO formulasMetaData (fid, name, notes, profile, catClass, finalType, customer_id) VALUES ('$fid', '$name', '$notes', '$profile', '$catClass', '$finalType', '$customer_id')")){
			$last_id = mysqli_insert_id($conn);
			$response = array(
				"success" => array(
				"id" => (int)$last_id,
				"msg" => "$name added!",
				)
			);
		}else{
			$response['error'] = 'Something went wrong...'.mysqli_error($conn);
		}
	}

	echo json_encode($response);
	return;
}
	
//DELETE FORMULA
if($_POST['action'] == 'delete' && $_POST['fid']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$fname = mysqli_real_escape_string($conn, $_POST['fname']);

	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid' AND isProtected = '1'"))){
		$response['error'] = 'Error deleting formula '.$fname.' is protected.</div>';
		echo json_encode($response);
		return;
	}
		
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid'"));

	if(mysqli_query($conn, "DELETE FROM formulas WHERE fid = '$fid'")){
		mysqli_query($conn, "DELETE FROM formulasMetaData WHERE fid = '$fid'");
		mysqli_query($conn, "DELETE FROM formulasRevisions WHERE fid = '$fid'");
		mysqli_query($conn, "DELETE FROM formula_history WHERE fid = '".$meta['id']."'");
		$response['success'] = 'Formula '.$fname.' deleted!';
	}else{
		$response['error'] = 'Error deleting '.$fname.' formula!';
	}
	echo json_encode($response);
	return;
}

//RESET ING IN MAKE FORMULA
if($_POST['action'] == 'makeFormula' && $_POST['undo'] == '1'){
	$q = trim($_POST['originalQuantity']);
	$ingID = mysqli_real_escape_string($conn, $_POST['ingID']);

	if(mysqli_query($conn, "UPDATE makeFormula SET toAdd = '1', overdose = '0', quantity = '".$_POST['originalQuantity']."' WHERE id = '".$_POST['ID']."'")){
		$response['success'] = $_POST['ing'].'\'s quantity reset';
		
		if($_POST['resetStock'] == "true"){
			mysqli_query($conn, "UPDATE suppliers SET stock = stock + $q WHERE ingID = '$ingID' AND preferred = '1'");
			$response['success'] .= "<br/><strong>Stock increased by ".$q.$settings['mUnit']."</strong>";
		}
		echo json_encode($response);
	}
	return;
}

//MAKE FORMULA
if($_POST['action'] == 'makeFormula' && $_POST['fid'] && $_POST['q'] && $_POST['qr'] && $_POST['id']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$id = mysqli_real_escape_string($conn, $_POST['id']);
	$ingID = mysqli_real_escape_string($conn, $_POST['ingId']);
	$qr = trim($_POST['qr']);
	if(!is_numeric($_POST['q'])){
		$response['error'] = 'Invalid value';
		echo json_encode($response);
		return;
	}
						 
	$q = trim($_POST['q']);

	if($qr == $q){
		if(mysqli_query($conn, "UPDATE makeFormula SET toAdd = '0' WHERE fid = '$fid' AND id = '$id'")){
			$response['success'] = $_POST['ing'].' added!';
		}
	}else{
		$sub_tot = $qr - $q;
		if(mysqli_query($conn, "UPDATE makeFormula SET quantity='$sub_tot' WHERE fid = '$fid' AND id = '$id'")){
			$response['success'] = 'Formula updated!';
		}
	}
	
	if($_POST['updateStock'] == "true"){
		mysqli_query($conn, "UPDATE suppliers SET stock = stock - $q WHERE ingID = '$ingID' AND preferred = '1'");
		$response['success'] .= "<br/><strong>Stock deducted by ".$q.$settings['mUnit']."</strong>";
	}	
	
	if($qr < $q){
		if(mysqli_query($conn, "UPDATE makeFormula SET overdose = '$q' WHERE fid = '$fid' AND id = '$id'")){
			$response['success'] = $_POST['ing'].' is overdosed, <strong>'.$q.'<strong> added';
		}
	}
	
	if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid' AND toAdd = '1'"))){
		$response['success'] = '<strong>All materials added. You should mark formula as complete now!</strong>';
	}
	
	
	echo json_encode($response);
	return;
}
//MARK COMPLETE
if($_POST['action'] == 'todo' && $_POST['fid'] && $_POST['markComplete']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid' AND toAdd = '1'"))){
		$response['error'] = '<strong>Formula is pending materials to add, cannot be marked as complete.</strong>';
		echo json_encode($response);
		return;
	}
	if(mysqli_query($conn,"UPDATE formulasMetaData SET isMade = '1', toDo = '0', madeOn = NOW(), status = '2' WHERE fid = '$fid'")){
		
		mysqli_query($conn, "DELETE FROM makeFormula WHERE fid = '$fid'");
		
		$response['success'] = '<strong>Formula is complete</strong>';
	}
	
	echo json_encode($response);
	return;
}


//TODO ADD FORMULA
if($_POST['action'] == 'todo' && $_POST['fid'] && $_POST['add']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$fname = mysqli_real_escape_string($conn, $_POST['fname']);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid' AND toDo = '1'"))){
		$response['error'] = 'Formula '.$fname.' already added';
		echo json_encode($response);
		return;
	}
								
	if(mysqli_query($conn, "INSERT INTO makeFormula (fid, name, ingredient, concentration, dilutant, quantity, originalQuantity, toAdd) SELECT fid, name, ingredient, concentration, dilutant, quantity, quantity, '1' FROM formulas WHERE fid = '$fid' AND exclude_from_calculation = '0'")){
		mysqli_query($conn, "UPDATE formulasMetaData SET toDo = '1', status = '1', isMade = '0' WHERE fid = '$fid'");
		$response['success'] = 'Formula <a href="/?do=todo">'.$fname.'</a> added in To Make list!';		
	}
	echo json_encode($response);
	return;
}

//TODO REMOVE FORMULA
if($_POST['action'] == 'todo' && $_POST['fid'] && $_POST['remove']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "DELETE FROM makeFormula WHERE fid = '$fid'")){
		mysqli_query($conn, "UPDATE formulasMetaData SET toDo = '0', status = '0', isMade = '0' WHERE fid = '$fid'");
		$response['success'] = $name.' removed';
		echo json_encode($response);
	}
	return;
}

//CART MANAGE
if($_POST['action'] == 'addToCart' && $_POST['material'] && $_POST['quantity']){
	$material = mysqli_real_escape_string($conn, $_POST['material']);
	$quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
	$purity = mysqli_real_escape_string($conn, $_POST['purity']);
	$ingID = mysqli_real_escape_string($conn, $_POST['ingID']);

	$qS = mysqli_fetch_array(mysqli_query($conn, "SELECT ingSupplierID, supplierLink FROM suppliers WHERE ingID = '$ingID'"));
	
	if(empty($qS['supplierLink'])){
		$response['error'] = $material.' cannot be added to cart as missing supplier info. Please update material supply details first.';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM cart WHERE name = '$material'"))){
		if(mysqli_query($conn, "UPDATE cart SET quantity = quantity + '$quantity' WHERE name = '$material'")){
			$response['success'] = 'Additional '.$quantity.$settings['mUnit'].' of '.$material.' added to the cart.';
		}
	}
									
	if(mysqli_query($conn, "INSERT INTO cart (ingID,name,quantity,purity) VALUES ('$ingID','$material','$quantity','$purity')")){
		$response['success'] = $material.' added to the cart!';
	}
	
	echo json_encode($response);
	return;
}

if($_POST['action'] == 'removeFromCart' && $_POST['materialId']){
	$materialId = mysqli_real_escape_string($conn, $_POST['materialId']);

	if(mysqli_query($conn, "DELETE FROM cart WHERE id = '$materialId'")){
		$response['success'] = $_POST['materialName'].' removed from cart!';
		echo json_encode($response);
	}
}

//PRINTING
if($_GET['action'] == 'printLabel' && $_GET['name']){
	if (file_exists(__ROOT__.'/tmp/labels/') === FALSE) {
		mkdir(__ROOT__.'/tmp/labels/', 0740, true);
	}
	$name = $_GET['name'];
		
	if($settings['label_printer_size'] == '62' || $settings['label_printer_size'] == '62 --red'){
		
		if($_GET['batchID']){
			$bNo = $_GET['batchID'];
		}else{
			$bNO = 'N/A';
		}
				
		$q = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$name'"));
		$info = "Production: ".date("d/m/Y")."\nProfile: ".$q['profile']."\nSex: ".$q['sex']."\nB. NO: ".$bNo."\nDescription:\n\n".wordwrap($q['notes'],30);
	}
	
	$dim =  explode(',',labelMap($settings['label_printer_size']));
	
	$w = $dim['0'];
	$h = $dim['1'];
	
	$lbl = imagecreatetruecolor($w, $h);

	$white = imagecolorallocate($lbl, 255, 255, 255);
	$black = imagecolorallocate($lbl, 0, 0, 0);	
	
	imagefilledrectangle($lbl, 0, 0, $w, $h, $white);
	
	$text = trim(base64_decode($name).$extras);
	$font = __ROOT__.'/fonts/Arial.ttf';

	imagettftext($lbl, $settings['label_printer_font_size'], 0, 0, 50, $black, $font, $text);
	$lblF = imagerotate($lbl, 90 ,0);
	
	if($settings['label_printer_size'] == '62' || $settings['label_printer_size'] == '62 --red'){
		imagettftext($lblF, 25, 0, 200, 300, $black, $font, $info);
	}
	$extras = '';
	if($_GET['dilution'] && $_GET['dilutant']){
		$extras = ' @'.$_GET['dilution'].'% in '.base64_decode($_GET['dilutant']);
						//font size 15 rotate 0 center 360 top 50
		imagettftext($lblF, $settings['label_printer_font_size']/3, 90, 120, 570, $black, $font, $extras);
		
	}
	$CAS = 'CAS: '.$_GET['cas'];
	imagettftext($lblF, $settings['label_printer_font_size']/2, 90, 90, 565, $black, $font, $CAS);
	$save = __ROOT__.'/tmp/labels/'.base64_encode($text.'png');

	if(imagepng($lblF, $save)){
		imagedestroy($lblF);
		shell_exec('/usr/bin/brother_ql -m '.$settings['label_printer_model'].' -p tcp://'.$settings['label_printer_addr'].' print -l '.$settings['label_printer_size'].' '. $save);
		//echo '<img src="/tmp/labels/'.base64_encode($text.'png').'"/>';
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Print sent!</div>';
	}
	
	return;
}

//PRINT BOX LABEL
if($_GET['action'] == 'printBoxLabel' && $_GET['name']){
	if (file_exists(__ROOT__.'/tmp/labels/') === FALSE) {
		mkdir(__ROOT__.'/tmp/labels/', 0740, true);
	}
	
	if(empty($_GET['copies']) || !is_numeric($_GET['copies'])){
		$copies = '1';
	}else{
		$copies = intval($_GET['copies']);
	}
	
	if($settings['label_printer_size'] == '62' || $settings['label_printer_size'] == '62 --red'){
		$name = mysqli_real_escape_string($conn, $_GET['name']);
		$q = mysqli_fetch_array(mysqli_query($conn, "SELECT product_name FROM formulasMetaData WHERE fid = '$name'"));
		$qIng = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '$name'");
		
		while($ing = mysqli_fetch_array($qIng)){
			$chName = mysqli_fetch_array(mysqli_query($conn, "SELECT chemical_name,name FROM ingredients WHERE name = '".$ing['ingredient']."' AND allergen = '1'"));
			$allergen[] = $chName['chemical_name']?:$chName['name'];
		}
		$allergen[] = 'Denatureted Ethyl Alcohol '.$_GET['carrier'].'% Vol, Fragrance, DPG, Distilled Water';

		if($_GET['batchID']){
			$bNo = $_GET['batchID'];
		}else{
			$bNO = 'N/A';
		}
		if($settings['brandName']){
			$brand = $settings['brandName'];
		}else{
			$brand = 'PV Pro';
		}
		$allergenFinal = implode(", ",array_filter(array_unique($allergen)));
		$info = "FOR EXTERNAL USE ONLY. \nKEEP AWAY FROM HEAT AND FLAME. \nKEEP OUT OF REACH OF CHILDREN. \nAVOID SPRAYING IN EYES. \n \nProduction: ".date("d/m/Y")." \nB. NO: ".$bNo." \n$brand";
		$w = '720';
		$h = '860';
	}
	if($_GET['download'] == 'text'){
		echo '<pre>';
		echo 'INGREDIENTS'."\n\n";
		echo wordwrap ($allergenFinal, 90)."\n\n";
		echo wordwrap ($info, 50)."\n\n";
		echo '</pre>';
		return;
	}

	$lbl = imagecreatetruecolor($h, $w);

	$white = imagecolorallocate($lbl, 255, 255, 255);
	$black = imagecolorallocate($lbl, 0, 0, 0);	
	
	imagefilledrectangle($lbl, 0, 0, $h, $w, $white);
	
	$text = strtoupper($q['product_name']);
	$font = __ROOT__.'/fonts/Arial.ttf';
	//font size 15 rotate 0 center 360 top 50
	imagettftext($lbl, 25, 0, 300, 50, $black, $font, 'INGREDIENTS');
	$lblF = imagerotate($lbl, 0 ,0);
	
	imagettftext($lblF, 17, 0, 0, 100, $black, $font, wordwrap ($allergenFinal, 90));
	imagettftext($lblF, 20, 0, 150, 490, $black, $font, wordwrap ($info, 50));

	$save = __ROOT__.'/tmp/labels/'.base64_encode($text.'png');

	if(imagepng($lblF, $save)){
		imagedestroy($lblF);
		if($_GET['download'] == 'image'){
			//echo '<img src="/tmp/labels/'.base64_encode($text.'png').'"/>';
			echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><a href="'.'/tmp/labels/'.base64_encode($text.'png').'" target="_blank">Get Label here</a></div>';
			return;
		}
		for ($k = 0; $k < $copies; $k++){
			shell_exec('/usr/bin/brother_ql -m '.$settings['label_printer_model'].' -p tcp://'.$settings['label_printer_addr'].' print -l '.$settings['label_printer_size'].' '. $save);
		}
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Print sent!</div>';
	}
	return;
}

?>

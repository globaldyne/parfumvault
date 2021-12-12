<?php 
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/labelMap.php');
require_once(__ROOT__.'/func/get_formula_notes.php');

//CREATE ACCORD
if($_POST['accordName'] && $_POST['accordProfile'] && $_POST['fid']){
	$fid = mysqli_real_escape_string($conn,$_POST['fid']);
	$accordProfile = mysqli_real_escape_string($conn,$_POST['accordProfile']);
	$accordName = mysqli_real_escape_string($conn,$_POST['accordName']);
	$nfid = base64_encode($accordName);
	
	if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM formulasMetaData WHERE fid = '$nfid'"))){
		echo  '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>A formula with name '.$accordName.' already exists, please choose a different name!</div>';
		return;
	}
									
	$get_formula = mysqli_query($conn,"SELECT ingredient FROM formulas WHERE fid = '$fid'");
	while($formula = mysqli_fetch_array($get_formula)){
        if($i = mysqli_fetch_array(mysqli_query($conn,"SELECT name,profile FROM ingredients WHERE profile = '$accordProfile' AND name ='".$formula['ingredient']."'"))){
        	mysqli_query($conn, "INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes) SELECT '$nfid', '$accordName', ingredient, ingredient_id, concentration, dilutant, quantity, notes FROM formulas WHERE fid = '$fid' AND ingredient = '".$i['name']."'");
		}
	}
	if(mysqli_query($conn,"INSERT INTO formulasMetaData (fid,name) VALUES ('$nfid','$accordName')")){
		$acc = mysqli_fetch_array(mysqli_query($conn,"SELECT id FROM formulasMetaData WHERE fid = '$nfid'"));
		echo  '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Accord <a href="?do=Formula&id='.$acc['id'].'" target="_blank">'.$accordName.'</a> created!</div>';
	}
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
if($_GET['fid'] && $_GET['SG'] && $_GET['amount']){
	$fid = mysqli_real_escape_string($conn, $_GET['fid']);
	$SG = mysqli_real_escape_string($conn, $_GET['SG']);
	$amount = mysqli_real_escape_string($conn, $_GET['amount']);

	$new_amount = $amount * $SG;
	$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '$fid'"));

	$q = mysqli_query($conn, "SELECT quantity,ingredient FROM formulas WHERE fid = '$fid'");
	while($cur =  mysqli_fetch_array($q)){
		$nq = $cur['quantity']/$mg['total_mg']*$new_amount;		
		if(empty($nq)){
			print 'Something went wrong...';
			return;
		}
		mysqli_query($conn,"UPDATE formulas SET quantity = '$nq' WHERE fid = '$fid' AND quantity = '".$cur['quantity']."' AND ingredient = '".$cur['ingredient']."'");
	}
	return;
}


//DIVIDE - MULTIPLY
if($_GET['formula'] && $_GET['do']){
	$formula = mysqli_real_escape_string($conn, $_GET['formula']);
	
	$q = mysqli_query($conn, "SELECT quantity,ingredient FROM formulas WHERE name = '$formula'");
	while($cur =  mysqli_fetch_array($q)){
		if($_GET['do'] == 'multiply'){
			$nq = $cur['quantity']*2;
		}elseif($_GET['do'] == 'divide'){
			$nq = $cur['quantity']/2;
		}
		
		if(empty($nq)){
			print 'error';
			return;
		}
		
		mysqli_query($conn,"UPDATE formulas SET quantity = '$nq' WHERE name = '$formula' AND quantity = '".$cur['quantity']."' AND ingredient = '".$cur['ingredient']."'");
	}
	return;
}

//DELETE INGREDIENT
if($_GET['action'] == 'deleteIng' && $_GET['ingID'] && $_GET['ing']){
	$id = mysqli_real_escape_string($conn, $_GET['ingID']);
	$ing = mysqli_real_escape_string($conn, $_GET['ing']);
	$fname = mysqli_real_escape_string($conn, $_GET['fname']);
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '".base64_encode($_GET['fname'])."'"));
	if($meta['isProtected'] == FALSE){
		
		if(mysqli_query($conn, "DELETE FROM formulas WHERE id = '$id' AND name = '$fname'")){
			echo  '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'.$ing.' removed from the formula!</div>';
			$lg = "REMOVED: $ing removed";
			mysqli_query($conn, "INSERT INTO formula_history (fid,change_made,user) VALUES ('".$meta['id']."','$lg','".$user['fullName']."')");
		}else{
			echo  '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'.$ing.' cannot be removed from the formula!</div>';
		}
	}
	return;
}

//ADD INGREDIENT
if($_GET['action'] == 'addIng' && $_GET['fname']){
	$fname = mysqli_real_escape_string($conn, $_GET['fname']);
	$ingredient = mysqli_real_escape_string($conn, $_GET['ingredient']);
	$quantity = preg_replace("/[^0-9.]/", "", mysqli_real_escape_string($conn, $_GET['quantity']));
	$concentration = preg_replace("/[^0-9.]/", "", mysqli_real_escape_string($conn, $_GET['concentration']));
	$dilutant = mysqli_real_escape_string($conn, $_GET['dilutant']);
	$ingredient_id = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$ingredient'"));
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '".base64_encode($_GET['fname'])."'"));
	if($meta['isProtected'] == FALSE){
		
		if (empty($quantity) || empty($concentration)){
			echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>Missing fields</div>';
		}else
			
		if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '$ingredient' AND name = '$fname'"))){
			echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$ingredient.' already exists in formula!</div>';
		}else{
	
			if(mysqli_query($conn,"INSERT INTO formulas(fid,name,ingredient,ingredient_id,concentration,quantity,dilutant) VALUES('".base64_encode($fname)."','$fname','$ingredient','".$ingredient_id['id']."','$concentration','$quantity','$dilutant')")){
				echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$quantity.'ml</strong> of <strong>'.$ingredient.'</strong> added to the formula!</div>';
			$lg = "ADDED: $ingredient $quantity @$concentration% $dilutant";
			mysqli_query($conn, "INSERT INTO formula_history (fid,change_made,user) VALUES ('".$meta['id']."','$lg','".$user['fullName']."')");
			}else{
				echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error adding '.$ingredient.'!</div>';
			}
		}
	}
	return;
}

//REPLACE INGREDIENT
if($_GET['action'] == 'repIng' && $_GET['fname']){
	$fname = mysqli_real_escape_string($conn, $_GET['fname']);
	$ingredient = mysqli_real_escape_string($conn, $_REQUEST['value']);
	$oldIngredient = mysqli_real_escape_string($conn, $_REQUEST['pk']);
	$ingredient_id = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$ingredient'"));
	
	$isProtected = mysqli_fetch_array(mysqli_query($conn, "SELECT isProtected FROM formulasMetaData WHERE fid = '".base64_encode($_GET['fname'])."'"));
	if($isProtected['isProtected'] == FALSE){
		
		if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '$ingredient' AND name = '$fname'"))){
			echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$ingredient.' already exists in formula!</div>';
		}else{
			if(mysqli_query($conn, "UPDATE formulas SET ingredient = '$ingredient', ingredient_id = '".$ingredient_id['id']."' WHERE ingredient = '$oldIngredient' AND name = '$fname'")){
				echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'.$oldIngredient.' replaced with '.$ingredient.'!</div>';
			}else{
				echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error replacing '.$oldIngredient.'</div>';
			}
		}
	}
	return;
}

//Convert to ingredient
if($_POST['action'] == 'conv2ing' && $_POST['ingName'] && $_POST['formula']){
	$name = mysqli_real_escape_string($conn, $_POST['ingName']);
	$fid = mysqli_real_escape_string($conn, $_POST['formula']);
	$fmame = base64_decode($fid);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$name'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$name.' already exists</div>';
		return;
	}

	$formula_q = mysqli_query($conn, "SELECT ingredient,quantity,concentration FROM formulas WHERE fid = '$fid'");
	while ($formula = mysqli_fetch_array($formula_q)){
		$ing_data = mysqli_fetch_array(mysqli_query($conn,"SELECT cas FROM ingredients WHERE name = '".$formula['ingredient']."'"));
		$conc = number_format($formula['quantity']/100 * 100, 3);
		$conc_p = number_format($formula['concentration'] / 100 * $conc, 3);
						
		mysqli_query($conn, "INSERT INTO allergens (ing, name, cas, percentage) VALUES ('$name','".$formula['ingredient']."','".$ing_data['cas']."','".$conc_p."')");
	}
			
	if(mysqli_query($conn, "INSERT INTO ingredients (name, type, cas, notes) VALUES ('$name','Base','Mixture','Converted from formula $fname')")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'.base64_decode($fid).' converted to ingredient <a href="/pages/mgmIngredient.php?id='.base64_encode($name).'" class="popup-link" target="_blank">'.$name.'</a>!</div>';
	}
	return;

}

//CLONE FORMULA
if($_GET['action'] == 'clone' && $_GET['formula']){
	$fid = mysqli_real_escape_string($conn, $_GET['formula']);
	$newName = base64_decode($fid).' - (Copy)';
	$newFid = base64_encode($newName);
		if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE fid = '$newFid'"))){
			echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$newName.' already exists, please remove or rename it first!</div>';
		}else{
			$sql.=mysqli_query($conn, "INSERT INTO formulasMetaData (fid, name, notes, profile, sex, defView, product_name, catClass) SELECT '$newFid', '$newName', notes, profile, sex, defView, '$newName', catClass FROM formulasMetaData WHERE fid = '$fid'");
			$sql.=mysqli_query($conn, "INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes) SELECT '$newFid', '$newName', ingredient, ingredient_id, concentration, dilutant, quantity, notes FROM formulas WHERE fid = '$fid'");
		}
	if($nID = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$newFid'"))){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'.base64_decode($fid).' cloned as <a href="?do=Formula&id='.$nID['id'].'" target="_blank">'.$newName.'</a>!</div>';
	}
	return;
}

//ADD NEW FORMULA
if($_POST['action'] == 'addFormula'){
	if(empty($_POST['name'])){
		echo '<div class="alert alert-danger alert-dismissible"><strong>Formula name is required.</strong></div>';
		return;
	}
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$notes = mysqli_real_escape_string($conn, $_POST['notes']);
	$profile = mysqli_real_escape_string($conn, $_POST['profile']);
	$catClass = mysqli_real_escape_string($conn, $_POST['catClass']);
	$fid = base64_encode($name);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE fid = '$fid'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$name.' already exists!</div>';
	}else{
		$q = mysqli_query($conn, "INSERT INTO formulasMetaData (fid, name, notes, profile, catClass) VALUES ('$fid', '$name', '$notes', '$profile', '$catClass')");
		if($nID = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid'"))){
			echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong><a href="?do=Formula&id='.$nID['id'].'">'.$name.'</a></strong> added!</div>';
		}else{
			echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Something went wrong...</strong></div>';
		}
	}

	return;
}
	
//DELETE FORMULA
if($_GET['action'] == 'delete' && $_GET['fid']){
	$fid = mysqli_real_escape_string($conn, $_GET['fid']);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid' AND isProtected = '1'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> formula '.base64_decode($fid).' is protected.</div>';
		return;
	}
	
	if(mysqli_query($conn, "DELETE FROM formulas WHERE fid = '$fid'")){
		mysqli_query($conn, "DELETE FROM formulasMetaData WHERE fid = '$fid'");
		mysqli_query($conn,"DELETE FROM formulasRevisions WHERE fid = '$fid'");
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Formula '.base64_decode($fid).' deleted!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error</strong> deleting '.base64_decode($fid).' formula!</div>';
	}
	return;
}

//MAKE FORMULA
if($_GET['action'] == 'makeFormula' && $_GET['fid'] && $_GET['q'] && $_GET['qr'] && $_GET['ingId']){
	$fid = mysqli_real_escape_string($conn, $_GET['fid']);
	$ingId = mysqli_real_escape_string($conn, $_GET['ingId']);
	$qr = trim($_GET['qr']);
	$q = trim($_GET['q']);

	if($qr == $q){
		if(mysqli_query($conn, "UPDATE makeFormula SET toAdd = '0' WHERE fid = '$fid' AND id = '$ingId'")){
			echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Added!</div>';
		}else{
			echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error</strong> '.mysqli_error($conn).'</div>';
		}
	}else{
		$sub_tot = $qr - $q;
		if(mysqli_query($conn, "UPDATE makeFormula SET quantity='$sub_tot' WHERE fid = '$fid' AND id = '$ingId'")){
			echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Updated!</div>';
		}
		
	}
	return;
}

//TODO ADD FORMULA
if($_GET['action'] == 'todo' && $_GET['fid'] && $_GET['add']){
	$fid = mysqli_real_escape_string($conn, $_GET['fid']);
	$name = base64_decode($fid);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Formula <strong>'.$name.'</strong> already exists!</div>';
	}else{							
		if(mysqli_query($conn, "INSERT INTO makeFormula (fid, name, ingredient, concentration, dilutant, quantity, toAdd) SELECT fid, name, ingredient, concentration, dilutant, quantity, '1' FROM formulas WHERE fid = '$fid'")){
			echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Formula <a href="?do=todo">'.$name.'</a> added in To Make list!</div>';
		}
	}
	return;
}

//TODO REMOVE FORMULA
if($_GET['action'] == 'todo' && $_GET['fid'] && $_GET['remove']){
	$fid = mysqli_real_escape_string($conn, $_GET['fid']);

	$todo = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM makeFormula WHERE fid = '$fid'"));
	
	if(mysqli_query($conn, "DELETE FROM makeFormula WHERE fid = '$fid'")){
		$msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Formula <strong>'.$todo['name'].'</strong> removed!</div>';
	}
	return;
}

//CART MANAGE
if($_GET['action'] == 'addToCart' && $_GET['material'] && $_GET['quantity']){
	$material = mysqli_real_escape_string($conn, $_GET['material']);
	$quantity = mysqli_real_escape_string($conn, $_GET['quantity']);
	$purity = mysqli_real_escape_string($conn, $_GET['purity']);
	$ingID = mysqli_real_escape_string($conn, $_GET['ingID']);

		
	$qS = mysqli_fetch_array(mysqli_query($conn, "SELECT ingSupplierID, supplierLink FROM suppliers WHERE ingID = '$ingID'"));
	
	if(empty($qS['supplierLink'])){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$material.'</strong> cannot be added to cart as missing supplier info. Please update material supply details first.</div>';
		return;
	}
	
	if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM cart WHERE name = '$material'"))){
		if(mysqli_query($conn, "UPDATE cart SET quantity = quantity + '$quantity' WHERE name = '$material'")){
			echo '<div class="alert alert-info alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Additional <strong>'.$quantity.$settings['mUnit'].'</strong> of '.$material.' added to cart.</div>';
		}else{
 			echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error:<strong>'.mysqli_error($conn).'</strong></div>';
		}
		return;
	}
									
	if(mysqli_query($conn, "INSERT INTO cart (ingID,name,quantity,purity) VALUES ('$ingID','$material','$quantity','$purity')")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$material.'</strong> added to cart!</div>';
		return;
	}
}

if($_GET['action'] == 'removeFromCart' && $_GET['materialId']){
	$materialId = mysqli_real_escape_string($conn, $_GET['materialId']);

	if(mysqli_query($conn, "DELETE FROM cart WHERE id = '$materialId'")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Removed from cart!</div>';
		return;
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
		$q = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$name'"));
		$qIng = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '$name'");
		
		while($ing = mysqli_fetch_array($qIng)){
				$chName = mysqli_fetch_array(mysqli_query($conn, "SELECT chemical_name FROM ingredients WHERE name = '".$ing['ingredient']."' AND allergen = '1'"));
				if($chName['chemical_name']){
					$getAllergen['name'] = $chName['chemical_name'];
				}else{
					$getIngAlergen = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '".$ing['ingredient']."' AND allergen = '1'"));
					$qAll = mysqli_query($conn, "SELECT name FROM allergens WHERE ing = '".$ing['ingredient']."'");
					while($getAllergen = mysqli_fetch_array($qAll)){
						$allergen[] = $getAllergen['name'];
					}
				}
			$allergen[] = $getIngAlergen['name'];
			$allergen[] = $getAllergen['name'];
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
	//imagettftext($lbl, 30, 0, 250, 50, $black, $font, $text);
	imagettftext($lbl, 25, 0, 300, 50, $black, $font, 'INGREDIENTS');
	$lblF = imagerotate($lbl, 0 ,0);
	
	imagettftext($lblF, 15, 0, 0, 100, $black, $font, wordwrap ($allergenFinal, 90));
	imagettftext($lblF, 15, 0, 150, 490, $black, $font, wordwrap ($info, 50));

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

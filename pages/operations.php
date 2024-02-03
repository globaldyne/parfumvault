<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');


if($_GET['do'] == 'userPerfClear'){

	if(mysqli_query($conn, "DELETE FROM user_prefs WHERE owner = '".$_SESSION['userID']."'")){
		$result['success'] = "User prefernces removed";
	}else{
		$result['error'] = 'Something went wrong, '.mysqli_error($conn);
		
	}
	unset($_SESSION['user_prefs']);
	echo json_encode($result);
	return;
}


if($_GET['do'] == 'db_update'){

	$a_ver = trim(file_get_contents(__ROOT__.'/VERSION.md'));
	$n_ver = trim(file_get_contents(__ROOT__.'/db/schema.ver'));
	$c_ver = trim($pv_meta['schema_ver']);
	$script = __ROOT__.'/db/scripts/update_'.$c_ver.'-'.$n_ver.'.php';

	if(file_exists($script) == TRUE){
		require_once($script);
	}
  	if($c_ver == $n_ver){
		$result['error'] = "No update is needed.";
		echo json_encode($result);
		return;
    }

	foreach ( range(round($c_ver*100), round($n_ver*100),  0.1*100) as $i ) {
		$c_ver = mysqli_fetch_array(mysqli_query($conn, "SELECT schema_ver FROM pv_meta"));
		$u_ver = number_format($i/100,1);
		$sql = __ROOT__.'/db/updates/update_'.$c_ver['schema_ver'].'-'.$u_ver.'.sql';
	
		if(file_exists($sql) == TRUE){	
			$cmd = "mysql -u$dbuser -p$dbpass -h$dbhost $dbname < $sql";
			passthru($cmd,$e);
		}
		
		$q = mysqli_query($conn, "UPDATE pv_meta SET schema_ver = '$u_ver'");
	}

	if($q){
		$result['success'] = "<strong>Your database has been updated!</strong>";
		echo json_encode($result);
	}
	
	return;
}


if($_GET['do'] == 'backupDB'){
	if( getenv('DB_BACKUP_PARAMETERS') ){
		$bkparams = getenv('DB_BACKUP_PARAMETERS');
	}
	
	if($_GET['column_statistics'] === 'true'){
		$bkparams = '--column-statistics=1';
	}
	
	$file = 'backup_'.$ver.'_'.date("d-m-Y").'.sql.gz';
	
	header( 'Content-Type: application/x-gzip' );
	header( 'Content-Disposition: attachment; filename="' .$file. '"' );
	$cmd = "mysqldump $bkparams -u $dbuser --password=$dbpass -h $dbhost $dbname | gzip --best";
	passthru($cmd);
	
	return;
}

if($_GET['restore'] == 'db_bk'){
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0777, true);
	}
	
	$target_path = $tmp_path.basename($_FILES['backupFile']['name']); 

	if(move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    	$gz_tmp = basename($_FILES['backupFile']['name']);
		preg_match('/_(.*?)_/', $gz_tmp, $v);

		if($ver !== $v['1']){
			$result['error'] = "Backup file is taken from a different version ".$v['1'];
			echo json_encode($result);
			return;
		}
		
		system("gunzip -c $target_path > ".$tmp_path.'restore.sql');
		$cmd = "mysql -u$dbuser -p$dbpass -h$dbhost $dbname < ".$tmp_path.'restore.sql'; 
		passthru($cmd,$e);
		
		unlink($target_path);
		unlink($tmp_path.'restore.sql');
		
		if(!$e){
			$result['success'] = 'Database has been restored. Please refresh the page for the changes to take effect.';
			unset($_SESSION['parfumvault']);
			session_unset();
		}else{
			$result['error'] = "Something went wrong...";
		}
	} else {
		$result['error'] = "There was an error processing backup file $target_path, please try again!";
	}
	
	echo json_encode($result);
	return;
}

if($_GET['action'] == 'exportIFRA'){
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM IFRALibrary")))){
		$msg['error'] = 'No data found to export.';
		echo json_encode($msg);
		return;
	}
	$IFRA_Data = 0;
	$q = mysqli_query($conn, "SELECT * FROM IFRALibrary");
	while($ifra = mysqli_fetch_assoc($q)){
		
		$r['id'] = (int)$ifra['id'];
		$r['ifra_key'] = (string)$ifra['ifra_key']?: "-";
		$r['image'] = (string)$ifra['image']?: "-";
		$r['amendment'] = (string)$ifra['amendment']?: "-";
		$r['prev_pub'] = (string)$ifra['prev_pub']?: "-";
		$r['last_pub'] = (string)$ifra['last_pub']?: "-";
		$r['deadline_existing'] = (string)$ifra['deadline_existing']?: "-";
		$r['deadline_new'] = (string)$ifra['deadline_new']?: "-";
		$r['name'] = (string)$ifra['name']?: "-";
		$r['cas'] = (string)$ifra['cas']?: "-";
		$r['cas_comment'] = (string)$ifra['cas_comment']?: "-";
		$r['synonyms'] = (string)$ifra['synonyms']?: "-";
		$r['formula'] = (string)$ifra['formula']?: "-";//DEPRECATED IN IFRA 51
		$r['flavor_use'] = (string)$ifra['flavor_use']?: "-";
		$r['prohibited_notes'] = (string)$ifra['prohibited_notes'] ?: "-";
		$r['restricted_photo_notes'] = (string)$ifra['restricted_photo_notes']?: "-";
		$r['restricted_notes'] = (string)$ifra['restricted_notes']?: "-";
		$r['specified_notes'] = (string)$ifra['specified_notes']?: "-";
		$r['type'] = (string)$ifra['type']?: "-";
		$r['risk'] = (string)$ifra['risk']?: "-";
		$r['contrib_others'] = (string)$ifra['contrib_others']?: "-";
		$r['contrib_others_notes'] = (string)$ifra['contrib_others_notes']?: "-";
		$r['cat1'] = (double)$ifra['cat1']?: 100;
		$r['cat2'] = (double)$ifra['cat2']?: 100;
		$r['cat3'] = (double)$ifra['cat3']?: 100;
		$r['cat4'] = (double)$ifra['cat4']?: 100;
		$r['cat5A'] = (double)$ifra['cat5A']?: 100;
		$r['cat5B'] = (double)$ifra['cat5B']?: 100;
		$r['cat5C'] = (double)$ifra['cat5C']?: 100;
		$r['cat5D'] = (double)$ifra['cat5D']?: 100;
		$r['cat6'] = (double)$ifra['cat6']?: 100;
		$r['cat7A'] = (double)$ifra['cat7A']?: 100;
		$r['cat7B'] = (double)$ifra['cat7B']?: 100;
		$r['cat8'] = (double)$ifra['cat8']?: 100;
		$r['cat9'] = (double)$ifra['cat9']?: 100;
		$r['cat10A'] = (double)$ifra['cat10A']?: 100;
		$r['cat10B'] = (double)$ifra['cat10B']?: 100;
		$r['cat11A'] = (double)$ifra['cat11A']?: 100;
		$r['cat11B'] = (double)$ifra['cat11B']?: 100;
		$r['cat12'] = (double)$ifra['cat12']?: 100;

		$IFRA_Data++;
		$if[] = $r;
	}
	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	$vd['ifra_entries'] = $IFRA_Data;
	$vd['timestamp'] = date('d/m/Y H:i:s');

	
	$result['IFRALibrary'] = $if;
	$result['pvMeta'] = $vd;
	
	header('Content-disposition: attachment; filename=IFRALibrary.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;
}

if($_GET['action'] == 'exportFormulas'){
	if($_GET['fid']){
		$filter = " WHERE fid ='".$_GET['fid']."'";
	}
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData")))){
		$msg['error'] = 'No formulas found to export.';
		echo json_encode($msg);
		return;
	}
	$formulas = 0;
	$ingredients = 0;
	
	$qfmd = mysqli_query($conn, "SELECT * FROM formulasMetaData $filter");
	while($meta = mysqli_fetch_assoc($qfmd)){
		
		$r['id'] = (int)$meta['id'];
		$r['name'] = (string)$meta['name'];
		$r['product_name'] = (string)$meta['product_name'];
		$r['fid'] = (string)$meta['fid'];
		$r['profile'] = (string)$meta['profile'];
		$r['category'] = (string)$meta['profile'] ?: 'Default';
		$r['sex'] = (string)$meta['sex'];
		$r['notes'] = (string)$meta['notes'] ?: 'None';
		$r['created'] = (string)$meta['created'];
		$r['isProtected'] = (int)$meta['isProtected'] ?: 0;
		$r['defView'] = (int)$meta['defView'];
		$r['catClass'] = (string)$meta['catClass'];
		$r['revision'] = (int)$meta['revision'] ?: 0;
		$r['finalType'] = (int)$meta['finalType'] ?: 100;
		$r['isMade'] = (int)$meta['isMade'];
		$r['madeOn'] = (string)$meta['madeOn'] ?: "0000-00-00 00:00:00";
		$r['scheduledOn'] = (string)$meta['scheduledOn'];
		$r['customer_id'] = (int)$meta['customer_id'];
		$r['status'] = (int)$meta['status'];
		$r['toDo'] = (int)$meta['toDo'];
		$r['rating'] = (int)$meta['rating'] ?: 0;
		
		$formulas++;
		$fm[] = $r;
	}
	
	$qfm = mysqli_query($conn, "SELECT * FROM formulas $filter");
	while($formula = mysqli_fetch_assoc($qfm)){
		
		
		$f['id'] = (int)$formula['id'];
		$f['fid'] = (string)$formula['fid'];
		$f['name'] = (string)$formula['name'];
		$f['ingredient'] = (string)$formula['ingredient'];
		$f['ingredient_id'] = (int)$formula['ingredient_id'] ?: 0;
		$f['concentration'] = (float)$formula['concentration'] ?: 100;
		$f['dilutant'] = (string)$formula['dilutant'] ?: 'None';
		$f['quantity'] = (float)$formula['quantity'];
		$f['exclude_from_summary'] = (int)$formula['exclude_from_summary'];
		$f['exclude_from_calculation'] = (int)$formula['exclude_from_calculation'];
		$f['notes'] = (string)$formula['notes'] ?: 'None';
		$f['created'] = (string)$formula['created'];
		$f['updated'] = (string)$formula['updated'];
		
		$ingredients++;
		$fd[] = $f;
	}
	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	$vd['formulas'] = $formulas;
	$vd['ingredients'] = $ingredients;
	$vd['timestamp'] = date('d/m/Y H:i:s');

	
	$result['formulasMetaData'] = $fm;
	$result['formulas'] = $fd;
	$result['pvMeta'] = $vd;

	if(!$_GET['fid']){
		$f['name'] = "All_formulas";
	}
	
	header('Content-disposition: attachment; filename='.$f['name'].'.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;
}

if($_GET['action'] == 'restoreFormulas'){
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0777, true);
	}
	
	if (!is_writable($tmp_path)) {
		$result['error'] = "Upload directory not writable. Make sure you have write permissions.";
		echo json_encode($result);
		return;
	}
	
	$target_path = $tmp_path.basename($_FILES['backupFile']['name']); 

	if(move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    	$data = json_decode(file_get_contents($target_path), true);
		
		if(!$data['formulasMetaData']){
			$result['error'] = "JSON File seems invalid. Please make sure you importing the right file";
			echo json_encode($result);
			return;
		}
		
		foreach ($data['formulasMetaData'] as $meta ){				
			$name = mysqli_real_escape_string($conn, $meta['name']);
			$product_name = mysqli_real_escape_string($conn, $meta['product_name']);
			$notes = mysqli_real_escape_string($conn, $meta['notes']);
			
			$sql = "INSERT IGNORE INTO formulasMetaData(name,product_name,fid,profile,sex,notes,created,isProtected,defView,catClass,revision,finalType,isMade,madeOn,scheduledOn,customer_id,status,toDo,rating) VALUES('".$name."','".$product_name."','".$meta['fid']."','".$meta['profile']."','".$meta['sex']."','".$notes."','".$meta['created']."','".$meta['isProtected']."','".$meta['defView']."','".$meta['catClass']."','".$meta['revision']."','".$meta['finalType']."','".$meta['isMade']."','".$meta['madeOn']."','".$meta['scheduledOn']."','".$meta['customer_id']."','".$meta['status']."','".$meta['toDo']."','".$meta['rating']."')";
			
			if(mysqli_query($conn,$sql)){
				mysqli_query($conn,"DELETE FROM formulas WHERE fid = '".$meta['fid']."'");
			}else{
				$result['error'] = "There was an error importing your JSON file ".mysqli_error($conn);
				echo json_encode($result);
				return;
			}
		}
		
		foreach ($data['formulas'] as $formula ){	
	
			$name = mysqli_real_escape_string($conn, $formula['name']);
			$notes = mysqli_real_escape_string($conn, $formula['notes']);
			$ingredient = mysqli_real_escape_string($conn, $formula['ingredient']);
			$exclude_from_summary = $formula['exclude_from_summary'] ?: 0;
			$exclude_from_calculation = $formula['exclude_from_calculation'] ?: 0;
			$created = $formula['created'] ?:  date('Y-m-d H:i:s');
			$updated = $formula['updated'] ?:  date('Y-m-d H:i:s');

			$sql = "INSERT INTO formulas(fid,name,ingredient,ingredient_id,concentration,dilutant,quantity,exclude_from_summary,exclude_from_calculation,notes,created,updated) VALUES('".$formula['fid']."','".$name."','".$ingredient."','".$formula['ingredient_id']."','".$formula['concentration']."','".$formula['dilutant']."','".$formula['quantity']."','".$exclude_from_summary."','".$exclude_from_calculation."','".$notes."','".$created."','".$updated."')";
			
			if(mysqli_query($conn,$sql)){
				$result['success'] = "Import complete";
				unlink($target_path);
			}else{
				$result['error'] = "There was an error importing your JSON file ".mysqli_error($conn);
				
			}
		}
		
	} else {
		$result['error'] = "There was an error processing backup file $target_path, please try again!";
		echo json_encode($result);

	}
	echo json_encode($result);
	return;

}

if($_GET['action'] == 'restoreIngredients'){
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0777, true);
	}
	
	if (!is_writable($tmp_path)) {
		$result['error'] = "Upload directory not writable. Make sure you have write permissions.";
		echo json_encode($result);
		return;
	}
	
	$target_path = $tmp_path.basename($_FILES['backupFile']['name']); 

	if(move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    	$data = json_decode(file_get_contents($target_path), true);
		
		if(!$data['ingredients']){
			$result['error'] = "JSON File seems invalid. Please make sure you importing the right file";
			echo json_encode($result);
			return;
		}
		
		foreach ($data['compositions'] as $cmp ){				
			mysqli_query($conn, "INSERT IGNORE INTO `allergens` (`ing`,`name`,`cas`,`ec`,`percentage`,`toDeclare`,`created`) VALUES ('".$cmp['ing']."','".$cmp['name']."','".$cmp['cas']."','".$cmp['ec']."','".$cmp['percentage']."','".$cmp['toDeclare']."', current_timestamp())");			
		}
		
		foreach ($data['suppliers'] as $sup ){				
			
			mysqli_query($conn, "INSERT IGNORE INTO `suppliers` (`id`,`ingSupplierID`,`ingID`,`supplierLink`,`price`,`size`,`manufacturer`,`preferred`,`batch`,`purchased`,`mUnit`,`stock`,`status`,`supplier_sku`,`internal_sku`,`storage_location`,`created_at`) VALUES ('".$sup['id']."','".$sup['ingSupplierID']."','".$sup['ingID']."','".$sup['supplierLink']."','".$sup['price']."','".$sup['size']."','".$sup['manufacturer']."', '".$sup['preferred']."', '".$sup['batch']."','".$sup['purchased']."','".$sup['mUnit']."','".$sup['stock']."','".$sup['status']."','".$sup['supplier_sku']."','".$sup['internal_sku']."','".$sup['storage_location']."',current_timestamp())");
			
		}
		
		foreach ($data['ingSuppliers'] as $is ){				
			
			if(!mysqli_query($conn, "INSERT IGNORE INTO `ingSuppliers` (`id`,`name`,`address`,`po`,`country`,`telephone`,`url`,`email`) VALUES ('".$is['id']."','".$is['name']."','".$is['address']."','".$is['po']."','".$is['country']."','".$is['telephone']."','".$is['url']."','".$is['email']."')")){
				
				$result['error'] = mysqli_error($conn);
				echo json_encode($result);
				return;
			}				
		}
		
		foreach ($data['ingredients'] as $ingredient ){				
			$name = mysqli_real_escape_string($conn, $ingredient['name']);
			$INCI = mysqli_real_escape_string($conn, $ingredient['INCI']);
			$notes = mysqli_real_escape_string($conn, $ingredient['notes']);

			$sql = "INSERT IGNORE INTO ingredients(id,name,INCI,cas,FEMA,type,strength,category,purity,einecs,reach,tenacity,chemical_name,formula,flash_point,notes,flavor_use,soluble,logp,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12,profile,physical_state,allergen,odor,impact_top,impact_heart,impact_base,created,usage_type,noUsageLimit,byPassIFRA,isPrivate,molecularWeight) VALUES('".$ingredient['id']."','".$name."','".$INCI."','".$ingredient['cas']."','".$ingredient['FEMA']."','".$ingredient['type']."','".$ingredient['strength']."','".$ingredient['category']."','".$ingredient['purity']."','".$ingredient['einecs']."','".$ingredient['reach']."','".$ingredient['tenacity']."','".$ingredient['chemical_name']."','".$ingredient['formula']."','".$ingredient['flash_point']."','".$notes."','".$ingredient['flavor_use']."','".$ingredient['soluble']."','".$ingredient['logp']."','".$ingredient['cat1']."','".$ingredient['cat2']."','".$ingredient['cat3']."','".$ingredient['cat4']."','".$ingredient['cat5A']."','".$ingredient['cat5B']."','".$ingredient['cat5C']."','".$ingredient['cat6']."','".$ingredient['cat7A']."','".$ingredient['cat7B']."','".$ingredient['cat8']."','".$ingredient['cat9']."','".$ingredient['cat10A']."','".$ingredient['cat10B']."','".$ingredient['cat11A']."','".$ingredient['cat11B']."','".$ingredient['cat12']."','".$ingredient['profile']."','".$ingredient['physical_state']."','".$ingredient['allergen']."','".$ingredient['odor']."','".$ingredient['impact_top']."','".$ingredient['impact_heart']."','".$ingredient['impact_base']."','".$ingredient['created']."','".$ingredient['usage_type']."','".$ingredient['noUsageLimit']."','".$ingredient['byPassIFRA']."','".$ingredient['isPrivate']."','".$ingredient['molecularWeight']."')";
			
			if(mysqli_query($conn,$sql)){
				$result['success'] = "Import complete";
				unlink($target_path);
			}else{
				$result['error'] = "There was an error importing your JSON file ".mysqli_error($conn);
				echo json_encode($result);
				return;
			}
		}
		
		
		
	} else {
		$result['error'] = "There was an error processing json file $target_path, please try again!";
		echo json_encode($result);

	}
	echo json_encode($result);
	return;

}


if($_GET['action'] == 'restoreIFRA'){
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0777, true);
	}
	
	if (!is_writable($tmp_path)) {
		$result['error'] = "Upload directory not writable. Make sure you have write permissions.";
		echo json_encode($result);
		return;
	}
	
	$target_path = $tmp_path.basename($_FILES['backupFile']['name']); 

	if(move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    	$data = json_decode(file_get_contents($target_path), true);
		if(!$data['IFRALibrary']){
			$result['error'] = "JSON File seems invalid. Please make sure you importing the right file";
			echo json_encode($result);
			return;
		}
		mysqli_query($conn, "TRUNCATE IFRALibrary");
		
		foreach ($data['IFRALibrary'] as $d ){				
			
			$s = mysqli_query($conn, "INSERT INTO `IFRALibrary` (`ifra_key`,`image`,`amendment`,`prev_pub`,`last_pub`,`deadline_existing`,`deadline_new`,`name`,`cas`,`cas_comment`,`synonyms`,`formula`,`flavor_use`,`prohibited_notes`,`restricted_photo_notes`,`restricted_notes`,`specified_notes`,`type`,`risk`,`contrib_others`,`contrib_others_notes`,`cat1`,`cat2`,`cat3`,`cat4`,`cat5A`,`cat5B`,`cat5C`,`cat5D`,`cat6`,`cat7A`,`cat7B`,`cat8`,`cat9`,`cat10A`,`cat10B`,`cat11A`,`cat11B`,`cat12`) VALUES ('".$d['ifra_key']."','".$d['image']."','".$d['amendment']."','".$d['prev_pub']."','".$d['last_pub']."','".$d['deadline_existing']."','".$d['deadline_new']."','".$d['name']."','".$d['cas']."','".$d['cas_comment']."','".$d['synonyms']."','".$d['formula']."','".$d['flavor_use']."','".$d['prohibited_notes']."','".$d['restricted_photo_notes']."','".$d['restricted_notes']."','".$d['specified_notes']."','".$d['type']."','".$d['risk']."','".$d['contrib_others']."','".$d['contrib_others_notes']."','".$d['cat1']."','".$d['cat2']."','".$d['cat3']."','".$d['cat4']."','".$d['cat5A']."','".$d['cat5B']."','".$d['cat5C']."','".$d['cat5D']."','".$d['cat6']."','".$d['cat7A']."','".$d['cat7B']."','".$d['cat8']."','".$d['cat9']."','".$d['cat10A']."','".$d['cat10B']."','".$d['cat11A']."','".$d['cat11B']."','".$d['cat12']."') ");
				
		}
				
		if($s){
			$result['success'] = "Import complete";
			unlink($target_path);
		}else{
			$result['error'] = "There was an error importing your JSON file ".mysqli_error($conn);
			echo json_encode($result);
			return;
		}
			
	} else {
		$result['error'] = "There was an error processing json file $target_path, please try again!";
		echo json_encode($result);

	}
	echo json_encode($result);
	return;

}


?>

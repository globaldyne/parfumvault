<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');

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

	foreach ( array_map(fn($n) => $n/100, range($c_ver*100, $n_ver*100,  0.1*100)) as $i) {
		$c_ver = mysqli_fetch_array(mysqli_query($conn, "SELECT schema_ver FROM pv_meta"));
		$u_ver = number_format($i,1);
		$sql = __ROOT__.'/db/updates/update_'.$c_ver['schema_ver'].'-'.$u_ver.'.sql';
	
		if(file_exists($sql) == TRUE){	
			$cmd = "mysql -u$dbuser -p$dbpass $dbname < $sql";
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
	
	$file = 'backup_'.$ver.'_'.date("d-m-Y").'.sql.gz';
	$mime = "application/x-gzip";
	
	header( 'Content-Type: '.$mime );
	header( 'Content-Disposition: attachment; filename="' .$file. '"' );
	
	$cmd = "mysqldump -u $dbuser --password=$dbpass -h $dbhost $dbname | gzip --best";
	passthru($cmd);
	
	return;
}

if($_GET['do'] == 'backupFILES'){
	
	$file = 'backup-'.date("d-m-Y").'.files.gz';
	$mime = "application/x-gzip";
	
	if (!file_exists(__ROOT__."/tmp")) {
		mkdir(__ROOT__."/tmp", 0777, true);
	}

	$cmd = "tar -czvf ".__ROOT__."/tmp/$file ".__ROOT__."/$uploads_path";   
	shell_exec($cmd);
	
	header( 'Content-Type: '.$mime );
	header( 'Location:/tmp/' .$file );

	return;	
}

if($_GET['restore'] == 'db_bk'){
	if (!file_exists(__ROOT__."/$tmp_path")) {
		mkdir(__ROOT__."/$tmp_path", 0777, true);
	}
	
	$target_path = __ROOT__.'/'.$tmp_path.basename($_FILES['backupFile']['name']); 

	if(move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    	$gz_tmp = basename($_FILES['backupFile']['name']);
		preg_match('/_(.*?)_/', $gz_tmp, $v);

		if($ver !== $v['1']){
			$result['error'] = "Backup file is taken from a different version ".$v['1'];
			echo json_encode($result);
			return;
		}
		
		system("gunzip -c $target_path > ".__ROOT__.'/'.$tmp_path.'restore.sql');
		$cmd = "mysql -u$dbuser -p$dbpass $dbname < ".__ROOT__.'/'.$tmp_path.'restore.sql'; 
		passthru($cmd,$e);
		unlink($target_path);
		unlink(__ROOT__.'/'.$tmp_path.'restore.sql');
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
		$r['sex'] = (string)$meta['sex'];
		$r['notes'] = (string)$meta['notes'];
		$r['created'] = (string)$meta['created'];
		$r['isProtected'] = (int)$meta['isProtected'];
		$r['defView'] = (int)$meta['defView'];
		$r['catClass'] = (string)$meta['catClass'];
		$r['revision'] = (int)$meta['revision'];
		$r['finalType'] = (int)$meta['finalType'];
		$r['isMade'] = (int)$meta['isMade'];
		$r['madeOn'] = (string)$meta['madeOn'] ?: "0000-00-00 00:00:00";
		$r['scheduledOn'] = (string)$meta['scheduledOn'];
		$r['customer_id'] = (int)$meta['customer_id'];
		$r['status'] = (int)$meta['status'];
		$r['toDo'] = (int)$meta['toDo'];
		$r['rating'] = (int)$meta['rating'];
		
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
		$f['concentration'] = (float)$formula['concentration'];
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

	if($_GET['fid']){
		header('Content-disposition: attachment; filename='.$f['name'].'.json');
		header('Content-type: application/json');
		echo json_encode($result, JSON_PRETTY_PRINT);
		return;
	}
	
	$file = __ROOT__.'/tmp/pv_formulas.json';
	unlink($file);
	
	$fp = fopen($file, 'w');
	fwrite($fp, json_encode($result, JSON_PRETTY_PRINT));
	fclose($fp);

	if(file_exists($file)){
		$msg['success'] ='<a href="/tmp/pv_formulas.json" target="_blank">JSON File is ready, right click to save it to your computer.</a>';
	}else{
		$msg['error'] = 'Error generating JSON file';
	}
	
	echo json_encode($msg);
	return;
}

if($_GET['action'] == 'restoreFormulas'){
	if (!file_exists(__ROOT__."/$tmp_path")) {
		mkdir(__ROOT__."/$tmp_path", 0777, true);
	}
	
	if (!is_writable(__ROOT__."/$tmp_path")) {
		$result['error'] = "Upload directory not writable. Make sure you have write permissions.";
		echo json_encode($result);
		return;
	}
	
	$target_path = __ROOT__.'/'.$tmp_path.basename($_FILES['backupFile']['name']); 

	if(move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    	$data = json_decode(file_get_contents($target_path), true);
		
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
		
			$sql = "INSERT INTO formulas(fid,name,ingredient,ingredient_id,concentration,dilutant,quantity,exclude_from_summary,exclude_from_calculation,notes,created,updated) VALUES('".$formula['fid']."','".$name."','".$ingredient."','".$formula['ingredient_id']."','".$formula['concentration']."','".$formula['dilutant']."','".$formula['quantity']."','".$formula['exclude_from_summary']."','".$formula['exclude_from_calculation']."','".$notes."','".$formula['created']."','".$formula['updated']."')";
			
			if(mysqli_query($conn,$sql)){
				$result['success'] = "Import complete";
				unlink(__ROOT__.'/'.$target_path);
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
	if (!file_exists(__ROOT__."/$tmp_path")) {
		mkdir(__ROOT__."/$tmp_path", 0777, true);
	}
	
	if (!is_writable(__ROOT__."/$tmp_path")) {
		$result['error'] = "Upload directory not writable. Make sure you have write permissions.";
		echo json_encode($result);
		return;
	}
	
	$target_path = __ROOT__.'/'.$tmp_path.basename($_FILES['backupFile']['name']); 

	if(move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    	$data = json_decode(file_get_contents($target_path), true);
		
		foreach ($data['compositions'] as $cmp ){				
			
		 mysqli_query($conn, "INSERT IGNORE INTO `allergens` (`ing`,`name`,`cas`,`ec`,`percentage`,`toDeclare`,`created`) VALUES ('".$cmp['ing']."','".$cmp['name']."','".$cmp['cas']."','".$cmp['ec']."','".$cmp['percentage']."','".$cmp['toDeclare']."', current_timestamp())");
			
		}
		
		foreach ($data['ingredients'] as $ingredient ){				
			$name = mysqli_real_escape_string($conn, $ingredient['name']);
			$INCI = mysqli_real_escape_string($conn, $ingredient['INCI']);
			$notes = mysqli_real_escape_string($conn, $ingredient['notes']);

			$sql = "INSERT IGNORE INTO ingredients(name,INCI,cas,FEMA,type,strength,category,purity,einecs,reach,tenacity,chemical_name,formula,flash_point,notes,flavor_use,soluble,logp,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12,profile,physical_state,allergen,odor,impact_top,impact_heart,impact_base,created,usage_type,noUsageLimit,byPassIFRA,isPrivate,molecularWeight) VALUES('".$name."','".$INCI."','".$ingredient['cas']."','".$ingredient['FEMA']."','".$ingredient['type']."','".$ingredient['strength']."','".$ingredient['category']."','".$ingredient['purity']."','".$ingredient['einecs']."','".$ingredient['reach']."','".$ingredient['tenacity']."','".$ingredient['chemical_name']."','".$ingredient['formula']."','".$ingredient['flash_point']."','".$notes."','".$ingredient['flavor_use']."','".$ingredient['soluble']."','".$ingredient['logp']."','".$ingredient['cat1']."','".$ingredient['cat2']."','".$ingredient['cat3']."','".$ingredient['cat4']."','".$ingredient['cat5A']."','".$ingredient['cat5B']."','".$ingredient['cat5C']."','".$ingredient['cat6']."','".$ingredient['cat7A']."','".$ingredient['cat7B']."','".$ingredient['cat8']."','".$ingredient['cat9']."','".$ingredient['cat10A']."','".$ingredient['cat10B']."','".$ingredient['cat11A']."','".$ingredient['cat11B']."','".$ingredient['cat12']."','".$ingredient['profile']."','".$ingredient['physical_state']."','".$ingredient['allergen']."','".$ingredient['odor']."','".$ingredient['impact_top']."','".$ingredient['impact_heart']."','".$ingredient['impact_base']."','".$ingredient['created']."','".$ingredient['usage_type']."','".$ingredient['noUsageLimit']."','".$ingredient['byPassIFRA']."','".$ingredient['isPrivate']."','".$ingredient['molecularWeight']."')";
			
			if(mysqli_query($conn,$sql)){
				$result['success'] = "Import complete";
				unlink(__ROOT__.'/'.$target_path);
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
?>
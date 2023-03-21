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
	
	$cmd = "mysqldump -u $dbuser --password=$dbpass $dbname | gzip --best";
	passthru($cmd);
	
	return;
}

if($_GET['do'] == 'backupFILES'){
	
	$file = 'backup-'.date("d-m-Y").'.files.gz';
	$mime = "application/x-gzip";
	
	if (!file_exists(__ROOT__."/$tmp_path")) {
		mkdir(__ROOT__."/$tmp_path", 0777, true);
	}

	$cmd = "tar -czvf ".__ROOT__."/$tmp_path$file ".__ROOT__."/$uploads_path";   
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
	
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData")))){
		$msg['error'] = 'No formulas found to export.';
		echo json_encode($msg);
		return;
	}
	
	$qfmd = mysqli_query($conn, "SELECT * FROM formulasMetaData");
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
		$r['schedulledOn'] = (string)$meta['schedulledOn'];
		$r['customer_id'] = (int)$meta['customer_id'];
		$r['status'] = (int)$meta['status'];
		$r['toDo'] = (int)$meta['toDo'];
		$r['rating'] = (int)$meta['rating'];

		$fm[] = $r;
	}
	
	$qfm = mysqli_query($conn, "SELECT * FROM formulas");
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

		$fd[] = $f;
	}
	
	$result['formulasMetaData'] = $fm;
	$result['formulas'] = $fd;


	header('Content-disposition: attachment; filename=pv_formulas.json');
	header('Content-type: application/json');
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
	
	$target_path = __ROOT__.'/'.$tmp_path.basename($_FILES['backupFile']['name']); 

	if(move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    	$data = json_decode(file_get_contents($target_path), true);
		
		foreach ($data['formulasMetaData'] as $meta ){				
			$name = mysqli_real_escape_string($conn, $meta['name']);
			$product_name = mysqli_real_escape_string($conn, $meta['product_name']);
			$notes = mysqli_real_escape_string($conn, $meta['notes']);
			
			$sql = "INSERT IGNORE INTO formulasMetaData(name,product_name,fid,profile,sex,notes,created,isProtected,defView,catClass,revision,finalType,isMade,madeOn,schedulledOn,customer_id,status,toDo,rating) VALUES('".$name."','".$product_name."','".$meta['fid']."','".$meta['profile']."','".$meta['sex']."','".$notes."','".$meta['created']."','".$meta['isProtected']."','".$meta['defView']."','".$meta['catClass']."','".$meta['revision']."','".$meta['finalType']."','".$meta['isMade']."','".$meta['madeOn']."','".$meta['schedulledOn']."','".$meta['customer_id']."','".$meta['status']."','".$meta['toDo']."','".$meta['rating']."')";
			
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
?>
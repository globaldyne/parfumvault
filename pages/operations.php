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
	$c_ver = $pv_meta['schema_ver'];
	$script = __ROOT__.'/db/scripts/update_'.$c_ver.'-'.$n_ver.'.php';

	if(file_exists($script) == TRUE){
		require_once($script);
	}
  	if($pv_meta['schema_ver'] == $n_ver){
		echo '<div class="alert alert-info alert-dismissible"><strong>No update is needed.</strong></div>';
		return;
    }

	foreach (range($c_ver['schema_ver']+0.1, $n_ver,  0.1) as $i) {
		$c_ver = mysqli_fetch_array(mysqli_query($conn, "SELECT schema_ver FROM pv_meta"));
		$u_ver = number_format($i,1);
		$sql = __ROOT__.'/db/updates/update_'.$c_ver['schema_ver'].'-'.$u_ver.'.sql';
	
		if(file_exists($sql) == TRUE){	
			$cmd = "mysql -u$dbuser -p$dbpass $dbname < $sql";
			passthru($cmd,$e);
		}
		
		$q = mysqli_query($conn, "UPDATE pv_meta SET schema_ver = '$u_ver'");
	}
	/*	
	if($e){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Failed to update the database,</strong> corrupted or wrong update file.</div>';
		//return; 
		//Notify the user but continue
	}
*/
	if($q){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Your database has been updated!</strong></div>';
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


?>
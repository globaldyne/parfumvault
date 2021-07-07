<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');

if($_GET['do'] == 'db_update'){

	$a_ver = trim(file_get_contents(__ROOT__.'/VERSION.md'));
	$n_ver = trim(file_get_contents(__ROOT__.'/db/schema.ver'));
	$c_ver = $pv_meta['schema_ver'];
	$sql = __ROOT__.'/db/updates/update_'.$c_ver.'-'.$n_ver.'.sql';
		
	if(file_exists($sql) == FALSE){
		echo '<div class="alert alert-danger"><strong>Missing update file!</strong> Please make sure file '.$sql.' exists and in the right path.</div>';
		return;
	}
	
	$cmd = "mysql -u$dbuser -p$dbpass $dbname < $sql"; 
	//echo $cmd;
	passthru($cmd,$e);

	if($e){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Failed to update the database,</strong>corrupted or wrong update file.</div>';
		return;
	}
	
	$q = mysqli_query($conn, "UPDATE pv_meta SET schema_ver = '$n_ver', app_ver = '$a_ver'");
	
	if($q){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Your database has been updated!</strong></div>';
	}
	return;
}


if($_GET['do'] == 'backupDB'){
	
	$file = 'backup-'.date("d-m-Y").'.sql.gz';
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
	
	$cmd = "tar -czvf ".__ROOT__."/$tmp_path$file ".__ROOT__."/$uploads_path";   
	shell_exec($cmd);
	
	header( 'Content-Type: '.$mime );
	header( 'Location:/tmp/' .$file );

	return;	
}

?>
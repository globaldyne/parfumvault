<?php
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');
require_once('../inc/product.php');

if($_GET['do'] == 'db_update'){

	$a_ver = trim(file_get_contents(__ROOT__.'/VERSION.md'));
	$n_ver = trim(file_get_contents('../db/schema.ver'));
	$c_ver = $pv_meta['schema_ver'];
	$sql = __ROOT__.'/db/updates/update_'.$c_ver.'-'.$n_ver.'.sql';
		
	if(file_exists($sql) == FALSE){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Missing update file!</strong>Please make sure file '.$sql.' exists and in the right path.</div>';
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
}

?>
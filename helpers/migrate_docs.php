<?php
/*
Migrate old SDS files to the new format introduced to PV 3.1
This needs to be run only if you are upgrading from versions up to 3.0 to a newer one.

WARNING: Please take a full back up of your database before you run this script.
*/


define('pvault_panel', TRUE);
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

$r = mysqli_query($conn, "SHOW COLUMNS FROM ingredients LIKE 'SDS'");
$chk = (mysqli_num_rows($r))?TRUE:FALSE;
if($chk) {
   
	$q = mysqli_query($conn, "SELECT id,name,SDS FROM ingredients WHERE SDS<>''");

	while($r = mysqli_fetch_array($q)){
		$docData = 'data:application/pdf;base64,' . base64_encode(file_get_contents(__ROOT__.'/'.$r['SDS']));
		
		if(mysqli_query($conn,"INSERT INTO documents (ownerID,type,name,notes,docData) VALUES ('".$r['id']."','1','MSDS','Migrated from old DB','$docData')")){
			mysqli_query($conn,"ALTER TABLE ingredients DROP SDS");
			echo 'Migrating '.$r['name']."\n";
			
		}else{
			echo 'Document migration failed...';
		}
	
	}
}else{
	echo 'Scripts seems to have been already executed.';
}
?>
<?php
/*
Migrate old SDS files to the new format introduced to PV 3.1
This needs to be run only if you are upgrading from versions up to 3.0 to a newer one.

WARNING: Please take a full back up of your database before you run this script.
*/


define('pvault_panel', TRUE);
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/opendb.php');

$chk_ing = (mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM ingredients LIKE 'SDS'")))?TRUE:FALSE;
$chk_fmd = (mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM formulasMetaData LIKE 'image'")))?TRUE:FALSE;

if($chk_ing) {
	$q = mysqli_query($conn, "SELECT id,name,SDS FROM ingredients WHERE SDS<>''");

	while($r = mysqli_fetch_array($q)){
		$docData = 'data:application/pdf;base64,' . base64_encode(file_get_contents(__ROOT__.'/'.$r['SDS']));
		
		if(mysqli_query($conn,"INSERT INTO documents (ownerID,type,name,notes,docData) VALUES ('".$r['id']."','1','MSDS','Migrated from old DB','$docData')")){
			mysqli_query($conn,"ALTER TABLE ingredients DROP SDS");
			unlink(__ROOT__.'/'.$r['SDS']);			
			echo 'Migrating '.$r['name']."\n";
			
		}else{
			echo "Document migration failed... \n";
		}
	}
	rmdir(__ROOT__.'/uploads/SDS');
}else{
	echo "Ingredients docs seems to have been already migrated. \n";
}

if($chk_fmd) {
	$q = mysqli_query($conn, "SELECT id,name,image FROM formulasMetaData WHERE image<>''");

	while($r = mysqli_fetch_array($q)){
		$docData = 'data:application/png;base64,' . base64_encode(file_get_contents(__ROOT__.'/'.$r['image']));
		
		if(mysqli_query($conn,"INSERT INTO documents (ownerID,type,name,notes,docData) VALUES ('".$r['id']."','2','".$r['name']."','Migrated from old DB','$docData')")){
			mysqli_query($conn,"ALTER TABLE formulasMetaData DROP image");
			unlink(__ROOT__.'/'.$r['image']);			
			echo 'Migrating '.$r['name']."\n";
			
		}else{
			echo "Document migration failed... \n";
		}
		
	}
	rmdir(__ROOT__.'/uploads/formulas');
}else{
	echo "Formulas images seems to have been already migrated. \n";
}


?>

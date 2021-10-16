<?php
/*
Migrate old Ingredients format to the new one introduced to PV 3.4
This needs to be run only if you are upgrading from versions up to 3.3 to a newer one.

WARNING: Please take a full back up of your database before you run this script.
*/

error_reporting(0);
define('pvault_panel', TRUE);
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');


$get_cat = mysqli_query($conn, "SELECT id, category FROM ingredients WHERE category REGEXP '[a-zA-Z]|^$' OR category IS NULL");

if(mysqli_num_rows($get_cat) == FALSE){
	echo 'No update needed';
	return;
}

while($cat = mysqli_fetch_array($get_cat)){
	$get_cat_id = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM ingCategory WHERE name = '".$cat['category']."'"));
	if($get_cat_id['id']){
		mysqli_query($conn, "UPDATE ingredients SET category = '".$get_cat_id['id']."' WHERE id = '".$cat['id']."'");
	}else{
        mysqli_query($conn, "UPDATE ingredients SET category = '1' WHERE id = '".$cat['id']."'");
	}
}


?>

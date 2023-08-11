<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

if( $_GET['set'] && $_GET['action'] == 'save'){	
	$a = $_GET['set'];
	$_SESSION["$a"] = $_POST;
	return;
}

if($a = $_GET['set'] && $_GET['action'] == 'load'){
	$a = $_GET['set'];
	echo json_encode($_SESSION["$a"]) ;
	return;
}

?>

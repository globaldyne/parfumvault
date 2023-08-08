<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

if($_GET['set'] = 'listFormulas' && $_GET['action'] == 'save'){
	
	$_SESSION["listFormulas"] = $_POST;
	return;
}
if($_GET['set'] = 'listFormulas' && $_GET['action'] == 'load'){

	echo json_encode($_SESSION["listFormulas"]) ;
	return;
}

?>
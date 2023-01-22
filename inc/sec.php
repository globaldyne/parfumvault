<?php
define('pvault_panel', TRUE);

if(defined('__ROOT__') == FALSE){
	define('__ROOT__', dirname(dirname(__FILE__))); 
}

session_start();
if(!isset($_SESSION['parfumvault'])){
//	$login = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'login.php';
	$login = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/login.php';
	header('Location: '.$login);
	exit;
}

?>

<?php
define('pvault_panel', TRUE);

if(defined('__ROOT__') == FALSE){
	define('__ROOT__', dirname(dirname(__FILE__))); 
}

if ( (! empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
     (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
     (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ) {
    $server_request_scheme = 'https';
} else {
    $server_request_scheme = 'http';
}

session_start();
if(!isset($_SESSION['parfumvault'])){
	if($_GET['do']){
		$redirect = '?do='.$_GET['do'];
	}
	$login = $server_request_scheme.'://'.$_SERVER['HTTP_HOST'].'/login.php'.$redirect;
	header('Location: '.$login);
	exit;
}

?>

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

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(strtoupper(getenv('PLATFORM')) === "CLOUD"){
	$session_timeout = getenv('SYS_TIMEOUT') ?: 1800;
} else {
	require_once(__ROOT__.'/inc/config.php');
}

if (isset($_SESSION['parfumvault_time'])) {
    if ((time() - $_SESSION['parfumvault_time']) > $session_timeout) {
        session_unset();
        session_destroy();
        $response['auth']['error'] = true;
		$response['auth']['msg'] = 'You have been automatically logged out due to inactivity of '.$session_timeout.' seconds. Please log in again. ';
		echo json_encode($response);
        return;
    } else {
        $_SESSION['parfumvault_time'] = time();
    }
} else {
    $_SESSION['parfumvault_time'] = time();
}

if(!isset($_SESSION['parfumvault'])){
	if($_GET['do']){
		$redirect = '?do='.$_GET['do'];
	}
	$login = $server_request_scheme.'://'.$_SERVER['HTTP_HOST'].'/login.php'.$redirect;
	header('Location: '.$login);
	exit;
}

?>

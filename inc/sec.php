<?php
if (!defined('pvault_panel')) {
    define('pvault_panel', TRUE);
}

if(defined('__ROOT__') == FALSE){
	define('__ROOT__', dirname(dirname(__FILE__))); 
}

if (!file_exists(__ROOT__ . '/inc/config.php') && 
    !getenv('DB_HOST') && 
    !getenv('DB_USER') && 
    !getenv('DB_PASS') && 
    !getenv('DB_NAME')) {
        $error_msg = 'Required parameters not found. Please make sure your provided all the required variables as per <a href="https://www.perfumersvault.com/knowledge-base/howto-docker/" target="_blank">documentation</a>';
        require_once(__ROOT__.'/pages/error.php');
        error_log("Configuration file not found.");
        exit;
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
        $response = [
            'auth' => [
            'error' => true,
            'msg' => 'You have been automatically logged out due to inactivity of ' . $session_timeout . ' seconds. Please log in again.'
            ]
        ];
        session_start();
        $_SESSION['temp_response'] = ['error' => 'You have been automatically logged out due to inactivity. Please log in again.'];
        echo json_encode($response);
        header('Location: '.$server_request_scheme.'://'.$_SERVER['HTTP_HOST'].'/login.php');
        return;
    } else {
        $_SESSION['parfumvault_time'] = time();
    }
} else {
    $_SESSION['parfumvault_time'] = time();
}

if(!isset($_SESSION['parfumvault'])){
	if(isset($_GET['do'])){
		$redirect = '?do='.$_GET['do'];
	}
	$login = $server_request_scheme.'://'.$_SERVER['HTTP_HOST'].'/login.php'.$redirect;
	header('Location: '.$login);
	exit;
}

?>

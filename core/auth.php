<?php
define('__ROOT__', dirname(dirname(__FILE__)));
define('pvault_panel', TRUE);

require_once(__ROOT__.'/inc/opendb.php');

if(strtoupper(getenv('PLATFORM')) === "CLOUD"){
	$session_timeout = getenv('SYS_TIMEOUT') ?: 1800;
} else {
	require_once(__ROOT__.'/inc/config.php');
}

if($_POST['action'] == 'login'){
	
	if(empty($_POST['email']) || empty($_POST['password'])){
		$response['auth']['error'] = true;
		$response['auth']['msg'] = 'Email and password fields cannot be empty';
		echo json_encode($response);
		return;
	}

	$email = mysqli_real_escape_string($conn,strtolower($_POST['email']));
	$password = mysqli_real_escape_string($conn,$_POST['password']);
	
	$row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM users WHERE email='".$email."' AND password=PASSWORD('".$password."')"));

	if($row['id']){
		if (session_status() === PHP_SESSION_NONE) {
			 session_set_cookie_params([
				'lifetime' => $session_timeout, // Set cookie lifetime to 30 minutes
				'path' => '/', // Make the cookie accessible throughout the domain
				'secure' => isset($_SERVER['HTTPS']), // Secure cookie if using HTTPS
				'httponly' => true, // Prevent JavaScript from accessing the cookie
				'samesite' => 'Strict', // Protect against CSRF attacks
			]);
    		session_start();
		}
		
		if (isset($_SESSION['parfumvault_time'])) {
			if ((time() - $_SESSION['parfumvault_time']) > $session_timeout) {
				session_unset();
				session_destroy();
				
				$response['auth']['error'] = true;
				$response['auth']['msg'] = 'Session expired. Please log in again.';
				echo json_encode($response);
				return;
			} else {
				$_SESSION['parfumvault_time'] = time();
			}
		} else {
			$_SESSION['parfumvault_time'] = time();
		}
		
		$_SESSION['parfumvault'] = true;
		$_SESSION['userID'] = $row['id'];
		if($_POST['do']){
			$redirect = '/index.php?do='.$_POST['do'];
		}elseif($_POST['url']){
			$redirect = $_POST['url'];
		}else{
			$redirect = '/index.php';
		}
		$response['auth']['success'] = true;
		$response['auth']['redirect'] = $redirect;
		echo json_encode($response);
		return;
	}else{
		$response['auth']['error'] = true;
		$response['auth']['msg'] = 'Email or password error';
		echo json_encode($response);
		return;
	}
	


	
}
	
?>

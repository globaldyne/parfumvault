<?php
define('__ROOT__', dirname(dirname(__FILE__)));
define('pvault_panel', TRUE);

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

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
		session_start();
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

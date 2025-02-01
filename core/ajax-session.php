<?php

define('pvault_panel', TRUE);
define('__ROOT__', dirname(dirname(__FILE__))); 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if(getenv('PLATFORM') === "CLOUD"){
	$session_timeout = getenv('SYS_TIMEOUT') ?: 1800;
} else {
	require_once(__ROOT__.'/inc/config.php');
}

if ((time() - $_SESSION['parfumvault_time']) > $session_timeout) {
    session_unset();
    session_destroy();
        
    echo json_encode( 
		array(
			'session_status' => false,
			'session_timeout' => $session_timeout,
			'session_time' => $_SESSION['parfumvault_time'] ?? null
		)
	);
    return;
}

if(!isset( $_SESSION['parfumvault']) || $_SESSION['parfumvault'] === false) {
    //session is expired
	echo json_encode( 
		array(
			'session_status' => false,
			'session_timeout' => $session_timeout,
			'session_time' => $_SESSION['parfumvault_time'] ?? null
		)
	);
    session_destroy();
} else {
    //session is valid
	require_once(__ROOT__.'/inc/sec.php');
	require_once(__ROOT__.'/inc/opendb.php');

	$userInfo = mysqli_fetch_array(mysqli_query($conn, "SELECT isActive FROM users WHERE id = '".$_SESSION['userID'] ."'"));
	if (!$userInfo || empty($userInfo['isActive'])) {
		
		echo json_encode( 
			array(
				'session_status' => false,
				'session_timeout' => $session_timeout,
				'session_time' => $_SESSION['parfumvault_time'] ?? null
			)
		);
		session_unset();
		session_destroy();
		//return;
	}
	echo json_encode( 
		array(
			'session_status' => true,
			'session_timeout' => $session_timeout,
			'session_time' => $_SESSION['parfumvault_time']
		)
	);
	return;
}


?>

<?php

define('pvault_panel', TRUE);
define('__ROOT__', dirname(dirname(__FILE__))); 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (getenv('PLATFORM') === "CLOUD") {
    $session_timeout = getenv('SYS_TIMEOUT') ?: 1800;
    $dbhost = getenv('DB_HOST');
	$dbuser = getenv('DB_USER');
	$dbpass = getenv('DB_PASS');
	$dbname = getenv('DB_NAME');
} else {
    require_once(__ROOT__.'/inc/config.php');
}

$current_time = time();
$session_start_time = $_SESSION['parfumvault_time'] ?? $current_time;
$time_left = max(0, ($session_start_time + $session_timeout - $current_time) / 60); // Convert to minutes

if (($current_time - $session_start_time) > $session_timeout) {
    session_unset();
    session_destroy();
        
    echo json_encode([
        'session_status' => false,
        'session_timeout' => $session_timeout,
        'session_time' => $session_start_time,
        'time_left' => 0
    ]);
    return;
}

if (!isset($_SESSION['parfumvault']) || $_SESSION['parfumvault'] === false) {
    echo json_encode([
        'session_status' => false,
        'session_timeout' => $session_timeout,
        'session_time' => $session_start_time,
        'time_left' => 0
    ]);
    session_destroy();
} else {
	
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
    $userInfo = mysqli_fetch_array(mysqli_query($conn, "SELECT id, email, isActive FROM users WHERE id = '".$_SESSION['userID']."'"));
    
	//error_log("userInfo: ".json_encode($userInfo).", time_left: ".round($time_left, 2));

	if (!$userInfo || empty($userInfo['isActive'])) {
        echo json_encode([
            'session_status' => false,
            'session_timeout' => $session_timeout,
            'session_time' => $session_start_time,
            'time_left' => 0
        ]);
        session_unset();
        session_destroy();
        return;
    }

    echo json_encode([
        'session_status' => true,
        'session_timeout' => $session_timeout,
        'session_time' => $session_start_time,
        'time_left' => round($time_left, 2) // Keeping precision up to 2 decimal places
    ]);
}

?>

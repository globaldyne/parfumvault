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

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (($current_time - $session_start_time) > $session_timeout) {
    $userID = $_SESSION['userID'];
    mysqli_query($conn, "DELETE FROM session_info WHERE owner_id = '$userID'");
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
    if (isset($_SESSION['userID'])) {
        $userID = $_SESSION['userID'];
    }
    try {
        if (!mysqli_query($conn, "DELETE FROM session_info WHERE owner_id = '$userID'")) {
            throw new Exception(mysqli_error($conn));
        }
    } catch (Exception $e) {
        error_log("Failed to delete session info: " . $e->getMessage());
    }
    echo json_encode([
        'session_status' => false,
        'session_timeout' => $session_timeout,
        'session_time' => $session_start_time,
        'time_left' => 0
    ]);
    session_destroy();
} else {
    $userInfo = mysqli_fetch_array(mysqli_query($conn, "SELECT id, email, isActive FROM users WHERE id = '".$_SESSION['userID']."'"));
    
    if (!$userInfo || empty($userInfo['isActive'])) {
        $userID = $_SESSION['userID'];
        mysqli_query($conn, "DELETE FROM session_info WHERE owner_id = '$userID'");
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

    $userID = $_SESSION['userID'];
    $remaining_time = round($time_left, 2);
    try {
        if (!mysqli_query($conn, "REPLACE INTO session_info (owner_id, remaining_time) VALUES ('$userID', '$remaining_time')")) {
            throw new Exception(mysqli_error($conn));
        }
    } catch (Exception $e) {
        error_log("Failed to update session info: " . $e->getMessage());
    }

    echo json_encode([
        'session_status' => true,
        'session_timeout' => $session_timeout,
        'session_time' => $session_start_time,
        'time_left' => $remaining_time // Keeping precision up to 2 decimal places
    ]);
}
mysqli_close($conn);

?>

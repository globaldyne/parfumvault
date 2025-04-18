<?php
define('pvault_panel', TRUE);
define('__ROOT__', dirname(__FILE__)); 

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
$userID = $_SESSION['userID'];
error_log("User $userID logged out");

session_unset();
session_destroy();

try {
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
    if (!$conn) {
        throw new Exception("Failed to connect to database: " . mysqli_connect_error());
    }

    if (!mysqli_query($conn, "DELETE FROM session_info WHERE owner_id = '$userID'")) {
        throw new Exception("Failed to delete session info");
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $error_msg = $e->getMessage();
    require_once(__ROOT__.'/pages/error.php');
    exit();
}



if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

header("Location: /login.php");
exit;

?>

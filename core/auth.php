<?php
define('__ROOT__', dirname(dirname(__FILE__)));
define('pvault_panel', TRUE);

require_once(__ROOT__.'/inc/opendb.php');
//require_once(__ROOT__.'/func/cleanupNonHashedPasswords.php');

if(getenv('PLATFORM') === "CLOUD"){
	$session_timeout = getenv('SYS_TIMEOUT') ?: 1800;
} else {
	require_once(__ROOT__.'/inc/config.php');
}

if ($_POST['action'] == 'login') {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $response['auth']['error'] = true;
        $response['auth']['msg'] = 'Email and password fields cannot be empty';
        echo json_encode($response);
        return;
    }
    /*  
	if(cleanupNonHashedPasswords($conn)){
		$response['auth']['error'] = true;
        $response['auth']['msg'] = 'Your password has to be reset. Please <a href="/">reload</a> the page to recreate your user';
        echo json_encode($response);
        return;
	}
    */
    $email = mysqli_real_escape_string($conn, strtolower($_POST['email']));
    $password = $_POST['password'];

    // Fetch user details from the database
    $result = mysqli_query($conn, "SELECT id, email, password, role FROM users WHERE email='$email' AND isActive = '1'");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Verify the hashed password
        if (password_verify($password, $row['password'])) {
            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_set_cookie_params([
                    'lifetime' => $session_timeout, // Set cookie lifetime
                    'path' => '/', // Accessible throughout the domain
                    'secure' => isset($_SERVER['HTTPS']), // Secure cookie for HTTPS
                    'httponly' => true, // Prevent JS access to the cookie
                    'samesite' => 'Strict', // Protect against CSRF
                ]);
                session_start();
            }

            // Handle session timeout
            if (isset($_SESSION['parfumvault_time'])) {
                if ((time() - $_SESSION['parfumvault_time']) > $session_timeout) {
                    session_unset();
                    session_destroy();

                    $response['auth']['error'] = true;
                    $response['auth']['msg'] = 'Session expired. Please log in again.';
                    echo json_encode($response);
                    return;
                }
            }
            $_SESSION['parfumvault_time'] = time();

            // Set session variables
            $_SESSION['parfumvault'] = true;
            $_SESSION['userID'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['user_email'] = $row['email'];

            // Determine redirection
            $redirect = $_POST['do'] ? '/index.php?do=' . $_POST['do'] :
                ($_POST['url'] ? $_POST['url'] : '/index.php');

            $response['auth']['success'] = true;
            $response['auth']['redirect'] = $redirect;
            echo json_encode($response);
            return;
        } else {
            // Incorrect password
            $response['auth']['error'] = true;
            $response['auth']['msg'] = 'Invalid email or password';
            echo json_encode($response);
            return;
        }
    } else {
        // Email not found
        $response['auth']['error'] = true;
        $response['auth']['msg'] = 'Invalid email or password';
        echo json_encode($response);
        return;
    }
}

	
?>

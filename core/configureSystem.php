<?php
define('__ROOT__', dirname(dirname(__FILE__)));
define('pvault_panel', TRUE);

//SELF REGISTER
if ($_POST['action'] == 'selfregister') {
    require_once(__ROOT__ . '/inc/opendb.php');
    require_once(__ROOT__ . '/inc/settings.php');
    require_once(__ROOT__.'/func/mailSys.php');
    require_once(__ROOT__.'/func/validateInput.php');

    if ($system_settings['USER_selfRegister'] == '0') {
        $response['error'] = 'Self registration is disabled';
        echo json_encode($response);
        return;
    }

    $fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Validate full name length
    if (strlen($fullName) < 8) {
        $response['error'] = 'Full name must be at least 8 characters long';
        echo json_encode($response);
        return;
    }

    if (!preg_match("/^[a-zA-Z\s]+$/", $fullName)) {
        $response['error'] = 'Full name can only contain letters and spaces';
        echo json_encode($response);
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['error'] = 'Invalid email address';
        echo json_encode($response);
        return;
    }
    $checkEmailQuery = "SELECT id FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $checkEmailQuery);
    if (mysqli_num_rows($result) > 0) {
        $response['error'] = 'Email already exists';
        echo json_encode($response);
        return;
    }
    
    if (!isPasswordComplex($password)) {
        $response['error'] = 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character';
        echo json_encode($response);
        return;
    }

    $token = bin2hex(random_bytes(16)); // Generates a 32-character unique string
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $_id = bin2hex(random_bytes(16)); // Generates a 32-character unique string
    if($system_settings['EMAIL_isEnabled']){
        $insertUser = "INSERT INTO users (id, email, password, fullName, role, isActive, isVerified, token) VALUES ('$_id', '$email', '$hashedPassword', '$fullName', 2, 0, 0, '$token')";
    } else {
        $insertUser = "INSERT INTO users (id, email, password, fullName, role, isActive, isVerified, token) VALUES ('$_id','$email', '$hashedPassword', '$fullName', 2, 1, 1, '$token')";
    }

    if($system_settings['EMAIL_isEnabled']){
        if(welcomeNewUser($fullName,$email,$token)){
            notifyAdminForNewUser($fullName, $email, 'registered');
            error_log("Email sent to $email");
        } else {
            $response['error'] = 'Failed to complete registration, unable to send email';
            echo json_encode($response);
            error_log("Failed to send email to $email");
            return;
        }
    }

    if (mysqli_query($conn, $insertUser)) {
        if($system_settings['EMAIL_isEnabled']){
            $response['success'] = 'User created, please check your email to verify your account';
        } else {
            $response['success'] = 'User created';
        }
        echo json_encode($response);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['temp_response'] = $response['success'];
    } else {
        $response['error'] = 'Failed to create user: ' . mysqli_error($conn);
        echo json_encode($response);
    }

    return;
}

//FIRST TIME REGISTRATION
if ($_POST['action'] == 'register') {
    require_once(__ROOT__ . '/inc/opendb.php');

    // Check for required fields
    if (!$_POST['password'] || !$_POST['fullName'] || !$_POST['email']) {
        $response['error'] = "All fields required";
        echo json_encode($response);
        return;
    }

    // Validate full name length
    if (strlen($_POST['fullName']) < 8) {
        $response['error'] = "Full name must be at least 8 characters long!";
        echo json_encode($response);
        return;
    }

    // Sanitize and validate inputs
    $password = $_POST['password'];
    $fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $app_ver = trim(file_get_contents(__ROOT__ . '/VERSION.md'));

    // Validate password length
    if (strlen($_POST['password']) < 8) {
        $response['error'] = "Password must be at least 8 characters long!";
        echo json_encode($response);
        return;
    }

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $_id = bin2hex(random_bytes(16)); // Generates a 32-character unique string
    $insertUserQuery = "
        INSERT INTO users (id, email, password, fullName, role, isActive, isVerified) 
        VALUES ('$_id', '$email', '$hashedPassword', '$fullName', 1, 1, 1)";
    
    if (mysqli_query($conn, $insertUserQuery)) {
        $db_ver = trim(file_get_contents(__ROOT__ . '/db/schema.ver'));
        mysqli_query($conn, "INSERT INTO pv_meta (schema_ver, app_ver) VALUES ('$db_ver', '$app_ver')");
        $response['success'] = "User created";
        echo json_encode($response);
    } else {
        $response['error'] = 'Failed to register local user ' . mysqli_error($conn);
        echo json_encode($response);
    }

    return;
}

// RESET PASSWORD
if ($_POST['action'] == 'resetPassword') {
    require_once(__ROOT__ . '/inc/opendb.php');
    require_once(__ROOT__ . '/inc/settings.php');
    require_once(__ROOT__ . '/func/mailSys.php');
    require_once(__ROOT__ . '/func/validateInput.php');

    $response = [];
    if (isset($_POST['token']) && isset($_POST['newPassword'])) {
        $token = mysqli_real_escape_string($conn, $_POST['token']);
        $newPassword = $_POST['newPassword'];

        if (!isPasswordComplex($newPassword)) {
            $response['error'] = 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character';
            echo json_encode($response);
            return;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $checkTokenQuery = "SELECT email FROM password_resets WHERE token = '$token' AND expiry > NOW()";
        $result = mysqli_query($conn, $checkTokenQuery);
        if (mysqli_num_rows($result) == 0) {
            $response['error'] = 'Invalid or expired token';
            echo json_encode($response);
            return;
        }

        $row = mysqli_fetch_assoc($result);
        $email = $row['email'];

        $updatePasswordQuery = "UPDATE users SET password = '$hashedPassword' WHERE email = '$email'";
        if (mysqli_query($conn, $updatePasswordQuery)) {
            mysqli_query($conn, "DELETE FROM password_resets WHERE email = '$email'");
            $response['success'] = 'Password has been reset successfully';
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['temp_response'] = $response['success'];
        } else {
            $response['error'] = 'Failed to reset password: ' . mysqli_error($conn);
        }

        echo json_encode($response);
        return;
    }

    $email = mysqli_real_escape_string($conn, $_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['error'] = 'Invalid email address';
        echo json_encode($response);
        return;
    }

    $checkEmailQuery = "SELECT id FROM users WHERE email = '$email' AND isActive = 1 AND provider = 1";
    $result = mysqli_query($conn, $checkEmailQuery);
    if (mysqli_num_rows($result) == 0) {
        $response['error'] = 'Email does not exist or user is inactive';
        echo json_encode($response);
        return;
    }
    $checkTokenQuery = "SELECT token FROM password_resets WHERE email = '$email' AND expiry > NOW()";
    $result = mysqli_query($conn, $checkTokenQuery);
    if (mysqli_num_rows($result) > 0) {
        $response['error'] = 'Password reset already requested. Please check your email.';
        echo json_encode($response);
        return;
    }
    $token = bin2hex(random_bytes(16)); // Generates a 32-character unique string
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour

    $insertTokenQuery = "INSERT INTO password_resets (email, token, expiry) VALUES ('$email', '$token', '$expiry') ON DUPLICATE KEY UPDATE token='$token', expiry='$expiry'";
  //  if (mysqli_query($conn, $insertTokenQuery)) {
        if (sendPasswordResetEmail($email, $token)) {
            mysqli_query($conn, $insertTokenQuery);
            $response['success'] = 'Password reset email sent';
        } else {
            $response['error'] = 'Failed to send password reset email';
        }
   // } else {
    //    $response['error'] = 'Failed to generate reset token: ' . mysqli_error($conn);
   // }

    echo json_encode($response);
    return;
}
?>

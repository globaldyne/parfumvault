<?php 
if (!defined('pvault_panel')){ die('Not Found');}



function auth_sso() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    global $conn, $system_settings;

    // Keycloak Configuration
    $clientId = $system_settings['SSO_clientID'];
    $clientSecret = $system_settings['SSO_clientSecret'];
    $redirectUri = $system_settings['SSO_redirectUri'];
    $authUrl = $system_settings['SSO_authUrl'];
    $tokenUrl = $system_settings['SSO_tokenUrl'];
    $userInfoUrl = $system_settings['SSO_userInfoUrl'];

    if (!isset($_GET['code'])) {
        // Step 1: Redirect to Keycloak for authentication
        $state = bin2hex(random_bytes(16)); // Generate a secure state parameter
        $_SESSION['oauth2state'] = $state;

        $authorizationUrl = $authUrl . '?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'scope' => 'openid email profile',
        ]);
        $response = [
            'auth' => [
                'success' => true,
                'redirect' => $authorizationUrl,
            ],
        ];
        echo json_encode($response);
        return;
    }

    // Step 2: Validate state parameter
    if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
        error_log("Invalid state parameter");
        $response = [];
        $response['error'] = 'Invalid state parameter, check your sso settings';
        $_SESSION['temp_response'] = $response;
        header('Location: /index.php');
        return;
    }

    // Step 3: Exchange authorization code for an access token
    $code = $_GET['code'];
    $response = fetchAccessToken($tokenUrl, $clientId, $clientSecret, $redirectUri, $code);

    if (!isset($response['access_token'])) {
        error_log("Failed to retrieve access token");
        $response = [];
        $response['error'] = 'Failed to retrieve access token, check your sso settings';
        $_SESSION['temp_response'] = $response;
        header('Location: /index.php');
        return;
    }
    
    //Fetch user info using the access token
    $accessToken = $response['access_token'];
    $user = fetchUserInfo($userInfoUrl, $accessToken);

    if (!$user || !isset($user['email'])) {
        error_log("Failed to fetch user info");
        $response = [];
        $response['error'] = 'Failed to fetch user info, check your sso settings';
        $_SESSION['temp_response'] = $response;
        header('Location: /index.php');
        return;
    }

    $dummyPassword = bin2hex(random_bytes(16)); //Generate random password to populate user entry in local db - not used when auth is sso
    $hashedPassword = password_hash($dummyPassword, PASSWORD_BCRYPT);
    $token = bin2hex(random_bytes(16)); //Generate random token to populate user entry in local db - not used when auth is sso
    
    
    // User attributes
    $provider = 2; 
    $isVerified = $user['email_verified'];
    $isActive = 1;

    $userId = $user['sub'];
    $fullName = $user['given_name'].' '.$user['family_name'];
    $email = $user['email'];

  
    
    try {
        // Check if user already exists
        $checkQuery = $conn->prepare("SELECT id, isActive, role FROM users WHERE email = ? AND provider = ?");
        $checkQuery->bind_param("si", $email, $provider);
        $checkQuery->execute();
        $checkQuery->store_result();
        $checkQuery->bind_result($userId, $isActive, $role);
        $checkQuery->fetch();

        if ($isActive == 0) {
            $response = [];
            $response['error'] = 'User account is inactive';
            $_SESSION['temp_response'] = $response;
            header('Location: /index.php');
            return;
        }
        if ($checkQuery->num_rows > 0) {
            // Update existing user
            $updateQuery = $conn->prepare("UPDATE users SET fullName = ?, password = ?, token = ?, role = ?, isActive = ?, isVerified = ? WHERE email = ? AND provider = ?");
            //$role = 2;
            $updateQuery->bind_param("sssiiisi", $fullName, $hashedPassword, $token, $role, $isActive, $isVerified, $email, $provider);
            $updateQuery->execute();
            error_log("User found in auth_kc: " . $email);
            error_log("Update Query: " . $updateQuery->error);


            session_regenerate_id(true); // Prevent session fixation

            $_SESSION['parfumvault'] = true;
            $_SESSION['userID'] = $user['sub'];
            $_SESSION['role'] = 2;
            $_SESSION['user_email'] = $user['email'];
            header('Location: /index.php');
        } else {
            // Insert new user
            error_log("User NOT found in auth_sso: " . $email);

            $insertQuery = "INSERT INTO users (id, fullName, email, password, provider, token, role, isActive, isVerified) VALUES ('$userId', '$fullName', '$email', '$hashedPassword', $provider, '$token', 2, 1, 1)";
            if ($conn->query($insertQuery) === TRUE) {
                error_log("New user created successfully: " . $email);
            } else {
                error_log("Error: " . $insertQuery . " - " . $conn->error);
            }
            // Notify or login as needed
            if($system_settings['EMAIL_isEnabled']){
                require_once(__ROOT__.'/func/mailSys.php');

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
            session_regenerate_id(true); // Prevent session fixation

            $_SESSION['parfumvault'] = true;
            $_SESSION['userID'] = $user['sub'];
            $_SESSION['role'] = 2;
            $_SESSION['user_email'] = $user['email'];

            header('Location: /index.php');
        }
        // Update last_login timestamp
        try {
            $update_query = "UPDATE users SET last_login = NOW() WHERE id = '" . $row['id'] . "'";
            if (!mysqli_query($conn, $update_query)) {
                throw new Exception('Failed to update last login timestamp: ' . mysqli_error($conn));
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        // Close statements
        $checkQuery->close();
        if (isset($updateQuery)) $updateQuery->close();
        if (isset($insertQuery)) $insertQuery->close();
    } catch (Exception $e) {
        error_log("Error in auth_kc: " . $e->getMessage());
        echo json_encode(['auth' => ['error' => true, 'msg' => 'An error occurred during authentication.']]);
    }

    $conn->close();

    return;
}

function fetchAccessToken($tokenUrl, $clientId, $clientSecret, $redirectUri, $code) {
    $postFields = http_build_query([
        'grant_type' => 'authorization_code',
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri' => $redirectUri,
        'code' => $code,
    ]);

    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

function fetchUserInfo($userInfoUrl, $accessToken) {
    $headers = [
        'Authorization: Bearer ' . $accessToken,
    ];

    $ch = curl_init($userInfoUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

?>
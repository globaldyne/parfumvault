<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


if($role !== 1){
    echo 'Unauthorised';
    return;
}

$response = ['success' => false, 'message' => ''];

if ($_FILES['jsonFile']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['jsonFile']['tmp_name'];
    $fileContents = file_get_contents($fileTmpPath);
    $users = json_decode($fileContents, true);

    
    if (json_last_error() === JSON_ERROR_NONE) {
        if (!isset($users['users']) || !is_array($users['users'])) {
            $response['message'] = 'Invalid JSON format: missing "users" key or it is not an array';
            echo json_encode($response);
            exit;
        }

        foreach ($users['users'] as $user) {
            $requiredFields = ['fullName', 'email', 'isActive', 'role', 'provider', 'password', 'country', 'isAPIActive', 'isVerified'];
            $missingFields = [];

            foreach ($requiredFields as $field) {
                if (!isset($user[$field])) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                $response['message'] = 'Missing required user fields: ' . implode(', ', $missingFields);
                echo json_encode($response);
                exit;
            }

            $fullName = $user['fullName'];
            $email = $user['email'];
            $status = $user['isActive'];
            $role = $user['role'];
            $provider = $user['provider'];
            $password = $user['password'];
            $country = $user['country'];
            $isAPIActive = $user['isAPIActive'];
            $API_key = $user['API_key'];
            $isVerified = $user['isVerified'];

            // Check if email already exists
            $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                // Email already exists, skip to next user
                continue;
            }
            $checkStmt->close();

            $stmt = $conn->prepare("INSERT INTO users (fullName, email, isActive, role, provider, password, country, isAPIActive, API_key, isVerified ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiiissisi", $fullName, $email, $status, $role, $provider, $password, $country, $isAPIActive, $API_key, $isVerified);

            if (!$stmt->execute()) {
                $response['message'] = 'Error inserting user: ' . $stmt->error;
                echo json_encode($response);
                exit;
            }
        }
        $response['success'] = true;
        $response['message'] = 'Users imported successfully';
    } else {
        $response['message'] = 'Invalid JSON format';
    }
} else {
    $response['message'] = 'File upload error';
}

echo json_encode($response);
?>
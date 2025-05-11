<?php 
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/func/get_formula_notes.php');
require_once(__ROOT__.'/func/pvPost.php');
require_once(__ROOT__.'/func/pvFileGet.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/func/sanChar.php');
require_once(__ROOT__.'/func/priceScrape.php');
require_once(__ROOT__.'/func/create_thumb.php');
require_once(__ROOT__.'/func/mailSys.php');

// Ensure the user is authenticated
if (!isset($userID) || $userID === '') {
    echo json_encode(['error' => 'Unauthorized']);
    return;
}


// Ensure the user is an admin
if($role === 1) {
    //UPDATE SYSTEM SETTINGS
    if (isset($_POST['request']) && $_POST['request'] === 'updatesys') {
        $response = [];

        // Prepare the SQL query
        $query = "UPDATE system_settings SET value = ? WHERE key_name = ?";
        $stmt = $conn->prepare($query);

        // Update each setting
        foreach ($_POST as $key => $value) {
            if ($key === 'request') {
                continue;
            }
            // Sanitize inputs
            $key = mysqli_real_escape_string($conn, $key);
            $value = mysqli_real_escape_string($conn, $value);

            // Bind parameters
            $stmt->bind_param('ss', $value, $key);

            if ($stmt->execute()) {
                $response['success'] = 'System settings updated';
            } else {
                $response['error'] = 'Failed to update system settings';
                error_log("Error updating system settings: " . $stmt->error . " Query: " . $query . " Params: value=" . $value . ", key_name=" . $key);
            }
        }
        // Close the statement
        $stmt->close();
        echo json_encode($response);
        return;
    }

    //IMPERSONATE USER
    if (isset($_POST['request']) && $_POST['request'] === 'impersonateuser' && isset($_POST['impersonate_user_id'])) {
        $impersonate_user_id = $_POST['impersonate_user_id'];

        // Fetch user details
        $impersonateQuery = $conn->prepare("SELECT id, fullName, email, role FROM users WHERE id = ?");
        $impersonateQuery->bind_param("s", $impersonate_user_id);
        $impersonateQuery->execute();
        $result = $impersonateQuery->get_result();

        if ($result->num_rows > 0) {
            $impersonateUser = $result->fetch_assoc();

            // Start impersonation
            $_SESSION['userID'] = $impersonateUser['id'];
            $_SESSION['role'] = $impersonateUser['role'];
            $_SESSION['impersonateuser'] = true;

            echo json_encode(['success' => 'User impersonation started', 'redirect_url' => '/']);
        } else {
            echo json_encode(['error' => 'User not found']);
        }

        $impersonateQuery->close();
        return;
    }


    //UPDATE USER INFO BY ADMIN
    if (isset($_POST['request']) && $_POST['request'] === 'updateuser') {
        // Validate required fields
        if (empty($_POST['user_id']) || empty($_POST['full_name']) || empty($_POST['email']) ) {
            echo json_encode(['error' => 'Full name and email are required']);
            return;
        }

        // Sanitize input
        $user_id = $conn->real_escape_string($_POST['user_id']);
        $full_name = $conn->real_escape_string($_POST['full_name']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'] ?? null;
        $country = $conn->real_escape_string($_POST['country']);
        $role = (int)$_POST['role'];
        $isActive = (int)$_POST['isActive'];
        $isVerified = (int)$_POST['isVerified'];
        $receiveEmails = (int)$_POST['receiveEmails'];

        // Validate password if provided
        if (!empty($password) && !isPasswordComplex($password)) {
            echo json_encode(['error' => 'Password does not meet complexity requirements']);
            return;
        }

        // Check for last admin user restrictions
        if ($role === 2) {
            $checkAdminQuery = $conn->prepare("SELECT role FROM users WHERE id = ?");
            $checkAdminQuery->bind_param("s", $user_id);
            $checkAdminQuery->execute();
            $result = $checkAdminQuery->get_result();
            $user = $result->fetch_assoc();

            if ($user['role'] === 1) {
                $adminCountQuery = $conn->prepare("SELECT COUNT(*) as admin_count FROM users WHERE role = 1");
                $adminCountQuery->execute();
                $adminCountResult = $adminCountQuery->get_result();
                $adminCount = $adminCountResult->fetch_assoc()['admin_count'];

                if ($adminCount <= 1) {
                    echo json_encode(['error' => 'Cannot change the last admin user to a different access level']);
                    return;
                }
            }
        }

        // Fetch owner_id
        $ownerQuery = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $ownerQuery->bind_param("s", $user_id);
        $ownerQuery->execute();
        $ownerResult = $ownerQuery->get_result();

        if ($ownerResult->num_rows === 0) {
            echo json_encode(['error' => 'User not found']);
            return;
        }

        // Prepare queries
        $updateQuery = "UPDATE users SET fullName = ?, email = ?, country = ?, role = ?, isActive = ?, isVerified = ?, receiveEmails = ?";
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateQuery .= ", password = ?";
        }
        $updateQuery .= " WHERE id = ?"; // Ensure this line is properly formatted and does not contain stray characters

        //$user_id = $ownerResult->fetch_assoc()['id'];

        // Execute user update query
        $stmt = $conn->prepare($updateQuery);
        if (!empty($password)) {
            $stmt->bind_param(
                "sssiiiiss",
                $full_name,
                $email,
                $country,
                $role,
                $isActive,
                $isVerified,
                $receiveEmails,
                $hashedPassword,
                $user_id
            );
        } else {
            $stmt->bind_param(
                "sssiiiis",
                $full_name,
                $email,
                $country,
                $role,
                $isActive,
                $isVerified,
                $receiveEmails,
                $user_id
            );
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => 'User updated successfully']);        
        } else {
            echo json_encode(['error' => 'Failed to update user: ' . $conn->error]);
        }

        // Close statements
        $stmt->close();
        $ownerQuery->close();
        return;
    }


    //CREATE USER BY ADMIN
    if (isset($_POST['request']) && $_POST['request'] === 'adduser') {

        if (empty($_POST['email']) || empty($_POST['password']) || empty($_POST['full_name']) || empty($_POST['country'])) {
            $response['error'] = 'Email, password, name and country are required';
            echo json_encode($response);
            return;
        }

        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $role = (int)$_POST['role'];
        $country = mysqli_real_escape_string($conn, $_POST['country']);
        $isActive = mysqli_real_escape_string($conn, $_POST['isActive']);
        $isVerified = mysqli_real_escape_string($conn, $_POST['isVerified']);

        if (!isPasswordComplex($password)) {
            $response['error'] = 'Password does not meet complexity requirements';
            echo json_encode($response);
            return;
        }
        
        // Check if email is already registered
        $emailCheckQuery = "SELECT id FROM users WHERE email = '$email'";
        $emailCheckResult = mysqli_query($conn, $emailCheckQuery);
    	$_id = bin2hex(random_bytes(16)); // Generates a 32-character unique string

        if (mysqli_num_rows($emailCheckResult) > 0) {
            $response['error'] = 'Email is already registered';
            echo json_encode($response);
            return;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into the database
        $insertQuery = "INSERT INTO users (id, email, password, fullName, role, country, isActive, isVerified) 
                    VALUES ('$_id','$email', '$hashedPassword', '$full_name', '$role', '$country', '$isActive', '$isVerified')";


        if (mysqli_query($conn, $insertQuery)) {
        //   welcomeNewUser($fname,$email,$token);
            $response['success'] = 'User created successfully';
        } else {
            $response['error'] = 'Database error: ' . mysqli_error($conn);
        }


        echo json_encode($response);
        return;
    }

    //DELETE USER BY ADMIN
    if (isset($_POST['request']) && $_POST['request'] === 'deleteuser') {
        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
        
        // Check if the user is an admin
        $checkAdminQuery = $conn->prepare("SELECT role FROM users WHERE id = ?");
        $checkAdminQuery->bind_param("s", $user_id);
        $checkAdminQuery->execute();
        $result = $checkAdminQuery->get_result();
        $user = $result->fetch_assoc();

        if ($user['role'] === 1) {
            // Check if this is the last admin
            $adminCountQuery = $conn->prepare("SELECT COUNT(*) as admin_count FROM users WHERE role = '1'");
            $adminCountQuery->execute();
            $adminCountResult = $adminCountQuery->get_result();
            $adminCount = $adminCountResult->fetch_assoc()['admin_count'];

            if ($adminCount <= 1) {
                $response['error'] = 'Cannot delete the last admin user';
                echo json_encode($response);
                return;
            }
        }

        // Fetch user_id for the user
        $userQuery = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $userQuery->bind_param("s", $user_id);
        $userQuery->execute();
        $userResult = $userQuery->get_result();
        $userData = $userResult->fetch_assoc();

        if ($userData) {
            // Proceed with deletion
            $deleteQuery = $conn->prepare("DELETE FROM users WHERE id = ?");
            $deleteQuery->bind_param("s", $user_id);

            $tables = [
                "batchIDHistory", "bottles", "cart", "customers", "documents",
                "formulaCategories", "formulas", "formulasMetaData", "formulasRevisions", "formulasTags",
                "formula_history", "IFRALibrary", "ingCategory", "ingredients", "ingredient_compounds",
                "ingredient_safety_data", "ingReplacements", "ingSafetyInfo", "ingSuppliers", "inventory_accessories",
                "inventory_compounds", "makeFormula", "perfumeTypes", "sds_data", "suppliers", "synonyms",
                "templates", "user_prefs", "user_settings", "branding", "orders", "order_items"
            ];

            foreach ($tables as $table) {
                $deleteStmt = $conn->prepare("DELETE FROM $table WHERE owner_id = ?");
                $deleteStmt->bind_param("s", $user_id);
                $deleteStmt->execute();
                $deleteStmt->close();
            }

            if ($deleteQuery->execute()) {
                $response['success'] = 'User deleted successfully';
            } else {
                $response['error'] = 'Database error: ' . $deleteQuery->error;
            }
            $deleteQuery->close();
        } else {
            $response['error'] = 'User not found';
        }

        echo json_encode($response);
        return;
    }
} // End of admin-only actions

//HANDLE ORDERS
if (isset($_POST['action']) && $_POST['action'] === 'addorder') {
    $randomString = bin2hex(random_bytes(8)); // Generates a 16-character random string
    $uploadDir = $tmp_path . "/uploads/" . $randomString . "/";

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0740, true);
    }
    // Validate received data
    $supplier_id = mysqli_real_escape_string($conn, $_POST['supplier_id']);

    $supplierQuery = $conn->prepare("SELECT name FROM ingSuppliers WHERE id = ? AND owner_id = ?");
    $supplierQuery->bind_param("is", $supplier_id, $userID);
    $supplierQuery->execute();
    $supplierResult = $supplierQuery->get_result();
    $supplierData = $supplierResult->fetch_assoc();
    $supplier_name = $supplierData['name'];
    $supplierQuery->close();

    $currency = mysqli_real_escape_string($conn, $_POST['currency']);
    $order_items = $_POST['order_items']; // Assuming order_items is an array
    $tax = (float)$_POST['tax'] ?: 0;
    $shipping = (float)$_POST['shipping'] ?: 0;
    $discount = (float)$_POST['discount'] ?: 0;
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $reference_number = mysqli_real_escape_string($conn, $_POST['reference_number']);
    $order_number = mysqli_real_escape_string($conn, $_POST['order_number']);

    $missingFields = [];
    if (empty($supplier_name)) $missingFields[] = 'Supplier';
    if (empty($currency)) $missingFields[] = 'Currency';
    if (empty($order_number)) $missingFields[] = 'Order number';
    if (empty($order_items)) $missingFields[] = 'Order items';

    if (!empty($missingFields)) {
        echo json_encode(['error' => 'The following fields are required: ' . implode(', ', $missingFields)]);
        return;
    }

    // Handle file upload if provided
    $attachments = null;
    if (isset($_FILES['orderFile']) && $_FILES['orderFile']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['orderFile']['tmp_name'];
        $file_name = $_FILES['orderFile']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv'];

        if (in_array($file_ext, $allowed_ext)) {
            $file_path = $uploadDir . $file_name;
            if (move_uploaded_file($file_tmp, $file_path)) {
                $attachments = file_get_contents($file_path); // Read file contents
            } else {
                echo json_encode(['error' => 'Failed to upload file.']);
                return;
            }
        } else {
            echo json_encode(['error' => 'Invalid file type. Allowed types: ' . implode(', ', $allowed_ext)]);
            return;
        }
    }

    // Insert order into the database
    $status = 'pending';
    $orderQuery = "INSERT INTO orders (order_id, reference_number, supplier, currency, status, tax, shipping, discount, received, notes, attachments, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($orderQuery);
    $stmt->bind_param('sssssdddssss', $order_number, $reference_number, $supplier_name, $currency, $status, $tax, $shipping, $discount, $received, $notes, $attachments, $userID);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        // Insert order items
        $itemQuery = "INSERT INTO order_items (order_id, material, size, unit_price, quantity, lot, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $itemStmt = $conn->prepare($itemQuery);

        foreach ($order_items as $item) {
            $material = mysqli_real_escape_string($conn, $item['item']);
            $size = mysqli_real_escape_string($conn, $item['size']);
            $unit_price = mysqli_real_escape_string($conn, $item['unit_price']);
            $quantity = mysqli_real_escape_string($conn, $item['quantity']);
            $lot = mysqli_real_escape_string($conn, $item['lot_number']) ?: '-';

            $itemStmt->bind_param('isdddss', $order_id, $material, $size, $unit_price, $quantity, $lot, $userID);
            $itemStmt->execute();
        }

        $itemStmt->close();

        // Remove the uploaded file after successful insertion
        if ($attachments) {
         //   unlink($attachments);
            rmdir($uploadDir);
        }

        echo json_encode(['success' => 'Order added successfully']);
    } else {
        echo json_encode(['error' => 'Failed to add order: ' . $stmt->error]);
        error_log("Error adding order: " . $stmt->error);
    }

    $stmt->close();

    return;
}

//UPDATE ORDER
if(isset($_POST['action']) && $_POST['action'] === 'updateorder') {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $orderStatus = mysqli_real_escape_string($conn, $_POST['orderStatus']);
    $orderDate = mysqli_real_escape_string($conn, $_POST['orderDate']);
    $receivedDate = mysqli_real_escape_string($conn, $_POST['receivedDate']) ?: null;
    $reference_number = mysqli_real_escape_string($conn, $_POST['reference_number']);
    $orderNotes = mysqli_real_escape_string($conn, $_POST['orderNotes']);

    $updateQuery = $conn->prepare("UPDATE orders SET status = ?, placed = ?, received = ?, reference_number = ?, notes = ? WHERE id = ? AND owner_id = ?");
    $updateQuery->bind_param("sssssis", $orderStatus, $orderDate, $receivedDate, $reference_number, $orderNotes, $order_id, $userID);

    if ($updateQuery->execute()) {
        $response['success'] = 'Order updated successfully';
    } else {
        $response['error'] = 'Failed to update order: ' . $updateQuery->error;
    }

    echo json_encode($response);
    return;
}

//RE-ORDER
if(isset($_POST['action']) && $_POST['action'] === 'reorder') {
    $supplierEmail = mysqli_real_escape_string($conn, $_POST['supplierEmail']);
    $orderItems = $_POST['items'];
    $supplier = mysqli_real_escape_string($conn, $_POST['supplier']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $currency = mysqli_real_escape_string($conn, $_POST['currency']);
    $response = [];

    // Insert new order
    $status = 'pending';
    $tax = 0;
    $shipping = 0;
    $discount = 0;

    $orderNumber = bin2hex(random_bytes(8)); // Generates a 16-character random string
    $orderQuery = $conn->prepare("INSERT INTO orders (order_id, supplier, currency, status, tax, shipping, discount, notes, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $orderQuery->bind_param("ssssdddss", $orderNumber, $supplier, $currency, $status, $tax, $shipping, $discount, $notes, $userID);

    if ($orderQuery->execute()) {
        $newOrderID = $orderQuery->insert_id;

        // Insert order items
        $itemQuery = $conn->prepare("INSERT INTO order_items (order_id, material, size, unit_price, quantity, lot, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $itemQuery->bind_param("isdddss", $newOrderID, $material, $size, $unit_price, $quantity, $lot, $userID);

        foreach ($orderItems as $item) {
            $material = $item['item'];
            $size = $item['size'];
            $unit_price = $item['unit_price'];
            $quantity = $item['quantity'];
            $lot = $item['sku'] ?: '-';
            $itemQuery->execute();
        }

        $itemQuery->close();

        $templateContent = file_get_contents(__ROOT__.'/emailTemplates/reorderIngredientsTemplate.html');

        // Replace placeholders in the template
        $templateContent = str_replace('{{supplier}}', htmlspecialchars($supplier, ENT_QUOTES, 'UTF-8'), $templateContent);
        $templateContent = str_replace('{{notes}}', nl2br(htmlspecialchars($notes, ENT_QUOTES, 'UTF-8')), $templateContent);

        $itemsHtml = '';
        foreach ($orderItems as $item) {
            $itemsHtml .= '<tr>';
            $itemsHtml .= '<td>' . htmlspecialchars($item['item'], ENT_QUOTES, 'UTF-8') . '</td>';
            $itemsHtml .= '<td>' . htmlspecialchars($item['size'], ENT_QUOTES, 'UTF-8') . '</td>';
            $itemsHtml .= '<td>' . htmlspecialchars($item['unit_price'], ENT_QUOTES, 'UTF-8') . '</td>';
            $itemsHtml .= '<td>' . htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8') . '</td>';
            $itemsHtml .= '<td>' . htmlspecialchars($item['sku'], ENT_QUOTES, 'UTF-8') . '</td>';
            $itemsHtml .= '</tr>';
        }
        $templateContent = str_replace('{{orderItems}}', $itemsHtml, $templateContent);

        // Send email
        $subject = $orderNumber.' - New order Request';
        $sendMail = sendMail($supplierEmail, $subject, $templateContent);

        if ($sendMail) {
            $response['success'] = 'Order successfully placed and email sent.';
        } else {
            $response['error'] = 'Order placed but failed to send email.';
        }
    } else {
        $response['error'] = 'Failed to create new order: ' . $orderQuery->error;
    }

    $orderQuery->close();
    echo json_encode($response);
    return;
}


//DELETE ORDER
if(isset($_GET['action']) && $_GET['action'] === 'deleteorder') {
    $order_id = mysqli_real_escape_string($conn, $_GET['order_id']);

    $deleteOrderQuery = $conn->prepare("DELETE FROM orders WHERE id = ? AND owner_id = ?");
    $deleteOrderQuery->bind_param("is", $order_id, $userID);

    $deleteOrderItemsQuery = $conn->prepare("DELETE FROM order_items WHERE order_id = ? AND owner_id = ?");
    $deleteOrderItemsQuery->bind_param("is", $order_id, $userID);
    
    if ($deleteOrderItemsQuery->execute()) {
        $response['success'] = 'Order items deleted successfully';
    } else {
        $response['error'] = 'Failed to delete order items: ' . $deleteOrderItemsQuery->error;
    }

    if ($deleteOrderQuery->execute()) {
        $response['success'] = 'Order deleted successfully';
    } else {
        $response['error'] = 'Failed to delete order: ' . $deleteOrderQuery->error;
    }

    echo json_encode($response);
    return;
}

//DELETE PROFILE
if (isset($_GET['action']) && $_GET['action'] === 'deleteprofile') {
    
    // Check if the user is an admin
    $checkAdminQuery = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $checkAdminQuery->bind_param("s", $userID);
    $checkAdminQuery->execute();
    $result = $checkAdminQuery->get_result();
    $user = $result->fetch_assoc();

    if ($user['role'] === 1) {
        // Check if this is the last admin
        $adminCountQuery = $conn->prepare("SELECT COUNT(*) as admin_count FROM users WHERE role = '1'");
        $adminCountQuery->execute();
        $adminCountResult = $adminCountQuery->get_result();
        $adminCount = $adminCountResult->fetch_assoc()['admin_count'];

        if ($adminCount <= 1) {
            $response['error'] = 'Cannot delete the last admin user';
            echo json_encode($response);
            return;
        }
    }

    // Fetch user_id for the user
    $userQuery = $conn->prepare("SELECT id,email,fullName FROM users WHERE id = ?");
    $userQuery->bind_param("s", $userID);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    $userData = $userResult->fetch_assoc();

    if ($userData) {
        // Proceed with deletion
        $deleteQuery = $conn->prepare("DELETE FROM users WHERE id = ?");
        $deleteQuery->bind_param("s", $userID);

        $tables = [
            "batchIDHistory", "bottles", "cart", "customers", "documents",
            "formulaCategories", "formulas", "formulasMetaData", "formulasRevisions", "formulasTags",
            "formula_history", "IFRALibrary", "ingCategory", "ingredients", "ingredient_compounds",
            "ingredient_safety_data", "ingReplacements", "ingSafetyInfo", "ingSuppliers", "inventory_accessories",
            "inventory_compounds", "makeFormula", "perfumeTypes", "sds_data", "suppliers", "synonyms",
            "templates", "user_prefs", "user_settings", "branding", "orders", "order_items"
        ];

        foreach ($tables as $table) {
            $deleteStmt = $conn->prepare("DELETE FROM $table WHERE owner_id = ?");
            $deleteStmt->bind_param("s", $userID);
            $deleteStmt->execute();
            $deleteStmt->close();
        }

        if ($deleteQuery->execute()) {
            $response['success'] = 'User deleted successfully';
            notifyAdminForNewUser($userData['fullName'], $userData['email'], 'deleted');
            userGoodbye($userData['fullName'], $userData['email']);
        } else {
            $response['error'] = 'Database error: ' . $deleteQuery->error;
        }
        $deleteQuery->close();
    } else {
        $response['error'] = 'User not found';
    }

    echo json_encode($response);
    return;
}


//IMPORT FORMULA FROM TEXT
if ($_POST['action'] == 'importTXTFormula') {
    require_once(__ROOT__ . '/func/genFID.php');

    $formulaName = isset($_POST['formulaName']) ? trim($_POST['formulaName']) : '';
    $formulaData = isset($_POST['formulaData']) ? trim($_POST['formulaData']) : '';

    $response = [];

    if (empty($formulaName) || empty($formulaData)) {
        $response['error'] = 'Formula name and data are required.';
        echo json_encode($response);
        return;
    }

	// Replace commas with dots in formula data
	$formulaData = str_replace(',', '.', $formulaData);
    // Check if formula name exists
    $query = "SELECT COUNT(*) as count FROM formulasMetaData WHERE name = ? AND owner_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $formulaName, $userID);

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        $response['error'] = 'Formula name already exists.';
        echo json_encode($response);
        return;
    }

    // Insert new formula metadata
    $fid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');
    $notes = "Imported via text";

    $insertQuery = "INSERT INTO formulasMetaData (fid, name, notes, owner_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param('ssss', $fid, $formulaName, $notes, $userID);

    if ($stmt->execute()) {
        // Get the last inserted ID
        $last_id = $conn->insert_id;

        // Parse and insert formula data
        $rows = explode("\n", $formulaData);
        $formulaInsertSuccess = true;

        foreach ($rows as $row) {
            // Match flexible patterns for quantity, ingredient name, and percentage
            if (preg_match('/^(\d+(\.\d+)?)\s+(.+?)(\d+(\.\d+)?%)?$|(.+?)\s*(\d+(\.\d+)?%)?\s+(\d+(\.\d+)?)$/', $row, $matches)) {
                if (!empty($matches[1])) {
                    // Format: Quantity first (e.g., "5.00 phenyl acetaldehyde 50%")
                    $quantity = floatval($matches[1]);
                    $ingredient = trim($matches[3]);
                    $percentage = isset($matches[4]) ? floatval($matches[4]) : 100;
                } else {
                    // Format: Quantity last (e.g., "phenyl acetaldehyde 50% 5")
                    $quantity = floatval($matches[9]);
                    $ingredient = trim($matches[6]);
                    $percentage = isset($matches[7]) ? floatval($matches[7]) : 100;
                }

                $dilutant = $percentage < 100 ? 'DPG' : 'None';
                if(!$percentage){
                    $percentage = 100;
                    $dilutant = 'None';
                }
                // Clean up the ingredient name
                $baseIngredient = preg_replace('/\s*\d+(\.\d+)?%\s*/', '', $ingredient);
                $baseIngredient = ucwords($baseIngredient);

                // Check if the ingredient exists in the database
                $getIngQuery = "SELECT id, name FROM ingredients WHERE name = ? AND owner_id = ?";
                $getIngStmt = $conn->prepare($getIngQuery);
                $getIngStmt->bind_param('ss', $baseIngredient, $userID);
                $getIngStmt->execute();
                $getIngResult = $getIngStmt->get_result();
                $ingredientRow = $getIngResult->fetch_assoc();

                $ingredient_id = $ingredientRow['id'] ?? 0; // Use ID if found, otherwise 0
                $ingredient_name = $ingredientRow['name'] ?? $baseIngredient;

                // Insert into formulas table
                $ingredientQuery = "INSERT INTO formulas (fid, name, ingredient_id, ingredient, quantity, concentration, dilutant, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $ingredientStmt = $conn->prepare($ingredientQuery);
                $ingredientStmt->bind_param('ssisdsss', $fid, $formulaName, $ingredient_id, $ingredient_name, $quantity, $percentage, $dilutant, $userID);

                if (!$ingredientStmt->execute()) {
                    $formulaInsertSuccess = false;
                    break;
                }
            }
        }

        // Insert tag associated with the formula
        if ($formulaInsertSuccess) {
            $tagQuery = "INSERT INTO formulasTags (formula_id, tag_name, owner_id) VALUES (?, 'Imported formula', ?)";
            $tagStmt = $conn->prepare($tagQuery);
            $tagStmt->bind_param('is', $last_id, $userID);

            if ($tagStmt->execute()) {
                $response['success'] = 'Formula imported successfully.';
            } else {
                $response['error'] = 'Failed to insert formula tag.';
            }
        } else {
            $response['error'] = 'Failed to insert formula data.';
        }
    } else {
        $response['error'] = 'Failed to import formula metadata.';
    }

    // Return JSON response
    echo json_encode($response);
    return;
}


if($_GET['update_user_avatar']){

	$allowed_ext = ['png', 'jpg', 'jpeg', 'gif', 'bmp'];
    $response = [];

	$file_info = $_FILES['avatar'];
    $file_ext = strtolower(pathinfo($file_info['name'], PATHINFO_EXTENSION));
    $file_tmp = $file_info['tmp_name'];

	
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        $response['error'] = 'Please choose a valid file to upload.';
        echo json_encode($response);
        return;
    }	

	if (!in_array($file_ext, $allowed_ext)) {
        $response['error'] = 'Invalid file extension. Allowed: ' . implode(', ', $allowed_ext);
        echo json_encode($response);
        return;
    }

	if (!file_exists($tmp_path."/uploads/logo/")) {
		mkdir($tmp_path."/uploads/logo/", 0740, true);
	}
		
    $unique_filename = uniqid('avatar_', true) . '.' . $file_ext;
    $destination = $tmp_path . "/uploads/logo/" . $unique_filename;
		
	if (move_uploaded_file($file_tmp, $destination)) {
        create_thumb($destination, 250, 250);

        $docData = 'data:image/' . $file_ext . ';base64,' . base64_encode(file_get_contents($destination));

        $stmt = $conn->prepare("DELETE FROM documents WHERE ownerID = ? AND type = '3' AND name = 'avatar' AND owner_id = ?");
        $stmt->bind_param('is', $user['id'], $userID);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO documents (ownerID, type, name, notes, docData, owner_id) VALUES (?, '3', 'avatar', 'Main Profile Avatar', ?, ?)");
        $stmt->bind_param('iss', $user['id'], $docData, $userID);

        if ($stmt->execute()) {
            unlink($destination);
            $response['success'] = [
                'msg' => 'User avatar updated!',
                'avatar' => $docData,
            ];
            echo json_encode($response);
            return;
        } else {
            $response['error'] = 'Database error occurred.';
            echo json_encode($response);
            return;
        }
    } else {
        $response['error'] = 'Failed to upload the file.';
        echo json_encode($response);
        return;
    }

	return;
}

//UPDATE USER PROFILE
if ($_POST['action'] === 'update_user_profile') {
    if (getenv('USER_EMAIL') && getenv('USER_NAME') && getenv('USER_PASSWORD')) {
        echo json_encode(["error" => "User information is externally managed and cannot be updated here."]);
        return;
    }

    // Validate required fields
    if (empty($_POST['user_fname']) || empty($_POST['user_email'])) {
        echo json_encode(["error" => "All fields are required"]);
        return;
    }

    // Validate full name length
    if (strlen($_POST['user_fname']) < 5) {
        echo json_encode(["error" => "Full name must be at least 5 characters long"]);
        return;
    }

    if (!preg_match("/^[a-zA-Z\s]+$/", $_POST['user_fname'])) {
        $response['error'] = 'Full name can only contain letters and spaces';
        echo json_encode($response);
        return;
    }

    // Validate email format
    if (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Invalid email address"]);
        return;
    }

    // Sanitize inputs
    $fullName = trim(mysqli_real_escape_string($conn, $_POST['user_fname']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['user_email']));
    $password = $_POST['user_pass'];
    $country = mysqli_real_escape_string($conn, $_POST['user_country']);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param('ss', $email, $userID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["error" => "Email address is already in use"]);
        $stmt->close();
        return;
    }
    $stmt->close();

    // Handle password update
    $passwordClause = '';
    if (!empty($password)) {
        if (strlen($password) < 5) {
            echo json_encode(["error" => "Password must be at least 5 characters long"]);
            return;
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $passwordClause = ", password=?";
    }

    // Prepare the SQL query
    $query = "UPDATE users SET fullName = ?, email = ?, country = ?" . $passwordClause . " WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($passwordClause) {
        $stmt->bind_param('sssss', $fullName, $email, $country, $hashedPassword, $userID);
    } else {
        $stmt->bind_param('ssss', $fullName, $email, $country, $userID);
    }

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            if ($email !== $_SESSION['user_email']) {
                echo json_encode(["success" => "User details updated. Please log in again with your new email address."]);
                session_destroy();
            } else {
                echo json_encode(["success" => "User details updated"]);
            }
        } else {
            echo json_encode(["success" => "No changes were made"]);
        }
    } else {
        error_log("PV error: " . $stmt->error); // Log the error
        echo json_encode(["error" => "Failed to update user details. Please try again later."]);
    }

    $stmt->close();
    return;
}

// UPDATE PVAI SETTINGS
if ($_POST['action'] === 'update_openai_settings') {
    $use_ai_service = isset($_POST['use_ai_service']) && $_POST['use_ai_service'] !== '' ? ($_POST['use_ai_service'] === 'true' ? '1' : '0') : null;
    $use_ai_chat = isset($_POST['use_ai_chat']) && $_POST['use_ai_chat'] !== '' ? ($_POST['use_ai_chat'] === 'true' ? '1' : '0') : null;
    $ai_service_provider = isset($_POST['ai_service_provider']) ? mysqli_real_escape_string($conn, $_POST['ai_service_provider']) : null;
    $making_ai_chat = isset($_POST['making_ai_chat']) && $_POST['making_ai_chat'] !== '' ? ($_POST['making_ai_chat'] === 'true' ? '1' : '0') : null;
    
    // OpenAI Settings (only if present)
    $openai_api_key = isset($_POST['openai_api_key']) ? mysqli_real_escape_string($conn, $_POST['openai_api_key']) : null;
    $openai_model = isset($_POST['openai_model']) ? mysqli_real_escape_string($conn, $_POST['openai_model']) : null;
    $openai_temperature = isset($_POST['openai_temperature']) ? mysqli_real_escape_string($conn, $_POST['openai_temperature']) : null;

    // Gemini Settings (only if present)
    $google_gemini_api_key = isset($_POST['google_gemini_api_key']) ? mysqli_real_escape_string($conn, $_POST['google_gemini_api_key']) : null;
    $google_gemini_model = isset($_POST['google_gemini_model']) ? mysqli_real_escape_string($conn, $_POST['google_gemini_model']) : null;

    function upsert_user_setting($conn, $userID, $key, $value) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM user_settings WHERE key_name = ? AND owner_id = ?");
        if (!$stmt) {
            error_log("PV error: Failed to prepare count query - " . $conn->error);
            return false;
        }
        $stmt->bind_param('ss', $key, $userID);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $stmt = $conn->prepare("UPDATE user_settings SET value = ? WHERE key_name = ? AND owner_id = ?");
            if (!$stmt) {
                error_log("PV error: Failed to prepare update query - " . $conn->error);
                return false;
            }
            $stmt->bind_param('sss', $value, $key, $userID);
        } else {
            $stmt = $conn->prepare("INSERT INTO user_settings (key_name, value, owner_id) VALUES (?, ?, ?)");
            if (!$stmt) {
                error_log("PV error: Failed to prepare insert query - " . $conn->error);
                return false;
            }
            $stmt->bind_param('sss', $key, $value, $userID);
        }

        $result = $stmt->execute();
        if (!$result) {
            error_log("PV error: Failed to execute upsert for $key - " . $stmt->error);
        }
        $stmt->close();
        return $result;
    }

    $success = true;

    if ($use_ai_service !== null) {
        $success &= upsert_user_setting($conn, $userID, 'use_ai_service', $use_ai_service);
    }

    if ($use_ai_chat !== null) {
        $success &= upsert_user_setting($conn, $userID, 'use_ai_chat', $use_ai_chat);
    }
    
    if ($ai_service_provider !== null) {
        $success &= upsert_user_setting($conn, $userID, 'ai_service_provider', $ai_service_provider);
    }

    // Only update if values are present in POST
    if ($openai_api_key !== null) {
        $success &= upsert_user_setting($conn, $userID, 'openai_api_key', $openai_api_key);
    }

    if ($openai_model !== null) {
        $success &= upsert_user_setting($conn, $userID, 'openai_model', $openai_model);
    }

    if ($openai_temperature !== null) {
        $success &= upsert_user_setting($conn, $userID, 'openai_temperature', $openai_temperature);
    }

    if ($google_gemini_api_key !== null) {
        $success &= upsert_user_setting($conn, $userID, 'google_gemini_api_key', $google_gemini_api_key);
    }

    if ($google_gemini_model !== null) {
        $success &= upsert_user_setting($conn, $userID, 'google_gemini_model', $google_gemini_model);
    }

    if ($making_ai_chat !== null) {
        $success &= upsert_user_setting($conn, $userID, 'making_ai_chat', $making_ai_chat);
    }

    if ($success) {
        echo json_encode(['success' => 'AI settings updated successfully']);
    } else {
        echo json_encode(['error' => 'Failed to update one or more AI settings']);
    }
    return;
}



//UPDATE USER SETTINGS
if ($_POST['action'] === 'update_user_settings') {
    $currency = mysqli_real_escape_string($conn, $_POST['currency']);
    $currency_code = mysqli_real_escape_string($conn, $_POST['currency_code']);

    $top_n = mysqli_real_escape_string($conn, $_POST['top_n']);
    $heart_n = mysqli_real_escape_string($conn, $_POST['heart_n']);
    $base_n = mysqli_real_escape_string($conn, $_POST['base_n']);
    
    $qStep = mysqli_real_escape_string($conn, $_POST['qStep']);
    $defCatClass = mysqli_real_escape_string($conn, $_POST['defCatClass']);
    $grp_formula = mysqli_real_escape_string($conn, $_POST['grp_formula']);
    $pubchem_view = mysqli_real_escape_string($conn, $_POST['pubchem_view']);
    $mUnit = mysqli_real_escape_string($conn, $_POST['mUnit']);
    $editor = mysqli_real_escape_string($conn, $_POST['editor']);
    $user_pref_eng = mysqli_real_escape_string($conn, $_POST['user_pref_eng']);
    $defPercentage = $_POST['defPercentage'];
    $bs_theme = $_POST['bs_theme'];
    $temp_sys = $_POST['temp_sys'];
    
    $chem_vs_brand = isset($_POST["chem_vs_brand"]) && $_POST["chem_vs_brand"] === 'true' ? '1' : '0';
    $chkVersion = isset($_POST["chkVersion"]) && $_POST["chkVersion"] === 'true' ? '1' : '0';
    $multi_dim_perc = isset($_POST["multi_dim_perc"]) && $_POST["multi_dim_perc"] === 'true' ? '1' : '0';
    $allow_incomplete_ingredients = isset($_POST["allow_incomplete_ingredients"]) && $_POST["allow_incomplete_ingredients"] === 'true' ? '1' : '0';
    
    $response = [];
    foreach ($_POST as $key => $value) {
        if ($key === 'action') {
            continue;
        }
   
        $key = mysqli_real_escape_string($conn, $key);
        $value = mysqli_real_escape_string($conn, $value);

        $stmt = $conn->prepare("SELECT COUNT(*) FROM user_settings WHERE key_name = ? AND owner_id = ?");
        $stmt->bind_param('ss', $key, $userID);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $stmt = $conn->prepare("UPDATE user_settings SET value = ? WHERE key_name = ? AND owner_id = ?");
            $stmt->bind_param('sss', $value, $key, $userID);
        } else {
            $stmt = $conn->prepare("INSERT INTO user_settings (key_name, value, owner_id) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $key, $value, $userID);
        }

        if ($stmt->execute()) {
            $response["success"] = 'Settings updated';
        } else {
            $response["error"] = 'An error occurred: ' . $stmt->error;
        }
    }
    echo json_encode($response);
    return;
}

//UPDATE API SETTINGS
if ($_POST['manage'] === 'api') {
    $api = $_POST['api'] === 'true' ? '1' : '0';
    
    // Check if API_key is empty
    $stmt = $conn->prepare("SELECT API_key FROM users WHERE id = ?");
    $stmt->bind_param('s', $userID);
    $stmt->execute();
    $stmt->bind_result($apiKey);
    $stmt->fetch();
    $stmt->close();

    if (empty($apiKey)) {
        // Generate a unique 32-character string
        $newApiKey = bin2hex(random_bytes(16));
        $stmt = $conn->prepare("UPDATE users SET API_key = ? WHERE id = ?");
        $stmt->bind_param('ss', $newApiKey, $userID);
        $stmt->execute();
        $stmt->close();
        $apiKey = $newApiKey;
    }

    $stmt = $conn->prepare("UPDATE users SET isAPIActive = ? WHERE id = ?");
    $stmt->bind_param('ss', $api, $userID);

    if ($stmt->execute()) {
        $response['success'] = 'API settings updated';
        $response['API_key'] = $apiKey;
    } else {
        $response['error'] = 'An error occurred: ' . $stmt->error;
    }

    $stmt->close();
    echo json_encode($response);
    return;
}

//GENERATE API KEY
if (!empty($_POST['regenerate-api-key']) && $_POST['regenerate-api-key'] === 'true') {
    do {
        $newApiKey = bin2hex(random_bytes(16)); // Generates a 32-character random string
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE API_key = ?");
        
        if (!$stmt) {
            error_log("PV error: Failed to prepare statement for uniqueness check - " . $conn->error);
            echo json_encode(['error' => 'Failed to regenerate API key']);
            return;
        }

        $stmt->bind_param('s', $newApiKey);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } while ($count > 0); // Regenerate if the key is not unique

    $stmt = $conn->prepare("UPDATE users SET API_key = ? WHERE id = ?");
    if (!$stmt) {
        error_log("PV error: Failed to prepare statement for updating API key - " . $conn->error);
        echo json_encode(['error' => 'Failed to regenerate API key']);
        return;
    }

    $stmt->bind_param('ss', $newApiKey, $userID);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => 'API key regenerated successfully', 'API_key' => $newApiKey]);
    } else {
        error_log("PV error: Failed to regenerate API key - " . $stmt->error);
        echo json_encode(['error' => 'Failed to regenerate API key']);
    }

    $stmt->close();
    return;
}


//BRANDING
if($_POST['action'] == 'branding'){
    $brandName = mysqli_real_escape_string($conn, $_POST['brandName']);
    $brandAddress = mysqli_real_escape_string($conn, $_POST['brandAddress']);
    $brandEmail = mysqli_real_escape_string($conn, $_POST['brandEmail']);
    $brandPhone = mysqli_real_escape_string($conn, $_POST['brandPhone']);

    // Check if branding information already exists
    $result = mysqli_query($conn, "SELECT * FROM branding WHERE owner_id = '$userID'");
    if(mysqli_num_rows($result) > 0){
        // Update existing branding information
        $query = "UPDATE branding SET brandName = '$brandName', brandAddress = '$brandAddress', brandEmail = '$brandEmail', brandPhone = '$brandPhone' WHERE owner_id = '$userID'";
    } else {
        // Insert new branding information
        $query = "INSERT INTO branding (brandName, brandAddress, brandEmail, brandPhone, owner_id) VALUES ('$brandName', '$brandAddress', '$brandEmail', '$brandPhone', '$userID')";
    }

    if(mysqli_query($conn, $query)){
        $response['success'] = 'Brand details updated';
    }else{
        $response['error'] = 'Error updating brand info';
    }
    echo json_encode($response);
    return;
}

//ADD CATEGORY
if ($_POST['manage'] == 'category') {
    // Sanitize user inputs
    $cat = mysqli_real_escape_string($conn, $_POST['category']);
    $notes = mysqli_real_escape_string($conn, $_POST['cat_notes']);
    
    // Check if category name is empty
    if (empty($cat)) {
        $response["error"] = 'Category name is required';
        echo json_encode($response);
        return;
    }

    // Check if category already exists using prepared statements
    $stmt = $conn->prepare("SELECT name FROM ingCategory WHERE name = ? AND owner_id = ?");
    $stmt->bind_param('ss', $cat, $userID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response["error"] = 'Category "' . $cat . '" already exists';
        echo json_encode($response);
        $stmt->close();
        return;
    }

    // Insert the new category using prepared statements
    $stmt = $conn->prepare("INSERT INTO ingCategory (name, notes, owner_id) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $cat, $notes, $userID);

    if ($stmt->execute()) {
        $response["success"] = 'Category "' . $cat . '" added successfully';
        echo json_encode($response);
    } else {
        // Log the error for debugging
        error_log("Error inserting category: " . $stmt->error);
        $response["error"] = 'Something went wrong, please try again later';
        echo json_encode($response);
    }

    $stmt->close();
    return;
}

//DELETE CATEGORY
if ($_POST['action'] == 'delete' && isset($_POST['catId'])) {
    $catId = mysqli_real_escape_string($conn, $_POST['catId']);
    $stmt = $conn->prepare("DELETE FROM ingCategory WHERE id = ? AND owner_id = ?");
    $stmt->bind_param('is', $catId, $userID);

    if ($stmt->execute()) {
        $response["success"] = 'Category deleted successfully';
        echo json_encode($response);
    } else {
        error_log("Error deleting category with ID $catId: " . $stmt->error);
        $response["error"] = 'Error deleting category, please try again later';
        echo json_encode($response);
    }
    $stmt->close();
    return;
}

//ADD INGREDIENT PROFILE
//TODO - ADMIN SIDE ONLY
/*
if ($_POST['action'] == 'add_ingprof') {
    $profile = mysqli_real_escape_string($conn, $_POST['profile']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    if (empty($profile)) {
        $response["error"] = 'Profile name is required';
        echo json_encode($response);
        return;
    }

    $stmt = $conn->prepare("SELECT name FROM ingProfiles WHERE name = ? AND owner_id = ?");
    $stmt->bind_param('si', $profile, $userID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response["error"] = 'Profile "' . $profile . '" already exists';
        echo json_encode($response);
        $stmt->close();
        return;
    }

    // Insert the new profile using prepared statements
    $stmt = $conn->prepare("INSERT INTO ingProfiles (name, notes, owner_id) VALUES (?, ?, ?)");
    $stmt->bind_param('ssi', $profile, $description, $userID);

    if ($stmt->execute()) {
        $response["success"] = 'Profile "' . $profile . '" added successfully';
        echo json_encode($response);
    } else {
        // Log the detailed error for debugging purposes
        error_log("Error inserting profile: " . $stmt->error);
        $response["error"] = 'Something went wrong, please try again later';
        echo json_encode($response);
    }

    $stmt->close();
    return;
}
*/				

//DELETE INGREDIENT PROFILE
//TODO - ADMIN SIDE ONLY
/*
if ($_POST['action'] == 'delete_ingprof' && isset($_POST['profId'])) {
    $profId = mysqli_real_escape_string($conn, $_POST['profId']);
    
    $stmt = $conn->prepare("DELETE FROM ingProfiles WHERE id = ? AND owner_id = ?");
    $stmt->bind_param('ii', $profId, $userID);

    if ($stmt->execute()) {
        $response["success"] = 'Profile deleted successfully';
        echo json_encode($response);
    } else {
        // Log the error for debugging
        error_log("Error deleting profile with ID $profId: " . $stmt->error);
        $response["error"] = 'Something went wrong, please try again later';
        echo json_encode($response);
    }

    $stmt->close();
    return;
}
*/

//ADD FORMULA CATEGORY
if ($_POST['manage'] == 'add_frmcategory') {
    $cat = mysqli_real_escape_string($conn, $_POST['category']);
    $type = mysqli_real_escape_string($conn, $_POST['cat_type']);
    if (empty($cat)) {
        $response["error"] = 'Category name is required';
        echo json_encode($response);
        return;
    }

    $stmt = $conn->prepare("SELECT name FROM formulaCategories WHERE name = ? AND owner_id = ?");
    $stmt->bind_param('ss', $cat, $userID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response["error"] = 'Category "' . $cat . '" already exists';
        echo json_encode($response);
        $stmt->close();
        return;
    }

    $stmt = $conn->prepare("INSERT INTO formulaCategories (name, cname, type, owner_id) VALUES (?, ?, ?, ?)");
    $cname = strtolower(str_replace(' ', '', $cat));
    $stmt->bind_param('ssss', $cat, $cname, $type, $userID);

    if ($stmt->execute()) {
        $response["success"] = 'Category "' . $cat . '" created successfully';
        echo json_encode($response);
    } else {
        // Log the error for debugging
        error_log("Error inserting category: " . $stmt->error);
        $response["error"] = 'Something went wrong, please try again later';
        echo json_encode($response);
    }

    $stmt->close();
    return;
}
				

//DELETE FORMULA CATEGORY
if ($_POST['action'] == 'del_frmcategory' && isset($_POST['catId'])) {
    $catId = mysqli_real_escape_string($conn, $_POST['catId']);
    $stmt = $conn->prepare("DELETE FROM formulaCategories WHERE id = ? AND owner_id = ?");
    $stmt->bind_param('is', $catId, $userID);

    if ($stmt->execute()) {
        $response["success"] = 'Category deleted successfully';
        echo json_encode($response);
    } else {
        // Log the error for debugging purposes
        error_log("Error deleting category with ID $catId: " . $stmt->error);
        $response["error"] = 'Something went wrong, please try again later';
        echo json_encode($response);
    }

    $stmt->close();
    return;
}


//DELETE BATCH
if ($_POST['action'] == 'batch' && isset($_POST['bid']) && isset($_POST['remove']) && $_POST['remove'] == 'true') {
    $id = mysqli_real_escape_string($conn, $_POST['bid']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    $stmt = $conn->prepare("DELETE FROM batchIDHistory WHERE id = ? AND owner_id = ?");
    $stmt->bind_param('is', $id, $userID); 

    if ($stmt->execute()) {
        $response["success"] = 'Batch ' . $id . ' for product ' . $name . ' deleted successfully';
        echo json_encode($response);
    } else {
        // Log the error for debugging
        error_log("Error deleting batch with ID $id for product $name: " . $stmt->error);
        $response["error"] = 'Something went wrong, please try again later';
        echo json_encode($response);
    }

    $stmt->close();
    return;
}


//UPDATE SDS DISCLAIMER
if ($_POST['action'] == 'sdsDisclaimerContent') {
    $sds_disc_content = mysqli_real_escape_string($conn, $_POST['sds_disc_content']);

    if (empty($sds_disc_content)) {
        $response["error"] = 'Disclaimer text is required.';
        echo json_encode($response);
        return;
    }

    // Check if the disclaimer already exists
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM sdsSettings WHERE owner_id = '$userID'");
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] > 0) {
        // Update existing disclaimer
        $query = "UPDATE sdsSettings SET sds_disclaimer = '$sds_disc_content' WHERE owner_id = '$userID'";
    } else {
        // Insert new disclaimer
        $query = "INSERT INTO sdsSettings (sds_disclaimer, owner_id) VALUES ('$sds_disc_content', '$userID')";
    }

    if (mysqli_query($conn, $query)) {
        $response["success"] = 'SDS Disclaimer text updated';
    } else {
        $response["error"] = 'Error ' . mysqli_error($conn);
    }

    echo json_encode($response);
    return;
}


//DELETE SDS
if ($_POST['action'] == 'delete' && isset($_POST['SDSID']) && $_POST['type'] == 'SDS') {
    $id = mysqli_real_escape_string($conn, $_POST['SDSID']);
    mysqli_begin_transaction($conn);

    try {
        $stmt1 = $conn->prepare("DELETE FROM documents WHERE ownerID = ? AND isSDS = '1' AND owner_id = ?");
        $stmt1->bind_param('is', $id, $userID);

        if ($stmt1->execute()) {
            $stmt2 = $conn->prepare("DELETE FROM sds_data WHERE id = ? AND owner_id = ?");
            $stmt2->bind_param('is', $id, $userID);

            if ($stmt2->execute()) {
                mysqli_commit($conn);
                $response["success"] = 'SDS deleted successfully';
            } else {
                mysqli_rollback($conn);
                $response["error"] = 'Failed to delete from sds_data';
            }
            $stmt2->close();
        } else {
            mysqli_rollback($conn);
            $response["error"] = 'Failed to delete from documents';
        }

        $stmt1->close();
    } catch (Exception $e) {
        // Rollback transaction if any error occurs
        mysqli_rollback($conn);
        $response["error"] = 'Something went wrong, please try again later';
        error_log("Error deleting SDS with ID $id: " . $e->getMessage());  // Log error for debugging
    }

    echo json_encode($response);
    return;
}


//ADD INVENTORY COMPOUND
if ($_POST['action'] == 'add' && $_POST['type'] == 'invCmp') {
    // Validate required inputs
    if (empty($_POST['cmp_name'])) {
        echo json_encode(["error" => "Name is required."]);
        return;
    }
    if (!is_numeric($_POST['cmp_size']) || $_POST['cmp_size'] <= 0) {
        echo json_encode(["error" => "Size must be a positive numeric value."]);
        return;
    }

    // Sanitize inputs
    $name = trim($_POST['cmp_name']);
    $size = floatval($_POST['cmp_size']);
    $batch_id = mysqli_real_escape_string($conn, $_POST['cmp_batch']);
    $location = mysqli_real_escape_string($conn, $_POST['cmp_location']);
    $description = mysqli_real_escape_string($conn, $_POST['cmp_desc']);
    $label_info = mysqli_real_escape_string($conn, $_POST['cmp_label_info']);

    // Use prepared statements to prevent SQL injection
    try {
        // Check if compound already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM inventory_compounds WHERE name = ? AND owner_id = ?");
        $stmt->bind_param('ss', $name, $userID);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo json_encode(["error" => "$name already exists."]);
            return;
        }

        // Insert new compound
        $stmt = $conn->prepare(
            "INSERT INTO inventory_compounds (name, description, batch_id, size, owner_id, location, label_info)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('sssdsss', $name, $description, $batch_id, $size, $userID, $location, $label_info);

        if ($stmt->execute()) {
            echo json_encode(["success" => "Compound $name added successfully."]);
        } else {
            throw new Exception("Failed to add compound: " . $stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        // Log the error and return a generic error message
        error_log("Error adding compound: " . $e->getMessage());
        echo json_encode(["error" => "Something went wrong. Please try again later."]);
    }
    return;
}



//UPDATE COMPOUND DATA
if ($_POST['action'] == 'update_inv_compound_data') {
    // Validate required fields
    if (empty($_POST['name'])) {
        echo json_encode(["error" => "Name is required"]);
        return;
    }
    if (empty($_POST['cmp_id']) || !is_numeric($_POST['cmp_id'])) {
        echo json_encode(["error" => "Valid compound ID is required"]);
        return;
    }
    if (!empty($_POST['size']) && (!is_numeric($_POST['size']) || $_POST['size'] <= 0)) {
        echo json_encode(["error" => "Size must be a positive number"]);
        return;
    }

    // Sanitize inputs
    $id = intval($_POST['cmp_id']);
    $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $description = trim(mysqli_real_escape_string($conn, $_POST['description']));
    $batch_id = trim(mysqli_real_escape_string($conn, $_POST['batch_id']));
    $size = empty($_POST['size']) ? null : floatval($_POST['size']);
    $location = trim(mysqli_real_escape_string($conn, $_POST['location'] ?: 'Unknown'));
    $label_info = trim(mysqli_real_escape_string($conn, $_POST['label_info'] ?: 'N/A'));

    // Update query with prepared statement
    try {
        $stmt = $conn->prepare(
            "UPDATE inventory_compounds SET name = ?, description = ?, batch_id = ?, size = ?, location = ?, label_info = ? WHERE id = ? AND owner_id = ?"
        );
        $stmt->bind_param('sssdssis', $name, $description, $batch_id, $size, $location, $label_info, $id, $userID);

        if ($stmt->execute()) {
            echo json_encode(["success" => "Compound updated successfully"]);
        } else {
            throw new Exception("Failed to update compound: " . $stmt->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        // Log error for debugging purposes
        error_log("Error updating compound: " . $e->getMessage());
        echo json_encode(["error" => "Something went wrong. Please try again later."]);
    }

    return;
}


//DELETE COMPOUND
if ($_POST['action'] === 'delete' && !empty($_POST['compoundId']) && $_POST['type'] === 'invCmp') {
    // Validate input
    $id = filter_var($_POST['compoundId'], FILTER_SANITIZE_NUMBER_INT);
    
    if (!$id) {
        $response["error"] = 'Invalid compound ID';
        echo json_encode($response);
        return;
    }

    // Use prepared statement to delete the record
    $stmt = $conn->prepare("DELETE FROM inventory_compounds WHERE id = ? AND owner_id = ?");
    $stmt->bind_param("is", $id, $userID);

    if ($stmt->execute()) {
        $response["success"] = 'Compound deleted successfully.';
    } else {
        $response["error"] = 'Failed to delete compound: ' . htmlspecialchars($stmt->error);
    }

    $stmt->close();
    $response["error"] = 'Invalid request.';
	echo json_encode($response);
	return;
}





//WIPE OUT FROMULAS
if($_POST['formulas_wipe'] == 'true'){
	
	if(mysqli_query($conn, "DELETE FROM formulas WHERE owner_id = '$userID'")){
		mysqli_query($conn, "DELETE FROM formulasMetaData WHERE owner_id = '$userID'");
		mysqli_query($conn, "DELETE FROM formula_history WHERE owner_id = '$userID'");
		mysqli_query($conn, "DELETE FROM formulasTags WHERE owner_id = '$userID'");
		mysqli_query($conn, "DELETE FROM ingredient_compounds WHERE owner_id = '$userID'");
		mysqli_query($conn, "DELETE FROM formulaCategories WHERE owner_id = '$userID'");
		mysqli_query($conn, "DELETE FROM formulasRevisions WHERE owner_id = '$userID'");
		mysqli_query($conn, "DELETE FROM makeFormula WHERE owner_id = '$userID'");

		$response["success"] = 'Formulas and related data deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//WIPE OUT INGREDIENTS
if($_POST['ingredient_wipe'] == 'true'){
	
	if(mysqli_query($conn, "DELETE FROM ingredients  WHERE owner_id = '$userID'")){
		mysqli_query($conn, "DELETE FROM ingCategory WHERE owner_id = '$userID'");
		//mysqli_query($conn, "DELETE FROM ingProfiles WHERE owner_id = '$userID'");
		mysqli_query($conn, "DELETE FROM ingReplacements WHERE owner_id = '$userID'");
		mysqli_query($conn, "DELETE FROM ingSafetyInfo WHERE owner_id = '$userID'");
		mysqli_query($conn, "DELETE FROM suppliers WHERE owner_id = '$userID'");
		mysqli_query($conn, "DELETE FROM synonyms WHERE owner_id = '$userID'");

		$response["success"] = 'Ingredients and related data deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}


//UPDATE CAS IFRA ENTRY
if($_POST['action'] == 'editIFRA'){
    $type = $_REQUEST['type'];
    $value = $_REQUEST['value'];
    $pk = $_REQUEST['pk'];

    $stmt = $conn->prepare("UPDATE IFRALibrary SET $type = ? WHERE owner_id = ? AND id = ?");
    $stmt->bind_param('ssi', $value, $userID, $pk);

    if($stmt->execute()){
        $response["success"] = 'IFRA entry updated';
    }else{
        $response["error"] = 'Something went wrong '. $stmt->error;
    }

    $stmt->close();
    echo json_encode($response);
    return;	
}

//DELETE IFRA ENTRY
if($_POST['IFRA'] == 'delete' && $_POST['ID'] && $_POST['type'] == 'IFRA'){
    
    if(mysqli_query($conn, "DELETE FROM IFRALibrary WHERE owner_id = '$userID' AND id = '".$_POST['ID']."'")){
        $response["success"] = 'IFRA entry deleted';
    }else{
        $response["error"] = 'Something went wrong '.mysqli_error($conn);
    }
    
    echo json_encode($response);
    return;	
}

//Merge ingredients
if($_POST['merge'] && $_POST['ingSrcID'] &&  $_POST['ingSrcName']  && $_POST['fid']){
	if(!$_POST['dest']){
		$response['error'] = 'Please select ingedient';
		echo json_encode($response);
    	return;
	}
	
	$dest = mysqli_fetch_array(mysqli_query($conn,"SELECT ingredient FROM formulas WHERE owner_id = '$userID' AND id = '".$_POST['dest']."'"));
	
	if($dest['ingredient'] == $_POST['ingSrcName']){
		$response['error'] = 'Source and destination ingredients cannot be the same';
		echo json_encode($response);
    	return;
	}
	
	$mq = "UPDATE formulas SET quantity = quantity + (SELECT quantity FROM formulas WHERE owner_id = '$userID' AND id ='".$_POST['ingSrcID']."' AND fid = '".$_POST['fid']."') WHERE id = '".$_POST['dest']."' AND fid = '".$_POST['fid']."'";
	
	if(mysqli_query($conn,$mq)){
		mysqli_query($conn,"DELETE FROM formulas WHERE owner_id = '$userID' AND id = '".$_POST['ingSrcID']."' AND fid = '".$_POST['fid']."'");
		$response['success'] = $_POST['ingSrcName'].' merged with '.$dest['ingredient'];
	}else{
		$response['error'] = 'Something went wrong, '.mysqli_error($conn);
	}
	
	echo json_encode($response);
    return;

}

// EMBED INGREDIENT
if ($_POST['action'] === 'embedIng') {
    $fid = $conn->real_escape_string($_POST['fid']);
    $ingID = $conn->real_escape_string($_POST['ingID']);
    $ingName = $conn->real_escape_string($_POST['ingName']);

    // Fetch sub-ingredients of the selected ingredient
    error_log("PV error: Fetching sub-ingredients for '$ingName' under user '$userID'");
    
    $subIngQuery = $conn->prepare("SELECT name, min_percentage FROM ingredient_compounds WHERE ing = ? AND owner_id = ?");
    $subIngQuery->bind_param("ss", $ingName, $userID);
    $subIngQuery->execute();
    $subIngResult = $subIngQuery->get_result();

    // Fetch the formula details
    $formulaQuery = $conn->prepare("SELECT name FROM formulasMetaData WHERE fid = ? AND owner_id = ?");
    $formulaQuery->bind_param("ss", $fid, $userID);
    $formulaQuery->execute();
    $formulaResult = $formulaQuery->get_result();
    $formulaData = $formulaResult->fetch_assoc();
    $formulaQuery->close();

    if (!$formulaData) {
        error_log("PV error: Formula with fid '$fid' not found for user '$userID'");
        echo json_encode(['error' => 'Formula not found.']);
        return;
    }

    if ($subIngResult->num_rows > 0) {
        while ($row = $subIngResult->fetch_assoc()) {
            $subIngName = $row['name'];
            $quantity = $row['min_percentage'];

            error_log("PV error: Checking ingredient ID for '$subIngName' under user '$userID'");

            $subIngIDQuery = $conn->prepare("SELECT id FROM ingredients WHERE name = ? AND owner_id = ?");
            $subIngIDQuery->bind_param("ss", $subIngName, $userID);
            $subIngIDQuery->execute();
            $subIngIDResult = $subIngIDQuery->get_result();
            $subIngIDData = $subIngIDResult->fetch_assoc();
            $subIngIDQuery->close();

            if (!$subIngIDData) {
                error_log("PV error: Ingredient '$subIngName' not found in database for user '$userID'");
                continue; // Skip this ingredient if not found
            }

            $subIngID = $subIngIDData['id'];

            // **Check if ingredient already exists in the user's formula**
            $checkQuery = $conn->prepare("SELECT 1 FROM formulas WHERE fid = ? AND ingredient_id = ? AND owner_id = ?");
            $checkQuery->bind_param("sss", $fid, $subIngID, $userID);
            $checkQuery->execute();
            $checkResult = $checkQuery->get_result();
            $ingredientExists = $checkResult->num_rows > 0;
            $checkQuery->close();

            if ($ingredientExists) {
                error_log("PV error: Ingredient '$subIngName' already exists in formula '$fid' for user '$userID' - Skipping insert");
                continue; // **Skip inserting duplicate ingredient**
            }

            error_log("PV error: Inserting ingredient: fid='$fid', formula='{$formulaData['name']}', ingredient='$subIngName', ingredient_id='$subIngID', quantity='$quantity', owner_id='$userID'");

            // Insert new ingredient only if it does not exist
            $insertQuery = $conn->prepare(
                "INSERT INTO formulas (fid, name, ingredient, ingredient_id, quantity, owner_id)
                 VALUES (?, ?, ?, ?, ?, ?)"
            ); 
            $insertQuery->bind_param("sssids", $fid, $formulaData['name'], $subIngName, $subIngID, $quantity, $userID);
            $insertQuery->execute();
            $insertQuery->close();
        }

        // DELETE Statement
        error_log("PV error: Deleting id='$ingID' from formulas for fid='$fid' and user '$userID'");
        $deleteQuery = $conn->prepare("DELETE FROM formulas WHERE fid = ? AND id = ? AND owner_id = ?");
        $deleteQuery->bind_param("sss", $fid, $ingID, $userID);
        $deleteQuery->execute();
        $deleteQuery->close();

        echo json_encode(['success' => 'Ingredient embedded successfully.']);
    } else {
        error_log("PV error: No sub-ingredients found for '$ingName' under user '$userID'");
        echo json_encode(['error' => 'No sub-ingredients found for the selected ingredient.']);
    }

    $subIngQuery->close();
    return;
}




//PVLibrary Single Import						
if ($_POST['action'] === 'import' && $_POST['source'] === 'PVLibrary' && $_POST['kind'] === 'ingredient' && !empty($_POST['ing_id'])) {
    // Sanitize input
    $id = filter_var($_POST['ing_id'], FILTER_SANITIZE_NUMBER_INT);

    if (!$id) {
        $response['error'] = 'Invalid ingredient ID.';
        echo json_encode($response);
        return;
    }

    // Fetch data from API
    $jAPI = $pvLibraryAPI . '?request=ingredients&src=PV_PRO&id=' . $id;
    $jsonData = json_decode(pv_file_get_contents($jAPI), true);

    if (!empty($jsonData['error'])) {
        $response['error'] = 'Error: ' . $jsonData['error']['msg'];
        echo json_encode($response);
        return;
    }

    $array_data = $jsonData['ingredients'];

    foreach ($array_data as $id => $row) {
        // Remove unwanted keys
        unset($row['structure'], $row['techData'], $row['ifra'], $row['IUPAC'], $row['id']);

        // Prepare keys and values for insertion
        $insertPairs = [];
        foreach ($row as $key => $val) {
            $insertPairs[$key] = $val;
        }

        // Check if the ingredient already exists
        $stmtCheck = $conn->prepare("SELECT name FROM ingredients WHERE owner_id = ? AND name = ?");
        $stmtCheck->bind_param("ss", $userID, $insertPairs['name']);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();

        if ($result->num_rows > 0) {
            $response['error'] = 'Ingredient ' . $insertPairs['name'] . ' already exists.';
            echo json_encode($response);
            return;
        }

        // Dynamically prepare the INSERT query
        $columns = '`' . implode('`,`', array_keys($insertPairs)) . '`';
        $placeholders = implode(',', array_fill(0, count($insertPairs), '?'));
        $values = array_values($insertPairs);

        $stmtInsert = $conn->prepare("INSERT INTO ingredients ({$columns}, `owner_id`) VALUES ({$placeholders}, ?)");

        $types = str_repeat('s', count($values)) . 's';
        $params = array_merge([$types], $values, [$userID]);

        call_user_func_array([$stmtInsert, 'bind_param'], $params);

        if ($stmtInsert->execute()) {
            $response["success"] = 'Ingredient data imported successfully.';
        } else {
            $response["error"] = 'Error inserting ingredient: ' . htmlspecialchars($stmtInsert->error);
        }

        $stmtInsert->close();
    }

    echo json_encode($response);
    return;
}


//UPDATE HTML TEMPLATE
if ($_REQUEST['action'] === 'htmlTmplUpdate') {
    // Sanitize input
    $value = ($_POST['value']);
    $id = filter_var($_POST['pk'], FILTER_SANITIZE_NUMBER_INT);
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);

    // Validate inputs
    if (!$value || !$id || !$name) {
        $response["error"] = 'Invalid input.';
        echo json_encode($response);
        return;
    }

    // Use a prepared statement to update the database
    $stmt = $conn->prepare("UPDATE templates SET $name = ? WHERE owner_id = ? AND id = ?");
    $stmt->bind_param("ssi", $value, $userID, $id);

    if ($stmt->execute()) {
        $response["success"] = 'Template updated successfully.';
    } else {
        $response["error"] = 'Error updating template: ' . htmlspecialchars($stmt->error);
    }

    $stmt->close();
	echo json_encode($response);
	return;
}

//DELETE HTML TEMPLATE
if ($_POST['action'] === 'htmlTmplDelete' && isset($_POST['tmplID']) && isset($_POST['tmplName'])) {
    $id = filter_var($_POST['tmplID'], FILTER_SANITIZE_NUMBER_INT);
    $name = filter_var($_POST['tmplName'], FILTER_SANITIZE_STRING);

    if (empty($id) || empty($name) || !is_numeric($id)) {
        $response["error"] = 'Invalid input data.';
        echo json_encode($response);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM templates WHERE owner_id = ? AND id = ?");
    $stmt->bind_param("si", $userID, $id);

    if ($stmt->execute()) {
        $response["success"] = 'Template ' . htmlspecialchars($name) . ' deleted successfully.';
    } else {
        $response["error"] = 'Error: ' . $stmt->error;
    }

    $stmt->close();
	echo json_encode($response);
	return;
}

//ADD NEW TEMPLATE
if($_POST['action'] == 'htmlTmplAdd'){
	
	if(empty($_POST['tmpl_name'])){
		$response["error"] = 'Name is required';
		echo json_encode($response);
		return;
	}
	
	if(empty($_POST['tmpl_content'])){
		$response["error"] = 'HTML Content is required';
		echo json_encode($response);
		return;
	}

	if(empty($_POST['tmpl_desc'])){
		$response["error"] = 'Description is required.';
		echo json_encode($response);
		return;
	}
	
	$name = mysqli_real_escape_string($conn,$_POST['tmpl_name']);
	$html = mysqli_real_escape_string($conn,$_POST['tmpl_content']);
	$desc = mysqli_real_escape_string($conn,$_POST['tmpl_desc']);

	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM templates WHERE name = '$name' AND owner_id = '$userID' "))){
		$response["error"] = $name.' already exists';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn, "INSERT INTO templates (name,content,description,owner_id) VALUES ('$name','$html','$desc','$userID')")){
		$response["success"] = $name.' created';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}



//UPDATE PERFUME TYPES
if($_GET['perfType'] == 'update'){
	$value = trim(mysqli_real_escape_string($conn, $_POST['value']));
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE perfumeTypes SET $name = '$value' WHERE id = '$id' AND owner_id = '$userID'")){
		$response["success"] = 'Perfume type updated';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
		error_log("PV error: " . $stmt->error);
	}
	
	echo json_encode($response);
	return;
}

//DELETE PERFUME TYPE
if ($_POST['perfType'] === 'delete' && isset($_POST['pID']) && isset($_POST['pName'])) {
    $id = filter_var($_POST['pID'], FILTER_SANITIZE_NUMBER_INT);
    $name = filter_var($_POST['pName'], FILTER_SANITIZE_STRING);

    if (empty($id) || empty($name) || !is_numeric($id)) {
        $response["error"] = 'Invalid input data.';
        echo json_encode($response);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM perfumeTypes WHERE id = ? AND owner_id = ?");
    $stmt->bind_param("is", $id, $userID);

    if ($stmt->execute()) {
        $response["success"] = 'Perfume type ' . htmlspecialchars($name) . ' deleted successfully.';
    } else {
        $response["error"] = 'Something went wrong. Please try again.';
		error_log("PV error: " . $stmt->error);
    }

    $stmt->close();
    echo json_encode($response);
    return;
}


//ADD PERFUME TYPE
if ($_POST['action'] === 'perfTypeAdd') {
    if (empty($_POST['perfType_name'])) {
        $response["error"] = 'Name is required';
        echo json_encode($response);
        return;
    }

    if (empty($_POST['perfType_conc'])) {
        $response["error"] = 'Concentration is required';
        echo json_encode($response);
        return;
    }

    if (!is_numeric($_POST['perfType_conc'])) {
        $response["error"] = 'Concentration must be a number';
        echo json_encode($response);
        return;
    }

    $name = trim(filter_var($_POST['perfType_name'], FILTER_SANITIZE_STRING));
    $conc = trim($_POST['perfType_conc']);
    $desc = isset($_POST['perfType_desc']) ? trim(filter_var($_POST['perfType_desc'], FILTER_SANITIZE_STRING)) : '';

    $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM perfumeTypes WHERE name = ? AND owner_id = ?");
    $stmtCheck->bind_param("ss", $name, $userID);
    $stmtCheck->execute();
    $stmtCheck->bind_result($count);
    $stmtCheck->fetch();
    $stmtCheck->close();

    if ($count > 0) {
        $response["error"] = $name . ' already exists, try a diffrent name';
        echo json_encode($response);
        return;
    }

    // Insert the new perfume type
    $stmtInsert = $conn->prepare("INSERT INTO perfumeTypes (name, concentration, description, owner_id) VALUES (?, ?, ?, ?)");
    if (!$stmtInsert) {
        $response["error"] = 'Failed to prepare the query.';
        error_log("PV error: " . $conn->error);
        echo json_encode($response);
        return;
    }

    $stmtInsert->bind_param("ssss", $name, $conc, $desc, $userID);

    if ($stmtInsert->execute()) {
        $response["success"] = $name . ' added';
    } else {
        $response["error"] = 'Error adding the perfume type.';
        error_log("PV error: " . $stmtInsert->error);
    }

    $stmtInsert->close();
    echo json_encode($response);
    return;
}


//UPDATE ACCESSORY PIC
if ($_GET['action'] === 'update_accessory_pic') {
    $allowed_ext = "png, jpg, jpeg, gif, bmp";
    $allowed_ext_array = explode(', ', strtolower($allowed_ext));

    $file_tmp = $_FILES['accessory_pic']['tmp_name'];
    $file_name = $_FILES['accessory_pic']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!$file_tmp) {
        $response["error"] = "No file uploaded";
        echo json_encode($response);
        return;
    }

    // Ensure temp directory exists
    if (!file_exists($tmp_path)) {
        if (!mkdir($tmp_path, 0740, true) && !is_dir($tmp_path)) {
            error_log("PV error: Failed to create directory at $tmp_path");
            $response["error"] = "Server error. Unable to create directory.";
            echo json_encode($response);
            return;
        }
    }

    // Validate file extension
    if (!in_array($file_ext, $allowed_ext_array, true)) {
        $response["error"] = 'Extension not allowed, please choose a ' . $allowed_ext . ' file.';
        echo json_encode($response);
        return;
    }

    $accessory = intval($_GET['accessory_id']);

    if ($_FILES['accessory_pic']['size'] > 0) {
        $encoded_filename = base64_encode($file_name);
        $upload_path = $tmp_path . $encoded_filename;

        if (!move_uploaded_file($file_tmp, $upload_path)) {
            error_log("PV error: Failed to move uploaded file to $upload_path");
            $response["error"] = "Failed to upload file.";
            echo json_encode($response);
            return;
        }

        create_thumb($upload_path, 250, 250);

        $docData = 'data:image/' . $file_ext . ';base64,' . base64_encode(file_get_contents($upload_path));

        // Delete previous document entries
        $stmtDelete = $conn->prepare("DELETE FROM documents WHERE ownerID = ? AND type = '5' AND owner_id = ?");
        $stmtDelete->bind_param("is", $accessory, $userID);
        if (!$stmtDelete->execute()) {
            error_log("PV error: Failed to delete old document. " . $stmtDelete->error);
            $response["error"] = "Failed to update photo. Please try again.";
            echo json_encode($response);
            return;
        }
        $stmtDelete->close();

        // Insert the new document
        $stmtInsert = $conn->prepare("INSERT INTO documents (ownerID, name, type, notes, docData, owner_id) VALUES (?, '-', '5', '-', ?, ?)");
        $stmtInsert->bind_param("iss", $accessory, $docData, $userID);

        if ($stmtInsert->execute()) {
            unlink($upload_path); // Clean up temporary file
            $response["success"] = [
                "msg" => "Photo updated",
                "accessory_pic" => $docData
            ];
            echo json_encode($response);
            return;
        } else {
            error_log("PV error: Failed to insert new document. " . $stmtInsert->error);
            $response["error"] = "Failed to save photo.";
            echo json_encode($response);
            return;
        }
    }

    $response["error"] = "No file to process.";
    echo json_encode($response);
    return;
}


//UPDATE BOTTLE PIC
if ($_GET['update_bottle_pic']) {
    $allowed_ext = "png, jpg, jpeg, gif, bmp";
    $bottle_id = $_GET['bottle_id'];

    if (!$bottle_id || !is_numeric($bottle_id)) {
        error_log("PV error: Invalid or missing bottle ID.");
        $response["error"] = "Invalid bottle ID.";
        echo json_encode($response);
        return;
    }

    if (!isset($_FILES['bottle_pic']) || $_FILES['bottle_pic']['size'] === 0) {
        $response["error"] = "Please choose a file to upload.";
        echo json_encode($response);
        return;
    }

    $file_name = $_FILES['bottle_pic']['name'];
    $file_size = $_FILES['bottle_pic']['size'];
    $file_tmp = $_FILES['bottle_pic']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext_array = explode(', ', strtolower($allowed_ext));

    // Validate file extension
    if (!in_array($file_ext, $allowed_ext_array, true)) {
        $response["error"] = "Extension not allowed. Please choose a $allowed_ext file.";
        echo json_encode($response);
        return;
    }

    // Validate file size
    if ($file_size > $upload_max_filesize) {
        $response["error"] = "File size must not exceed " . ($upload_max_filesize / (1024 * 1024)) . " MB.";
        echo json_encode($response);
        return;
    }

    // Ensure temporary directory exists
    if (!file_exists($tmp_path) && !mkdir($tmp_path, 0740, true) && !is_dir($tmp_path)) {
        error_log("PV error: Failed to create temporary directory at $tmp_path");
        $response["error"] = "Server error. Unable to create directory.";
        echo json_encode($response);
        return;
    }

    // Encode file name
    $encoded_filename = base64_encode($file_name);
    $upload_path = $tmp_path . $encoded_filename;

    if (move_uploaded_file($file_tmp, $upload_path)) {
        // Create thumbnail
        create_thumb($upload_path, 250, 250);

        // Generate base64 encoded data
        $docData = 'data:image/' . $file_ext . ';base64,' . base64_encode(file_get_contents($upload_path));

        // Delete previous document entry
        $stmtDelete = $conn->prepare("DELETE FROM documents WHERE ownerID = ? AND type = '4' AND owner_id = ?");
        $stmtDelete->bind_param("is", $bottle_id, $userID);

        if (!$stmtDelete->execute()) {
            error_log("PV error: Failed to delete existing document for bottle ID $bottle_id. " . $stmtDelete->error);
        }
        $stmtDelete->close();

        // Insert new document entry
        $stmtInsert = $conn->prepare("INSERT INTO documents (ownerID, name, type, notes, docData, owner_id) VALUES (?, '-', '4', '-', ?, ?)");
        $stmtInsert->bind_param("iss", $bottle_id, $docData, $userID);

        if ($stmtInsert->execute()) {
            unlink($upload_path); // Clean up temporary file
            $response["success"] = ["msg" => "File uploaded", "file" => $docData];
        } else {
            error_log("PV error: Failed to insert document for bottle ID $bottle_id. " . $stmtInsert->error);
            $response["error"] = "Failed to upload file.";
        }
        $stmtInsert->close();
    } else {
        error_log("PV error: Failed to move uploaded file to $upload_path");
        $response["error"] = "Failed to upload file.";
    }

    echo json_encode($response);
    return;
}


//UPDATE BOTTLE DATA
if ($_POST['action'] === 'update_bottle_data') {

    // Validate required fields
    if (empty($_POST['name'])) {
        $response["error"] = "Name is required";
        echo json_encode($response);
        return;
    }

    if (!is_numeric($_POST['size']) || $_POST['size'] <= 0) {
        $response["error"] = "Size is invalid";
        echo json_encode($response);
        return;
    }

    if (!is_numeric($_POST['price']) || $_POST['price'] <= 0) {
        $response["error"] = "Price is invalid";
        echo json_encode($response);
        return;
    }

    // Retrieve and sanitize input data
    $id = $_POST['bottle_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $ml = $_POST['size'];
    $price = $_POST['price'];
    $height = $_POST['height'];
    $width = $_POST['width'];
    $diameter = $_POST['diameter'];
    $supplier = $_POST['supplier'];
    $supplier_link = $_POST['supplier_link'];
    $notes = $_POST['notes'];
    $pieces = $_POST['pieces'] ?: 0;
    $weight = $_POST['weight'] ?: 0;

    $stmt = $conn->prepare("UPDATE bottles 
                            SET name = ?, ml = ?, price = ?, height = ?, width = ?, diameter = ?, 
                                supplier = ?, supplier_link = ?, notes = ?, pieces = ?, weight = ? 
                            WHERE id = ? AND owner_id = ?");
    
	$stmt->bind_param("sdddddsssidis", $name, $ml, $price, $height, $width, $diameter, 
	$supplier, $supplier_link, $notes, $pieces, $weight, $id, $userID);

    if ($stmt->execute()) {
        $response['success'] = "Bottle updated";
    } else {
        $response['error'] = "Error updating bottle data: " . $stmt->error;
        error_log("PV error: " . $stmt->error);  // Log error for debugging
    }

    $stmt->close();

    echo json_encode($response);
    return;
}


//UPDATE ACCESSORY DATA
if ($_POST['action'] === 'update_accessory_data') {
    if (empty($_POST['name'])) {
        $response["error"] = "Name is required";
        echo json_encode($response);
        return;
    }

    if (!is_numeric($_POST['price']) || $_POST['price'] <= 0) {
        $response["error"] = 'Price cannot be empty or 0';
        echo json_encode($response);
        return;
    }

    $id = $_POST['accessory_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $accessory = $_POST['accessory'];
    $supplier = $_POST['supplier'];
    $supplier_link = $_POST['supplier_link'];
    $pieces = $_POST['pieces'] ?: 0;
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE inventory_accessories 
                            SET name = ?, accessory = ?, price = ?, supplier = ?, supplier_link = ?, pieces = ? 
                            WHERE id = ? AND owner_id = ?");
    
    $stmt->bind_param("ssdsdiis", $name, $accessory, $price, $supplier, $supplier_link, $pieces, $id, $userID);

    if ($stmt->execute()) {
        $response['success'] = "Accessory updated";
    } else {
        $response['error'] = "Error updating accessory data: " . $stmt->error;
        error_log("PV error: " . $stmt->error);  // Log error for debugging
    }

    $stmt->close();

    echo json_encode($response);
    return;
}

//DELETE BOTTLE
if($_POST['action'] == 'delete' && $_POST['btlId'] && $_POST['type'] == 'bottle'){
	$id = mysqli_real_escape_string($conn, $_POST['btlId']);
	
	if(mysqli_query($conn, "DELETE FROM bottles WHERE id = '$id' AND owner_id = '$userID' ")){
		mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '$id' AND type = '4' AND owner_id = '$userID'");
		$response["success"] = 'Bottle deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//DELETE ACCESSORY
if($_POST['action'] == 'delete' && $_POST['accessoryId'] && $_POST['type'] == 'accessory'){
	$id = mysqli_real_escape_string($conn, $_POST['accessoryId']);
	
	if(mysqli_query($conn, "DELETE FROM inventory_accessories WHERE id = '$id' AND owner_id = '$userID'")){
		mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '$id' AND type = '5' AND owner_id = '$userID'");
		$response["success"] = 'Accessory deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//IMPORT IMAGES FROM PUBCHEM
if (isset($_GET['IFRA_PB']) && $_GET['IFRA_PB'] === 'import') {
    require_once(__ROOT__.'/func/pvFileGet.php');

    $i = 0;
    $response = [];

    // Fetch CAS numbers that need updating
    $query = "SELECT cas FROM IFRALibrary WHERE (image IS NULL OR image = '' OR image = '-') AND owner_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check for database query errors
    if (!$result) {
        error_log("PV error: Database query failed: " . $conn->error);
        echo json_encode(["error" => "Database query failed."]);
        return;
    }

    // If no records found, return an error message
    if ($result->num_rows === 0) {
        echo json_encode(["error" => "No records need updating in the IFRA Database."]);
        return;
    }

    $view = $settings['pubchem_view'];

    while ($row = $result->fetch_assoc()) {
        $casNumber = trim($row['cas']);

        // Skip empty CAS numbers
        if (empty($casNumber)) {
            error_log("PV error: Skipping empty CAS number entry.");
            continue;
        }

        $imageUrl = $pubChemApi . '/pug/compound/name/' . urlencode($casNumber) . '/PNG?record_type=' . $view . '&image_size=small';

        // Fetch image content
        $imageContent = pv_file_get_contents($imageUrl);

        // Handle failed fetch attempts
        if ($imageContent === false || empty($imageContent)) {
            error_log("PV error: Failed to fetch image structure for CAS: $casNumber");
            continue;
        }

        // Encode image data
        $image = base64_encode($imageContent);

        // Update database
        $updateStmt = $conn->prepare("UPDATE IFRALibrary SET image = ? WHERE cas = ? AND owner_id = ?");
        $updateStmt->bind_param("sss", $image, $casNumber, $userID);

        if ($updateStmt->execute()) {
            $i++;
        } else {
            error_log("PV error: Error updating image for CAS: $casNumber - " . $updateStmt->error);
        }

        $updateStmt->close();

        // Shorter delay to improve performance while respecting API limits
        usleep(50000); // 50 milliseconds
    }

    $stmt->close();

    // Send success response
    echo json_encode(["success" => "$i images updated successfully!"]);
    return;
}



//Update data FROM PubChem
if($_POST['pubChemData'] == 'update' && $_POST['cas']){
	$cas = trim($_POST['cas']);
	$molecularWeight = $_POST['molecularWeight'];
	$logP = trim($_POST['logP']);
	$molecularFormula = $_POST['molecularFormula'];
	$InChI = $_POST['InChI'];
	$CanonicalSMILES = $_POST['CanonicalSMILES'];
	$ExactMass = trim($_POST['ExactMass']);
	
	if($molecularWeight){
		$q = mysqli_query($conn, "UPDATE ingredients SET molecularWeight = '$molecularWeight' WHERE cas='$cas' AND owner_id = '$userID' ");
	}
	if($logP){
		$q.= mysqli_query($conn, "UPDATE ingredients SET logp = '$logP' WHERE cas='$cas' AND owner_id = '$userID'");
	}
	if($molecularFormula){
		$q.= mysqli_query($conn, "UPDATE ingredients SET formula = '$molecularFormula' WHERE cas='$cas' AND owner_id = '$userID'");
	}
	if($InChI){
		$q.= mysqli_query($conn, "UPDATE ingredients SET INCI = '$InChI' WHERE cas='$cas' AND owner_id = '$userID'");
	}
	if($q){
		$response["success"] = 'Local data updated';
	}else{
		$response["error"] = 'Unable to update data';
	}
	echo json_encode($response);
	return;
}

//IMPORT SYNONYMS FROM PubChem
if ($_POST['synonym'] == 'import' && $_POST['method'] == 'pubchem') {
    $ing = base64_decode($_POST['ing']);
    $cas = trim($_POST['cas']);

    // Construct the PubChem API URL
    $url = $pubChemApi . '/pug/compound/name/' . $cas . '/synonyms/JSON';
    $json = file_get_contents($url);
    
    // Decode JSON response from PubChem API
    $json = json_decode($json);

    if (!isset($json->InformationList->Information[0])) {
        $response["error"] = 'No data found from PubChem for CAS: ' . $cas;
        echo json_encode($response);
        return;
    }

    // Extract synonyms and CID from PubChem response
    $data = $json->InformationList->Information[0]->Synonym;
    $cid = $json->InformationList->Information[0]->CID;
    $source = 'PubChem';

    // If no synonyms found, return an error
    if (empty($data)) {
        $response["error"] = 'No synonyms found for CAS: ' . $cas;
        echo json_encode($response);
        return;
    }

    $i = 0;
    $stmt = $conn->prepare("SELECT synonym FROM synonyms WHERE synonym = ? AND ing = ? AND owner_id = ?");
    $insertStmt = $conn->prepare("INSERT INTO synonyms (ing, cid, synonym, source, owner_id) VALUES (?, ?, ?, ?, ?)");

    foreach ($data as $d) {
        // Process EINECS data
        if (strpos($d, 'EINECS ') !== false) {
            $einecs = explode('EINECS ', $d);
            if (isset($einecs[1])) {
                $updateStmt = $conn->prepare("UPDATE ingredients SET einecs = ? WHERE cas = ? AND owner_id = ?");
                $updateStmt->bind_param('sss', $einecs[1], $cas, $userID);
                $updateStmt->execute();
                $updateStmt->close();
            }
        }

        // Process FEMA data
        if (strpos($d, 'FEMA ') !== false) {
            $fema = explode('FEMA ', $d);
            if (isset($fema[1])) {
                $femaNumber = preg_replace("/[^0-9]/", "", $fema[1]);
                $updateStmt = $conn->prepare("UPDATE ingredients SET FEMA = ? WHERE cas = ? AND owner_id = ?");
                $updateStmt->bind_param('sss', $femaNumber, $cas, $userID);
                $updateStmt->execute();
                $updateStmt->close();
            }
        }

        // Check if synonym already exists
        $stmt->bind_param('sss', $d, $ing, $userID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            // Insert new synonym if it doesn't exist
            $insertStmt->bind_param('sssss', $ing, $cid, $d, $source, $userID);
            if ($insertStmt->execute()) {
                $i++;
            }
        }
    }

    // Close prepared statements
    $stmt->close();
    $insertStmt->close();

    if ($i > 0) {
        $response["success"] = $i . ' synonym(s) imported';
    } else {
        $response["error"] = 'No new synonyms were added, data already in sync.';
    }

    echo json_encode($response);
    return;
}


//ADD SYNONYM
if($_POST['synonym'] == 'add'){
	$synonym = mysqli_real_escape_string($conn, $_POST['sName']);
	$source = mysqli_real_escape_string($conn, $_POST['source']);
	
	$ing = base64_decode($_POST['ing']);

	if(empty($synonym)){
		$response["error"] = 'Synonym name is required';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT synonym FROM synonyms WHERE synonym = '$synonym' AND ing = '$ing' AND owner_id = '$userID'"))){
		$response["error"] = $synonym.' already exists';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn, "INSERT INTO synonyms (synonym,source,ing,owner_id) VALUES ('$synonym','$source','$ing','$userID')")){
		$response["success"] = $synonym.' added to the list';
		echo json_encode($response);
	}
	
	return;
}


//UPDATE SYNONYM
if($_GET['synonym'] == 'update'){
	$value = trim(mysqli_real_escape_string($conn, $_POST['value']));
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ing = base64_decode($_GET['ing']);

	if(mysqli_query($conn, "UPDATE synonyms SET $name = '$value' WHERE id = '$id' AND ing='$ing' AND owner_id = '$userID'")){
		$response["success"] = 'Synonym updated';
	} else {
		$response["error"] = mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//DELETE ING SYNONYM	
if($_GET['synonym'] == 'delete'){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	if(mysqli_query($conn, "DELETE FROM synonyms WHERE id = '$id' AND owner_id = '$userID'")){
		$response["success"] = 'Synonym deleted';	
	} else {
		$response["error"] = mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//ADD REPLACEMENT
if ($_POST['replacement'] == 'add') {
    $ing_name = base64_decode($_POST['ing_name']);
    $ing_cas = trim($_POST['ing_cas']);
    
    if (empty($_POST['rName'])) {
        $response["error"] = 'Name is required';
        echo json_encode($response);
        return;
    }

    if (empty($_POST['rCAS'])) {
        $response["error"] = 'CAS is required';
        echo json_encode($response);
        return;
    }
    
    $stmt = $conn->prepare("SELECT ing_rep_name FROM ingReplacements WHERE owner_id = ? AND ing_name = ? AND ing_rep_name = ?");
    $stmt->bind_param('sss', $userID, $ing_name, $_POST['rName']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response["error"] = $_POST['rName'] . ' already exists';
        echo json_encode($response);
        return;
    }
    
    $insertStmt = $conn->prepare("INSERT INTO ingReplacements (ing_id, ing_name, ing_cas, ing_rep_id, ing_rep_name, ing_rep_cas, notes, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insertStmt->bind_param('ississss', $_POST['ing_id'], $ing_name, $ing_cas, $_POST['rIngId'], $_POST['rName'], $_POST['rCAS'], $_POST['rNotes'], $userID);
    
    if ($insertStmt->execute()) {
        $response["success"] = $_POST['rName'] . ' added to the list';
    } else {
		error_log("PV error: " . $insertStmt->error);  // Log the error for debugging
        $response["error"] = 'Error: ' . $insertStmt->error;
    }

    // Close the prepared statements
    $stmt->close();
    $insertStmt->close();
    
    echo json_encode($response);
    return;
}


//UPDATE ING REPLACEMENT
if($_GET['replacement'] == 'update'){
	$value = trim(mysqli_real_escape_string($conn, $_POST['value']));
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ing = base64_decode($_GET['ing']);

	if(mysqli_query($conn, "UPDATE ingReplacements SET $name = '$value' WHERE id = '$id' AND ing_name='$ing' AND owner_id = '$userID'")){
		$response["success"] = $ing.' replacement updated';
	}else{
		error_log("PV error: " . mysqli_error($conn));  // Log the error for debugging
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}


//DELETE ING REPLACEMENT	
if($_POST['replacement'] == 'delete'){
	$id = mysqli_real_escape_string($conn, $_POST['id']);
	if(mysqli_query($conn, "DELETE FROM ingReplacements WHERE id = '$id' AND owner_id = '$userID'")){
		$response["success"] = $_POST['name'].' replacement removed';
	}else{
		error_log("PV error: " . mysqli_error($conn));  // Log the error for debugging
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//UPDATE ING DOCUMENT
if($_GET['action'] == 'updateDocument'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ownerID = mysqli_real_escape_string($conn, $_GET['ingID']);

	if(mysqli_query($conn, "UPDATE documents SET $name = '$value' WHERE ownerID = '$ownerID' AND id='$id' AND owner_id = '$userID'")){
		$response["success"] = 'Document updated';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
		error_log("PV error: " . mysqli_error($conn));  // Log the error for debugging
	}
	echo json_encode($response);
	return;
}


//DELETE DOCUMENT	
if($_GET['action'] == 'deleteDocument'){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	$ownerID = mysqli_real_escape_string($conn, $_GET['ownerID']);
							
	if(mysqli_query($conn, "DELETE FROM documents WHERE id = '$id' AND ownerID='$ownerID' AND owner_id = '$userID'")){
		$response["success"] = 'Document deletetd';
	}else{
		error_log("PV error: " . mysqli_error($conn));  // Log the error for debugging
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//GET SUPPLIER PRICE
if ($_POST['ingSupplier'] == 'getPrice') {
    $ingID = mysqli_real_escape_string($conn, $_POST['ingID']);
    $ingSupplierID = mysqli_real_escape_string($conn, $_POST['ingSupplierID']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $supplier_link = urldecode($_POST['sLink']);

    $query = "SELECT price_tag_start, price_tag_end, add_costs, price_per_size FROM ingSuppliers WHERE id = ? AND owner_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('is', $ingSupplierID, $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $supp_data = $result->fetch_assoc();
    $stmt->close();

    if ($supp_data) {
        $newPrice = priceScrape($supplier_link, $size, $supp_data['price_tag_start'], $supp_data['price_tag_end'], $supp_data['add_costs'], $supp_data['price_per_size']);
        if ($newPrice !== false) {
            $updateQuery = "UPDATE suppliers SET price = ? WHERE ingSupplierID = ? AND ingID = ? AND owner_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('diis', $newPrice, $ingSupplierID, $ingID, $userID);
            if ($updateStmt->execute()) {
                $response["success"] = 'Price updated, please validate data is correct';
            } else {
                $response["error"] = 'Error updating the price in the database.';
            }
            $updateStmt->close();
        } else {
            $response["error"] = 'Error getting the price from the supplier. Previous value has been retained.';
        }
    } else {
        $response["error"] = 'Supplier data not found.';
    }

    echo json_encode($response);
    return;
}

//ADD ING SUPPLIER
if($_POST['ingSupplier'] == 'add'){
	if(empty($_POST['supplier_id']) || empty($_POST['supplier_link']) || empty($_POST['supplier_size']) || empty($_POST['supplier_price'])){
		$response["error"] = 'Error: Missing fields!';
		echo json_encode($response);
		return;
	}
	
	if(!is_numeric($_POST['supplier_size']) || !is_numeric($_POST['stock']) || !is_numeric($_POST['supplier_price'])){
		$response["error"] = 'Error: Only numeric values allowed in size, stock and price fields!';
		echo json_encode($response);
		return;
	}
	
	$ingID = mysqli_real_escape_string($conn, $_POST['ingID']);
	$supplier_id = mysqli_real_escape_string($conn, $_POST['supplier_id']);
	$supplier_link = mysqli_real_escape_string($conn, $_POST['supplier_link']);	
	$supplier_size = mysqli_real_escape_string($conn, $_POST['supplier_size']);
	$supplier_price = mysqli_real_escape_string($conn, $_POST['supplier_price']);
	$supplier_manufacturer = mysqli_real_escape_string($conn, $_POST['supplier_manufacturer']);
	$supplier_name = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '$supplier_id'"));
	$supplier_batch = mysqli_real_escape_string($conn, $_POST['supplier_batch']);
	$purchased = mysqli_real_escape_string($conn, $_POST['purchased'] ?: date('Y-m-d'));
	$stock = mysqli_real_escape_string($conn, $_POST['stock'] ?: 0);
	$mUnit = $_POST['mUnit'];
	$status = $_POST['status'];
	$supplier_sku = mysqli_real_escape_string($conn, $_POST['supplier_sku']);
	$internal_sku = mysqli_real_escape_string($conn, $_POST['internal_sku']);
	$storage_location = mysqli_real_escape_string($conn, $_POST['storage_location']);


	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingSupplierID FROM suppliers WHERE ingSupplierID = '$supplier_id' AND ingID = '$ingID' AND owner_id = '$userID'"))){
		$response["error"] = $supplier_name['name'].' already exists';
		echo json_encode($response);
		return;
	}
		
	if(!mysqli_num_rows(mysqli_query($conn, "SELECT ingSupplierID FROM suppliers WHERE ingID = '$ingID' AND owner_id = '$userID'"))){
	   $preferred = '1';
	}else{
		$preferred = '0';
	}
		
	if(mysqli_query($conn, "INSERT INTO suppliers (ingSupplierID,ingID,supplierLink,price,size,manufacturer,preferred,batch,purchased,stock,mUnit,status,supplier_sku,internal_sku,storage_location,owner_id) VALUES ('$supplier_id','$ingID','$supplier_link','$supplier_price','$supplier_size','$supplier_manufacturer','$preferred','$supplier_batch','$purchased','$stock','$mUnit','$status','$supplier_sku','$internal_sku','$storage_location','$userID')")){
		$response["success"] = $supplier_name['name'].' added';
	}else{
		error_log("PV error: " . mysqli_error($conn));  // Log the error for debugging
		$response["error"] = mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

//UPDATE ING SUPPLIER
if($_GET['ingSupplier'] == 'update'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ingID = mysqli_real_escape_string($conn, $_GET['ingID']);

	if(mysqli_query($conn, "UPDATE suppliers SET $name = '$value' WHERE id = '$id' AND ingID='$ingID' AND owner_id = '$userID'")){
		$response["success"] = 'Supplier updated';
	} else {
		error_log("PV error: " . mysqli_error($conn));  // Log the error for debugging
		$response["error"] = mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

//UPDATE PREFERRED SUPPLIER
if($_GET['ingSupplier'] == 'preferred'){
	$sID = mysqli_real_escape_string($conn, $_GET['sID']);
	$ingID = mysqli_real_escape_string($conn, $_GET['ingID']);
	$status = mysqli_real_escape_string($conn, $_GET['status']);
	
	mysqli_query($conn, "UPDATE suppliers SET preferred = '0' WHERE ingID='$ingID'");
	if(mysqli_query($conn, "UPDATE suppliers SET preferred = '$status' WHERE ingSupplierID = '$sID' AND ingID='$ingID' AND owner_id = '$userID'")){
		$response["success"] = 'Supplier set to prefered';
	} else {
		error_log("PV error: " . mysqli_error($conn));  // Log the error for debugging
		$response["error"] = mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//DELETE ING SUPPLIER	
if($_GET['ingSupplier'] == 'delete'){

	$sID = mysqli_real_escape_string($conn, $_GET['sID']);
	$ingID = mysqli_real_escape_string($conn, $_GET['ingID']);
	
	$supplierCount = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM suppliers WHERE ingID = '$ingID' AND owner_id = '$userID'"));
    if ($supplierCount <= 1) {
        $response["error"] = 'Cannot delete the last supplier for this ingredient';
		echo json_encode($response);
		return;
    }
								
	if(mysqli_query($conn, "DELETE FROM suppliers WHERE id = '$sID' AND ingID='$ingID' AND owner_id = '$userID'")){
		$response["success"] = 'Supplier deleted';
	} else {
		$response["error"] = mysqli_error($conn);
		error_log("PV error: " . mysqli_error($conn));  // Log the error for debugging
	}
	echo json_encode($response);
	return;
}

//FORMULA QUANTITY MANAGEMENT
if($_POST['updateQuantity'] && $_POST['ingQuantityID'] &&  $_POST['ingQuantityName']  && $_POST['fid']){
	$fid = $_POST['fid'];
	$value = $_POST['ingQuantity'];
	$ingredient = $_POST['ingQuantityID'];
	$ing_name = $_POST['ingQuantityName'];
	
	if(empty($_POST['ingQuantity'])){
		$response["error"] = 'Quantity cannot be empty';
		echo json_encode($response);
		return;
	}
	if(!is_numeric($_POST['ingQuantity'])){
		$response["error"] = 'Quantity must be numeric only';
		echo json_encode($response);
		return;
	}
	
	if($_POST['curQuantity'] == $_POST['ingQuantity']){
		$response["error"] = 'Quantity is already the same';
		echo json_encode($response);
		return;
	}
	
	
	if($_POST['ingReCalc'] == 'true'){
		$ingID = $_POST['ingID'];
		if(!$_POST['formulaSolventID']){
			$response["error"] = 'Please select a solvent';
			echo json_encode($response);
			return;
		}
		$formulaSolventID = $_POST['formulaSolventID'];
		
		if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM ingredients WHERE id = '".$ingID."' AND profile='solvent' AND owner_id = '$userID'"))){
			$response["error"] = 'You cannot exchange a solvent with a solvent';
			echo json_encode($response);
			return;
		}
		
		$slv = mysqli_fetch_array(mysqli_query($conn,"SELECT quantity FROM formulas WHERE ingredient_id = '".$formulaSolventID."' AND fid = '".$fid."' AND owner_id = '$userID'"));
		
		$fq = $_POST['ingQuantity'] - $_POST['curQuantity'];
		
		if($slv['quantity'] < $fq){
			$response["error"] = 'Not enough solvent, available: '.number_format($slv['quantity'],$settings['qStep']).$settings['mUnit'].', Requested: '.number_format($fq,$settings['qStep']).$settings['mUnit'];
			echo json_encode($response);
			return;
		}
		
		//UPDATE SOLVENT
		function formatVal($num){
    		return sprintf("%+.4f",$num);
		}
		
		$curV = mysqli_fetch_array(mysqli_query($conn, "SELECT quantity FROM formulas WHERE fid = '$fid' AND id = '".$ingredient."' AND owner_id = '$userID'"));
		$diff = number_format($curV['quantity'] -  $value  , 4);
		$v = formatVal($diff);
		
		$qs ="UPDATE formulas SET quantity = quantity $v WHERE fid = '$fid' AND ingredient_id = '".$formulaSolventID."' AND owner_id = '$userID'";
		if(!mysqli_query($conn, $qs)){
			$response["error"] = 'Error updating solvent: '.mysqli_error($conn);
			$response["query"] = $qs;
			echo json_encode($response);
			return;
		}
	}
		
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '".$_POST['fid']."' AND owner_id = '$userID'"));
	
	if($meta['isProtected'] == FALSE){
		
		if(mysqli_query($conn, "UPDATE formulas SET quantity = '$value' WHERE fid = '$fid' AND id = '$ingredient'")){
			
			$lg = "CHANGED: ".$ing_name." Set $name to $value";
			mysqli_query($conn, "INSERT INTO formula_history (fid,ing_id,change_made,user,owner_id) VALUES ('".$meta['id']."','$ingredient','$lg','".$user['fullName']."','$userID')");
			
			$response["success"] = 'Quantity updated';
			echo json_encode($response);
		
		}else{
			$response["error"] = mysqli_error($conn);
			error_log("PV error: " . mysqli_error($conn));  // Log the error for debugging
			echo json_encode($response);
		}
		
	}
	return;
}

if($_POST['value'] && $_GET['formula'] && $_POST['pk']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, $_GET['formula']);
	$ingredient = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	$ing_name =  mysqli_fetch_array(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE id = '$ingredient' AND fid = '".$_GET['formula']."' AND owner_id = '$userID'"));
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '".$_GET['formula']."' AND owner_id = '$userID'"));
	if($meta['isProtected'] == FALSE){
					
		if(mysqli_query($conn, "UPDATE formulas SET $name = '$value' WHERE fid = '$formula' AND id = '$ingredient' AND owner_id = '$userID'")){
			
			$lg = "CHANGED: ".$ing_name['ingredient']." Set $name to $value";
			mysqli_query($conn, "INSERT INTO formula_history (fid,ing_id,change_made,user,owner_id) VALUES ('".$meta['id']."','$ingredient','$lg','".$user['fullName']."','$userID')");
			$response["success"] = 'Formula updated';
		} else {
			$response["error"] = mysqli_error($conn);
			error_log("PV error: " . mysqli_error($conn));  // Log the error for debugging
		}
	}
	echo json_encode($response);
	return;
}

if($_GET['formulaMeta']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, base64_decode($_GET['formulaMeta']));
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE formulasMetaData SET $name = '$value' WHERE id = '$id' AND owner_id = '$userID'")){
		$response["success"] = 'Formula meta updated';
	} else {
		$response["error"] = mysqli_error($conn);
		error_log("PV error: " . mysqli_error($conn));  // Log the error for debugging
	}
	echo json_encode($response);
	return;
}

if($_GET['createRev'] == 'man'){
	require_once(__ROOT__.'/func/createFormulaRevision.php');
	$fid = $_GET['fid'];
	
	if($l = createFormulaRevision($fid,'Manually',$conn)){
		$response["success"] = 'Revision created';
		echo json_encode($response);
	}else{
		$response["error"] = 'No changes detected in formula';
		echo json_encode($response);
	}
	return;
}

if($_GET['protect']){
	require_once(__ROOT__.'/func/createFormulaRevision.php');
	$fid = mysqli_real_escape_string($conn, $_GET['protect']);
	
	if($_GET['isProtected'] == 'true'){
		$isProtected = '1';
		$l = 'locked';
		createFormulaRevision($fid,'Automatic',$conn);
	}else{
		$isProtected = '0';
		$l = 'unlocked';
	}
	if(mysqli_query($conn, "UPDATE formulasMetaData SET isProtected = '$isProtected' WHERE fid = '$fid' AND owner_id = '$userID'")){
		$response["success"] = 'Formula '.$l;
		echo json_encode($response);
	}else{
		$response["error"] = 'Something went wrong';
		echo json_encode($response);
	}
	return;
}

if($_POST['formulaSettings'] &&  $_POST['set']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$set = mysqli_real_escape_string($conn, $_POST['set']);
	$val = mysqli_real_escape_string($conn, $_POST['val']);

	if(mysqli_query($conn, "UPDATE formulasMetaData SET $set = '$val' WHERE fid = '$fid' AND owner_id = '$userID'")){
		$response["success"] = "Formula settings updated";
		echo json_encode($response);
	}else{
		$response["error"] = 'Something went wrong';
		echo json_encode($response);
	}
	return;
}



if($_GET['action'] == 'rename' && $_GET['fid']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$fid = mysqli_real_escape_string($conn, $_GET['fid']);
	$id = $_POST['pk'];
	if(!$value){
		$response["error"] = 'Formula name cannot be empty';
		echo json_encode($response);
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE name = '$value' AND owner_id = '$userID'"))){
		$response["error"] = 'Name already exists';
		echo json_encode($response);
	
	}else{
		mysqli_query($conn, "UPDATE formulasMetaData SET name = '$value' WHERE id = '$id' AND owner_id = '$userID'");
		if(mysqli_query($conn, "UPDATE formulas SET name = '$value' WHERE fid = '$fid' AND owner_id = '$userID'")){
			$response["success"] = 'Formula renamed';
			$response["msg"] = $value;
			echo json_encode($response);
		}
	
	}
	
	return;	
}

if($_GET['action'] == 'ingredientCategories'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$cat_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE ingCategory SET $name = '$value' WHERE id = '$cat_id' AND owner_id = '$userID'")){
		$response["success"] = 'Ingredient category updated';
	}else{
		$response["error"] = mysqli_error($conn);
        error_log("PV error: " . $error_message);  // Log the error for debugging
	}
	
	echo json_encode($response);
	return;
}

if ($_GET['settings'] == 'prof') {
    $value = mysqli_real_escape_string($conn, $_POST['value']);
    $id = mysqli_real_escape_string($conn, $_POST['pk']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    
    $query = "UPDATE ingProfiles SET $name = '$value' WHERE id = '$id' AND owner_id = '$userID'";
    
    if (mysqli_query($conn, $query)) {
        // If the query is successful, no action needed here
        return;
    } else {
        $error_message = 'Error updating profile: ' . mysqli_error($conn);
        error_log("PV error: " . $error_message);  // Log the error for debugging
        $response["error"] = $error_message;
        echo json_encode($response);
        return;
    }
	return;
}


if($_GET['settings'] == 'fcat' && $_GET['action'] == 'updateFormulaCategory' ){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$cat_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE formulaCategories SET $name = '$value' WHERE id = '$cat_id' AND owner_id = '$userID'")){
		$response["success"] = 'Formula actegory updated';
	} else {
		$response["error"] = mysqli_error($conn);
	}
	echo json_encode($response);
	return;	
}

if($_GET['kind'] == 'suppliers' && $_GET['action'] == 'supplier_update'){
	$value = htmlentities($_POST['value']);
	$sup_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE ingSuppliers SET $name = '$value' WHERE id = '$sup_id'  AND owner_id = '$userID'")){
		$response["success"] = 'Supplier updated';
	} else {
		$response["error"] = mysqli_error($conn);
		error_log("PV error: " . mysqli_error($conn));  // Log the error for debugging
	}
	echo json_encode($response);
	return;	
}

//EDIT SUPPLIER
if ($_POST['action'] == 'editsupplier') {
    $id = $_POST['id'];

    // Validate required fields
    $requiredFields = ['name', 'address', 'po', 'country', 'currency', 'telephone', 'url', 'email'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $response["error"] = ucfirst($field) . ' is required';
            echo json_encode($response);
            return;
        }
    }

    // Sanitize inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $po = mysqli_real_escape_string($conn, $_POST['po']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $currency = mysqli_real_escape_string($conn, $_POST['currency']);
    $telephone = mysqli_real_escape_string($conn, $_POST['telephone']);
    $url = mysqli_real_escape_string($conn, $_POST['url']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Use prepared statement to update supplier
    $stmt = $conn->prepare("UPDATE ingSuppliers SET address = ?, po = ?, country = ?, currency = ?, telephone = ?, url = ?, email = ? WHERE id = ? AND owner_id = ?");
    $stmt->bind_param('sssssssis', $address, $po, $country, $currency, $telephone, $url, $email, $id, $userID);

    if ($stmt->execute()) {
        $response["success"] = 'Supplier ' . $name . ' updated';
    } else {
        $response["error"] = 'Something went wrong: ' . $stmt->error;
    }

    $stmt->close();
    echo json_encode($response);
    return;
}

//ADD SUPPLIER
if ($_POST['action'] == 'addsupplier') {
    // Validate numeric fields
    if (!is_numeric($_POST['min_ml']) || !is_numeric($_POST['min_gr'])) {
        $response["error"] = 'Only numeric values allowed in ml and grams fields';
        echo json_encode($response);
        return;
    }

    // Sanitize inputs
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $po = mysqli_real_escape_string($conn, $_POST['po']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $currency = mysqli_real_escape_string($conn, $_POST['currency']);
    $telephone = mysqli_real_escape_string($conn, $_POST['telephone']);
    $url = mysqli_real_escape_string($conn, $_POST['url']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $platform = mysqli_real_escape_string($conn, $_POST['platform']);
    $price_tag_start = htmlentities($_POST['price_tag_start']);
    $price_tag_end = htmlentities($_POST['price_tag_end']);
    $add_costs = is_numeric($_POST['add_costs']) ? $_POST['add_costs'] : 0;
    $min_ml = mysqli_real_escape_string($conn, $_POST['min_ml']) ?: 0;
    $min_gr = mysqli_real_escape_string($conn, $_POST['min_gr']) ?: 0;

    // Validate required fields
    if (empty($name)) {
        $response["error"] = 'Supplier name required';
        echo json_encode($response);
        return;
    }

    // Check for duplicate supplier name
    $query = "SELECT name FROM ingSuppliers WHERE name = '$name' AND owner_id = '$userID'";
    if (mysqli_num_rows(mysqli_query($conn, $query))) {
        $response["error"] = $name . ' supplier name already exists';
        echo json_encode($response);
        return;
    }

    // Insert new supplier
    $query = "INSERT INTO ingSuppliers (name, address, po, country, currency, telephone, url, email, platform, price_tag_start, price_tag_end, add_costs, notes, min_ml, min_gr, owner_id) 
              VALUES ('$name', '$address', '$po', '$country', '$currency', '$telephone', '$url', '$email', '$platform', '$price_tag_start', '$price_tag_end', '$add_costs', '$description', '$min_ml', '$min_gr', '$userID')";
    if (mysqli_query($conn, $query)) {
        $response["success"] = 'Supplier ' . $name . ' added';
    } else {
        $response["error"] = 'Something went wrong: ' . mysqli_error($conn);
    }

    echo json_encode($response);
    return;
}

//DELETE ING SUPPLIER
if($_GET['supp'] == 'delete' && $_GET['ID']){
	$ID = mysqli_real_escape_string($conn, $_GET['ID']);
	$supplier = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '$ID' AND owner_id = '$userID'"));

	if(mysqli_query($conn, "DELETE FROM ingSuppliers WHERE id = '$ID'")){
		$response["success"] = 'Supplier '.$supplier['name'].' deleted';
	}else{
		$response["error"] = 'Something went wrong: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//ADD composition
if($_POST['composition'] == 'add'){
    $allgName = mysqli_real_escape_string($conn, $_POST['allgName']);
    $allgCAS = mysqli_real_escape_string($conn, $_POST['allgCAS']);
    $allgEC = mysqli_real_escape_string($conn, $_POST['allgEC']);
    $minPerc = rtrim(mysqli_real_escape_string($conn, $_POST['minPerc']), '%');
    $maxPerc = rtrim(mysqli_real_escape_string($conn, $_POST['maxPerc']), '%');
    $GHS = rtrim(mysqli_real_escape_string($conn, $_POST['GHS']));

    $ing = base64_decode($_POST['ing']);

    $declare = ($_POST['addToDeclare'] == 'true') ? '1' : '0';

    if(empty($allgName)){
        $response["error"] = 'Name is required';
        echo json_encode($response);
        return;
    }

    if(empty($allgCAS)){
        $response["error"] = 'CAS number is required';
        echo json_encode($response);
        return;
    }

    if(empty($minPerc)){
        $response["error"] = 'Minimum percentage is required';
        echo json_encode($response);
        return;
    }

    if(empty($maxPerc)){
        $response["error"] = 'Max percentage is required';
        echo json_encode($response);
        return;
    }

    if(!is_numeric($minPerc)){
        $response["error"] = 'Minimum percentage value needs to be numeric';
        echo json_encode($response);
        return;
    }

    if(!is_numeric($maxPerc)){
        $response["error"] = 'Maximum percentage value needs to be numeric';
        echo json_encode($response);
        return;
    }

    $stmt = $conn->prepare("SELECT name FROM ingredient_compounds WHERE name = ? AND ing = ? AND owner_id = ?");
    $stmt->bind_param('sss', $allgName, $ing, $userID);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        $response["error"] = $allgName.' already exists';
        echo json_encode($response);
        return;
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO ingredient_compounds (name, cas, ec, min_percentage, max_percentage, GHS, toDeclare, ing, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssssss', $allgName, $allgCAS, $allgEC, $minPerc, $maxPerc, $GHS, $declare, $ing, $userID);

    if($stmt->execute()){
        $response["success"] = $allgName.' added to the composition';
    } else {
        $response["error"] = $stmt->error;
    }
    $stmt->close();

    if($_POST['addToIng'] == 'true'){
        $stmt = $conn->prepare("SELECT id FROM ingredients WHERE name = ? AND owner_id = ?");
        $stmt->bind_param('ss', $allgName, $userID);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows == 0){
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO ingredients (name, cas, einecs, owner_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $allgName, $allgCAS, $allgEC, $userID);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt->close();
        }
    }

    echo json_encode($response);
    return;
}

//UPDATE composition
if($_GET['composition'] == 'update'){
	$value = rtrim(mysqli_real_escape_string($conn, $_POST['value']),'%');
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE ingredient_compounds SET $name = '$value' WHERE id = '$id' AND owner_id = '$userID'")){
		$response["success"] = 'Ingredient updated';
	} else {
		$response["error"] = mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

//DELETE composition	
if($_POST['composition'] == 'delete'){

	$id = mysqli_real_escape_string($conn, $_POST['allgID']);
	//$ing = base64_decode($_POST['ing']);

	$delQ = mysqli_query($conn, "DELETE FROM ingredient_compounds WHERE id = '$id' AND owner_id = '$userID'");	
	if($delQ){
		$response["success"] = $ing.' deleted';
	}else {
		$response["error"] = mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

//DELETE INGREDIENT	
if($_POST['ingredient'] == 'delete' && $_POST['ing_id']){

	$id = mysqli_real_escape_string($conn, $_POST['ing_id']);
	$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingredients WHERE id = '$id' AND owner_id = '$userID'"));
	
	if($_POST['forceDelIng'] == "false"){

			if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '".$ing['name']."' AND owner_id = '$userID'"))){
			$response["error"] = $ing['name'].' is in use by at least one formula and cannot be deleted';
			echo json_encode($response);
			return;
		}
	}
	if(mysqli_query($conn, "DELETE FROM ingredients WHERE id = '$id' AND owner_id = '$userID'")){
		mysqli_query($conn,"DELETE FROM ingredient_compounds WHERE ing = '".$ing['name']."' AND owner_id = '$userID'");
		$response["success"] = 'Ingredient '.$ing['name'].' and its data deleted';
	}
	
	echo json_encode($response);
	return;

}

//CUSTOMERS - ADD
if($_POST['customer'] == 'add'){
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	if(empty($name)){
		$response["error"] = 'Customer name is required.';
		echo json_encode($response);
		return;
	}
	$address = mysqli_real_escape_string($conn, $_POST['address']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$web = mysqli_real_escape_string($conn, $_POST['web']);
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM customers WHERE name = '$name' AND owner_id = '$userID'"))){
		$response["error"] = $name.' already exists';
	}elseif(mysqli_query($conn, "INSERT INTO customers (name,address,email,web,owner_id) VALUES ('$name', '$address', '$email', '$web', '$userID')")){
		$response["success"] = 'Customer '.$name.' added!';
	}else{
		$response["error"] = 'Error adding customer.';
	}
	echo json_encode($response);
	return;
}

//CUSTOMERS - DELETE
if($_POST['action'] == 'delete' && $_POST['type'] == 'customer' && $_POST['customer_id']){
	$customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
	if(mysqli_query($conn, "DELETE FROM customers WHERE id = '$customer_id' AND owner_id = '$userID'")){
		$response["success"] = 'Customer deleted';
	}else{
		$response["error"] = 'Error deleting customer '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}
	
//CUSTOMERS - UPDATE
if($_POST['update_customer_data'] && $_POST['customer_id']){
	$id = $_POST['customer_id'];
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	if(empty($name)){
		$response["error"] = 'Name cannot be empty';
		echo json_encode($response);
		return;
	}
	$address = mysqli_real_escape_string($conn, $_POST['address'])?:'N/A';
	$email = mysqli_real_escape_string($conn, $_POST['email'])?:'N/A';
	$web = mysqli_real_escape_string($conn, $_POST['web'])?:'N/A';
	$phone = mysqli_real_escape_string($conn, $_POST['phone'])?:'N/A';

	if(mysqli_query($conn, "UPDATE customers SET name = '$name', address = '$address', email = '$email', web = '$web', phone = '$phone' WHERE id = '$id' AND owner_id = '$userID'")){
		$response["success"] = 'Customer details updated';
	}else{
		$response["error"] = 'Error updating customer '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;	
}

//MGM INGREDIENT
if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'general'){
	$ing = mysqli_real_escape_string($conn, $_POST['ing']);

	$INCI = trim(mysqli_real_escape_string($conn, $_POST["INCI"]));
	$cas = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["cas"])));
	$einecs = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["einecs"])));
	$reach = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["reach"])));
	$fema = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["fema"])));
	$purity = validateInput($_POST["purity"]);
	$profile = mysqli_real_escape_string($conn, $_POST["profile"]);
	$type = mysqli_real_escape_string($conn, $_POST["type"]);	
	$strength = mysqli_real_escape_string($conn, $_POST["strength"]);
	$category = mysqli_real_escape_string($conn, $_POST["category"] ? $_POST['category']: '1');
	$physical_state = mysqli_real_escape_string($conn, $_POST["physical_state"]);
	$odor = ucfirst(trim(mysqli_real_escape_string($conn, $_POST["odor"])));
	$notes = ucfirst(trim(mysqli_real_escape_string($conn, $_POST["notes"])));
	
	if(empty($_POST['name'])){
	
		$query = "UPDATE ingredients SET INCI = '$INCI',cas = '$cas',solvent='".$_POST["solvent"]."', einecs = '$einecs', reach = '$reach',FEMA = '$fema',purity='$purity',profile='$profile',type = '$type',strength = '$strength', category='$category',physical_state = '$physical_state',odor = '$odor',notes = '$notes' WHERE name='$ing' AND owner_id = '$userID'";
		
		if(mysqli_query($conn, $query)){
			$response["success"] = 'General details have been updated';
		}else{
			$response["error"] = 'Unable to update database: '.mysqli_error($conn);
		}
	}else{
		$name = sanChar(mysqli_real_escape_string($conn, $_POST["name"]));

		$query = "INSERT INTO ingredients (name, INCI, cas, einecs, reach, FEMA, type, strength, category, profile, notes, odor, purity, solvent, physical_state,owner_id) VALUES ('$name', '$INCI', '$cas', '$einecs', '$reach', '$fema', '$type', '$strength', '$category', '$profile',  '$notes', '$odor', '$purity', '$solvent', '1','$userID')";
		
		if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$name' AND owner_id = '$userID'"))){
			$response["error"] = $name.' already exists';
			$ing = mysqli_fetch_array(mysqli_query($conn,"SELECT id FROM ingredients WHERE name = '$name' AND owner_id = '$userID'"));
			$response["ingid"] = (int)$ing['id'];

		}else{
			if(mysqli_query($conn, $query)){
				$response["success"] = 'Ingredient '.$name.' created';
				$response["ingid"] = mysqli_insert_id($conn);
			}else{
				$response["error"] = 'Failed to create ingredient';
			}
		}
	}
	echo json_encode($response);
	return;	
}


if ($_POST['manage'] === 'ingredient' && $_POST['tab'] === 'usage_limits') {
    $ingID = (int) $_POST['ingID'];

    $flavor_use = ($_POST['flavor_use'] === 'true') ? 1 : 0;
    $noUsageLimit = ($_POST['noUsageLimit'] === 'true') ? 1 : 0;
    $byPassIFRA = ($_POST['byPassIFRA'] === 'true') ? 1 : 0;
    $allergen = ($_POST['isAllergen'] === 'true') ? 1 : 0;

    $usage_type = mysqli_real_escape_string($conn, trim($_POST['usage_type']));

    $categories = [
        'cat1' => (float) $_POST['cat1'],
        'cat2' => (float) $_POST['cat2'],
        'cat3' => (float) $_POST['cat3'],
        'cat4' => (float) $_POST['cat4'],
        'cat5A' => (float) $_POST['cat5A'],
        'cat5B' => (float) $_POST['cat5B'],
        'cat5C' => (float) $_POST['cat5C'],
        'cat5D' => (float) $_POST['cat5D'],
        'cat6' => (float) $_POST['cat6'],
        'cat7A' => (float) $_POST['cat7A'],
        'cat7B' => (float) $_POST['cat7B'],
        'cat8' => (float) $_POST['cat8'],
        'cat9' => (float) $_POST['cat9'],
        'cat10A' => (float) $_POST['cat10A'],
        'cat10B' => (float) $_POST['cat10B'],
        'cat11A' => (float) $_POST['cat11A'],
        'cat11B' => (float) $_POST['cat11B'],
        'cat12' => (float) $_POST['cat12'],
    ];

    $stmt = $conn->prepare(
        "UPDATE ingredients SET byPassIFRA = ?, noUsageLimit = ?, flavor_use = ?, 
        usage_type = ?, allergen = ?, cat1 = ?, cat2 = ?, cat3 = ?, cat4 = ?, 
        cat5A = ?, cat5B = ?, cat5C = ?, cat5D = ?, cat6 = ?, cat7A = ?, cat7B = ?, 
        cat8 = ?, cat9 = ?, cat10A = ?, cat10B = ?, cat11A = ?, cat11B = ?, 
        cat12 = ? WHERE id = ? AND owner_id = ?"
    );

    $stmt->bind_param(
        'iiisiddddddddddddddddddis',
        $byPassIFRA, $noUsageLimit, $flavor_use, $usage_type, $allergen, 
        $categories['cat1'], $categories['cat2'], $categories['cat3'], 
        $categories['cat4'], $categories['cat5A'], $categories['cat5B'], 
        $categories['cat5C'], $categories['cat5D'], $categories['cat6'], 
        $categories['cat7A'], $categories['cat7B'], $categories['cat8'], 
        $categories['cat9'], $categories['cat10A'], $categories['cat10B'], 
        $categories['cat11A'], $categories['cat11B'], $categories['cat12'], $ingID, $userID
    );

    if ($stmt->execute()) {
        $response["success"] = 'Usage limits have been updated!';
    } else {
        $response["error"] = 'Something went wrong: ' . $stmt->error;
    }

    $stmt->close();

    echo json_encode($response);
    return;
}


if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'tech_data'){
	$ingID = (int)$_POST['ingID'];
	$tenacity = mysqli_real_escape_string($conn, $_POST["tenacity"]);
	$flash_point = mysqli_real_escape_string($conn, $_POST["flash_point"]);
	$chemical_name = mysqli_real_escape_string($conn, $_POST["chemical_name"]);
	$formula = mysqli_real_escape_string($conn, $_POST["formula"]);
	$logp = mysqli_real_escape_string($conn, $_POST["logp"]);
	$soluble = mysqli_real_escape_string($conn, $_POST["soluble"]);
	$molecularWeight = mysqli_real_escape_string($conn, $_POST["molecularWeight"]);
	$appearance = mysqli_real_escape_string($conn, $_POST["appearance"]);
	$rdi = (int)$_POST["rdi"]?:0;
	$shelf_life = mysqli_real_escape_string($conn, $_POST["shelf_life"]) ?: 0;

	
	$query = "UPDATE ingredients SET tenacity='$tenacity',flash_point='$flash_point',chemical_name='$chemical_name',formula='$formula',logp = '$logp',soluble = '$soluble',molecularWeight = '$molecularWeight',appearance='$appearance',rdi='$rdi', shelf_life = '$shelf_life' WHERE id='$ingID' AND owner_id = '$userID'";
	if(mysqli_query($conn, $query)){
		$response["success"] = 'Technical data has been updated';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}	
	echo json_encode($response);
	return;
}

if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'note_impact'){
	$ingID = (int)$_POST['ingID'];
	$impact_top = mysqli_real_escape_string($conn, $_POST["impact_top"]);
	$impact_base = mysqli_real_escape_string($conn, $_POST["impact_base"]);
	$impact_heart = mysqli_real_escape_string($conn, $_POST["impact_heart"]);

	$query = "UPDATE ingredients SET impact_top = '$impact_top',impact_heart = '$impact_heart',impact_base = '$impact_base' WHERE id='$ingID' AND owner_id = '$userID'";
	if(mysqli_query($conn, $query)){
		$response["success"] = 'Note impact has been updated';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'privacy'){
	$ingID = (int)$_POST['ingID'];
	if($_POST['isPrivate'] == 'true'){ $isPrivate = '1'; }else{ $isPrivate = '0'; }
	
	$query = "UPDATE ingredients SET isPrivate = '$isPrivate' WHERE id='$ingID' AND owner_id = '$userID'";
	if(mysqli_query($conn, $query)){
		$response["success"] = 'Privacy settings has been updated!';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//ADD PICTOGRAM
if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'safety_info' && $_POST['action'] == 'add'){
	$ingID = (int)$_POST['ingID'];
	$GHS = (int)$_POST['pictogram'];

	if(mysqli_query($conn, "INSERT INTO ingSafetyInfo (GHS, ingID, owner_id) VALUES ('$GHS','$ingID','$userID') ON DUPLICATE KEY UPDATE GHS = VALUES(GHS), ingID = VALUES(ingID), owner_id = VALUES(owner_id)")){
		$response["success"] = 'Safety data has been updated';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//REMOVE PICTOGRAM
if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'safety_info' && $_POST['pictogram_id'] && $_POST['action'] == 'remove'){
	$ingID = (int)$_POST['ingID'];
	$GHS = (int)$_POST['pictogram_id'];
	
	if(mysqli_query($conn, "DELETE FROM ingSafetyInfo WHERE GHS = '$GHS' AND ingID = '$ingID' AND owner_id = '$userID'")){
		$response["success"] = 'Safety data has been updated';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//DEPRECATED????
if($_GET['import'] == 'ingredient'){
	$name = sanChar(mysqli_real_escape_string($conn, base64_decode($_GET["name"])));
	$query = "INSERT INTO ingredients (name, INCI, cas, notes, odor, owner_id) VALUES ('$name', '$INCI', '$cas', 'Auto Imported', '$odor', '$userID')";
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$name' AND owner_id = '$userID'"))){
		$response["error"] = $name.' already exists';
	}else{
		if(mysqli_query($conn, $query)){
			$response["success"] = $name.' imported';
		}else{
			$response["error"] = 'Something went wrong '.mysqli_error($conn);
		}
	}
	echo json_encode($response);	
	return;
}

//DUPLICATE INGREDIENT
if ($_POST['action'] == 'duplicate_ingredient' && isset($_POST['old_ing_name'], $_POST['ing_id'], $_POST['new_ing_name'])) {
    // Sanitize inputs
    $ing_id = (int)$_POST['ing_id'];
    $old_ing_name = mysqli_real_escape_string($conn, trim($_POST['old_ing_name']));
    $new_ing_name = mysqli_real_escape_string($conn, trim($_POST['new_ing_name']));

    // Check if a new ingredient name is provided
    if (empty($new_ing_name)) {
        $response['error'] = 'Please provide a name';
        echo json_encode($response);
        return;
    }

    // Check if the new ingredient name already exists
    $checkNameQuery = "SELECT name FROM ingredients WHERE name = '$new_ing_name' AND owner_id = '$userID'";
    if (mysqli_num_rows(mysqli_query($conn, $checkNameQuery)) > 0) {
        $response['error'] = $new_ing_name . ' already exists';
        echo json_encode($response);
        return;
    }

    // Begin the duplication process
    $queries = [];

    // Duplicate ingredient compounds
    $queries[] = "INSERT INTO ingredient_compounds (ing, name, cas, min_percentage, max_percentage, owner_id)
                  SELECT '$new_ing_name', name, cas, min_percentage, max_percentage, '$userID'
                  FROM ingredient_compounds WHERE ing = '$old_ing_name' AND owner_id = '$userID'";

    // Duplicate ingredient details
	$queries[] = "INSERT INTO ingredients (name, INCI, type, strength, category, purity, cas, FEMA, reach, tenacity, chemical_name, formula, 
				flash_point, appearance, notes, profile, solvent, odor, allergen, flavor_use, soluble, logp, cat1, cat2, cat3, cat4, cat5A, 
				cat5B, cat5C, cat5D, cat6, cat7A, cat7B, cat8, cat9, cat10A, cat10B, cat11A, cat11B, cat12, impact_top, impact_heart, impact_base, 
				usage_type, noUsageLimit, isPrivate, molecularWeight, physical_state, owner_id)
				SELECT '$new_ing_name', INCI, type, strength, category, purity, cas, FEMA, reach, tenacity, chemical_name, formula, 
				flash_point, appearance, notes, profile, solvent, odor, allergen, flavor_use, soluble, logp, cat1, cat2, cat3, cat4, cat5A, 
				cat5B, cat5C, cat5D, cat6, cat7A, cat7B, cat8, cat9, cat10A, cat10B, cat11A, cat11B, cat12, impact_top, impact_heart, impact_base, 
				usage_type, noUsageLimit, isPrivate, molecularWeight, physical_state, '$userID'
				FROM ingredients WHERE id = '$ing_id' AND owner_id = '$userID'
				ON DUPLICATE KEY UPDATE notes = VALUES(notes)";


    // Capture the newly inserted ingredient ID
    $newID = 0;
    if (mysqli_query($conn, end($queries))) {
        $newID = mysqli_insert_id($conn);
    }

    // Duplicate suppliers
    $queries[] = "INSERT INTO suppliers (ingSupplierID, ingID, supplierLink, price, size, manufacturer, preferred, batch, purchased, 
                  mUnit, stock, status, supplier_sku, internal_sku, storage_location, owner_id)
                  SELECT ingSupplierID, '$newID', supplierLink, price, size, manufacturer, preferred, batch, purchased, 
                  mUnit, stock, status, supplier_sku, internal_sku, storage_location, '$userID'
                  FROM suppliers WHERE ingID = '$ing_id' AND owner_id = '$userID'";

    // Execute all queries
    foreach ($queries as $query) {
        if (!mysqli_query($conn, $query)) {
            $response['error'] = 'Error during duplication: ' . mysqli_error($conn);
            error_log("PV error: $query - " . mysqli_error($conn)); // Log the specific query and error
            echo json_encode($response);
            return;
        }
    }

    // Verify the new ingredient and return success
    $newIngredientQuery = "SELECT id, name FROM ingredients WHERE name = '$new_ing_name' AND owner_id = '$userID'";
    if ($newIngredient = mysqli_fetch_assoc(mysqli_query($conn, $newIngredientQuery))) {
        $response['success'] = $old_ing_name . ' duplicated as <a href="/pages/mgmIngredient.php?id=' . $newIngredient['id'] . '">' . $new_ing_name . '</a>';
        echo json_encode($response);
        return;
    }

    // Handle unexpected cases
    $response['error'] = 'Unknown error occurred during duplication';
    error_log("PV error: Unknown duplication issue for ingredient - Old: $old_ing_name, New: $new_ing_name");
    echo json_encode($response);
    return;
}




//RENAME INGREDIENT
if($_POST['action'] == 'rename' && $_POST['old_ing_name'] && $_POST['ing_id']){
	$ing_id = mysqli_real_escape_string($conn, $_POST['ing_id']);

	$old_ing_name = mysqli_real_escape_string($conn, $_POST['old_ing_name']);
	$new_ing_name = mysqli_real_escape_string($conn, $_POST['new_ing_name']);
	if(empty($new_ing_name)){
		$response['error'] = 'Please provide a name';
		echo json_encode($response);
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$new_ing_name' AND owner_id = '$userID'"))){
		$response['error'] = $new_ing_name.' already exists';
		echo json_encode($response);
		return;
	}
	
	$sql.=mysqli_query($conn, "UPDATE ingredient_compounds SET ing = '$new_ing_name' WHERE ing = '$old_ing_name' AND owner_id = '$userID'");

	$sql.=mysqli_query($conn, "UPDATE ingredients SET name = '$new_ing_name' WHERE name = '$old_ing_name' AND id = '$ing_id' AND owner_id = '$userID'");
	$sql.=mysqli_query($conn, "UPDATE formulas SET ingredient = '$new_ing_name' WHERE ingredient = '$old_ing_name' AND ingredient_id = '$ing_id' AND owner_id = '$userID'");

	if($nID = mysqli_fetch_array(mysqli_query($conn, "SELECT id,name FROM ingredients WHERE name = '$new_ing_name' AND owner_id = '$userID'"))){
		
		$response['success']['msg'] = $old_ing_name.' renamed to <a href="/pages/mgmIngredient.php?id='.$nID['id'].'" >'.$new_ing_name.'</a>';
		$response['success']['id'] = $nID['id'];
		echo json_encode($response);
		return;
	}
	
	return;
}


//FIRST AID INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'faid_info') {
    $ingID = (int)$_POST['ingID'];
    $first_aid_general = trim($_POST['first_aid_general']);
    $first_aid_inhalation = trim($_POST['first_aid_inhalation']);
    $first_aid_skin = trim($_POST['first_aid_skin']);
    $first_aid_eye = trim($_POST['first_aid_eye']);
    $first_aid_ingestion = trim($_POST['first_aid_ingestion']);
    $first_aid_self_protection = trim($_POST['first_aid_self_protection']);
    $first_aid_symptoms = trim($_POST['first_aid_symptoms']);
    $first_aid_dr_notes = trim($_POST['first_aid_dr_notes']);

    // Validate required fields
    $missingFields = [];
    if (empty($ingID)) $missingFields[] = "Ingredient ID";
    if (empty($first_aid_general)) $missingFields[] = "First Aid (General)";
    if (empty($first_aid_inhalation)) $missingFields[] = "First Aid (Inhalation)";
    if (empty($first_aid_skin)) $missingFields[] = "First Aid (Skin)";
    if (empty($first_aid_eye)) $missingFields[] = "First Aid (Eye)";
    if (empty($first_aid_ingestion)) $missingFields[] = "First Aid (Ingestion)";
    if (empty($first_aid_self_protection)) $missingFields[] = "First Aid (Self-Protection)";
    if (empty($first_aid_symptoms)) $missingFields[] = "First Aid (Symptoms)";
    if (empty($first_aid_dr_notes)) $missingFields[] = "First Aid (Doctor Notes)";

    if (!empty($missingFields)) {
        $response["error"] = "The following fields are required: " . implode(", ", $missingFields);
        echo json_encode($response);
        return;
    }

    // Check if the record exists for this `ingID` and `owner_id`
    $checkStmt = $conn->prepare(
        "SELECT COUNT(*) FROM ingredient_safety_data WHERE ingID = ? AND owner_id = ?"
    );
    $checkStmt->bind_param('is', $ingID, $userID);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        // Update existing record
        $stmt = $conn->prepare(
            "UPDATE ingredient_safety_data
            SET 
                first_aid_general = ?, 
                first_aid_inhalation = ?, 
                first_aid_skin = ?, 
                first_aid_eye = ?, 
                first_aid_ingestion = ?, 
                first_aid_self_protection = ?, 
                first_aid_symptoms = ?, 
                first_aid_dr_notes = ?
            WHERE ingID = ? AND owner_id = ?"
        );

        $stmt->bind_param(
            'ssssssssis',
            $first_aid_general,
            $first_aid_inhalation,
            $first_aid_skin,
            $first_aid_eye,
            $first_aid_ingestion,
            $first_aid_self_protection,
            $first_aid_symptoms,
            $first_aid_dr_notes,
            $ingID,
            $userID
        );
    } else {
        // Insert a new record
        $stmt = $conn->prepare(
            "INSERT INTO ingredient_safety_data (
                ingID, first_aid_general, first_aid_inhalation, first_aid_skin, 
                first_aid_eye, first_aid_ingestion, first_aid_self_protection, 
                first_aid_symptoms, first_aid_dr_notes, owner_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'isssssssss',
            $ingID,
            $first_aid_general,
            $first_aid_inhalation,
            $first_aid_skin,
            $first_aid_eye,
            $first_aid_ingestion,
            $first_aid_self_protection,
            $first_aid_symptoms,
            $first_aid_dr_notes,
            $userID
        );
    }

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'First aid data has been successfully saved.';
    } else {
        $errorMessage = "Failed to execute SQL: " . $stmt->error;
        error_log("PV error: $errorMessage");
        $response["error"] = 'Something went wrong. Please check the logs.';
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//FIREFIGHTING
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'fire_info') {
    $ingID = (int)$_POST['ingID'];

    $firefighting_suitable_media = trim($_POST['firefighting_suitable_media']);
    $firefighting_non_suitable_media = trim($_POST['firefighting_non_suitable_media']);
    $firefighting_special_hazards = trim($_POST['firefighting_special_hazards']);
    $firefighting_advice = trim($_POST['firefighting_advice']);
    $firefighting_other_info = trim($_POST['firefighting_other_info']);

    // Validate required fields
    $missingFields = [];
    if (empty($ingID)) $missingFields[] = "Ingredient ID";
    if (empty($firefighting_suitable_media)) $missingFields[] = "Firefighting Suitable Media";
    if (empty($firefighting_non_suitable_media)) $missingFields[] = "Firefighting Non-Suitable Media";
    if (empty($firefighting_special_hazards)) $missingFields[] = "Firefighting Special Hazards";
    if (empty($firefighting_advice)) $missingFields[] = "Firefighting Advice";
    if (empty($firefighting_other_info)) $missingFields[] = "Firefighting Other Info";

    if (!empty($missingFields)) {
        $response["error"] = "The following fields are required: " . implode(", ", $missingFields);
        echo json_encode($response);
        return;
    }

    // Check if the record exists for this `ingID` and `owner_id`
    $checkStmt = $conn->prepare(
        "SELECT COUNT(*) FROM ingredient_safety_data WHERE ingID = ? AND owner_id = ?"
    );
    $checkStmt->bind_param('is', $ingID, $userID);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        // Update existing record
        $stmt = $conn->prepare(
            "UPDATE ingredient_safety_data
            SET 
                firefighting_suitable_media = ?, 
                firefighting_non_suitable_media = ?, 
                firefighting_special_hazards = ?, 
                firefighting_advice = ?, 
                firefighting_other_info = ?
            WHERE ingID = ? AND owner_id = ?"
        );

        $stmt->bind_param(
            'ssssiis',
            $firefighting_suitable_media,
            $firefighting_non_suitable_media,
            $firefighting_special_hazards,
            $firefighting_advice,
            $firefighting_other_info,
            $ingID,
            $userID
        );
    } else {
        // Insert a new record
        $stmt = $conn->prepare(
            "INSERT INTO ingredient_safety_data (
                ingID, firefighting_suitable_media, firefighting_non_suitable_media, 
                firefighting_special_hazards, firefighting_advice, firefighting_other_info, owner_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'issssss',
            $ingID,
            $firefighting_suitable_media,
            $firefighting_non_suitable_media,
            $firefighting_special_hazards,
            $firefighting_advice,
            $firefighting_other_info,
            $userID
        );
    }

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Firefighting data has been successfully saved.';
    } else {
        $errorMessage = "Failed to execute SQL: " . $stmt->error;
        error_log("PV error: $errorMessage");
        $response["error"] = 'Something went wrong. Please check the logs.';
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}



//ACCIDENTAL RELEASE
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'save_acc_rel') {
    $ingID = (int)$_POST['ingID'];
    $accidental_release_per_precautions = trim($_POST['accidental_release_per_precautions']);
    $accidental_release_env_precautions = trim($_POST['accidental_release_env_precautions']);
    $accidental_release_cleaning = trim($_POST['accidental_release_cleaning']);
    $accidental_release_refs = trim($_POST['accidental_release_refs']);
    $accidental_release_other_info = trim($_POST['accidental_release_other_info']);

    // Check if all fields are populated
    if (
        empty($ingID) || empty($accidental_release_per_precautions) || empty($accidental_release_env_precautions) ||
        empty($accidental_release_cleaning) || empty($accidental_release_refs) || empty($accidental_release_other_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Check if the record exists for this `ingID` and `owner_id`
    $checkStmt = $conn->prepare(
        "SELECT COUNT(*) FROM ingredient_safety_data WHERE ingID = ? AND owner_id = ?"
    );
    $checkStmt->bind_param('is', $ingID, $userID);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        // Update existing record
        $stmt = $conn->prepare(
            "UPDATE ingredient_safety_data
            SET 
                accidental_release_per_precautions = ?, 
                accidental_release_env_precautions = ?, 
                accidental_release_cleaning = ?, 
                accidental_release_refs = ?, 
                accidental_release_other_info = ?
            WHERE ingID = ? AND owner_id = ?"
        );

        $stmt->bind_param(
            'ssssiis',
            $accidental_release_per_precautions,
            $accidental_release_env_precautions,
            $accidental_release_cleaning,
            $accidental_release_refs,
            $accidental_release_other_info,
            $ingID,
            $userID
        );
    } else {
        // Insert a new record
        $stmt = $conn->prepare(
            "INSERT INTO ingredient_safety_data (
                ingID, accidental_release_per_precautions, accidental_release_env_precautions, 
                accidental_release_cleaning, accidental_release_refs, accidental_release_other_info, owner_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'issssss',
            $ingID,
            $accidental_release_per_precautions,
            $accidental_release_env_precautions,
            $accidental_release_cleaning,
            $accidental_release_refs,
            $accidental_release_other_info,
            $userID
        );
    }

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Accidental release data has been successfully saved.';
    } else {
        $errorMessage = "Failed to execute SQL: " . $stmt->error;
        error_log("PV error: $errorMessage");
        $response["error"] = 'Something went wrong. Please check the logs.';
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//HANDLING & STORAGE
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'HS') {
    $ingID = (int)$_POST['ingID'];
    $handling_protection = trim($_POST['handling_protection']);
    $handling_hygiene = trim($_POST['handling_hygiene']);
    $handling_safe_storage = trim($_POST['handling_safe_storage']);
    $handling_joint_storage = trim($_POST['handling_joint_storage']);
    $handling_specific_uses = trim($_POST['handling_specific_uses']);

    // Check if all fields are populated
    if (
        empty($ingID) || empty($handling_protection) || empty($handling_hygiene) ||
        empty($handling_safe_storage) || empty($handling_joint_storage) || empty($handling_specific_uses)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Check if the record exists for this `ingID` and `owner_id`
    $checkStmt = $conn->prepare(
        "SELECT COUNT(*) FROM ingredient_safety_data WHERE ingID = ? AND owner_id = ?"
    );
    $checkStmt->bind_param('is', $ingID, $userID);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        // Update existing record
        $stmt = $conn->prepare(
            "UPDATE ingredient_safety_data
            SET 
                handling_protection = ?, 
                handling_hygiene = ?, 
                handling_safe_storage = ?, 
                handling_joint_storage = ?, 
                handling_specific_uses = ?
            WHERE ingID = ? AND owner_id = ?"
        );

        $stmt->bind_param(
            'ssssiis',
            $handling_protection,
            $handling_hygiene,
            $handling_safe_storage,
            $handling_joint_storage,
            $handling_specific_uses,
            $ingID,
            $userID
        );
    } else {
        // Insert a new record
        $stmt = $conn->prepare(
            "INSERT INTO ingredient_safety_data (
                ingID, handling_protection, handling_hygiene, 
                handling_safe_storage, handling_joint_storage, handling_specific_uses, owner_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'issssss',
            $ingID,
            $handling_protection,
            $handling_hygiene,
            $handling_safe_storage,
            $handling_joint_storage,
            $handling_specific_uses,
            $userID
        );
    }

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Handling and storage data has been successfully saved.';
    } else {
        $errorMessage = "Failed to execute SQL: " . $stmt->error;
        error_log("PV error: $errorMessage");
        $response["error"] = 'Something went wrong. Please check the logs.';
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}

//EXPOSURE DATA
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'exposure_data') {
    $ingID = (int)$_POST['ingID'];

    // New fields for exposure data
    $exposure_occupational_limits = trim($_POST['exposure_occupational_limits']);
    $exposure_biological_limits = trim($_POST['exposure_biological_limits']);
    $exposure_intented_use_limits = trim($_POST['exposure_intented_use_limits']);
    $exposure_other_remarks = trim($_POST['exposure_other_remarks']);
    $exposure_face_protection = trim($_POST['exposure_face_protection']);
    $exposure_skin_protection = trim($_POST['exposure_skin_protection']);
    $exposure_respiratory_protection = trim($_POST['exposure_respiratory_protection']);
    $exposure_env_exposure = trim($_POST['exposure_env_exposure']);
    $exposure_consumer_exposure = trim($_POST['exposure_consumer_exposure']);
    $exposure_other_info = trim($_POST['exposure_other_info']);

    // Check if all fields are populated
    if (
        empty($ingID) || empty($exposure_occupational_limits) || empty($exposure_biological_limits) || 
        empty($exposure_intented_use_limits) || empty($exposure_other_remarks) || empty($exposure_face_protection) || 
        empty($exposure_skin_protection) || empty($exposure_respiratory_protection) || empty($exposure_env_exposure) || 
        empty($exposure_consumer_exposure) || empty($exposure_other_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Check if the record exists for this `ingID` and `owner_id`
    $checkStmt = $conn->prepare(
        "SELECT COUNT(*) FROM ingredient_safety_data WHERE ingID = ? AND owner_id = ?"
    );
    $checkStmt->bind_param('is', $ingID, $userID);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        // Update existing record
        $stmt = $conn->prepare(
            "UPDATE ingredient_safety_data
            SET 
                exposure_occupational_limits = ?, 
                exposure_biological_limits = ?, 
                exposure_intented_use_limits = ?, 
                exposure_other_remarks = ?, 
                exposure_face_protection = ?, 
                exposure_skin_protection = ?, 
                exposure_respiratory_protection = ?, 
                exposure_env_exposure = ?, 
                exposure_consumer_exposure = ?, 
                exposure_other_info = ?
            WHERE ingID = ? AND owner_id = ?"
        );

        $stmt->bind_param(
            'ssssssssssis',
            $exposure_occupational_limits,
            $exposure_biological_limits,
            $exposure_intented_use_limits,
            $exposure_other_remarks,
            $exposure_face_protection,
            $exposure_skin_protection,
            $exposure_respiratory_protection,
            $exposure_env_exposure,
            $exposure_consumer_exposure,
            $exposure_other_info,
            $ingID,
            $userID
        );
    } else {
        // Insert a new record
        $stmt = $conn->prepare(
            "INSERT INTO ingredient_safety_data (
                ingID, exposure_occupational_limits, exposure_biological_limits, 
                exposure_intented_use_limits, exposure_other_remarks, 
                exposure_face_protection, exposure_skin_protection, 
                exposure_respiratory_protection, exposure_env_exposure, 
                exposure_consumer_exposure, exposure_other_info, owner_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'isssssssssis',
            $ingID,
            $exposure_occupational_limits,
            $exposure_biological_limits,
            $exposure_intented_use_limits,
            $exposure_other_remarks,
            $exposure_face_protection,
            $exposure_skin_protection,
            $exposure_respiratory_protection,
            $exposure_env_exposure,
            $exposure_consumer_exposure,
            $exposure_other_info,
            $userID
        );
    }

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Exposure data has been successfully saved.';
    } else {
        $errorMessage = "Failed to execute SQL: " . $stmt->error;
        error_log("PV error: $errorMessage");
        $response["error"] = 'Something went wrong. Please check the logs.';
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//PCP
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'pcp') {
    $ingID = (int)$_POST['ingID'];

    // Gather and trim all inputs
    $odor_threshold = trim($_POST['odor_threshold']);
    $pH = trim($_POST['pH']);
    $melting_point = trim($_POST['melting_point']);
    $boiling_point = trim($_POST['boiling_point']);
    $flash_point = trim($_POST['flash_point']);
    $evaporation_rate = trim($_POST['evaporation_rate']);
    $solubility = trim($_POST['solubility']);
    $auto_infl_temp = trim($_POST['auto_infl_temp']);
    $decomp_temp = trim($_POST['decomp_temp']);
    $viscosity = trim($_POST['viscosity']);
    $explosive_properties = trim($_POST['explosive_properties']);
    $oxidising_properties = trim($_POST['oxidising_properties']);
    $particle_chars = trim($_POST['particle_chars']);
    $flammability = trim($_POST['flammability']);
    $logP = trim($_POST['logP']);
    $soluble = trim($_POST['soluble']);
    $color = trim($_POST['color']);
    $low_flammability_limit = trim($_POST['low_flammability_limit']);
    $vapour_pressure = trim($_POST['vapour_pressure']);
    $vapour_density = trim($_POST['vapour_density']);
    $relative_density = trim($_POST['relative_density']);
    $pcp_other_info = trim($_POST['pcp_other_info']);
    $pcp_other_sec_info = trim($_POST['pcp_other_sec_info']);

    // Check if all fields are populated
    if (
        empty($ingID) || empty($odor_threshold) || empty($pH) || 
        empty($melting_point) || empty($boiling_point) || empty($flash_point) || 
        empty($evaporation_rate) || empty($solubility) || empty($auto_infl_temp) || 
        empty($decomp_temp) || empty($viscosity) || empty($explosive_properties) || 
        empty($oxidising_properties) || empty($particle_chars) || empty($flammability) || 
        empty($logP) || empty($soluble) || empty($color) || empty($low_flammability_limit) || 
        empty($vapour_pressure) || empty($vapour_density) || empty($relative_density) || 
        empty($pcp_other_info) || empty($pcp_other_sec_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Check if the record exists for this `ingID` and `owner_id`
    $checkStmt = $conn->prepare(
        "SELECT COUNT(*) FROM ingredient_safety_data WHERE ingID = ? AND owner_id = ?"
    );
    $checkStmt->bind_param('is', $ingID, $userID);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        // Update existing record
        $stmt = $conn->prepare(
            "UPDATE ingredient_safety_data
            SET 
                odor_threshold = ?, pH = ?, melting_point = ?, boiling_point = ?, flash_point = ?, 
                evaporation_rate = ?, solubility = ?, auto_infl_temp = ?, decomp_temp = ?, 
                viscosity = ?, explosive_properties = ?, oxidising_properties = ?, 
                particle_chars = ?, flammability = ?, logP = ?, soluble = ?, 
                color = ?, low_flammability_limit = ?, vapour_pressure = ?, 
                vapour_density = ?, relative_density = ?, pcp_other_info = ?, 
                pcp_other_sec_info = ?
            WHERE ingID = ? AND owner_id = ?"
        );

		$stmt->bind_param(
			'sssssssssssssssssssssssis', // 25 placeholders
			$odor_threshold, $pH, $melting_point, $boiling_point, $flash_point, 
			$evaporation_rate, $solubility, $auto_infl_temp, $decomp_temp, 
			$viscosity, $explosive_properties, $oxidising_properties, $particle_chars, 
			$flammability, $logP, $soluble, $color, $low_flammability_limit, 
			$vapour_pressure, $vapour_density, $relative_density, $pcp_other_info, 
			$pcp_other_sec_info, $ingID, $userID
		);
		
    } else {
        // Insert a new record
        $stmt = $conn->prepare(
            "INSERT INTO ingredient_safety_data (
                ingID, odor_threshold, pH, melting_point, boiling_point, flash_point, 
                evaporation_rate, solubility, auto_infl_temp, decomp_temp, viscosity, 
                explosive_properties, oxidising_properties, particle_chars, flammability, 
                logP, soluble, color, low_flammability_limit, vapour_pressure, 
                vapour_density, relative_density, pcp_other_info, pcp_other_sec_info, owner_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'issssssssssssssssssssssss', 
            $ingID, $odor_threshold, $pH, $melting_point, $boiling_point, 
            $flash_point, $evaporation_rate, $solubility, $auto_infl_temp, $decomp_temp, 
            $viscosity, $explosive_properties, $oxidising_properties, $particle_chars, 
            $flammability, $logP, $soluble, $color, $low_flammability_limit, $vapour_pressure, 
            $vapour_density, $relative_density, $pcp_other_info, $pcp_other_sec_info, $userID
        );
    }

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Physico-chemical properties data has been successfully saved.';
    } else {
        $errorMessage = "Failed to execute SQL: " . $stmt->error;
        error_log("PV error: $errorMessage");
        $response["error"] = 'Something went wrong. Please check the logs.';
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//SR INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'sr_info') {
    $ingID = (int)$_POST['ingID'];
    $stabillity_reactivity = $_POST['stabillity_reactivity'];
    $stabillity_chemical = $_POST['stabillity_chemical'];
    $stabillity_reactions = $_POST['stabillity_reactions'];
    $stabillity_avoid = $_POST['stabillity_avoid'];
    $stabillity_incompatibility = $_POST['stabillity_incompatibility'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($stabillity_reactivity) || empty($stabillity_chemical) || 
        empty($stabillity_reactions) || empty($stabillity_avoid) || empty($stabillity_incompatibility)
    ) {
        error_log("SR Info Error: Missing required fields for ingID: $ingID");
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Check if the entry exists
    $checkStmt = $conn->prepare(
        "SELECT COUNT(*) FROM ingredient_safety_data WHERE ingID = ? AND owner_id = ?"
    );
    $checkStmt->bind_param('is', $ingID, $userID);

    if (!$checkStmt->execute()) {
        error_log("SR Info Error: Failed to check existence for ingID: $ingID, Error: " . $checkStmt->error);
        $response["error"] = 'Something went wrong while checking data';
        echo json_encode($response);
        return;
    }

    $checkStmt->bind_result($exists);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($exists) {
        // Update the existing entry
        $stmt = $conn->prepare(
            "UPDATE ingredient_safety_data 
             SET stabillity_reactivity = ?, stabillity_chemical = ?, stabillity_reactions = ?, 
                 stabillity_avoid = ?, stabillity_incompatibility = ? 
             WHERE ingID = ? AND owner_id = ?"
        );

        if (!$stmt) {
            error_log("SR Info Error: Prepare failed for update on ingID: $ingID, Error: " . $conn->error);
            $response["error"] = 'Prepare failed: ' . $conn->error;
            echo json_encode($response);
            return;
        }

        $stmt->bind_param(
            'sssssss', $stabillity_reactivity, $stabillity_chemical, $stabillity_reactions,
            $stabillity_avoid, $stabillity_incompatibility, $ingID, $userID
        );
    } else {
        // Insert a new entry
        $stmt = $conn->prepare(
            "INSERT INTO ingredient_safety_data (
                ingID, stabillity_reactivity, stabillity_chemical, stabillity_reactions, 
                stabillity_avoid, stabillity_incompatibility, owner_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            error_log("SR Info Error: Prepare failed for insert on ingID: $ingID, Error: " . $conn->error);
            $response["error"] = 'Prepare failed: ' . $conn->error;
            echo json_encode($response);
            return;
        }

        $stmt->bind_param(
            'issssss', $ingID, $stabillity_reactivity, $stabillity_chemical, $stabillity_reactions,
            $stabillity_avoid, $stabillity_incompatibility, $userID
        );
    }

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Stability and reactivity data has been successfully updated';
    } else {
        error_log("SR Info Error: Execution failed for ingID: $ingID, Error: " . $stmt->error);
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}



//TOXICOLOGICAL INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'tx_info') {
    $ingID = (int)$_POST['ingID'];

    $toxicological_acute_oral = $_POST['toxicological_acute_oral'];
    $toxicological_acute_dermal = $_POST['toxicological_acute_dermal'];
    $toxicological_acute_inhalation = $_POST['toxicological_acute_inhalation'];
    $toxicological_skin = $_POST['toxicological_skin'];
    $toxicological_eye = $_POST['toxicological_eye'];
    $toxicological_sensitisation = $_POST['toxicological_sensitisation'];
    $toxicological_organ_repeated = $_POST['toxicological_organ_repeated'];
    $toxicological_organ_single = $_POST['toxicological_organ_single'];
    $toxicological_carcinogencity = $_POST['toxicological_carcinogencity'];
    $toxicological_reproductive = $_POST['toxicological_reproductive'];
    $toxicological_cell_mutation = $_POST['toxicological_cell_mutation'];
    $toxicological_resp_tract = $_POST['toxicological_resp_tract'];
    $toxicological_other_info = $_POST['toxicological_other_info'];
    $toxicological_other_hazards = $_POST['toxicological_other_hazards'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($toxicological_acute_oral) || empty($toxicological_acute_dermal) ||
        empty($toxicological_acute_inhalation) || empty($toxicological_skin) || empty($toxicological_eye) ||
        empty($toxicological_sensitisation) || empty($toxicological_organ_repeated) || empty($toxicological_organ_single) ||
        empty($toxicological_carcinogencity) || empty($toxicological_reproductive) || empty($toxicological_cell_mutation) ||
        empty($toxicological_resp_tract) || empty($toxicological_other_info) || empty($toxicological_other_hazards)
    ) {
        error_log("Toxicological Info Error: Missing required fields for ingID: $ingID");
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Check if the entry exists
    $checkStmt = $conn->prepare(
        "SELECT COUNT(*) FROM ingredient_safety_data WHERE ingID = ? AND owner_id = ?"
    );
    $checkStmt->bind_param('is', $ingID, $userID);

    if (!$checkStmt->execute()) {
        error_log("Toxicological Info Error: Failed to check existence for ingID: $ingID, Error: " . $checkStmt->error);
        $response["error"] = 'Something went wrong while checking data';
        echo json_encode($response);
        return;
    }

    $checkStmt->bind_result($exists);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($exists) {
        // Update the existing entry
        $stmt = $conn->prepare(
            "UPDATE ingredient_safety_data 
             SET toxicological_acute_oral = ?, toxicological_acute_dermal = ?, toxicological_acute_inhalation = ?, 
                 toxicological_skin = ?, toxicological_eye = ?, toxicological_sensitisation = ?, 
                 toxicological_organ_repeated = ?, toxicological_organ_single = ?, toxicological_carcinogencity = ?, 
                 toxicological_reproductive = ?, toxicological_cell_mutation = ?, toxicological_resp_tract = ?, 
                 toxicological_other_info = ?, toxicological_other_hazards = ?
             WHERE ingID = ? AND owner_id = ?"
        );

        if (!$stmt) {
            error_log("Toxicological Info Error: Prepare failed for update on ingID: $ingID, Error: " . $conn->error);
            $response["error"] = 'Prepare failed: ' . $conn->error;
            echo json_encode($response);
            return;
        }

        $stmt->bind_param(
            'ssssssssssssssis', $toxicological_acute_oral, $toxicological_acute_dermal, 
            $toxicological_acute_inhalation, $toxicological_skin, $toxicological_eye, 
            $toxicological_sensitisation, $toxicological_organ_repeated, $toxicological_organ_single, 
            $toxicological_carcinogencity, $toxicological_reproductive, $toxicological_cell_mutation, 
            $toxicological_resp_tract, $toxicological_other_info, $toxicological_other_hazards, 
            $ingID, $userID
        );
    } else {
        // Insert a new entry
        $stmt = $conn->prepare(
            "INSERT INTO ingredient_safety_data (
                ingID, toxicological_acute_oral, toxicological_acute_dermal, toxicological_acute_inhalation, 
                toxicological_skin, toxicological_eye, toxicological_sensitisation, toxicological_organ_repeated, 
                toxicological_organ_single, toxicological_carcinogencity, toxicological_reproductive, 
                toxicological_cell_mutation, toxicological_resp_tract, toxicological_other_info, 
                toxicological_other_hazards, owner_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            error_log("Toxicological Info Error: Prepare failed for insert on ingID: $ingID, Error: " . $conn->error);
            $response["error"] = 'Prepare failed: ' . $conn->error;
            echo json_encode($response);
            return;
        }

        $stmt->bind_param(
            'isssssssssssssis', $ingID, $toxicological_acute_oral, $toxicological_acute_dermal, 
            $toxicological_acute_inhalation, $toxicological_skin, $toxicological_eye, 
            $toxicological_sensitisation, $toxicological_organ_repeated, $toxicological_organ_single, 
            $toxicological_carcinogencity, $toxicological_reproductive, $toxicological_cell_mutation, 
            $toxicological_resp_tract, $toxicological_other_info, $toxicological_other_hazards, 
            $userID
        );
    }

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Toxicology data has been successfully updated';
    } else {
        error_log("Toxicological Info Error: Execution failed for ingID: $ingID, Error: " . $stmt->error);
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//ECOLOGICAL INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'ec_info') {
    $ingID = (int)$_POST['ingID'];

    $ecological_toxicity = $_POST['ecological_toxicity'];
    $ecological_persistence = $_POST['ecological_persistence'];
    $ecological_bioaccumulative = $_POST['ecological_bioaccumulative'];
    $ecological_soil_mobility = $_POST['ecological_soil_mobility'];
    $ecological_PBT_vPvB = $_POST['ecological_PBT_vPvB'];
    $ecological_endocrine_properties = $_POST['ecological_endocrine_properties'];
    $ecological_other_adv_effects = $_POST['ecological_other_adv_effects'];
    $ecological_additional_ecotoxicological_info = $_POST['ecological_additional_ecotoxicological_info'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($ecological_toxicity) || empty($ecological_persistence) || 
        empty($ecological_bioaccumulative) || empty($ecological_soil_mobility) || 
        empty($ecological_PBT_vPvB) || empty($ecological_endocrine_properties) || 
        empty($ecological_other_adv_effects) || empty($ecological_additional_ecotoxicological_info)
    ) {
        error_log("EC Info Error: Missing required fields for ingID: $ingID");
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Check if the entry exists
    $checkStmt = $conn->prepare(
        "SELECT COUNT(*) FROM ingredient_safety_data WHERE ingID = ? AND owner_id = ?"
    );
    $checkStmt->bind_param('is', $ingID, $userID);

    if (!$checkStmt->execute()) {
        error_log("EC Info Error: Failed to check existence for ingID: $ingID, Error: " . $checkStmt->error);
        $response["error"] = 'Something went wrong while checking data';
        echo json_encode($response);
        return;
    }

    $checkStmt->bind_result($exists);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($exists) {
        // Update the existing entry
        $stmt = $conn->prepare(
            "UPDATE ingredient_safety_data 
             SET ecological_toxicity = ?, ecological_persistence = ?, ecological_bioaccumulative = ?, 
                 ecological_soil_mobility = ?, ecological_PBT_vPvB = ?, ecological_endocrine_properties = ?, 
                 ecological_other_adv_effects = ?, ecological_additional_ecotoxicological_info = ?
             WHERE ingID = ? AND owner_id = ?"
        );

        if (!$stmt) {
            error_log("EC Info Error: Prepare failed for update on ingID: $ingID, Error: " . $conn->error);
            $response["error"] = 'Prepare failed: ' . $conn->error;
            echo json_encode($response);
            return;
        }

        $stmt->bind_param(
            'sssssssiis', $ecological_toxicity, $ecological_persistence, $ecological_bioaccumulative, 
            $ecological_soil_mobility, $ecological_PBT_vPvB, $ecological_endocrine_properties, 
            $ecological_other_adv_effects, $ecological_additional_ecotoxicological_info, 
            $ingID, $userID
        );
    } else {
        // Insert a new entry
        $stmt = $conn->prepare(
            "INSERT INTO ingredient_safety_data (
                ingID, ecological_toxicity, ecological_persistence, ecological_bioaccumulative, 
                ecological_soil_mobility, ecological_PBT_vPvB, ecological_endocrine_properties, 
                ecological_other_adv_effects, ecological_additional_ecotoxicological_info, owner_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            error_log("EC Info Error: Prepare failed for insert on ingID: $ingID, Error: " . $conn->error);
            $response["error"] = 'Prepare failed: ' . $conn->error;
            echo json_encode($response);
            return;
        }

        $stmt->bind_param(
            'isssssssss', $ingID, $ecological_toxicity, $ecological_persistence, 
            $ecological_bioaccumulative, $ecological_soil_mobility, $ecological_PBT_vPvB, 
            $ecological_endocrine_properties, $ecological_other_adv_effects, 
            $ecological_additional_ecotoxicological_info, $userID
        );
    }

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Ecology data has been successfully updated';
    } else {
        error_log("EC Info Error: Execution failed for ingID: $ingID, Error: " . $stmt->error);
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//DISPOSE INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'dis_info') {
    $ingID = (int)$_POST['ingID'];

    $disposal_product = $_POST['disposal_product'];
    $disposal_remarks = $_POST['disposal_remarks'];

    // Check if all fields are populated
    if (empty($ingID) || empty($disposal_product) || empty($disposal_remarks)) {
        error_log("Disposal Info Error: Missing required fields for ingID: $ingID");
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Check if the entry exists
    $checkStmt = $conn->prepare(
        "SELECT COUNT(*) FROM ingredient_safety_data WHERE ingID = ? AND owner_id = ?"
    );
    $checkStmt->bind_param('is', $ingID, $userID);

    if (!$checkStmt->execute()) {
        error_log("Disposal Info Error: Failed to check existence for ingID: $ingID, Error: " . $checkStmt->error);
        $response["error"] = 'Something went wrong while checking data';
        echo json_encode($response);
        return;
    }

    $checkStmt->bind_result($exists);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($exists) {
        // Update the existing entry
        $stmt = $conn->prepare(
            "UPDATE ingredient_safety_data 
             SET disposal_product = ?, disposal_remarks = ?
             WHERE ingID = ? AND owner_id = ?"
        );

        if (!$stmt) {
            error_log("Disposal Info Error: Prepare failed for update on ingID: $ingID, Error: " . $conn->error);
            $response["error"] = 'Prepare failed: ' . $conn->error;
            echo json_encode($response);
            return;
        }

        $stmt->bind_param(
            'ssis', $disposal_product, $disposal_remarks, $ingID, $userID
        );
    } else {
        // Insert a new entry
        $stmt = $conn->prepare(
            "INSERT INTO ingredient_safety_data (
                ingID, disposal_product, disposal_remarks, owner_id
            ) VALUES (?, ?, ?, ?)"
        );

        if (!$stmt) {
            error_log("Disposal Info Error: Prepare failed for insert on ingID: $ingID, Error: " . $conn->error);
            $response["error"] = 'Prepare failed: ' . $conn->error;
            echo json_encode($response);
            return;
        }

        $stmt->bind_param(
            'isss', $ingID, $disposal_product, $disposal_remarks, $userID
        );
    }

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Disposal data has been successfully updated';
    } else {
        error_log("Disposal Info Error: Execution failed for ingID: $ingID, Error: " . $stmt->error);
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//TRANSPORTATION INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'trans_info') {
    $ingID = (int)$_POST['ingID'];

    $transport_un_number = $_POST['transport_un_number'];
    $transport_shipping_name = $_POST['transport_shipping_name'];
    $transport_hazard_class = $_POST['transport_hazard_class'];
    $transport_packing_group = $_POST['transport_packing_group'];
    $transport_env_hazards = $_POST['transport_env_hazards'];
    $transport_precautions = $_POST['transport_precautions'];
    $transport_bulk_shipping = $_POST['transport_bulk_shipping'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($transport_un_number) || empty($transport_shipping_name) || 
        empty($transport_hazard_class) || empty($transport_packing_group) || 
        empty($transport_env_hazards) || empty($transport_precautions) || 
        empty($transport_bulk_shipping)
    ) {
        error_log("Transportation Info Error: Missing required fields for ingID: $ingID");
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Check if the entry exists
    $checkStmt = $conn->prepare(
        "SELECT COUNT(*) FROM ingredient_safety_data WHERE ingID = ? AND owner_id = ?"
    );
    $checkStmt->bind_param('is', $ingID, $userID);

    if (!$checkStmt->execute()) {
        error_log("Transportation Info Error: Failed to check existence for ingID: $ingID, Error: " . $checkStmt->error);
        $response["error"] = 'Something went wrong while checking data';
        echo json_encode($response);
        return;
    }

    $checkStmt->bind_result($exists);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($exists) {
        // Update the existing entry
        $stmt = $conn->prepare(
            "UPDATE ingredient_safety_data 
             SET transport_un_number = ?, transport_shipping_name = ?, transport_hazard_class = ?, 
                 transport_packing_group = ?, transport_env_hazards = ?, transport_precautions = ?, 
                 transport_bulk_shipping = ?
             WHERE ingID = ? AND owner_id = ?"
        );

        if (!$stmt) {
            error_log("Transportation Info Error: Prepare failed for update on ingID: $ingID, Error: " . $conn->error);
            $response["error"] = 'Prepare failed: ' . $conn->error;
            echo json_encode($response);
            return;
        }

        $stmt->bind_param(
            'sssssssis', $transport_un_number, $transport_shipping_name, $transport_hazard_class, 
            $transport_packing_group, $transport_env_hazards, $transport_precautions, $transport_bulk_shipping, 
            $ingID, $userID
        );
    } else {
        // Insert a new entry
        $stmt = $conn->prepare(
            "INSERT INTO ingredient_safety_data (
                ingID, transport_un_number, transport_shipping_name, transport_hazard_class, 
                transport_packing_group, transport_env_hazards, transport_precautions, transport_bulk_shipping, owner_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            error_log("Transportation Info Error: Prepare failed for insert on ingID: $ingID, Error: " . $conn->error);
            $response["error"] = 'Prepare failed: ' . $conn->error;
            echo json_encode($response);
            return;
        }

        $stmt->bind_param(
            'issssssss', $ingID, $transport_un_number, $transport_shipping_name, $transport_hazard_class, 
            $transport_packing_group, $transport_env_hazards, $transport_precautions, 
            $transport_bulk_shipping, $userID
        );
    }

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Transportation data has been successfully updated';
    } else {
        error_log("Transportation Info Error: Execution failed for ingID: $ingID, Error: " . $stmt->error);
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//LEGISLATION INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'leg_info') {
    $ingID = (int)$_POST['ingID'];

    $legislation_safety = $_POST['legislation_safety'];
    $legislation_eu = $_POST['legislation_eu'];
    $legislation_chemical_safety_assessment = $_POST['legislation_chemical_safety_assessment'];
    $legislation_other_info = $_POST['legislation_other_info'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($legislation_safety) || empty($legislation_eu) || 
        empty($legislation_chemical_safety_assessment) || empty($legislation_other_info)
    ) {
        error_log("Legislation Info Error: Missing required fields for ingID: $ingID");
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Check if the entry exists
    $checkStmt = $conn->prepare(
        "SELECT COUNT(*) FROM ingredient_safety_data WHERE ingID = ? AND owner_id = ?"
    );
    $checkStmt->bind_param('is', $ingID, $userID);

    if (!$checkStmt->execute()) {
        error_log("Legislation Info Error: Failed to check existence for ingID: $ingID, Error: " . $checkStmt->error);
        $response["error"] = 'Something went wrong while checking data';
        echo json_encode($response);
        return;
    }

    $checkStmt->bind_result($exists);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($exists) {
        // Update the existing entry
        $stmt = $conn->prepare(
            "UPDATE ingredient_safety_data 
             SET legislation_safety = ?, legislation_eu = ?, legislation_chemical_safety_assessment = ?, 
                 legislation_other_info = ?
             WHERE ingID = ? AND owner_id = ?"
        );

        if (!$stmt) {
            error_log("Legislation Info Error: Prepare failed for update on ingID: $ingID, Error: " . $conn->error);
            $response["error"] = 'Prepare failed: ' . $conn->error;
            echo json_encode($response);
            return;
        }

        $stmt->bind_param(
            'ssssis', $legislation_safety, $legislation_eu, $legislation_chemical_safety_assessment, 
            $legislation_other_info, $ingID, $userID
        );
    } else {
        // Insert a new entry
        $stmt = $conn->prepare(
            "INSERT INTO ingredient_safety_data (
                ingID, legislation_safety, legislation_eu, legislation_chemical_safety_assessment, 
                legislation_other_info, owner_id
            ) VALUES (?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            error_log("Legislation Info Error: Prepare failed for insert on ingID: $ingID, Error: " . $conn->error);
            $response["error"] = 'Prepare failed: ' . $conn->error;
            echo json_encode($response);
            return;
        }

        $stmt->bind_param(
            'isssss', $ingID, $legislation_safety, $legislation_eu, $legislation_chemical_safety_assessment, 
            $legislation_other_info, $userID
        );
    }

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Legislation data has been successfully updated';
    } else {
        error_log("Legislation Info Error: Execution failed for ingID: $ingID, Error: " . $stmt->error);
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//ADDITIONAL INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'add_info') {
    $ingID = (int)$_POST['ingID'];

	$add_info_changes = $_POST['add_info_changes'];
    $add_info_acronyms = $_POST['add_info_acronyms'];
    $add_info_references = $_POST['add_info_references'];
    $add_info_HazCom = $_POST['add_info_HazCom'];
    $add_info_GHS = $_POST['add_info_GHS'];
    $add_info_training = $_POST['add_info_training'];
    $add_info_other = $_POST['add_info_other'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($add_info_changes) || empty($add_info_acronyms) || empty($add_info_references) || 
        empty($add_info_HazCom) || empty($add_info_GHS) || empty($add_info_training) || empty($add_info_other) || empty($userID)
    ) {
        error_log("Add Info Error: Missing required fields for ingID: $ingID");
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Check if the entry exists (based on ingID)
    $checkStmt = $conn->prepare(
        "SELECT COUNT(*) FROM ingredient_safety_data WHERE ingID = ?"
    );
    $checkStmt->bind_param('i', $ingID);

    if (!$checkStmt->execute()) {
        error_log("Add Info Error: Failed to check existence for ingID: $ingID, Error: " . $checkStmt->error);
        $response["error"] = 'Something went wrong while checking data';
        echo json_encode($response);
        return;
    }

    $checkStmt->bind_result($exists);
    $checkStmt->fetch();
    $checkStmt->close();

    // Prepare SQL for update or insert
    if ($exists) {
        // Update existing record
        $stmt = $conn->prepare(
            "UPDATE ingredient_safety_data 
             SET add_info_changes = ?, add_info_acronyms = ?, add_info_references = ?, add_info_HazCom = ?, 
                 add_info_GHS = ?, add_info_training = ?, add_info_other = ?, owner_id = ?
             WHERE ingID = ?"
        );
    } else {
        // Insert new record
        $stmt = $conn->prepare(
            "INSERT INTO ingredient_safety_data (
                ingID, add_info_changes, add_info_acronyms, add_info_references, add_info_HazCom, add_info_GHS, 
                add_info_training, add_info_other, owner_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
    }

    if (!$stmt) {
        error_log("Add Info Error: Prepare failed for ingID: $ingID, Error: " . $conn->error);
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind parameters
    if ($exists) {
        $stmt->bind_param(
            'sssssssss', $add_info_changes, $add_info_acronyms, $add_info_references, $add_info_HazCom, 
            $add_info_GHS, $add_info_training, $add_info_other, $userID, $ingID
        );
    } else {
        $stmt->bind_param(
            'issssssss', $ingID, $add_info_changes, $add_info_acronyms, $add_info_references, $add_info_HazCom, 
            $add_info_GHS, $add_info_training, $add_info_other, $userID
        );
    }

    // Execute statement
    if ($stmt->execute()) {
        $response["success"] = 'Additional info data has been updated';
    } else {
        error_log("Add Info Error: Execution failed for ingID: $ingID, Error: " . $stmt->error);
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//IMPORT MARKETPLACE FORMULA
if ($_POST['action'] == 'import' && $_POST['kind'] == 'formula') {
	require_once(__ROOT__.'/func/genFID.php');

    // Sanitize the inputs
    $id = mysqli_real_escape_string($conn, $_POST['fid']);
	$fid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');

    $jAPI = $pvLibraryAPI . '?request=MarketPlace&action=get&id=' . $id;
    $jsonData = json_decode(pv_file_get_contents($jAPI), true);

    // Check for errors in the API response
    if ($jsonData['error']) {
        $response['error'] = 'Error: ' . $jsonData['error']['msg'];
        echo json_encode($response);
        return;
    }

    // Check if the formula has already been downloaded by the user
    $sql_check = "SELECT fid FROM formulasMetaData WHERE name = ? AND src = 1 AND owner_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $jsonData['meta']['name'], $userID);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        $response['error'] = 'Formula name ' . $jsonData['meta']['name'] . ' already downloaded. If you want to re-download it, please remove it first.';
        echo json_encode($response);
        $stmt_check->close();
        return;
    }
    $stmt_check->close();

    // Prepare and execute the insert statement for the formula metadata
    $sql_insert = "INSERT INTO formulasMetaData 
        (name, product_name, fid, profile, gender, notes, defView, catClass, finalType, status, src, owner_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)";
    
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("sssssssssss", 
        $jsonData['meta']['name'], $jsonData['meta']['product_name'], $fid, 
        $jsonData['meta']['profile'], $jsonData['meta']['gender'], $jsonData['meta']['notes'], 
        $jsonData['meta']['defView'], $jsonData['meta']['catClass'], $jsonData['meta']['finalType'], 
        $jsonData['meta']['status'], $userID);
    
    if (!$stmt_insert->execute()) {
        $response['error'] = 'Unable to import the formula metadata: ' . $stmt_insert->error;
        echo json_encode($response);
        $stmt_insert->close();
        return;
    }

    $last_id = $stmt_insert->insert_id;
    $stmt_insert->close();

    // Insert the formula source tag
    $source = $jsonData['meta']['source'];
    $stmt_tag = $conn->prepare("INSERT INTO formulasTags (formula_id, tag_name, owner_id) VALUES (?, ?, ?)");
    $stmt_tag->bind_param("iss", $last_id, $source, $userID);
    if (!$stmt_tag->execute()) {
        $response['error'] = 'Unable to insert formula source tag: ' . $stmt_tag->error;
        echo json_encode($response);
        $stmt_tag->close();
        return;
    }
    $stmt_tag->close();

    // Process the formula ingredients
    $array_data = $jsonData['formula'];
    foreach ($array_data as $row) {
        // Sanitize the ingredient data
        $ingredient = mysqli_real_escape_string($conn, $row['ingredient']);
        
        // Check if the ingredient exists in the database
        $stmt_get_ing = $conn->prepare("SELECT id FROM ingredients WHERE name = ? AND owner_id = ?");
        $stmt_get_ing->bind_param("ss", $ingredient, $userID);
        $stmt_get_ing->execute();
        $stmt_get_ing->store_result();
        
        if ($stmt_get_ing->num_rows == 0) {
            // Insert the ingredient if it doesn't exist
            $stmt_insert_ing = $conn->prepare("INSERT INTO ingredients (name, owner_id) VALUES (?, ?)");
            $stmt_insert_ing->bind_param("ss", $ingredient, $userID);
            if (!$stmt_insert_ing->execute()) {
                $response['error'] = 'Unable to insert ingredient: ' . $stmt_insert_ing->error;
                echo json_encode($response);
                $stmt_insert_ing->close();
                $stmt_get_ing->close();
                return;
            }
            $ingredient_id = $stmt_insert_ing->insert_id;
            $stmt_insert_ing->close();
        } else {
            // Retrieve the existing ingredient ID
            $stmt_get_ing->bind_result($ingredient_id);
            $stmt_get_ing->fetch();
        }
        $stmt_get_ing->close();

        // Prepare the insert query for formula details
        $stmt_formula = $conn->prepare(
            "INSERT INTO formulas (fid, name, ingredient_id, ingredient, concentration, dilutant, quantity, notes, owner_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt_formula->bind_param("ssissssss", 
            $fid, $jsonData['meta']['name'], $ingredient_id, $ingredient, 
            $row['concentration'], $row['dilutant'], $row['quantity'], $row['notes'], $userID);

        // Execute the formula insert
        if (!$stmt_formula->execute()) {
            $response['error'] = 'Unable to insert formula details: ' . $stmt_formula->error;
            echo json_encode($response);
            $stmt_formula->close();
            return;
        }

        $stmt_formula->close();
    }

    $response['success'] = $jsonData['meta']['name'] . ' formula imported!';
    echo json_encode($response);
    return;
}


//CONTACT MARKETPLACE AUTHOR
if($_POST['action'] == 'contactAuthor'){
	$fname = $_POST['fname'];
	$fid = $_POST['fid'];
	
	if(empty($contactName = $_POST['contactName'])){
		$response['error'] = 'Please provide your full name';
		echo json_encode($response);
		return;
	}
	if(empty($contactEmail = $_POST['contactEmail'])){
		$response['error'] = 'Please provide your email';
		echo json_encode($response);
		return;
	}
	if(empty($contactReason = $_POST['contactReason'])){
		$response['error'] = 'Please provide report details';
		echo json_encode($response);
		return;
	}
	

	$data = [ 
		 'request' => 'MarketPlace',
		 'action' => 'contactAuthor',
		 'src' => 'marketplace',
		 'fname' => $fname, 
		 'fid' => $fid,
		 'contactName' => $contactName,
		 'contactEmail' => $contactEmail,
		 'contactReason' => $contactReason
		 ];
	
    $req = json_decode(pvPost($pvLibraryAPI, $data));
	if($req->success){
		$response['success'] = $req->success;
	}else{
		$response['error'] = $req->error;
	}
	echo json_encode($response);
	return;
	
}

//REPORT MARKETPLACE FORMULA
if($_POST['action'] == 'report' && $_POST['src'] == 'pvMarket'){
	$fname = $_POST['fname'];
	$fid= $_POST['fid'];
	
	if(empty($reporterName = $_POST['reporterName'])){
		$response['error'] = 'Please provide your full name';
		echo json_encode($response);
		return;
	}
	if(empty($reporterEmail = $_POST['reporterEmail'])){
		$response['error'] = 'Please provide your email';
		echo json_encode($response);
		return;
	}
	if(empty($reportReason = $_POST['reportReason'])){
		$response['error'] = 'Please provide report details';
		echo json_encode($response);
		return;
	}
	

	$data = [ 
		 'request' => 'MarketPlace',
		 'action' => 'report',
		 'src' => 'marketplace',
		 'fname' => $fname, 
		 'fid' => $fid,
		 'reporterName' => $reporterName,
		 'reporterEmail' => $reporterEmail,
		 'reportReason' => $reportReason
		 ];
	
    $req = json_decode(pvPost($pvLibraryAPI, $data));
	if($req->success){
		$response['success'] = $req->success;
	}else if($req->error){
		$response['error'] = $req->error;
	}else{
		$response['error'] = "Unknown error";
	}
	echo json_encode($response);
	return;
	
}	

//CLEAR USER PREFS BY ADMIN
if($_GET['action'] == 'userPerfClearGlobal'){

	if(mysqli_query($conn, "DELETE FROM user_prefs")){
		$result['success'] = "User preferences removed for all users";
	}else{
		$result['error'] = 'Something went wrong, '.mysqli_error($conn);
		
	}
	unset($_SESSION['user_prefs']);
	echo json_encode($result);
	return;
}

//CLEAR USER PREFS BY USER
if($_GET['action'] == 'userPerfClear'){

    $result = [];
	if(mysqli_query($conn, "DELETE FROM user_prefs WHERE owner_id = '".$userID."'")){
		$result['success'] = "User preferences removed";
	}else{
		$result['error'] = 'Something went wrong, '.mysqli_error($conn);
	}
	unset($_SESSION['user_prefs']);
	echo json_encode($result);
	return;
}

//CLEAR USER SETTINGS BY USER
if($_GET['action'] == 'reset_user_settings'){

    if(mysqli_query($conn, "DELETE FROM user_settings WHERE owner_id = '".$userID."'")){
        $response['success'] = "User settings reset";
    }else{
        $response['error'] = 'Something went wrong, '.mysqli_error($conn);
    }
    echo json_encode($response);
    return;
}


//DB UPDATE
if (isset($_GET['do']) && $_GET['do'] === 'db_update') {
	if($role === (int)1) {
		$a_ver = trim(@file_get_contents(__ROOT__ . '/VERSION.md'));
		$n_ver = trim(@file_get_contents(__ROOT__ . '/db/schema.ver'));

		if (empty($a_ver) || empty($n_ver)) {
			echo json_encode(['error' => 'Version information is missing.']);
			return;
		}

		$c_ver = trim($pv_meta['schema_ver']);
		$script = __ROOT__ . "/db/scripts/update_{$c_ver}-{$n_ver}.php";

		if (file_exists($script)) {
			require_once $script;
		}

		if ($c_ver === $n_ver) {
			echo json_encode(['error' => 'No update is needed.']);
			return;
		}

		$currentVer = floatval($c_ver);
		$newVer = floatval($n_ver);

		foreach (range(round($currentVer * 100), round($newVer * 100), 10) as $i) {
			$u_ver = number_format($i / 100, 1);

			// Check if SQL update file exists
			$sqlFile = __ROOT__ . "/db/updates/update_{$currentVer}-{$u_ver}.sql";
			if (file_exists($sqlFile)) {
				$sqlContent = file_get_contents($sqlFile);
				if ($sqlContent) {
					// Execute the SQL update
					if (!mysqli_multi_query($conn, $sqlContent)) {
						echo json_encode(['error' => 'Failed to apply SQL update: ' . mysqli_error($conn)]);
						return;
					}
					while (mysqli_next_result($conn)) { /* Flush multi-query results */ }
				}
			}

			// Update schema version in the database
			$stmt = $conn->prepare("UPDATE pv_meta SET schema_ver = ?");
			$stmt->bind_param("s", $u_ver);
			if (!$stmt->execute()) {
				echo json_encode(['error' => 'Failed to update schema version: ' . $stmt->error]);
				return;
			}
			$stmt->close();
		}

		// Log update history
		$stmt = $conn->prepare("INSERT INTO update_history (prev_ver, new_ver) VALUES (?, ?)");
		$stmt->bind_param("ss", $c_ver, $a_ver);
		if ($stmt->execute()) {
			echo json_encode(['success' => 'Your database has been updated.']);
		} else {
			echo json_encode(['error' => 'Failed to log update history: ' . $stmt->error]);
		}
		$stmt->close();
	}	else {
		echo json_encode(['error' => 'Not authorised']);
		error_log("PV Error: Not authorised: $role");
	}
	return;
}


//DB BACKUP
if ($_GET['do'] == 'backupDB') {
    if ($role === (int)1) {
        $bkparams = '';

        if (getenv('DB_BACKUP_PARAMETERS')) {
            $bkparams = getenv('DB_BACKUP_PARAMETERS');
        }

        if (isset($_GET['column_statistics']) && $_GET['column_statistics'] === 'true') {
            $bkparams .= ' --column-statistics=1';
        }

        // Generate a temporary file with a random name
        $tmpFile = tempnam(sys_get_temp_dir(), 'backup_') . '.sql';
        $compressedFile = $tmpFile . '.gz';

        $cmd = "mysqldump $bkparams -u $dbuser --password=$dbpass -h $dbhost $dbname > $tmpFile";

        exec($cmd, $output, $result_code);

        if ($result_code !== 0) {
            error_log("PV Backup Error: Command failed with code $result_code");
            echo json_encode(['error' => 'Backup failed. Please check the server logs for more details.']);
            unlink($tmpFile); // Clean up the temporary file
            return;
        }

        // Compress the temporary file
        $cmd = "gzip --best $tmpFile 2>&1";
        exec($cmd, $output, $result_code);

        if ($result_code !== 0 || !file_exists($compressedFile)) {
            error_log("PV Backup Error: Compression failed with code $result_code");
            echo json_encode(['error' => 'Compression failed. Please check the server logs for more details.']);
            unlink($tmpFile); // Clean up the temporary file
            return;
        }

        // Pass the compressed file to download
        $file = 'backup_' . date("d-m-Y") . '.sql.gz';

        // Ensure no output before headers
        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/gzip');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($compressedFile));
        header('Content-Encoding: none');
        header('Connection: close');

        // Stream file in chunks
        $fp = fopen($compressedFile, 'rb');
        if ($fp) {
            while (!feof($fp)) {
                echo fread($fp, 8192);
                flush();
            }
            fclose($fp);
        } else {
            error_log("PV Backup Error: Unable to open compressed file for reading.");
            echo json_encode(['error' => 'Download failed.']);
        }

        // Clean up the temporary files
        unlink($compressedFile);
        exit;
    } else {
        echo json_encode(['error' => 'Not authorised']);
        error_log("PV Error: Not authorised: $role");
    }
    return;
}


//DB RESTORE
if($_GET['restore'] == 'db_bk'){
	if($role === (int)1) {

		if (!file_exists($tmp_path)) {
			mkdir($tmp_path, 0777, true);
		}
		$result = [];
        $original_filename = basename($_FILES['backupFile']['name']);
        $target_path = $tmp_path . $fid . '_' . $original_filename;

		if(move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
			$gz_tmp = basename($_FILES['backupFile']['name']);
			preg_match('/_(.*?)_/', $gz_tmp, $v);

			if($ver !== $v['1']){
				$result['error'] = "Backup file is taken from a different version ".$v['1'];
				echo json_encode($result);
				return;
			}
			
			system("gunzip -c $target_path > ".$tmp_path.'restore.sql');
			$cmd = "mysql -u$dbuser -p$dbpass -h$dbhost $dbname < ".$tmp_path.'restore.sql'; 
			passthru($cmd,$e);
			
			unlink($target_path);
			unlink($tmp_path.'restore.sql');
			
			if(!$e){
				$result['success'] = 'Database has been restored. Please refresh the page for the changes to take effect.';
				unset($_SESSION['parfumvault']);
				session_unset();
			}else{
				$result['error'] = "Something went wrong...";
			}
		} else {
			$result['error'] = "There was an error processing backup file $target_path, please try again!";
		}
	
	}	else {
		echo json_encode(['error' => 'Not authorised']);
		error_log("PV Error: Not authorised: .$role");
	}

	echo json_encode($result);
	return;
}

//EXPORT IFRA
if($_GET['action'] == 'exportIFRA'){
    if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM IFRALibrary WHERE owner_id = '$userID'")))){
        $msg['error'] = 'No data found to export.';
        echo json_encode($msg);
        return;
    }
    $IFRA_Data = 0;
    $q = mysqli_query($conn, "SELECT * FROM IFRALibrary WHERE owner_id = '$userID'");
    while($ifra = mysqli_fetch_assoc($q)){
        $r = [
            'ifra_key' => (string)$ifra['ifra_key'] ?: "-",
            'image' => (string)$ifra['image'] ?: "-",
            'amendment' => (string)$ifra['amendment'] ?: "-",
            'prev_pub' => (string)$ifra['prev_pub'] ?: "-",
            'last_pub' => (string)$ifra['last_pub'] ?: "-",
            'deadline_existing' => (string)$ifra['deadline_existing'] ?: "-",
            'deadline_new' => (string)$ifra['deadline_new'] ?: "-",
            'name' => (string)$ifra['name'] ?: "-",
            'cas' => (string)$ifra['cas'] ?: "-",
            'cas_comment' => (string)$ifra['cas_comment'] ?: "-",
            'synonyms' => (string)$ifra['synonyms'] ?: "-",
            'formula' => (string)$ifra['formula'] ?: "-", // DEPRECATED IN IFRA 51
            'flavor_use' => (string)$ifra['flavor_use'] ?: "-",
            'prohibited_notes' => (string)$ifra['prohibited_notes'] ?: "-",
            'restricted_photo_notes' => (string)$ifra['restricted_photo_notes'] ?: "-",
            'restricted_notes' => (string)$ifra['restricted_notes'] ?: "-",
            'specified_notes' => (string)$ifra['specified_notes'] ?: "-",
            'type' => (string)$ifra['type'] ?: "-",
            'risk' => (string)$ifra['risk'] ?: "-",
            'contrib_others' => (string)$ifra['contrib_others'] ?: "-",
            'contrib_others_notes' => (string)$ifra['contrib_others_notes'] ?: "-",
            'cat1' => (double)$ifra['cat1'] ?: 100,
            'cat2' => (double)$ifra['cat2'] ?: 100,
            'cat3' => (double)$ifra['cat3'] ?: 100,
            'cat4' => (double)$ifra['cat4'] ?: 100,
            'cat5A' => (double)$ifra['cat5A'] ?: 100,
            'cat5B' => (double)$ifra['cat5B'] ?: 100,
            'cat5C' => (double)$ifra['cat5C'] ?: 100,
            'cat5D' => (double)$ifra['cat5D'] ?: 100,
            'cat6' => (double)$ifra['cat6'] ?: 100,
            'cat7A' => (double)$ifra['cat7A'] ?: 100,
            'cat7B' => (double)$ifra['cat7B'] ?: 100,
            'cat8' => (double)$ifra['cat8'] ?: 100,
            'cat9' => (double)$ifra['cat9'] ?: 100,
            'cat10A' => (double)$ifra['cat10A'] ?: 100,
            'cat10B' => (double)$ifra['cat10B'] ?: 100,
            'cat11A' => (double)$ifra['cat11A'] ?: 100,
            'cat11B' => (double)$ifra['cat11B'] ?: 100,
            'cat12' => (double)$ifra['cat12'] ?: 100
        ];

        $IFRA_Data++;
        $if[] = $r;
    }
    
    $vd = [
        'product' => $product,
        'version' => $ver,
        'ifra_entries' => $IFRA_Data,
        'timestamp' => date('d/m/Y H:i:s')
    ];

    $result = [
        'IFRALibrary' => $if,
        'pvMeta' => $vd
    ];
    
    header('Content-disposition: attachment; filename=IFRALibrary.json');
    header('Content-type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT);
    return;
}


//IMPORT FORMULAS
if($_GET['action'] == 'restoreFormulas'){
    $result = [];
    require_once(__ROOT__.'/core/import_formulas.php');
    return;
}

//IMPORT INGREDIENTS
if($_GET['action'] == 'restoreIngredients') {
    require_once(__ROOT__.'/core/import_ingredients.php');
    return;
}

//IMPORT IFRA JSON
if($_GET['action'] == 'restoreIFRA'){
    $result = [];
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0777, true);
	}
	
	if (!is_writable($tmp_path)) {
		$result['error'] = "Upload directory not writable. Make sure you have write permissions.";
		echo json_encode($result);
		return;
	}
	
	$target_path = $tmp_path.basename($_FILES['backupFile']['name']); 

	if(move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    	$data = json_decode(file_get_contents($target_path), true);
		if(!$data['IFRALibrary']){
			$result['error'] = "JSON File seems invalid. Please make sure you importing the right file";
			echo json_encode($result);
			return;
		}
		mysqli_query($conn, "DELETE FROM IFRALibrary WHERE owner_id = '$userID'");
		
		foreach ($data['IFRALibrary'] as $d ){				
			$ifra_key = mysqli_real_escape_string($conn, $d['ifra_key']);
			$image = mysqli_real_escape_string($conn, $d['image']);
			$amendment = mysqli_real_escape_string($conn, $d['amendment']);
			$prev_pub = mysqli_real_escape_string($conn, $d['prev_pub']);
			$last_pub = mysqli_real_escape_string($conn, $d['last_pub']);
			$deadline_existing = mysqli_real_escape_string($conn, $d['deadline_existing']);
			$deadline_new = mysqli_real_escape_string($conn, $d['deadline_new']);
			$name = mysqli_real_escape_string($conn, $d['name']);
			$cas = mysqli_real_escape_string($conn, $d['cas']);
			$cas_comment = mysqli_real_escape_string($conn, $d['cas_comment']);
			$synonyms = mysqli_real_escape_string($conn, $d['synonyms']);
			$formula = mysqli_real_escape_string($conn, $d['formula']);
			$flavor_use = mysqli_real_escape_string($conn, $d['flavor_use']);
			$prohibited_notes = mysqli_real_escape_string($conn, $d['prohibited_notes']);
			$restricted_photo_notes = mysqli_real_escape_string($conn, $d['restricted_photo_notes']);
			$restricted_notes = mysqli_real_escape_string($conn, $d['restricted_notes']);
			$specified_notes = mysqli_real_escape_string($conn, $d['specified_notes']);
			$type = mysqli_real_escape_string($conn, $d['type']);
			$risk = mysqli_real_escape_string($conn, $d['risk']);
			$contrib_others = mysqli_real_escape_string($conn, $d['contrib_others']);
			$contrib_others_notes = mysqli_real_escape_string($conn, $d['contrib_others_notes']);
		
			$cat1 = isset($d['cat1']) && $d['cat1'] !== '' ? floatval($d['cat1']) : 100;
			$cat2 = isset($d['cat2']) && $d['cat2'] !== '' ? floatval($d['cat2']) : 100;
			$cat3 = isset($d['cat3']) && $d['cat3'] !== '' ? floatval($d['cat3']) : 100;
			$cat4 = isset($d['cat4']) && $d['cat4'] !== '' ? floatval($d['cat4']) : 100;
			$cat5A = isset($d['cat5A']) && $d['cat5A'] !== '' ? floatval($d['cat5A']) : 100;
			$cat5B = isset($d['cat5B']) && $d['cat5B'] !== '' ? floatval($d['cat5B']) : 100;
			$cat5C = isset($d['cat5C']) && $d['cat5C'] !== '' ? floatval($d['cat5C']) : 100;
			$cat5D = isset($d['cat5D']) && $d['cat5D'] !== '' ? floatval($d['cat5D']) : 100;
			$cat6 = isset($d['cat6']) && $d['cat6'] !== '' ? floatval($d['cat6']) : 100;
			$cat7A = isset($d['cat7A']) && $d['cat7A'] !== '' ? floatval($d['cat7A']) : 100;
			$cat7B = isset($d['cat7B']) && $d['cat7B'] !== '' ? floatval($d['cat7B']) : 100;
			$cat8 = isset($d['cat8']) && $d['cat8'] !== '' ? floatval($d['cat8']) : 100;
			$cat9 = isset($d['cat9']) && $d['cat9'] !== '' ? floatval($d['cat9']) : 100;
			$cat10A = isset($d['cat10A']) && $d['cat10A'] !== '' ? floatval($d['cat10A']) : 100;
			$cat10B = isset($d['cat10B']) && $d['cat10B'] !== '' ? floatval($d['cat10B']) : 100;
			$cat11A = isset($d['cat11A']) && $d['cat11A'] !== '' ? floatval($d['cat11A']) : 100;
			$cat11B = isset($d['cat11B']) && $d['cat11B'] !== '' ? floatval($d['cat11B']) : 100;
			$cat12 = isset($d['cat12']) && $d['cat12'] !== '' ? floatval($d['cat12']) : 100;
		
			$s = mysqli_query($conn, "
				INSERT INTO `IFRALibrary` (
					`ifra_key`, `image`, `amendment`, `prev_pub`, `last_pub`, 
					`deadline_existing`, `deadline_new`, `name`, `cas`, `cas_comment`, 
					`synonyms`, `formula`, `flavor_use`, `prohibited_notes`, `restricted_photo_notes`, 
					`restricted_notes`, `specified_notes`, `type`, `risk`, `contrib_others`, 
					`contrib_others_notes`, `cat1`, `cat2`, `cat3`, `cat4`, `cat5A`, 
					`cat5B`, `cat5C`, `cat5D`, `cat6`, `cat7A`, `cat7B`, `cat8`, `cat9`, 
					`cat10A`, `cat10B`, `cat11A`, `cat11B`, `cat12`, `owner_id`
				) VALUES (
					'$ifra_key', '$image', '$amendment', '$prev_pub', '$last_pub', 
					'$deadline_existing', '$deadline_new', '$name', '$cas', '$cas_comment', 
					'$synonyms', '$formula', '$flavor_use', '$prohibited_notes', '$restricted_photo_notes', 
					'$restricted_notes', '$specified_notes', '$type', '$risk', '$contrib_others', 
					'$contrib_others_notes', '$cat1', '$cat2', '$cat3', '$cat4', '$cat5A', 
					'$cat5B', '$cat5C', '$cat5D', '$cat6', '$cat7A', '$cat7B', '$cat8', '$cat9', 
                    '$cat10A', '$cat10B', '$cat11A', '$cat11B', '$cat12', '$userID'
				)
			");
		}

				
		if($s){
			$result['success'] = "Import complete";
			unlink($target_path);
		}else{
			$result['error'] = "There was an error importing your JSON file ".mysqli_error($conn);
			echo json_encode($result);
			return;
		}
			
	} else {
		$result['error'] = "There was an error processing json file $target_path, please try again!";
		echo json_encode($result);

	}
	echo json_encode($result);
	return;

}

//IMPORT SUPPLIERS
if ($_GET['action'] == 'importSuppliers') {
    $result = [];
    if (!file_exists($tmp_path)) {
        mkdir($tmp_path, 0777, true);
    }

    if (!is_writable($tmp_path)) {
        $result['error'] = "Upload directory not writable. Make sure you have write permissions.";
        echo json_encode($result);
        return;
    }

    $target_path = $tmp_path . basename($_FILES['jsonFile']['name']); 

    if (move_uploaded_file($_FILES['jsonFile']['tmp_name'], $target_path)) {
        $data = json_decode(file_get_contents($target_path), true);
        if (!$data['inventory_suppliers']) {
            $result['error'] = "JSON File seems invalid. Please make sure you are importing the right file";
            echo json_encode($result);
            return;
        }

        foreach ($data['inventory_suppliers'] as $d) {				
            // Escape values to prevent SQL injection
            $name = mysqli_real_escape_string($conn, $d['name']);
            $address = mysqli_real_escape_string($conn, $d['address']);
            $po = mysqli_real_escape_string($conn, $d['po']);
            $country = mysqli_real_escape_string($conn, $d['country']);
            $telephone = mysqli_real_escape_string($conn, $d['telephone']);
            $url = mysqli_real_escape_string($conn, $d['url']);
            $email = mysqli_real_escape_string($conn, $d['email']);
            $platform = mysqli_real_escape_string($conn, $d['platform']);
            $price_tag_start = mysqli_real_escape_string($conn, $d['price_tag_start']);
            $price_tag_end = mysqli_real_escape_string($conn, $d['price_tag_end']);
            $add_costs = mysqli_real_escape_string($conn, $d['add_costs']);
            $price_per_size = mysqli_real_escape_string($conn, $d['price_per_size']);
            $notes = mysqli_real_escape_string($conn, $d['notes']);
            $min_ml = mysqli_real_escape_string($conn, $d['min_ml']);
            $min_gr = mysqli_real_escape_string($conn, $d['min_gr']);

            // Check if entry exists with same name and owner_id
            $query_check = "SELECT COUNT(*) FROM `ingSuppliers` WHERE `name` = '$name' AND `owner_id` = '$userID'";
            $result_check = mysqli_query($conn, $query_check);
            $exists = mysqli_fetch_row($result_check)[0];

            if ($exists > 0) {
                // Update the existing record if it exists
                $query = "
                    UPDATE `ingSuppliers` 
                    SET 
                        `address` = '$address', 
                        `po` = '$po', 
                        `country` = '$country', 
                        `telephone` = '$telephone', 
                        `url` = '$url', 
                        `email` = '$email', 
                        `platform` = '$platform', 
                        `price_tag_start` = '$price_tag_start', 
                        `price_tag_end` = '$price_tag_end', 
                        `add_costs` = '$add_costs', 
                        `price_per_size` = '$price_per_size', 
                        `notes` = '$notes', 
                        `min_ml` = '$min_ml', 
                        `min_gr` = '$min_gr'
                    WHERE 
                        `name` = '$name' AND `owner_id` = '$userID'
                ";
            } else {
                // Insert new record if it doesn't exist
                $query = "
                    INSERT INTO `ingSuppliers` 
                    (`name`, `address`, `po`, `country`, `telephone`, `url`, `email`, `platform`, `price_tag_start`, `price_tag_end`, `add_costs`, `price_per_size`, `notes`, `min_ml`, `min_gr`, `owner_id`) 
                    VALUES 
                    ('$name', '$address', '$po', '$country', '$telephone', '$url', '$email', '$platform', '$price_tag_start', '$price_tag_end', '$add_costs', '$price_per_size', '$notes', '$min_ml', '$min_gr', '$userID')
                ";
            }

            // Execute the query (insert or update)
            if (!mysqli_query($conn, $query)) {
                $result['error'] = "There was an error processing the record for $name: " . mysqli_error($conn);
                echo json_encode($result);
                return;
            }
        }

        $result['success'] = "Import complete";
        unlink($target_path);
    } else {
        $result['error'] = "There was an error processing the JSON file $target_path, please try again!";
        echo json_encode($result);
    }

    echo json_encode($result);
    return;
}


//IMPORT CUSTOMERS
if ($_GET['action'] == 'importCustomers') {
    $result = [];
    if (!file_exists($tmp_path)) {
        mkdir($tmp_path, 0777, true);
    }

    if (!is_writable($tmp_path)) {
        $result['error'] = "Upload directory not writable. Make sure you have write permissions.";
        echo json_encode($result);
        return;
    }

    $target_path = $tmp_path . basename($_FILES['jsonFile']['name']); 

    if (move_uploaded_file($_FILES['jsonFile']['tmp_name'], $target_path)) {
        $data = json_decode(file_get_contents($target_path), true);
        if (!$data['inventory_customers']) {
            $result['error'] = "JSON File seems invalid. Please make sure you are importing the right file";
            echo json_encode($result);
            return;
        }

        foreach ($data['inventory_customers'] as $d) {				
            // Escape values to prevent SQL injection
            $name = mysqli_real_escape_string($conn, $d['name']);
            $address = mysqli_real_escape_string($conn, $d['address']) ?: '-';
            $email = mysqli_real_escape_string($conn, $d['email']) ?: '-';
            $phone = mysqli_real_escape_string($conn, $d['phone']) ?: '-';
            $web = mysqli_real_escape_string($conn, $d['web']) ?: '-';

            // Check if entry exists with same name and owner_id
            $query_check = "SELECT COUNT(*) FROM `customers` WHERE `name` = '$name' AND `owner_id` = '$userID'";
            $result_check = mysqli_query($conn, $query_check);
            $exists = mysqli_fetch_row($result_check)[0];

            if ($exists > 0) {
                // Update the existing record if it exists
                $query = "
                    UPDATE `customers` 
                    SET 
                        `address` = '$address', 
                        `email` = '$email', 
                        `phone` = '$phone', 
                        `web` = '$web'
                    WHERE 
                        `name` = '$name' AND `owner_id` = '$userID'
                ";
            } else {
                // Insert new record if it doesn't exist
                $query = "
                    INSERT INTO `customers` 
                    (`name`, `address`, `email`, `phone`, `web`, `owner_id`) 
                    VALUES 
                    ('$name', '$address', '$email', '$phone', '$web', '$userID')
                ";
            }

            // Execute the query (insert or update)
            if (!mysqli_query($conn, $query)) {
                $result['error'] = "There was an error processing the record for $name: " . mysqli_error($conn);
                echo json_encode($result);
                return;
            }
        }

        $result['success'] = "Import complete";
        unlink($target_path);
    } else {
        $result['error'] = "There was an error processing the JSON file $target_path, please try again!";
        echo json_encode($result);
    }

    echo json_encode($result);
    return;
}


//IMPORT BOTTLES
if ($_GET['action'] == 'importBottles') {
    $result = [];
    if (!file_exists($tmp_path)) {
        mkdir($tmp_path, 0777, true);
    }

    if (!is_writable($tmp_path)) {
        $result['error'] = "Upload directory not writable. Make sure you have write permissions.";
        echo json_encode($result);
        return;
    }

    $target_path = $tmp_path . basename($_FILES['jsonFile']['name']); 

    if (move_uploaded_file($_FILES['jsonFile']['tmp_name'], $target_path)) {
        $data = json_decode(file_get_contents($target_path), true);
        if (!$data['inventory_bottles']) {
            $result['error'] = "JSON File seems invalid. Please make sure you are importing the right file";
            echo json_encode($result);
            return;
        }

        foreach ($data['inventory_bottles'] as $d) {				
            // Escape values to prevent SQL injection
            $name = mysqli_real_escape_string($conn, $d['name']);
            $ml = mysqli_real_escape_string($conn, $d['ml']);
            $price = mysqli_real_escape_string($conn, $d['price']);
            $height = mysqli_real_escape_string($conn, $d['height']);
            $width = mysqli_real_escape_string($conn, $d['width']);
            $diameter = mysqli_real_escape_string($conn, $d['diameter']);
            $weight = mysqli_real_escape_string($conn, $d['weight']);
            $supplier = mysqli_real_escape_string($conn, $d['supplier']);
            $supplier_link = mysqli_real_escape_string($conn, $d['supplier_link']);
            $notes = mysqli_real_escape_string($conn, $d['notes']);
            $pieces = mysqli_real_escape_string($conn, $d['pieces']);

            // Check if entry exists with same name and owner_id
            $query_check = "SELECT COUNT(*) FROM `bottles` WHERE `name` = '$name' AND `owner_id` = '$userID'";
            $result_check = mysqli_query($conn, $query_check);
            $exists = mysqli_fetch_row($result_check)[0];

            if ($exists > 0) {
                // Update the existing record if it exists
                $query = "
                    UPDATE `bottles` 
                    SET 
                        `ml` = '$ml', 
                        `price` = '$price', 
                        `height` = '$height', 
                        `width` = '$width', 
                        `diameter` = '$diameter', 
                        `weight` = '$weight', 
                        `supplier` = '$supplier', 
                        `supplier_link` = '$supplier_link', 
                        `notes` = '$notes', 
                        `pieces` = '$pieces'
                    WHERE 
                        `name` = '$name' AND `owner_id` = '$userID'
                ";
            } else {
                // Insert new record if it doesn't exist
                $query = "
                    INSERT INTO `bottles` 
                    (`name`, `ml`, `price`, `height`, `width`, `diameter`, `weight`, `supplier`, `supplier_link`, `notes`, `pieces`, `owner_id`) 
                    VALUES 
                    ('$name', '$ml', '$price', '$height', '$width', '$diameter', '$weight', '$supplier', '$supplier_link', '$notes', '$pieces', '$userID')
                ";
            }

            // Execute the query (insert or update)
            if (!mysqli_query($conn, $query)) {
                $result['error'] = "There was an error processing the record for $name: " . mysqli_error($conn);
                echo json_encode($result);
                return;
            }
        }

        $result['success'] = "Import complete";
        unlink($target_path);
    } else {
        $result['error'] = "There was an error processing the JSON file $target_path, please try again!";
        echo json_encode($result);
    }

    echo json_encode($result);
    return;
}


//IMPORT ACCESSORIES
if ($_GET['action'] == 'importAccessories') {
    $result = [];
    if (!file_exists($tmp_path)) {
        mkdir($tmp_path, 0777, true);
    }

    if (!is_writable($tmp_path)) {
        $result['error'] = "Upload directory not writable. Make sure you have write permissions.";
        echo json_encode($result);
        return;
    }

    $target_path = $tmp_path . basename($_FILES['jsonFile']['name']); 

    if (move_uploaded_file($_FILES['jsonFile']['tmp_name'], $target_path)) {
        $data = json_decode(file_get_contents($target_path), true);
        if (!$data['inventory_accessories']) {
            $result['error'] = "JSON File seems invalid. Please make sure you are importing the right file";
            echo json_encode($result);
            return;
        }

        foreach ($data['inventory_accessories'] as $d) {
            // Escape values to prevent SQL injection
            $name = mysqli_real_escape_string($conn, $d['name']);
            $accessory = mysqli_real_escape_string($conn, $d['accessory']);
            $price = mysqli_real_escape_string($conn, $d['price']);
            $supplier = mysqli_real_escape_string($conn, $d['supplier']);
            $supplier_link = mysqli_real_escape_string($conn, $d['supplier_link']);
            $pieces = mysqli_real_escape_string($conn, $d['pieces']);

            // Check if entry exists with the same name and owner_id
            $query_check = "SELECT COUNT(*) FROM `inventory_accessories` WHERE `name` = '$name' AND `owner_id` = '$userID'";
            $result_check = mysqli_query($conn, $query_check);
            $exists = mysqli_fetch_row($result_check)[0];

            if ($exists > 0) {
                // Update the existing record if it exists
                $query = "
                    UPDATE `inventory_accessories`
                    SET 
                        `accessory` = '$accessory', 
                        `price` = '$price', 
                        `supplier` = '$supplier', 
                        `supplier_link` = '$supplier_link', 
                        `pieces` = '$pieces'
                    WHERE 
                        `name` = '$name' AND `owner_id` = '$userID'
                ";
            } else {
                // Insert a new record if it doesn't exist
                $query = "
                    INSERT INTO `inventory_accessories` 
                    (`name`, `accessory`, `price`, `supplier`, `supplier_link`, `pieces`, `owner_id`) 
                    VALUES 
                    ('$name', '$accessory', '$price', '$supplier', '$supplier_link', '$pieces', '$userID')
                ";
            }

            // Execute the query (insert or update)
            if (!mysqli_query($conn, $query)) {
                $result['error'] = "There was an error processing the record for $name: " . mysqli_error($conn);
                echo json_encode($result);
                return;
            }
        }

        $result['success'] = "Import complete";
        unlink($target_path);
    } else {
        $result['error'] = "There was an error processing the JSON file $target_path, please try again!";
        echo json_encode($result);
    }

    echo json_encode($result);
    return;
}



//IMPORT COMPOUNDS
if ($_GET['action'] == 'importCompounds') {
    $result = [];
    if (!file_exists($tmp_path)) {
        mkdir($tmp_path, 0777, true);
    }

    if (!is_writable($tmp_path)) {
        $result['error'] = "Upload directory not writable. Make sure you have write permissions.";
        echo json_encode($result);
        return;
    }

    $target_path = $tmp_path . basename($_FILES['jsonFile']['name']); 

    if (move_uploaded_file($_FILES['jsonFile']['tmp_name'], $target_path)) {
        $data = json_decode(file_get_contents($target_path), true);
        if (!$data['inventory_compounds']) {
            $result['error'] = "JSON File seems invalid. Please make sure you are importing the right file";
            echo json_encode($result);
            return;
        }

        foreach ($data['inventory_compounds'] as $d) {
            // Escape values to prevent SQL injection
            $name = mysqli_real_escape_string($conn, $d['name']);
            $description = mysqli_real_escape_string($conn, $d['description']);
            $batch_id = mysqli_real_escape_string($conn, $d['batch_id']);
            $size = mysqli_real_escape_string($conn, $d['size']);
            $location = mysqli_real_escape_string($conn, $d['location']);
            $label_info = mysqli_real_escape_string($conn, $d['label_info']);

            // Check if entry exists with the same name and owner_id
            $query_check = "SELECT COUNT(*) FROM `inventory_compounds` WHERE `name` = '$name' AND `owner_id` = '$userID'";
            $result_check = mysqli_query($conn, $query_check);
            $exists = mysqli_fetch_row($result_check)[0];

            if ($exists > 0) {
                // Update the existing record if it exists
                $query = "
                    UPDATE `inventory_compounds`
                    SET 
                        `description` = '$description', 
                        `batch_id` = '$batch_id', 
                        `size` = '$size', 
                        `location` = '$location', 
                        `label_info` = '$label_info'
                    WHERE 
                        `name` = '$name' AND `owner_id` = '$userID'
                ";
            } else {
                // Insert a new record if it doesn't exist
                $query = "
                    INSERT INTO `inventory_compounds` 
                    (`name`, `description`, `batch_id`, `size`, `location`, `label_info`, `owner_id`) 
                    VALUES 
                    ('$name', '$description', '$batch_id', '$size', '$location', '$label_info', '$userID')
                ";
            }

            // Execute the query (insert or update)
            if (!mysqli_query($conn, $query)) {
                $result['error'] = "There was an error processing the record for $name: " . mysqli_error($conn);
                echo json_encode($result);
                return;
            }
        }

        $result['success'] = "Import complete";
        unlink($target_path);
    } else {
        $result['error'] = "There was an error processing the JSON file $target_path, please try again!";
        echo json_encode($result);
    }

    echo json_encode($result);
    return;
}



// IMPORT CATEGORIES
if ($_GET['action'] == 'importCategories') {
    $result = [];

    if (!file_exists($tmp_path)) {
        mkdir($tmp_path, 0777, true);
    }

    if (!is_writable($tmp_path)) {
        $result['error'] = "Upload directory not writable. Make sure you have write permissions.";
        echo json_encode($result);
        return;
    }

    $target_path = $tmp_path . basename($_FILES['jsonFile']['name']);

    if (move_uploaded_file($_FILES['jsonFile']['tmp_name'], $target_path)) {
        $data = json_decode(file_get_contents($target_path), true);

        if (!$data['ingCategory'] && !$data['formulaCategories'] && !$data['ingProfiles']) {
            $result['error'] = "JSON File seems invalid. Please make sure you are importing the right file";
            echo json_encode($result);
            return;
        }

        $conn->autocommit(FALSE); // Turn off auto-commit for transaction

        $success = true;

        if ($data['ingCategory']) {
            $stmt = $conn->prepare("INSERT INTO `ingCategory` (`name`, `notes`, `image`, `colorKey`, `owner_id`) VALUES (?, ?, ?, ?, ?)");
            foreach ($data['ingCategory'] as $d) {
                $stmt->bind_param("sssss", $d['name'], $d['notes'], $d['image'], $d['colorKey'], $userID);
                if (!$stmt->execute()) {
                    $success = false;
                    $result['error'] = "Error adding category: " . $stmt->error;
                    break;
                }
            }
            $stmt->close();
        }

        if ($data['formulaCategories']) {
            $stmt = $conn->prepare("INSERT INTO `formulaCategories` (`name`, `cname`, `type`, `colorKey`, `owner_id`) VALUES (?, ?, ?, ?, ?)");
            foreach ($data['formulaCategories'] as $d) {
                $stmt->bind_param("sssss", $d['name'], $d['cname'], $d['type'], $d['colorKey'], $userID);
                if (!$stmt->execute()) {
                    $success = false;
                    $result['error'] = "Error adding formula category: " . $stmt->error;
                    break;
                }
            }
            $stmt->close();
        }
		
		if ($data['ingProfiles']) {
            $stmt = $conn->prepare("INSERT INTO `ingProfiles` (`name`, `notes`, `image`, `owner_id`) VALUES (?, ?, ?, ?)");
            foreach ($data['ingProfiles'] as $d) {
                $stmt->bind_param("ssss", $d['name'], $d['notes'], $d['image'], $userID);
                if (!$stmt->execute()) {
                    $success = false;
                    $result['error'] = "Error adding profile: " . $stmt->error;
                    break;
                }
            }
            $stmt->close();
        }

        if ($success) {
            $conn->commit(); // Commit the transaction
            $result['success'] = "Import complete";
            unlink($target_path);
        } else {
            $conn->rollback(); // Rollback the transaction on error
            echo json_encode($result);
            return;
        }

        $conn->autocommit(TRUE); // Turn auto-commit back on
    } else {
        $result['error'] = "There was an error processing json file $target_path, please try again!";
    }

    echo json_encode($result);
    return;
}


// IMPORT MAKING --- TODO: PREVENT DUPLICATE FORMULAS
if ($_GET['action'] == 'importMaking') {
    $result = [];
    
    if (!is_dir($tmp_path) && !mkdir($tmp_path, 0777, true)) {
        $result['error'] = "Failed to create upload directory.";
        echo json_encode($result);
        return;
    }

    if (!is_writable($tmp_path)) {
        $result['error'] = "Upload directory not writable. Please check permissions.";
        echo json_encode($result);
        return;
    }

    $target_path = $tmp_path . basename($_FILES['jsonFile']['name']);
    
    if (!move_uploaded_file($_FILES['jsonFile']['tmp_name'], $target_path)) {
        $result['error'] = "Error processing the uploaded JSON file.";
        echo json_encode($result);
        return;
    }

    $data = json_decode(file_get_contents($target_path), true);
    
    if (!$data || empty($data['makeFormula'])) {
        $result['error'] = "Invalid JSON file. Ensure you're importing the correct file.";
        echo json_encode($result);
        return;
    }

    $conn->autocommit(FALSE);
    $success = true;

    try {
        $conn->begin_transaction();
        $fidMap = []; // Store fid for each formulasMetaData entry
        
        // Insert into formulasMetaData and store generated fid
        foreach ($data['formulasMetaData'] as $d) {
            $fid = bin2hex(random_bytes(16)); // Unique fid per entry
            $fidMap[$d['name']] = $fid; // Store for lookup
            
            $todo = 1;
            $stmtMeta = $conn->prepare("INSERT INTO formulasMetaData (name, fid, todo, scheduledOn, owner_id) 
                                        VALUES (?, ?, ?, ?, ?) 
                                        ON DUPLICATE KEY UPDATE 
                                        todo=VALUES(todo), scheduledOn=VALUES(scheduledOn)");
            $stmtMeta->bind_param("ssiss", $d['name'], $fid, $todo, date('Y-m-d H:i:s'), $userID);
            if (!$stmtMeta->execute()) {
                throw new Exception("PV error: Error inserting into formulasMetaData: " . $stmtMeta->error);
            }
            $stmtMeta->close();
        }
    
        // Insert into makeFormula and formulas using matching fid
        foreach ($data['makeFormula'] as $d) {
            if (!isset($fidMap[$d['name']])) {
                continue; // Skip if name not found in formulasMetaData
            }
            $fid = $fidMap[$d['name']];
    
            // Fetch or Insert `ingredient_id`
            $stmtIngredient = $conn->prepare("SELECT id FROM ingredients WHERE name = ? AND owner_id = ?");
            $stmtIngredient->bind_param("ss", $d['ingredient'], $userID);
            $stmtIngredient->execute();
            $stmtIngredient->bind_result($ingredient_id);
            $stmtIngredient->fetch();
            $stmtIngredient->close();
    
            if (empty($ingredient_id)) {
                $stmtInsertIngredient = $conn->prepare("INSERT INTO ingredients (name, owner_id) VALUES (?, ?)");
                $stmtInsertIngredient->bind_param("ss", $d['ingredient'], $userID);
                if (!$stmtInsertIngredient->execute()) {
                    throw new Exception("PV error: Error inserting into ingredients: " . $stmtInsertIngredient->error);
                }
                $ingredient_id = $stmtInsertIngredient->insert_id;
                $stmtInsertIngredient->close();
            }
    
            // Insert into makeFormula
            $stmtFormula = $conn->prepare("INSERT INTO makeFormula (fid, name, ingredient, ingredient_id, replacement_id, concentration, dilutant, quantity, overdose, originalQuantity, notes, skip, toAdd, created_at, owner_id) 
                                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
                                           ON DUPLICATE KEY UPDATE 
                                           replacement_id=VALUES(replacement_id), concentration=VALUES(concentration), 
                                           dilutant=VALUES(dilutant), quantity=VALUES(quantity), overdose=VALUES(overdose), 
                                           originalQuantity=VALUES(originalQuantity), notes=VALUES(notes), skip=VALUES(skip), 
                                           toAdd=VALUES(toAdd)");
            $stmtFormula->bind_param("sssssssssssssss", $fid, $d['name'], $d['ingredient'], $ingredient_id, $d['replacement_id'], $d['concentration'], $d['dilutant'], $d['quantity'], $d['overdose'], $d['originalQuantity'], $d['notes'], $d['skip'], $d['toAdd'], date('Y-m-d H:i:s'), $userID);
            if (!$stmtFormula->execute()) {
                throw new Exception("PV error: Error inserting into makeFormula: " . $stmtFormula->error);
            }
            $stmtFormula->close();
    
            // Insert into formulas
            $stmtInsertFormulas = $conn->prepare("INSERT INTO formulas (name, fid, ingredient, ingredient_id, concentration, dilutant, quantity, owner_id) 
                                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
                                                  ON DUPLICATE KEY UPDATE 
                                                  concentration=VALUES(concentration), dilutant=VALUES(dilutant), quantity=VALUES(quantity)");
            $stmtInsertFormulas->bind_param("ssssssss", $d['name'], $fid, $d['ingredient'], $ingredient_id, $d['concentration'], $d['dilutant'], $d['quantity'], $userID);
            if (!$stmtInsertFormulas->execute()) {
                throw new Exception("PV error: Error inserting into formulas: " . $stmtInsertFormulas->error);
            }
            $stmtInsertFormulas->close();
        }
    
        $conn->commit();
        unlink($target_path);
        $result['success'] = "Import complete";
    } catch (Exception $e) {
        $conn->rollback();
        error_log("PV error: " . $e->getMessage());
        $result['error'] = $e->getMessage();
    }
    
    
    $conn->autocommit(TRUE);
    echo json_encode($result);
    return;
}



//EXPORT INGREDIENT CATEGORIES
if ($_GET['action'] == 'exportIngCat') {
    $query = "SELECT id FROM ingCategory WHERE owner_id = '$userID'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        $msg['error'] = 'No data found to export';
        echo json_encode($msg);
        return;
    }

    $data = 0;
    $categories = [];
    $query = "SELECT * FROM ingCategory WHERE owner_id = '$userID'";
    $result = mysqli_query($conn, $query);

    while ($resData = mysqli_fetch_assoc($result)) {
        $category = [
            'name' => (string)$resData['name'] ?: "-",
            'notes' => (string)$resData['notes'] ?: "-",
            'image' => (string)$resData['image'] ?: "-",
            'colorKey' => (string)$resData['colorKey'] ?: "-"
        ];

        $data++;
        $categories[] = $category;
    }

    $meta = [
        'product' => $product,
        'version' => $ver,
        'ingCategory' => $data,
        'timestamp' => date('d/m/Y H:i:s')
    ];

    $result = [
        'ingCategory' => $categories,
        'pvMeta' => $meta
    ];

    header('Content-disposition: attachment; filename=Ingredient_Categories.json');
    header('Content-type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT);
    return;
}

//EXPORT FORMULA CATEGORIES
if ($_GET['action'] == 'exportFrmCat') {
    $query = "SELECT id FROM formulaCategories WHERE owner_id = '$userID'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        $msg['error'] = 'No data found to export.';
        echo json_encode($msg);
        return;
    }

    $data = 0;
    $categories = [];
    $query = "SELECT * FROM formulaCategories WHERE owner_id = '$userID'";
    $result = mysqli_query($conn, $query);

    while ($resData = mysqli_fetch_assoc($result)) {
        $category = [
            'name' => (string)$resData['name'] ?: "-",
            'cname' => (string)$resData['cname'] ?: "-",
            'type' => (string)$resData['type'] ?: "-",
            'colorKey' => (string)$resData['colorKey'] ?: "-"
        ];

        $data++;
        $categories[] = $category;
    }

    $meta = [
        'product' => $product,
        'version' => $ver,
        'formulaCategories' => $data,
        'timestamp' => date('d/m/Y H:i:s')
    ];

    $result = [
        'formulaCategories' => $categories,
        'pvMeta' => $meta
    ];

    header('Content-disposition: attachment; filename=Formula_Categories.json');
    header('Content-type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT);
    return;
}

//EXPORT PERFUME TYPES
if ($_GET['action'] == 'exportPerfTypes') {
    $query = "SELECT id FROM perfumeTypes WHERE owner_id = '$userID'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        $msg['error'] = 'No data found to export.';
        echo json_encode($msg);
        return;
    }

    $data = 0;
    $perfumeTypes = [];
    $query = "SELECT * FROM perfumeTypes WHERE owner_id = '$userID'";
    $result = mysqli_query($conn, $query);

    while ($resData = mysqli_fetch_assoc($result)) {
        $type = [
            'name' => (string)$resData['name'] ?: "-",
            'concentration' => (int)$resData['concentration'] ?: 100,
            'description' => (string)$resData['description'] ?: "-"
        ];

        $data++;
        $perfumeTypes[] = $type;
    }

    $meta = [
        'product' => $product,
        'version' => $ver,
        'perfumeTypes' => $data,
        'timestamp' => date('d/m/Y H:i:s')
    ];

    $result = [
        'perfumeTypes' => $perfumeTypes,
        'pvMeta' => $meta
    ];

    header('Content-disposition: attachment; filename=Perfume_Types.json');
    header('Content-type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT);
    return;
}



//EXPORT MAKING FORMULA
if($_GET['action'] == 'exportMaking'){
    if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE owner_id = '$userID'")))){
        $msg['error'] = 'No data found to export.';
        echo json_encode($msg);
        return;
    }
    $data = 0;
    $filter = "";
    if($fid = $_GET['fid']){
        $filter = " WHERE fid = '$fid' AND owner_id = '$userID'";    
    } else {
        $filter = " WHERE owner_id = '$userID'";
    }
    

    $formulas = 0;
    $ingredients = 0;
    
    $qfmd = mysqli_query($conn, "SELECT * FROM formulasMetaData $filter AND toDo = 1"); 
    while($meta = mysqli_fetch_assoc($qfmd)){
        $r = [
            'name' => (string)$meta['name'],
            'product_name' => (string)$meta['product_name'],
          // 'fid' => (string)$meta['fid'],
            'profile' => (string)$meta['profile'],
            'category' => (string)$meta['profile'] ?: 'Default',
            'gender' => (string)$meta['gender'],
            'notes' => (string)$meta['notes'] ?: 'None',
            'created_at' => (string)$meta['created_at'],
            'isProtected' => (int)$meta['isProtected'] ?: 0,
            'defView' => (int)$meta['defView'],
            'catClass' => (string)$meta['catClass'],
            'revision' => (int)$meta['revision'] ?: 0,
            'finalType' => (int)$meta['finalType'] ?: 100,
            'isMade' => (int)$meta['isMade'],
            'madeOn' => (string)$meta['madeOn'] ?: "0000-00-00 00:00:00",
            'scheduledOn' => (string)$meta['scheduledOn'],
            'customer_id' => (int)$meta['customer_id'],
            'status' => (int)$meta['status'],
            'toDo' => (int)$meta['toDo'],
            'rating' => (int)$meta['rating'] ?: 0
        ];
        $formulas++;
        $fm[] = $r;
    }


    $q = mysqli_query($conn, "SELECT * FROM makeFormula $filter");
    while($resData = mysqli_fetch_assoc($q)){
        $r = [
           // 'fid' => (string)$resData['fid'],
            'name' => (string)$resData['name'],
            'ingredient' => (string)$resData['ingredient'],
            'ingredient_id' => (int)$resData['ingredient_id'],
            'replacement_id' => (int)$resData['replacement_id'],
            'concentration' => (double)$resData['concentration'],
            'dilutant' => (string)$resData['dilutant'] ?: "None",
            'quantity' => (double)$resData['quantity'],
            'overdose' => (double)$resData['overdose'],
            'originalQuantity' => (double)$resData['originalQuantity'],
            'notes' => (string)$resData['notes'],
            'skip' => (int)$resData['skip'],
            'toAdd' => (int)$resData['toAdd']
        ];

        $data++;
        $dat_arr[] = $r;
    }
    
    $vd = [
        'product' => $product,
        'version' => $ver,
        'makeFormula' => $formulas,
        'timestamp' => date('d/m/Y H:i:s')
    ];

    $result = [
        'formulasMetaData' => $fm,
        'makeFormula' => $dat_arr,
        'pvMeta' => $vd
    ];
    
    header('Content-disposition: attachment; filename=MakeFormula.json');
    header('Content-type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT);
    return;
}

//EXPORT INGREDIENT PROFILES
if($_GET['action'] == 'exportIngProf'){
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingProfiles WHERE owner_id = '$userID'")))){
		$msg['error'] = 'No data found to export.';
		echo json_encode($msg);
		return;
	}
	$data = 0;
	$q = mysqli_query($conn, "SELECT * FROM ingProfiles WHERE owner_id = '$userID'");
	while($resData = mysqli_fetch_assoc($q)){
		
		//$r['id'] = (int)$resData['id'];
		$r['name'] = (string)$resData['name']?: "-";
		$r['notes'] = (string)$resData['notes']?: "-";
		$r['image'] = (string)$resData['image'] ?: "-";
		
		$data++;
		$cat[] = $r;
	}
	
	$vd['product'] = $product;
	$vd['version'] = $ver;
	$vd['ingCategory'] = $data;
	$vd['timestamp'] = date('d/m/Y H:i:s');

	
	$result['ingProfiles'] = $cat;
	$result['pvMeta'] = $vd;
	
	header('Content-disposition: attachment; filename=IngProfiles.json');
	header('Content-type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);
	return;
}

//ADD tags
if($_POST['action'] == 'tagadd' && $_POST['fid'] && $_POST['tag']){
	if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM formulasTags WHERE formula_id='".$_POST['fid']."' AND tag_name = '".$_POST['tag']."' AND owner_id = '$userID'"))){
		$response[] = '';
		echo json_encode($response);
		return;
	}
	mysqli_query($conn,"INSERT INTO formulasTags (formula_id,tag_name,owner_id) VALUES('".$_POST['fid']."','".$_POST['tag']."','$userID')" );
	$response[] = '';
	echo json_encode($response);
	return;
}

//REMOVE tags
if($_POST['action'] == 'tagremove' && $_POST['fid'] && $_POST['tag']){
	mysqli_query($conn,"DELETE FROM formulasTags WHERE formula_id='".$_POST['fid']."' AND tag_name = '".$_POST['tag']."' AND owner_id = '$userID'" );
	$response[] = '';
	echo json_encode($response);
	return;
}

//RATING UPDATE
if($_POST['update_rating'] == '1' && $_POST['fid'] && is_numeric($_POST['score'])){
	mysqli_query($conn,"UPDATE formulasMetaData SET rating = '".$_POST['score']."' WHERE id = '".$_POST['fid']."' AND owner_id = '$userID'");
	return;
}

//EXCLUDE/INCLUDE INGREDIENT
if($_POST['action'] == 'excIng' && $_POST['ingID']){
	$id = mysqli_real_escape_string($conn, $_POST['ingID']);
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$ing = mysqli_real_escape_string($conn, $_POST['ingName']);

	$status = (int)$_POST['status'];
	if($status == 1){
		$st = 'excluded from calclulations';
	}else{
		$st = 'included in calculations';
	}
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '$fid' AND owner_id = '$userID'"));
	if($meta['isProtected'] == FALSE){
		if(mysqli_query($conn, "UPDATE formulas SET exclude_from_calculation = '$status' WHERE id  = '$id' AND owner_id = '$userID'")){
			$response['success'] = $ing.' is now '. $st;
		}else{
			$response['error'] = $ing.' cannot be '.$st.' from the formula!';
		}
	}
	
	echo json_encode($response);
	return;
}

//IS MADE
if($_POST['isMade'] && $_POST['fid']){
	$fid = mysqli_real_escape_string($conn,$_POST['fid']);
	
	$quant = mysqli_query($conn, "SELECT ingredient,quantity FROM formulas WHERE fid = '$fid' AND owner_id = '$userID'");
	while($get_quant = mysqli_fetch_array($quant)){
		$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '".$get_quant['ingredient']."' AND owner_id = '$userID'"));
		$q = "UPDATE suppliers SET stock = GREATEST(0, stock - '".$get_quant['quantity']."') WHERE ingID = '".$ing['id']."' AND stock = GREATEST(stock, '".$get_quant['quantity']."') AND owner_id = '$userID'";
		$upd = mysqli_query($conn, $q);	
		
	}
	if($upd){
		mysqli_query($conn,"UPDATE formulasMetaData SET isMade = '1', madeOn = NOW() WHERE fid = '$fid' AND owner_id = '$userID'");
		$response['success'] = 'Inventory updated';
	}else{
		$response['error'] = "Something went wrong, check logs for more info".mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}


//CREATE ACCORD
if($_POST['accordName'] && $_POST['accordProfile'] && $_POST['fid']){
	require_once(__ROOT__.'/func/genFID.php');

	$fid = mysqli_real_escape_string($conn,$_POST['fid']);
	$accordProfile = mysqli_real_escape_string($conn,$_POST['accordProfile']);
	$accordName = mysqli_real_escape_string($conn,$_POST['accordName']);
	$nfid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');
	
	if(mysqli_num_rows(mysqli_query($conn,"SELECT name FROM formulasMetaData WHERE name = '$accordName' AND owner_id = '$userID'"))){
		$response['error'] = 'A formula with name '.$accordName.' already exists, please choose a different name';
		echo json_encode($response);
		return;
	}
									
	$get_formula = mysqli_query($conn,"SELECT ingredient FROM formulas WHERE fid = '$fid' AND owner_id = '$userID'");
	while($formula = mysqli_fetch_array($get_formula)){
        if($i = mysqli_fetch_array(mysqli_query($conn,"SELECT name,profile FROM ingredients WHERE profile = '$accordProfile' AND name ='".$formula['ingredient']."' AND owner_id = '$userID'"))){
        	mysqli_query($conn, "INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes, owner_id) SELECT '$nfid', '$accordName', ingredient, ingredient_id, concentration, dilutant, quantity, notes, '$userID' FROM formulas WHERE fid = '$fid' AND ingredient = '".$i['name']."' AND owner_id = '$userID'");
		}
	}
	if(mysqli_query($conn,"INSERT INTO formulasMetaData (fid,name,owner_id) VALUES ('$nfid','$accordName', '$userID')")){
		$response['success'] =  'Accord <a href="/?do=Formula&id='.mysqli_insert_id($conn).'" target="_blank">'.$accordName.'</a> created';
	}
	echo json_encode($response);
	return;
}

//RESTORE REVISION
if ($_GET['restore'] === 'rev' && !empty($_GET['revision']) && !empty($_GET['fid'])) {
    // Escape inputs to prevent SQL injection
    $fid = mysqli_real_escape_string($conn, $_GET['fid']);
    $revision = mysqli_real_escape_string($conn, $_GET['revision']);

    // Delete existing formula
    $deleteQuery = "DELETE FROM formulas WHERE fid = '$fid' AND owner_id = '$userID'";
    if (!mysqli_query($conn, $deleteQuery)) {
        error_log("PV error: Failed to delete formula: " . mysqli_error($conn));
        echo json_encode(['error' => 'Failed to delete formula']);
        return;
    }

    // Restore formula from revision
    $restoreQuery = "
        INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes, owner_id)
        SELECT fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes, '$userID'
        FROM formulasRevisions
        WHERE fid = '$fid' AND revision = '$revision' AND owner_id = '$userID'";
    if (mysqli_query($conn, $restoreQuery)) {
        // Update metadata with the restored revision
        $updateMetaQuery = "
            UPDATE formulasMetaData 
            SET revision = '$revision' 
            WHERE fid = '$fid' AND owner_id = '$userID'";
        if (!mysqli_query($conn, $updateMetaQuery)) {
            error_log("PV error: Failed to update formula metadata: " . mysqli_error($conn));
            echo json_encode(['error' => 'Failed to update formula metadata']);
            return;
        }

        // Success response
        echo json_encode(['success' => 'Formula revision restored']);
    } else {
        error_log("PV error: Failed to restore formula revision: " . mysqli_error($conn));
        echo json_encode(['error' => 'Unable to restore revision']);
    }
	return;
}

//DELETE REVISION
if($_GET['delete'] == 'rev' && $_GET['revision'] && $_GET['fid']){
	$fid = mysqli_real_escape_string($conn,$_GET['fid']);
	$revision = $_GET['revision'];
	
	if(mysqli_query($conn,"DELETE FROM formulasRevisions WHERE fid = '$fid' AND revision = '$revision' AND owner_id = '$userID'")){
		$response['success'] = 'Formula revision deleted';
	}else{
		$response['error'] = 'Unable to delete revision '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//MANAGE VIEW
if ($_GET['manage_view'] == '1') {
    $ing = mysqli_real_escape_string($conn, str_replace('_', ' ', $_GET['ex_ing']));
    $fid = mysqli_real_escape_string($conn, $_GET['fid']);
    $ex_status = filter_var($_GET['ex_status'], FILTER_VALIDATE_BOOLEAN);

    $status = $ex_status ? '0' : '1';

    $query = "UPDATE formulas SET exclude_from_summary = '$status' WHERE fid = '$fid' AND ingredient = '$ing' AND owner_id = '$userID'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $response = ['success' => 'View updated'];
    } else {
        $response = ['error' => 'Something went wrong', 'details' => mysqli_error($conn)];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    return;
}

//SCALE FORMULA
if ($_POST['fid'] && $_POST['action'] == 'advancedScale' && $_POST['SG'] && $_POST['amount']) {
    $fid = mysqli_real_escape_string($conn, $_POST['fid']);
    $SG = mysqli_real_escape_string($conn, $_POST['SG']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);

    $new_amount = $amount * $SG;
    $mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '$fid' AND owner_id = '$userID'"));

    $q = mysqli_query($conn, "SELECT quantity, ingredient FROM formulas WHERE fid = '$fid' AND owner_id = '$userID'");
    $all_success = true;

    while ($cur = mysqli_fetch_array($q)) {
        $nq = $cur['quantity'] / $mg['total_mg'] * $new_amount;
        
        if (empty($nq)) {
            $response['error'] = 'Something went wrong';
            echo json_encode($response);
            return;
        }

        $update = mysqli_query($conn, "UPDATE formulas SET quantity = '$nq' WHERE fid = '$fid' AND quantity = '" . $cur['quantity'] . "' AND ingredient = '" . $cur['ingredient'] . "' AND owner_id = '$userID'");
        
        if (!$update) {
            $all_success = false;
            $error_message = mysqli_error($conn);
            break; 
        }
    }

    if ($all_success) {
        $response['success'] = 'Formula scaled';
    } else {
        $response['error'] = 'Something went wrong ' . $error_message;
    }

    echo json_encode($response);
    return;
}


//DIVIDE - MULTIPLY
if ($_POST['formula'] && $_POST['action'] == 'simpleScale') {
    $fid = mysqli_real_escape_string($conn, $_POST['formula']);
    $q = mysqli_query($conn, "SELECT quantity, ingredient FROM formulas WHERE fid = '$fid'");
    $all_success = true;
	
    while ($cur = mysqli_fetch_array($q)) {
        // Calculate the new quantity based on the scale action
        if ($_POST['scale'] == 'multiply') {
            $nq = $cur['quantity'] * 2;
        } elseif ($_POST['scale'] == 'divide') {
            $nq = $cur['quantity'] / 2;
        } else {
            $all_success = false;
            $error_message = "Invalid scale action.";
            break; 
        }

        $update = mysqli_query($conn, "UPDATE formulas SET quantity = '$nq' WHERE fid = '$fid' AND quantity = '" . $cur['quantity'] . "' AND ingredient = '" . $cur['ingredient'] . "' AND owner_id = '$userID'");
        
        if (!$update) {
            $all_success = false;
            $error_message = mysqli_error($conn); 
            break; 
        }
    }

    if ($all_success) {
        $response['success'] = 'Formula scaled successfully';
    } else {
        $response['error'] = 'Error during scaling: ' . $error_message;
    }

    echo json_encode($response);
    return;
}


//DELETE INGREDIENT
if($_POST['action'] == 'deleteIng' && $_POST['ingID'] && $_POST['ing']){
	$id = mysqli_real_escape_string($conn, $_POST['ingID']);
	$ing = mysqli_real_escape_string($conn, $_POST['ing']);
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$ingredient_id = mysqli_real_escape_string($conn, $_POST['ingredient_id']);
	
	if($_POST['reCalc'] == 'true'){
		if(!$_POST['formulaSolventID']){
			$response["error"] = 'Please select solvent';
			echo json_encode($response);
			return;
		}
		$formulaSolventID = $_POST['formulaSolventID'];
		
		if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM ingredients WHERE id = '".$ingredient_id."' AND profile='solvent' AND owner_id = '$userID'"))){
			$response["error"] = 'You cannot deduct a solvent from a solvent';
			echo json_encode($response);
			return;
		}
		
		$qs = mysqli_fetch_array(mysqli_query($conn,"SELECT quantity FROM formulas WHERE id = '$id' AND fid = '$fid' AND owner_id = '$userID'"));
		$v = $qs['quantity'];
		mysqli_query($conn,"UPDATE formulas SET quantity = quantity + $v WHERE fid = '$fid' AND ingredient_id = '".$formulaSolventID."' AND owner_id = '$userID'");

	}
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '$fid' AND owner_id = '$userID'"));

	if($meta['isProtected'] == FALSE){
		
		if(mysqli_query($conn, "DELETE FROM formulas WHERE id = '$id' AND fid = '$fid' AND owner_id = '$userID'")){
			$response['success'] = $ing.' removed from the formula';
			$lg = "REMOVED: $ing removed";
			mysqli_query($conn, "INSERT INTO formula_history (fid,ing_id,change_made,user,owner_id) VALUES ('".$meta['id']."','".$ingredient_id."','$lg','".$user['fullName']."', '$userID')");
		}else{
			$response['error'] = $ing.' cannot be removed from the formula';
		}
	}
	echo json_encode($response);
	return;
}

//ADD INGREDIENT
if($_POST['action'] == 'addIngToFormula'){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$id = mysqli_real_escape_string($conn, $_POST['id']);
	$ingredient_id = mysqli_real_escape_string($conn, $_POST['ingredient']);
	$quantity = preg_replace("/[^0-9.]/", "", mysqli_real_escape_string($conn, $_POST['quantity']));
	$concentration = preg_replace("/[^0-9.]/", "", mysqli_real_escape_string($conn, $_POST['concentration']));
	$dilutant = mysqli_real_escape_string($conn, $_POST['dilutant']);
	$ingredient = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingredients WHERE id = '$ingredient_id' AND owner_id = '$userID'"));
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected,name FROM formulasMetaData WHERE fid = '$fid' AND owner_id = '$userID'"));
	if($meta['isProtected'] == TRUE){
		$response["error"] = 'Formula is protected and cannot be modified';
		echo json_encode($response);
		return;
	}
	
	if (empty($quantity) || empty($concentration)){
		$response['error'] = 'Missing required fields';
		echo json_encode($response);
		return;
	}
			
	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient_id FROM formulas WHERE ingredient_id = '$ingredient_id' AND fid = '$fid' AND owner_id = '$userID'"))){
		$response['error'] = $ingredient['name'].' already exists in formula';
		echo json_encode($response);
		return;
	}
	
	if($_POST['reCalc'] == 'true'){
		if(!$_POST['formulaSolventID']){
			$response["error"] = 'Please select solvent';
			echo json_encode($response);
			return;
		}
		
		$formulaSolventID = $_POST['formulaSolventID'];
		
		if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM ingredients WHERE id = '".$ingredient_id."' AND profile='solvent' AND owner_id = '$userID'"))){
			$response["error"] = 'You cannot add a solvent to a solvent';
			echo json_encode($response);
			return;
		}
		
		$slv = mysqli_fetch_array(mysqli_query($conn,"SELECT quantity FROM formulas WHERE ingredient_id = '".$formulaSolventID."' AND fid = '".$fid."' AND owner_id = '$userID'"));

        if($slv['quantity'] < $quantity){
        	$response["error"] = 'Not enough solvent, available: '.number_format($slv['quantity'],$settings['qStep']).$settings['mUnit'];
            echo json_encode($response);
            return;
        }
				
		mysqli_query($conn,"UPDATE formulas SET quantity = quantity - $quantity WHERE fid = '$fid' AND ingredient_id = '".$formulaSolventID."' AND owner_id = '$userID'");

	}
	
	if(mysqli_query($conn,"INSERT INTO formulas(fid,name,ingredient,ingredient_id,concentration,quantity,dilutant,owner_id) VALUES('$fid','".$meta['name']."','".$ingredient['name']."','".$ingredient_id."','$concentration','$quantity','$dilutant','$userID')")){
			
		$lg = "ADDED: ".$ingredient['name']." $quantity".$settings['mUnit']." @$concentration% $dilutant";
		mysqli_query($conn, "INSERT INTO formula_history (fid,ing_id,change_made,user,owner_id) VALUES ('".$id."','$ingredient_id','$lg','".$user['fullName']."','$userID')");
		mysqli_query($conn, "UPDATE formulasMetaData SET status = '1' WHERE fid = '".$fid."' AND status = '0' AND isProtected = '0'");
			
		$response['success'] = '<strong>'.$quantity.$settings['mUnit'].'</strong> of <strong>'.$ingredient['name'].'</strong> added to the formula';
		echo json_encode($response);
		return;
	} else {
		$response['error'] = 'Something went wrong '.mysqli_error($conn);
		echo json_encode($response);
	}
		
 	if(mysqli_error($conn)){
		$response['error'] = 'Something went wrong '.mysqli_error($conn);
		echo json_encode($response);
	}
	return;
}

//REPLACE INGREDIENT
if($_POST['action'] == 'repIng' && $_POST['fid']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	
	if(!$_POST['dest']){
		$response['error'] = 'Please select ingredient';
		echo json_encode($response);
		return;
	}
	
	$ingredient = mysqli_real_escape_string($conn, $_POST['dest']);
	$oldIngredient = mysqli_real_escape_string($conn, $_POST['ingSrcName']);
	$ingredient_id = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$ingredient' AND owner_id = '$userID'"));
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '$fid' AND owner_id = '$userID'"));
	if($meta['isProtected'] == FALSE){
		if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '$ingredient' AND fid = '$fid' AND owner_id = '$userID'"))){
			$response['error'] = $ingredient.' already exists in formula';
			echo json_encode($response);
			return;
		}
		
		if(mysqli_query($conn, "UPDATE formulas SET ingredient = '$ingredient', ingredient_id = '".$ingredient_id['id']."' WHERE ingredient = '$oldIngredient' AND id = '".$_POST['ingSrcID']."' AND fid = '$fid' AND owner_id = '$userID'")){
			$response['success'] = $oldIngredient.' replaced by '.$ingredient;
			$lg = "REPLACED: $oldIngredient WITH $ingredient";
			mysqli_query($conn, "INSERT INTO formula_history (fid,ing_id,change_made,user,owner_id) VALUES ('".$meta['id']."','".$ingredient_id['id']."','$lg','".$user['fullName']."','$userID')");
		}else{
			$response['error'] = 'Error replacing '.$oldIngredient;
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode($response);
	return;
}

//Convert to ingredient
if($_POST['action'] == 'conv2ing' && $_POST['ingName'] && $_POST['fid']){
	$name = mysqli_real_escape_string($conn, $_POST['ingName']);
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$fname = mysqli_real_escape_string($conn, $_POST['fname']);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$name' AND owner_id = '$userID'"))){
		$response['error'] = '<a href="/?do=ingredients&search='.$name.'" target="_blank">'.$name.'</a> already exists';
		echo json_encode($response);
		return;
	}

	$formula_q = mysqli_query($conn, "SELECT ingredient,quantity,concentration FROM formulas WHERE fid = '$fid' AND owner_id = '$userID'");
	while ($formula = mysqli_fetch_array($formula_q)){
		$ing_data = mysqli_fetch_array(mysqli_query($conn,"SELECT cas FROM ingredients WHERE name = '".$formula['ingredient']."' AND owner_id = '$userID'"));
		$conc = number_format($formula['quantity']/100 * 100, $settings['qStep']);
		$conc_p = number_format($formula['concentration'] / 100 * $conc, $settings['qStep']);
						
		mysqli_query($conn, "INSERT INTO ingredient_compounds (ing, name, cas, min_percentage, max_percentage, owner_id) VALUES ('$name','".$formula['ingredient']."','".$ing_data['cas']."','".$conc_p."','".$conc_p."','$userID')");
	}
			
	if(mysqli_query($conn, "INSERT INTO ingredients (name, type, cas, notes, owner_id) VALUES ('$name','Base','Mixture','Converted from formula $fname','$userID')")){
		$response['success'] = '<a href="/?do=ingredients&search='.$name.'" target="_blank">'.$name.'</a> converted to ingredient';
		echo json_encode($response);
	}
	return;

}

//DUPLICATE FORMULA
if($_POST['action'] == 'clone' && $_POST['fid']){
	require_once(__ROOT__.'/func/genFID.php');

	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$fname = mysqli_real_escape_string($conn, $_POST['fname']);

	$newName = $fname.' - (Copy)';
	$newFid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE name = '$newName' AND owner_id = '$userID'"))){
		$response['error'] = $newName.' already exists, please choose a different name</div>';
		echo json_encode($response);
        return;
    }
	$sql1 = "INSERT INTO formulasMetaData (fid, name, notes, profile, gender, defView, product_name, catClass, owner_id) SELECT '$newFid', '$newName', notes, profile, gender, defView, '$newName', catClass, '$userID' FROM formulasMetaData WHERE fid = '$fid' AND owner_id = '$userID'";
    $sql2 = "INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, notes, owner_id) SELECT '$newFid', '$newName', ingredient, ingredient_id, concentration, dilutant, quantity, notes, '$userID' FROM formulas WHERE fid = '$fid' AND owner_id = '$userID'";
    
    if(mysqli_query($conn, $sql1) && mysqli_query($conn, $sql2)) {
        // Fetch the id of the newly inserted record
        $nID = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$newFid' AND owner_id = '$userID'"));
        if($nID){
            $response['success'] = $fname.' cloned as <a href="/?do=Formula&id='.$nID['id'].'" target="_blank">'.$newName.'</a></div>';
        } else {
            $response['error'] = "Failed to fetch ID of cloned record!";
			echo json_encode($response);
			return;
        }
    } else {
        $response['error'] = "Failed to clone formula!";
		echo json_encode($response);
		return;
    }
	echo json_encode($response);
	return;
}

//ADD NEW FORMULA
if($_POST['action'] == 'addFormula'){
	if(empty($_POST['name'])){
		$response['error'] = 'Formula name is required.';
		echo json_encode($response);
		return;
	}
	
	if(strlen($_POST['name']) > '100'){
		$response['error'] = 'Formula name is too big. Max 100 chars allowed.';
		echo json_encode($response);
		return;
	}
	
	require_once(__ROOT__.'/func/genFID.php');
	
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$notes = mysqli_real_escape_string($conn, $_POST['notes']);
	$profile = mysqli_real_escape_string($conn, $_POST['profile']);
	$catClass = mysqli_real_escape_string($conn, $_POST['catClass']);
	$finalType = mysqli_real_escape_string($conn, $_POST['finalType']);
	$customer_id = $_POST['customer']?:0;
	$fid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE name = '$name' AND owner_id = '$userID'"))){
		$response['error'] = $name.' already exists!';
	}else{
		if(mysqli_query($conn, "INSERT INTO formulasMetaData (fid, name, notes, profile, catClass, finalType, customer_id, owner_id) VALUES ('$fid', '$name', '$notes', '$profile', '$catClass', '$finalType', '$customer_id', '$userID')")){
			$last_id = mysqli_insert_id($conn);
			$fullver = $product.' '.$ver;
			mysqli_query($conn, "INSERT INTO formulasTags (formula_id, tag_name, owner_id) VALUES ('$last_id','$fullver','$userID')");
			$response = array(
				"success" => array(
				"id" => (int)$last_id,
				"msg" => "$name added!",
				)
			);
		}else{
			$response['error'] = 'Something went wrong '.mysqli_error($conn);
		}
	}

	echo json_encode($response);
	return;
}

//ADD NEW AI FORMULA
if ($_POST['action'] == 'addFormulaAI') {
    require_once(__ROOT__.'/core/pvAI.php');
    return;
}


//AI CHAT
if ($_POST['action'] == 'aiChat' && $_POST['message']) {
    require_once(__ROOT__.'/core/pvAI.php');
    return;
}

//DELETE FORMULA
if($_POST['action'] == 'deleteFormula' && $_POST['fid']){

	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$fname = mysqli_real_escape_string($conn, $_POST['fname']);

	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid' AND isProtected = '1' AND owner_id = '$userID'"))){
		$response['error'] = 'Formula '.$fname.' is protected and cannot be deleted';
		echo json_encode($response);
		return;
	}

	if($_POST['archiveFormula'] == "true"){
        
		require_once(__ROOT__.'/libs/fpdf.php');
		require_once(__ROOT__.'/func/genBatchPDF.php');
		require_once(__ROOT__.'/func/ml2L.php');
        require_once(__ROOT__.'/func/genFID.php');

		define('FPDF_FONTPATH',__ROOT__.'/fonts');
        $nfid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');
		$defCatClass = $settings['defCatClass'];
		$arcID = "Archived-".$fname.$nfid;
		
		$rs = genBatchPDF($fid,$arcID,'100','100','100',$defCatClass,$settings['qStep'],$settings['defPercentage'],'formulas');
		
		if($rs !== true){
			$response['error'] = 'Error archiving the formula, '.$rs['error'];
			echo json_encode($response);
			return;
		}

	}
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid' AND owner_id = '$userID'"));

	if(mysqli_query($conn, "DELETE FROM formulas WHERE fid = '$fid' AND owner_id = '$userID'")){
		mysqli_query($conn, "DELETE FROM formulasMetaData WHERE fid = '$fid' AND owner_id = '$userID'");
		mysqli_query($conn, "DELETE FROM formulasRevisions WHERE fid = '$fid' AND owner_id = '$userID'");
		mysqli_query($conn, "DELETE FROM formula_history WHERE fid = '".$meta['id']."' AND owner_id = '$userID'");
		mysqli_query($conn, "DELETE FROM formulasTags WHERE formula_id = '".$meta['id']."' AND owner_id = '$userID'");
		mysqli_query($conn, "DELETE FROM makeFormula WHERE fid = '$fid' AND owner_id = '$userID'");
		$response['success'] = 'Formula '.$fname.' deleted';
	}else{
		$response['error'] = 'Error deleting '.$fname.' formula';
	}
	echo json_encode($response);
	return;
}

//RESET ING IN MAKE FORMULA
if($_POST['action'] == 'makeFormula' && $_POST['undo'] == '1'){
	$q = trim($_POST['originalQuantity']);
	$ingID = mysqli_real_escape_string($conn, $_POST['ingID']);
	$repName = $_POST['repName'];

	if(mysqli_query($conn, "UPDATE makeFormula SET replacement_id = '0', toAdd = '1', skip = '0', overdose = '0', quantity = '".$_POST['originalQuantity']."' WHERE id = '".$_POST['ID']."' AND owner_id = '$userID'")){
		if(!empty($repName)) {
			$msg = $repName."'s quantity reset";
		}else{
			$msg = $_POST['ing']."'s quantity reset";
		}
		$response['success'] = $msg;
		
		if($_POST['resetStock'] == "true"){
			if(!($_POST['supplier'])){
				$response['error'] = 'Please select a supplier';
				echo json_encode($response);
				return;
			}
			$nIngID = $_POST['repID'] ?: $ingID;
			mysqli_query($conn, "UPDATE suppliers SET stock = stock + $q WHERE ingID = '$nIngID' AND ingSupplierID = '".$_POST['supplier']."' AND owner_id = '$userID'");
			$response['success'] .= "<br/><strong>Stock increased by ".$q.$settings['mUnit']."</strong>";
		}
		echo json_encode($response);
	}
	return;
}

//MAKE FORMULA
if($_POST['action'] == 'makeFormula' && $_POST['fid'] && $_POST['qr'] && $_POST['id']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$id = mysqli_real_escape_string($conn, $_POST['id']);
	
	if($_POST['repID']) {
		$repID = $_POST['repID'];
		$ingID = $_POST['repID'];
	} else {
		$repID = 0;
		$ingID = $_POST['ingId'];
	}

	$ingredient =  mysqli_real_escape_string($conn, $_POST['repName'] ?: $_POST['ing']);
	
	$notes = mysqli_real_escape_string($conn, $_POST['notes']) ?: "-";

	$qr = trim($_POST['qr']);
	$q = trim($_POST['q']);
	
	
	if(!is_numeric($_POST['q'])){
		$response['error'] = 'Invalid amount value';
		echo json_encode($response);
		return;
	}
						 
	
	if($_POST['updateStock'] == "true"){
		if(!($_POST['supplier'])){
			$response['error'] = 'Please select a supplier';
			echo json_encode($response);
			return;
		}
		$getStock = mysqli_fetch_array(mysqli_query($conn, "SELECT stock,mUnit FROM suppliers WHERE ingID = '$ingID' AND ingSupplierID = '".$_POST['supplier']."' AND owner_id = '$userID'"));
		if($getStock['stock'] < $q){
			$w = "<p>Amount exceeds quantity available in stock (".$getStock['stock'].$getStock['mUnit']."). The maximum available will be deducted from stock</p>";
			
			$q = $getStock['stock'] ?: 0;
		}
		mysqli_query($conn, "UPDATE suppliers SET stock = stock - $q WHERE ingID = '$ingID' AND ingSupplierID = '".$_POST['supplier']."' AND owner_id = '$userID'");
		$response['success'] .= "<br/><strong>Stock deducted by ".$q.$settings['mUnit']."</strong>";
	}
	
	$q = trim($_POST['q']); //DIRTY HACK - TODO
	
	if($qr == $q){
		if(mysqli_query($conn, "UPDATE makeFormula SET replacement_id = '$repID', toAdd = '0', notes = '$notes' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")){
			$response['success'] = $ingredient.' added in the formula.'.$w;
		} else {
			$response['error'] = mysqli_error($conn);
		}
	}else{
		$sub_tot = $qr - $q;
		if(mysqli_query($conn, "UPDATE makeFormula SET  replacement_id = '$repID', quantity='$sub_tot', notes = '$notes' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")){
			$response['success'] = 'Formula updated';
		}
	}

		
	if($qr < $q){
		if(mysqli_query($conn, "UPDATE makeFormula SET overdose = '$q' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")){
			$response['success'] = $_POST['ing'].' is overdosed, <strong>'.$q.'<strong> added';
		}
	}
	
	if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid' AND toAdd = '1' AND owner_id = '$userID'"))){
		$response['success'] = '<strong>All materials added. You should mark formula as complete now</strong>';
	}
	
	
	echo json_encode($response);
	return;
}



//SKIP MATERIAL FROM MAKE FORMULA
if($_POST['action'] == 'skipMaterial' && $_POST['fid'] &&  $_POST['id']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$id = mysqli_real_escape_string($conn, $_POST['id']);
	$ingID = mysqli_real_escape_string($conn, $_POST['ingId']);
	$notes = mysqli_real_escape_string($conn, $_POST['notes']) ?: "-";

	if(mysqli_query($conn, "UPDATE makeFormula SET skip = '1', notes = '$notes' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")){
		$response['success'] = $_POST['ing'].' skipped from the formulation';
	} else {
		$response['error'] = 'Error skipping the ingredient';
	}
	
	echo json_encode($response);
	return;
}



//MARK COMPLETE
if($_POST['action'] == 'todo' && $_POST['fid'] && $_POST['markComplete']){
	require_once(__ROOT__.'/libs/fpdf.php');
	require_once(__ROOT__.'/func/genBatchID.php');
	require_once(__ROOT__.'/func/genBatchPDF.php');
	require_once(__ROOT__.'/func/ml2L.php');

	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$total_quantity = mysqli_real_escape_string($conn, $_POST['totalQuantity']);

	define('FPDF_FONTPATH',__ROOT__.'/fonts');
	$defCatClass = $settings['defCatClass'];
	
		
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid' AND toAdd = '1' AND skip = '0' AND owner_id = '$userID'"))){
		$response['error'] = '<strong>Formula is pending materials to add, cannot be marked as complete.</strong>';
		echo json_encode($response);
		return;
	}
	if(mysqli_query($conn,"UPDATE formulasMetaData SET isMade = '1', toDo = '0', madeOn = NOW(), status = '2' WHERE fid = '$fid' AND owner_id = '$userID'")){
		$batchID = genBatchID();
		genBatchPDF($fid,$batchID,$total_quantity,'100',$total_quantity,$defCatClass,$settings['qStep'],$settings['defPercentage'],'makeFormula');

		mysqli_query($conn, "DELETE FROM makeFormula WHERE fid = '$fid' AND owner_id = '$userID'");
		
		$response['success'] = '<strong>Formula is complete</strong>';
	}
	
	echo json_encode($response);
	return;
}


//MAKING ADD FORMULA
if($_POST['action'] == 'todo' && $_POST['fid'] && $_POST['add']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$fname = mysqli_real_escape_string($conn, $_POST['fname']);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid' AND toDo = '1' AND owner_id = '$userID'"))){
		$response['error'] = 'Formula '.$fname.' is already scheduled';
		echo json_encode($response);
		return;
	}
								
	if(mysqli_query($conn, "INSERT INTO makeFormula (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, originalQuantity, toAdd, owner_id) SELECT fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, quantity, '1', '$userID' FROM formulas WHERE fid = '$fid' AND exclude_from_calculation = '0' AND owner_id = '$userID'")){
		mysqli_query($conn, "UPDATE formulasMetaData SET toDo = '1', status = '1', isMade = '0', scheduledOn = NOW() WHERE fid = '$fid' AND owner_id = '$userID'");
		$response['success'] = 'Formula <a href="/?do=scheduledFormulas">'.$fname.'</a> scheduled for making';
	}else{
		$response['error'] = 'An error occured '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

//MAKING REMOVE FORMULA
if($_POST['action'] == 'todo' && $_POST['fid'] && $_POST['remove']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "DELETE FROM makeFormula WHERE fid = '$fid' AND owner_id = '$userID'")){
		mysqli_query($conn, "UPDATE formulasMetaData SET toDo = '0', status = '0', isMade = '0' WHERE fid = '$fid' AND owner_id = '$userID'");
		$response['success'] = $name.' removed';
		echo json_encode($response);
	}
	return;
}

//CART MANAGE
if($_POST['action'] == 'addToCart' && $_POST['material'] && $_POST['quantity']){
	$material = mysqli_real_escape_string($conn, $_POST['material']);
	$quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
	$purity = mysqli_real_escape_string($conn, $_POST['purity']);
	$ingID = mysqli_real_escape_string($conn, $_POST['ingID']);

	$qS = mysqli_fetch_array(mysqli_query($conn, "SELECT ingSupplierID, supplierLink FROM suppliers WHERE ingID = '$ingID' AND owner_id = '$userID'"));
	
	if(empty($qS['supplierLink'])){
		$response['error'] = $material.' cannot be added to cart as missing supplier info. Please update material supply details first.';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM cart WHERE name = '$material' AND owner_id = '$userID'"))){
		if(mysqli_query($conn, "UPDATE cart SET quantity = quantity + '$quantity' WHERE name = '$material' AND owner_id = '$userID'")){
			$response['success'] = 'Additional '.$quantity.$settings['mUnit'].' of '.$material.' added to the cart.';
		}
		echo json_encode($response);
		return;
	}
									
	if(mysqli_query($conn, "INSERT INTO cart (ingID,name,quantity,purity,owner_id) VALUES ('$ingID','$material','$quantity','$purity','$userID')")){
		$response['success'] = $material.' added to the cart!';
		echo json_encode($response);
		return;
	}
	
	return;
}

if($_POST['action'] == 'removeFromCart' && $_POST['materialId']){
	$materialId = mysqli_real_escape_string($conn, $_POST['materialId']);

	if(mysqli_query($conn, "DELETE FROM cart WHERE id = '$materialId' AND owner_id = '$userID'")){
		$response['success'] = $_POST['materialName'].' removed from cart!';
		echo json_encode($response);
	}
}


//VIEW BOX BACK LABEL
if($_GET['action'] == 'viewBoxLabel' && $_GET['fid']){
    $fid = mysqli_real_escape_string($conn, $_GET['fid']);
    
    $q = mysqli_fetch_array(mysqli_query($conn, "SELECT name, product_name FROM formulasMetaData WHERE fid = '$fid' AND owner_id = '$userID'"));
    $name = $q['name'];
    $qIng = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '$fid' AND owner_id = '$userID'");
    $branding = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM branding WHERE owner_id = '$userID'"));

    $allergen = [];
    while($ing = mysqli_fetch_array($qIng)){
        $chName = mysqli_fetch_array(mysqli_query($conn, "SELECT chemical_name, name FROM ingredients WHERE name = '".$ing['ingredient']."' AND allergen = '1' AND owner_id = '$userID'"));
        
        if($qCMP = mysqli_query($conn, "SELECT name FROM ingredient_compounds WHERE ing = '".$ing['ingredient']."' AND toDeclare = '1' AND owner_id = '$userID'")){
            while($cmp = mysqli_fetch_array($qCMP)){
                $allergen[] = $cmp['name'];
            }
        }
        $allergen[] = $chName['chemical_name'] ?: $chName['name'];
    }
    $allergen[] = 'Denatured Ethyl Alcohol '.$_GET['carrier'].'% Vol, Fragrance, DPG, Distilled Water';
    
    $bNo = $_GET['batchID'] ?: 'N/A';
    $brand = $branding['brandName'] ?: 'PV Pro';
    $allergenFinal = implode(", ", array_filter(array_unique($allergen)));
    $info = "FOR EXTERNAL USE ONLY. \nKEEP AWAY FROM HEAT AND FLAME. \nKEEP OUT OF REACH OF CHILDREN. \nAVOID SPRAYING IN EYES. \n \nProduction: ".date("d/m/Y")." \nB. NO: $bNo \n$brand";

    echo "<pre>";
    echo "<strong>$name</strong>\n\n";
    echo "INGREDIENTS\n\n";
    echo wordwrap($allergenFinal, 90)."\n\n";
    echo wordwrap($info, 50)."\n\n";
    echo "</pre>";

    return;
}

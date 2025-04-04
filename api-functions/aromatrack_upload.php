<?php

if (!defined('pvault_panel')){ die('Not Found');}

header('Content-Type: application/json');
global $conn, $userID;

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

//error_log(print_r($data, true));

if ($data === null) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    exit();
}

if (isset($_GET['kind']) && $_GET['kind'] === 'supplier') {
    $name = mysqli_real_escape_string($conn, $data['name']);
    $updated_at = date('Y-m-d H:i:s');

    // Check if the supplier exists by name and owner_id
    $check_query = "SELECT id FROM ingSuppliers WHERE name = '$name' AND owner_id = '$userID' LIMIT 1";
    $result = mysqli_query($conn, $check_query);
    if ($result && mysqli_num_rows($result) > 0) {
        // Update existing supplier
        $row = mysqli_fetch_assoc($result);
        $supplier_id = $row['id'];

        $update_fields = [];
        if (isset($data['notes'])) {
            $notes = mysqli_real_escape_string($conn, $data['notes']);
            $update_fields[] = "notes = '$notes'";
        }
        if (isset($data['telephone'])) {
            $telephone = mysqli_real_escape_string($conn, $data['telephone']);
            $update_fields[] = "telephone = '$telephone'";
        }
        if (isset($data['url'])) {
            $url = mysqli_real_escape_string($conn, $data['url']);
            $update_fields[] = "url = '$url'";
        }
        if (isset($data['email'])) {
            $email = mysqli_real_escape_string($conn, $data['email']);
            $update_fields[] = "email = '$email'";
        }
        if (isset($data['add_costs'])) {
            $add_costs = (float)$data['add_costs'];
            $update_fields[] = "add_costs = $add_costs";
        }
        if (isset($data['currency'])) {
            $currency = mysqli_real_escape_string($conn, $data['currency']);
            $update_fields[] = "currency = '$currency'";
        }
        if (isset($data['address'])) {
            $address = mysqli_real_escape_string($conn, $data['address']);
            $update_fields[] = "address = '$address'";
        }
        if (isset($data['po'])) {
            $po = mysqli_real_escape_string($conn, $data['po']);
            $update_fields[] = "po = '$po'";
        }
        if (isset($data['country'])) {
            $country = mysqli_real_escape_string($conn, $data['country']);
            $update_fields[] = "country = '$country'";
        }

        $update_fields[] = "updated_at = '$updated_at'";

        $update_query = "UPDATE ingSuppliers SET " . implode(', ', $update_fields) . " WHERE id = $supplier_id AND owner_id = '$userID'";
        
        if (!mysqli_query($conn, $update_query)) {
            error_log(mysqli_error($conn));
            echo json_encode(['status' => 'error', 'message' => 'Failed to update data for supplier: ' . mysqli_error($conn)]);
            exit();
        }
    } else {
        // Insert new supplier
        $created_at = date('Y-m-d H:i:s');
        $insert_query = "INSERT INTO ingSuppliers 
            (name, notes, telephone, url, email, add_costs, currency, address, po, updated_at, country, owner_id, created_at)
            VALUES 
            ('$name', 
            '" . mysqli_real_escape_string($conn, $data['notes'] ?? '') . "', 
            '" . mysqli_real_escape_string($conn, $data['telephone'] ?? '') . "', 
            '" . mysqli_real_escape_string($conn, $data['url'] ?? '') . "', 
            '" . mysqli_real_escape_string($conn, $data['email'] ?? '') . "', 
            " . ((float)($data['add_costs'] ?? 0)) . ", 
            '" . mysqli_real_escape_string($conn, $data['currency'] ?? '') . "', 
            '" . mysqli_real_escape_string($conn, $data['address'] ?? '') . "', 
            '" . mysqli_real_escape_string($conn, $data['po'] ?? '') . "', 
            '$updated_at', 
            '" . mysqli_real_escape_string($conn, $data['country'] ?? '') . "', 
            '$userID', 
            '$created_at')";
        
        if (!mysqli_query($conn, $insert_query)) {
            error_log(mysqli_error($conn));
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert data for supplier: ' . mysqli_error($conn)]);
            exit();
        }
    }

    // Respond with a success message
    echo json_encode(['status' => 'success', 'message' => 'Supplier data inserted/updated successfully']);
    exit();
}

if (isset($_GET['kind']) && $_GET['kind'] === 'material') {
    $name = mysqli_real_escape_string($conn, $data['name']);
    $updated_at = date('Y-m-d H:i:s');
    $supplier_name = mysqli_real_escape_string($conn, $data['supplier_name']);
    $mUnit = mysqli_real_escape_string($conn, $data['mUnit']);
    $price = (float)$data['price'];
    $purchased = mysqli_real_escape_string($conn, $data['purchased'] ?? date('Y-m-d H:i:s'));
    $sku = mysqli_real_escape_string($conn, $data['sku']);
    $cas = mysqli_real_escape_string($conn, $data['cas']);
    $manufacturer = mysqli_real_escape_string($conn, $data['manufacturer']);
    $availability = (int)$data['availability'];
    $batchNo = mysqli_real_escape_string($conn, $data['batchNo']);
    $stock = (int)$data['stock'];

    // Check if the material exists by name and owner_id
    $check_query = "SELECT id FROM ingredients WHERE name = '$name' AND owner_id = '$userID' LIMIT 1";
    $result = mysqli_query($conn, $check_query);
    if ($result && mysqli_num_rows($result) > 0) {
        // Update existing material
        $row = mysqli_fetch_assoc($result);
        $material_id = $row['id'];

        $update_fields = [];
       
        if (isset($data['cas'])) {
            $update_fields[] = "cas = '$cas'";
        }
        if (isset($data['aromaTrackID'])) {
            $update_fields[] = "aromaTrackID = '$aromaTrackID'";
        }
       

        $update_fields[] = "updated_at = '$updated_at'";

        $update_query = "UPDATE ingredients SET " . implode(', ', $update_fields) . " WHERE id = $material_id AND owner_id = '$userID'";
        
        if (!mysqli_query($conn, $update_query)) {
            error_log(mysqli_error($conn));
            echo json_encode(['status' => 'error', 'message' => 'Failed to update data for material: ' . mysqli_error($conn)]);
            exit();
        }
    } else {
        // Insert new material
        $created_at = date('Y-m-d H:i:s');
        $insert_query = "INSERT INTO ingredients 
            (name, cas, aromaTrackID, updated_at, owner_id, created_at)
            VALUES 
            ('$name', '$cas', '$aromaTrackID', '$updated_at', '$userID', '$created_at')";
        
        if (!mysqli_query($conn, $insert_query)) {
            error_log(mysqli_error($conn));
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert data for material: ' . mysqli_error($conn)]);
            exit();
        }

        // Get the last inserted ingredient ID
        $material_id = mysqli_insert_id($conn);
    }

    // Insert or update suppliers table
    $ing_supplier_check_query = "SELECT id, url FROM ingSuppliers WHERE name = '$supplier_name' AND owner_id = '$userID' LIMIT 1";
    $ing_supplier_result = mysqli_query($conn, $ing_supplier_check_query);
    if ($ing_supplier_result && mysqli_num_rows($ing_supplier_result) > 0) {
        $ing_supplier_row = mysqli_fetch_assoc($ing_supplier_result);
        $ing_supplier_id = $ing_supplier_row['id'];
        $supplier_link = $ing_supplier_row['url'];

        $supplier_check_query = "SELECT id FROM suppliers WHERE ingID = $material_id AND owner_id = '$userID' LIMIT 1";
        $supplier_result = mysqli_query($conn, $supplier_check_query);
        if ($supplier_result && mysqli_num_rows($supplier_result) > 0) {
            // Update existing supplier
            $supplier_row = mysqli_fetch_assoc($supplier_result);
            $supplier_id = $supplier_row['id'];
            $supplier_update_query = "UPDATE suppliers SET price = $price, supplierLink = '$supplier_link', updated_at = '$updated_at' WHERE id = $supplier_id AND owner_id = '$userID'";
            if (!mysqli_query($conn, $supplier_update_query)) {
                error_log(mysqli_error($conn));
                echo json_encode(['status' => 'error', 'message' => 'Failed to update data for supplier: ' . mysqli_error($conn)]);
                exit();
            }
        } else {
            // Insert new supplier
            //if (!isset($ing_supplier_id)) {
                $created_at = date('Y-m-d H:i:s');
                $insert_supplier_query = "INSERT INTO ingSuppliers (name, owner_id, updated_at) VALUES ('$supplier_name', '$userID', '$updated_at')";
                if (!mysqli_query($conn, $insert_supplier_query)) {
                    error_log(mysqli_error($conn));
                    echo json_encode(['status' => 'error', 'message' => 'Failed to insert data for ingSupplier: ' . mysqli_error($conn)]);
                    exit();
                }
                $ing_supplier_id = mysqli_insert_id($conn);
            //}
            $supplier_insert_query = "INSERT INTO suppliers (ingID, ingSupplierID, price, stock, supplierLink, owner_id, updated_at) VALUES ($material_id, $ing_supplier_id, $price, $stock, '$supplier_link', '$userID', '$updated_at')";
            error_log($supplier_insert_query);
            if (!mysqli_query($conn, $supplier_insert_query)) {
                error_log(mysqli_error($conn));
                echo json_encode(['status' => 'error', 'message' => 'Failed to insert data for supplier: ' . mysqli_error($conn)]);
                exit();
            }
        }
    }

    // Respond with a success message
    echo json_encode(['status' => 'success', 'message' => 'Material data inserted/updated successfully']);
    exit();
}

// Respond with a success message
echo json_encode(['status' => 'success', 'message' => 'Data inserted/updated successfully']);
?>

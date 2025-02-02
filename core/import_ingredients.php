<?php

if (!defined('pvault_panel')) {
    die('Not Found');
}

if (!file_exists($tmp_path)) {
    mkdir($tmp_path, 0777, true);
}

if (!is_writable($tmp_path)) {
    respondWithError("Upload directory not writable. Make sure you have write permissions.");
}

$target_path = $tmp_path . basename($_FILES['backupFile']['name']);

// Validate JSON decoding
if (json_last_error() !== JSON_ERROR_NONE) {
    respondWithError("Invalid JSON format: " . json_last_error_msg());
}


if (!move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    respondWithError("There was an error processing json file $target_path, please try again!");
}

$data = json_decode(file_get_contents($target_path), true);

if (empty($data) || !isset($data['ingredients'])) {
    respondWithError("JSON File seems invalid or empty. Please make sure you are importing the right file");
}

$result = [];
$warn = '';

processIngredients($data['ingredients']);
processSuppliers($data['ingSuppliers']);
processIngredientCompounds($data['compositions']);
processIngredientDocuments($data['documents']);

$result['success'] = "Import complete";
if ($warn) {
    $result['warning'] = $warn;
}

unlink($target_path);
echo json_encode($result);

function respondWithError($message) {
    global $result;
    $result['error'] = $message;
    echo json_encode($result);
    exit;
}

function processIngredients($ingredients) {
    global $conn, $userID, $warn;

    foreach ($ingredients as $ingredient) {
        $ingredient = array_map(function($value) use ($conn) {
            return mysqli_real_escape_string($conn, $value);
        }, $ingredient);

        $query_check = "SELECT COUNT(*) FROM `ingredients` WHERE `name` = '{$ingredient['name']}' AND `owner_id` = '$userID'";
        $exists = mysqli_fetch_row(mysqli_query($conn, $query_check))[0];

        if ($exists == 0) {
            $query = "INSERT INTO ingredients (name, INCI, cas, FEMA, type, strength, category, purity, einecs, reach, tenacity, chemical_name, formula, flash_point, notes, flavor_use, soluble, logp, cat1, cat2, cat3, cat4, cat5A, cat5B, cat5C, cat6, cat7A, cat7B, cat8, cat9, cat10A, cat10B, cat11A, cat11B, cat12, profile, physical_state, allergen, odor, impact_top, impact_heart, impact_base, usage_type, noUsageLimit, byPassIFRA, isPrivate, molecularWeight, owner_id) 
                      VALUES ('{$ingredient['name']}', '{$ingredient['INCI']}', '{$ingredient['cas']}', '{$ingredient['FEMA']}', '{$ingredient['type']}', '{$ingredient['strength']}', '{$ingredient['category']}', '{$ingredient['purity']}', '{$ingredient['einecs']}', '{$ingredient['reach']}', '{$ingredient['tenacity']}', '{$ingredient['chemical_name']}', '{$ingredient['formula']}', '{$ingredient['flash_point']}', '{$ingredient['notes']}', '{$ingredient['flavor_use']}', '{$ingredient['soluble']}', '{$ingredient['logp']}', '{$ingredient['cat1']}', '{$ingredient['cat2']}', '{$ingredient['cat3']}', '{$ingredient['cat4']}', '{$ingredient['cat5A']}', '{$ingredient['cat5B']}', '{$ingredient['cat5C']}', '{$ingredient['cat6']}', '{$ingredient['cat7A']}', '{$ingredient['cat7B']}', '{$ingredient['cat8']}', '{$ingredient['cat9']}', '{$ingredient['cat10A']}', '{$ingredient['cat10B']}', '{$ingredient['cat11A']}', '{$ingredient['cat11B']}', '{$ingredient['cat12']}', '{$ingredient['profile']}', '{$ingredient['physical_state']}', '{$ingredient['allergen']}', '{$ingredient['odor']}', '{$ingredient['impact_top']}', '{$ingredient['impact_heart']}', '{$ingredient['impact_base']}', '{$ingredient['usage_type']}', '{$ingredient['noUsageLimit']}', '{$ingredient['byPassIFRA']}', '{$ingredient['isPrivate']}', '{$ingredient['molecularWeight']}', '$userID')";
            
            if (!mysqli_query($conn, $query)) {
                respondWithError('Error executing query: ' . mysqli_error($conn));
            }

            $new_ingID = mysqli_insert_id($conn);
            updateSuppliers($ingredient['id'], $new_ingID);
        }
    }
}

function processSuppliers($suppliers) {
    global $conn, $userID;

    foreach ($suppliers as $supplier) {
        $supplier = array_map(function($value) use ($conn) {
            return mysqli_real_escape_string($conn, $value);
        }, $supplier);

        $query_check = "SELECT COUNT(*) FROM `ingSuppliers` WHERE `name` = '{$supplier['name']}' AND `owner_id` = '$userID'";
        $exists = mysqli_fetch_row(mysqli_query($conn, $query_check))[0];

        if ($exists == 0) {
            $query = "INSERT INTO `ingSuppliers` (name, address, po, country, telephone, url, email, notes, owner_id) 
                      VALUES ('{$supplier['name']}', '{$supplier['address']}', '{$supplier['po']}', '{$supplier['country']}', '{$supplier['telephone']}', '{$supplier['url']}', '{$supplier['email']}', '{$supplier['notes']}', '$userID')";
            
            if (!mysqli_query($conn, $query)) {
                respondWithError('Error executing query: ' . mysqli_error($conn));
            }
        }
    }
}

function updateSuppliers($old_ingID, $new_ingID) {
    global $conn, $userID, $warn, $data;

    foreach ($data['suppliers'] as $sup) {
        if ($sup['ingID'] == $old_ingID) {
            $sup = array_map(function($value) use ($conn) {
                return mysqli_real_escape_string($conn, $value);
            }, $sup);

            if (!is_numeric($sup['price']) || empty($sup['price']) || $sup['price'] == 0) {
                $warn .= "Invalid price for supplier ID {$sup['ingSupplierID']} - Ignoring<br/>";
                continue;
            }

            $query_check = "SELECT COUNT(*) FROM `suppliers` WHERE `ingSupplierID` = '{$sup['ingSupplierID']}' AND ingID = '$new_ingID' AND `owner_id` = '$userID'";
            $exists = mysqli_fetch_row(mysqli_query($conn, $query_check))[0];
            if (!empty($sup['purchased']) && strtotime($sup['purchased']) === false) {
                $sup['purchased'] = date('Y-m-d H:i:s');
            }
            if ($exists == 0) {
                $query = "INSERT INTO `suppliers` (ingSupplierID, ingID, supplierLink, price, size, manufacturer, preferred, batch, purchased, mUnit, stock, status, supplier_sku, internal_sku, storage_location, created_at, owner_id) 
                          VALUES ('{$sup['ingSupplierID']}', '$new_ingID', '{$sup['supplierLink']}', '{$sup['price']}', '{$sup['size']}', '{$sup['manufacturer']}', '{$sup['preferred']}', '{$sup['batch']}', '{$sup['purchased']}', '{$sup['mUnit']}', '{$sup['stock']}', '{$sup['status']}', '{$sup['supplier_sku']}', '{$sup['internal_sku']}', '{$sup['storage_location']}', CURRENT_TIMESTAMP(), '$userID')";
                
                if (!mysqli_query($conn, $query)) {
                    respondWithError('Error executing query: ' . mysqli_error($conn));
                }
            }
        }
    }
}

function processIngredientCompounds($compounds) {
    global $conn, $userID;

    foreach ($compounds as $cmp) {
        $cmp = array_map(function($value) use ($conn) {
            return mysqli_real_escape_string($conn, $value);
        }, $cmp);

        $query_check = "SELECT COUNT(*) FROM `ingredient_compounds` WHERE `name` = '{$cmp['name']}' AND `owner_id` = '$userID'";
        $exists = mysqli_fetch_row(mysqli_query($conn, $query_check))[0];

        if ($exists == 0) {
            $query = "INSERT INTO `ingredient_compounds` (ing, name, cas, ec, min_percentage, max_percentage, GHS, toDeclare, created_at, owner_id) 
                      VALUES ('{$cmp['ing']}', '{$cmp['name']}', '{$cmp['cas']}', '{$cmp['ec']}', '{$cmp['min_percentage']}', '{$cmp['max_percentage']}', '{$cmp['GHS']}', '{$cmp['toDeclare']}', CURRENT_TIMESTAMP(), '$userID')";
            
            if (!mysqli_query($conn, $query)) {
                respondWithError('Error executing query: ' . mysqli_error($conn));
            }
        }
    }
}

function processIngredientDocuments($documents) {
    global $conn, $userID;

    foreach ($documents as $docs) {
        $docs = array_map(function($value) use ($conn) {
            return mysqli_real_escape_string($conn, $value);
        }, $docs);

        $query_check = "SELECT COUNT(*) FROM `documents` WHERE `name` = '{$docs['name']}' AND `owner_id` = '$userID' AND `type` = '{$docs['type']}'";
        $exists = mysqli_fetch_row(mysqli_query($conn, $query_check))[0];

        if ($exists == 0) {
            $query = "INSERT INTO `documents` (ownerID, type, name, notes, docData, isBatch, isSDS, created_at, updated_at, owner_id) 
                      VALUES ('{$docs['ownerID']}', '{$docs['type']}', '{$docs['name']}', '{$docs['notes']}', '{$docs['docData']}', '{$docs['isBatch']}', '{$docs['isSDS']}', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), '$userID')";
            
            if (!mysqli_query($conn, $query)) {
                respondWithError('Error executing query: ' . mysqli_error($conn));
            }
        }
    }
}

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

$random_string = bin2hex(random_bytes(8));
$target_path = $tmp_path.'/'.$random_string.'_'. basename($_FILES['backupFile']['name']);
error_log("PV: Target path: $target_path");

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
            $ingredient['cat1'] = empty($ingredient['cat1']) ? 100 : $ingredient['cat1'];
            $ingredient['cat2'] = empty($ingredient['cat2']) ? 100 : $ingredient['cat2'];
            $ingredient['cat3'] = empty($ingredient['cat3']) ? 100 : $ingredient['cat3'];
            $ingredient['cat4'] = empty($ingredient['cat4']) ? 100 : $ingredient['cat4'];
            $ingredient['cat5A'] = empty($ingredient['cat5A']) ? 100 : $ingredient['cat5A'];
            $ingredient['cat5B'] = empty($ingredient['cat5B']) ? 100 : $ingredient['cat5B'];
            $ingredient['cat5C'] = empty($ingredient['cat5C']) ? 100 : $ingredient['cat5C'];
            $ingredient['cat6'] = empty($ingredient['cat6']) ? 100 : $ingredient['cat6'];
            $ingredient['cat7A'] = empty($ingredient['cat7A']) ? 100 : $ingredient['cat7A'];
            $ingredient['cat7B'] = empty($ingredient['cat7B']) ? 100 : $ingredient['cat7B'];
            $ingredient['cat8'] = empty($ingredient['cat8']) ? 100 : $ingredient['cat8'];
            $ingredient['cat9'] = empty($ingredient['cat9']) ? 100 : $ingredient['cat9'];
            $ingredient['cat10A'] = empty($ingredient['cat10A']) ? 100 : $ingredient['cat10A'];
            $ingredient['cat10B'] = empty($ingredient['cat10B']) ? 100 : $ingredient['cat10B'];
            $ingredient['cat11A'] = empty($ingredient['cat11A']) ? 100 : $ingredient['cat11A'];
            $ingredient['cat11B'] = empty($ingredient['cat11B']) ? 100 : $ingredient['cat11B'];
            $ingredient['cat12'] = empty($ingredient['cat12']) ? 100 : $ingredient['cat12'];

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

function processSuppliers($ingSuppliers) {
    global $conn, $userID;
    $supplierIDMap = [];

    if (empty($ingSuppliers)) {
        error_log("PV error: No ingSuppliers data provided.");
        return $supplierIDMap;
    }

    foreach ($ingSuppliers as $supplier) {
        if (!isset($supplier['name']) || empty($supplier['name'])) {
            error_log("PV error: Missing supplier name, skipping.");
            continue;
        }

        $supplier = array_map(function($value) use ($conn) {
            return mysqli_real_escape_string($conn, $value);
        }, $supplier);

        $query_check = "SELECT id FROM `ingSuppliers` WHERE `name` = '{$supplier['name']}' AND `owner_id` = '$userID'";
        $result = mysqli_query($conn, $query_check);
        $existingSupplier = mysqli_fetch_assoc($result);

        if ($existingSupplier) {
            $newID = $existingSupplier['id'];
        } else {
            $query = "INSERT INTO `ingSuppliers` (name, address, po, country, telephone, url, email, notes, owner_id) 
                      VALUES ('{$supplier['name']}', '{$supplier['address']}', '{$supplier['po']}', '{$supplier['country']}', 
                      '{$supplier['telephone']}', '{$supplier['url']}', '{$supplier['email']}', '{$supplier['notes']}', '$userID')";

            if (mysqli_query($conn, $query)) {
                $newID = mysqli_insert_id($conn);
            } else {
                error_log("PV error: Error executing ingSuppliers insert: " . mysqli_error($conn));
                continue;
            }
        }

        $supplierIDMap[$supplier['id']] = $newID;
    }

    if (empty($supplierIDMap)) {
        error_log("PV error: Supplier ID mapping is empty after processing.");
    }

    return $supplierIDMap;
}

function updateSuppliers($old_ingID, $new_ingID) {
    global $conn, $userID, $warn, $data;

    if (empty($data['suppliers'])) {
        error_log("PV error: No suppliers data found.");
        return;
    }

    $supplierIDMap = processSuppliers($data['ingSuppliers']);

    foreach ($data['suppliers'] as $sup) {
        if ($sup['ingID'] == $old_ingID) {
            $sup = array_map(function($value) use ($conn) {
                return mysqli_real_escape_string($conn, $value);
            }, $sup);

            if (!isset($supplierIDMap[$sup['ingSupplierID']])) {
                error_log("PV error: No mapped supplier ID for old ID {$sup['ingSupplierID']}");
                continue;
            }

            $newSupplierID = $supplierIDMap[$sup['ingSupplierID']];

            if (!is_numeric($sup['price']) || empty($sup['price']) || $sup['price'] == 0) {
                $warn .= "Invalid price for supplier ID {$sup['ingSupplierID']} - Ignoring<br/>";
                continue;
            }

            if (!empty($sup['purchased']) && strtotime($sup['purchased']) === false) {
                $sup['purchased'] = date('Y-m-d H:i:s');
            }

            $query_check = "SELECT COUNT(*) FROM `suppliers` WHERE `ingSupplierID` = '$newSupplierID' AND ingID = '$new_ingID' AND `owner_id` = '$userID'";
            $exists = mysqli_fetch_row(mysqli_query($conn, $query_check))[0];

            if ($exists == 0) {
                $query = "INSERT INTO `suppliers` (ingSupplierID, ingID, supplierLink, price, size, manufacturer, preferred, batch, purchased, mUnit, stock, status, supplier_sku, internal_sku, storage_location, created_at, updated_at, owner_id) 
                          VALUES ('$newSupplierID', '$new_ingID', '{$sup['supplierLink']}', '{$sup['price']}', '{$sup['size']}', '{$sup['manufacturer']}', 
                          '{$sup['preferred']}', '{$sup['batch']}', '{$sup['purchased']}', '{$sup['mUnit']}', '{$sup['stock']}', '{$sup['status']}', 
                          '{$sup['supplier_sku']}', '{$sup['internal_sku']}', '{$sup['storage_location']}', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), '$userID')";

                if (!mysqli_query($conn, $query)) {
                    error_log("PV error: Error executing suppliers insert: " . mysqli_error($conn));
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

        $query_check = "SELECT COUNT(*) FROM `ingredient_compounds` WHERE `name` = '{$cmp['name']}' AND `ing` = '{$cmp['ing']}' AND `owner_id` = '$userID'";
        $exists = mysqli_fetch_row(mysqli_query($conn, $query_check))[0];
      //  error_log("PV: Checking for existing compound: $query_check, exists: $exists");
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
        try {
            $docs = array_map(function($value) use ($conn) {
                return mysqli_real_escape_string($conn, $value);
            }, $docs);

            $query_check = "SELECT COUNT(*) FROM `documents` WHERE `name` = '{$docs['name']}' AND `owner_id` = '$userID' AND `type` = '{$docs['type']}'";
            $exists = mysqli_fetch_row(mysqli_query($conn, $query_check))[0];

            if ($exists == 0) {
                $query = "INSERT INTO `documents` (ownerID, type, name, notes, docData, isBatch, isSDS, created_at, updated_at, owner_id) 
                          VALUES ('{$docs['ownerID']}', '{$docs['type']}', '{$docs['name']}', '{$docs['notes']}', '{$docs['docData']}', '{$docs['isBatch']}', '{$docs['isSDS']}', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), '$userID')";
                
                if (!mysqli_query($conn, $query)) {
                    throw new Exception('Error executing query: ' . mysqli_error($conn));
                }
                error_log("Document '{$docs['name']}' inserted successfully.");
            } else {
                error_log("Document '{$docs['name']}' already exists.");
            }
        } catch (Exception $e) {
            error_log("Exception caught while processing documents: " . $e->getMessage());
            respondWithError($e->getMessage());
        }
    }
}

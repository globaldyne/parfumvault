<?php

if (!defined('pvault_panel')) {
    die('Not Found');
}

if (!file_exists($tmp_path) && !mkdir($tmp_path, 0777, true)) {
    respondWithError("Failed to create upload directory.");
}

if (!is_writable($tmp_path)) {
    respondWithError("Upload directory not writable. Check permissions.");
}

// Validate uploaded file
if (!isset($_FILES['backupFile']) || $_FILES['backupFile']['error'] !== UPLOAD_ERR_OK) {
    respondWithError("File upload error: " . $_FILES['backupFile']['error']);
}

// Generate a unique file name
$random_string = bin2hex(random_bytes(8));
$original_filename = basename($_FILES['backupFile']['name']);
$target_path = "$tmp_path/{$random_string}_{$original_filename}";

//error_log("PV error: Target path: $target_path");

// Move the uploaded file
if (!move_uploaded_file($_FILES['backupFile']['tmp_name'], $target_path)) {
    respondWithError("Error processing JSON file. Please try again.");
}

// Read and decode JSON
$file_contents = file_get_contents($target_path);
if ($file_contents === false) {
    respondWithError("Failed to read the uploaded JSON file.");
}

$data = json_decode($file_contents, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    respondWithError("Invalid JSON format: " . json_last_error_msg());
}

if (empty($data['formulasMetaData'])) {
    respondWithError("Invalid JSON file. Ensure you are importing the correct format.");
}

require_once(__ROOT__ . '/func/genFID.php');

$fid_map = [];
foreach ($data['formulasMetaData'] as $meta) {
    $name = mysqli_real_escape_string($conn, $meta['name']);
    $product_name = mysqli_real_escape_string($conn, $meta['product_name']);
    $notes = mysqli_real_escape_string($conn, $meta['notes']);
    $scheduledOn = !empty($meta['scheduledOn']) && strtotime($meta['scheduledOn']) ? $meta['scheduledOn'] : date('Y-m-d');

    // Ensure unique formula name
    $original_name = $name;
    $counter = 1;
    $stmt = $conn->prepare("SELECT name FROM formulasMetaData WHERE name = ? AND owner_id = ?");
    
    while (true) {
        $stmt->bind_param("ss", $name, $userID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) break;

        $name = "$original_name ($counter)";
        $counter++;
    }
    $stmt->close();

    // Generate new FID
    $newfid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');
    $fid_map[$meta['fid']] = $newfid;

    // Insert formula metadata
    $stmt = $conn->prepare("INSERT INTO formulasMetaData (name, product_name, fid, profile, gender, notes, isProtected, defView, catClass, revision, finalType, isMade, madeOn, scheduledOn, customer_id, status, toDo, rating, owner_id) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        respondWithError("Database error: " . $conn->error);
    }

    $stmt->bind_param("sssssssssssssssssss", $name, $product_name, $newfid, $meta['profile'], $meta['gender'], $notes, $meta['isProtected'], $meta['defView'], $meta['catClass'], $meta['revision'], $meta['finalType'], $meta['isMade'], $meta['madeOn'], $scheduledOn, $meta['customer_id'], $meta['status'], $meta['toDo'], $meta['rating'], $userID);
    
    if (!$stmt->execute()) {
        error_log("PV error: Query execution failed: " . $stmt->error);
        respondWithError("Error importing JSON file: " . $stmt->error);
    }
    $stmt->close();
}

// Insert formulas
foreach ($data['formulas'] as $formula) {
    $name = mysqli_real_escape_string($conn, $formula['name']);
    $notes = mysqli_real_escape_string($conn, $formula['notes']);
    $ingredient = mysqli_real_escape_string($conn, $formula['ingredient']);
    $exclude_from_summary = isset($formula['exclude_from_summary']) ? (int)$formula['exclude_from_summary'] : 0;
    $exclude_from_calculation = isset($formula['exclude_from_calculation']) ? (int)$formula['exclude_from_calculation'] : 0;
    $ingredient_id = 0;
    $newfid = $fid_map[$formula['fid']] ?? null;

    if (!$newfid) {
        error_log("PV error: Missing FID mapping for formula: " . print_r($formula, true));
        continue;
    }

    $stmt = $conn->prepare("INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, exclude_from_summary, exclude_from_calculation, notes, owner_id) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        respondWithError("Database error: " . $conn->error);
    }

    $stmt->bind_param("sssiiddisss", $newfid, $name, $ingredient, $ingredient_id, $formula['concentration'], $formula['dilutant'], $formula['quantity'], $exclude_from_summary, $exclude_from_calculation, $notes, $userID);
    
    if (!$stmt->execute()) {
        error_log("PV error: Query execution failed: " . $stmt->error);
        respondWithError("Error importing JSON file: " . $stmt->error);
    }
    $stmt->close();
}

// Cleanup
$result['success'] = "Import complete";
unlink($target_path);
echo json_encode($result);
exit;

/**
 * Handles error responses, logs errors, and terminates script execution.
 * 
 * @param string $message The error message to return.
 */
function respondWithError($message) {
    global $result;
    $result['error'] = "PV error: " . $message;
    error_log($result['error']);
    echo json_encode($result);
    exit;
}

<?php
if (!defined('pvault_panel')){ die('Not Found');}
header('Content-Type: application/json');
global $conn,$userID;

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    return;
}

if (empty($data['formulas']) || !is_array($data['formulas'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid formulas array']);
    return;
}

// Start transaction so partial failures rollback
if (!mysqli_begin_transaction($conn)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to start database transaction']);
    return;
}

$insertedFormulas = 0;
$insertedIngredients = 0;

$meta_query = "INSERT INTO formulasMetaData (fid, name, product_name, notes, finalType, status, isProtected, rating, profile, src, customer_id, revision, madeOn, owner_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
$meta_stmt = mysqli_prepare($conn, $meta_query);
if (!$meta_stmt) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare metadata statement','details' => mysqli_error($conn)]);
    return;
}

$ing_query = "INSERT INTO formulas (fid, name, ingredient, concentration, dilutant, quantity, notes) VALUES (?,?,?,?,?,?,?)";
$ing_stmt = mysqli_prepare($conn, $ing_query);
if (!$ing_stmt) {
    mysqli_stmt_close($meta_stmt);
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare ingredient statement','details' => mysqli_error($conn)]);
    return;
}

// Prepare statements for existence check, update metadata and delete ingredients
$check_query = "SELECT COUNT(*) FROM formulasMetaData WHERE fid = ?";
$check_stmt = mysqli_prepare($conn, $check_query);
if (!$check_stmt) {
    mysqli_stmt_close($meta_stmt);
    mysqli_stmt_close($ing_stmt);
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare check statement','details' => mysqli_error($conn)]);
    return;
}

$meta_update_query = "UPDATE formulasMetaData SET name=?, product_name=?, notes=?, finalType=?, status=?, isProtected=?, rating=?, profile=?, src=?, customer_id=?, revision=?, madeOn=?, owner_id=? WHERE fid=?";
$meta_update_stmt = mysqli_prepare($conn, $meta_update_query);
if (!$meta_update_stmt) {
    mysqli_stmt_close($meta_stmt);
    mysqli_stmt_close($ing_stmt);
    mysqli_stmt_close($check_stmt);
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare metadata update statement','details' => mysqli_error($conn)]);
    return;
}

$ing_delete_query = "DELETE FROM formulas WHERE fid = ?";
$ing_delete_stmt = mysqli_prepare($conn, $ing_delete_query);
if (!$ing_delete_stmt) {
    mysqli_stmt_close($meta_stmt);
    mysqli_stmt_close($ing_stmt);
    mysqli_stmt_close($check_stmt);
    mysqli_stmt_close($meta_update_stmt);
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare ingredient delete statement','details' => mysqli_error($conn)]);
    return;
}

foreach ($data['formulas'] as $row) {
    // Validate required fields
    if (empty($row['fid']) || empty($row['name'])) {
        mysqli_stmt_close($meta_stmt);
        mysqli_stmt_close($ing_stmt);
        mysqli_stmt_close($check_stmt);
        mysqli_stmt_close($meta_update_stmt);
        mysqli_stmt_close($ing_delete_stmt);
        mysqli_rollback($conn);
        http_response_code(400);
        echo json_encode(['error' => 'Each formula must include fid and name']);
        return;
    }

    $fid = (string)$row['fid'];
    $name = (string)$row['name'];
    $product_name = isset($row['product_name']) ? (string)$row['product_name'] : '-';
    $notes = isset($row['notes']) ? (string)$row['notes'] : '-';
    $concentration = isset($row['concentration']) ? (int)$row['concentration'] : 100;
    $status = isset($row['status']) ? (int)$row['status'] : 0;
    $isProtected = isset($row['isProtected']) ? (int)$row['isProtected'] : 0;
    $rating = isset($row['rating']) ? (int)$row['rating'] : 0;
    $profile = isset($row['profile']) ? (string)$row['profile'] : 'Default';
    $src = isset($row['src']) ? (int)$row['src'] : 0;
    $customer_id = isset($row['customer_id']) ? (int)$row['customer_id'] : 0;
    $revision = isset($row['revision']) ? (int)$row['revision'] : 0;
    $madeOn = isset($row['madeOn']) ? (string)$row['madeOn'] : date('Y-m-d H:i:s');

    $owner_id = isset($row['owner_id']) ? (string)$row['owner_id'] : (string)$userID; 

    // Check if fid already exists
    if (!mysqli_stmt_bind_param($check_stmt, 's', $fid)) {
        mysqli_stmt_close($meta_stmt);
        mysqli_stmt_close($ing_stmt);
        mysqli_stmt_close($check_stmt);
        mysqli_stmt_close($meta_update_stmt);
        mysqli_stmt_close($ing_delete_stmt);
        mysqli_rollback($conn);
        http_response_code(500);
        echo json_encode(['error' => 'Failed to bind check parameters','details' => mysqli_stmt_error($check_stmt)]);
        return;
    }
    if (!mysqli_stmt_execute($check_stmt)) {
        mysqli_stmt_close($meta_stmt);
        mysqli_stmt_close($ing_stmt);
        mysqli_stmt_close($check_stmt);
        mysqli_stmt_close($meta_update_stmt);
        mysqli_stmt_close($ing_delete_stmt);
        mysqli_rollback($conn);
        http_response_code(500);
        echo json_encode(['error' => 'Failed to execute check statement','details' => mysqli_stmt_error($check_stmt)]);
        return;
    }
    mysqli_stmt_bind_result($check_stmt, $existsCount);
    mysqli_stmt_fetch($check_stmt);
    mysqli_stmt_free_result($check_stmt);

    if ($existsCount > 0) {
        // Update existing metadata
        $update_types = 'sssiiiisiiisss';
        if (!mysqli_stmt_bind_param($meta_update_stmt, $update_types, $name, $product_name, $notes, $concentration, $status, $isProtected, $rating, $profile, $src, $customer_id, $revision, $madeOn, $owner_id, $fid)) {
            mysqli_stmt_close($meta_stmt);
            mysqli_stmt_close($ing_stmt);
            mysqli_stmt_close($check_stmt);
            mysqli_stmt_close($meta_update_stmt);
            mysqli_stmt_close($ing_delete_stmt);
            mysqli_rollback($conn);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to bind update parameters','details' => mysqli_stmt_error($meta_update_stmt)]);
            return;
        }
        if (!mysqli_stmt_execute($meta_update_stmt)) {
            mysqli_stmt_close($meta_stmt);
            mysqli_stmt_close($ing_stmt);
            mysqli_stmt_close($check_stmt);
            mysqli_stmt_close($meta_update_stmt);
            mysqli_stmt_close($ing_delete_stmt);
            mysqli_rollback($conn);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update metadata','details' => mysqli_stmt_error($meta_update_stmt)]);
            return;
        }

        // Remove old ingredients for this fid before inserting new ones
        if (!mysqli_stmt_bind_param($ing_delete_stmt, 's', $fid)) {
            mysqli_stmt_close($meta_stmt);
            mysqli_stmt_close($ing_stmt);
            mysqli_stmt_close($check_stmt);
            mysqli_stmt_close($meta_update_stmt);
            mysqli_stmt_close($ing_delete_stmt);
            mysqli_rollback($conn);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to bind ingredient delete parameters','details' => mysqli_stmt_error($ing_delete_stmt)]);
            return;
        }
        if (!mysqli_stmt_execute($ing_delete_stmt)) {
            mysqli_stmt_close($meta_stmt);
            mysqli_stmt_close($ing_stmt);
            mysqli_stmt_close($check_stmt);
            mysqli_stmt_close($meta_update_stmt);
            mysqli_stmt_close($ing_delete_stmt);
            mysqli_rollback($conn);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete existing ingredients','details' => mysqli_stmt_error($ing_delete_stmt)]);
            return;
        }

    } else {
        // Insert new metadata
        $types = 'ssssiiiisiiiss';
        if (!mysqli_stmt_bind_param($meta_stmt, $types, $fid, $name, $product_name, $notes, $concentration, $status, $isProtected, $rating, $profile, $src, $customer_id, $revision, $madeOn, $owner_id)) {
            mysqli_stmt_close($meta_stmt);
            mysqli_stmt_close($ing_stmt);
            mysqli_stmt_close($check_stmt);
            mysqli_stmt_close($meta_update_stmt);
            mysqli_stmt_close($ing_delete_stmt);
            mysqli_rollback($conn);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to bind metadata parameters','details' => mysqli_stmt_error($meta_stmt)]);
            return;
        }
        if (!mysqli_stmt_execute($meta_stmt)) {
            mysqli_stmt_close($meta_stmt);
            mysqli_stmt_close($ing_stmt);
            mysqli_stmt_close($check_stmt);
            mysqli_stmt_close($meta_update_stmt);
            mysqli_stmt_close($ing_delete_stmt);
            mysqli_rollback($conn);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to insert formula metadata','details' => mysqli_stmt_error($meta_stmt)]);
            return;
        }
        $insertedFormulas++;
    }

    // If we updated metadata we still want to count it as an inserted/updated formula
    if ($existsCount > 0) {
        $insertedFormulas++;
    }

    // Ingredients
    if (!empty($row['ingredients']) && is_array($row['ingredients'])) {
        foreach ($row['ingredients'] as $ingredient) {
            if (empty($ingredient['ingredient'])) {
                // skip invalid ingredient entries
                continue;
            }
            $ingredient_fid = isset($ingredient['fid']) ? (string)$ingredient['fid'] : $fid;
            $ingredient_name = (string)$ingredient['ingredient'];
            $ingredient_concentration = isset($ingredient['concentration']) ? (float)$ingredient['concentration'] : 100.0;
            $ingredient_quantity = isset($ingredient['quantity']) ? (float)$ingredient['quantity'] : 0.0;
            $ingredient_dilutant = isset($ingredient['dilutant']) ? (string)$ingredient['dilutant'] : '-';
            $ing_notes = isset($ingredient['notes']) ? (string)$ingredient['notes'] : '-';

            $ing_types = 'sssdsds';
            if (!mysqli_stmt_bind_param($ing_stmt, $ing_types, $ingredient_fid, $name, $ingredient_name, $ingredient_concentration, $ingredient_dilutant, $ingredient_quantity, $ing_notes)) {
                mysqli_stmt_close($meta_stmt);
                mysqli_stmt_close($ing_stmt);
                mysqli_rollback($conn);
                http_response_code(500);
                echo json_encode(['error' => 'Failed to bind ingredient parameters','details' => mysqli_stmt_error($ing_stmt)]);
                return;
            }
            if (!mysqli_stmt_execute($ing_stmt)) {
                mysqli_stmt_close($meta_stmt);
                mysqli_stmt_close($ing_stmt);
                mysqli_rollback($conn);
                http_response_code(500);
                echo json_encode(['error' => 'Failed to insert ingredient','details' => mysqli_stmt_error($ing_stmt)]);
                return;
            }
            $insertedIngredients++;
        }
    }
}

// Commit transaction
if (!mysqli_commit($conn)) {
    mysqli_stmt_close($meta_stmt);
    mysqli_stmt_close($ing_stmt);
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to commit transaction']);
    return;
}

mysqli_stmt_close($meta_stmt);
mysqli_stmt_close($ing_stmt);

echo json_encode(['success' => 'Formulas uploaded successfully', 'formulas_inserted' => $insertedFormulas, 'ingredients_inserted' => $insertedIngredients]);

?>


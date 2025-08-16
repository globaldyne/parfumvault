<?php
if (!defined('pvault_panel')){ die('Not Found');}
header('Content-Type: application/json');
global $conn,$userID;

$json = file_get_contents('php://input');
// Log raw incoming JSON to project logs and to error log (truncated)
/*
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) { @mkdir($logDir, 0755, true); }
$logFile = $logDir . '/formulas_upload.log';
@file_put_contents($logFile, date('c') . ' ' . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . ' ' . $json . PHP_EOL, FILE_APPEND | LOCK_EX);
// Also write a truncated version to the PHP error log for quick debugging
error_log('formulas_upload payload (truncated): ' . substr($json, 0, 2000));
*/
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

$createdFormulas = 0;
$updatedFormulas = 0;
$insertedIngredients = 0;

$meta_query = "INSERT INTO formulasMetaData (fid, name, product_name, notes, finalType, status, isProtected, rating, profile, src, customer_id, revision, madeOn, owner_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
$meta_stmt = mysqli_prepare($conn, $meta_query);
if (!$meta_stmt) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare metadata statement','details' => mysqli_error($conn)]);
    return;
}

$ing_query = "INSERT INTO formulas (fid, name, ingredient, concentration, dilutant, quantity, notes, owner_id) VALUES (?,?,?,?,?,?,?,?)";
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

// Prepare selects to fetch existing metadata and ingredients for comparison
$get_meta_query = "SELECT name, product_name, notes, finalType, status, isProtected, rating, profile, src, customer_id, revision, madeOn, owner_id FROM formulasMetaData WHERE fid = ?";
$get_meta_stmt = mysqli_prepare($conn, $get_meta_query);
if (!$get_meta_stmt) {
    mysqli_stmt_close($meta_stmt);
    mysqli_stmt_close($ing_stmt);
    mysqli_stmt_close($check_stmt);
    mysqli_stmt_close($meta_update_stmt);
    mysqli_stmt_close($ing_delete_stmt);
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare get_meta statement','details' => mysqli_error($conn)]);
    return;
}

$get_ing_query = "SELECT ingredient, concentration, dilutant, quantity, notes FROM formulas WHERE fid = ? ORDER BY ingredient ASC";
$get_ing_stmt = mysqli_prepare($conn, $get_ing_query);
if (!$get_ing_stmt) {
    mysqli_stmt_close($meta_stmt);
    mysqli_stmt_close($ing_stmt);
    mysqli_stmt_close($check_stmt);
    mysqli_stmt_close($meta_update_stmt);
    mysqli_stmt_close($ing_delete_stmt);
    mysqli_stmt_close($get_meta_stmt);
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare get_ing statement','details' => mysqli_error($conn)]);
    return;
}

// Prepare ingredient update and single-delete statements
$ing_update_query = "UPDATE formulas SET concentration=?, dilutant=?, quantity=?, notes=?, name=? WHERE fid=? AND ingredient=?";
$ing_update_stmt = mysqli_prepare($conn, $ing_update_query);
if (!$ing_update_stmt) {
    mysqli_stmt_close($meta_stmt);
    mysqli_stmt_close($ing_stmt);
    mysqli_stmt_close($check_stmt);
    mysqli_stmt_close($meta_update_stmt);
    mysqli_stmt_close($ing_delete_stmt);
    mysqli_stmt_close($get_meta_stmt);
    mysqli_stmt_close($get_ing_stmt);
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare ingredient update statement','details' => mysqli_error($conn)]);
    return;
}

$ing_delete_single_query = "DELETE FROM formulas WHERE fid = ? AND ingredient = ?";
$ing_delete_single_stmt = mysqli_prepare($conn, $ing_delete_single_query);
if (!$ing_delete_single_stmt) {
    mysqli_stmt_close($meta_stmt);
    mysqli_stmt_close($ing_stmt);
    mysqli_stmt_close($check_stmt);
    mysqli_stmt_close($meta_update_stmt);
    mysqli_stmt_close($ing_delete_stmt);
    mysqli_stmt_close($get_meta_stmt);
    mysqli_stmt_close($get_ing_stmt);
    mysqli_stmt_close($ing_update_stmt);
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare ingredient single-delete statement','details' => mysqli_error($conn)]);
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
        mysqli_stmt_close($ing_update_stmt);
        mysqli_stmt_close($ing_delete_single_stmt);
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

    $handledIngredients = false;

    // Check if fid already exists
    if (!mysqli_stmt_bind_param($check_stmt, 's', $fid)) {
        mysqli_stmt_close($meta_stmt);
        mysqli_stmt_close($ing_stmt);
        mysqli_stmt_close($check_stmt);
        mysqli_stmt_close($meta_update_stmt);
        mysqli_stmt_close($ing_delete_stmt);
        mysqli_stmt_close($ing_update_stmt);
        mysqli_stmt_close($ing_delete_single_stmt);
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
        mysqli_stmt_close($ing_update_stmt);
        mysqli_stmt_close($ing_delete_single_stmt);
        mysqli_rollback($conn);
        http_response_code(500);
        echo json_encode(['error' => 'Failed to execute check statement','details' => mysqli_stmt_error($check_stmt)]);
        return;
    }
    mysqli_stmt_bind_result($check_stmt, $existsCount);
    mysqli_stmt_fetch($check_stmt);
    mysqli_stmt_free_result($check_stmt);

    if ($existsCount > 0) {
        // Mark as handled to avoid re-inserting ingredients when none changed
        $handledIngredients = true;

        // Fetch existing metadata
        if (!mysqli_stmt_bind_param($get_meta_stmt, 's', $fid) || !mysqli_stmt_execute($get_meta_stmt)) {
            mysqli_stmt_close($meta_stmt);
            mysqli_stmt_close($ing_stmt);
            mysqli_stmt_close($check_stmt);
            mysqli_stmt_close($meta_update_stmt);
            mysqli_stmt_close($ing_delete_stmt);
            mysqli_stmt_close($get_meta_stmt);
            mysqli_stmt_close($get_ing_stmt);
            mysqli_stmt_close($ing_update_stmt);
            mysqli_stmt_close($ing_delete_single_stmt);
            mysqli_rollback($conn);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch existing metadata','details' => mysqli_stmt_error($get_meta_stmt)]);
            return;
        }
        mysqli_stmt_bind_result($get_meta_stmt, $e_name, $e_product_name, $e_notes, $e_finalType, $e_status, $e_isProtected, $e_rating, $e_profile, $e_src, $e_customer_id, $e_revision, $e_madeOn, $e_owner_id);
        mysqli_stmt_fetch($get_meta_stmt);
        mysqli_stmt_free_result($get_meta_stmt);

        // Compare metadata fields
        $metaChanged = (
            $e_name !== $name ||
            $e_product_name !== $product_name ||
            $e_notes !== $notes ||
            ((int)$e_finalType) !== $concentration ||
            ((int)$e_status) !== $status ||
            ((int)$e_isProtected) !== $isProtected ||
            ((int)$e_rating) !== $rating ||
            $e_profile !== $profile ||
            ((int)$e_src) !== $src ||
            ((int)$e_customer_id) !== $customer_id ||
            ((int)$e_revision) !== $revision ||
            $e_madeOn !== $madeOn ||
            ((string)$e_owner_id) !== $owner_id
        );

        // Fetch existing ingredients
        if (!mysqli_stmt_bind_param($get_ing_stmt, 's', $fid) || !mysqli_stmt_execute($get_ing_stmt)) {
            mysqli_stmt_close($meta_stmt);
            mysqli_stmt_close($ing_stmt);
            mysqli_stmt_close($check_stmt);
            mysqli_stmt_close($meta_update_stmt);
            mysqli_stmt_close($ing_delete_stmt);
            mysqli_stmt_close($get_meta_stmt);
            mysqli_stmt_close($get_ing_stmt);
            mysqli_stmt_close($ing_update_stmt);
            mysqli_stmt_close($ing_delete_single_stmt);
            mysqli_rollback($conn);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch existing ingredients','details' => mysqli_stmt_error($get_ing_stmt)]);
            return;
        }
        mysqli_stmt_bind_result($get_ing_stmt, $e_ing_name, $e_ing_conc, $e_ing_dilutant, $e_ing_qty, $e_ing_notes);
        $existingIngredients = [];
        while (mysqli_stmt_fetch($get_ing_stmt)) {
            $existingIngredients[] = [
                'ingredient' => (string)$e_ing_name,
                'concentration' => (float)$e_ing_conc,
                'dilutant' => (string)$e_ing_dilutant,
                'quantity' => (float)$e_ing_qty,
                'notes' => (string)$e_ing_notes
            ];
        }
        mysqli_stmt_free_result($get_ing_stmt);

        // Normalize incoming ingredients
        $incomingIngredients = [];
        if (!empty($row['ingredients']) && is_array($row['ingredients'])) {
            foreach ($row['ingredients'] as $ingredient) {
                if (empty($ingredient['ingredient'])) continue;
                $incomingIngredients[] = [
                    'ingredient' => (string)$ingredient['ingredient'],
                    'concentration' => (float)($ingredient['concentration'] ?? 0.0),
                    'dilutant' => (string)($ingredient['dilutant'] ?? '-'),
                    'quantity' => (float)($ingredient['quantity'] ?? 0.0),
                    'notes' => (string)($ingredient['notes'] ?? '-')
                ];
            }
        }
        // Sort both arrays by ingredient name for consistent comparison
        usort($existingIngredients, function($a,$b){ return strcmp($a['ingredient'],$b['ingredient']); });
        usort($incomingIngredients, function($a,$b){ return strcmp($a['ingredient'],$b['ingredient']); });

        $ingredientsChanged = (json_encode($existingIngredients) !== json_encode($incomingIngredients));

        // If nothing changed, skip processing
        if (!$metaChanged && !$ingredientsChanged) {
            // no-op
            continue;
        }

        // If metadata changed, update
        if ($metaChanged) {
            $update_types = 'sssiiiisiiisss';
            if (!mysqli_stmt_bind_param($meta_update_stmt, $update_types, $name, $product_name, $notes, $concentration, $status, $isProtected, $rating, $profile, $src, $customer_id, $revision, $madeOn, $owner_id, $fid)) {
                mysqli_stmt_close($meta_stmt);
                mysqli_stmt_close($ing_stmt);
                mysqli_stmt_close($check_stmt);
                mysqli_stmt_close($meta_update_stmt);
                mysqli_stmt_close($ing_delete_stmt);
                mysqli_stmt_close($ing_update_stmt);
                mysqli_stmt_close($ing_delete_single_stmt);
                mysqli_rollback($conn);
                http_response_code(500);
                error_log('Failed to bind update parameters: ' . mysqli_stmt_error($meta_update_stmt));
                echo json_encode(['error' => 'Failed to bind update parameters, see logs for details']);
                return;
            }
            if (!mysqli_stmt_execute($meta_update_stmt)) {
                mysqli_stmt_close($meta_stmt);
                mysqli_stmt_close($ing_stmt);
                mysqli_stmt_close($check_stmt);
                mysqli_stmt_close($meta_update_stmt);
                mysqli_stmt_close($ing_delete_stmt);
                mysqli_stmt_close($ing_update_stmt);
                mysqli_stmt_close($ing_delete_single_stmt);
                mysqli_rollback($conn);
                http_response_code(500);
                error_log('Failed to update metadata: ' . mysqli_stmt_error($meta_update_stmt));
                echo json_encode(['error' => 'Failed to update metadata, see logs for details']);
                return;
            }
        }

        // If ingredients changed, handle per-ingredient updates/inserts/deletes
        if ($ingredientsChanged) {
            // Build maps for quick lookup
            $existingMap = [];
            foreach ($existingIngredients as $ei) {
                $existingMap[$ei['ingredient']] = $ei;
            }
            $incomingMap = [];
            foreach ($incomingIngredients as $ii) {
                $incomingMap[$ii['ingredient']] = $ii;
            }

            // Update existing ingredients or insert new ones
            foreach ($incomingMap as $iname => $idata) {
                if (isset($existingMap[$iname])) {
                    // compare fields
                    $e = $existingMap[$iname];
                    $needsUpdate = (
                        ((float)$e['concentration']) !== (float)$idata['concentration'] ||
                        $e['dilutant'] !== $idata['dilutant'] ||
                        ((float)$e['quantity']) !== (float)$idata['quantity'] ||
                        $e['notes'] !== $idata['notes']
                    );
                    if ($needsUpdate) {
                        $ing_update_types = 'dsdssss';
                        if (!mysqli_stmt_bind_param($ing_update_stmt, $ing_update_types, $idata['concentration'], $idata['dilutant'], $idata['quantity'], $idata['notes'], $name, $fid, $iname)) {
                            mysqli_stmt_close($meta_stmt);
                            mysqli_stmt_close($ing_stmt);
                            mysqli_stmt_close($check_stmt);
                            mysqli_stmt_close($meta_update_stmt);
                            mysqli_stmt_close($ing_delete_stmt);
                            mysqli_stmt_close($ing_update_stmt);
                            mysqli_stmt_close($ing_delete_single_stmt);
                            mysqli_rollback($conn);
                            http_response_code(500);
                            error_log('Failed to bind ingredient update parameters: ' . mysqli_stmt_error($ing_update_stmt));
                            echo json_encode(['error' => 'Failed to bind ingredient update parameters, see logs for details']);
                            return;
                        }
                        if (!mysqli_stmt_execute($ing_update_stmt)) {
                            mysqli_stmt_close($meta_stmt);
                            mysqli_stmt_close($ing_stmt);
                            mysqli_stmt_close($check_stmt);
                            mysqli_stmt_close($meta_update_stmt);
                            mysqli_stmt_close($ing_delete_stmt);
                            mysqli_stmt_close($ing_update_stmt);
                            mysqli_stmt_close($ing_delete_single_stmt);
                            mysqli_rollback($conn);
                            http_response_code(500);
                            error_log('Failed to update ingredient: ' . mysqli_stmt_error($ing_update_stmt));
                            echo json_encode(['error' => 'Failed to update ingredient, see logs for details']);
                            return;
                        }
                    }
                } else {
                    // Insert new ingredient
                    $ingredient_fid = $fid;
                    $ingredient_name = $iname;
                    $ingredient_concentration = (float)$idata['concentration'];
                    $ingredient_quantity = (float)$idata['quantity'];
                    $ingredient_dilutant = $idata['dilutant'];
                    $ing_notes = $idata['notes'];

                    $ing_types_local = 'sssdsdss';
                    if (!mysqli_stmt_bind_param($ing_stmt, $ing_types_local, $ingredient_fid, $name, $ingredient_name, $ingredient_concentration, $ingredient_dilutant, $ingredient_quantity, $ing_notes, $owner_id)) {
                        mysqli_stmt_close($meta_stmt);
                        mysqli_stmt_close($ing_stmt);
                        mysqli_stmt_close($check_stmt);
                        mysqli_stmt_close($meta_update_stmt);
                        mysqli_stmt_close($ing_delete_stmt);
                        mysqli_stmt_close($ing_update_stmt);
                        mysqli_stmt_close($ing_delete_single_stmt);
                        mysqli_rollback($conn);
                        http_response_code(500);
                        error_log('Failed to bind new ingredient parameters: ' . mysqli_stmt_error($ing_stmt));
                        echo json_encode(['error' => 'Failed to bind new ingredient parameters, see logs for details']);
                        return;
                    }
                    if (!mysqli_stmt_execute($ing_stmt)) {
                        mysqli_stmt_close($meta_stmt);
                        mysqli_stmt_close($ing_stmt);
                        mysqli_stmt_close($check_stmt);
                        mysqli_stmt_close($meta_update_stmt);
                        mysqli_stmt_close($ing_delete_stmt);
                        mysqli_stmt_close($ing_update_stmt);
                        mysqli_stmt_close($ing_delete_single_stmt);
                        mysqli_rollback($conn);
                        http_response_code(500);
                        error_log('Failed to insert new ingredient: ' . mysqli_stmt_error($ing_stmt));
                        echo json_encode(['error' => 'Failed to insert new ingredient, see logs for details']);
                        return;
                    }
                    $insertedIngredients++;
                }
            }

            // Delete ingredients that are no longer present
            foreach ($existingMap as $ename => $edata) {
                if (!isset($incomingMap[$ename])) {
                    if (!mysqli_stmt_bind_param($ing_delete_single_stmt, 'ss', $fid, $ename)) {
                        mysqli_stmt_close($meta_stmt);
                        mysqli_stmt_close($ing_stmt);
                        mysqli_stmt_close($check_stmt);
                        mysqli_stmt_close($meta_update_stmt);
                        mysqli_stmt_close($ing_delete_stmt);
                        mysqli_stmt_close($ing_update_stmt);
                        mysqli_stmt_close($ing_delete_single_stmt);
                        mysqli_rollback($conn);
                        http_response_code(500);
                        error_log('Failed to bind ingredient single-delete parameters: ' . mysqli_stmt_error($ing_delete_single_stmt));
                        echo json_encode(['error' => 'Failed to bind ingredient delete parameters, see logs for details']);
                        return;
                    }
                    if (!mysqli_stmt_execute($ing_delete_single_stmt)) {
                        mysqli_stmt_close($meta_stmt);
                        mysqli_stmt_close($ing_stmt);
                        mysqli_stmt_close($check_stmt);
                        mysqli_stmt_close($meta_update_stmt);
                        mysqli_stmt_close($ing_delete_stmt);
                        mysqli_stmt_close($ing_update_stmt);
                        mysqli_stmt_close($ing_delete_single_stmt);
                        mysqli_rollback($conn);
                        http_response_code(500);
                        error_log('Failed to delete old ingredient: ' . mysqli_stmt_error($ing_delete_single_stmt));
                        echo json_encode(['error' => 'Failed to delete old ingredient, see logs for details']);
                        return;
                    }
                }
            }

            $handledIngredients = true;
        }

        // Count as updated if either metadata or ingredients changed
        if ($metaChanged || $ingredientsChanged) {
            $updatedFormulas++;
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
            mysqli_stmt_close($ing_update_stmt);
            mysqli_stmt_close($ing_delete_single_stmt);
            mysqli_rollback($conn);
            http_response_code(500);
            error_log('Failed to bind metadata parameters: ' . mysqli_stmt_error($meta_stmt));
            echo json_encode(['error' => 'Failed to bind metadata parameters, see logs for details']);
            return;
        }
        if (!mysqli_stmt_execute($meta_stmt)) {
            mysqli_stmt_close($meta_stmt);
            mysqli_stmt_close($ing_stmt);
            mysqli_stmt_close($check_stmt);
            mysqli_stmt_close($meta_update_stmt);
            mysqli_stmt_close($ing_delete_stmt);
            mysqli_stmt_close($ing_update_stmt);
            mysqli_stmt_close($ing_delete_single_stmt);
            mysqli_rollback($conn);
            http_response_code(500);
            error_log('Failed to insert formula metadata: ' . mysqli_stmt_error($meta_stmt));
            echo json_encode(['error' => 'Failed to insert formula metadata, see logs for details']);
            return;
        }
        $createdFormulas++;
    }

    // Ingredients
    if (!$handledIngredients && !empty($row['ingredients']) && is_array($row['ingredients'])) {
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

            $ing_types = 'sssdsdss';
            if (!mysqli_stmt_bind_param($ing_stmt, $ing_types, $ingredient_fid, $name, $ingredient_name, $ingredient_concentration, $ingredient_dilutant, $ingredient_quantity, $ing_notes, $owner_id)) {
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
                error_log('Failed to insert ingredient: ' . mysqli_stmt_error($ing_stmt));
                echo json_encode(['error' => 'Failed to insert ingredient, see logs for details']);
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
    error_log('Failed to commit transaction: ' . mysqli_error($conn));
    echo json_encode(['error' => 'Failed to commit transaction, see logs for details']);
    return;
}

mysqli_stmt_close($meta_stmt);
mysqli_stmt_close($ing_stmt);

echo json_encode([
    'success' => 'Formulas processed successfully',
    'formulas_created' => $createdFormulas,
    'formulas_updated' => $updatedFormulas
]);

?>


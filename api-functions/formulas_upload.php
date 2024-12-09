<?php
if (!defined('pvault_panel')){ die('Not Found');}

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

foreach ($data['formulas'] as $row) {
    // Parse formula metadata
    $fid = mysqli_real_escape_string($conn, $row['fid']);
    $name = mysqli_real_escape_string($conn, $row['name']);
    $product_name = mysqli_real_escape_string($conn, $row['product_name'] ?? '-');
    $notes = mysqli_real_escape_string($conn, $row['notes'] ?? '-');
    $concentration = (int)($row['concentration'] ?? 100);
    $status = (int)($row['status'] ?? 0);
    $isProtected = (int)($row['isProtected'] ?? 0);
    $rating = (int)($row['rating'] ?? 0);
    $profile = mysqli_real_escape_string($conn, $row['profile'] ?? 'Default');
    $src = (int)($row['src'] ?? 0);
    $customer_id = (int)($row['customer_id'] ?? 0);
    $revision = (int)($row['revision'] ?? 0);
    $madeOn = mysqli_real_escape_string($conn, $row['madeOn'] ?? date('Y-m-d H:i:s'));

    $query = "INSERT INTO formulasMetaData (fid, name, product_name, notes, finalType, status, isProtected, rating, profile, src, customer_id, revision, madeOn) 
              VALUES ('$fid', '$name', '$product_name', '$notes', '$concentration', '$status', '$isProtected', '$rating', '$profile', '$src', '$customer_id', '$revision', '$madeOn')";

    if (!mysqli_query($conn, $query)) {
        error_log(mysqli_error($conn));
        echo json_encode(['error' => 'Failed to insert formula', 'details' => mysqli_error($conn)]);
        return;
    }

    // Parse associated ingredients
    if (!empty($row['ingredients']) && is_array($row['ingredients'])) {
        foreach ($row['ingredients'] as $ingredient) {
            $ingredient_fid = mysqli_real_escape_string($conn, $ingredient['fid']);
            $ingredient_name = mysqli_real_escape_string($conn, $ingredient['ingredient']);
            $ingredient_concentration = (float)($ingredient['concentration'] ?? 100);
            $ingredient_quantity = (float)($ingredient['quantity'] ?? 0);
            $ingredient_dilutant = mysqli_real_escape_string($conn, $ingredient['dilutant']);

            $ingredient_query = "INSERT INTO formulas (fid, name, ingredient, concentration, dilutant, quantity, notes) 
                                 VALUES ('$ingredient_fid', '$name', '$ingredient_name', '$ingredient_concentration', '$ingredient_dilutant', '$ingredient_quantity', '-')";

            if (!mysqli_query($conn, $ingredient_query)) {
                error_log(mysqli_error($conn));
                echo json_encode(['error' => 'Failed to insert ingredient', 'details' => mysqli_error($conn)]);
            }
        }
    }
}

echo json_encode(['success' => 'Data inserted successfully']);

?>

    
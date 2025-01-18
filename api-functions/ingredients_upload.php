<?php

if (!defined('pvault_panel')){ die('Not Found');}

header('Content-Type: application/json');
global $conn, $userID;

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Check if the JSON data is valid
if ($data === null) {
    echo json_encode(['error' => 'Invalid JSON data']);
    exit();
}

// Loop through each ingredient and insert it into the database
foreach ($data['ingredients'] as $ingredient) {
    // Escape data to prevent SQL injection (for fields with string values)
    $name = mysqli_real_escape_string($conn, $ingredient['name']);
    $INCI = mysqli_real_escape_string($conn, $ingredient['INCI'] ?? null);
    $type = mysqli_real_escape_string($conn, $ingredient['type'] ?? null);
    $strength = mysqli_real_escape_string($conn, $ingredient['strength'] ?? null);
    $category = (int)($ingredient['category'] ?? 1);  // Default to 1 if not provided
    $purity = mysqli_real_escape_string($conn, $ingredient['purity'] ?? null);
    $cas = mysqli_real_escape_string($conn, $ingredient['cas'] ?? null);
    $einecs = mysqli_real_escape_string($conn, $ingredient['einecs'] ?? null);
    $FEMA = mysqli_real_escape_string($conn, $ingredient['FEMA'] ?? null);
    $reach = mysqli_real_escape_string($conn, $ingredient['reach'] ?? null);
    $tenacity = mysqli_real_escape_string($conn, $ingredient['tenacity'] ?? null);
    $chemical_name = mysqli_real_escape_string($conn, $ingredient['chemical_name'] ?? null);
    $formula = mysqli_real_escape_string($conn, $ingredient['formula'] ?? null);
    $flash_point = mysqli_real_escape_string($conn, $ingredient['flash_point'] ?? null);
    $appearance = mysqli_real_escape_string($conn, $ingredient['appearance'] ?? null);
    $rdi = (int)($ingredient['rdi'] ?? 0);
    $notes = mysqli_real_escape_string($conn, $ingredient['notes'] ?? null);
    $profile = mysqli_real_escape_string($conn, $ingredient['profile'] ?? null);
    $solvent = mysqli_real_escape_string($conn, $ingredient['solvent'] ?? null);
    $odor = mysqli_real_escape_string($conn, $ingredient['odor'] ?? null);
    $allergen = (int)($ingredient['allergen'] ?? null);
    $flavor_use = (int)($ingredient['flavor_use'] ?? null);
    $soluble = mysqli_real_escape_string($conn, $ingredient['soluble'] ?? null);
    $logp = mysqli_real_escape_string($conn, $ingredient['logp'] ?? null);

    // Treating catX fields as floats
    $cat1 = (float)($ingredient['cat1'] ?? 100);
    $cat2 = (float)($ingredient['cat2'] ?? 100);
    $cat3 = (float)($ingredient['cat3'] ?? 100);
    $cat4 = (float)($ingredient['cat4'] ?? 100);
    $cat5A = (float)($ingredient['cat5A'] ?? 100);
    $cat5B = (float)($ingredient['cat5B'] ?? 100);
    $cat5C = (float)($ingredient['cat5C'] ?? 100);
    $cat5D = (float)($ingredient['cat5D'] ?? 100);
    $cat6 = (float)($ingredient['cat6'] ?? 100);
    $cat7A = (float)($ingredient['cat7A'] ?? 100);
    $cat7B = (float)($ingredient['cat7B'] ?? 100);
    $cat8 = (float)($ingredient['cat8'] ?? 100);
    $cat9 = (float)($ingredient['cat9'] ?? 100);
    $cat10A = (float)($ingredient['cat10A'] ?? 100);
    $cat10B = (float)($ingredient['cat10B'] ?? 100);
    $cat11A = (float)($ingredient['cat11A'] ?? 100);
    $cat11B = (float)($ingredient['cat11B'] ?? 100);
    $cat12 = (float)($ingredient['cat12'] ?? 100);

    // Additional fields
    $impact_top = mysqli_real_escape_string($conn, $ingredient['impact_top'] ?? null);
    $impact_heart = mysqli_real_escape_string($conn, $ingredient['impact_heart'] ?? null);
    $impact_base = mysqli_real_escape_string($conn, $ingredient['impact_base'] ?? null);
    //$owner_id = (int)($ingredient['owner_id'] ?? 0);
    $usage_type = mysqli_real_escape_string($conn, $ingredient['usage_type'] ?? null);
    $noUsageLimit = (int)($ingredient['noUsageLimit'] ?? 0);
    $byPassIFRA = (int)($ingredient['byPassIFRA'] ?? 0);
    $isPrivate = (int)($ingredient['isPrivate'] ?? 0);
    $molecularWeight = mysqli_real_escape_string($conn, $ingredient['molecularWeight'] ?? null);
    $physical_state = (int)($ingredient['physical_state'] ?? 1); // Default to 1 if not provided
    $cid = (int)($ingredient['cid'] ?? null);
    $shelf_life = (int)($ingredient['shelf_life'] ?? 0);

    // Create SQL query for inserting data
    $query = "INSERT INTO ingredients 
        (name, INCI, type, strength, category, purity, cas, einecs, FEMA, reach, tenacity, chemical_name, 
        formula, flash_point, appearance, rdi, notes, profile, solvent, odor, allergen, flavor_use, soluble, 
        logp, cat1, cat2, cat3, cat4, cat5A, cat5B, cat5C, cat5D, cat6, cat7A, cat7B, cat8, cat9, cat10A, 
        cat10B, cat11A, cat11B, cat12, impact_top, impact_heart, impact_base, created_at, updated_at, owner_id, 
        usage_type, noUsageLimit, byPassIFRA, isPrivate, molecularWeight, physical_state, cid, shelf_life)
        VALUES 
        ('$name', '$INCI', '$type', '$strength', $category, '$purity', '$cas', '$einecs', '$FEMA', '$reach', 
        '$tenacity', '$chemical_name', '$formula', '$flash_point', '$appearance', $rdi, '$notes', '$profile', 
        '$solvent', '$odor', $allergen, $flavor_use, '$soluble', '$logp', $cat1, $cat2, $cat3, $cat4, $cat5A, 
        $cat5B, $cat5C, $cat5D, $cat6, $cat7A, $cat7B, $cat8, $cat9, $cat10A, $cat10B, $cat11A, $cat11B, 
        $cat12, '$impact_top', '$impact_heart', '$impact_base', NOW(), NOW(), $userID, '$usage_type', 
        $noUsageLimit, $byPassIFRA, $isPrivate, '$molecularWeight', $physical_state, $cid, $shelf_life)";
    
    // Execute the SQL query
    if (!mysqli_query($conn, $query)) {
		error_log(mysqli_error($conn));
        echo json_encode(['error' => 'Failed to insert data for ingredient: ' . mysqli_error($conn)]);
        exit();
    }
}

// Respond with a success message
echo json_encode(['success' => 'Data inserted successfully']);
?>

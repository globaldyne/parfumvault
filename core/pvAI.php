<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/pvAIHelper.php');


if ($_POST['action'] === 'addFormulaAI') {
    if ($user_settings['use_ai_service'] == '0') {
        echo json_encode(['error' => 'AI formula creation is disabled']);
        return;
    }

    $name = trim($_POST['name'] ?? '');
    $notes = trim($_POST['description'] ?? '');

    if (!$name) {
        echo json_encode(['error' => 'Formula name is required.']);
        return;
    }
    if (strlen($name) > 100) {
        echo json_encode(['error' => 'Formula name is too big. Max 100 chars allowed.']);
        return;
    }
    if (!$notes) {
        echo json_encode(['error' => 'Formula description is required.']);
        return;
    }

    // Prevent duplicates
    $safe_name = mysqli_real_escape_string($conn, $name);
    if (mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE name = '$safe_name' AND owner_id = '$userID'"))) {
        echo json_encode(['error' => "Formula $name already exists"]);
        return;
    }

    require_once(__ROOT__.'/func/genFID.php');
    $catClass = mysqli_real_escape_string($conn, $_POST['catClass'] ?? '');
    $finalType = mysqli_real_escape_string($conn, $_POST['finalType'] ?? '100');
    $customer_id = (int)($_POST['customer'] ?? 0);
    $fid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');

    $prompt = $notes. ', type formula';
    $result = pvAIHelper($prompt);

    if (isset($result['error'])) {
        echo json_encode(['error' => $result['error']]);
        return;
    }

    // Fix: If AI returns a success with an embedded error, treat as error
    if (isset($result['success']['error'])) {
        echo json_encode(['error' => is_array($result['success']['error']) ? json_encode($result['success']['error']) : $result['success']['error']]);
        return;
    }

    // --- Begin: decode formula from AI result ---
    $ingredients = [];
    if (isset($result['success']['formula']) && is_array($result['success']['formula'])) {
        $ingredients = $result['success']['formula'];
    } elseif (is_array($result['success'])) {
        // fallback for legacy or unexpected structure
        $ingredients = $result['success'];
    } else {
        error_log("Invalid ingredient format: " . json_encode($result['success']));
        echo json_encode(['error' => 'Invalid ingredient format from AI']);
        return;
    }
    // --- End: decode formula from AI result ---

    error_log("Decoded JSON: " . json_encode($ingredients));

    if (!is_array($ingredients)) {
        error_log("Invalid ingredient format: " . json_encode($ingredients));
        echo json_encode(['error' => 'Invalid ingredient format from AI']);
        return;
    }

    $escaped_name = mysqli_real_escape_string($conn, $name);
    $escaped_notes = mysqli_real_escape_string($conn, $notes);

    $query = "INSERT INTO formulasMetaData (fid, name, notes, catClass, finalType, customer_id, owner_id) 
              VALUES ('$fid', '$escaped_name', '$escaped_notes', '$catClass', '$finalType', '$customer_id', '$userID')";

    if (!mysqli_query($conn, $query)) {
        echo json_encode(['error' => 'Failed to save metadata: ' . mysqli_error($conn)]);
        return;
    }

    $last_id = mysqli_insert_id($conn);
    mysqli_query($conn, "INSERT INTO formulasTags (formula_id, tag_name, owner_id) VALUES ('$last_id','AI Generated','$userID')");

    foreach ($ingredients as $row) {
        $ingredient = mysqli_real_escape_string($conn, $row['ingredient'] ?? '');
        $cas = mysqli_real_escape_string($conn, $row['cas'] ?? '');
        $quantity = floatval($row['quantity'] ?? 0);
        $dilution = floatval($row['dilution'] ?? 100);
        $solvent = mysqli_real_escape_string($conn, $row['solvent'] ?? 'None');
    
        if ($ingredient && $cas && $quantity > 0) {
            // Check if the ingredient exists
            $result = mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$ingredient' AND owner_id = '$userID' LIMIT 1");
    
            if ($r = mysqli_fetch_assoc($result)) {
                $ingredient_id = (int)$r['id'];
            } else {
                // Insert ingredient if not exists
                $insert = mysqli_query($conn, "
                    INSERT INTO ingredients (name, cas, owner_id)
                    VALUES ('$ingredient', '$cas', '$userID')
                ");
    
                if (!$insert) {
                    error_log("PV error: Failed to insert new ingredient $ingredient ($cas): " . mysqli_error($conn));
                    continue;
                }
    
                $ingredient_id = mysqli_insert_id($conn);
            }
    
            // Insert into formulas
            mysqli_query($conn, "
                INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity, owner_id)
                VALUES ('$fid', '$escaped_name', '$ingredient', '$ingredient_id', '$dilution', '$solvent', '$quantity', '$userID')
            ");
            error_log("Inserted fid: $fid, name: $escaped_name, ingredient: $ingredient with quantity: $quantity, ingredient_id: $ingredient_id, owner_id: $userID");
        }
    }
    

    echo json_encode([
        'success' => [
            'id' => $last_id,
            'msg' => "$name created",
            'formula' => $ingredients
        ]
    ]);

} else if ($_POST['action'] === 'aiChat') {
    $prompt = $_POST['message'] ?? '';
    $result = pvAIHelper($prompt);

   // error_log("AI Chat Prompt: $prompt");
   // error_log("AI Chat Result: " . json_encode($result));

    if (isset($result['error'])) {
        echo json_encode(['error' => $result['error']]);
        return;
    }

    // Fix: If AI returns a success with an embedded error, treat as error
    if (isset($result['success']['error'])) {
        echo json_encode(['error' => is_array($result['success']['error']) ? json_encode($result['success']['error']) : $result['success']['error']]);
        return;
    }

    // Standardise output: always include type at top level and inside success
    $type = $result['type'] ?? ($result['success']['type'] ?? 'unknown');
    if (is_array($result['success'])) {
        $result['success']['type'] = $type;
    }
    echo json_encode([
        'success' => $result['success'],
        'type' => $type
    ]);
    return;
} else if ($_POST['action'] === 'getAIReplacementSuggestions') {

    $ingredient = $_POST['ingredient'] ?? '';
    
    if (empty($ingredient)) {
        echo json_encode(['error' => 'Ingredient is required.']);
        return;
    }

    $prompt = "Suggest 5 replacements for the ingredient $ingredient";
    
    $result = pvAIHelper($prompt);
    
    error_log("AI Replacement Prompt: $prompt");
    error_log("AI Replacement Result: " . json_encode($result));
    
    if (isset($result['error'])) {
        echo json_encode(['error' => $result['error']]);
        return;
    }

    // Expecting: $result['success']['replacements'] is an array, $result['type'] === 'replacements'
    if (
        isset($result['success']['replacements']) &&
        is_array($result['success']['replacements']) &&
        isset($result['type']) && $result['type'] === 'replacements'
    ) {
        // Enrich each suggestion with inventory info
        foreach ($result['success']['replacements'] as &$suggestion) {
            $safe_ingredient = mysqli_real_escape_string($conn, $suggestion['name'] ?? $suggestion['ingredient'] ?? $suggestion['cas'] ??'');
            $ingredient_name = trim(preg_replace('/\s*\(CAS.*$/i', '', $safe_ingredient));
            $ingredient_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$ingredient_name' AND owner_id = '$userID' LIMIT 1"));

            if ($ingredient_data) {
                $ingredient_id = (int)$ingredient_data['id'];
                $inventory = mysqli_fetch_assoc(mysqli_query($conn, "
                    SELECT ingSupplierID, SUM(stock) AS stock, mUnit 
                    FROM suppliers 
                    WHERE ingID = '$ingredient_id' AND owner_id = '$userID'
                "));
                // Fix: Ensure inventory is always an object with stock and mUnit, even if null
                $suggestion['inventory'] = [
                    'stock' => isset($inventory['stock']) ? (float)$inventory['stock'] : 0,
                    'mUnit' => $inventory['mUnit'] ?? ''
                ];
            } else {
                $suggestion['inventory'] = [
                    'stock' => 0,
                    'mUnit' => ''
                ];
            }
        }
        echo json_encode([
            'success' => [
                'replacements' => $result['success']['replacements'],
            ],
            'type' => 'replacements'
        ]);
        return;
    }

    // fallback: if not in expected format, return as error
    echo json_encode(['error' => 'AI did not return replacement suggestions in the expected format.']);
} else {
    echo json_encode(['error' => 'Invalid action']);
}

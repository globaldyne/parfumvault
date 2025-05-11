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

    $prompt = "Create a perfume formula in JSON with ingredient name as ingredient, CAS number as cas, and quantity in grams as quantity. Total formula quantity 100. Description: $notes. Return only JSON.";

    $result = pvAIHelper($prompt);

    if (isset($result['error'])) {
        echo json_encode(['error' => $result['error']]);
        return;
    }

    $ingredients = $result['success'];
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
    //mysqli_query($conn, "UPDATE formulasMetaData SET isProtected='1' WHERE id='$last_id' AND owner_id='$userID'");

    foreach ($ingredients as $row) {
        $ingredient = mysqli_real_escape_string($conn, $row['ingredient'] ?? '');
        $cas = mysqli_real_escape_string($conn, $row['cas'] ?? '');
        $quantity = floatval($row['quantity'] ?? 0);
    
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
                VALUES ('$fid', '$escaped_name', '$ingredient', '$ingredient_id', 100, 'None', '$quantity', '$userID')
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
    $result = pvAIHelper($prompt." \n\nAnswer in JSON format with only the answer in the property description . No other text. \n\n");
    
    error_log("AI Chat Prompt: $prompt");
    error_log("AI Chat Result: " . json_encode($result));
    
    if (isset($result['error'])) {
        echo json_encode(['error' => $result['error']]);
        return;
    }

    echo json_encode(['success' => $result['success']]);

} else if ($_POST['action'] === 'getAIReplacementSuggestions') {

    $ingredient = $_POST['ingredient'] ?? '';
    $prompt = "Suggest 5 replacements for the ingredient $ingredient. Return only ingredient name and description as a JSON.";
    
    $result = pvAIHelper($prompt);
    
    error_log("AI Replacement Prompt: $prompt");
    error_log("AI Replacement Result: " . json_encode($result));
    
    if (isset($result['error'])) {
        echo json_encode(['error' => $result['error']]);
        return;
    }

    echo json_encode(['success' => $result['success']]);
} else {
    echo json_encode(['error' => 'Invalid action']);
}

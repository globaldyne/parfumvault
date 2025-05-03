<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


if ($_POST['action'] === 'addFormulaAI') {
    if ($user_settings['use_ai_service'] == '0') {
        echo json_encode(['error' => 'AI formula creation is disabled']);
        return;
    }

    $name = trim($_POST['name'] ?? '');
    $notes = trim($_POST['description'] ?? '');
    $profile = trim($_POST['profile'] ?? '');

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
    if (!$profile) {
        echo json_encode(['error' => 'Formula profile is required.']);
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

    $prompt = "Create a perfume formula in JSON with ingredient name, CAS number as cas, and quantity in grams as quantity. Description: $notes. Profile: $profile. Return only JSON.";

    $formula_json = '';
    $decoded = null;

    if ($user_settings['ai_service_provider'] === 'openai') {
        $openai_api_key = $user_settings['openai_api_key'];
        if (empty($openai_api_key)) {
            echo json_encode(['error' => 'OpenAI API key is not set']);
            return;
        }

        $openai_model = $user_settings['openai_model'] ?: "gpt-4.1";
        $openai_temperature = $user_settings['openai_temperature'] ?: 0.7;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer $openai_api_key"
            ],
            CURLOPT_POSTFIELDS => json_encode([
                "model" => $openai_model,
                "messages" => [
                    ["role" => "system", "content" => "You are a perfumer AI that only responds with valid JSON arrays of ingredients."],
                    ["role" => "user", "content" => $prompt]
                ],
                "temperature" => $openai_temperature
            ])
        ]);

        $openai_response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            echo json_encode(['error' => 'OpenAI API request failed: ' . $error]);
            return;
        }

        $openai_data = json_decode($openai_response, true);
        $formula_json = $openai_data['choices'][0]['message']['content'] ?? '';
        $decoded = json_decode($formula_json, true);

        if (!$decoded || !is_array($decoded)) {
            error_log("OpenAI error: " . json_encode($openai_data));
            echo json_encode(['error' => $openai_data['error']['message'] ?? 'Invalid response from OpenAI']);
            return;
        }

    } elseif ($user_settings['ai_service_provider'] === 'google_gemini') {
        error_log("Service provider: Google Gemini");
        $gemini_api_key = $user_settings['google_gemini_api_key'];
        $gemini_model = $user_settings['google_gemini_model'] ?: 'gemini-2.0-flash';

        if (empty($gemini_api_key)) {
            echo json_encode(['error' => 'Gemini API key is not set']);
            return;
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://generativelanguage.googleapis.com/v1beta/models/$gemini_model:generateContent?key=$gemini_api_key",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            CURLOPT_POSTFIELDS => json_encode([
                "contents" => [[
                    "role" => "user",
                    "parts" => [["text" => "$prompt\nOnly output JSON array."]]
                ]]
            ])
        ]);

        $gemini_response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            echo json_encode(['error' => 'Gemini API request failed: ' . $error]);
            return;
        }

        $gemini_data = json_decode($gemini_response, true);
        $raw_content = $gemini_data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (!$raw_content) {
            error_log("Gemini error 400: " . json_encode($gemini_data));
            echo json_encode(['error' => $gemini_data ['error']['message'] ?? 'Invalid response from Gemini']);
            return;
        }

        // Clean JSON string
        $cleaned = trim($raw_content);
        $cleaned = preg_replace('/^```(json)?/i', '', $cleaned);
        $cleaned = preg_replace('/```$/', '', $cleaned);
        $cleaned = preg_replace('/^[^{\[].*?({|\[)/s', '$1', $cleaned);

        $formula_json = $cleaned;
        $decoded = json_decode($formula_json, true);

        if (!$decoded || !is_array($decoded)) {
            error_log("Gemini returned invalid JSON: " . $formula_json);
            echo json_encode(['error' => 'Gemini returned malformed JSON.']);
            return;
        }
    }

    $ingredients = $decoded;
    error_log("Decoded JSON: " . json_encode($ingredients));
    if (!is_array($ingredients)) {
        error_log("Invalid ingredient format: " . json_encode($ingredients));
        echo json_encode(['error' => 'Invalid ingredient format from AI']);
        return;
    }

    $escaped_name = mysqli_real_escape_string($conn, $name);
    $escaped_notes = mysqli_real_escape_string($conn, $notes);
    $escaped_profile = mysqli_real_escape_string($conn, $profile);

    $query = "INSERT INTO formulasMetaData (fid, name, notes, profile, catClass, finalType, customer_id, owner_id) 
              VALUES ('$fid', '$escaped_name', '$escaped_notes', '$escaped_profile', '$catClass', '$finalType', '$customer_id', '$userID')";

    if (!mysqli_query($conn, $query)) {
        echo json_encode(['error' => 'Failed to save metadata: ' . mysqli_error($conn)]);
        return;
    }

    $last_id = mysqli_insert_id($conn);
    mysqli_query($conn, "INSERT INTO formulasTags (formula_id, tag_name, owner_id) VALUES ('$last_id','AI','$userID')");
    mysqli_query($conn, "UPDATE formulasMetaData SET isProtected='1' WHERE id='$last_id' AND owner_id='$userID'");

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
}

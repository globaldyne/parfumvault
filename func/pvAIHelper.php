<?php
/**
 * Generates a formula and Chat responses using AI based on the given provider and prompt.
 *
 * This function interacts with AI providers (e.g., OpenAI, Gemini) to generate a response
 * based on the provided prompt and user_settings. It supports OpenAI and Gemini.
 *
 * @param string $prompt The user-provided input to guide the AI's response.
 * @return array An associative array containing:
 *   - 'success': The decoded JSON response from the AI (if successful).
 *   - 'error': An error message (if the request fails or the response is invalid).
 */

if (!defined('pvault_panel')){ die('Not Found');}

function getUserSettings($userID) {
    global $conn;
    $settings = [];
    $q = mysqli_query($conn, "SELECT key_name, value FROM user_settings WHERE owner_id = '$userID'");
    while ($row = mysqli_fetch_assoc($q)) {
        $settings[$row['key_name']] = $row['value'];
    }
    return $settings;
}

function pvAIHelper($prompt) {
    global $system_settings, $user_settings, $userID;
    if (empty($user_settings) && isset($userID)) {
        $user_settings = getUserSettings($userID);
    }

    // Check if the user has enabled AI service
    if ($user_settings['use_ai_service'] !== '1') {
        return ['error' => 'AI service is disabled, please enable it in settings.'];
    }

    // Validate the provider
    $provider = strtolower($user_settings['ai_service_provider'] ?? 'openai');
    if (!in_array($provider, ['openai', 'google_gemini'])) {
        return ['error' => 'Unsupported AI provider'];
    }

    // Validate the prompt
    if (empty($prompt)) {
        return ['error' => 'Prompt cannot be empty'];
    }

    // GEMINI or OPENAI
    $isGemini = $provider === 'google_gemini';
    $api_key = $isGemini
        ? ($user_settings['google_gemini_api_key'] ?? '')
        : ($user_settings['openai_api_key'] ?? '');
    $model = $isGemini
        ? ($user_settings['google_gemini_model'] ?: 'gemini-2.0-flash')
        : ($user_settings['openai_model'] ?: 'gpt-4.1');
    $temperature = isset($user_settings['openai_temperature']) ? (float)$user_settings['openai_temperature'] : 0.7;

    if (empty($api_key)) {
        return ['error' => 'API key is not set'];
    }

    // Use classifyPrompt for type detection
    $type = classifyPrompt($prompt);

    // Build system prompt
    if ($isGemini) {
        if ($type === 'ingredient') {
            $systemPrompt = "Respond ONLY with a single JSON object containing the following properties if available: 'description', 'physical_state', 'color', 'category', 'cas' and 'olfactory_type' (such as top, heart, or base note). Do not include any other text or explanation.";
        } elseif ($type === 'formula') {
            $systemPrompt = "Create a perfume formula in JSON. Each ingredient must be an object with: ingredient name as 'ingredient', CAS number as 'cas', quantity in grams as 'quantity', dilution percentage as 'dilution', solvent type as 'solvent', properties, and olfactory type (top, heart, or base note) if known. Total formula quantity 100 grams. Return only JSON. All quantity values must be formatted as numbers with two decimal places (e.g., 12.00). Do not include any other text, name or explanation.";
        } elseif ($type === 'replacements') {
            $systemPrompt = "You are a professional perfumer. Only respond to questions about perfume formulation, ingredients, fragrance notes, and techniques for creating perfume. Do not respond to anything unrelated to perfumery. Suggest 5 replacements for the ingredient {$prompt} Respond ONLY with a single JSON object with a 'replacements' property containing an array of up to 5 suitable ingredient replacements for '{$prompt}'. For each replacement, include the ingredient name as 'ingredient', CAS number as 'cas', properties as 'properties', and a short description only. Do not include any other text or explanation.";
        } else {
            $systemPrompt = "You are a professional perfumer. Only respond to questions about perfume formulation, ingredients, fragrance notes, and techniques for creating perfume. Do not respond to anything unrelated to perfumery.\n$prompt\nOnly output JSON array. Add json property type set to ingredient, formula, replacements, or general based on the content of the response. If you are not sure, set type to general and context in field description.";
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=$api_key",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            CURLOPT_POSTFIELDS => json_encode([
                "contents" => [[
                    "role" => "user",
                    "parts" => [[
                        'text' => $systemPrompt
                    ]]
                ]]
            ])
        ]);
    } else { // OPENAI
        if ($type === 'ingredient') {
            $systemPrompt = "Respond ONLY with a single JSON object containing the following properties if available: 'description', 'physical_state', 'color', 'category', and 'olfactory_type' (such as top, heart, or base note). Do not include any other text or explanation.";
        } elseif ($type === 'formula') {
            $systemPrompt = "Create a perfume formula in JSON. Each ingredient must be an object with: ingredient name as 'ingredient', CAS number as 'cas', quantity in grams as 'quantity', dilution percentage as 'dilution', solvent type as 'solvent', properties, and olfactory type (top, heart, or base note) if known. Total formula quantity 100 grams. Return only JSON. All quantity values must be formatted as numbers with two decimal places (e.g., 12.00). Do not include any other text, name or explanation.";
        } elseif ($type === 'replacements') {
            $systemPrompt = "You are a professional perfumer. Only respond to questions about perfume formulation, ingredients, fragrance notes, and techniques for creating perfume. Do not respond to anything unrelated to perfumery. Suggest 5 replacements for the ingredient {$prompt} Respond ONLY with a single JSON object with a 'replacements' property containing an array of up to 5 suitable ingredient replacements for '{$prompt}'. For each replacement, include the ingredient name as 'ingredient', CAS number as 'cas', properties as 'properties', and a short description only. Do not include any other text or explanation.";
        } else {
            $systemPrompt = "You are a professional perfumer. Only respond to questions about perfume formulation, ingredients, fragrance notes, and techniques for creating perfume. Do not respond to anything unrelated to perfumery.\n$prompt\nOnly output JSON array. Add json property type set to ingredient, formula, replacements, or general based on the content of the response. If you are not sure, set type to general and context in field description.";
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer $api_key"
            ],
            CURLOPT_POSTFIELDS => json_encode([
                "model" => $model,
                "messages" => [
                    ["role" => "system", "content" => $systemPrompt],
                    ["role" => "user", "content" => $prompt]
                ],
                "temperature" => $temperature
            ])
        ]);
    }

    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    if ($error) {
        return ['error' => 'API request failed: ' . $error];
    }

    // Parse response
    if ($isGemini) {
        $data = json_decode($response, true);
        $raw_content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    } else { // OpenAI
        $data = json_decode($response, true);
        $raw_content = $data['choices'][0]['message']['content'] ?? '';
    }

    if (!$raw_content) {
        error_log("API error 400: " . json_encode($data));
        return ['error' => $data['error']['message'] ?? 'Invalid response from API'];
    }

    // --- Robust AI JSON extraction and type detection ---
    $cleaned = trim($raw_content);
    $cleaned = preg_replace('/^```(json)?/i', '', $cleaned);
    $cleaned = preg_replace('/```$/', '', $cleaned);
    $cleaned = preg_replace('/^[^{\[].*?({|\[)/s', '$1', $cleaned);

    $decoded = json_decode($cleaned, true);

    $type = 'unknown';
    if (is_array($decoded)) {
        if (isset($decoded['formula']) || (isset($decoded[0]) && isset($decoded[0]['formula']))) {
            $type = 'formula';
        } elseif (isset($decoded['replacements']) && is_array($decoded['replacements'])) {
            $type = 'replacements';
        } elseif (isset($decoded['description']) && is_array($decoded['description'])) {
            $type = 'replacements';
        } elseif (isset($decoded['description']) || (isset($decoded[0]) && isset($decoded[0]['description']))) {
            $type = 'ingredient';
        } elseif (isset($decoded['type']) && $decoded['type'] === 'general') {
            $type = 'general';
        }
    }

    if ($decoded !== null && is_array($decoded)) {
        $decoded['type'] = $type;
        if ($type === 'general' && isset($decoded['description'])) {
            return [
                'success' => $decoded['description'],
                'type' => $type
            ];
        }
        if ($type === 'replacements') {
            return [
                'success' => $decoded,
                'type' => $type
            ];
        }
        if ($type !== 'unknown') {
            return [
                'success' => $decoded,
                'type' => $type
            ];
        }
        // If type is unknown but the decoded is an array with a single object with a text and type=general, fix to type general
        if (
            isset($decoded[0]['type']) && $decoded[0]['type'] === 'general' &&
            isset($decoded[0]['text'])
        ) {
            unset($decoded[0]['type']);
            return [
                'success' => [
                    0 => $decoded[0],
                    'type' => 'general'
                ],
                'type' => 'general'
            ];
        }
    }

    // If not JSON/object, or type is unknown, return as type general and put data in 'text'
    $raw = is_string($raw_content ?? $raw_response ?? '') ? ($raw_content ?? $raw_response) : json_encode($decoded);

    // Try to extract JSON array from inside a markdown code block in text
    if (preg_match('/```(?:json)?\s*([\s\S]+?)\s*```/i', $raw, $matches)) {
        $inner = trim($matches[1]);
        $innerDecoded = json_decode($inner, true);
        if (is_array($innerDecoded)) {
            // If it's an array of objects with text/type, flatten to just text fields
            $result = [];
            foreach ($innerDecoded as $i => $obj) {
                if (isset($obj['text'])) {
                    $result[$i] = ['text' => $obj['text']];
                }
            }
            return [
                'success' => $result,
                'type' => 'general'
            ];
        }
    }

    // Default fallback
    return [
        'success' => [
            '0' => [
                'text' => $raw
            ]
        ],
        'type' => 'general'
    ];
}

/**
 * Classify the prompt as 'ingredient', 'formula', 'replacements', or 'general'.
 * Used for Gemini/OpenAI prompt logic.
 */
function classifyPrompt($prompt) {
    $promptLower = strtolower($prompt);

    // Keywords for formula requests
    $formulaKeywords = [
        'formula', 'blend', 'recipe', 'composition', 'make a', 'create a', 'generate a', 'build a', 'suggest a formula'
    ];
    // Keywords for ingredient questions
    $ingredientKeywords = [
       'ingredient', 'what is', 'tell me about', 'describe', 'properties of', 'use of', 'source of', 'explain', 'info about'
    ];

    // Detect replacements prompt
    if (preg_match('/replacements? for|alternative(s)? to|substitute(s)? for|replace(s)? for/i', $promptLower, $match)) {
        return 'replacements';
    }

    foreach ($formulaKeywords as $kw) {
        if (strpos($promptLower, $kw) !== false) {
            return 'formula';
        }
    }
    foreach ($ingredientKeywords as $kw) {
        if (strpos($promptLower, $kw) !== false) {
            return 'ingredient';
        }
    }
    if (str_word_count($prompt) == 1) {
        return 'ingredient';
    }
    if (preg_match('/^(generate|make|create|build|suggest)\b/i', $prompt, $match)) {
        return 'general';
    }
    return 'general'; // Default to general if not matched
}
?>
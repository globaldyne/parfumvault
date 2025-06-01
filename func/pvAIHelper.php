<?php
/**
 * Generates a formula and Chat responses using AI based on the given provider and prompt.
 *
 * This function interacts with AI providers (e.g., OpenAI) to generate a response
 * based on the provided prompt and user_settings. It currently supports OpenAI and
 * includes error handling for invalid API keys, failed requests, and unexpected responses.
 *
 * @param string $prompt The user-provided input to guide the AI's response.

 *
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
    // Check if AI service is enabled
    /* TODO: Uncomment to check system settings (PV Online)
    if ($system_settings['ai_service'] !== '1') {
        return ['error' => 'AI service is disabled'];
    }
    */

    // Check if the user has enabled AI service
    if ($user_settings['use_ai_service'] !== '1') {
        return ['error' => 'AI service is disabled for this user'];
    }

    
    // Validate the provider
    $provider = strtolower($user_settings['ai_service_provider'] ?? 'openai');
    if (!in_array($provider, ['openai', 'google_gemini', 'pedro_perfumer'])) {
        return ['error' => 'Unsupported AI provider'];
    }

    // Validate the prompt
    if (empty($prompt)) {
        return ['error' => 'Prompt cannot be empty'];
    }
    
    if ($provider === 'openai') {
        $api_key = $user_settings['openai_api_key'];
        $model = $user_settings['openai_model'] ?: "gpt-4.1";
        $temperature = $user_settings['openai_temperature'] ?: 0.7;

        if (empty($api_key)) {
            return ['error' => 'OpenAI API key is not set'];
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
                    ["role" => "system", "content" => "You are a perfumer AI that only responds to perfume formulation related questions."],
                    ["role" => "user", "content" => $prompt]
                ],
                "temperature" => $temperature
            ])
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return ['error' => 'OpenAI API request failed: ' . $error];
        }

        $data = json_decode($response, true);
        $content = $data['choices'][0]['message']['content'] ?? '';
        $decoded = json_decode($content, true);

        if (!$decoded || !is_array($decoded)) {
            error_log("OpenAI error: " . json_encode($data));
            return ['error' => $data['error']['message'] ?? 'Invalid response from OpenAI'];
        }

        return ['success' => $decoded];
    } elseif ($provider === 'google_gemini') {
        $api_key = $user_settings['google_gemini_api_key'];
        $model = $user_settings['google_gemini_model'] ?: 'gemini-2.0-flash';

        if (empty($api_key)) {
            return ['error' => 'Gemini API key is not set'];
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
                        'text' => "You are a professional perfumer. Only respond to questions about perfume formulation, ingredients, fragrance notes, and techniques for creating perfume. Do not respond to anything unrelated to perfumery.\n$prompt\nOnly output JSON array."
                    ]]
                ]]
            ])
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return ['error' => 'Gemini API request failed: ' . $error];
        }

        $data = json_decode($response, true);
        $raw_content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (!$raw_content) {
            error_log("Gemini error 400: " . json_encode($data));
            return ['error' => $data['error']['message'] ?? 'Invalid response from Gemini'];
        }

        $cleaned = trim($raw_content);
        $cleaned = preg_replace('/^```(json)?/i', '', $cleaned);
        $cleaned = preg_replace('/```$/', '', $cleaned);
        $cleaned = preg_replace('/^[^{\[].*?({|\[)/s', '$1', $cleaned);

        $decoded = json_decode($cleaned, true);

        if (!$decoded || !is_array($decoded)) {
            error_log("Gemini returned invalid JSON: " . $cleaned);
            return ['error' => 'Gemini returned malformed JSON.'];
        }

        return ['success' => $decoded];

    } elseif ($provider === 'pedro_perfumer') {
        $api_key = $user_settings['pedro_perfumer_api_key'] ?? '';
        if (empty($api_key)) {
            return ['error' => 'Pedro Perfumer API key is not set'];
        }

        $postFields = http_build_query([
            "prompt" => $prompt,
            "api_key" => $api_key
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://pedro.perfumersvault.com/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ["Content-Type: application/x-www-form-urlencoded"],
            CURLOPT_POSTFIELDS => $postFields
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return ['error' => 'Pedro Perfumer API request failed: ' . $error];
        }

        $data = json_decode($response, true);

        // Expecting: {"response":"..."}
        if (!$data || !is_array($data) || !isset($data['response'])) {
            error_log("Pedro Perfumer error: " . $response);
            return ['error' => 'Invalid response from Pedro Perfumer'];
        }

        $raw_response = $data['response'];

        // If response is already an array/object (not a string), use it directly
        if (is_array($raw_response)) {
            $decoded = $raw_response;
        } else {
            $cleaned = trim($raw_response);
            $cleaned = preg_replace('/^```(json)?/i', '', $cleaned);
            $cleaned = preg_replace('/```$/', '', $cleaned);
            $cleaned = preg_replace('/^[^{\[].*?({|\[)/s', '$1', $cleaned);
            $decoded = json_decode($cleaned, true);
        }

        // Determine type: ingredient, formula, general, replacements, or unknown
        $type = 'unknown';
        if (is_array($decoded)) {
            if (isset($decoded['formula']) || (isset($decoded[0]) && isset($decoded[0]['formula']))) {
                $type = 'formula';
            } elseif (isset($decoded['replacements']) && is_array($decoded['replacements'])) {
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
            return [
                'success' => $decoded,
                'type' => $type
            ];
        }

        // If not JSON/object, return as string with type unknown
        return [
            'success' => $raw_response,
            'type' => $type
        ];
    }

    return ['error' => 'Unsupported AI provider'];
}
?>
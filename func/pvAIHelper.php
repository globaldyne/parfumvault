<?php
/**
 * Generates a formula using AI based on the given provider and prompt.
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


function pvAIHelper($prompt) {

    global $user_settings, $system_settings;

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
    if (!in_array($provider, ['openai', 'google_gemini'])) {
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
                    ["role" => "system", "content" => "You are a perfumer AI that only responds with valid JSON arrays of ingredients."],
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
                    "parts" => [["text" => "$prompt\nOnly output JSON array."]]
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
    }

    return ['error' => 'Unsupported AI provider'];
}
?>
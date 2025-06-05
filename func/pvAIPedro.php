<?php
if (!defined('pvault_panel')){ die('Not Found');}

function pvAIPedroHelper($prompt, $user_settings) {
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
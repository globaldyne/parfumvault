<?php
//if (!defined('pvault_panel')){ die('Not Found');}

header('Content-Type: application/json');
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://pedro.perfumersvault.com/?status",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5
]);
$response = curl_exec($curl);
$error = curl_error($curl);
curl_close($curl);

if ($error) {
    echo json_encode(['status' => 'error', 'message' => $error]);
    exit;
}

echo $response;
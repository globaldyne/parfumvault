<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


$PVSCALE = $settings['pv_scale_host'];

if ($_GET['action'] == 'check_reload_signal'){
	$reloadSignal = file_get_contents($tmp_path.'reload_signal.txt');
	echo $reloadSignal;
	return;
}

if ($_GET['action'] == 'update_reload_signal'){
	file_put_contents($tmp_path.'reload_signal.txt', 'noreload');;
	return;
}

if ($_GET['action'] == 'version'){
	$url = "http://$PVSCALE/version";
	$response = file_get_contents($url);

	if ($response !== false) {
		$responseData = json_decode($response);
        if ($responseData !== null) {

			echo json_encode(['success' => true, 'data' => $responseData]);
			
		} else {
            echo json_encode(['success' => false, 'error' => 'Error decoding JSON response']);
        }
	} else {
		echo json_encode(['success' => false, 'error' => 'Error occurred getting version']);
	}
	return;
}


if ($_GET['action'] == 'send2PVScale') {
    $url = "http://$PVSCALE/api";
    $jsonData = file_get_contents('php://input');
    // Decode the JSON data
    $requestData = json_decode($jsonData);
    // Check if decoding was successful
    if ($requestData !== null) {
        // Prepare data for sending
        $postData = json_encode($requestData);
        // Initialize curl session
        $ch = curl_init();
        // Set curl options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Execute curl session
        $response = curl_exec($ch);
        // Check for errors
        if ($response === false) {
            echo json_encode(['success' => false, 'error' => 'Error sending data to the remote server: ' . curl_error($ch)]);
        } else {
            // Process the response
            $responseData = json_decode($response);
            if ($responseData !== null) {
                echo json_encode(['success' => true, 'message' => $responseData->message]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error decoding JSON response']);
            }
        }
        
        // Close curl session
        curl_close($ch);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error decoding JSON data']);
    }

    return;
}





?>
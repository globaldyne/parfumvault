<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


$PVSCALE = $settings['pv_scale_host'];


if ($_POST['action'] == 'update' ){
	
	if (!filter_var($_POST['pv_scale_host'], FILTER_VALIDATE_IP)) {
    	$result['error'] = "Scale IP is invalid";
		echo json_encode($result);
		return;
	}
	$pv_scale_enabled = (int)$_POST['enabled'];
	$q = mysqli_query($conn,"UPDATE settings SET pv_scale_enabled = '$pv_scale_enabled'");
	
	if($q){
		$result['success'] = "Settings updated";
	} else {
		$result['error'] = "Unable to update settings ".mysqli_error($conn);
	}
	
	echo json_encode($result);
	return;
}


if ($_POST['ping']){
	$pvScHost = $_POST['pv_scale_host'];
	$timeout = 30; // Timeout in seconds
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $pvScHost."/ping");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	$response = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	if ($http_code == 200 && $response == '{"response":"pong"}') {
		$sysResponse = file_get_contents("http://".$pvScHost."/sys");
		echo json_encode([
			'success' => true,
			'data' => $response,
			'sysData' => json_decode($sysResponse, true),
			'msg' => "Connection was successful"
			]);
	} else {
		echo json_encode(['success' => false, 'data' => $response]);
	}
	return;
}

if ($_GET['action'] == 'check_reload_signal'){
	$reloadSignal = file_get_contents($tmp_path.'reload_signal.txt');
	echo $reloadSignal;
	return;
}

if ($_GET['action'] == 'update_reload_signal'){
	file_put_contents($tmp_path.'reload_signal.txt', 'noreload');;
	return;
}

if ($_GET['action'] == 'firmwareCheck'){
	$url = "http://$PVSCALE/firmware/check";
	$response = file_get_contents($url);

	if ($response !== false) {
		$responseData = json_decode($response);
        if ($responseData !== null) {

			echo json_encode(['success' => true, 'response' => $responseData]);
			
		} else {
            echo json_encode(['success' => false, 'response' => 'Error decoding JSON response']);
        }
	} else {
		echo json_encode(['success' => false, 'response' => 'Error occurred getting version']);
	}
	return;
}

if ($_GET['action'] == 'firmwareUpdate'){
	$url = "http://$PVSCALE/firmware/update";
	$response = file_get_contents($url);

	if ($response !== false) {
		$responseData = json_decode($response);
        if ($responseData->success == true) {

			echo json_encode(['success' => true, 'response' => $responseData->message]);
			
		} else {
            echo json_encode(['success' => false, 'response' => $responseData->message]);
        }
	} else {
		echo json_encode(['success' => false, 'response' => $responseData->message]);
	}
	return;
}

if ($_GET['action'] == 'send2PVScale') {
    $url = "http://$PVSCALE/api";
    $jsonData = file_get_contents('php://input');
    $requestData = json_decode($jsonData);
    if ($requestData !== null) {
        $postData = json_encode($requestData);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if ($response === false) {
            echo json_encode(['success' => false, 'error' => 'Error sending data to the remote server: ' . curl_error($ch)]);
        } else {
            $responseData = json_decode($response);
            if ($responseData !== null) {
                echo json_encode(['success' => true, 'message' => $responseData->message]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error decoding remote response']);
            }
        }
        
        curl_close($ch);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error decoding JSON data']);
    }

    return;
}





?>
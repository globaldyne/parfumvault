<?php 
define('__ROOT__', dirname(dirname(dirname(__FILE__)))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if ($role !== 1){
    echo json_encode(['success' => false, 'error' => 'Not authorised']);
	return;
}
$PVSCALE = $user_settings['pv_scale_host'];


if ($_POST['action'] == 'update' ){
	
	$result = [];
	if (!filter_var($_POST['pv_scale_host'], FILTER_VALIDATE_IP)) {
		$result['error'] = "Scale IP is invalid";
		echo json_encode($result);
		return;
	}
		
	$pv_scale_enabled = isset($_POST["pv_scale_enabled"]) && $_POST["pv_scale_enabled"] === 'true' ? '1' : '0';
	
	$response = [];
	foreach ($_POST as $key => $value) {
		if ($key === 'action') {
			continue;
		}
	
		$key = mysqli_real_escape_string($conn, $key);
		$value = mysqli_real_escape_string($conn, $value);

		$stmt = $conn->prepare("SELECT COUNT(*) FROM user_settings WHERE key_name = ? AND owner_id = ?");
		$stmt->bind_param('si', $key, $userID);
		$stmt->execute();
		$stmt->bind_result($count);
		$stmt->fetch();
		$stmt->close();

		if ($count > 0) {
			$stmt = $conn->prepare("UPDATE user_settings SET value = ? WHERE key_name = ? AND owner_id = ?");
			$stmt->bind_param('ssi', $value, $key, $userID);
		} else {
			$stmt = $conn->prepare("INSERT INTO user_settings (key_name, value, owner_id) VALUES (?, ?, ?)");
			$stmt->bind_param('ssi', $key, $value, $userID);
		}

		if ($stmt->execute()) {
			$response["success"] = 'Settings updated';
		} else {
			$response["error"] = 'An error occurred: ' . $stmt->error;
		}
	}
	echo json_encode($response);
	return;

}


if ($_POST['ping']) {
	$pvScHost = $_POST['pv_scale_host'];
	$timeout = 30; // Timeout in seconds

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $pvScHost . "/ping");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	$response = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$curl_error = curl_error($ch);
	curl_close($ch);

	if ($http_code == 200 && $response == '{"response":"pong"}') {
		$sysResponse = file_get_contents("http://" . $pvScHost . "/sys");
		if ($sysResponse !== false) {
			echo json_encode([
				'success' => true,
				'data' => $response,
				'sysData' => json_decode($sysResponse, true),
				'msg' => "Connection was successful"
			]);
		} else {
			error_log("Error fetching system data from $pvScHost/sys");
			echo json_encode(['success' => false, 'error' => 'Error fetching system data']);
		}
	} else {
		error_log("Ping request to $pvScHost failed with HTTP code $http_code and response: $response. Curl error: $curl_error");
		echo json_encode(['success' => false, 'error' => 'Ping request failed', 'data' => $response]);
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

if ($_GET['action'] == 'screen'){
	$status = $_GET['status'];
	$url = "http://$PVSCALE/display/$status";
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

if ($_GET['action'] == 'completeSetup'){
	$url = "http://$PVSCALE/configure/sys/1";
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

?>
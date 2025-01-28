<?php 
define('__ROOT__', dirname(dirname(dirname(__FILE__)))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if ($role !== 1){
    echo json_encode(['success' => false, 'error' => 'Not authorised']);
	return;
}

//UPDATE BK PROVIDER
if ($_POST['action'] == 'googlebackups_update') {
    $response = [];
    if (empty($_POST['googlebackups_agent_srv_host']) || empty($_POST['googlebackups_credentials']) || empty($_POST['googlebackups_schedule']) || empty($_POST['googlebackups_description']) || empty($_POST['googlebackups_gdrive_name'])) {
        $response["error"] = 'Missing fields';
        echo json_encode($response);
        return;
    }
    
    foreach ($_POST as $key => $value) {
        if ($key === 'action') {
            continue;
        }

        $key = mysqli_real_escape_string($conn, $key);
        if ($key === 'googlebackups_credentials') {
            $value = json_encode(json_decode($value, true)); // Ensure it's properly formatted JSON
        } else {
            $value = mysqli_real_escape_string($conn, $value);
        }

        $stmt = $conn->prepare("SELECT COUNT(*) FROM integrations_settings WHERE key_name = ?");
        $stmt->bind_param('s', $key);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $stmt = $conn->prepare("UPDATE integrations_settings SET value = ? WHERE key_name = ?");
            $stmt->bind_param('ss', $value, $key);
        } else {
            $stmt = $conn->prepare("INSERT INTO integrations_settings (key_name, value) VALUES (?, ?)");
            $stmt->bind_param('ss', $key, $value);
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

$BKPOD = $integrations_settings['googlebackups_agent_srv_host'];

if($BKPOD == ''){
    echo json_encode(['success' => false, 'error' => 'Backup agent hostname or IP not set']);
    return;
}

if ($_GET['action'] == 'restart'){
	$url = "http://$BKPOD/restart";
	$response = file_get_contents($url);
	if ($response !== false) {
		echo json_encode(['success' => true]);
	} else {
		echo json_encode(['success' => false, 'error' => 'Error occurred while restarting service']);
	}
	return;
}


if ($_GET['action'] == 'version'){
	$url = "http://$BKPOD/version";
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


if ($_GET['action'] == 'createBackup') {
    $url = "http://$BKPOD/createBackup";
    $response = file_get_contents($url);
    
    if ($response !== false) {
        $responseData = json_decode($response);
        if ($responseData !== null) {
            echo json_encode(['success' => true, 'message' => $responseData->message]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error decoding JSON response']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error creating a backup']);
    }
    return;
}

if ($_GET['action'] == 'getRemoteBackups') {
    $url = "http://$BKPOD/getRemoteBackups";
    $response = file_get_contents($url);
    
    if ($response !== false) {
        $responseData = json_decode($response);
        if ($responseData !== null) {
            $formattedData = [];
            $numItems = count($responseData->data);
            for ($i = 0; $i < $numItems; $i++) {
                $item = $responseData->data[$i];
                $formattedData[] = [
                    'file_name' => $item->file_name,
                    'file_id' => $item->file_id,
                    'file_size' => $item->file_size,
                    'download_link' => $item->download_link
                ];
            }
            echo json_encode(['success' => true, 'data' => $formattedData]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error decoding JSON response']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error retrieving remote backups']);
    }
    return;
}

if ($_GET['action'] == 'deleteRemoteBackup') {
	$id = $_GET['id'];
    $url = "http://$BKPOD/deleteRemoteBackup/$id";
    $response = file_get_contents($url);
    
    if ($response !== false) {
        $responseData = json_decode($response);
        if ($responseData !== null) {
            echo json_encode(['success' => true, 'message' => $responseData->message]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error decoding JSON response']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error creating a backup']);
    }
    return;
}


?>
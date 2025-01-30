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
    if (empty($_POST['googlebackups_agent_srv_host']) || empty($_POST['googlebackups_credentials']) || empty($_POST['googlebackups_schedule']) || empty($_POST['googlebackups_description'])) {
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
        } elseif ($key === 'googlebackups_schedule') {
            $value = strtotime($value); // Convert to timestamp
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

$BKPOD_HOST = $integrations_settings['googlebackups_agent_srv_host'];
$BKPOD_PORT = $integrations_settings['googlebackups_agent_srv_port'];

if($BKPOD_HOST == ''){
    echo json_encode(['success' => false, 'error' => 'Backup agent hostname or IP not set']);
    return;
}

if($BKPOD_PORT == ''){
    echo json_encode(['success' => false, 'error' => 'Backup agent TCP port not set']);
    return;
}
$BKPOD = $BKPOD_HOST . ':' . $BKPOD_PORT;

if ($_GET['action'] == 'info'){
	$url = "http://$BKPOD/info";
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
                if ($item->size > 0) {
                    $formattedData[] = [
                        'name' => $item->name,
                        'id' => $item->id,
                        'size' => $item->size,
                        'createdTime' => $item->createdTime,
                        'DownloadLink' => $item->webViewLink
                    ];
                }
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

if ($_GET['action'] == 'ping') {
    $url = "http://$BKPOD/ping";
    $response = file_get_contents($url);
    
    if ($response !== false) {
        $responseData = json_decode($response);
        if ($responseData !== null) {
            echo '<i class="fa-solid fa-circle-check mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Service is running"></i>';
        } else {
            echo '<i class="fa-solid fa-circle-xmark mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Error decoding JSON response"></i>';
        }
    } else {
        echo '<i class="fa-solid fa-circle-xmark mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Error connecting to the agent"></i>';
    }
    return;
}

?>
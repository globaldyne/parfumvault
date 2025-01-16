<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if ($role !== 1){
    echo json_encode(['success' => false, 'error' => 'Not authorised']);
	return;
}

$BKPOD = $settings['bk_srv_host'];

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
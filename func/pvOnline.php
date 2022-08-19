<?php 

if (!defined('pvault_panel')){ die('Not Found');}

function pvOnlineStats($api, $s){
	$jAPI = $api.'?do=getStats';
	if($jData = json_decode(file_get_contents($jAPI),true)){
		return $jData[$s];
	}else{
		return 'Connection failed';
	}
}

function pvOnlineValAcc($api, $apiUser, $apiPass, $ver){
	$jAPI = $api.'?username='.$apiUser.'&password='.$apiPass.'&login=1&ver='.$ver;
	$jData = json_decode(file_get_contents($jAPI),true);

	return $jData['auth'];
}

function pvUploadData($pvOnlineAPI, $data){
	$ch = curl_init($pvOnlineAPI);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
}

function pvPost($url, $data){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    return $response = curl_exec($curl);
}
?>
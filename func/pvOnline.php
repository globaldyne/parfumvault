<?php 

if (!defined('pvault_panel')){ die('Not Found');}

function pvOnlineStats($api, $apiUser, $apiPass, $s){
	$jAPI = $api.'?username='.$apiUser.'&password='.$apiPass.'&do=count';
	$jData = json_decode(file_get_contents($jAPI),true);
	return $jData['count'][0][$s];
}

?>
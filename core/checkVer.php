<?php 

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/func/pvFileGet.php');

$app_ver = $_GET['app_ver'];
if($app_ver){
	$githubVer = 'https://raw.githubusercontent.com/globaldyne/parfumvault/master/VERSION.md';
	$githubREL = 'https://raw.githubusercontent.com/globaldyne/parfumvault/master/releasenotes.md';

	$docUrl = 'https://www.jbparfum.com/knowledge-base/how-to-update-pv-to-its-latest-version/';
	$data = trim(pv_file_get_contents($githubVer));
	$gitHubRep = 'https://github.com/globaldyne/parfumvault/archive/refs/tags/v'.$data.'.zip';

	if($app_ver < $data){
		if(file_exists('/config/.DOCKER') == TRUE){
			$response["success"] = '<strong>New <a href="'.$gitHubRep.'" target="_blank">version ('.$data.')</a> is availale!</strong> Please refer <a href="'.$docUrl.'" target="_blank">here</a> for update instructions.';
		}else{
			$response["success"] = '<strong>New <a href="'.$gitHubRep.'" target="_blank">version ('.$data.')</a> is availale!</strong> <a href="#" data-toggle="modal" data-target="#sysUpgradeDialog" data-ver="'.$githubVer.'">Upgrade available.</a>';
		}
	}
}
	
	echo json_encode($response);
	return;
?>

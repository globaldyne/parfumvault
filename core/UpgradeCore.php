<?php 
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/func/pvFileGet.php');
require_once(__ROOT__.'/func/pvCopy.php');		

$githubVer = 'https://raw.githubusercontent.com/globaldyne/parfumvault/master/VERSION.md';
$data = trim(pv_file_get_contents($githubVer));
if(trim(file_get_contents(__ROOT__.'/VERSION.md')) > $data){
	$response["error"] = "PV is already in its latest version";
	echo json_encode($response);
	return;
}



if (file_exists(__ROOT__.'/tmp/') === FALSE) {
   	mkdir(__ROOT__.'/tmp/', 0740, true);
}

$gitHubRep = 'https://github.com/globaldyne/parfumvault/archive/refs/tags/v'.$data.'.zip';
$tmpData = __ROOT__.'/tmp/'.$data.'.zip';
	
if (!is_writable(__ROOT__.'/VERSION.md')) {
	$response["error"] = "Unable to update as home directory its not writable. Please make sure you have write permissions on your server.";
	echo json_encode($response);
	return;
}

file_put_contents($tmpData, fopen($gitHubRep, 'r'));
$zip = new ZipArchive;
$res = $zip->open($tmpData);
		
if ($res === TRUE) {
	$zip->extractTo(__ROOT__.'/tmp/CoreUpgrade/');
	$zip->close();
	pvCopy(__ROOT__.'/tmp/CoreUpgrade/parfumvault-'.$data, __ROOT__.'/');
			
	$r = '<div class="alert alert-success"></div>';
	$response["success"] = "Update complete. Please refresh this page.";
	echo json_encode($response);
			
}else {
	$response["error"] = "Something went wrong. Check server logs";
	echo json_encode($response);	
}

return;
?>

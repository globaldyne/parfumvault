<?php
//if (!defined('pvault_panel')){ die('Not Found');}

function pv_file_get_contents($url){

	$arrContextOptions=array(
			"ssl"=>array(
			"verify_peer"=>false,
			"verify_peer_name"=>false,
		),
	);
	
	return file_get_contents($url, false, stream_context_create($arrContextOptions));
}
?>
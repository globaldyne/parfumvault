<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function checkVer($app_ver) {
	if($app_ver){
		$githubVer = 'https://raw.githubusercontent.com/globaldyne/parfumvault/master/VERSION.md';
		$gitHubRep = 'https://www.jbparfum.com/features/';
		
		$data = trim(pv_file_get_contents($githubVer));
		if($app_ver < $data){	
			echo '<div class="alert alert-info alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>New <a href="'.$gitHubRep.'" target="_blanc">version ('.$data.')</a> is availale!</strong></div>';
		}
	}
	
}
?>

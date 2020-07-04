<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function checkVer($ver) {
	$gitHubRep = 'https://www.jbparfum.com/';
	$githubVer = $gitHubRep.'proVERSION.md';
	
	$data = trim(file_get_contents($githubVer));
	if($ver < $data){	
		echo '<div class="alert alert-info alert-dismissible">
  		<strong>New <a href="'.$gitHubRep.'pro" target="_blanc">version ('.$data.')</a> availale!</strong>
		</div>';
	}

}
?>
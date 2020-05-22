<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<?php

function checkVer($ver) {
	$githubVer = 'https://raw.githubusercontent.com/globaldyne/parfumvault/master/VERSION.md';
	$changeLog = 'https://raw.githubusercontent.com/globaldyne/parfumvault/master/CHANGELOG.md';
	$gitHubRep = 'https://github.com/globaldyne/parfumvault';
	
	$data = file_get_contents($githubVer);
	if($ver < $data){	
		echo '<div class="alert alert-info alert-dismissible">
  		<strong>New <a href="'.$gitHubRep.'" target="_blanc">version</a> availale!</strong> See <a href="'.$changeLog.'" target="_blanc" >CHANGELOG</a> for changes!
		</div>';
	}

}
?>
<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function checkVer($ver) {
	$githubVer = 'https://raw.githubusercontent.com/globaldyne/parfumvault/master/VERSION.md';
	$gitHubRep = 'https://www.jbparfum.com/features/';
	
	$data = trim(file_get_contents($githubVer));
	if($ver < $data){	
		echo '<div class="alert alert-info alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>New <a href="'.$gitHubRep.'" target="_blanc">version ('.$data.')</a> is availale!</strong>
		</div>';
	}

}
?>

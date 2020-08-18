<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function checkVer($app_ver, $db_ver) {
	if($app_ver){
		$githubVer = 'https://raw.githubusercontent.com/globaldyne/parfumvault/master/VERSION.md';
		$gitHubRep = 'https://www.jbparfum.com/features/';
		
		$data = trim(pv_file_get_contents($githubVer));
		if($app_ver < $data){	
			echo '<div class="alert alert-info alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
			<strong>New <a href="'.$gitHubRep.'" target="_blanc">version ('.$data.')</a> is availale!</strong>
			</div>';
		}
	}elseif($db_ver){
		$data = trim(file_get_contents('db/schema.ver'));
		if($db_ver < $data){	
			echo '<div class="alert alert-warning alert-dismissible"><strong>Your database schema needs to be updated. Please <a href="pages/maintenance.php?do=backupDB">backup</a> your database first and then click <a href="javascript:updateDB()">here to update the db schema.</a></strong></div>';
		}
	}
}
?>

<?php

function prepare($from, $to){
	
	$msg = "<p>To upgrade to version <strong>$to</strong>, we need to pre-configure your database first, as this version isn't compatible with the current schema <strong>($from)</strong>.</p>";
		
	$msg.= "<p>We strongly recommend to take a back-up of your database before proceed or use this version in a fresh installation.</p>";
	
	$msg.= "<p>As this version contains significant changes regarding user management, please be aware, that <strong>ALL users</strong> currently configured will be automatically removed and the system will force you to create a new one which you can now connect to PV Online.</p>";
	
	$msg.= "<p>If you already have a PV Online account, please use the same email address and password, this will allow the system to use PV Online automatically.</p>";
	$msg.= "<p>If you don't wanna use PV Online, just untick the account creation option when you creating your local user or from the settings menu, in PV Online, untick the Enable Service box.</p>";
	
	$msg.= "<p><strong>IMPORTANT:</strong> After you upgrade, you will have to use your email to login instead of a username</p>";
	
	$msg.= "<p><hr /></p>";
	$msg.= '<p>For more details please check our KB <a href="https://www.jbparfum.com/knowledge-base/upgrading-to-version-4-8/" target="_blank">article</a></p>';
	
	
	
	return $msg;
}

if($_POST['action'] == 'upgrade'){
		define('pvault_panel', TRUE);
		
		require_once(dirname(dirname(dirname(__FILE__))).'/inc/config.php');
		require_once(dirname(dirname(dirname(__FILE__))).'/inc/opendb.php');
		
		$version = $_POST['version'];
		
		$q = mysqli_query($conn, "ALTER TABLE users DROP username, DROP avatar");
		$q.= mysqli_query($conn, "TRUNCATE users");
		$q.= mysqli_query($conn, "ALTER TABLE pv_online DROP id, DROP email, DROP password");
		$q.= mysqli_query($conn, "ALTER TABLE pv_meta DROP id");
		$q.= mysqli_query($conn, "ALTER TABLE suppliers CHANGE stock stock INT(11) NOT NULL DEFAULT '0'");
		$q.= mysqli_query($conn, "ALTER TABLE ingredients ADD rdi INT NOT NULL DEFAULT '0' AFTER appearance"); 
		$q.= mysqli_query($conn, "ALTER TABLE `bottles` DROP `photo`"); 
		$q.= mysqli_query($conn, "ALTER TABLE `bottles` ADD `pieces` INT NOT NULL DEFAULT '0' AFTER `notes`"); 

		$q.= mysqli_query($conn, "ALTER TABLE `lids` DROP `photo`"); 
		$q.= mysqli_query($conn, "ALTER TABLE `lids` ADD `pieces` INT NOT NULL DEFAULT '0' AFTER `supplier_link`"); 


		$q.= mysqli_query($conn, "UPDATE pv_meta SET schema_ver = '$version', app_ver = '$version' ");

		if($q){
			$response['success'] = "Upgrade complete";
		}else{
			$response['error'] = "Something went wrong: ".mysqli_error($conn);
		}
		
		echo json_encode($response);
		
		return;

}
	


?>

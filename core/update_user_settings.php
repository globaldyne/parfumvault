<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$pref_storage = $settings['user_pref_eng'];

if( $_GET['set'] && $_GET['action'] == 'save'){
	$pref_name = $_GET['set'];
	$pref_tab = $_GET['tableId'];
	switch ($pref_storage) {
		case 'DB':
		case '2':
			$data = serialize($_POST);
			mysqli_query($conn, "INSERT INTO user_prefs (pref_name, pref_data, pref_tab, owner_id) VALUES ('".$pref_name."', '".$data."', '".$pref_tab."', '".$userID."') ON DUPLICATE KEY UPDATE pref_name = VALUES(pref_name), pref_data = VALUES(pref_data), pref_tab = VALUES(pref_tab)");
			break;
			
		case '1':
    	case 'PHP_SESS':
		default:
			$_SESSION["user_prefs"]["$pref_name"] = $_POST;
	   		break;
		   
	} 
	return;
}

if($a = $_GET['set'] && $_GET['action'] == 'load'){
	$pref_name = $_GET['set'];
	$pref_tab = $_GET['tableId'];

	switch ($pref_storage) {
		case 'DB':
		case '2':
			$data  = mysqli_fetch_array(mysqli_query($conn, "SELECT pref_data FROM user_prefs WHERE pref_name = '".$pref_name."' AND owner_id = '".$userID."' AND pref_tab = '".$pref_tab."' "));
			
			echo json_encode(unserialize($data['pref_data']));
			break;
		
    	case 'PHP_SESS':
		case '1':
		default:
			echo json_encode($_SESSION["user_prefs"]["$pref_name"]) ;
	   		break;
		   
	}
	return;
}

?>

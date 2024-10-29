<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');

$sysLogs = getenv('SYS_LOGS') ?: FALSE;

if ($sysLogs === FALSE) {
	
	$response["error"] = 'Function is disabled';
	echo json_encode($response);
	return;	
	
}

$logFile = $_GET['log'];


switch ($logFile) {
	case 'access':
	echo file_get_contents('/tmp/nginx-access.log');	
	break;
		
    case 'error':
	default:
	echo file_get_contents('/tmp/nginx-error.log');	
	break;
		   
}

?>
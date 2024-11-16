<?php

error_reporting(E_ERROR);

if (!defined('pvault_panel')){ die('Not Found');}  
define('__ROOT__', dirname(__FILE__)); 

if(strtoupper(getenv('PLATFORM')) === "CLOUD"){
	
	if(!getenv('DB_HOST') || !getenv('DB_USER') || !getenv('DB_PASS') || !getenv('DB_NAME')){
		$error_msg = 'Required parameters not found. Please make sure your provided all the required variables as per <a href="https://www.perfumersvault.com/knowledge-base/howto-docker/" target="_blank">documentation</a>';
		require_once(__ROOT__.'/pages/error.php');
		exit;
	}

	$dbhost = getenv('DB_HOST');
	$dbuser = getenv('DB_USER');
	$dbpass = getenv('DB_PASS');
	$dbname = getenv('DB_NAME');

	$tmp_path = getenv('TMP_PATH') ?: "/tmp/";
	$allowed_ext = getenv('FILE_EXT') ?: "pdf, doc, docx, xls, csv, xlsx, png, jpg, jpeg, gif";
	$max_filesize = getenv('MAX_FILE_SIZE') ?: "4194304";
	$bkparams =  getenv('DB_BACKUP_PARAMETERS') ?: '--column-statistics=1';
    $sysLogsEnabled = strtoupper(getenv('SYS_LOGS')) === 'ENABLED' || getenv('SYS_LOGS') === '1';
	$session_timeout = getenv('SYS_TIMEOUT') ?: 1800;
	
	$conn = dbConnect($dbhost, $dbuser, $dbpass, $dbname);

}elseif(file_exists(__ROOT__.'/inc/config.php') == TRUE) {
	require_once(__ROOT__.'/inc/config.php');
	$conn = dbConnect($dbhost, $dbuser, $dbpass, $dbname);
}


function dbConnect(string $dbhost, string $dbuser, string $dbpass, string $dbname) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
        mysqli_set_charset($conn, "utf8");
        return $conn;
    } catch (mysqli_sql_exception $e) {
		$error_msg = "Database connection error: " . $e->getMessage();
        require_once(__ROOT__.'/pages/error.php');
		error_log($error_msg);
        return false; // Return false on failure
    }
}

?>

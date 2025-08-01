<?php

error_reporting(E_ERROR);

if (!defined('pvault_panel')){ die('Not Found');}  
define('__ROOT__', dirname(__FILE__)); 

if(getenv('PLATFORM') === "CLOUD"){
	
	if(!getenv('DB_HOST') || !getenv('DB_USER') || !getenv('DB_PASS') || !getenv('DB_NAME')){
		$error_msg = 'Required parameters not found. Please make sure your provided all the required variables as per <a href="https://www.perfumersvault.com/kb/howto-docker/" target="_blank">documentation</a>';
		require_once(__ROOT__.'/pages/error.php');
		exit;
	}

	$dbhost = getenv('DB_HOST');
	$dbuser = getenv('DB_USER');
	$dbpass = getenv('DB_PASS');
	$dbname = getenv('DB_NAME');

	$tmp_path = getenv('TMP_PATH') ?: "/tmp/";
	$allowed_ext = getenv('FILE_EXT') ?: "pdf, doc, docx, xls, csv, xlsx, png, jpg, jpeg, gif";
	$bkparams =  getenv('DB_BACKUP_PARAMETERS') ?: '--single-transaction --routines --triggers';
	$sysLogsEnabled = getenv('SYS_LOGS') === 'ENABLED' || getenv('SYS_LOGS') === '1';
	$session_timeout = getenv('SYS_TIMEOUT') ?: 1800;
	$disable_updates = getenv('DISABLE_UPDATES');

	$upload_max_filesize = getenv('UPLOAD_MAX_FILESIZE') ?: "500M";
	$post_max_size = getenv('POST_MAX_SIZE') ?: "500M";
	$memory_limit = getenv('MEMORY_LIMIT') ?: "500M";

	ini_set('upload_max_filesize', $upload_max_filesize);
	ini_set('post_max_size', $post_max_size);
	ini_set('memory_limit', $memory_limit);


	$conn = dbConnect($dbhost, $dbuser, $dbpass, $dbname);

}elseif(file_exists(__ROOT__.'/inc/config.php') === TRUE) {
	require_once(__ROOT__.'/inc/config.php');
	$conn = dbConnect($dbhost, $dbuser, $dbpass, $dbname);
}else{
	error_log("Configuration file not found.");
	$error_msg = 'Configuration file not found. Please make sure you have installed the application correctly.';
	require_once(__ROOT__.'/pages/error.php');
	exit;
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

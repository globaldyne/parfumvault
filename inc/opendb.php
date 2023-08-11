<?php 
if (!defined('pvault_panel')){ die('Not Found');}  
define('__ROOT__', dirname(__FILE__)); 

if(strtoupper(getenv('PLATFORM')) === "CLOUD"){
	
	if(!getenv('DB_HOST') || !getenv('DB_USER') || !getenv('DB_PASS') || !getenv('DB_NAME')){
		echo 'Required parameters not found. Please make sure your provided all the required variables as per <a href="https://www.perfumersvault.com/knowledge-base/howto-docker/" target="_blank">documentation</a>';
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
	
	$conn = dbConnect($dbhost, $dbuser, $dbpass, $dbname);

}elseif(file_exists(__ROOT__.'/inc/config.php') == TRUE) {
	require_once(__ROOT__.'/inc/config.php');
	$conn = dbConnect($dbhost, $dbuser, $dbpass, $dbname);
}



function dbConnect($dbhost, $dbuser, $dbpass, $dbname){
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Unable to connect to '.$dbname.' database on '.	$dbhost.' host. Please make sure the database exists and user '.$dbuser.' has full permissions on it.');
	mysqli_select_db($conn, $dbname);
	mysqli_set_charset($conn, "utf8");
	return $conn;
}
?>
<?php 
if (!defined('pvault_panel')){ die('Not Found');}  
define('__ROOT__', dirname(__FILE__)); 

if(file_exists(__ROOT__.'/inc/config.php') == FALSE && getenv('DB_HOST') && getenv('DB_USER') && getenv('DB_PASS') && getenv('DB_NAME')){

	$dbhost = getenv('DB_HOST');
	$dbuser = getenv('DB_USER');
	$dbpass = getenv('DB_PASS');
	$dbname = getenv('DB_NAME');
	
	$uploads_path = getenv('UPLOADS_PATH') ?: "uploads/";
	$tmp_path = getenv('TMP_PATH') ?: "/tmp/";
	$allowed_ext = getenv('FILE_EXT') ?: "pdf, doc, docx, xls, csv, xlsx, png, jpg, jpeg, gif";
	$max_filesize = getenv('MAX_FILE_SIZE') ?: "4194304";

}elseif(file_exists(__ROOT__.'/inc/config.php') == TRUE) {
	require_once(__ROOT__.'/inc/config.php');
}


$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Error connecting to database ');
mysqli_select_db($conn, $dbname);
mysqli_set_charset($conn, "utf8");

?>
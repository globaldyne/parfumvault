<?php 
if (!defined('pvault_panel')){ die('Not Found');}  

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Error connecting to database');
mysqli_select_db($conn, $dbname);

?>
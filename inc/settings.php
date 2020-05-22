<?php
if (!defined('pvault_panel')){ die('Not Found');}

$settings = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM settings")); 
$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE id = '$_SESSION[userID]'")); 
?>
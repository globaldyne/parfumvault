<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function apiCheckAuth($username, $password ,$dbhost, $dbuser, $dbpass, $dbname){
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Error connecting to database');
        $row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM users WHERE username='$username' AND password=PASSWORD('$password')"));
        return $row['id'];

}
?>

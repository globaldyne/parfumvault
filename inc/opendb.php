<?php

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Error connecting to database');
mysqli_select_db($conn, $dbname);
//mysqli_set_charset($conn, 'utf8');

?>
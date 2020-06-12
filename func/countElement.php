<?php
if (!defined('pvault_panel')){ die('Not Found');}

function countElement($element = 'formulas  GROUP BY name' ,$conn){
	//$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Error connecting to database');
        $sql = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM $element")); 
        return $sql;

}
?>

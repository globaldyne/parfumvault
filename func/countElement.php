<?php
if (!defined('pvault_panel')){ die('Not Found');}

function countElement($element = 'formulas  GROUP BY name' ,$conn){
        $sql = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM $element")); 
        return $sql;
}
?>

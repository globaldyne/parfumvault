<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function checkDupes($array) {
   return count($array) !== count(array_unique($array));
}
?>

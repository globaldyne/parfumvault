<?php
function checkDupes($array) {
   return count($array) !== count(array_unique($array));
}
?>
<?php
if (!defined('pvault_panel')){ die('Not Found');}

function ml2L($ml, $s=2, $mUnit){
    // Ensure $s is always an integer
    $s = is_int($s) ? $s : 2;

    if($ml > 1000){
        $conv = number_format($ml/1000, $s) .'L';
    }else{
        $conv = number_format($ml, $s).$mUnit;
    }
    return $conv;
}
?>
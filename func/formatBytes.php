<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function formatBytes($size, $precision = 2){
    if (is_string($size)) {
        $size = strtolower($size);
        $units = array('b' => 0, 'kb' => 1, 'mb' => 2, 'gb' => 3, 'tb' => 4);
        $number = floatval($size);
        $unit = preg_replace('/[^a-z]/', '', $size);
        $size = $number * pow(1024, $units[$unit]);
    } else {
        $size = floatval($size);
    }
    $base = log($size, 1024);
    $suffixes = array('', 'KB', 'MB', 'GB', 'TB');   

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}

?>
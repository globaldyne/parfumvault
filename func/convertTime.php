<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function convertTime($seconds) {
    if (!is_numeric($seconds) || $seconds < 0) {
        return "Invalid input. Please enter a non-negative number.";
    }

    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);
    $remainingMinutes = $minutes % 60;

    return [
        'hours' => $hours,
        'minutes' => $remainingMinutes,
    ];
}

?>
<?php if (!defined('pvault_panel')){ die('Not Found');}?>

<?php
function formatBytes($size, $precision = 2){
    $base = log($size, 1024);
    $suffixes = array('', 'KB', 'MB', 'GB', 'TB');   

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}

?>
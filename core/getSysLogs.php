<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');


$sysLogs = strtoupper(getenv('SYS_LOGS')) === 'ENABLED' || getenv('SYS_LOGS') === '1';

if (!$sysLogs) {
    $response["error"] = 'Function is disabled';
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    return;
}

$logFile = $_GET['log'] ?? 'access';
$logPath = match ($logFile) {
    'access' => '/tmp/nginx-access.log',
    'error' => '/tmp/nginx-error.log',
    'fpm' => '/tmp/php-fpm-www-error.log',
    default => '/tmp/nginx-access.log',
};

if (file_exists($logPath) && is_readable($logPath)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo file_get_contents($logPath);
} else {
    $response["error"] = 'Log file not found or not readable';
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
}

return;


?>
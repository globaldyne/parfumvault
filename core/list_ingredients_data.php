<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');

// Fetch allowed providers from JSON files in the modules/providers/ directory
$providerFiles = glob(__ROOT__.'/modules/providers/*.json');
$allowedProviders = array_map(function($file) {
    return basename($file, '.json');
}, $providerFiles);

$provider = isset($_POST['provider']) ? trim($_POST['provider']) : 'local';

// Validate and sanitize the provider input
if (!in_array($provider, $allowedProviders)) {
    $provider = 'local';
}

$providerFile = __ROOT__.'/modules/providers/'.$provider.'.php';

if (file_exists($providerFile)) {
    require_once($providerFile);
} else {
    // Handle the error if the file does not exist
	error_log("Provider file not found: $providerFile");
    die('Provider file not found.');
}
?>
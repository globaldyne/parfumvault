<?php 

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/func/pvFileGet.php');

// Sanitize input
$app_ver = filter_input(INPUT_GET, 'app_ver', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($app_ver) {
    $githubVerUrl = 'https://raw.githubusercontent.com/globaldyne/parfumvault/master/VERSION.md';
    $githubRelUrl = 'https://raw.githubusercontent.com/globaldyne/parfumvault/master/releasenotes.md';

    $docUrl = 'https://www.perfumersvault.com/knowledge-base/how-to-update-pv-to-its-latest-version/';
    
    // Fetch the latest version from GitHub
    $data = trim(pv_file_get_contents($githubVerUrl));

    if ($data === false) {
        $response["error"] = 'Failed to retrieve version information. Please try again later.';
        echo json_encode($response);
        http_response_code(500);
        exit;
    }

    $gitHubRep = 'https://github.com/globaldyne/parfumvault/archive/refs/tags/v'.$data.'.zip';

    if (version_compare($app_ver, $data, '<')) {
        $response["success"] = '<strong>New <a href="'.$gitHubRep.'" target="_blank">version ('.$data.')</a> is available!</strong>';
        $response["success"] .= file_exists('/config/.DOCKER') === TRUE || getenv('PLATFORM') === 'CLOUD'
            ? ' Please refer <a href="'.$docUrl.'" target="_blank">here</a> for update instructions.'
            : ' <a href="#" data-bs-toggle="modal" data-bs-target="#sysUpgradeDialog" data-ver="'.$githubVerUrl.'">Upgrade available.</a>';
        echo json_encode($response);
    } else {
        $response["info"] = 'No updates available.';
        echo json_encode($response);
    }
} else {
    $response["error"] = 'Invalid version provided.';
    echo json_encode($response);
    http_response_code(400);
}
?>

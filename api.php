<?php
define('pvault_panel', TRUE);
define('__ROOT__', dirname(__FILE__));
define('LOG_PATH', '/tmp/logs/');
define('DEFAULT_IMAGE', __ROOT__ . '/img/pv_molecule.png');
define('LOG_API_FILE', 'pv-api.log');

require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/rgbToHex.php');
// Load settings
$settingsQuery = "SELECT * FROM settings";
$settings = mysqli_fetch_array(mysqli_query($conn, $settingsQuery));
$defCatClass = $settings['defCatClass'];
$defImage = base64_encode(file_get_contents(DEFAULT_IMAGE));

// Create log directory and file
if (!file_exists(LOG_PATH)) {
    if (!mkdir(LOG_PATH, 0740, true)) {
        die(json_encode(['error' => 'Failed to create log directory']));
    }
}
if (!file_exists(LOG_PATH . LOG_API_FILE)) {
    if (!touch(LOG_PATH . LOG_API_FILE)) {
        die(json_encode(['error' => 'Failed to create log file']));
    }
}

// Log incoming requests
$reqDump = print_r($_REQUEST, true);
file_put_contents(LOG_PATH . LOG_API_FILE, $reqDump, FILE_APPEND);

/**
 * Check API authentication
 */
function apiCheckAuth($key, $conn) {
    global $userID;
    $query = "SELECT id FROM users WHERE isAPIActive = '1' AND isActive = '1' AND API_key = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $key);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    mysqli_stmt_bind_result($stmt, $id);
    if (mysqli_stmt_fetch($stmt)) {
        $userID = $id;
    }
    return mysqli_stmt_num_rows($stmt) > 0;
}

/**
 * Validate API Key and Execute Request
 */
function validateKeyAndExecute($conn, $key, $callback) {
    if (!apiCheckAuth($key, $conn)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'Auth failed']);
        return false;
    }
    return $callback();
}

// Validate required parameters
$key = $_REQUEST['key'] ?? null;
$do = strtolower($_REQUEST['do'] ?? '');
$type = $_REQUEST['type'] ?? null;


if ($key && $do === 'auth') {
    if(!apiCheckAuth($key, $conn)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['type'=>'auth','status' => 'failed']);
        return;
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['type'=>'auth','status' => 'success']);
        return;
    }
}

$validEndpoints = [
    'upload' => ['formula', 'ingredients'],
    'get' => ['formulas', 'ingredients', 'categories', 'suppliers', 'documents', 'ifra'],
    'manage' => ['makeformula']
];

// Function to return valid endpoints
function getValidEndpoints($endpoints) {
    $formatted = [];
    foreach ($endpoints as $do => $types) {
        foreach ($types as $type) {
            $formatted[] = ['method' => 'POST','do' => $do, 'type' => $type];
        }
    }
    return $formatted;
}

// Route requests
switch ($do) {
    case 'upload':
        if ($type === 'formulas') {
            validateKeyAndExecute($conn, $key, function () {
                require_once(__ROOT__ . '/api-functions/formulas_upload.php');
            });
        } elseif ($type === 'ingredients') {
            validateKeyAndExecute($conn, $key, function () {
                require_once(__ROOT__ . '/api-functions/ingredients_upload.php');
            });
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 'Invalid type for upload',
                'valid_endpoints' => getValidEndpoints($validEndpoints)
            ], JSON_PRETTY_PRINT);
            return;
        }
        break;

    case 'get':
        $apiFileMap = [
            'formulas' => '/api-functions/formulas_get.php',
            'ingredients' => '/api-functions/ingredients_get.php',
            'categories' => '/api-functions/categories_get.php',
            'suppliers' => '/api-functions/suppliers_get.php',
            'documents' => '/api-functions/documents_get.php',
            'ifra' => '/api-functions/ifra_get.php'
        ];
        if (isset($apiFileMap[$type])) {
            validateKeyAndExecute($conn, $key, function () use ($type, $apiFileMap) {
                require_once(__ROOT__ . $apiFileMap[$type]);
            });
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 'Invalid type for get',
                'valid_endpoints' => getValidEndpoints($validEndpoints)
            ], JSON_PRETTY_PRINT);
            return;
        }
        break;

    case 'manage':
        if ($type === 'makeformula') {
            validateKeyAndExecute($conn, $key, function () {
                require_once(__ROOT__ . '/api-functions/manage_makeformula.php');
            });
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 'Invalid type for manage',
                'valid_endpoints' => getValidEndpoints($validEndpoints)
            ], JSON_PRETTY_PRINT);
            return;
        }
        break;

    default:
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'Unknown action',
            'valid_endpoints' => getValidEndpoints($validEndpoints)
        ], JSON_PRETTY_PRINT);
        return;
}
?>

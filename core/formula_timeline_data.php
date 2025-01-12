<?php

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__ . '/inc/sec.php');
require_once(__ROOT__ . '/inc/opendb.php');
require_once(__ROOT__ . '/inc/settings.php');

// Initialize variables
$response = [
    "draw" => (int)($_POST['draw'] ?? 0),
    "recordsTotal" => 0,
    "recordsFiltered" => 0,
    "data" => []
];


// Validate the requested ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $response['error'] = "Invalid or missing ID.";
    echo json_encode($response);
    return;
}

$metaQuery = "SELECT id, name FROM formulasMetaData WHERE id = '$id' AND owner_id = '$userID'";
$historyQuery = "SELECT * FROM formula_history WHERE fid = '$id' AND owner_id = '$userID' ORDER BY date_time DESC";

// Fetch metadata
$metaResult = mysqli_query($conn, $metaQuery);
$meta = mysqli_fetch_assoc($metaResult);

if (!$meta) {
    $response['error'] = "Requested ID is not valid or you lack access permissions.";
    echo json_encode($response);
    return;
}

// Fetch formula history
$historyResult = mysqli_query($conn, $historyQuery);
$historyData = [];
while ($row = mysqli_fetch_assoc($historyResult)) {
    $historyData[] = [
        "id" => (int)$row['id'],
        "fid" => (string)$row['fid'],
        "ing_id" => (int)$row['ing_id'],
        "change_made" => (string)$row['change_made'],
        "date_time" => (string)$row['date_time'],
        "user" => (string)$row['user'],
    ];
}

// Populate response
$response['recordsTotal'] = count($historyData);
$response['recordsFiltered'] = count($historyData);
$response['data'] = $historyData;
//$response['debug'] = $historyQuery;

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;

?>

<?php
define('__ROOT__', dirname(__DIR__));

require_once(__ROOT__ . '/inc/sec.php');
require_once(__ROOT__ . '/inc/opendb.php');

$response = ['data' => []];

$cat_q = mysqli_query($conn, "SELECT id, name, notes, image FROM ingProfiles");

if ($cat_q) {
    while ($profs_res = mysqli_fetch_assoc($cat_q)) {
        $response['data'][] = [
            'id' => (int)$profs_res['id'],
            'name' => (string)$profs_res['name'],
            'notes' => (string)$profs_res['notes'] ?: 'N/A',
            'image' => (string)$profs_res['image'] ?: null,
        ];
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);


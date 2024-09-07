<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$q = mysqli_query($conn, "SELECT price, stock, size FROM suppliers WHERE stock <> ''");

$data = [];
$w = [];

while ($res = mysqli_fetch_assoc($q)) {
    $data[] = $res;
    $w[] = $res['price'] * $res['stock'] / $res['size'];
}

$r = [
    'ingredients' => [
        'total_worth' => number_format(array_sum($w), 2)
    ],
    'currency' => (string)$settings['currency']
];

$response = [
    'data' => !empty($data) ? [$r] : []
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

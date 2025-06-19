<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/ml2L.php');
require_once(__ROOT__.'/func/countElement.php');


$meta = isset($_GET['meta']) ? (int)$_GET['meta'] : 0;
$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;

$order_by = isset($_POST['order_by']) ? mysqli_real_escape_string($conn, $_POST['order_by']) : 'name';
$order_as = isset($_POST['order_as']) ? mysqli_real_escape_string($conn, $_POST['order_as']) : 'ASC';
$extra = "ORDER BY toAdd DESC, $order_by $order_as";

$search = isset($_POST['search']['value']) ? trim(mysqli_real_escape_string($conn, $_POST['search']['value'])) : '';
$table = "makeFormula";
$fid = isset($_GET['fid']) ? mysqli_real_escape_string($conn, $_GET['fid']) : '';
$full = isset($_GET['full']) ? (int)$_GET['full'] : 0;

if (isset($_GET['qStep'])) {
    $settings['qStep'] = $_GET['qStep'];
}

$filter = $search !== '' ? " AND (ingredient LIKE '%$search%') AND owner_id = '$userID'" : " AND owner_id = '$userID' ";

$response = ['data' => [], 'meta' => []];
$mg = ['total_mg' => 0, 'total_mg_left' => 0];
$rx = [];

if ($meta === 0) {
    // If full=1, ignore paging and search, return all rows for this fid
    if ($full === 1) {
        $q = mysqli_query($conn, "SELECT * FROM $table WHERE fid = '$fid' AND owner_id = '$userID' $extra");
    } else {
        $q = mysqli_query($conn, "SELECT * FROM $table WHERE fid = '$fid' $filter $extra LIMIT $row, $limit");
    }
    while ($res = mysqli_fetch_assoc($q)) {
        $rs[] = $res;
    }

    $rsq = mysqli_fetch_all(mysqli_query($conn, "SELECT quantity FROM $table WHERE fid = '$fid' AND owner_id = '$userID'"), MYSQLI_ASSOC);
    $rsL = mysqli_fetch_all(mysqli_query($conn, "SELECT quantity FROM $table WHERE fid = '$fid' AND toAdd = '1' AND owner_id = '$userID'"), MYSQLI_ASSOC);
    
    foreach ($rs as $rq) {
        $ingredient = mysqli_fetch_assoc(mysqli_query($conn, "SELECT cas, notes FROM ingredients WHERE name = '" . mysqli_real_escape_string($conn, $rq['ingredient']) . "' AND owner_id = '$userID'"));
        $inventory = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ingSupplierID, SUM(stock) OVER() AS stock, mUnit FROM suppliers WHERE ingID = '" . (int)$rq['ingredient_id'] . "' AND owner_id = '$userID'"));
        $supplier = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, name FROM ingSuppliers WHERE id = '" . (int)$inventory['ingSupplierID'] . "' AND owner_id = '$userID'"));
        $replacement = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM ingredients WHERE id = '" . (int)$rq['replacement_id'] . "' AND owner_id = '$userID'"));

        $r = [
            'id' => (int)$rq['id'],
            'fid' => (string)$rq['fid'],
            'repID' => (string)$rq['replacement_id'],
            'repName' => (string)$replacement['name'],
            'name' => (string)$rq['name'],
            'ingredient' => (string)$rq['ingredient'],
            'ingID' => (int)$rq['ingredient_id'],
            'cas' => (string)($ingredient['cas'] ?? '-'),
            'notes' => (string)($ingredient['notes'] ?? '-'),
            'concentration' => (float)$rq['concentration'],
            'dilutant' => (string)($rq['dilutant'] ?? 'None'),
            'quantity' => number_format((float)$rq['quantity'], $settings['qStep'], '.', '') ?: 0,
            'originalQuantity' => number_format((float)$rq['originalQuantity'], $settings['qStep'], '.', '') ?: 0,
            'overdose' => number_format((float)$rq['overdose'], $settings['qStep'], '.', '') ?: 0,
            'inventory' => [
                'stock' => (float)($inventory['stock'] ?? 0),
                'mUnit' => (string)($inventory['mUnit'] ?? $settings['mUnit']),
                'supplier' => [
                    'name' => (string)$supplier['name'],
                    'id' => (string)$supplier['id'],
                ]
            ],
            'toAdd' => (int)$rq['toAdd'],
            'toSkip' => (int)$rq['skip'],
            'created_at' => (string)$rq['created_at'],
            'updated_at' => (string)$rq['updated_at']
        ];
        
        $rx[] = $r;
    }

    foreach ($rsq as $rq) {
        $mg['total_mg'] += (float)$rq['quantity'];
    }
    foreach ($rsL as $rq) {
        $mg['total_mg_left'] += (float)$rq['quantity'];
    }

    $m = [
        'total_ingredients' => (int)countElement("$table", "fid = '$fid'"),
        'total_ingredients_left' => (int)countElement("$table", "fid = '$fid' AND toAdd = '1' AND skip = '0'"),
        'total_quantity' => (float)ml2l($mg['total_mg'], $settings['qStep'], $settings['mUnit']),
        'total_quantity_left' => (float)ml2l($mg['total_mg_left'], $settings['qStep'], $settings['mUnit']),
        'quantity_unit' => (string)$settings['mUnit'],
        'last_updated' => (string)mysqli_fetch_assoc(mysqli_query($conn, "SELECT updated_at FROM $table WHERE fid = '$fid' AND owner_id = '$userID' ORDER BY updated_at DESC LIMIT 1"))['updated_at']
    ];

    $response['meta'] = $m;
    
    $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM $table WHERE fid = '$fid' AND owner_id = '$userID'"));
    $filtered = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM $table WHERE fid = '$fid' $filter"));
} else {
    $filter = $search !== '' ? " AND (name LIKE '%$search%') AND owner_id = '$userID'" : " AND owner_id = '$userID' ";
    $q = mysqli_query($conn, "SELECT id, fid, name, madeOn, scheduledOn, toDo AS toAdd FROM formulasMetaData WHERE toDo = '1' $filter $extra LIMIT $row, $limit");
    
    while ($res = mysqli_fetch_assoc($q)) {
        $r = [
            'id' => (int)$res['id'],
            'fid' => (string)$res['fid'],
            'name' => (string)$res['name'],
            'total_ingredients' => (int)countElement("$table","fid = '" . mysqli_real_escape_string($conn, $res['fid']) . "'"),
            'total_ingredients_left' => (int)countElement("$table", "fid = '" . mysqli_real_escape_string($conn, $res['fid']) . "' AND toAdd = '1' AND skip = '0'"),
            'toAdd' => (int)$res['toAdd'],
            'scheduledOn' => (string)$res['scheduledOn'],
            'madeOn' => (string)($res['madeOn'] ?? 'In progress')
        ];

        $rx[] = $r;
    }

    $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM formulasMetaData WHERE toDo = '1' AND owner_id = '$userID'"));
    $filtered = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM formulasMetaData WHERE toDo = '1' $filter"));
}

// Add a hash of the data for frontend comparison
$data_hash = hash('sha256', json_encode($rx));

$response = array_merge($response, [
    "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
    "recordsTotal" => (int)$total['entries'],
    "recordsFiltered" => (int)$filtered['entries'],
    "data" => $rx,
    "data_hash" => $data_hash
]);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

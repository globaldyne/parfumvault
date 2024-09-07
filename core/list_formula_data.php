<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/countElement.php');


$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$order_by = isset($_POST['order_by']) ? mysqli_real_escape_string($conn, $_POST['order_by']) : 'name';
$order = isset($_POST['order_as']) ? mysqli_real_escape_string($conn, $_POST['order_as']) : 'ASC';

$extra = "ORDER BY $order_by $order";
/*
$cats = [];
$cats_q = mysqli_query($conn, "SELECT id, name, description, type FROM IFRACategories ORDER BY id ASC");
while ($cats_res = mysqli_fetch_assoc($cats_q)) {
    $cats[] = $cats_res;
}
*/
$filters = [];
if (!empty($_GET['filter']) && (!empty($_GET['profile']) || !empty($_GET['sex']))) {
    if (!empty($_GET['profile'])) {
        $filters[] = "profile = '" . mysqli_real_escape_string($conn, $_GET['profile']) . "'";
    }
    if (!empty($_GET['sex'])) {
        $filters[] = "sex = '" . mysqli_real_escape_string($conn, $_GET['sex']) . "'";
    }
}

$s = trim($_POST['search']['value'] ?? '');
if ($s !== '') {
    $searchTerm = mysqli_real_escape_string($conn, $s);
    $filters[] = "(name LIKE '%$searchTerm%' OR product_name LIKE '%$searchTerm%' OR notes LIKE '%$searchTerm%')";
}

$f = !empty($filters) ? 'WHERE ' . implode(' AND ', $filters) : '';

$Query = "
    SELECT 
        id, fid, name, product_name, isProtected, profile, sex, created, 
        catClass, isMade, madeOn, status, rating, revision, 
        (SELECT updated FROM formulas WHERE fid = formulasMetaData.fid ORDER BY updated DESC LIMIT 1) AS updated, 
        (SELECT COUNT(dilutant) FROM formulas WHERE fid = formulasMetaData.fid) AS ingredients 
    FROM formulasMetaData 
    $f 
    $extra 
    LIMIT $row, $limit
";

$formulas = mysqli_query($conn, $Query);

$formulaData = [];
while ($allFormulas = mysqli_fetch_assoc($formulas)) {
    $formulaData[] = $allFormulas;
}

$rx = [];
foreach ($formulaData as $formula) {
    $r = [
        'id' => (int)$formula['id'],
        'fid' => (string)$formula['fid'],
        'product_name' => (string)($formula['product_name'] ?: 'N/A'),
        'name' => (string)($formula['name'] ?: 'Unnamed'),
        'isProtected' => (int)($formula['isProtected'] ?: 0),
        'profile' => (string)($formula['profile'] ?: 'N/A'),
        'sex' => (string)($formula['sex'] ?: 'unisex'),
        'created' => (string)$formula['created'],
        'updated' => (string)($formula['updated'] ?: '-'),
        'catClass' => (string)($formula['catClass'] ?: 'N/A'),
        'ingredients' => (int)($formula['ingredients'] ?: 0),
        'isMade' => (int)($formula['isMade'] ?: 0),
        'madeOn' => (string)($formula['madeOn'] ?: 'N/A'),
        'status' => (int)($formula['status'] ?: 0),
        'rating' => (int)($formula['rating'] ?: 0),
        'revision' => (int)($formula['revision'] ?: 0)
    ];
    
    $rx[] = $r;
}

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM formulasMetaData"));
$filtered = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM formulasMetaData $f"));

$response = [
    "draw" => (int)$_POST['draw'],
    "recordsTotal" => (int)$total['entries'],
    "recordsFiltered" => (int)$filtered['entries'],
    "data" => $rx,
    "debug" => $Query
];

if (empty($rx)) {
    $response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;

?>

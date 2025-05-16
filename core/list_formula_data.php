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

$filters = [];
if (!empty($_GET['filter']) && (!empty($_GET['profile']) || !empty($_GET['gender']))) {
    if (!empty($_GET['profile'])) {
        $filters[] = "fm.profile = '" . mysqli_real_escape_string($conn, $_GET['profile']) . "'";
    }
    if (!empty($_GET['gender'])) {
        $filters[] = "fm.gender = '" . mysqli_real_escape_string($conn, $_GET['gender']) . "'";
    }
}

$s = trim($_POST['search']['value'] ?? '');
$extendSearchLabels = isset($_POST['extendSearchLabels']) && $_POST['extendSearchLabels'] === 'true';

if ($s !== '') {
    $searchTerm = mysqli_real_escape_string($conn, $s);
    $searchFilter = "(fm.name LIKE '%$searchTerm%' OR fm.product_name LIKE '%$searchTerm%' OR fm.notes LIKE '%$searchTerm%')";

    if ($extendSearchLabels) {
        $tagFilter = "EXISTS (
            SELECT 1 FROM formulasTags ft 
            WHERE ft.formula_id = fm.id AND ft.tag_name LIKE '%$searchTerm%' AND ft.owner_id = '$userID'
        )";

        $filters[] = "($searchFilter OR $tagFilter)";
    } else {
        $filters[] = $searchFilter;
    }
}

$f = !empty($filters) ? 'AND ' . implode(' AND ', $filters) : '';

$Query = "
    SELECT 
        fm.id, fm.fid, fm.name, fm.product_name, fm.isProtected, fm.profile, fm.gender, fm.created_at, fm.catClass, 
        fm.isMade, fm.madeOn, fm.status, fm.rating, fm.revision,
        (SELECT updated_at 
         FROM formulas 
         WHERE fid = fm.fid AND owner_id = '$userID' 
         ORDER BY updated_at DESC 
         LIMIT 1) AS updated_at, 
        (SELECT COUNT(id) 
         FROM formulas 
         WHERE fid = fm.fid AND owner_id = '$userID') AS ingredients,
        (SELECT g.id FROM groups g WHERE g.fid = fm.fid AND g.user_id = '$userID' LIMIT 1) AS gid 
    FROM formulasMetaData fm
    LEFT JOIN groups g ON fm.fid = g.fid AND g.user_id = '$userID'
    WHERE (fm.owner_id = '$userID' 
       OR fm.fid IN (SELECT g.fid FROM groups g WHERE g.user_id = '$userID')) 
    $f $extra 
    LIMIT $row, $limit;
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
        'product_name' => (string)($formula['product_name'] ?: '-'),
        'name' => (string)($formula['name'] ?: 'Unnamed'),
        'isProtected' => (int)($formula['isProtected'] ?: 0),
        'profile' => (string)($formula['profile'] ?: '-'),
        'gender' => (string)($formula['gender'] ?: 'unisex'),
        'created_at' => (string)($formula['created_at'] ?: '0000-00-00 00:00:00'),
        'updated_at' => (string)($formula['updated_at'] ?: '0000-00-00 00:00:00'),
        'catClass' => (string)($formula['catClass'] ?: '-'),
        'ingredients' => (int)($formula['ingredients'] ?: 0),
        'isMade' => (int)($formula['isMade'] ?: 0),
        'madeOn' => (string)($formula['madeOn'] ?: '-'),
        'status' => (int)($formula['status'] ?: 0),
        'rating' => (int)($formula['rating'] ?: 0),
        'revision' => (int)($formula['revision'] ?: 0),
        'gid' => (int)($formula['gid'] ?: 0)
    ];
    
    $rx[] = $r;
}

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM formulasMetaData fm WHERE fm.owner_id = '$userID'"));
$filtered = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM formulasMetaData fm WHERE (fm.owner_id = '$userID' OR fm.fid IN (SELECT g.fid FROM groups g WHERE g.user_id = '$userID')) $f"));

$response = [
    "draw" => (int)$_POST['draw'],
    "recordsTotal" => (int)$total['entries'],
    "recordsFiltered" => (int)$filtered['entries'],
    "data" => $rx,
   // "debug" => $Query
];

if (empty($rx)) {
    $response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

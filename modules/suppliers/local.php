<?php
$starttime = microtime(true);

if(defined('__ROOT__') == FALSE){
	define('__ROOT__', dirname(dirname(__FILE__))); 
}

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

require_once(__ROOT__.'/func/getIngSupplier.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/getDocument.php');

$defCatClass = $settings['defCatClass'];

$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$order_by = in_array($_POST['order_by'], ['name', 'cas', 'profile']) ? $_POST['order_by'] : 'name';
$order = in_array(strtoupper($_POST['order_as']), ['ASC', 'DESC']) ? strtoupper($_POST['order_as']) : 'ASC';

$filter = "WHERE ingredients.owner_id = '$userID'"; // Base filter for user-specific data

if ($_POST['advanced']) {
    if ($name = trim(mysqli_real_escape_string($conn, $_POST['name']))) {
        $n = $name;
    } else {
        $n = '%';
    }

    $filter .= " AND ingredients.name LIKE '%$n%'";

    if ($cas = trim(mysqli_real_escape_string($conn, $_POST['cas']))) {
        $filter .= " AND ingredients.cas LIKE '%$cas%'";
    }

    if ($einecs = trim(mysqli_real_escape_string($conn, $_POST['einecs']))) {
        $filter .= " AND ingredients.einecs LIKE '%$einecs%'";
    }

    if ($odor = trim(mysqli_real_escape_string($conn, $_POST['odor']))) {
        $filter .= " AND ingredients.odor LIKE '%$odor%'";
    }

    if ($profile = mysqli_real_escape_string($conn, $_POST['profile'])) {
        $filter .= " AND ingredients.profile = '$profile'";
    }

    if ($category = mysqli_real_escape_string($conn, $_POST['cat'])) {
        $filter .= " AND ingredients.category = '$category'";
    }

    if ($synonym = mysqli_real_escape_string($conn, $_POST['synonym'])) {
        $filter .= " AND ingredients.name IN (SELECT ing FROM synonyms WHERE synonym LIKE '%$synonym%' AND owner_id = '$userID')";
    }
}

$search = trim(mysqli_real_escape_string($conn, $_POST['search']['value'] ?? $_POST['pvSearch'] ?? ''));
if ($search !== '') {
    $filter = "WHERE ingredients.owner_id = '$userID' AND (ingredients.name LIKE '%$search%' OR ingredients.cas LIKE '%$search%' OR ingredients.einecs LIKE '%$search%' OR ingredients.odor LIKE '%$search%' OR ingredients.INCI LIKE '%$search%')";
}

$extra = "ORDER BY $order_by $order";

$query = "
    SELECT 
        ingredients.id, ingredients.name, ingredients.INCI, ingredients.cas, ingredients.einecs, ingredients.profile, ingredients.category, 
        ingredients.odor, $defCatClass, ingredients.allergen, ingredients.usage_type, ingredients.logp, ingredients.formula, 
        ingredients.flash_point, ingredients.molecularWeight, ingredients.byPassIFRA, ingredients.physical_state, 
        c.name AS cat_name, c.image AS cat_image
    FROM ingredients 
    LEFT JOIN ingCategory c ON ingredients.category = c.id
    $filter
    $extra
    LIMIT $row, $limit
";
//error_log("PV Info: Query: $query");
$result = mysqli_query($conn, $query);

if (!$result) {
    error_log("PV error: Query failed - " . mysqli_error($conn));
    exit;
}

$ingredients = [];
while ($ingredient = mysqli_fetch_assoc($result)) {
    $r = [];
    $r['id'] = (int)$ingredient['id'];
    $r['name'] = htmlspecialchars($ingredient['name'], ENT_QUOTES, 'UTF-8');
    $r['IUPAC'] = htmlspecialchars($ingredient['INCI'], ENT_QUOTES, 'UTF-8') ?: '-';
    $r['cas'] = $ingredient['cas'] ?: '-';
    $r['einecs'] = $ingredient['einecs'] ?: '-';
    $r['profile'] = $ingredient['profile'] ?: null;
    $r['odor'] = $ingredient['odor'] ?: '-';
    $r['allergen'] = (int)$ingredient['allergen'] ?: 0;
    $r['physical_state'] = (int)$ingredient['physical_state'] ?: 0;
    $r['techData']['LogP'] = (float)$ingredient['logp'] ?: 0;
    $r['techData']['formula'] = $ingredient['formula'] ?: '-';
    $r['techData']['flash_point'] = $ingredient['flash_point'] ?: '-';
    $r['techData']['molecula_weight'] = (float)$ingredient['molecularWeight'] ?: 0;

    $r['category']['id'] = (int)$ingredient['category'] ?: 1;
    $r['category']['name'] = $ingredient['cat_name'] ?: '-';
    $r['category']['image'] = $ingredient['cat_image'] ?: '/img/pv_molecule.png';

    $limit = searchIFRA($ingredient['cas'], $ingredient['name'], null, $defCatClass, 0);
    if ($limit && $ingredient['byPassIFRA'] == 0) {
        $r['usage']['limit'] = (float)$limit['val'];
        $r['usage']['reason'] = $limit['risk'];
    } else {
        $r['usage']['limit'] = number_format((float)$ingredient[$defCatClass], $settings['qStep']) ?: 100;
        $r['usage']['reason'] = (int)$ingredient['usage_type'];
    }
    $r['info']['byPassIFRA'] = (int)$ingredient['byPassIFRA'];

    $suppliers = getIngSupplier($ingredient['id'], 0, $conn);
    if ($suppliers) {
        $j = 0;
        foreach ($suppliers as $supplier) {
            $r['supplier'][$j]['name'] = $supplier['name'];
            $r['supplier'][$j]['link'] = $supplier['supplierLink'];
            $r['supplier'][$j]['status'] = (int)$supplier['status'];
            $j++;
        }
    } else {
        $r['supplier'] = null;
        $r['error'] = "No supplier is configured.\nYou won't be able to use this ingredient in a formula unless at least one supplier is configured.";
    }

    $documents = getDocument($ingredient['id'], 1, $conn);
    if ($documents) {
        $i = 0;
        foreach ($documents as $doc) {
            $r['document'][$i]['name'] = $doc['name'];
            $r['document'][$i]['id'] = (int)$doc['id'];
            $i++;
        }
    }

    $r['stock'] = number_format((float)getIngSupplier($ingredient['id'], 1, $conn)['stock'], $settings['qStep']) ?: 0;

    $ingredients[] = $r;
}

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(name) AS entries FROM ingredients WHERE ingredients.owner_id = '$userID'"));
$filtered = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(name) AS entries FROM ingredients $filter"));

$response = [
    "source" => 'local',
    "draw" => (int)$_POST['draw'],
    "recordsTotal" => (int)$total['entries'],
    "recordsFiltered" => (int)$filtered['entries'],
    "data" => $ingredients
];

$load_time = microtime(true) - $starttime;
$logMessage = sprintf(
    "PV Info: Ingredients local DB load time: %.4f seconds",
    $load_time
);

// Log the load time
error_log($logMessage);


header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);


?>
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
require_once(__ROOT__.'/func/getCatByID.php');
require_once(__ROOT__.'/func/getDocument.php');

$defCatClass = $settings['defCatClass'];
$row = $_POST['start'] ?? 0;
$limit = $_POST['length'] ?? 10;
$order_by = $_POST['order_by'] ?? 'name';
$order = $_POST['order_as'] ?? 'ASC';

function sanitizeInput($conn, $input) {
    return trim(mysqli_real_escape_string($conn, $input));
}

// Advanced search filters
$filter = 'WHERE 1=1';  // Base filter

if ($_POST['adv']) {
    $name = sanitizeInput($conn, $_POST['name']) ?: '%';
    $filter .= " AND name LIKE '%$name%'";

    $cas = sanitizeInput($conn, $_POST['cas']);
    if ($cas) $filter .= " AND cas LIKE '%$cas%'";

    $einecs = sanitizeInput($conn, $_POST['einecs']);
    if ($einecs) $filter .= " AND einecs LIKE '%$einecs%'";

    $odor = sanitizeInput($conn, $_POST['odor']);
    if ($odor) $filter .= " AND odor LIKE '%$odor%'";

    $profile = sanitizeInput($conn, $_POST['profile']);
    if ($profile) $filter .= " AND profile = '$profile'";

    $category = sanitizeInput($conn, $_POST['cat']);
    if ($category) $filter .= " AND category = '$category'";

    $synonym = sanitizeInput($conn, $_POST['synonym']);
    if ($synonym) {
        $filter = "WHERE synonym LIKE '%$synonym%' AND ing = name GROUP BY name";
    }
}

// Basic search
$searchTerm = sanitizeInput($conn, $_POST['search']['value'] ?? $_POST['pvSearch']);
if ($searchTerm) {
    $filter = "WHERE 1 AND (name LIKE '%$searchTerm%' OR cas LIKE '%$searchTerm%' OR einecs LIKE '%$searchTerm%' OR odor LIKE '%$searchTerm%' OR INCI LIKE '%$searchTerm%')";
}

$extra = "ORDER BY $order_by $order";

// Fetch ingredients based on filter and pagination
$query = "SELECT ingredients.id, name, INCI, cas, einecs, profile, category, odor, $defCatClass, allergen, usage_type, logp, formula, flash_point, molecularWeight, byPassIFRA, physical_state 
          FROM ingredients 
          $filter 
          $extra 
          LIMIT $row, $limit";
$q = mysqli_query($conn, $query);

$ingredients = [];
while ($res = mysqli_fetch_array($q)) {
    $ingredients[] = $res;
}

// Prepare the response data
$rx = [];
foreach ($ingredients as $ingredient) {
    $catQuery = "SELECT name, image FROM ingCategory WHERE id = '" . (int)$ingredient['category'] . "'";
    $cat = mysqli_fetch_array(mysqli_query($conn, $catQuery));

    $r = [
        'id' => (int)$ingredient['id'],
        'name' => (string)filter_var($ingredient['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        'IUPAC' => (string)filter_var($ingredient['INCI'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'N/A',
        'cas' => (string)$ingredient['cas'] ?: 'N/A',
        'einecs' => (string)$ingredient['einecs'] ?: 'N/A',
        'profile' => (string)$ingredient['profile'] ?: null,
        'odor' => (string)$ingredient['odor'] ?: 'N/A',
        'allergen' => (int)$ingredient['allergen'] ?: 0,
        'physical_state' => (int)$ingredient['physical_state'] ?: 0,
        'techData' => [
            'LogP' => (float)$ingredient['logp'] ?: 0,
            'formula' => (string)$ingredient['formula'] ?: 'N/A',
            'flash_point' => (string)$ingredient['flash_point'] ?: 'N/A',
            'molecular_weight' => (float)$ingredient['molecularWeight'] ?: 0,
        ],
        'category' => [
            'id' => (int)$ingredient['category'] ?: 1,
            'name' => (string)$cat['name'] ?: 'N/A',
            'image' => (string)$cat['image'] ?: '/img/pv_molecule.png',
        ],
        'info' => [
            'byPassIFRA' => (int)$ingredient['byPassIFRA'],
        ]
    ];

    // Handle IFRA usage limits
    if (($limit = searchIFRA($ingredient['cas'], $ingredient['name'], null, $defCatClass)) && $ingredient['byPassIFRA'] == 0) {
        $r['usage'] = [
            'limit' => (float)$limit['val'],
            'reason' => (string)$limit['risk']
        ];
    } else {
        $r['usage'] = [
            'limit' => number_format((float)$ingredient[$defCatClass], $settings['qStep']) ?: 100,
            'reason' => (int)$ingredient['usage_type']

        ];
    }

    // Supplier data
    $suppliers = getIngSupplier($ingredient['id'], 0, $conn);
    $r['supplier'] = $suppliers ? array_map(function($supplier) {
        return [
            'name' => (string)$supplier['name'],
            'link' => (string)$supplier['supplierLink'],
            'status' => (int)$supplier['status']
        ];
    }, $suppliers) : null;

    // Document data
    $documents = getDocument($ingredient['id'], 1, $conn);
    $r['document'] = $documents ? array_map(function($doc) {
        return [
            'name' => (string)$doc['name'],
            'id' => (int)$doc['id']
        ];
    }, $documents) : null;

    // Stock data
    $r['stock'] = number_format((float)getIngSupplier($ingredient['id'], 1, $conn)['stock'], $settings['qStep']) ?: 0;

    $rx[] = $r;
}

// Fetch total entries
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(name) AS entries FROM ingredients"));
$filtered = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(name) AS entries FROM ingredients $filter"));

// Prepare JSON response
$response = [
    "source" => 'local',
    "draw" => (int)$_POST['draw'],
    "recordsTotal" => (int)$total['entries'],
    "recordsFiltered" => (int)$filtered['entries'],
    "data" => $rx ?: []
];

// Load time
$response['sys']['load_time'] = microtime(true) - $starttime;

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);


?>

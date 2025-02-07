<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if($role !== 1){
    header('Location: /');
    return;
}

$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$order_by = isset($_POST['order_by']) ? mysqli_real_escape_string($conn, $_POST['order_by']) : 'name';
$order = isset($_POST['order_as']) ? mysqli_real_escape_string($conn, $_POST['order_as']) : 'ASC';

$extra = "ORDER BY $order_by $order";

$filters = [];

$s = trim($_POST['search']['value'] ?? '');
if ($s !== '') {
    $searchTerm = mysqli_real_escape_string($conn, $s);
    $filters[] = "(email LIKE '%$searchTerm%' OR user_id LIKE '%$searchTerm%')";
}

$f = !empty($filters) ? 'WHERE ' . implode(' AND ', $filters) : '';

$Query = "SELECT * FROM users LIMIT $row, $limit";

$users = mysqli_query($conn, $Query);

$userData = [];
while ($allFormulas = mysqli_fetch_assoc($users)) {
    $userData[] = $allFormulas;
}

$rx = [];
foreach ($userData as $user) {
    $isLoggedIn = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM session_info WHERE owner_id = '".$user['id']."'"));
    $formulaCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM formulasMetaData WHERE owner_id = '".$user['id']."'"));
    $ingredientCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM ingredients WHERE owner_id = '".$user['id']."'"));
    $batchIDHistoryCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM batchIDHistory WHERE owner_id = '".$user['id']."'"));
    $bottlesCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM bottles WHERE owner_id = '".$user['id']."'"));
    $cartCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM cart WHERE owner_id = '".$user['id']."'"));
    $customersCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM customers WHERE owner_id = '".$user['id']."'"));
    $documentsCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM documents WHERE owner_id = '".$user['id']."'"));
    $formulaCategoriesCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM formulaCategories WHERE owner_id = '".$user['id']."'"));
    $formulasCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM formulas WHERE owner_id = '".$user['id']."'"));
    $formulasRevisionsCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM formulasRevisions WHERE owner_id = '".$user['id']."'"));
    $formulasTagsCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM formulasTags WHERE owner_id = '".$user['id']."'"));
    $formulaHistoryCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM formula_history WHERE owner_id = '".$user['id']."'"));
    $IFRALibraryCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM IFRALibrary WHERE owner_id = '".$user['id']."'"));
    $ingCategoryCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM ingCategory WHERE owner_id = '".$user['id']."'"));
    $ingredientCompoundsCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM ingredient_compounds WHERE owner_id = '".$user['id']."'"));
    $ingredientSafetyDataCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM ingredient_safety_data WHERE owner_id = '".$user['id']."'"));
    $ingReplacementsCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM ingReplacements WHERE owner_id = '".$user['id']."'"));
    $ingSafetyInfoCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM ingSafetyInfo WHERE owner_id = '".$user['id']."'"));
    $ingSuppliersCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM ingSuppliers WHERE owner_id = '".$user['id']."'"));
    $inventoryAccessoriesCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM inventory_accessories WHERE owner_id = '".$user['id']."'"));
    $inventoryCompoundsCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM inventory_compounds WHERE owner_id = '".$user['id']."'"));
    $makeFormulaCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM makeFormula WHERE owner_id = '".$user['id']."'"));
    $perfumeTypesCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM perfumeTypes WHERE owner_id = '".$user['id']."'"));
    $sdsDataCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM sds_data WHERE owner_id = '".$user['id']."'"));
    $suppliersCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM suppliers WHERE owner_id = '".$user['id']."'"));
    $synonymsCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM synonyms WHERE owner_id = '".$user['id']."'"));
    $templatesCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM templates WHERE owner_id = '".$user['id']."'"));
    $sessionValidUntil = mysqli_fetch_array(mysqli_query($conn, "SELECT remaining_time FROM session_info WHERE owner_id = '".$user['id']."'"));

    $r = [
        'id' => $user['id'],
        'email' => $user['email'],
        'full_name' => $user['fullName'],
        'provider' => $user['provider'],
        'role' => $user['role'],
        'status' => $user['isActive'],
        'country' => $user['country'],
        'is_api_active' => $user['isAPIActive'],
        'api_key' => $user['API_key'],
        'is_verified' => $user['isVerified'],
        'is_logged_in' => (int)$isLoggedIn['entries'],
        'session_valid_until' => round($sessionValidUntil['remaining_time'] / 60, 2) . ' hours',
        //'session_valid_until_raw' => (float)$sessionValidUntil['remaining_time'],
        'stats' => [
            'total_formulas' => (int)$formulaCount['entries'],
            'total_ingredients' => (int)$ingredientCount['entries'],
            'total_batch_id_history' => (int)$batchIDHistoryCount['entries'],
            'total_bottles' => (int)$bottlesCount['entries'],
            'total_cart' => (int)$cartCount['entries'],
            'total_customers' => (int)$customersCount['entries'],
            'total_documents' => (int)$documentsCount['entries'],
            'total_formula_categories' => (int)$formulaCategoriesCount['entries'],
            'total_formulas' => (int)$formulasCount['entries'],
            'total_formulas_revisions' => (int)$formulasRevisionsCount['entries'],
            'total_formulas_tags' => (int)$formulasTagsCount['entries'],
            'total_formula_history' => (int)$formulaHistoryCount['entries'],
            'total_ifra_library' => (int)$IFRALibraryCount['entries'],
            'total_ing_category' => (int)$ingCategoryCount['entries'],
            'total_ingredient_compounds' => (int)$ingredientCompoundsCount['entries'],
            'total_ingredient_safety_data' => (int)$ingredientSafetyDataCount['entries'],
            'total_ing_replacements' => (int)$ingReplacementsCount['entries'],
            'total_ing_safety_info' => (int)$ingSafetyInfoCount['entries'],
            'total_ing_suppliers' => (int)$ingSuppliersCount['entries'],
            'total_inventory_accessories' => (int)$inventoryAccessoriesCount['entries'],
            'total_inventory_compounds' => (int)$inventoryCompoundsCount['entries'],
            'total_make_formula' => (int)$makeFormulaCount['entries'],
            'total_perfume_types' => (int)$perfumeTypesCount['entries'],
            'total_sds_data' => (int)$sdsDataCount['entries'],
            'total_suppliers' => (int)$suppliersCount['entries'],
            'total_synonyms' => (int)$synonymsCount['entries'],
            'total_templates' => (int)$templatesCount['entries']
        ],
        'created_at' => $user['created_at'],
        'updated_at' => $user['updated_at']
    ];
    
    $rx[] = $r;
}

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM users"));
$filtered = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM users $f"));

$response = [
    "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 0,
    "recordsTotal" => (int)$total['entries'],
    "recordsFiltered" => (int)$filtered['entries'],
    "data" => $rx,
    //"debug" => $Query
];

if (empty($rx)) {
    $response['data'] = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;

?>
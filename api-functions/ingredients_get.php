<?php

if (!defined('pvault_panel')){ die('Not Found');}

header('Content-Type: application/json; charset=utf-8');
global $conn, $userID;

// Function to fetch data as associative array
function fetch_assoc($conn, $query) {
    $result = mysqli_query($conn, $query);
    return $result ? mysqli_fetch_assoc($result) : null;
}

// Function to sanitize and normalize values
function normalize_value($value, $type = 'string', $default = '-') {
    if (is_null($value) || empty($value)) {
        return $default;
    }
    switch ($type) {
        case 'int':
            return (int)$value;
        case 'float':
            return (float)$value;
        case 'string':
        default:
            return (string)$value;
    }
}

// Fetch ingredients data
$sql = "SELECT id, INCI, name, cas, notes, profile, physical_state, category, purity, allergen 
        FROM ingredients WHERE owner_id = '$userID'";
$result = mysqli_query($conn, $sql);

$r = [];
while ($rx = mysqli_fetch_assoc($result)) {
    // Fetch IFRALibrary data
    $ifra_query = "SELECT * FROM IFRALibrary WHERE cas = '{$rx['cas']}' AND owner_id = '$userID'";
    $ifra = fetch_assoc($conn, $ifra_query);

    //if ($ifra) {
        foreach (['cat1', 'cat2', 'cat3', 'cat4', 'cat5A', 'cat5B', 'cat5C', 'cat5D', 
                  'cat6', 'cat7A', 'cat7B', 'cat8', 'cat9', 'cat10A', 'cat10B', 
                  'cat11A', 'cat11B', 'cat12'] as $cat) {
            $rx[$cat] = normalize_value(preg_replace("/[^0-9.]/", "", $ifra[$cat]), 'float', 100.0);
        }
        $rx['class'] = $ifra['type'] ?? 'Recommendation';
   // }

    // Fetch supplier data
    $supplier_query = "SELECT ingSupplierID, price, size, stock 
                       FROM suppliers WHERE ingID = '{$rx['id']}' AND preferred = 1 AND owner_id = '$userID'";
    $gSupQ = fetch_assoc($conn, $supplier_query);

    // Fetch supplier name
    $supplier_name_query = "SELECT name FROM ingSuppliers WHERE id = '{$gSupQ['ingSupplierID']}' AND owner_id = '$userID'";
    $gSupN = fetch_assoc($conn, $supplier_name_query);

    // Fetch category details
    $category_query = "SELECT name, notes FROM ingCategory WHERE id = '{$rx['category']}' AND owner_id = '$userID'";
    $gCatQ = fetch_assoc($conn, $category_query);

    // Calculate defaults
    $size = $gSupQ['size'] ?: 10;
    $price_per_unit = $gSupQ['price'] / $size;

    // Normalize and structure data
//    $rx['id'] = normalize_value($rx['id'], 'int');
    unset($rx['id']);
    $rx['INCI'] = normalize_value($rx['INCI']);
    $rx['name'] = normalize_value($rx['name']);
    $rx['type'] = normalize_value($rx['type']);
	$rx['strength'] = normalize_value($rx['strength']);
	$rx['category'] = normalize_value($rx['category'], 0);
	$rx['purity'] = normalize_value($rx['purity']);
    $rx['cas'] = normalize_value($rx['cas']) ;
	$rx['einecs'] = normalize_value($rx['einecs']);
	$rx['FEMA'] = normalize_value($rx['FEMA']);
	$rx['reach'] = normalize_value($rx['reach']);
	$rx['tenacity'] = normalize_value($rx['tenacity']);
	$rx['chemical_name'] = normalize_value($rx['chemical_name']);
	$rx['formula'] = normalize_value($rx['formula']);
	$rx['flash_point'] = normalize_value($rx['flash_point']);
	$rx['appearance'] = normalize_value($rx['appearance']);
	$rx['rdi'] = normalize_value($rx['rdi'], 'int', 0);
	$rx['notes'] = normalize_value($rx['notes']);
	$rx['solvent'] = normalize_value($rx['solvent']);
    $rx['flavor_use'] = normalize_value($rx['flavor_use']);
    $rx['soluble'] = normalize_value($rx['soluble']);
    $rx['logp'] = normalize_value($rx['logp']);
    $rx['impact_top'] = normalize_value($rx['impact_top']);
    $rx['impact_heart'] = normalize_value($rx['impact_heart']);
    $rx['impact_base'] = normalize_value($rx['impact_base']);
    $rx['usage_type'] = normalize_value($rx['usage_type']);
    $rx['noUsageLimit'] = normalize_value($rx['noUsageLimit'], 'int', 0);
    $rx['byPassIFRA'] = normalize_value($rx['byPassIFRA'], 'int', 0);
    $rx['molecularWeight'] = normalize_value($rx['molecularWeight']);
    $rx['physical_state'] = normalize_value($rx['physical_state'], 'int', 0);
    $rx['cid'] = normalize_value($rx['cid'], 'int', 0);
    $rx['shelf_life'] = normalize_value($rx['shelf_life'], 'int', 0);	
    $rx['profile'] = normalize_value($rx['profile'] ?: "Default");
    $rx['physical_state'] = normalize_value($rx['physical_state'], 'int', 0);
    $rx['purity'] = normalize_value($rx['purity'], 'float', 100.0);
    $rx['allergen'] = normalize_value($rx['allergen'], 'int', 0);
    $rx['category'] = normalize_value($rx['category'], 'int', 0);
    $rx['category_name'] = normalize_value($gCatQ['name'], 'string', 'Uncategorized');
    $rx['category_notes'] = normalize_value($gCatQ['notes']);
    $rx['category_identifier'] = rgb_to_hex('rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ', 0.8)');
    $rx['supplier'] = normalize_value($gSupN['name']);
    $rx['price'] = normalize_value($price_per_unit, 'float', 0.0);
    $rx['stock'] = normalize_value($gSupQ['stock'], 'float', 0.0);
    $rx['isSolvent'] = ($rx['profile'] === "Solvent") ? 1 : 0;
    // Set odor for ios app compatibility
    $rx['odor'] = ($gCatQ['name'] ?: '-') ;

    $r[] = $rx;
}

// Output JSON response
$response = [
    "ingredients" => $r,
];
echo json_encode($response, JSON_PRETTY_PRINT);
?>

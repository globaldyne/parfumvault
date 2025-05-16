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
$sql = "SELECT id, INCI, name, cas, notes, physical_state, updated_at, created_at FROM ingredients WHERE owner_id = '$userID'";
$result = mysqli_query($conn, $sql);

$r = [];
while ($rx = mysqli_fetch_assoc($result)) {
    // Fetch supplier data
    $supplier_query = "SELECT ingSupplierID, price, size, stock, mUnit, status FROM suppliers WHERE ingID = '{$rx['id']}' AND preferred = 1 AND owner_id = '$userID'";
    $gSupQ = fetch_assoc($conn, $supplier_query);
    error_log('PV API AROMATRACK QUERY: '.$supplier_query);
    // Fetch supplier name
    $supplier_name_query = "SELECT * FROM ingSuppliers WHERE id = '{$gSupQ['ingSupplierID']}' AND owner_id = '$userID'";
    $gSupN = fetch_assoc($conn, $supplier_name_query);

    // Calculate defaults
    $size = $gSupQ['size'] ?: 10;
    $price_per_unit = $gSupQ['price'] / $size;

    // Normalize and structure data
    unset($rx['id']);
    $rx['aromaTrackID'] = normalize_value($rx['aromaTrackID']);
    $rx['inci'] = normalize_value($rx['INCI']);
    $rx['name'] = normalize_value($rx['name']);
    $rx['cas'] = normalize_value($rx['cas']) ;
	$rx['appearance'] = normalize_value($rx['appearance']);
	$rx['notes'] = normalize_value($rx['notes']);
    $rx['price'] = normalize_value($price_per_unit, 'float', 0.0);
    $rx['stock'] = normalize_value($gSupQ['stock'], 'float', 0.0);
    $rx['mUnit'] = normalize_value($gSupQ['mUnit']);
    $rx['availability'] = (int)normalize_value($gSupQ['status']) ?: 0;
    $rx['updated_at'] = normalize_value(date(DATE_ISO8601, strtotime($rx['updated_at'])));
    $rx['created_at'] = normalize_value(date(DATE_ISO8601, strtotime($rx['created_at'])));

    $rx['supplier'] = [
        'name' => normalize_value($gSupN['name']),
        'address' => normalize_value($gSupN['address']),
        'po' => normalize_value($gSupN['po']),
        'country' => normalize_value($gSupN['country']),
        'currency' => normalize_value($gSupN['currency']),
        'telephone' => normalize_value($gSupN['telephone']),
        'url' => normalize_value($gSupN['url']),
        'email' => normalize_value($gSupN['email']),
        'notes' => normalize_value($gSupN['notes']),
        'add_costs' => normalize_value($gSupN['add_costs']),
        'updated_at' => normalize_value($gSupN['updated_at'] ? date(DATE_ISO8601, strtotime($gSupN['updated_at'])) : date(DATE_ISO8601)),
        'created_at' => normalize_value($gSupN['created_at'] ? date(DATE_ISO8601, strtotime($gSupN['created_at'])) : date(DATE_ISO8601)),
    ];

    $r[] = $rx;
}

// Output JSON response
$response = [
    "status" => "success",
    "ingredients" => $r,
];
echo json_encode($response, JSON_PRETTY_PRINT);
?>

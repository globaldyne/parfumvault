<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');


if($role === 1){

    //EXPORT USERS JSON
    if ($_GET['format'] === 'json' && $_GET['kind'] === 'users') {

        // Fetch user data
        $userQuery = "SELECT * FROM users";
        $userResult = mysqli_query($conn, $userQuery);

        if (!$userResult) {
            // Handle query failure
            error_log("PV error: Failed to fetch users. MySQL error: " . mysqli_error($conn));
            echo json_encode(['error' => 'Failed to fetch user data.']);
            return;
        }

        $users = [];
        while ($row = mysqli_fetch_assoc($userResult)) {
            $users[] = [
                'id'         => (int) $row['id'],
                'fullName'   => (string) $row['fullName'],
                'email'      => (string) $row['email'],
                'password'   => (string) $row['password'],
                'provider'   => (int) $row['provider'],
                'isActive'   => (int) $row['isActive'],
                'role'       => (int) $row['role'],
                'country'    => (string) $row['country'],
                'isAPIActive'=> (int) $row['isAPIActive'],
                'API_key'    => (string) $row['API_key'],
                'isVerified'  => (int) $row['isVerified'],
                'updated_at' => (string) $row['updated_at'],
                'created_at' => (string) $row['created_at'],
            ];
        }

        // Count the number of users
        $usersCount = count($users);

        // Add metadata
        $metaData = [
            'product'   => $product,
            'version'   => $ver,
            'users'     => $usersCount,
            'timestamp' => date('d/m/Y H:i:s'),
        ];

        // Prepare the result
        $result = [
            'users'  => $users,
            'pvMeta' => $metaData,
        ];

        // Send JSON headers and output the result
        header('Content-Disposition: attachment; filename=pv_users.json');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result, JSON_PRETTY_PRINT);
        return;
    }
}

//EXPORT USER DATA
if ($_GET['kind'] === 'user-data') {
    if ($role === 1 && isset($_GET['id']) && !empty($_GET['id'])) {
        $userID = mysqli_real_escape_string($conn, $_GET['id']);
    }

    error_log("PV error: Export initiated for userID: $userID");

    $format = $_GET['format'] ?? 'sql';

    // Allowed tables for export
    $allowedTables = [
        'IFRALibrary', 'bottles', 'branding', 'cart', 'customers', 
        'documents', 'formulaCategories', 'formula_history', 'formulas', 'formulasMetaData', 
        'formulasRevisions', 'formulasTags', 'groups', 'ingCategory', 'ingReplacements', 
        'ingSafetyInfo', 'ingSuppliers', 'ingredient_compounds', 
        'ingredient_safety_data', 'ingredients', 'inventory_accessories', 'inventory_compounds', 
        'makeFormula', 'order_items', 'orders', 'perfumeTypes', 'sdsSettings', 'sds_data', 
        'suppliers', 'synonyms', 'templates', 'user_prefs', 'user_settings'
    ];

    $result = [];

    foreach ($allowedTables as $table) {
        $columnsQuery = "SHOW COLUMNS FROM `$table`";
        $columnsRes = mysqli_query($conn, $columnsQuery);
        if (!$columnsRes) {
            error_log("PV error: Failed to get columns for $table. MySQL error: " . mysqli_error($conn));
            continue;
        }

        $columns = [];
        $doubleColumns = [];

        while ($col = mysqli_fetch_assoc($columnsRes)) {
            $colName = $col['Field'];
            $colType = strtolower($col['Type']);

            if ($colName !== 'id') {
                $columns[] = "`$colName`";
                if (strpos($colType, 'double') !== false || strpos($colType, 'float') !== false || strpos($colType, 'decimal') !== false) {
                    $doubleColumns[] = $colName;
                }
            }
        }

        if (empty($columns)) {
            error_log("PV error: No valid columns found for $table.");
            continue;
        }

        $columnsList = implode(", ", $columns);
        $query = "SELECT $columnsList FROM `$table` WHERE owner_id = '$userID'";
        error_log("PV error: Running query: $query");

        $res = mysqli_query($conn, $query);
        if (!$res) {
            error_log("PV error: Failed to fetch data from $table. MySQL error: " . mysqli_error($conn));
            continue;
        }

        if (mysqli_num_rows($res) === 0) {
            error_log("PV error: No data found in $table for userID: $userID");
            continue;
        }

        while ($row = mysqli_fetch_assoc($res)) {
            foreach ($doubleColumns as $colName) {
                if (!is_numeric($row[$colName])) {
                    error_log("PV error: Invalid DOUBLE value in $table.$colName, setting to 0.");
                    $row[$colName] = 0;
                }
            }
            $result[$table][] = $row;
        }
    }

    if ($format === 'json') {
        header('Content-Disposition: attachment; filename=user_data.json');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result, JSON_PRETTY_PRINT);
    } elseif ($format === 'sql') {
        $gzipFile = tempnam(sys_get_temp_dir(), 'export_') . '.gz';
        $gz = gzopen($gzipFile, 'w9'); // Gzip with max compression

        gzwrite($gz, "-- SQL Export\n\n");

        foreach ($result as $table => $rows) {
            if (empty($rows)) continue;

            $columnNames = implode(", ", array_keys($rows[0]));

            gzwrite($gz, "INSERT INTO `$table` ($columnNames) VALUES\n");
            $values = [];
            foreach ($rows as $row) {
                $escapedValues = array_map(function ($value) use ($conn) {
                    if (is_null($value)) return 'NULL';
                    if (is_numeric($value) && floatval($value) == $value) return $value;
                    return "'" . mysqli_real_escape_string($conn, $value) . "'";
                }, $row);
                $values[] = '(' . implode(', ', $escapedValues) . ')';
            }
            gzwrite($gz, implode(",\n", $values) . ";\n\n");
        }

        gzclose($gz);

        header('Content-Disposition: attachment; filename=user_data.sql.gz');
        header('Content-Type: application/gzip');
        header('Content-Length: ' . filesize($gzipFile));
        readfile($gzipFile);
        unlink($gzipFile); // Delete temp file after download
    }

    return;
}



//EXPORT FORMULAS JSON
if($_GET['action'] == 'exportFormulas'){
    $filter = " WHERE fm.owner_id = '$userID'";
    if($_GET['fid']){
        $filter .= " AND fm.fid ='".$_GET['fid']."'";
    }

    if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE owner_id = '$userID'")))){
        $msg['error'] = 'No formulas found to export';
        echo json_encode($msg);
        return;
    }

    require_once(__ROOT__.'/func/genFID.php');
    $formulas = 0;
    $ingredients = 0;

    $result = [
        'formulasMetaData' => [],
        'formulas' => [],
        'pvMeta' => [
            'product' => $product,
            'version' => $ver,
            'formulas' => 0,
            'ingredients' => 0,
            'timestamp' => date('d-m-Y H:i:s')
        ]
    ];

    $query = "
        SELECT 
            fm.*, 
            f.name AS formula_name, f.ingredient, f.ingredient_id, f.concentration, f.dilutant, f.quantity, 
            f.exclude_from_summary, f.exclude_from_calculation, f.notes AS formula_notes, f.created_at, f.updated_at
        FROM 
            formulasMetaData fm
        LEFT JOIN 
            formulas f ON fm.fid = f.fid
        $filter
    ";
    $res = mysqli_query($conn, $query);

    $processedFids = [];
    $filename = "All_formulas";

    while($row = mysqli_fetch_assoc($res)){
        if (!isset($processedFids[$row['fid']])) {
            $newfid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz'); // Generate new fid for each formula
            $result['formulasMetaData'][] = [
                'name' => (string)$row['name'],
                'product_name' => (string)$row['product_name'] ?: $row['name'],
                'fid' => (string)$newfid,
                'profile' => (string)$row['profile'],
                'category' => (string)$row['profile'] ?: 'Default',
                'gender' => (string)$row['gender'],
                'notes' => (string)$row['notes'] ?: 'None',
                'created_at' => (string)$row['created_at'],
                'isProtected' => (int)$row['isProtected'] ?: 0,
                'defView' => (int)$row['defView'],
                'catClass' => (string)$row['catClass'],
                'revision' => (int)$row['revision'] ?: 0,
                'finalType' => (int)$row['finalType'] ?: 100,
                'isMade' => (int)$row['isMade'],
                'madeOn' => (string)$row['madeOn'] ?: "0000-00-00 00:00:00",
                'scheduledOn' => (string)$row['scheduledOn'] ?: "0000-00-00 00:00:00",
                'customer_id' => (int)$row['customer_id'],
                'status' => (int)$row['status'],
                'toDo' => (int)$row['toDo'],
                'rating' => (int)$row['rating'] ?: 0
            ];
            $formulas++;
            $processedFids[$row['fid']] = $newfid;

            // Set filename based on the formula name if fid is provided
            if ($_GET['fid']) {
                $filename = $row['name'];
            }
        }

        if ($row['formula_name']) {
            $result['formulas'][] = [
                'fid' => (string)$processedFids[$row['fid']],
                'name' => (string)$row['formula_name'],
                'ingredient' => (string)$row['ingredient'],
                'concentration' => (float)$row['concentration'] ?: 100,
                'dilutant' => (string)$row['dilutant'] ?: 'None',
                'quantity' => (float)$row['quantity'],
                'exclude_from_summary' => (int)$row['exclude_from_summary'],
                'exclude_from_calculation' => (int)$row['exclude_from_calculation'],
                'notes' => (string)$row['formula_notes'] ?: 'None',
                'created_at' => (string)$row['created_at'] ?: "0000-00-00 00:00:00",
                'updated_at' => (string)$row['updated_at'] ?: "0000-00-00 00:00:00"
            ];
            $ingredients++;
        }
    }

    $result['pvMeta']['formulas'] = $formulas;
    $result['pvMeta']['ingredients'] = $ingredients;

    header('Content-disposition: attachment; filename='.preg_replace('/[^a-zA-Z0-9-_]/', '_', $filename).'.json');
    header('Content-type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT);
    return;
}



//EXPORT ACCESSORIES JSON
if ($_GET['format'] === 'json' && $_GET['kind'] === 'accessories') {
    // Validate if there are accessories to export
    $accessoryCheckQuery = "SELECT COUNT(id) AS count FROM inventory_accessories WHERE owner_id = '$userID'";
    $accessoryCheckResult = mysqli_query($conn, $accessoryCheckQuery);
    $accessoryCount = mysqli_fetch_assoc($accessoryCheckResult)['count'] ?? 0;

    if ($accessoryCount === 0) {
        echo json_encode(['error' => 'No accessories found to export']);
        return;
    }

    // Fetch accessory data
    $accessoryQuery = "SELECT * FROM inventory_accessories WHERE owner_id = '$userID'";
    $accessoryResult = mysqli_query($conn, $accessoryQuery);

    $accessories = [];
    while ($row = mysqli_fetch_assoc($accessoryResult)) {
        $accessories[] = [
            'name'           => (string)$row['name'],
            'accessory'      => (string)$row['accessory'],
            'price'          => (double)$row['price'],
            'currency'       => (string)$settings['currency'],
            'supplier'       => (string)$row['supplier'],
            'supplier_link'  => (string)$row['supplier_link'],
            'pieces'         => (int)$row['pieces'],
        ];
    }

    // Add metadata
    $metaData = [
        'product'              => $product,
        'version'              => $ver,
        'inventory_accessories' => $accessoryCount,
        'timestamp'            => date('d-m-Y H:i:s'),
    ];

    // Prepare the result
    $result = [
        'inventory_accessories' => $accessories,
        'pvMeta'                => $metaData,
    ];

    // Send JSON headers and output the result
    header('Content-Disposition: attachment; filename=accessories_inventory.json');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result, JSON_PRETTY_PRINT);
    return;
}



//EXPORT BOTTLES JSON
if ($_GET['format'] === 'json' && $_GET['kind'] === 'bottles') {
    // Validate if there are bottles to export
    $bottleCheckQuery = "SELECT COUNT(id) AS count FROM bottles WHERE owner_id = '$userID'";
    $bottleCheckResult = mysqli_query($conn, $bottleCheckQuery);
    $bottleCount = mysqli_fetch_assoc($bottleCheckResult)['count'] ?? 0;

    if ($bottleCount === 0) {
        echo json_encode(['error' => 'No bottles found to export']);
        return;
    }

    // Fetch bottle data
    $bottleQuery = "SELECT * FROM bottles WHERE owner_id = '$userID'";
    $bottleResult = mysqli_query($conn, $bottleQuery);

    $bottles = [];
    while ($row = mysqli_fetch_assoc($bottleResult)) {
        $bottles[] = [
            'name'           => (string)$row['name'],
            'ml'             => (string)$row['ml'],
            'price'          => (double)$row['price'],
            'currency'       => (string)$settings['currency'],
            'height'         => (double)$row['height'],
            'width'          => (double)$row['width'],
            'weight'         => (double)$row['weight'],
            'diameter'       => (double)$row['diameter'],
            'supplier'       => (string)$row['supplier'],
            'supplier_link'  => (string)$row['supplier_link'],
            'notes'          => (string)$row['notes'],
            'pieces'         => (int)$row['pieces'],
            'created_at'     => (string)$row['created_at'],
            'updated_at'     => (string)$row['updated_at'],
        ];
    }

    // Add metadata
    $metaData = [
        'product'         => $product,
        'version'         => $ver,
        'inventory_bottles' => $bottleCount,
        'timestamp'       => date('d/m/Y H:i:s'),
    ];

    // Prepare the result
    $result = [
        'inventory_bottles' => $bottles,
        'pvMeta'            => $metaData,
    ];

    // Send JSON headers and output the result
    header('Content-Disposition: attachment; filename=bottles_inventory.json');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result, JSON_PRETTY_PRINT);
    return;
}


//EXPORT CUSTOMERS JSON
if ($_GET['format'] === 'json' && $_GET['kind'] === 'customers') {
    // Check if there are any customers to export
    $customerCheckQuery = "SELECT COUNT(id) AS count FROM customers WHERE owner_id = '$userID'";
    $customerCheckResult = mysqli_query($conn, $customerCheckQuery);
    $customerCount = mysqli_fetch_assoc($customerCheckResult)['count'] ?? 0;

    if ($customerCount === 0) {
        echo json_encode(['error' => 'No customers found to export.']);
        return;
    }

    // Fetch customer data
    $customerQuery = "SELECT * FROM customers WHERE owner_id = '$userID'";
    $customerResult = mysqli_query($conn, $customerQuery);

    $customers = [];
    while ($row = mysqli_fetch_assoc($customerResult)) {
        $customers[] = [
            'name'       => (string)$row['name'],
            'address'    => (string)$row['address'],
            'email'      => (string)$row['email'],
            'phone'      => (string)$row['phone'],
            'web'        => (string)$row['web'],
            'owner_id'   => $row['owner_id'],
            'created_at' => (string)$row['created_at'],
            'updated_at' => (string)$row['updated_at'],
        ];
    }

    // Add metadata
    $metaData = [
        'product'            => $product,
        'version'            => $ver,
        'inventory_customers' => $customerCount,
        'timestamp'          => date('d/m/Y H:i:s'),
    ];

    // Prepare the result
    $result = [
        'inventory_customers' => $customers,
        'pvMeta'              => $metaData,
    ];

    // Send JSON headers and output the result
    header('Content-Disposition: attachment; filename=customers_inventory.json');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result, JSON_PRETTY_PRINT);
    return;
}


//EXPORT COMPOUNDS JSON
if ($_GET['format'] === 'json' && $_GET['kind'] === 'inventory_compounds') {
    // Check if there are any compounds to export
    $compoundCheckQuery = "SELECT COUNT(id) AS count FROM inventory_compounds WHERE owner_id = '$userID'";
    $compoundCheckResult = mysqli_query($conn, $compoundCheckQuery);
    $compoundCount = mysqli_fetch_assoc($compoundCheckResult)['count'] ?? 0;

    if ($compoundCount === 0) {
        echo json_encode(['error' => 'No compounds found to export.']);
        return;
    }

    // Fetch compound data
    $compoundQuery = "SELECT * FROM inventory_compounds WHERE owner_id = '$userID'";
    $compoundResult = mysqli_query($conn, $compoundQuery);

    $compounds = [];
    while ($row = mysqli_fetch_assoc($compoundResult)) {
        $compounds[] = [
            'name'        => (string)$row['name'],
            'description' => (string)$row['description'],
            'batch_id'    => (int)$row['batch_id'],
            'size'        => (string)$row['size'],
            'owner_id'    => $row['owner_id'],
            'location'    => (string)$row['location'],
            'label_info'  => (string)$row['label_info'],
            'created_at'  => (string)$row['created_at'],
            'updated_at'  => (string)$row['updated_at'],
        ];
    }

    // Add metadata
    $metaData = [
        'product'             => $product,
        'version'             => $ver,
        'inventory_compounds' => $compoundCount,
        'timestamp'           => date('d/m/Y H:i:s'),
    ];

    // Prepare the result
    $result = [
        'inventory_compounds' => $compounds,
        'pvMeta'              => $metaData,
    ];

    // Send JSON headers and output the result
    header('Content-Disposition: attachment; filename=inventory_compounds.json');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result, JSON_PRETTY_PRINT);
    return;
}



//EXPORT INGREDIENTS CSV
if ($_GET['format'] === 'csv' && $_GET['kind'] === 'ingredients') {
    // Check if there are any ingredients to export
    $ingredientCheckQuery = "SELECT COUNT(id) AS count FROM ingredients WHERE owner_id = '$userID'";
    $ingredientCheckResult = mysqli_query($conn, $ingredientCheckQuery);
    $ingredientCount = mysqli_fetch_assoc($ingredientCheckResult)['count'] ?? 0;

    if ($ingredientCount === 0) {
        echo json_encode(['error' => 'No ingredients found to export.']);
        return;
    }

    // Fetch ingredient data
    $ingredientQuery = "
        SELECT 
            name, INCI, cas, FEMA, type, strength, profile, physical_state, 
            allergen, odor, impact_top, impact_heart, impact_base
        FROM ingredients
        WHERE owner_id = '$userID'
    ";
    $ingredientResult = mysqli_query($conn, $ingredientQuery);

    // Prepare CSV headers
    $csvHeaders = [
        'Name', 'INCI', 'CAS', 'FEMA', 'Type', 'Strength', 
        'Profile', 'Physical State', 'Allergen', 'Odor Description', 
        'Top Note Impact', 'Heart Note Impact', 'Base Note Impact'
    ];

    // Send CSV headers to the browser
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=inventory_ingredients.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, $csvHeaders);

    // Write ingredient data to CSV
    while ($row = mysqli_fetch_assoc($ingredientResult)) {
        fputcsv($output, $row);
    }

    fclose($output);
    return;
}


//EXPORT INGREDIENTS JSON
if ($_GET['format'] == 'json' && $_GET['kind'] == 'ingredients') {
    
    // Check if there are ingredients for the user
    $ingredients_query = mysqli_query($conn, "SELECT id FROM ingredients WHERE owner_id = '$userID'");
    if (mysqli_num_rows($ingredients_query) == 0) {
        echo json_encode(['error' => 'No ingredients found to export.']);
        return;
    }

    $includeSuppliers = $_GET['includeSuppliers'] ?? false;
    $includeCompositions = $_GET['includeCompositions'] ?? false;
    $includeDocuments = $_GET['includeDocuments'] ?? false;

    $ingredients_count = 0;
    $suppliers_count = 0;
    $ing_suppliers_count = 0;
    $ingredient_compounds_count = 0;
    $ingredient_documents_coun = 0;

    // Get ingredients
    $q = mysqli_query($conn, "SELECT * FROM ingredients WHERE owner_id = '$userID'");
    $ing = [];
    while ($res = mysqli_fetch_assoc($q)) {
        $r = [
            'id' => (int) $res['id'],
            'name' => (string) $res['name'],
            'INCI' => (string) $res['INCI'],
            'cas' => (string) $res['cas'],
            'FEMA' => (string) $res['FEMA'],
            'type' => (string) $res['type'],
            'strength' => (string) $res['strength'],
            'category' => (int) $res['category'],
            'purity' => (int) $res['purity'],
            'einecs' => (string) $res['einecs'],
            'reach' => (string) $res['reach'],
            'tenacity' => (string) $res['tenacity'],
            'chemical_name' => (string) $res['chemical_name'],
            'formula' => (string) $res['formula'],
            'flash_point' => (string) $res['flash_point'],
            'notes' => (string) $res['notes'],
            'flavor_use' => (int) $res['flavor_use'],
            'soluble' => (string) $res['soluble'],
            'logp' => (string) $res['logp'],
            'cat1' => (double) $res['cat1'],
            'cat2' => (double) $res['cat2'],
            'cat3' => (double) $res['cat3'],
            'cat4' => (double) $res['cat4'],
            'cat5A' => (double) $res['cat5A'],
            'cat5B' => (double) $res['cat5B'],
            'cat5C' => (double) $res['cat5C'],
            'cat6' => (double) $res['cat6'],
            'cat7A' => (double) $res['cat7A'],
            'cat7B' => (double) $res['cat7B'],
            'cat8' => (double) $res['cat8'],
            'cat9' => (double) $res['cat9'],
            'cat10A' => (double) $res['cat10A'],
            'cat10B' => (double) $res['cat10B'],
            'cat11A' => (double) $res['cat11A'],
            'cat11B' => (double) $res['cat11B'],
            'cat12' => (double) $res['cat12'],
            'profile' => (string) $res['profile'],
            'physical_state' => (int) $res['physical_state'],
            'allergen' => (int) $res['allergen'],
            'odor' => (string) $res['odor'],
            'impact_top' => (int) $res['impact_top'],
            'impact_heart' => (int) $res['impact_heart'],
            'impact_base' => (int) $res['impact_base'],
            'created_at' => (string) $res['created_at'],
            'usage_type' => (string) $res['usage_type'],
            'noUsageLimit' => (int) $res['noUsageLimit'],
            'byPassIFRA' => (int) $res['byPassIFRA'],
            'isPrivate' => (int) $res['isPrivate'],
            'molecularWeight' => (string) $res['molecularWeight']
        ];
        $ingredients_count++;
        $ing[] = $r;
    }
    
    // Get ingredient compounds if requested
    $cmp = [];
    if ($includeCompositions) {
        $q = mysqli_query($conn, "SELECT * FROM ingredient_compounds WHERE owner_id = '$userID'");
        while ($res = mysqli_fetch_assoc($q)) {
            $c = [
                'id' => (int) $res['id'],
                'ing' => (string) $res['ing'],
                'name' => (string) $res['name'],
                'cas' => (string) ($res['cas'] ?: '-'),
                'ec' => (string) ($res['ec'] ?: '-'),
                'min_percentage' => (double) $res['min_percentage'],
                'max_percentage' => (double) $res['max_percentage'],
                'GHS' => (string) $res['GHS'],
                'toDeclare' => (int) $res['toDeclare'],
                'created_at' => (string) $res['created_at']
            ];
            $ingredient_compounds_count++;
            $cmp[] = $c;
        }
    }
    
    // Get suppliers if requested
    $sup = [];
    $ingSup = [];
    if ($includeSuppliers) {
        $q = mysqli_query($conn, "SELECT * FROM suppliers WHERE owner_id = '$userID'");
        while ($res = mysqli_fetch_assoc($q)) {
            $sd = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '" . $res['ingSupplierID'] . "' AND owner_id = '$userID'"));
            $s = [
                'id' => (int) $res['id'],
                'name' => (string) ($sd['name'] ?: 'Unknown'),
                'ingSupplierID' => (int) $res['ingSupplierID'],
                'ingID' => (int) $res['ingID'],
                'supplierLink' => (string) ($res['supplierLink'] ?: '-'),
                'price' => (double) $res['price'],
                'size' => (double) ($res['size'] ?: 10),
                'manufacturer' => (string) ($res['manufacturer'] ?: '-'),
                'preferred' => (int) ($res['preferred'] ?: 0),
                'batch' => (string) ($res['batch'] ?: '-'),
                'purchased' => (string) ($res['purchased'] ?: '-'),
                'mUnit' => (string) ($res['mUnit'] ?: '-'),
                'stock' => (double) ($res['stock'] ?: 0),
                'status' => (int) ($res['status'] ?: 1),
                'created_at' => (string) $res['created_at'],
                'updated_at' => (string) $res['updated_at'],
                'supplier_sku' => (string) ($res['supplier_sku'] ?: '-'),
                'internal_sku' => (string) ($res['internal_sku'] ?: '-'),
                'storage_location' => (string) ($res['storage_location'] ?: '-')
            ];
            $sup[] = $s;
            $suppliers_count++;
        }
        
        // Get ingredient suppliers
        $qs = mysqli_query($conn, "SELECT * FROM ingSuppliers WHERE owner_id = '$userID'");
        while ($res_sup = mysqli_fetch_assoc($qs)) {
            $is = [
                'id' => (int) $res_sup['id'],
                'name' => (string) $res_sup['name'],
                'address' => (string) ($res_sup['address'] ?: '-'),
                'po' => (string) ($res_sup['po'] ?: '-'),
                'country' => (string) ($res_sup['country'] ?: '-'),
                'telephone' => (string) ($res_sup['telephone'] ?: '-'),
                'url' => (string) ($res_sup['url'] ?: '-'),
                'email' => (string) ($res_sup['email'] ?: '-')
            ];
            $ingSup[] = $is;
            $ing_suppliers_count++;
        }
    }

    //Get ingredient documents
    $docs = [];
    if ($includeDocuments) {
        try {
            $memoryLimit = ini_get('memory_limit');
            $memoryLimitBytes = (int)str_replace('M', '', $memoryLimit) * 1024 * 1024;
            $memoryUsageBefore = memory_get_usage();
            $memoryAvailable = $memoryLimitBytes - $memoryUsageBefore;

            $q = mysqli_query($conn, "SELECT * FROM documents WHERE type = 1 AND owner_id = '$userID'");
            while ($res = mysqli_fetch_assoc($q)) {
                $d = [
                    'ownerID' => (int) $res['ownerID'],
                    'type' => (int) $res['type'],
                    'name' => (string) $res['name'],
                    'notes' => (string) ($res['notes'] ?: '-'),
                    'docData' => base64_encode($res['docData']),
                    'isBatch' => (int) $res['isBatch'],
                    'isSDS' => (int) $res['isSDS'],
                    'updated_at' => (string) $res['updated_at'],
                    'created_at' => (string) $res['created_at']
                ];
                $ingredient_documents_count++;
                $docs[] = $d;

                $memoryUsageAfter = memory_get_usage();
                if ($memoryUsageAfter - $memoryUsageBefore > $memoryAvailable * 0.9) {
                    ini_set('memory_limit', (int)($memoryLimitBytes / (1024 * 1024) + 10) . 'M'); // Increase memory limit by 10MB
                    $memoryLimitBytes = (int)str_replace('M', '', ini_get('memory_limit')) * 1024 * 1024;
                    $memoryAvailable = $memoryLimitBytes - $memoryUsageAfter;
                }
            }
        } catch (Exception $e) {
            error_log("Memory error: " . $e->getMessage());
            echo json_encode(['error' => $e->getMessage(), 'memoryAvailable' => $memoryAvailable]);
            return;
        }
    }


    // Metadata
    $vd = [
        'product' => $product,
        'version' => $ver,
        'ingredients' => $ingredients_count,
        'suppliers' => $ing_suppliers_count,
        'ingredient_compounds' => $ingredient_compounds_count,
        'documents' => $ingredient_documents_count,
        'timestamp' => date('d/m/Y H:i:s')
    ];

    // Compile result
    $result = [
        'ingredients' => $ing,
        'compositions' => $cmp,
        'suppliers' => $sup,
        'ingSuppliers' => $ingSup,
        'documents' => $docs,
        'pvMeta' => $vd
    ];

    // Output as JSON
    header('Content-disposition: attachment; filename=ingredients.json');
    header('Content-type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT);
    return;
}


//EXPORT SINGLE INGREDIENT
if ($_GET['format'] === 'json' && $_GET['kind'] === 'single-ingredient' && isset($_GET['id'])) {
    $ingredientID = mysqli_real_escape_string($conn, $_GET['id']);

    // Validate ingredient existence
    $ingredientCheckQuery = "SELECT id FROM ingredients WHERE id='$ingredientID' AND owner_id='$userID'";
    if (!mysqli_num_rows(mysqli_query($conn, $ingredientCheckQuery))) {
        echo json_encode(['error' => 'No ingredients found to export.']);
        return;
    }

    // Initialize counts and arrays
    $ingredientCount = 0;
    $suppliersCount = 0;
    $ingredientCompoundsCount = 0;
    $ingredientSuppliersCount = 0;

    $ingredients = [];
    $compositions = [];
    $suppliers = [];
    $ingredientSuppliers = [];

    // Fetch the ingredient details
    $ingredientQuery = "SELECT * FROM ingredients WHERE id='$ingredientID' AND owner_id='$userID'";
    $ingredientResult = mysqli_query($conn, $ingredientQuery);

    while ($row = mysqli_fetch_assoc($ingredientResult)) {
        $ingredients[] = [
            'id'             => (int)$row['id'],
            'name'           => (string)$row['name'],
            'INCI'           => (string)($row['INCI'] ?: '-'),
            'cas'            => (string)$row['cas'],
            'FEMA'           => (string)($row['FEMA'] ?: '-'),
            'type'           => (string)$row['type'],
            'strength'       => (string)$row['strength'],
            'category'       => (int)$row['category'],
            'purity'         => (int)$row['purity'],
            'einecs'         => (string)($row['einecs'] ?: '-'),
            'reach'          => (string)($row['reach'] ?: '-'),
            'tenacity'       => (string)($row['tenacity'] ?: '-'),
            'chemical_name'  => (string)($row['chemical_name'] ?: '-'),
            'formula'        => (string)($row['formula'] ?: '-'),
            'flash_point'    => (string)($row['flash_point'] ?: '-'),
            'notes'          => (string)($row['notes'] ?: '-'),
            'flavor_use'     => (int)$row['flavor_use'],
            'soluble'        => (string)($row['soluble'] ?: '-'),
            'logp'           => (string)($row['logp'] ?: '-'),
            'profile'        => (string)$row['profile'],
            'physical_state' => (int)$row['physical_state'],
            'allergen'       => (int)$row['allergen'],
            'odor'           => (string)$row['odor'],
            'impact_top'     => (int)$row['impact_top'],
            'impact_heart'   => (int)$row['impact_heart'],
            'impact_base'    => (int)$row['impact_base'],
            'created_at'     => (string)$row['created_at'],
            'usage_type'     => (string)$row['usage_type'],
            'noUsageLimit'   => (int)$row['noUsageLimit'],
            'byPassIFRA'     => (int)$row['byPassIFRA'],
            'isPrivate'      => (int)$row['isPrivate'],
            'molecularWeight'=> (string)($row['molecularWeight'] ?: '-'),
            'cat1' => (double)($row['cat1'] ?? 100),
            'cat2' => (double)($row['cat2'] ?? 100),
            'cat3' => (double)($row['cat3'] ?? 100),
            'cat4' => (double)($row['cat4'] ?? 100),
            'cat5A' => (double)($row['cat5A'] ?? 100),
            'cat5B' => (double)($row['cat5B'] ?? 100),
            'cat5C' => (double)($row['cat5C'] ?? 100),
            'cat6' => (double)($row['cat6'] ?? 100),
            'cat7A' => (double)($row['cat7A'] ?? 100),
            'cat7B' => (double)($row['cat7B'] ?? 100),
            'cat8' => (double)($row['cat8'] ?? 100),
            'cat9' => (double)($row['cat9'] ?? 100),
            'cat10A' => (double)($row['cat10A'] ?? 100),
            'cat10B' => (double)($row['cat10B'] ?? 100),
            'cat11A' => (double)($row['cat11A'] ?? 100),
            'cat11B' => (double)($row['cat11B'] ?? 100),
            'cat12' => (double)($row['cat12'] ?? 100),
        ];
        $ingredientCount++;
    }

    // Fetch ingredient compounds
    $compoundQuery = "SELECT * FROM ingredient_compounds WHERE ing='{$ingredients[0]['name']}' AND owner_id='$userID'";
    $compoundResult = mysqli_query($conn, $compoundQuery);

    while ($row = mysqli_fetch_assoc($compoundResult)) {
        $compositions[] = [
            //'id'            => (int)$row['id'],
            'ing'           => (string)$row['ing'],
            'name'          => (string)$row['name'],
            'cas'           => (string)($row['cas'] ?: '-'),
            'ec'            => (string)($row['ec'] ?: '-'),
            'min_percentage'=> (double)$row['min_percentage'],
            'max_percentage'=> (double)$row['max_percentage'],
            'GHS'           => (string)$row['GHS'],
            'toDeclare'     => (int)$row['toDeclare'],
            'created_at'    => (string)$row['created_at'],
        ];
        $ingredientCompoundsCount++;
    }

    // Fetch suppliers
    $supplierQuery = "SELECT * FROM suppliers WHERE ingID='{$ingredients[0]['id']}' AND owner_id='$userID'";
    $supplierResult = mysqli_query($conn, $supplierQuery);

    while ($row = mysqli_fetch_assoc($supplierResult)) {
        $suppliers[] = [
           // 'id'              => (int)$row['id'],
            'ingSupplierID'   => (int)$row['ingSupplierID'],
            'ingID'           => (int)$row['ingID'],
            'supplierLink'    => (string)($row['supplierLink'] ?: '-'),
            'price'           => (double)$row['price'],
            'size'            => (double)($row['size'] ?: 10),
            'manufacturer'    => (string)($row['manufacturer'] ?: '-'),
            'preferred'       => (int)($row['preferred'] ?: 0),
            'batch'           => (string)($row['batch'] ?: '-'),
            'purchased'       => (string)($row['purchased'] ?: '-'),
            'mUnit'           => (string)($row['mUnit'] ?: '-'),
            'stock'           => (double)($row['stock'] ?: 0),
            'status'          => (int)($row['status'] ?: 1),
            'created_at'      => (string)$row['created_at'],
            'updated_at'      => (string)$row['updated_at'],
            'supplier_sku'    => (string)($row['supplier_sku'] ?: '-'),
            'internal_sku'    => (string)($row['internal_sku'] ?: '-'),
            'storage_location'=> (string)($row['storage_location'] ?: '-'),
        ];
        $suppliersCount++;

        // Fetch ingredient supplier details
        $ingSupplierQuery = "SELECT * FROM ingSuppliers WHERE id='{$row['ingSupplierID']}' AND owner_id='$userID'";
        $ingSupplierResult = mysqli_query($conn, $ingSupplierQuery);

        while ($ingRow = mysqli_fetch_assoc($ingSupplierResult)) {
            $ingredientSuppliers[] = [
                'id'        => (int)$ingRow['id'],
                'name'      => (string)$ingRow['name'],
                'address'   => (string)($ingRow['address'] ?: '-'),
                'po'        => (string)($ingRow['po'] ?: '-'),
                'country'   => (string)($ingRow['country'] ?: '-'),
                'telephone' => (string)($ingRow['telephone'] ?: '-'),
                'url'       => (string)($ingRow['url'] ?: '-'),
                'email'     => (string)($ingRow['email'] ?: '-'),
            ];
            $ingredientSuppliersCount++;
        }
    }

    // Metadata
    $metaData = [
        'product'              => $product,
        'version'              => $ver,
        'ingredients'          => $ingredientCount,
        'suppliers'            => $ingredientSuppliersCount,
        'ingredient_compounds' => $ingredientCompoundsCount,
        'timestamp'            => date('d/m/Y H:i:s'),
    ];

    // Final result
    $result = [
        'ingredients'   => $ingredients,
        'compositions'  => $compositions,
        'suppliers'     => $suppliers,
        'ingSuppliers'  => $ingredientSuppliers,
        'pvMeta'        => $metaData,
    ];

    // Output JSON
    header('Content-Disposition: attachment; filename=' . $ingredients[0]['name'] . '.json');
    header('Content-Type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT);
    return;
}


//EXPORT SUPPLIERS JSON
if ($_GET['format'] === 'json' && $_GET['kind'] === 'suppliers') {
    // Check if there are any suppliers in the database
    $supplierCheckQuery = "SELECT COUNT(id) AS count FROM ingSuppliers WHERE owner_id = '$userID'";
    $supplierCheckResult = mysqli_query($conn, $supplierCheckQuery);
    $supplierCount = mysqli_fetch_assoc($supplierCheckResult)['count'] ?? 0;

    if ($supplierCount === 0) {
        echo json_encode(['error' => 'No suppliers found to export.']);
        return;
    }

    // Fetch suppliers and their materials count
    $supplierQuery = "
        SELECT 
            s.id, s.name, s.address, s.po, s.country, s.telephone, s.url, s.email, s.platform, 
            s.price_tag_start, s.price_tag_end, s.add_costs, s.price_per_size, s.notes, 
            s.min_ml, s.min_gr, 
            (SELECT COUNT(id) FROM suppliers WHERE ingSupplierID = s.id AND owner_id = '$userID') AS materials
        FROM ingSuppliers s
        WHERE s.owner_id = '$userID'
    ";
    $supplierResult = mysqli_query($conn, $supplierQuery);

    $suppliers = [];
    while ($row = mysqli_fetch_assoc($supplierResult)) {
        $suppliers[] = [
            'name' => (string)$row['name'],
            'address' => (string)$row['address'],
            'po' => (string)$row['po'],
            'country' => (string)$row['country'],
            'telephone' => (string)$row['telephone'],
            'url' => (string)$row['url'],
            'email' => (string)$row['email'],
            'platform' => (string)$row['platform'],
            'price_tag_start' => (string)$row['price_tag_start'],
            'price_tag_end' => (string)$row['price_tag_end'],
            'add_costs' => (double)$row['add_costs'],
            'price_per_size' => (int)$row['price_per_size'],
            'notes' => (string)$row['notes'],
            'min_ml' => (double)$row['min_ml'],
            'min_gr' => (double)$row['min_gr'],
            'materials' => (int)$row['materials'],
        ];
    }

    // Prepare metadata
    $meta = [
        'product' => $product,
        'version' => $ver,
        'inventory_suppliers' => count($suppliers),
        'timestamp' => date('d/m/Y H:i:s'),
    ];

    // Prepare the response
    $response = [
        'inventory_suppliers' => $suppliers,
        'pvMeta' => $meta,
    ];

    // Output the response as a JSON file
    header('Content-Disposition: attachment; filename=suppliers_inventory.json');
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    return;
}


//EXPORT SUPPLIERS MATERIALS
if ($_GET['format'] === 'json' && $_GET['kind'] === 'supplier-materials' && isset($_GET['id'])) {
    // Sanitize inputs
    $supplierID = mysqli_real_escape_string($conn, $_GET['id']);
    $supplierName = isset($_GET['supplier-name']) ? mysqli_real_escape_string($conn, $_GET['supplier-name']) : 'unknown_supplier';

    // Check if data exists
    $supplierCheckQuery = "SELECT ingID FROM suppliers WHERE ingSupplierID = '$supplierID' AND owner_id = '$userID'";
    $supplierCheckResult = mysqli_query($conn, $supplierCheckQuery);

    if (mysqli_num_rows($supplierCheckResult) === 0) {
        echo json_encode(['error' => 'No data found to export.']);
        return;
    }

    // Fetch ingredients data
    $ingredientQuery = "
        SELECT i.id, i.name, i.cas, i.created_at, i.odor
        FROM suppliers s
        JOIN ingredients i ON s.ingID = i.id
        WHERE s.ingSupplierID = '$supplierID' AND s.owner_id = '$userID' AND i.owner_id = '$userID'
    ";
    $ingredientResult = mysqli_query($conn, $ingredientQuery);

    $ingredients = [];
    while ($row = mysqli_fetch_assoc($ingredientResult)) {
        $ingredients[] = [
            'name' => (string)$row['name'],
            'cas' => (string)($row['cas'] ?: '-'),
            'odor' => (string)($row['odor'] ?: '-'),
            'created_at' => (string)($row['created_at'] ?: '-'),
        ];
    }

    // Prepare metadata
    $meta = [
        'product' => $product,
        'version' => $ver,
        'ingredients' => count($ingredients),
        'supplier' => $supplierName,
        'timestamp' => date('d/m/Y H:i:s'),
    ];

    // Prepare the response
    $response = [
        'supplier_materials' => $ingredients,
        'pvMeta' => $meta,
    ];

    // Output the response as a JSON file
    header('Content-Disposition: attachment; filename=' . $supplierName . '_materials.json');
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    return;
}


?>

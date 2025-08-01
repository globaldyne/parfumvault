<?php
define('__ROOT__', dirname(dirname(__FILE__))); 
global $conn, $userID , $settings, $user_settings, $system_settings;


require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/ml2L.php');
require_once(__ROOT__.'/func/countElement.php');


$action = isset($_GET['action']) ? $_GET['action'] : 'meta';
$row = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10000;

$order_by = isset($_POST['order_by']) ? mysqli_real_escape_string($conn, $_POST['order_by']) : 'name';
$order_as = isset($_POST['order_as']) ? mysqli_real_escape_string($conn, $_POST['order_as']) : 'ASC';
$extra = "ORDER BY toAdd DESC, $order_by $order_as";

$search = isset($_POST['search']['value']) ? trim(mysqli_real_escape_string($conn, $_POST['search']['value'])) : '';
$table = "makeFormula";
$fid = isset($_GET['fid']) ? mysqli_real_escape_string($conn, $_GET['fid']) : '';

if (isset($_GET['qStep'])) {
    $settings['qStep'] = $_GET['qStep'];
}

$filter = $search !== '' ? " AND (ingredient LIKE '%$search%') AND owner_id = '$userID'" : " AND owner_id = '$userID' ";

$response = ['data' => [], 'meta' => []];
$mg = ['total_mg' => 0, 'total_mg_left' => 0];
$rx = [];

switch ($action) {
    case 'compose':
        $q = mysqli_query($conn, "SELECT * FROM $table WHERE fid = '$fid' $filter $extra LIMIT $row, $limit");
        while ($res = mysqli_fetch_assoc($q)) {
            $rs[] = $res;
        }

        $rsq = mysqli_fetch_all(mysqli_query($conn, "SELECT quantity FROM $table WHERE fid = '$fid' AND owner_id = '$userID'"), MYSQLI_ASSOC);
        $rsL = mysqli_fetch_all(mysqli_query($conn, "SELECT quantity FROM $table WHERE fid = '$fid' AND toAdd = 1 AND skip = 0 AND owner_id = '$userID'"), MYSQLI_ASSOC);
        
        foreach ($rs as $rq) {
            $ingredient = mysqli_fetch_assoc(mysqli_query($conn, "SELECT cas, notes FROM ingredients WHERE name = '" . mysqli_real_escape_string($conn, $rq['ingredient']) . "' AND owner_id = '$userID'"));
            $replacement = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM ingredients WHERE id = '" . (int)$rq['replacement_id'] . "' AND owner_id = '$userID'"));

            // Fetch all suppliers for this ingredient
            $sup = [];
            $q = mysqli_query($conn, "SELECT * FROM suppliers WHERE ingID='" . (int)$rq['ingredient_id'] . "' AND owner_id = '$userID'");
            while ($res = mysqli_fetch_array($q)) {
                $sup[] = $res;
            }

            $suppliers = [];
            $total_supplier_stock = 0;
            $preferred_munit = null;
            foreach ($sup as $supplierRow) {
                $supplierNameRow = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '" . (int)$supplierRow['ingSupplierID'] . "' AND owner_id = '$userID'"));
                $stock = isset($supplierRow['stock']) ? (float)$supplierRow['stock'] : 0;
                $suppliers[] = [
                    'id' => (string)$supplierRow['ingSupplierID'],
                    'ingID' => (string)$supplierRow['ingID'],
                    'name' => isset($supplierNameRow['name']) ? (string)$supplierNameRow['name'] : '',
                    'price' => (float)$supplierRow['price'],
                    'mUnit' => (string)$supplierRow['mUnit'],
                    'size' => (string)$supplierRow['size'],
                    'supplierLink' => (string)$supplierRow['supplierLink'],
                    'stock' => $stock,
                    'preferred' => (int)$supplierRow['preferred']
                ];
                $total_supplier_stock += $stock;
                if ((int)$supplierRow['preferred'] === 1 && !$preferred_munit) {
                    $preferred_munit = (string)$supplierRow['mUnit'];
                }
            }

            // Fallback to first supplier's mUnit if no preferred found
            if (!$preferred_munit && isset($suppliers[0]['mUnit'])) {
                $preferred_munit = $suppliers[0]['mUnit'];
            }

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
                'quantity' => (float)$rq['quantity'],
                'originalQuantity' => (float)$rq['originalQuantity'],
                'overdose' => (float)$rq['overdose'],
                'inventory' => [
                    'suppliers' => $suppliers,
                    'total_supplier_stock' => $total_supplier_stock,
                    'mUnit' => $preferred_munit ?: ($settings['mUnit'] ?? 'ml')
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
        break;

    case 'updatequantity':
        if (empty($_GET['quantity_added']) || empty($_GET['ingID'])) {
            echo json_encode(['error' => 'Missing required parameters']);
            return;
        }
        if (!is_numeric($_GET['quantity_added']) || !is_numeric($_GET['quantity_requested'])) {
            echo json_encode(['error' => 'Invalid amount value']);
            return;
        }
        $quantity_added = (float)$_GET['quantity_added'];
        $quantity_requested = (float)$_GET['quantity_requested'];
        $id = (int)$_GET['id'];

        if ($_GET['repID']) {
            $repID = $_GET['repID'];
            $ingID = $_GET['repID'];
        } else {
            $repID = 0;
            $ingID = $_GET['ingID'];
        }

        $ingredient = mysqli_real_escape_string($conn, $_GET['repName'] ?: $_GET['ingredient_name']);
        $notes = mysqli_real_escape_string($conn, $_GET['notes']) ?: "-";

        if ($_GET['update_stock'] == "true") {
            if (!($_GET['supplier_id'])) {
                echo json_encode(['error' => 'Please select a supplier']);
                return;
            }
            $supplier_id = (int)$_GET['supplier_id'];
            $ing_supplier_id = (int)$_GET['ing_supplier_id'];
            $getStock = mysqli_fetch_array(mysqli_query($conn, "SELECT stock, mUnit FROM suppliers WHERE ingID = '$ing_supplier_id' AND ingSupplierID = '" . $supplier_id . "' AND owner_id = '$userID'"));
           /*
            if ($getStock['stock'] < $quantity_requested) {
                echo json_encode(['success' => "Amount exceeds quantity available in stock (" . $getStock['stock'] . $getStock['mUnit'] . ")."]);
                return;
            }
         */
            mysqli_query($conn, "UPDATE suppliers SET stock = stock - $quantity_added WHERE ingID = '$ing_supplier_id' AND ingSupplierID = '" . $supplier_id . "' AND owner_id = '$userID'");
        }

        if ($quantity_added == $quantity_requested) {
            if (mysqli_query($conn, "UPDATE makeFormula SET replacement_id = '$repID', toAdd = 0, notes = '$notes' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")) {
                error_log("UPDATE makeFormula SET replacement_id = '$repID', toAdd = 0, notes = '$notes' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'");
                echo json_encode(['success' => $ingredient . ' added in the formula']);
            } else {
                echo json_encode(['error' => mysqli_error($conn)]);
            }
            return;
        } else {
            $sub_tot =  $quantity_requested - $quantity_added;
            if (mysqli_query($conn, "UPDATE makeFormula SET  replacement_id = '$repID', quantity='$sub_tot', notes = '$notes' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")) {
                error_log("UPDATE makeFormula SET  replacement_id = '$repID', quantity='$sub_tot', notes = '$notes' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'");
                echo json_encode(['success' => 'Formula updated with ' . $ingredient . ' and ' . $sub_tot . ' left']);
            } else {
                echo json_encode(['error' => mysqli_error($conn)]);
            }
            return;
        }

        if ($quantity_added < $quantity_requested) {
            if (mysqli_query($conn, "UPDATE makeFormula SET overdose = '$quantity_requested' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")) {
                echo json_encode(['success' => $_POST['ing'] . ' is overdosed, ' . $quantity_requested . ' added']);
            } else {
                echo json_encode(['error' => mysqli_error($conn)]);
            }
            return;
        }

        if (!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid' AND toAdd = 1 AND owner_id = '$userID'"))) {
            echo json_encode(['success' => 'All materials added. You should mark formula as complete now']);
            return;
        }

        // fallback error
        echo json_encode(['error' => 'Unknown error']);
        return;

        break;

    case 'skipMaterial':

        if (empty($_GET['id'])) {
            echo json_encode(['error' => 'Missing required parameters']);
            return;
        }
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        $notes = mysqli_real_escape_string($conn, $_GET['notes']) ?: "-";
        $skipQuery = "UPDATE makeFormula SET skip = '1', notes = '$notes' WHERE id = '$id' AND fid = '$fid' AND owner_id = '$userID'";
        if(mysqli_query($conn, $skipQuery)){
            echo json_encode(['success' => ($_POST['ing'] ?? 'Ingredient').' skipped from the formulation']);
            return;
        } else {
            echo json_encode(['error' => 'Error skipping the ingredient']);
            return;
        }
        // No meta/data in skipMaterial response
        break;

    case 'undoMaterial':
        // Required: id, originalQuantity, ingID, repName, resetStock, supplier, repID
        if (empty($_GET['id']) || !isset($_GET['originalQuantity'])) {
            echo json_encode(['error' => 'Missing required parameters']);
            return;
        }

        $id = mysqli_real_escape_string($conn, $_GET['id']);
        $ingID = mysqli_real_escape_string($conn, $_GET['ingID']);

        $originalQuantity = (float)$_GET['originalQuantity'];
        $resetStock = $_GET['resetStock'] ?? '';
        $supplier = $_GET['supplier_id'] ?? '';

        // Reset the formula row
        $update = mysqli_query($conn, "UPDATE makeFormula SET replacement_id = 0, toAdd = 1, skip = 0, overdose = 0, quantity = '$originalQuantity' WHERE id = '$id' AND fid = '$fid' AND owner_id = '$userID'");
        if ($update) {
            $msg =  "Ingredient's quantity reset.";
            $response = ['success' => $msg];

            // Optionally reset stock
            if ($resetStock === "true") {
                if (!$supplier) {
                    echo json_encode(['error' => 'Please select a supplier']);
                    return;
                }
                $nIngID = $repID ?: $ingID;
                mysqli_query($conn, "UPDATE suppliers SET stock = stock + $originalQuantity WHERE ingID = '$nIngID' AND ingSupplierID = '$supplier' AND owner_id = '$userID'");
                $response['success'] .= " Stock increased by ".$originalQuantity.($settings['mUnit'] ?? 'ml');
            }
            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'Failed to reset material.']);
        }
        return;

    case 'markComplete':
        if( empty($_GET['fid']) ){
            echo json_encode(['error' => 'Missing fid parameter']);
            return;
        }
        require_once(__ROOT__.'/libs/fpdf.php');
        require_once(__ROOT__.'/func/genBatchID.php');
        require_once(__ROOT__.'/func/genBatchPDF.php');
        require_once(__ROOT__.'/func/ml2L.php');

        $fid = mysqli_real_escape_string($conn, $_GET['fid']);
        $total_quantity = mysqli_real_escape_string($conn, $_GET['totalQuantity'] ?: 100);

        define('FPDF_FONTPATH',__ROOT__.'/fonts');
        // Ensure defCatClass is never null
        $defCatClass = isset($settings['defCatClass']) && $settings['defCatClass'] !== null && $settings['defCatClass'] !== '' ? $settings['defCatClass'] : 'cat4';

        //error_log("Marking formula $fid as complete with total quantity $total_quantity - $defCatClass");
        // Check if already marked as made
        $meta = mysqli_fetch_assoc(mysqli_query($conn, "SELECT isMade FROM formulasMetaData WHERE fid = '$fid' AND owner_id = '$userID'"));
        if ($meta && $meta['isMade'] == '1') {
            echo json_encode(['error' => 'Formula is already marked as complete']);
            return;
        }

        if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid' AND toAdd = '1' AND skip = '0' AND owner_id = '$userID'"))){
            echo json_encode(['error' => 'Formula is pending materials to add, cannot be marked as complete']);
            return;
        }
        if(mysqli_query($conn,"UPDATE formulasMetaData SET isMade = '1', toDo = '0', madeOn = NOW(), status = '2' WHERE fid = '$fid' AND owner_id = '$userID'")){
            $batchID = genBatchID();
            genBatchPDF($fid,$batchID,$total_quantity,'100',$total_quantity,$defCatClass,$settings['qStep'] ?: 2,$settings['defPercentage'] ?: 100,'makeFormula');
            mysqli_query($conn, "DELETE FROM makeFormula WHERE fid = '$fid' AND owner_id = '$userID'");
            echo json_encode(['success' => 'Formula is complete']);
        }
    return;

    case 'getAIReplacementSuggestions':
        if (empty($_GET['ingredient'])) {
            echo json_encode(['error' => 'Missing required parameters']);
            return;
        }

        $ingredient = mysqli_real_escape_string($conn, $_GET['ingredient']);
        $prompt = "Suggest 5 replacements for the ingredient $ingredient";

        require_once(__ROOT__.'/func/pvAIHelper.php');
        $result = pvAIHelper($prompt);

        error_log("AI Replacement Prompt: $prompt");
        error_log("AI Replacement Result: " . json_encode($result));

        if (isset($result['error'])) {
            echo json_encode(['error' => $result['error']]);
            return;
        }

        // Expecting: $result['success']['replacements'] is an array, $result['type'] === 'replacements'
        $replacements = [];
        if (
            isset($result['success']['replacements']) &&
            is_array($result['success']['replacements']) &&
            isset($result['type']) && $result['type'] === 'replacements'
        ) {
            $replacements = $result['success']['replacements'];
        }

        // Enrich each suggestion with inventory info
        foreach ($replacements as &$suggestion) {
            // Support both 'name' and 'ingredient' keys for compatibility
            $ingredient_raw = $suggestion['name'] ?? $suggestion['ingredient'] ?? '';
            $safe_ingredient = mysqli_real_escape_string($conn, $ingredient_raw);
            // Remove any "(CAS ...)" from the name
            $ingredient_name = trim(preg_replace('/\s*\(CAS.*$/i', '', $safe_ingredient));
            $ingredient_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$ingredient_name' AND owner_id = '$userID' LIMIT 1"));

            if ($ingredient_data) {
                $ingredient_id = (int)$ingredient_data['id'];
                $inventory = mysqli_query($conn, "
                    SELECT ingSupplierID, stock, mUnit 
                    FROM suppliers 
                    WHERE ingID = '$ingredient_id' AND owner_id = '$userID'
                ");
                $total_supplier_stock = 0;
                $mUnit = '';
                while ($inv = mysqli_fetch_assoc($inventory)) {
                    $total_supplier_stock += (float)$inv['stock'];
                    if (!$mUnit && !empty($inv['mUnit'])) {
                        $mUnit = $inv['mUnit'];
                    }
                }
                $suggestion['inventory'] = [
                    'stock' => $total_supplier_stock,
                    'mUnit' => $mUnit
                ];
                $suggestion['total_supplier_stock'] = $total_supplier_stock;
            } else {
                $suggestion['inventory'] = [
                    'stock' => 0,
                    'mUnit' => ''
                ];
                $suggestion['total_supplier_stock'] = 0;
            }
            // Always set 'name' for frontend display
            $suggestion['name'] = $ingredient_raw;
        }

        echo json_encode([
            'success' => [
                'replacements' => $replacements,
                'type' => 'replacements'
            ],
            'type' => 'replacements'
        ]);
        return;

    case 'delete':
        // Ensure 'fid' is provided
        if (empty($fid)) {
            echo json_encode(['error' => 'Formula ID is required for deletion.']);
            return;
        }

        // Delete action should set formula to toDo = 0 and isMade = 0
        $updateMeta = mysqli_query($conn, "UPDATE formulasMetaData SET toDo = '0', isMade = '0' WHERE fid = '$fid' AND owner_id = '$userID'");
        if (!$updateMeta) {
            echo json_encode(['error' => 'Failed to update formula metadata.']);
            return;
        }
        //$deleteFormula = mysqli_query($conn, "DELETE FROM $table WHERE fid = '$fid' AND owner_id = '$userID'");
        //$deleteMeta = mysqli_query($conn, "DELETE FROM formulasMetaData WHERE fid = '$fid' AND owner_id = '$userID'");

        if ($updateMeta) {
            echo json_encode(['success' => 'Formula removed from the making queue.']);
        } else {
            echo json_encode(['error' => 'Failed to remove the formula from the queue. Please try again.']);
        }
        return;

    case 'meta':
    default:
        $filter = $search !== '' ? " AND (name LIKE '%$search%') AND owner_id = '$userID'" : " AND owner_id = '$userID' ";
        $q = mysqli_query($conn, "SELECT id, fid, name, madeOn, scheduledOn, toDo AS toAdd FROM formulasMetaData WHERE toDo = '1' $filter $extra LIMIT $row, $limit");
        
        while ($res = mysqli_fetch_assoc($q)) {
            $r = [
                'id' => (int)$res['id'],
                'fid' => (string)$res['fid'],
                'name' => (string)$res['name'],
                'total_ingredients' => (int)countElement("$table","fid = '" . mysqli_real_escape_string($conn, $res['fid']) . "'"),
                'total_ingredients_left' => (int)countElement("$table", "fid = '" . mysqli_real_escape_string($conn, $res['fid']) . "' AND toAdd = 1 AND skip = 0"),
                'toAdd' => (int)$res['toAdd'],
                'scheduledOn' => (string)$res['scheduledOn'],
                'madeOn' => (string)($res['madeOn'] ?? 'In progress')
            ];

            $rx[] = $r;
        }

        $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM formulasMetaData WHERE toDo = '1' AND owner_id = '$userID'"));
        $filtered = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS entries FROM formulasMetaData WHERE toDo = '1' $filter"));
        break;
}

$response = array_merge($response, [
    "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
    "recordsTotal" => (int)$total['entries'],
    "recordsFiltered" => (int)$filtered['entries'],
    "data" => $rx
]);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;
?>

?>
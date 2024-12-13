<?php
if (!defined('pvault_panel')){ die('Not Found');}
global $conn;

if ($fid = mysqli_real_escape_string($conn, $_REQUEST['fid'])) {
    $sql = mysqli_query($conn, "SELECT id, fid, name, product_name, notes, finalType AS concentration, status, created_at, isProtected, rating, profile, src, customer_id, revision, madeOn FROM formulasMetaData WHERE fid = '$fid'");
} else {
    $sql = mysqli_query($conn, "SELECT id, fid, name, product_name, notes, finalType AS concentration, status, created_at, isProtected, rating, profile, src, customer_id, revision, madeOn FROM formulasMetaData");
}

$rows = ["formulas" => []];

if ($sql && mysqli_num_rows($sql) > 0) {
    while ($r = mysqli_fetch_assoc($sql)) {
        $C = date_format(date_create($r['created_at']), "Y-m-d H:i:s");
        $I = mysqli_fetch_array(mysqli_query($conn, "SELECT docData FROM documents WHERE ownerID = '" . $r['id'] . "' AND type = '2'"));
        $sql_ing = mysqli_query($conn, "SELECT ingredient AS name, fid, ingredient, concentration, dilutant, quantity, notes FROM formulas WHERE fid = '" . $r['fid'] . "'");

        $formula = [
            'fid' => (string)$r['fid'],
            'name' => (string)$r['name'],
            'product_name' => (string)$r['product_name'] ?: "Not Set",
            'notes' => (string)$r['notes'] ?: "-",
            'concentration' => (int)$r['concentration'] ?: 100,
            'status' => (int)$r['status'] ?: 0,
            'created_at' => (string)$C ?: "-",
            'isProtected' => (int)$r['isProtected'] ?: 0,
            'rating' => (int)$r['rating'] ?: 0,
            'profile' => (string)$r['profile'] ?: "Default",
            'src' => (int)$r['src'] ?: 0,
            'customer_id' => (int)$r['customer_id'] ?: 0,
            'revision' => (int)$r['revision'] ?: 0,
            'madeOn' => (string)$r['madeOn'] ?: "-",
            'image' => (string)$I['docData'] ?: $defImage,
            'ingredients' => []
        ];

        if ($sql_ing && mysqli_num_rows($sql_ing) > 0) {
            while ($i = mysqli_fetch_assoc($sql_ing)) {
                $ingredient = [
                    'fid' => (string)$i['fid'],
                    'ingredient' => (string)$i['ingredient'],
                    'concentration' => (float)$i['concentration'] ?: 100,
                    'quantity' => (float)$i['quantity'] ?: 0,
                    'notes' => (string)$i['notes'] ?: '-'
                ];

                $formula['ingredients'][] = $ingredient;
            }
        }

        $rows['formulas'][] = $formula;
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows, JSON_PRETTY_PRINT);

?>
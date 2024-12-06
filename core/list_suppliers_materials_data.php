<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');


$ingredient = 0;
$rx = [];

$stmt = $conn->prepare("SELECT ingID, supplierLink FROM suppliers WHERE ingSupplierID = ?");
$stmt->bind_param("i", $_GET['supplier_id']);
$stmt->execute();
$result = $stmt->get_result();

while ($res = $result->fetch_assoc()) {
    $ingredientStmt = $conn->prepare("SELECT id, name, cas, created, odor FROM ingredients WHERE id = ?");
    $ingredientStmt->bind_param("i", $res['ingID']);
    $ingredientStmt->execute();
    $ingredientResult = $ingredientStmt->get_result();

    if ($i = $ingredientResult->fetch_assoc()) {
        $r['id'] = (int)$i['id'];
        $r['material'] = (string)$i['name'];
        $r['cas'] = (string)($i['cas'] ?? 'N/A');
        $r['created'] = (string)($i['created'] ?? 'N/A');
        $r['odor'] = (string)($i['odor'] ?? 'N/A');
        $r['supplier_link'] = (string)$res['supplierLink'];
		
        $rx[] = $r;
        $ingredient++;
    }

    $ingredientStmt->close();
}

$response = [
    "data" => $rx
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
return;

?>
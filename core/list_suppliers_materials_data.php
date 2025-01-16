<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$supplierID = (int)mysqli_real_escape_string($conn,$_GET['supplier_id']);
$ingredient = 0;
$rx = [];

$stmt = $conn->prepare("SELECT ingID, supplierLink FROM suppliers WHERE ingSupplierID = ? AND owner_id = ?");
$stmt->bind_param("ii", $supplierID,$userID);
$stmt->execute();
$result = $stmt->get_result();

while ($res = $result->fetch_assoc()) {
    $ingredientStmt = $conn->prepare("SELECT id, name, cas, created_at, odor FROM ingredients WHERE id = ? AND owner_id = ?");
    $ingredientStmt->bind_param("ii", $res['ingID'],$userID);
    $ingredientStmt->execute();
    $ingredientResult = $ingredientStmt->get_result();

    if ($i = $ingredientResult->fetch_assoc()) {
        $r['id'] = (int)$i['id'];
        $r['material'] = (string)$i['name'];
        $r['cas'] = (string)($i['cas'] ?? '-');
        $r['created'] = (string)($i['created_at'] ?? '-');
        $r['odor'] = (string)($i['odor'] ?? '-');
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
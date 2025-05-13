<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$supplierID = (int)mysqli_real_escape_string($conn,$_GET['supplier_id']);
$ingredient = 0;
$rx = [];

$stmt = $conn->prepare("SELECT ingID, supplierLink, price, size, supplier_sku FROM suppliers WHERE ingSupplierID = ? AND owner_id = ?");
$stmt->bind_param("is", $supplierID,$userID);
$stmt->execute();
$result = $stmt->get_result();

while ($res = $result->fetch_assoc()) {
    $ingredientStmt = $conn->prepare("SELECT id, name, cas, created_at, notes FROM ingredients WHERE id = ? AND owner_id = ?");
    $ingredientStmt->bind_param("is", $res['ingID'],$userID);
    $ingredientStmt->execute();
    $ingredientResult = $ingredientStmt->get_result();

    if ($i = $ingredientResult->fetch_assoc()) {
        $r['id'] = (int)$i['id'];
        $r['material'] = (string)$i['name'];
        $r['cas'] = (string)($i['cas'] ?? '-');
        $r['created'] = (string)($i['created_at'] ?? '-');
        $r['notes'] = (string)($i['notes'] ?? '-');
        $r['supplier_link'] = (string)$res['supplierLink'];
        $r['price'] = (float)$res['price'];
        $r['size'] = (float)$res['size'];
        $r['supplier_sku'] = (string)$res['supplier_sku'];
		
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
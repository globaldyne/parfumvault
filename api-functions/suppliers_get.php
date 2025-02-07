<?php

if (!defined('pvault_panel')){ die('Not Found');}

header('Content-Type: application/json; charset=utf-8');
global $conn, $userID;

$stmt = $conn->prepare("SELECT ingSupplierID, ingID, supplierLink, price, size, manufacturer, preferred FROM suppliers WHERE owner_id = ?");
$stmt->bind_param("s", $userID);
$stmt->execute();
$result = $stmt->get_result();

$rows = array();
while($r = $result->fetch_assoc()) {
  $r['manufacturer'] = $r['manufacturer'] ?: "-";
  $r['supplierLink'] = $r['supplierLink'] ?: "-";
  $rows['suppliers'][] = $r;
}

if (empty($rows)) {
  echo json_encode(array('message' => 'No suppliers found'), JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
  echo json_encode($rows, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

$stmt->close();
return;
<?php

if (!defined('pvault_panel')){ die('Not Found');}

header('Content-Type: application/json');
global $conn, $userID;


$sql = mysqli_query($conn, "SELECT ingSupplierID, ingID, supplierLink, price, size, manufacturer, preferred FROM suppliers WHERE owner_id = '$userID'");
$rows = array();
while($r = mysqli_fetch_assoc($sql)) {
  if (empty($r['manufacturer'])) {
	 $r['manufacturer'] = "-";
  }
  if (empty($r['supplierLink'])) {
	 $r['supplierLink'] = "-";
  }
  $rows['suppliers'][] = $r;
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT);
return;
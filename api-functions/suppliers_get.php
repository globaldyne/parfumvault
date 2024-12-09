<?php

if (!defined('pvault_panel')){ die('Not Found');}

header('Content-Type: application/json');


$sql = mysqli_query($conn, "SELECT ingSupplierID, ingID, supplierLink, price, size, manufacturer, preferred FROM suppliers");
$rows = array();
while($r = mysqli_fetch_assoc($sql)) {
  if (empty($r['manufacturer'])) {
	 $r['manufacturer'] = "N/A";
  }
  if (empty($r['supplierLink'])) {
	 $r['supplierLink'] = "N/A";
  }
  $rows['suppliers'][] = $r;
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT);
return;
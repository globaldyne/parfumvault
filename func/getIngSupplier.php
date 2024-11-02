<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function getIngSupplier($ingID,$getStock,$conn){
	
if($getStock == 1){
	$result = mysqli_fetch_array(mysqli_query($conn, "SELECT mUnit, price, SUM(stock) AS stock FROM suppliers WHERE ingID = '$ingID'"));	
}else{
	$q = mysqli_query($conn, "SELECT ingSupplierID,supplierLink,status FROM suppliers WHERE ingID = '$ingID'");
	while($r = mysqli_fetch_array($q)){
		$sup = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '".$r['ingSupplierID']."'"));

		$result[] = array_merge((array)$r, (array)$sup);
	}
}
	return $result;
}


function getPrefSupplier($ingID,$conn){
	
	$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT price,ingSupplierID,size,supplierLink FROM suppliers WHERE ingID = '$ingID' AND preferred = '1'"));
	$sup = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '".$ing['ingSupplierID']."'"));
		
	$result = array_merge((array)$ing, (array)$sup);

	return $result;
}

function getSingleSupplier($sID,$ingID,$conn){
	
	$result = mysqli_fetch_array(mysqli_query($conn, "SELECT price FROM suppliers WHERE ingSupplierID = '$sID' AND ingID = '$ingID'"));
		
	return $result;
}

function getSupplierByID($sID,$conn){
	
	$result = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '$sID'"));
		
	return $result;
}

?>
<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function getIngSupplier($ingID,$getStock,$conn){
	global $userID;

	if($getStock == 1){
		$result = mysqli_fetch_array(mysqli_query($conn, "SELECT mUnit, price, SUM(stock) AS stock FROM suppliers WHERE ingID = '$ingID' AND owner_id = '$userID'"));	
	}else{
		$q = mysqli_query($conn, "SELECT ingSupplierID,supplierLink,status FROM suppliers WHERE ingID = '$ingID' AND owner_id = '$userID'");
		while($r = mysqli_fetch_array($q)){
			$sup = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '".$r['ingSupplierID']."' AND owner_id = '$userID'"));

			$result[] = array_merge((array)$r, (array)$sup);
		}
	}
	return $result;
}


function getPrefSupplier($ingID,$conn){
	global $userID;

	$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT price,ingSupplierID,size,supplierLink FROM suppliers WHERE ingID = '$ingID' AND preferred = '1' AND owner_id = '$userID'"));
	$sup = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '".$ing['ingSupplierID']."' AND owner_id = '$userID'"));
		
	$result = array_merge((array)$ing, (array)$sup);

	return $result;
}

function getSingleSupplier($sID,$ingID,$conn){
	global $userID;

	$result = mysqli_fetch_array(mysqli_query($conn, "SELECT price FROM suppliers WHERE ingSupplierID = '$sID' AND ingID = '$ingID' AND owner_id = '$userID'"));
		
	return $result;
}

function getSupplierByID($sID,$conn){
	global $userID;

	$result = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '$sID' AND owner_id = '$userID'"));
		
	return $result;
}

?>
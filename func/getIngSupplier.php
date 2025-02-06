<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function getIngSupplier($ingID, $getStock, $conn) {
    global $userID;

    if ($getStock == 1) {
        // Single optimized query for stock retrieval
        $query = "SELECT mUnit, price, SUM(stock) AS stock 
                  FROM suppliers 
                  WHERE ingID = '$ingID' AND owner_id = '$userID'";
        return mysqli_fetch_assoc(mysqli_query($conn, $query)) ?: null;
    } else {
        // Batch fetch supplier data
        $query = "SELECT s.ingSupplierID, s.supplierLink, s.status, i.name 
                  FROM suppliers s
                  JOIN ingSuppliers i ON s.ingSupplierID = i.id 
                  WHERE s.ingID = '$ingID' AND s.owner_id = '$userID' AND i.owner_id = '$userID'";

        $result = mysqli_query($conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC) ?: null;
    }
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
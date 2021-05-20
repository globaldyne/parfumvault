<?php if (!defined('pvault_panel')){ die('Not Found');}?>

<?php
function getIngSupplier($ingID,$conn){
	
	$q = mysqli_query($conn, "SELECT * FROM suppliers WHERE ingID = '$ingID'");

	while($r = mysqli_fetch_array($q)){
		$sup = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '".$r['ingSupplierID']."'"));

		$result[] = array_merge($r, $sup);
	}
	return $result;
}
?>
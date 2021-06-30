<?php if (!defined('pvault_panel')){ die('Not Found');}?>

<?php
function getDocument($ownerID,$type,$conn){
	
	$q = mysqli_query($conn, "SELECT name,docData FROM documents WHERE ownerID = '$ownerID' AND type = '$type'");

	while($r = mysqli_fetch_array($q)){

		$result[] = $r;
	}
	return $result;
}

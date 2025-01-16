<?php if (!defined('pvault_panel')){ die('Not Found');}?>

<?php
function getDocument($ownerID, $type, $conn){
	global $userID;

	$q = mysqli_query($conn, "SELECT id, name, docData FROM documents WHERE ownerID = '$ownerID' AND type = '$type' AND owner_id = '$userID'");

	while($r = mysqli_fetch_array($q)){

		$result[] = $r;
	}
	return $result;
}
?>
<?php if (!defined('pvault_panel')){ die('Not Found');}?>

<?php
function getFormula($id,$profile,$conn){
	global $userID;

	$formula = mysqli_fetch_array(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE id = '$id' AND owner_id = '$userID'"));
	$ing = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '".$formula['fid']."' AND owner_id = '$userID'");
	
	while($q = mysqli_fetch_array($ing)){
		$chk = mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '".$q['ingredient']."' AND profile = '$profile' AND owner_id = '$userID'");
		while ($qValues=mysqli_fetch_array($chk)){
			echo $qValues['name'].'\n';
		}
	}
}
?>
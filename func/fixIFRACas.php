<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function fixIFRACas($conn){
	global $userID;

	$q0 = mysqli_query($conn, "SELECT id,cas FROM IFRALibrary WHERE cas REGEXP '^[a-zA-Z().]' AND owner_id = '$userID' ");
	if (!$q0) {
		error_log("Error in query q0: " . mysqli_error($conn));
		return;
	}
	while($f = mysqli_fetch_array($q0)){
		$r = preg_replace("/^\s/", "",preg_replace("/\s+/", "\n",preg_replace("/[^0-9,\-,\n]/", "", $f['cas'])));
		$updateQuery = "UPDATE IFRALibrary SET cas = '$r' WHERE id = '".$f['id']."' AND owner_id = '$userID'";
		if (!mysqli_query($conn, $updateQuery)) {
			error_log("Error in update query: " . mysqli_error($conn));
		} else {
			error_log("Successfully updated record with id: " . $f['id']);
		}
	}

	$q1 = mysqli_query($conn, "SELECT id,ifra_key,cas FROM IFRALibrary WHERE cas REGEXP '\n' AND owner_id = '$userID'");
	if (!$q1) {
		error_log("Error in query q1: " . mysqli_error($conn));
		return;
	}
	$fields = 'ifra_key,image,amendment,prev_pub,last_pub,deadline_existing,deadline_new,name,cas,cas_comment,synonyms,formula,flavor_use,prohibited_notes,restricted_photo_notes,restricted_notes,specified_notes,type,risk,contrib_others,contrib_others_notes,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12,owner_id';
	
	while($r =  mysqli_fetch_array($q1)){
		$e = explode("\n",$r['cas']);
		foreach($e as $casvalue){
			$casvalue = preg_replace("/[^0-9\-]/", "", $casvalue);
			$query = "INSERT INTO IFRALibrary ($fields) SELECT ifra_key,image,amendment,prev_pub,last_pub,deadline_existing,deadline_new,name,'$casvalue',cas_comment,synonyms,formula,flavor_use,prohibited_notes,restricted_photo_notes,restricted_notes,specified_notes,type,risk,contrib_others,contrib_others_notes,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12,'$userID' FROM IFRALibrary WHERE id = '".$r['id']."' AND owner_id = '$userID'";
			if (!mysqli_query($conn, $query)) {
				error_log("Error in insert query: " . mysqli_error($conn));
			} else {
				error_log("Successfully inserted new record for cas: " . $casvalue);
			}
		}
	}

	
	if (!mysqli_query($conn, "DELETE FROM IFRALibrary WHERE cas REGEXP '\n' AND owner_id = '$userID'")) {
		error_log("Error in delete query: " . mysqli_error($conn));
	} else {
		error_log("Successfully deleted records with cas containing newline characters.");
	}
	
	if (!mysqli_query($conn, "DELETE FROM IFRALibrary WHERE name = '' OR ifra_key = 'Key|String' AND owner_id = '$userID'")) {
		error_log("Error in delete query: " . mysqli_error($conn));
	} else {
		error_log("Successfully deleted records with empty name or default ifra_key.");
	}
}
?>
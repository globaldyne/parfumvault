<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function countPending($ing = NULL, $fid ){
    global $userID, $conn;


	if($ing == '1' && $fid){
		$c = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid' AND toAdd = '1' AND owner_id = '$userID'"));
	}else{
		$c = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE toAdd = '1'  AND owner_id = '$userID' GROUP BY name"));
	}
	
	if($c == '0'){
		return NULL;
	}else{
		return $c;
	}
}

?>
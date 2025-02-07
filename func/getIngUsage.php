<?php

if (!defined('pvault_panel')){ die('Not Found');}

function getIngUsage($ingredient){
	global $conn, $userID;

	$ing = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulas WHERE ingredient = '$ingredient' AND owner_id = '$userID'"));	
	echo $ing;
	return;
}

function getNoteImpact($ingredient,$note){
	global $conn, $userID;

	$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT impact_$note FROM ingredients WHERE name =  '$ingredient' AND owner_id = '$userID'"));	
	
	if ($ing["impact_$note"] == '10'){
		$imn['int'] = 10;
		$imn['str'] = 'Low';
	}elseif ($ing["impact_$note"] == '50'){
		$imn['int'] = 50;
		$imn['str'] = 'Medium';
	}elseif ($ing["impact_$note"] == '100'){
		$imn['int'] = 100;
		$imn['str'] = 'High';
	}elseif ($ing["impact_$note"] == 'none'){
		$imn['int'] = 0;
		$imn['str'] = 'None';
	}
	
	return $imn;
}

?>
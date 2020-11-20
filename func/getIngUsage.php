<?php

if (!defined('pvault_panel')){ die('Not Found');}

function getIngUsage($ingredient,$conn){
	$ing = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulas WHERE ingredient = '$ingredient'"));	
	echo $ing;
	return;
}

function getNoteImpact($ingredient,$note,$conn){
	$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT impact_$note FROM ingredients WHERE name =  '$ingredient'"));	
	
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
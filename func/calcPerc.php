<?php
if (!defined('pvault_panel')){ die('Not Found');}

function calcPerc($formula, $profile, $percent, $conn){
	$formula_q = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE name = '$formula'");
	while ($formula = mysqli_fetch_array($formula_q)) {
		$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT profile FROM ingredients WHERE name = '".$formula['ingredient']."'"));
		$prf[] = $ing_q['profile'];
	}
	$number = array_count_values($prf); 
    return ($number[$profile] / $percent) * 100;
}
?>
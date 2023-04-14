<?php
if (!defined('pvault_panel')){ die('Not Found');}

function calcPerc($id, $profile, $percent, $conn){
	$formula = mysqli_fetch_array(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE id = '$id'"));
	$formula_q = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '".$formula['fid']."'");
	while ($formula = mysqli_fetch_array($formula_q)) {
		$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT profile FROM ingredients WHERE name = '".$formula['ingredient']."'"));
		$prf[] = $ing_q['profile'];
	}
	if($prf){
		$number = array_count_values($prf); 
    	return ($number[$profile] / $percent) * 100;
	}
	return;
}

function multi_dim_search($array, $key, $value){
    $results = array();

    if (is_array($array)) {
        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }
		
        foreach ($array as $subarray) {
            $results = array_merge($results, multi_dim_search($subarray, $key, $value));
        }
		
    }

    return $results;
}

function multi_dim_perc($conn, $form, $ingCas, $qStep) {
	foreach ($form as $formula){
		
		if($compos = mysqli_query($conn, "SELECT name,percentage,cas FROM allergens WHERE ing = '".$formula['ingredient']."'")){
		
			while($compo = mysqli_fetch_array($compos)){
				$cmp[] = $compo;
			}
			
			foreach ($cmp as $a){
				$arrayLength = count($a);
				$i = 0;
				while ($i < $arrayLength){
					$c = multi_dim_search($a, 'cas', $a['cas'])[$i];
					$conc[$a['cas']] += number_format($c['percentage']/100 * $formula['quantity'] * $formula['concentration'] / 100, $qStep);
	
					$i++;
				}
			}
		}
	}
	return $conc;
}

?>

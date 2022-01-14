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
function multi_dim_perc($conn, $form,  $qStep) {
	foreach ($form as $formula){
		
		$allIngQuery = mysqli_query($conn, "SELECT name,percentage,cas FROM allergens WHERE ing = '".$formula['ingredient']."'");
		
		while($allgIng_res = mysqli_fetch_array($allIngQuery)){
			$allgIng[] = $allgIng_res;
		}
		
		foreach ($allgIng as $aa){
			$arrayLength = count($aa);
			$i = 0;
			while ($i < $arrayLength){
				//if(empty($c = multi_dim_search($aa, 'cas', $aa['cas'])[$i])){
					$c = multi_dim_search($aa, 'name', $formula['ingredient'])[$i];
				//}
				$conc[$formula['ingredient']] += number_format($formula['quantity'] / 100 * $c['percentage'], $qStep);
				/*
					$conc= $formula['concentration'] / 100 * $formula['quantity']/$mg['total_mg'] * 100;
				*/
				//$c = multi_dim_search($aa, 'cas', $cas)[$i];
				//$conc[$formula['ingredient']] += number_format($formula['quantity'] / 100 * $c['percentage'], 3);
				//$conc[$cas] += number_format($c['percentage'] / 100 * $formula['quantity'] , $qStep);

				$i++;
			}
		}
	}
	//print_r($conc);
	return array_unique(array_merge(array_filter($conc)));
}
?>

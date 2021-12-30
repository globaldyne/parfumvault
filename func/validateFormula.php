<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function validateFormula($fid, $bottle, $new_conc, $mg, $defCatClass, $qStep, $conn ){
	$formula_q = mysqli_query($conn, "SELECT ingredient,quantity,concentration FROM formulas WHERE fid = '$fid'");
	while ($formula = mysqli_fetch_array($formula_q)) {
		$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT cas, $defCatClass FROM ingredients WHERE name = '".$formula['ingredient']."'"));

		$limitIFRA = searchIFRA($ing_q['cas'],$formula['ingredient'],null,$conn,$defCatClass);
		$limit = explode(' - ', $limitIFRA);
		$limit = $limit['0'];
					  
		$new_quantity = $formula['quantity']/$mg*$new_conc;
		$conc = $new_quantity/$bottle * 100;						
		$conc_p = number_format($formula['concentration'] / 100 * $conc, $qStep);
					 	

		if($limit != null){
			if($limit < $conc_p){
				$val[] = 1;//VALUE IS TO HIGH AGAINST IFRA
			}else{
				$val[] = 0; //VALUE IS OK
			}
		}else
		  if($ing_q[$defCatClass] != null){
		  	if($ing_q[$defCatClass] < $conc_p){
				$val[] = 0; //VALUE IS TO HIGH AGAINST LOCAL DB BUT WILL BE IGNORED
		  	}else{
				$val[] = 0; //VALUE IS OK
			}
		}else{
			$val[] = 1; //NO RECORD FOUND
		}
	}
	if (in_array('1', $val)) {
		return 1;
	}else{
		return 0;
	}
}

?>

<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function getIngStock($ingID,$format,$conn){
	global $userID;
	
	$data = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(stock) AS stock,mUnit FROM suppliers WHERE ingID = '$ingID' AND owner_id = '$userID'"));	
	if($format == 1){
		if($data['stock'] != 0){
			$result = '<i class = "stock2 badge badge-instock mr3">In stock: '.$data['stock'].$data['mUnit'].'</i>';
		}else{
			$result = '<i class = "stock2 badge badge-nostock mr3">Not in stock: '.$data['stock'].$data['mUnit'].'</i>';
		}
		
	}else{
		$result = $data['stock'];
	}

	return $result;

}


?>

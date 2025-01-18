<?php
if (!defined('pvault_panel')){ die('Not Found');}
global $conn, $userID;

if($_REQUEST['action'] == 'skipMaterial'){
	$fid = mysqli_real_escape_string($conn, $_REQUEST['fid']);
	$id = mysqli_real_escape_string($conn, $_REQUEST['id']);
	$ingID = mysqli_real_escape_string($conn, $_REQUEST['ingId']);
	$notes = mysqli_real_escape_string($conn, $_REQUEST['notes']) ?: "-";

	if(mysqli_query($conn, "UPDATE makeFormula SET skip = '1', notes = '$notes' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")){
		$response['success'] = true;
		$response['message'] = $_REQUEST['ing'].' skipped from the formulation';
		file_put_contents($tmp_path.'reload_signal.txt', 'reload');
	} else {
		$response['success'] = false;
		$response['message'] = 'Error skipping the ingredient';
	}
	header('Content-Type: application/json;');
	echo json_encode($response);
	return;
}

	if($_REQUEST['do'] == 'callback'){ 
	//SKIP MATERIAL FROM MAKE FORMULA

		
		if( $_REQUEST['action'] == 'makeFormula'){
	
			$fid = mysqli_real_escape_string($conn, $_REQUEST['fid']);
			$id = mysqli_real_escape_string($conn, $_REQUEST['id']);
			$ingID = mysqli_real_escape_string($conn, $_REQUEST['ingId']);
			$qr = trim($_REQUEST['qr']);
			
			if (empty($fid) || empty($id) || empty($ingID) || empty($qr)) {
				$response['success'] = false;
				$response['message'] = 'Missing required params';
				echo json_encode($response);
				return;
			}
			
			if(!is_numeric($_REQUEST['q'])){
				$response['success'] = false;
				$response['message'] = 'Invalid quantity value';
				echo json_encode($response);
				return;
			}
			
			if((double)$_REQUEST['q'] == 0.00){
				$response['success'] = false;
				$response['message'] = 'Please add quantity';
				echo json_encode($response);
				return;
			}
							 
			$q = trim($_REQUEST['q']);
			$notes = mysqli_real_escape_string($conn, $_REQUEST['notes']);
			
			if($_REQUEST['updateStock'] == "true"){
				$getStock = mysqli_fetch_array(mysqli_query($conn, "SELECT stock,mUnit FROM suppliers WHERE ingID = '$ingID' AND preferred = '1' AND owner_id = '$userID'"));
				if($getStock['stock'] < $q){
					//$response['warning'] = 'Amount exceeds quantity available in stock ('.$getStock['stock'].$getStock['mUnit'].'). The maximum available will be deducted from stock';
					//echo json_encode($response);
					//return;
					$q = $getStock['stock'];
				}
				mysqli_query($conn, "UPDATE suppliers SET stock = stock - $q WHERE ingID = '$ingID' AND preferred = '1' AND owner_id = '$userID'");
					$response['success'] = true;
					$response['message'] = "Stock deducted by ".$q.$settings['mUnit'];
			}
			
			$q = trim($_REQUEST['q']);
			if($qr == $q){
				if(mysqli_query($conn, "UPDATE makeFormula SET toAdd = '0' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")){
					$response = array("success" => true, "message" => "Ingredient added");
				}
			}else{
				$sub_tot = $qr - $q;
				//if ($sub_tot < 0) {
				//	    $sub_tot += abs($sub_tot);
				//}
				if(mysqli_query($conn, "UPDATE makeFormula SET quantity='$sub_tot' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")){
					$response = array("success" => true, "message" => "Quantity updated ($q)");
				}
			}
		
			if($notes){
				$notes = "Formula make, ingredient: ".$_REQUEST['ing']."\\n";
				mysqli_query($conn, "UPDATE formulasMetaData SET notes = CONCAT(notes, '".$notes."') WHERE fid = '$fid' AND owner_id = '$userID'");
			}
			
			if($qr < $q){
				if(mysqli_query($conn, "UPDATE makeFormula SET overdose = '$q' WHERE fid = '$fid' AND id = '$id' AND owner_id = '$userID'")){
					$response['success'] = true;
					$response['message'] = $_REQUEST['ing'].' is overdosed, '.$q.' added';
				}
			}
			
			if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid' AND toAdd = '1' AND owner_id = '$userID'"))){
				$response['success'] = true;
				$response['message'] = 'All materials added. You should mark formula as complete now';
			}
			
			file_put_contents($tmp_path.'reload_signal.txt', 'reload');
			header('Content-Type: application/json;');
       		echo json_encode($response);
			return;
	
		}
	}
	
?>
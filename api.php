<?php
define('pvault_panel', TRUE);
define('__ROOT__', dirname(dirname(__FILE__)));

require_once('inc/config.php');
require_once('inc/opendb.php');
//require_once('inc/product.php');

$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen('logs/api.log', 'a');
fwrite($fp, $req_dump);
fclose($fp);


if (isset($_REQUEST['login']) && isset($_REQUEST['key'])){	
	$key = mysqli_real_escape_string($conn, $_REQUEST['key']);
	$client = mysqli_real_escape_string($conn, $_REQUEST['client']);
	if(apiCheckAuth($key, $conn) == true){
		$response['status'] = "Success";
	}else{
		$response['status'] = "Failed";
	}
	header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
	return;
}

if($_REQUEST['key'] && $_REQUEST['do']){
	$key = mysqli_real_escape_string($conn, $_REQUEST['key']);
	$_REQUEST['do'] = strtolower(mysqli_real_escape_string($conn, $_REQUEST['do']));

	if(apiCheckAuth($key, $conn) == false){
		$response['status'] = "Failed";
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($response);
		return;
	}
	
	if($_REQUEST['do'] == 'formulas'){

		$sql = mysqli_query($conn, "SELECT name, notes, fid FROM formulasMetaData");
		$rows = array();
		while($r = mysqli_fetch_assoc($sql)) {
			foreach ($r as $key => $value) {
    			if (is_null($value) || empty($value)) {
        	 		$r[$key] = "N/A";
   				}
			}
			$rows[$_REQUEST['do']][] = $r;
		}
		header('Content-Type: application/json; charset=utf-8');
      	echo json_encode($rows, JSON_HEX_APOS|JSON_HEX_QUOT);
      	return;
	}
	
	if($_REQUEST['do'] == 'formula'){
		if($fid = mysqli_real_escape_string($conn, $_REQUEST['fid'])){

			$sql = mysqli_query($conn, "SELECT name, ingredient, concentration, dilutant, quantity FROM formulas WHERE fid = '$fid'");

		}else{
		
			$sql = mysqli_query($conn, "SELECT name, ingredient, concentration, dilutant, quantity FROM formulas");
		}
		$rows = array();
		while($r = mysqli_fetch_assoc($sql)) {
			foreach ($r as $key => $value) {
    			if (is_null($value) || empty($value)) {
        	 		$r[$key] = "N/A";
   				}
				if (!is_numeric($r['concentration'])) {
        	 		$r['concentration'] = "100";
   				}
				if (!is_numeric($r['quantity'])) {
        	 		$r['quantity'] = "0.00";
   				}
			}
			$rows[$_REQUEST['do']][] = $r;
		}
		header('Content-Type: application/json; charset=utf-8');
      	echo json_encode($rows, JSON_PRETTY_PRINT);
      	return;
	}
	
	if($_REQUEST['do'] == 'ingredients'){
		$sql = mysqli_query($conn, "SELECT id, name, cas, odor, profile, physical_state, cat4, category, type FROM ingredients");
		
		$rows = array();    
		while($r = mysqli_fetch_assoc($sql)) {
			$cName = mysqli_fetch_array(mysqli_query($conn,"SELECT name AS cName FROM ingCategory WHERE id = ".$r['category'].""));
			array_push($r, $cName['cName']);
			$r['cName'] = $r['0'];
			$rx = array_filter($r, fn($value) => !is_null($value) && $value !== '');
			foreach ($rx as $key => $value) {
    			if (is_null($value) || empty($value)) {
        	 		$rx[$key] = "N/A";
   				}
				
				if (!is_numeric($r['cat4'])) {
        	 		$rx['cat4'] = "100";
   				}
				if (!is_numeric($r['physical_state'])) {
        	 		$rx['physical_state'] = "1";
   				}
				
			}
			$rows[$_REQUEST['do']][] = array_filter($rx);
        }
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($rows, JSON_PRETTY_PRINT);
      return;
	}
}



 

function apiCheckAuth($key, $conn){
   if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM settings WHERE api = '1' AND api_key='$key'"))){
	   return true;
   }else{
	   return false;
   }
}

?>

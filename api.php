<?php
define('pvault_panel', TRUE);
define('__ROOT__', dirname(dirname(__FILE__)));

require_once('inc/config.php');
require_once('inc/opendb.php');

$defCatClass = $settings['defCatClass'];

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
		while($r = mysqli_fetch_assoc($sql)) {
    		if (is_null($r['name']) || empty($r['name'])) {
        		$r['name'] = "N/A";
   			}
			if (is_null($r['notes']) || empty($r['notes'])) {
        		$r['notes'] = "N/A";
   			}
			$r['name'] = (string)$r['name'];
			$r['notes'] = (string)$r['notes'];

			$rows[$_REQUEST['do']][] = array_filter($r);
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
			
			$r['dilutant'] = $r['dilutant'] ? $r['dilutant']: 'None';

			if (!is_numeric($r['concentration'])) {
        		$r['concentration'] = "100";
   			}
			if (!is_numeric($r['quantity'])) {
        		$r['quantity'] = "0.00";
   			}
			$r['name'] = (string)$r['name'];
			$r['ingredient'] = (string)$r['ingredient'];
			$r['dilutant'] = (string)$r['dilutant'];
			$r['concentration'] = (float)$r['concentration']?:100;
			$r['quantity'] = (float)$r['quantity']?:0;

			$rows[$_REQUEST['do']][] = $r;
		}
		header('Content-Type: application/json; charset=utf-8');
      	echo json_encode($rows,  JSON_PRETTY_PRINT);
      	return;
	}
	
	if($_REQUEST['do'] == 'ingredients'){
		$sql = mysqli_query($conn, "SELECT id, name, cas, odor, profile, physical_state, cat4, category, type FROM ingredients");
		$rows = array();    
		while($rx = mysqli_fetch_assoc($sql)) { 
			if($ifra = mysqli_fetch_array(mysqli_query($conn, "SELECT cat4, type FROM IFRALibrary WHERE cas = '".$rx['cas']."'"))){
        		$rx['cat4'] = preg_replace("/[^0-9.]/", "", $ifra['cat4']);
        		$rx['class'] = $ifra['type'];
   			}
					
            if (!$rx['class']) {
				$rx['class'] = 'Recommendation';
			}
			
            if (is_null($rx['cas']) || empty($rx['cas'])) {
                $rx['cas'] = "N/A";
             }
				
			 if (is_null($rx['odor']) || empty($rx['odor'])) {
                $rx['odor'] = "N/A";
             }
				
			 if (is_null($rx['profile']) || empty($rx['profile'])) {
                  $rx['profile'] = "N/A";
             }
				
			 if (is_null($rx['type']) || empty($rx['type'])) {
                $rx['type'] = "AC";
             }
				
			$rx['id'] = (int)$rx['id'];
			$rx['name'] = (string)$rx['name'];
			$rx['cas'] = (string)$rx['cas'];
			$rx['odor'] = (string)$rx['odor'];
			$rx['profile'] = (string)$rx['profile'];
			$rx['physical_state'] = (int)$rx['physical_state'];
			$rx['cat4'] = (double)$rx['cat4'];
			$rx['category'] = (int)$rx['category'];
			$rx['type'] = (string)$rx['type'];
			$rx['class'] = (string)$rx['class'];

				
			$rows[$_REQUEST['do']][] = array_filter($rx);
        }
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($rows,  JSON_PRETTY_PRINT);
      return;
	}

	if($_REQUEST['do'] == 'categories'){
       $sql = mysqli_query($conn, "SELECT id, name, notes, image, colorKey FROM ingCategory");
       $rows = array();
       	while($r = mysqli_fetch_assoc($sql)) {
          if (empty($r['notes'])) {
             $r['notes'] = "N/A";
          }
          if (empty($r['image'])) {
           	$r['image'] = base64_encode(file_get_contents("img/molecule.png"));
          }else{
			$img = explode('data:image/png;base64,',$r['image']);
			$r['image'] = $img['1']?:base64_encode(file_get_contents("img/molecule.png"));
		  }
		  if (empty($r['colorKey'])) {
            $r['colorKey'] = "255, 255, 255";
          }
		  $r['id'] = (int)$r['id'];
          $rows[$_REQUEST['do']][] = array_filter($r);
     	}
       header('Content-Type: application/json; charset=utf-8');
       echo json_encode($rows, JSON_PRETTY_PRINT);
       return;
    }
	
	
	if($_REQUEST['do'] == 'suppliers'){
       $sql = mysqli_query($conn, "SELECT ingSupplierID, ingID, supplierLink, price, size, manufacturer, preferred FROM suppliers");
       $rows = array();
       	while($r = mysqli_fetch_assoc($sql)) {
          if (empty($r['manufacturer'])) {
             $r['manufacturer'] = "N/A";
          }
          if (empty($r['supplierLink'])) {
             $r['supplierLink'] = "N/A";
          }
          $rows[$_REQUEST['do']][] = $r;
     	}
       header('Content-Type: application/json; charset=utf-8');
       echo json_encode($rows, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT);
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


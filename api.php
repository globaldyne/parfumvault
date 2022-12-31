<?php
define('pvault_panel', TRUE);
define('__ROOT__', dirname(__FILE__));

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/rgbToHex.php');

$defCatClass = $settings['defCatClass'];

$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen(__ROOT__.'/logs/api.log', 'a');
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

		$sql = mysqli_query($conn, "SELECT name, notes, finalType AS concentration, fid FROM formulasMetaData");
		while($r = mysqli_fetch_assoc($sql)) {
    		if (is_null($r['name']) || empty($r['name'])) {
        		$r['name'] = "N/A";
   			}
			if (is_null($r['notes']) || empty($r['notes'])) {
        		$r['notes'] = "N/A";
   			}
			$r['name'] = (string)$r['name'];
			$r['notes'] = (string)$r['notes'];
			$r['concentration'] = (int)$r['concentration'];

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
		$sql = mysqli_query($conn, "SELECT id, name, cas, odor, profile, physical_state, cat4, category, type, INCI, purity FROM ingredients");
		while($rx = mysqli_fetch_assoc($sql)) { 
			if($ifra = mysqli_fetch_array(mysqli_query($conn, "SELECT cat4, type FROM IFRALibrary WHERE cas = '".$rx['cas']."'"))){
        		$rx['cat4'] = preg_replace("/[^0-9.]/", "", $ifra['cat4']);
        		$rx['class'] = $ifra['type'];
   		   }
		   $gSupQ = mysqli_fetch_array(mysqli_query($conn, "SELECT ingSupplierID, price, size FROM suppliers WHERE ingID = '".$rx['id']."' AND preferred = '1'"));
		   $gSupN = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '".$gSupQ['ingSupplierID']."'"));
	   		$gCatQ = mysqli_fetch_array(mysqli_query($conn, "SELECT name, notes, colorKey FROM ingCategory WHERE id = '".$rx['category']."'"));


		   $size = $gSupQ['size']?:10;
		   $s = $gSupQ['price']/$size;
		
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
			
			$rx['category'] = (int)$rx['category'] ?: (int)'0';
			$rx['category_name'] = (string)$gCatQ['name'] ?: (string)'Uncategorised';
			$rx['category_notes'] = (string)$gCatQ['notes'] ?: (string)'N/A';
			$rx['category_identifier'] = (string)rgb_to_hex( 'rgba('.$gCatQ['colorKey']?:'239, 239, 250, 0.8'.')' );
		
			$rx['type'] = (string)$rx['type'];
			$rx['class'] = (string)$rx['class'];
			$rx['purity'] = (double)$rx['purity']?: 100;
			$rx['INCI'] = (string)$rx['INCI']?:'N/A';
			
			$rx['supplier'] = (string)$gSupN['name'] ?: (string)'N/A';
			$rx['price'] = (double)$s ?: (double)'0';
		
			$r[] = $rx;
	}


	$response = array(
	  	"ingredients" => $r
	);
	header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response,  JSON_PRETTY_PRINT);
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


<?php
define('pvault_panel', TRUE);
define('__ROOT__', dirname(__FILE__));

require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/rgbToHex.php');

$defCatClass = $settings['defCatClass'];
$defImage = base64_encode(file_get_contents(__ROOT__.'/img/pv_molecule.png'));

$log_path = $tmp_path.'/logs/';
$log_api_file = 'pv-api.log';


if (!file_exists($log_path)) {
	mkdir($log_path, 0740, true);
}

if (!file_exists($tmp_path.$log_api_file)) {
    touch($log_path.$log_api_file);
}

$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen($tmp_path.$log_api_file, 'a');
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

		$sql = mysqli_query($conn, "SELECT id, name, product_name, notes, finalType AS concentration, fid, status, created, isProtected, rating, profile, src,customer_id,revision,madeOn FROM formulasMetaData");
		while($r = mysqli_fetch_assoc($sql)) {
    		if (is_null($r['name']) || empty($r['name'])) {
        		$r['name'] = "N/A";
   			}
			if (is_null($r['notes']) || empty($r['notes'])) {
        		$r['notes'] = "N/A";
   			}

			$C = date_format(date_create($r['created']),"d/m/Y H:i");
			$I = mysqli_fetch_array(mysqli_query($conn, "SELECT docData FROM documents WHERE ownerID = '".$r['id']."' AND type = '2'"));
			
			$r['name'] = (string)$r['name'];
			$r['product_name'] = (string)$r['product_name'] ?: "Not Set";
			$r['notes'] = (string)$r['notes'];
			$r['concentration'] = (int)$r['concentration'] ?: 100;
			$r['status'] = (int)$r['status'] ?: 0;
			$r['created'] = (string)$C ?: "-";
			$r['isProtected'] = (int)$r['isProtected'] ?: 0;
			$r['rating'] = (int)$r['rating'] ?: 0;
			$r['profile'] = (string)$r['profile'] ?: "Default";
			$r['src'] = (int)$r['src'] ?: 0;
			$r['customer_id'] = (int)$r['customer_id'] ?: 0;
			$r['revision'] = (int)$r['revision'] ?: 0;
			$r['madeOn'] = (string)$r['madeOn'] ?: "-";
			$r['image'] = (string)$I['docData'] ?: $defImage;

			$rows[$_REQUEST['do']][] = array_filter($r);
		}
		header('Content-Type: application/json; charset=utf-8');
      	echo json_encode($rows, JSON_HEX_APOS|JSON_HEX_QUOT);
      	return;
	}
	
	if($_REQUEST['do'] == 'formula'){
		if($fid = mysqli_real_escape_string($conn, $_REQUEST['fid'])){
			$sql = mysqli_query($conn, "SELECT fid, name, ingredient, concentration, dilutant, quantity, notes FROM formulas WHERE fid = '$fid'");
		}else{
			$sql = mysqli_query($conn, "SELECT fid, name, ingredient, concentration, dilutant, quantity, notes FROM formulas");
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
			$r['notes'] = (string)$r['notes'] ?: 'None';

			$rows[$_REQUEST['do']][] = $r;
		}
		header('Content-Type: application/json; charset=utf-8');
      	echo json_encode($rows,  JSON_PRETTY_PRINT);
      	return;
	}
	
	if($_REQUEST['do'] == 'ingredients'){
		$sql = mysqli_query($conn, "SELECT id, name, cas, odor, profile, physical_state, cat1, cat2, cat3, cat4, cat5A, cat5B, cat5C, cat5D, cat6, cat7A, cat7B, cat8, cat9, cat10A, cat10B, cat11A, cat11B, cat12, category, type, INCI, purity,allergen FROM ingredients");
		while($rx = mysqli_fetch_assoc($sql)) { 
			if($ifra = mysqli_fetch_array(mysqli_query($conn, "SELECT cat1, cat2, cat3, cat4, cat5A, cat5B, cat5C, cat5D, cat6, cat7A, cat7B, cat8, cat9, cat10A, cat10B, cat11A, cat11B, cat12, type FROM IFRALibrary WHERE cas = '".$rx['cas']."'"))){
        		$rx['cat1'] = preg_replace("/[^0-9.]/", "", $ifra['cat1']);
        		$rx['cat2'] = preg_replace("/[^0-9.]/", "", $ifra['cat2']);
        		$rx['cat3'] = preg_replace("/[^0-9.]/", "", $ifra['cat3']);
				$rx['cat4'] = preg_replace("/[^0-9.]/", "", $ifra['cat4']);
				
				$rx['cat5A'] = preg_replace("/[^0-9.]/", "", $ifra['cat5A']);
				$rx['cat5B'] = preg_replace("/[^0-9.]/", "", $ifra['cat5B']);
				$rx['cat5C'] = preg_replace("/[^0-9.]/", "", $ifra['cat5C']);
				$rx['cat5D'] = preg_replace("/[^0-9.]/", "", $ifra['cat5D']);
				$rx['cat7A'] = preg_replace("/[^0-9.]/", "", $ifra['cat7A']);
				$rx['cat7B'] = preg_replace("/[^0-9.]/", "", $ifra['cat7B']);
				$rx['cat8'] = preg_replace("/[^0-9.]/", "", $ifra['cat8']);
				$rx['cat9'] = preg_replace("/[^0-9.]/", "", $ifra['cat9']);
				$rx['cat10A'] = preg_replace("/[^0-9.]/", "", $ifra['cat10A']);
				$rx['cat10B'] = preg_replace("/[^0-9.]/", "", $ifra['cat10B']);
				$rx['cat11A'] = preg_replace("/[^0-9.]/", "", $ifra['cat11A']);
				$rx['cat11B'] = preg_replace("/[^0-9.]/", "", $ifra['cat11B']);
				$rx['cat12'] = preg_replace("/[^0-9.]/", "", $ifra['cat12']);
				
        		$rx['class'] = $ifra['type'];
   		   }
		   $gSupQ = mysqli_fetch_array(mysqli_query($conn, "SELECT ingSupplierID, price, size, stock FROM suppliers WHERE ingID = '".$rx['id']."' AND preferred = '1'"));
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
			$rx['cat1'] = (double)$rx['cat1'];
			$rx['cat2'] = (double)$rx['cat2'];
			$rx['cat3'] = (double)$rx['cat3'];
			$rx['cat4'] = (double)$rx['cat4'];
			$rx['cat5A'] = (double)$rx['cat5A'];
			$rx['cat5B'] = (double)$rx['cat5B'];
			$rx['cat5C'] = (double)$rx['cat5C'];
			$rx['cat5D'] = (double)$rx['cat5D'];
			$rx['cat6'] = (double)$rx['cat6'];
			$rx['cat7A'] = (double)$rx['cat7A'];
			$rx['cat7B'] = (double)$rx['cat7B'];
			$rx['cat8'] = (double)$rx['cat8'];
			$rx['cat9'] = (double)$rx['cat9'];
			$rx['cat10A'] = (double)$rx['cat10A'];
			$rx['cat10B'] = (double)$rx['cat10B'];
			$rx['cat11A'] = (double)$rx['cat11A'];
			$rx['cat11B'] = (double)$rx['cat11B'];
			$rx['cat12'] = (double)$rx['cat12'];

			$rx['allergen'] = (int)$rx['allergen'] ?: (int)'0';
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
			
			$rx['stock'] = (double)$gSupQ['stock']?: 0;

			if($rx['profile'] == "Solvent"){
				$rx['isSolvent'] = 1;
			}else{
				$rx['isSolvent'] = 0;
			}
			
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
	
	if($_REQUEST['do'] == 'documents'){
       $sql = mysqli_query($conn, "SELECT id, ownerID, type, name, notes, docData, created, updated FROM documents LIMIT 10");
       $rows = array();
       	while($rx = mysqli_fetch_assoc($sql)) {
   			
			$r['id'] = (int)$rx['id'];			
		 	$r['ownerID'] = (int)$rx['ownerID'] ?: 0;			
			$r['type'] = (int)$rx['type'] ?: 0;			
			$r['name'] = (string)$rx['name'] ?: "-";				
			$r['notes'] = (string)$rx['notes'] ?: "-";			
			$r['docData'] = (string)$rx['docData'];			
			$r['created'] = (string)$rx['created'] ?: "-";			
			$r['updated'] = (string)$rx['updated'] ?: "-";			

          	$rows[$_REQUEST['do']][] = $r;
     	}
       header('Content-Type: application/json; charset=utf-8');
       echo json_encode($rows, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT);
       return;
    }
	
	//CALLBACKS
	if($_REQUEST['do'] == 'callback'){ 
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
								 
			$q = trim($_REQUEST['q']);
			$notes = mysqli_real_escape_string($conn, $_REQUEST['notes']);
			
			if($_REQUEST['updateStock'] == "true"){
				$getStock = mysqli_fetch_array(mysqli_query($conn, "SELECT stock,mUnit FROM suppliers WHERE ingID = '$ingID' AND preferred = '1'"));
				if($getStock['stock'] < $q){
					//$response['warning'] = 'Amount exceeds quantity available in stock ('.$getStock['stock'].$getStock['mUnit'].'). The maximum available will be deducted from stock';
					//echo json_encode($response);
					//return;
					$q = $getStock['stock'];
				}
				mysqli_query($conn, "UPDATE suppliers SET stock = stock - $q WHERE ingID = '$ingID' AND preferred = '1'");
					$response['success'] = true;
					$response['message'] = "Stock deducted by ".$q.$settings['mUnit'];
			}
			
			$q = trim($_REQUEST['q']);
			if($qr == $q){
				if(mysqli_query($conn, "UPDATE makeFormula SET toAdd = '0' WHERE fid = '$fid' AND id = '$id'")){
					$response = array("success" => true, "message" => "Ingredient added");
				}
			}else{
				$sub_tot = $qr - $q;
				if ($sub_tot < 0) {
					    $sub_tot += abs($sub_tot);

				}
				if(mysqli_query($conn, "UPDATE makeFormula SET quantity='$sub_tot' WHERE fid = '$fid' AND id = '$id'")){
					$response = array("success" => true, "message" => "Quantity updated ($q)");
				}
			}
		
			if($notes){
				$notes = "Formula make, ingredient: ".$_REQUEST['ing']."\\n";
				mysqli_query($conn, "UPDATE formulasMetaData SET notes = CONCAT(notes, '".$notes."') WHERE fid = '$fid'");
			}
			
			if($qr < $q){
				if(mysqli_query($conn, "UPDATE makeFormula SET overdose = '$q' WHERE fid = '$fid' AND id = '$id'")){
					$response['success'] = true;
					$response['message'] = $_REQUEST['ing'].' is overdosed, '.$q.' added';
				}
			}
			
			if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid' AND toAdd = '1'"))){
				$response['success'] = true;
				$response['message'] = 'All materials added. You should mark formula as complete now';
			}
			
			file_put_contents($tmp_path.'reload_signal.txt', 'reload');
			header('Content-Type: application/json;');
       		echo json_encode($response);
			return;
	
		}
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

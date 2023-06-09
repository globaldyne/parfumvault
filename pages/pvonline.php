<?php 
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/func/pvOnline.php');
require_once(__ROOT__.'/func/pvFileGet.php');


//IMPORT MARKETPLACE FORMULA
if($_POST['action'] == 'import' && $_POST['kind'] == 'formula'){
	
	$id = mysqli_real_escape_string($conn, $_POST['fid']);
	
	$jAPI = $pvOnlineAPI.'?do=MarketPlace&action=get&id='.$id;
    $jsonData = json_decode(pv_file_get_contents($jAPI), true);

    if($jsonData['error']){
		$response['error'] = 'Error connecting or retrieving data from PV Online '.$jsonData['error'];
		echo json_encode($response);
        return;
    }

	if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE fid = '".$jsonData['meta']['fid']."' AND src = '1'"))){
	  $response['error'] = 'Formula name '.$jsonData['meta']['name'].' already downloaded. If you want to re-download it, please remove it first.';
	  echo json_encode($response);
	  return;
	}

	$q = "INSERT INTO formulasMetaData (name,product_name,fid,profile,sex,notes,defView,catClass,finalType,status,src) VALUES ('".$jsonData['meta']['name']."','".$jsonData['meta']['product_name']."','".$jsonData['meta']['fid']."','".$jsonData['meta']['profile']."','".$jsonData['meta']['sex']."','".$jsonData['meta']['notes']."','".$jsonData['meta']['defView']."','".$jsonData['meta']['catClass']."','".$jsonData['meta']['finalType']."','".$jsonData['meta']['status']."','1')";
	
    $qIns = mysqli_query($conn,$q);
	$last_id = mysqli_insert_id($conn);
	$source = $jsonData['meta']['source'];
	mysqli_query($conn, "INSERT INTO formulasTags (formula_id, tag_name) VALUES ('$last_id','$source')");
		
   $array_data = $jsonData['formula'];
   foreach ($array_data as $id=>$row) {
	  $insertPairs = array();
      	foreach ($row as $key=>$val) {
      		$insertPairs[addslashes($key)] = addslashes($val);
      	}
      $insertVals = '"'.$jsonData['meta']['fid'].'",'.'"'.$jsonData['meta']['name'].'",'.'"' . implode('","', array_values($insertPairs)) . '"';
   
      $jsql = "INSERT INTO formulas (`fid`,`name`,`ingredient`,`concentration`,`dilutant`,`quantity`,`notes`) VALUES ({$insertVals});";
       $qIns.= mysqli_query($conn,$jsql);
    
	}
	
    if($qIns){
		$response['success'] = $jsonData['meta']['name'].' formula imported!';
    }else{
		$response['error'] = 'Unable to import the formula '.mysqli_error($conn);
    }
	echo json_encode($response);
	return;
}

//CONTACT MARKETPLACE AUTHOR
if($_POST['action'] == 'contactAuthor'){
	$fname = $_POST['fname'];
	$fid= $_POST['fid'];
	
	if(empty($contactName = $_POST['contactName'])){
		$response['error'] = 'Please provide your full name';
		echo json_encode($response);
		return;
	}
	if(empty($contactEmail = $_POST['contactEmail'])){
		$response['error'] = 'Please provide your email';
		echo json_encode($response);
		return;
	}
	if(empty($contactReason = $_POST['contactReason'])){
		$response['error'] = 'Please provide report details';
		echo json_encode($response);
		return;
	}
	

	$data = [ 
		 'do' => 'MarketPlace',
		 'action' => 'contactAuthor',
		 'src' => 'pvMarket',
		 'fname' => base64_encode($fname), 
		 'fid' => $fid,
		 'contactName' => base64_encode($contactName),
		 'contactEmail' => base64_encode($contactEmail),
		 'contactReason' => base64_encode($contactReason)
		 ];
	
    $req = json_decode(pvPost($pvOnlineAPI, $data));
	if($req->success){
		$response['success'] = $req->success;
	}else{
		$response['error'] = $req->error;
	}
	echo json_encode($response);
	return;
	
}

//REPORT MARKETPLACE FORMULA
if($_POST['action'] == 'report' && $_POST['src'] == 'pvMarket'){
	$fname = $_POST['fname'];
	$fid= $_POST['fid'];
	
	if(empty($reporterName = $_POST['reporterName'])){
		$response['error'] = 'Please provide your full name';
		echo json_encode($response);
		return;
	}
	if(empty($reporterEmail = $_POST['reporterEmail'])){
		$response['error'] = 'Please provide your email';
		echo json_encode($response);
		return;
	}
	if(empty($reportReason = $_POST['reportReason'])){
		$response['error'] = 'Please provide report details';
		echo json_encode($response);
		return;
	}
	

	$data = [ 
		 'do' => 'MarketPlace',
		 'action' => 'reportFormula',
		 'src' => 'marketplace',
		 'fname' => base64_encode($fname), 
		 'fid' => $fid,
		 'reporterName' => base64_encode($reporterName),
		 'reporterEmail' => base64_encode($reporterEmail),
		 'reportReason' => base64_encode($reportReason)
		 ];
	
    $req = json_decode(pvPost($pvOnlineAPI, $data));
	if($req->success){
		$response['success'] = $req->success;
	}else{
		$response['error'] = $req->error;
	}
	echo json_encode($response);
	return;
	
}	
			
			
//IMPORT INGREDIENTS FROM PV ONLINE
if($_POST['action'] == 'import' && $_POST['items']){
	
	$items = explode(',',trim($_POST['items']));
    
	if($_POST['includeSynonyms'] == 'false'){
		unset($items['4']);
	}
	if($_POST['includeCompositions'] == 'false'){
		unset($items['1']);
	}

	$i = 0;
    foreach ($items as &$item) {
		$jAPI = $pvOnlineAPI.'?do='.$item.'&isAll=1';
        $jsonData = json_decode(pv_file_get_contents($jAPI), true);
		
        if($jsonData['error']){
			$response['error'] = 'Error connecting or retrieving data from PV Online '.$jsonData['error'];
			echo json_encode($response);
            return;
         }

         $array_data = $jsonData[$item];
         foreach ($array_data as $id=>$row) {
         	$insertPairs = array();
            foreach ($row as $key=>$val) {
            	$insertPairs[addslashes($key)] = addslashes($val);
            }
            $insertKeys = '`' . implode('`,`', array_keys($insertPairs)) . '`';
            $insertVals = '"' . implode('","', array_values($insertPairs)) . '"';
            if($item == 'allergens'){
		        $query = "SELECT name FROM $item WHERE name = '".$insertPairs['name']."' AND ing = '".$insertPairs['ing']."'";
			}elseif($item == 'suppliers'){
		        $query = "SELECT ingSupplierID FROM $item WHERE ingSupplierID = '".$insertPairs['ingSupplierID']."' AND ingID = '".$insertPairs['ingID']."'";
				
			}elseif($item == 'suppliersMeta'){
				$item = 'ingSuppliers'; //TODO: TO BE RENAMED
		        $query = "SELECT id FROM $item WHERE id = '".$insertPairs['id']."' AND name = '".$insertPairs['name']."'";
			
			}elseif($item == 'synonyms'){
		        $query = "SELECT id FROM $item WHERE id = '".$insertPairs['id']."' AND ing = '".$insertPairs['ing']."'";


            }else{
        	    $query = "SELECT name FROM $item WHERE name = '".$insertPairs['name']."'";
            }

            if(!mysqli_num_rows(mysqli_query($conn, $query))){
            	$jsql = "INSERT INTO $item ({$insertKeys}) VALUES ({$insertVals});";
                $qIns = mysqli_query($conn,$jsql);
                $i++;
            }
		}
	}

	if($qIns){
		$response['success'] = $i.' items imported!';
	}else{
		$response['warning'] = 'Database already in sync!';
	}
	
	echo json_encode($response);
    return;
}

?>

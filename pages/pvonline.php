<?php 
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/func/pvOnline.php');

//INVITE TO PV ONLINE
if($_POST['action'] == 'invToPv'){
	//if(empty($_POST['invEmail']) || empty($_POST['invName'])){
	//	$response['error'] = 'Email and name cannot be empty';
	//	echo json_encode($response);
	//	return;
	//}
	$invEmail = base64_encode(mysqli_real_escape_string($conn, $_POST['invEmail']));
	$invName = base64_encode(mysqli_real_escape_string($conn, $_POST['invName']));

	$data = [ 'username' => strtolower($pv_online['email']), 'password' => $pv_online['password'],'do' => 'invToPv','invEmail' => base64_encode($_POST['invEmail']), 'invName' => $invName ];

    $req = json_decode(pvPost($pvOnlineAPI, $data));

	if($req->success){
		$response['success'] = $req->success;
	}else{
		$response['error'] = $req->error;
	}
	
	echo json_encode($response);
	return;
	
}

//SHARE FORMULA TO PV ONLINE
if($_POST['action'] == 'share' && $_POST['fid']){
	if(!$_POST['users']){
		$response['error'] = 'Please select user(s) first.';
		echo json_encode($response);
		return;
	}
	$fid = $_POST['fid'];
	
	$qMeta = mysqli_fetch_array(mysqli_query($conn, "SELECT name, product_name, profile, sex, defView, catClass, finalType, status FROM formulasMetaData WHERE fid = '$fid'"));
	
	if(mysqli_num_rows(mysqli_query($conn,"SElECT ingredient FROM formulas WHERE fid = '$fid'")) == FALSE) {
		$response['error'] = 'Formula cannot be shared as its currently empty';
		echo json_encode($response);
		return;
	}

									
	$q = mysqli_query($conn, "SELECT name, ingredient, concentration, dilutant, quantity, notes FROM formulas WHERE fid = '$fid'");
	
	while($formula = mysqli_fetch_assoc($q)){
		$r[] = $formula;
	}
	
	$credits = ' <p></p> This formula authored by '.$user['fullName'];
	$comments = $_POST['comments'].$credits;
	
	$fData = array(
		"meta" => array(
		"fid" => $_POST['fid'],
		"users" => $_POST['users'],
		"notes" => $comments,
		"name" => (string)$qMeta['name'],
		"product_name" => (string)$qMeta['product_name'],
		"profile" => (string)$qMeta['profile'],
		"sex" => (string)$qMeta['sex'],
		"defView" => (int)$qMeta['defView'],
		"catClass" => (string)$qMeta['catClass'],
		"finalType" => (int)$qMeta['finalType'],
		"status" => (int)$qMeta['status']),
		"data" => $r
	);
	$params = "?username=".$pv_online['email']."&password=".$pv_online['password']."&do=share&kind=formula";
	$req = pvUploadData($pvOnlineAPI.$params, json_encode($fData));	
	
	$json = json_decode($req, true);

	if($json['success']){
		$response['success'] = $json['success'];
	}
	
	if($json['error']){
		$response['error'] = $json['error'];
	}
	
	echo json_encode($response);
	return;
}

//IMPORT SHARED FORMULAS ON PV ONLINE
if($_POST['action'] == 'importShareFormula' && $_POST['fid']){
	require_once(__ROOT__.'/func/genFID.php');

	$params = "?username=".$pv_online['email']."&password=".$pv_online['password']."&do=getShared&fid=".$_POST['fid'];
    $jsonData = json_decode(file_get_contents($pvOnlineAPI.$params), true);
	
	$jsonData['meta']['name'] = $_POST['localName'];
    
	if($jsonData['error']){
       
	   $response['error'] = $json['error'];
	   echo json_encode($response);
	   return;
    }
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE name = '".$jsonData['meta']['name']."'"))){
	  $response['error'] = 'Formula name '.$jsonData['meta']['name'].' already exists, please choose a different name.';
	  echo json_encode($response);
	  return;
	}

	$newFid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');
	$q = "INSERT INTO formulasMetaData (name,product_name,fid,profile,sex,notes,defView,catClass,finalType,status) VALUES ('".$jsonData['meta']['name']."','".$jsonData['meta']['product_name']."','".$newFid."','".$jsonData['meta']['profile']."','".$jsonData['meta']['sex']."','".$jsonData['meta']['notes']."','".$jsonData['meta']['defView']."','".$jsonData['meta']['catClass']."','".$jsonData['meta']['finalType']."','".$jsonData['meta']['status']."')";
	
   $qIns = mysqli_query($conn,$q);

   $array_data = $jsonData['formula'];
   foreach ($array_data as $id=>$row) {
	  $insertPairs = array();
      	foreach ($row as $key=>$val) {
      		$insertPairs[addslashes($key)] = addslashes($val);
      	}
      $insertVals = '"'.$newFid.'",'.'"'.$jsonData['meta']['name'].'",'.'"' . implode('","', array_values($insertPairs)) . '"';
   
      if(!mysqli_num_rows(mysqli_query($conn, $query))){
       	$jsql = "INSERT INTO formulas (`fid`,`name`,`ingredient`,`concentration`,`dilutant`,`quantity`,`notes`) VALUES ({$insertVals});";
         $qIns.= mysqli_query($conn,$jsql);
      }
	}
	
    if($qIns){
		$params = "?username=".$pv_online['email']."&password=".$pv_online['password']."&do=remShared&fid=".$_POST['fid']."&download=1";
    	$jsonData = json_decode(file_get_contents($pvOnlineAPI.$params), true);
		$response['success'] = $_POST['localName'].' formula imported!';
    }else{
		$response['error'] = 'Unable to import the formula '.mysqli_error($conn);
    }
	echo json_encode($response);
    return;

}

//IMPORT INGREDIENTS FROM PV ONLINE
if($_POST['action'] == 'import' && $_POST['items']){
	$items = explode(',',trim($_POST['items']));
    $i = 0;
    foreach ($items as &$item) {
		$jAPI = $pvOnlineAPI.'?do='.$item;
        $jsonData = json_decode(file_get_contents($jAPI), true);

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

if($_POST['action'] == 'upload' && $_POST['items'] == 'ingredients'){
	//Upload all the ingredients
	$ingQ = mysqli_query($conn, "SELECT * FROM ingredients WHERE isPrivate = '0'");
	$i = 0;
	while($ing = mysqli_fetch_assoc($ingQ)){
		$ing['rID'] = $ing['id'];
		unset($ing['id'],$ing['created']);
		if($_POST['excludeNotes'] == 'true'){
			unset($ing['notes']);			
		}
		$ingData['data'][] = array_filter($ing);
		$i++;
	}
	
	//Upload all the allergens
	$algQ = mysqli_query($conn, "SELECT * FROM allergens");
	$a = 0;
	while($alg = mysqli_fetch_assoc($algQ)){
		unset($alg['id']);
		$algData['data'][] = array_filter($alg);
		$a++;
	}
	
	//Upload all the categories
	$alC = mysqli_query($conn, "SELECT id, name, notes, image, colorKey FROM ingCategory");
	$c = 0;
	while($cat = mysqli_fetch_assoc($alC)){
		$cData['data'][] = array_filter($cat);
		$c++;
	}
		
	//Upload all the synonyms
	$syn = mysqli_query($conn, "SELECT ing, cid, synonym, source FROM synonyms");
	$s = 0;
	while($synonym = mysqli_fetch_assoc($syn)){
		$sData['data'][] = array_filter($synonym);
		$s++;
	}
	
	//Upload all the suppliers
	$sup = mysqli_query($conn, "SELECT ingSupplierID, ingID, supplierLink, price, size FROM suppliers");
	$sp = 0;
	while($supplier = mysqli_fetch_assoc($sup)){
		$spData['data'][] = array_filter($supplier);
		$sp++;
	}
	
	$supMap = mysqli_query($conn, "SELECT id AS rID,name FROM ingSuppliers");
	$sm = 0;
	while($supplierMap = mysqli_fetch_assoc($supMap)){
		$smData['data'][] = array_filter($supplierMap);
		$sm++;
	}
	
	$params = "?username=".$pv_online['email']."&password=".$pv_online['password']."&do=add&kind=ingredient";
	$up_req = pvUploadData($pvOnlineAPI.$params, json_encode($ingData));
	
	if($_POST['excludeCompositions'] == 'false'){
		$params = "?username=".$pv_online['email']."&password=".$pv_online['password']."&do=add&kind=compos";
		$up_req.= pvUploadData($pvOnlineAPI.$params, json_encode($algData));
		$msg.= ', <strong>'.$a.'</strong> compositions';
	}
	
	$params = "?username=".$pv_online['email']."&password=".$pv_online['password']."&do=add&kind=category";
	$up_req.= pvUploadData($pvOnlineAPI.$params, json_encode($cData));
	
	if($_POST['excludeSynonyms'] == 'false'){
		$params = "?username=".$pv_online['email']."&password=".$pv_online['password']."&do=add&kind=synonym";
		$up_req.= pvUploadData($pvOnlineAPI.$params, json_encode($sData));
		$msg.= ', <strong>'.$s.'</strong> synonyms';
	}
	
	if($_POST['excludeSuppliers'] == 'false'){
		$params = "?username=".$pv_online['email']."&password=".$pv_online['password']."&do=add&kind=suppliers";
		$up_req.= pvUploadData($pvOnlineAPI.$params, json_encode($spData));
		$params = "?username=".$pv_online['email']."&password=".$pv_online['password']."&do=add&kind=ingSuppliers";
		$up_req.= pvUploadData($pvOnlineAPI.$params, json_encode($smData));
		$msg.= ', <strong>'.$sp.'</strong> suppliers';
	}
	
	if($up_req){
		$response['success'] = $i.' '.$msg.' and '.$c.' categories uploaded!';
	}else{
		$response['error'] = 'Unable to upload data!';
	}
	
	echo json_encode($response);
	return;
}

?>

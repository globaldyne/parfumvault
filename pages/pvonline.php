<?php 
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/func/pvOnline.php');

//SHARE FORMULA TO PV ONLINE
if($_POST['action'] == 'share' && $_POST['fid']){
	if(!$_POST['users']){
        echo  '<div class="alert alert-danger alert-dismissible">Please select user(s) first.</div>';
		return;
	}
	$fid = $_POST['fid'];
	
	$qMeta = mysqli_fetch_array(mysqli_query($conn, "SELECT name, product_name, profile, sex, defView, catClass, finalType, status FROM formulasMetaData WHERE fid = '$fid'"));
	
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
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$json['success'].'</strong></div>';
		return;
	}
	if($json['error']){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$json['error'].'</strong></div>';
		return;
	}
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
if($_GET['action'] == 'import' && $_GET['items']){
	$items = explode(',',trim($_GET['items']));
    $i = 0;
    foreach ($items as &$item) {
		$jAPI = $pvOnlineAPI.'?do='.$item;
        $jsonData = json_decode(file_get_contents($jAPI), true);

        if($jsonData['status'] == 'Failed'){
        	echo  '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error connecting or retrieving data from PV Online.</div>';
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
    	echo  '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'.$i.' ingredients and compositions imported!</div>';
    }else{
		echo  '<div class="alert alert-info alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Database already in sync! '.mysqli_error($conn).'</div>';
    }
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
	$sup = mysqli_query($conn, "SELECT ingSupplierID, ingID, supplierLink FROM suppliers");
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
		echo  '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$i.'</strong> ingredients'.$msg.' and <strong>'.$c.'</strong> categories uploaded!</div>';
	}

	return;
}

?>

<?php 
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/func/pvOnline.php');


if($_GET['action'] == 'import' && $_GET['items']){
	$items = explode(',',trim($_GET['items']));
    $i = 0;
    foreach ($items as &$item) {
		$jAPI = $pvOnlineAPI.'?do='.$item;
        $jsonData = json_decode(file_get_contents($jAPI), true);

        if($jsonData['status'] == 'Failed'){
        	echo  '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Invalid credentials or your PV Online account is inactive.</div>';
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
    	echo  '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'.$i.' ingredients imported!</div>';
    }else{
		echo  '<div class="alert alert-info alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Database already in sync! '.mysqli_error($conn).'</div>';
    }
    return;
}

if($_GET['action'] == 'upload' && $_GET['items'] == 'ingredients'){
	//Upload all the ingredients
	$ingQ = mysqli_query($conn, "SELECT * FROM ingredients WHERE isPrivate = '0'");
	$i = 0;
	while($ing = mysqli_fetch_assoc($ingQ)){
		$ing['rID'] = $ing['id'];
		unset($ing['id'],$ing['created']);
		if($_GET['excludeNotes'] == 'true'){
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
	
	$supMap = mysqli_query($conn, "SELECT name FROM ingSuppliers");
	$sm = 0;
	while($supplierMap = mysqli_fetch_assoc($supMap)){
		$smData['data'][] = array_filter($supplierMap);
		$sm++;
	}
	
	$params = "?username=".$pv_online['email']."&password=".$pv_online['password']."&do=add&kind=ingredient";
	$up_req = pvUploadData($pvOnlineAPI.$params, json_encode($ingData));
	
	if($_GET['excludeCompositions'] == 'false'){
		$params = "?username=".$pv_online['email']."&password=".$pv_online['password']."&do=add&kind=compos";
		$up_req.= pvUploadData($pvOnlineAPI.$params, json_encode($algData));
		$msg.= ', <strong>'.$a.'</strong> compositions';
	}
	
	$params = "?username=".$pv_online['email']."&password=".$pv_online['password']."&do=add&kind=category";
	$up_req.= pvUploadData($pvOnlineAPI.$params, json_encode($cData));
	
	if($_GET['excludeSynonyms'] == 'false'){
		$params = "?username=".$pv_online['email']."&password=".$pv_online['password']."&do=add&kind=synonym";
		$up_req.= pvUploadData($pvOnlineAPI.$params, json_encode($sData));
		$msg.= ', <strong>'.$s.'</strong> synonyms';
	}
	
	if($_GET['excludeSuppliers'] == 'false'){
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

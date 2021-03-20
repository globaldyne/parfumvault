<?php 
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');


if($_GET['action'] == 'import' && $_GET['items']){
        $items = explode(',',trim($_GET['items']));
        $i = 0;
        foreach ($items as &$item) {
                $jAPI = $pvOnlineAPI.'?username='.$pv_online['email'].'&password='.$pv_online['password'].'&do='.$item;
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
	//Do all the ingredients
	$ingQ = mysqli_query($conn, "SELECT * FROM ingredients");
	$i = 0;
	while($ing = mysqli_fetch_assoc($ingQ)){
		unset($ing['id'],$ing['created']);
		if($_GET['excludeNotes'] == 'true'){
			unset($ing['notes']);			
		}
		$ar = array_filter($ing);
		
		$url = http_build_query($ar);
		$jAPI = $pvOnlineAPI.'?username='.$pv_online['email'].'&password='.$pv_online['password'].'&do=add&kind=ingredient&'.$url;
		$i++;
		$up_req = file_get_contents($jAPI,true);
	}
	
	//Do all the allergens
	$algQ = mysqli_query($conn, "SELECT * FROM allergens");
	while($alg = mysqli_fetch_assoc($algQ)){
		unset($alg['id']);
		$ar = array_filter($alg);
		
		$url = http_build_query($alg);
		$jAPI = $pvOnlineAPI.'?username='.$pv_online['email'].'&password='.$pv_online['password'].'&do=add&kind=allergen&'.$url;
		$up_req.= file_get_contents($jAPI,true);
	}
	
	if($up_req){
		echo  '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'.$i.' ingredients uploaded!</div>';
	}

	return;
}
?>

<?php 
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');
require_once('../inc/product.php');


if($_GET['action'] == 'import' && $_GET['items']){
	$items = trim($_GET['items']);
	
	$jAPI = $pvOnlineAPI.'?username='.$pv_online['email'].'&password='.$pv_online['password'].'&do='.$items;
	
	$jsonData = json_decode(file_get_contents($jAPI), true);
	
	if($jsonData['status'] == 'Failed'){
		echo  '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Invalid credentials or your PV Online account is inactive.</div>';
		return;
	}
	
	$array_data = $jsonData['ingredients'];
	$i = 0;
	foreach ($array_data as $id=>$row) {
		$insertPairs = array();
		foreach ($row as $key=>$val) {
			$insertPairs[addslashes($key)] = addslashes($val);
		}
		$insertKeys = '`' . implode('`,`', array_keys($insertPairs)) . '`';
		$insertVals = '"' . implode('","', array_values($insertPairs)) . '"';
		if(!mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '".$insertPairs['name']."'"))){
			$jsql = "INSERT INTO `{$tableName}` ({$insertKeys}) VALUES ({$insertVals});";
			$qIns = mysqli_query($conn,$jsql);
			$i++;
		}
	}
	if($qIns){
		echo  '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'.$i.' ingredients imported!</div>';
	}else{
		echo  '<div class="alert alert-info alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Database already in sync! '.mysqli_error($conn).'</div>';
	}
	return;
}

?>
<?php 
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');
require_once('../inc/product.php');


if($_GET['action'] == 'import' && $_GET['items']){
	$items = $_GET['items'];
	$tableName = 'ingredients2';
	
	$jAPI = $pvOnlineAPI.'?username='.$pv_online['email'].'&password='.$pv_online['password'].'&do='.$items;
	
	$jsonData = json_decode(file_get_contents($jAPI), true);
	$array_data = $jsonData['ingredients'];
	
	foreach ($array_data as $id=>$row) {
		$insertPairs = array();
		foreach ($row as $key=>$val) {
			$insertPairs[addslashes($key)] = addslashes($val);
		}
		$insertKeys = '`' . implode('`,`', array_keys($insertPairs)) . '`';
		$insertVals = '"' . implode('","', array_values($insertPairs)) . '"';
		if(!mysqli_num_rows(mysqli_query($conn, "SELECT name FROM $tableName WHERE name = '".$insertPairs['name']."'"))){
			$jsql = "INSERT INTO `{$tableName}` ({$insertKeys}) VALUES ({$insertVals});";
			mysqli_query($conn,$jsql) or die(mysqli_error($conn));
		}
	}
	return;
}

?>
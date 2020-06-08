<?php 
define('pvault_panel', TRUE);
require_once('./inc/config.php');
require_once('./inc/opendb.php');
require_once('./func/apiCheckAuth.php');

$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen('tmp/api.log', 'a');
fwrite($fp, $req_dump);
fclose($fp);

if (isset($_GET['login'])){
	$username = $_REQUEST['username'];
	$password = $_REQUEST['password'];

	if(apiCheckAuth($username, $password ,$dbhost, $dbuser, $dbpass, $dbname)==true){
		$response['status'] = "Success";
	}else{

		$response['status'] = "Failed";
	}
        echo json_encode($response);
	exit;
}
if($_REQUEST['username'] && $_REQUEST['password'] && $_REQUEST['do']){
	$username = $_REQUEST['username'];
	$password = $_REQUEST['password'];
	$_REQUEST['do'] = strtolower($_REQUEST['do']);

	if(apiCheckAuth($username, $password ,$dbhost, $dbuser, $dbpass, $dbname)==true){
		//$LIMIT = 'LIMIT 2';
		if($_REQUEST['do'] == 'formulas' && empty($_REQUEST['name'])){
			$sql = mysqli_query($conn, "SELECT name, notes, image, fid FROM formulasMetaData ORDER BY name ASC");
		}elseif($_REQUEST['do'] == 'ingredients'){
			$sql = mysqli_query($conn, "SELECT id, name, odor FROM ingredients WHERE odor IS NOT NULL");
		}elseif($_REQUEST['do'] == 'formula' && $_REQUEST['fid']){
			$sql = mysqli_query($conn, "SELECT id, ingredient AS name, concentration AS purity, quantity AS mg FROM formulas WHERE fid = '$_REQUEST[fid]' ORDER BY ingredient ASC");	
		}elseif($_REQUEST['do'] == 'ingredient' && $_REQUEST['id']){
			$sql = mysqli_query($conn, "SELECT name, type, strength, IFRA, price, profile, odor, notes FROM ingredients WHERE id = '$_REQUEST[id]'");	
		}elseif($_REQUEST['do'] == 'ifra'){
			$sql = mysqli_query($conn, "SELECT name, risk, cat4 FROM IFRALibrary ORDER BY id ASC");	
		}elseif($_REQUEST['do'] == 'delete' && $_REQUEST['kind'] == 'ingredient'){
			$id =  $_REQUEST['id'];

			if(mysqli_query($conn, "DELETE FROM formulas WHERE id = '$id'")){

				$response['status'] = "Deleted $id";
			}else{

				$response['status'] = "Failed";
			}
			echo json_encode($response);
			exit;
		}
/*
		//ADD	
		}elseif($_REQUEST['do'] == 'add' && $_REQUEST['kind'] == 'ingredients'){
			$kind = mysqli_real_escape_string($conn, $_REQUEST['kind']);
			$ing = mysqli_real_escape_string($conn, $_REQUEST['ing']);
			$sql = mysqli_query($conn, "INSERT INTO $kind (name) VALUES ('$ing')");	
*/
		$rows = array();
		while($r = mysqli_fetch_assoc($sql)) {
    			$rows[$_REQUEST['do']][] = $r;
		}
			//print '<pre>';
			echo json_encode($rows, JSON_PRETTY_PRINT);
		}
}else{

		$response['status'] = "Failed";
		echo json_encode($response);
}
?>

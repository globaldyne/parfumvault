<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function searchIFRA($cas, $name, $get,  $defCatClass ){
	global $conn;
	if(empty($name)){
		return null;
	}
	
	if($cas !== '0'){//IGNORE VALUE FOR CARRIERS
		if($cas){
			$q = "cas = '$cas'";
		}else{
			$q = "name = '$name' OR synonyms LIKE '%$name%'";
		}
			
		$res = mysqli_fetch_array(mysqli_query($conn, "SELECT risk, $defCatClass, type, formula FROM IFRALibrary WHERE $q"));
		if($get){
			return $res[$get];
		}else{		
			if($res){
				if(!$res["$defCatClass"]){
					return $res['type'].' - '.$res['risk'];
				}else{
					if ($res["$defCatClass"] === 0 || $res["$defCatClass"] === null || !is_numeric($res["$defCatClass"])) {
						return '0'.' - '.$res['risk'];
    				}else{
						return $res["$defCatClass"].' - '.$res['risk'];
					}
				}
			}
		}
	}
}
?>

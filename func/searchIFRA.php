<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function searchIFRA($cas, $name, $get, $conn, $cat = 'cat4'){
	if($cas !== '0'){//IGNORE VALUE FOR CARRIERS
		if($cas){
			$q = "cas = '$cas'";
		}else{
			$q = "name = '$name' OR synonyms LIKE '%$name%'";
		}
			
		$res = mysqli_fetch_array(mysqli_query($conn, "SELECT risk, $cat, type, formula FROM IFRALibrary WHERE $q"));
		if($get){
			return $res[$get];
		}else{		
			if($res){
				if(!$res["$cat"]){
					return $res['type'].' - '.$res['risk'];
				}else{
					return $res["$cat"].' - '.$res['risk'];
				}
			}
		}
	}
}
?>
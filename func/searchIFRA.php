<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<?php
function searchIFRA($cas,$name,$conn){
	if($cas !== '0'){//IGNORE VALUE FOR CARRIERS
		if($cas){
			//$q = "cas LIKE '%$cas%'";
			$q = "cas = '$cas'";
			//$q = "instr(`cas`, '$cas') > 0";
			//$q = "cas REGEXP '[^\n\r]".$cas."[$\n\r]|^".$cas."$'";
		}else{
			$q = "name = '$name' OR synonyms LIKE '%$name%'";
		}
			
		$res = mysqli_fetch_array(mysqli_query($conn, "SELECT risk,cat4,type FROM IFRALibrary WHERE $q"));
		if($res){
			if(!$res['cat4']){
				return $res['type'].' - '.$res['risk'];
			}else{
				return $res['cat4'].' - '.$res['risk'];
			}
		//	return 'N/A';
		}
	}
}
?>
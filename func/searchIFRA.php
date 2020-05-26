<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<?php
function searchIFRA($cas,$name,$dbhost,$dbuser,$dbpass,$dbname){
	
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ('Error connecting to database');
	if($cas !== '0'){//IGNORE VALUE FOR CARRIERS
		if($cas){
			$q = "cas LIKE '%$cas%'";
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
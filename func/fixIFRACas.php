<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function fixIFRACas($conn){
	
	$q1 = mysqli_query($conn, "SELECT id,ifra_key,cas FROM IFRALibrary WHERE cas REGEXP '\n'");
	$fields = 'ifra_key,image,amendment,prev_pub,last_pub,deadline_existing,deadline_new,name,cas,cas_comment,synonyms,formula,flavor_use,prohibited_notes,restricted_photo_notes,restricted_notes,specified_notes,type,risk,contrib_others,contrib_others_notes,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12';
	
	while($r =  mysqli_fetch_array($q1)){
		$e = explode("\n",$r['cas']);
		foreach($e as $value){
	
			$n = "INSERT INTO IFRALibrary ($fields) SELECT  ifra_key,image,amendment,prev_pub,last_pub,deadline_existing,deadline_new,name,'$value',cas_comment,synonyms,formula,flavor_use,prohibited_notes,restricted_photo_notes,restricted_notes,specified_notes,type,risk,contrib_others,contrib_others_notes,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12 FROM IFRALibrary WHERE id = '".$r['id']."'";
			mysqli_query($conn, $n);
		}
	}
	mysqli_query($conn, "DELETE FROM IFRALibrary WHERE cas REGEXP '\n'");
	
}
?>
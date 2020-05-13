<?php
if($_GET['name']){

	require_once('../inc/config.php');
	require_once('../inc/opendb.php');
	
	$name = mysqli_real_escape_string($conn, $_GET['name']);
	 
	$result = mysqli_query($conn,"SELECT ingredient, concentration, quantity FROM formulas WHERE name = '$name'");    
	header("Content-Type: application/xls");    
	header('Content-Disposition: attachment; filename='.urlencode($name).'.xls');  
	header("Pragma: no-cache"); 
	header("Expires: 0");

	$sep = "\t";
	for ($i = 0; $i < mysqli_num_fields($result); $i++) {
		echo strtoupper(mysqli_fetch_field_direct($result,$i)->name) . "\t";
	}
	print("\n");    
		while($row = mysqli_fetch_row($result)){
			$schema_insert = "";
			for($j=0; $j<mysqli_num_fields($result);$j++){
				if(!isset($row[$j])){
					$schema_insert .= "NULL".$sep;
				}elseif ($row[$j] != ""){
					$schema_insert .= "$row[$j]".$sep;
				}else{
					$schema_insert .= "".$sep;
				}
			}
			$schema_insert = str_replace($sep."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			print(trim($schema_insert));
			print "\n";
		}   
}else{
	header('Location: /');
}
?>
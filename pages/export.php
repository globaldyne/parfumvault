<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if($_GET['format'] == 'csv' && $_GET['kind'] == 'ingredients'){
	$defCatClass = $settings['defCatClass'];
	$r = mysqli_query($conn, "SELECT name,INCI,cas,FEMA,type,strength,category,profile,$defCatClass,physical_state,allergen FROM ingredients");
	
	$ing = array();
	if (mysqli_num_rows($r) > 0) {
		while ($row = mysqli_fetch_assoc($r)) {
			$ing[] = $row;
		}
	}

	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename='.$_GET['kind'].'.csv');
	$output = fopen('php://output', 'w');
	fputcsv($output, array('Name', 'INCI', 'CAS', 'FEMA', 'Type', 'Strength', 'Category', 'Profile', 'Category Class', 'Physical State', 'Allergen'));
	
	if (count($ing) > 0) {
		foreach ($ing as $row) {
			fputcsv($output, $row);
		}
	}
	
	return;	
}

?>

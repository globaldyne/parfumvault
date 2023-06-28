<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function get_formula_notes($conn, $fid, $cat) {
	$formula_q = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '$fid' ORDER BY exclude_from_summary");
	while ($formula = mysqli_fetch_array($formula_q)){
		$form[] = $formula;
	}
	
	foreach ($form as $formula){
		$top_ing = mysqli_fetch_array(mysqli_query($conn, "SELECT name AS ing,category FROM ingredients WHERE name = '".$formula['ingredient']."' AND profile = 'Top' AND category IS NOT NULL"));
		
		$heart_ing = mysqli_fetch_array(mysqli_query($conn, "SELECT name AS ing,category FROM ingredients WHERE name = '".$formula['ingredient']."' AND profile = 'Heart' AND category IS NOT NULL"));
		
		$base_ing = mysqli_fetch_array(mysqli_query($conn, "SELECT name AS ing,category FROM ingredients WHERE name = '".$formula['ingredient']."' AND profile = 'Base' AND category IS NOT NULL"));
	
	
		$top_cat = mysqli_fetch_array(mysqli_query($conn, "SELECT image,name FROM ingCategory WHERE id = '".$top_ing['category']."' AND image IS NOT NULL"));
		$heart_cat = mysqli_fetch_array(mysqli_query($conn, "SELECT image,name FROM ingCategory WHERE id = '".$heart_ing['category']."' AND image IS NOT NULL"));
		$base_cat = mysqli_fetch_array(mysqli_query($conn, "SELECT  image,name FROM ingCategory WHERE id = '".$base_ing['category']."' AND image IS NOT NULL"));
		
		$top['name'] = $top_cat['name'];
		$top['image'] = $top_cat['image'];
		$top['ing'] = $top_ing['ing'];
		
		$heart['name'] = $heart_cat['name'];
		$heart['image'] = $heart_cat['image'];
		$heart['ing'] = $heart_ing['ing'];
		
		$base['name'] = $base_cat['name'];
		$base['image'] = $base_cat['image'];
		$base['ing'] = $base_ing['ing'];
		
		$tx[] = $top;
		$hx[] = $heart;
		$bx[] = $base;

	}
	
	if($cat == 'top'){
		return arrFilter(array_filter($tx));
	}
	if($cat == 'heart'){
		return arrFilter(array_filter($hx));
	}
	if($cat == 'base'){
		return arrFilter(array_filter($bx));	
	}
	return;
}

function get_formula_excludes($conn, $fid, $cat) {
	$q = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '$fid' AND exclude_from_summary = '1'");
	while ($formula = mysqli_fetch_array($q)){

		if($cat == 'top'){
			$top_ex = mysqli_fetch_array(mysqli_query($conn, "SELECT category FROM ingredients WHERE name = '".$formula['ingredient']."' AND profile = 'Top' AND category IS NOT NULL"));
			$value = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingCategory WHERE id = '".$top_ex['category']."'"));
			$v[] = $value['name'];
		}

		if($cat == 'heart'){
			$heart_ex = mysqli_fetch_array(mysqli_query($conn, "SELECT category FROM ingredients WHERE name = '".$formula['ingredient']."' AND profile = 'Heart' AND category IS NOT NULL"));
			$value = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingCategory WHERE id = '".$heart_ex['category']."'"));
			$v[] = $value['name'];
		}
	
		if($cat == 'base'){
			$base_ex = mysqli_fetch_array(mysqli_query($conn, "SELECT category FROM ingredients WHERE name = '".$formula['ingredient']."' AND profile = 'Base' AND category IS NOT NULL"));
			$value = mysqli_fetch_array(mysqli_query($conn, "SELECT  name FROM ingCategory WHERE id = '".$base_ex['category']."'"));
			$v[] = $value['name'];
		}
		
	}
	return array_filter((array)$v);
}

?>
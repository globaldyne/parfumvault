<?php
if (!defined('pvault_panel')){ die('Not Found');}

function pubChem($cas, $type){
	
	$api = 'https://pubchem.ncbi.nlm.nih.gov/rest/pug';
	$cids = trim(file_get_contents($api.'/compound/name/'.$cas.'/cids/TXT'));
	if($type = 'PNG'){
		$image = file_get_contents($api.'/compound/cid/'.$cids.'/'.$type.'?record_type=2d&image_size=large');
		return base64_encode($image);
	}
}
?>
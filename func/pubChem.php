<?php 
if (!defined('pvault_panel')){ die('Not Found');}

function pubChem($cas, $view, $image){
	
	$cas = trim($_GET['cas']);
	$type = 'PNG';
	$api = 'https://pubchem.ncbi.nlm.nih.gov/rest/pug';

	if(preg_match('/(Mixture|Blend)/i', $cas) === 1){	
		echo  '<div class="alert alert-info">Data not available for mixtures</div>';
		return;
	}

	$cids = explode("\n",trim(pv_file_get_contents($api.'/compound/name/'.$cas.'/cids/TXT')));
	if($image){
		$data = 'data:image/png;base64,'.base64_encode(pv_file_get_contents($api.'/compound/cid/'.$cids['0'].'/'.$type.'?record_type='.$view.'&image_size=large'));
	}else{	
		$data = json_decode(trim(pv_file_get_contents($api.'/compound/name/'.$cas.'/JSON')),true);
	}
	return $data;
	
}


?>


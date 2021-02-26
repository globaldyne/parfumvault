<?php

if(!$_GET['cas']){
	echo 'Error: Missing CAS number';
	return;
}
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');
require_once('../func/pvFileGet.php');

$cas = trim($_GET['cas']);
$type = 'PNG';

if(preg_match('/(Mixture|Blend)/i', $cas) === 1){	
	echo  '<div class="alert alert-info">Data not available for mixtures</div>';
	return;
}

$api = 'https://pubchem.ncbi.nlm.nih.gov/rest/pug';
$cids = explode("\n",trim(pv_file_get_contents($api.'/compound/name/'.$cas.'/cids/TXT')));

$image = 'data:image/png;base64,'.base64_encode(pv_file_get_contents($api.'/compound/cid/'.$cids['0'].'/'.$type.'?record_type='.$settings['pubchem_view'].'&image_size=large'));
$data = json_decode(trim(pv_file_get_contents($api.'/compound/name/'.$cas.'/JSON')),true);
		
if(empty($data)){
	echo  '<div class="alert alert-info">Data not available</div>';
	return;
}
?>

<table width="100%" border="0">
                  <tr>
                    <td width="20%" rowspan="5" valign="top"><img src="<?php echo $image;?>"/></td>
                    <td width="34%">Molecular Formula:</td>
                    <td width="46%"><strong><?php echo $data['PC_Compounds']['0']['props']['16']['value']['sval'];?></strong></td>
                  </tr>
                  <tr>
                    <td>Molecular Weight:</td>
                    <td><strong><?php echo $data['PC_Compounds']['0']['props']['17']['value']['fval'];?></strong></td>
                  </tr>
                  <tr>
                    <td>Canonical Smiles:</td>
                    <td><strong><?php echo $data['PC_Compounds']['0']['props']['18']['value']['sval'];?></strong></td>
                  </tr>
                  <tr>
                    <td colspan="2">&nbsp;</td>
                  </tr>
                  <tr>
                   <td colspan="2">&nbsp;</td>
            </tr>
</table>

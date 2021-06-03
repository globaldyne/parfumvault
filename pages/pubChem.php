<?php

if(!$_GET['cas']){
	echo 'Error: Missing CAS number';
	return;
}
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/pvFileGet.php');

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

if($molecularWeight = $data['PC_Compounds']['0']['props']['17']['value']['fval']){
	mysqli_query($conn, "UPDATE ingredients SET molecularWeight = '$molecularWeight' WHERE cas='$cas'");
}
if($logP = $data['PC_Compounds']['0']['props']['14']['value']['fval']){
	mysqli_query($conn, "UPDATE ingredients SET logp = '$logP' WHERE cas='$cas'");
}
if($molecularFormula = $data['PC_Compounds']['0']['props']['18']['value']['sval']){
	mysqli_query($conn, "UPDATE ingredients SET formula = '$molecularFormula' WHERE cas='$cas'");
}
if(empty($data)){
	echo  '<div class="alert alert-info">Data not available</div>';
	return;
}

?>
<script>
$(document).ready(function(){    
     $("#molecularWeight").val('<?=$molecularWeight?>');
     $("#logP").val('<?=$logP?>');
     $("#molecularFormula").val('<?=$molecularFormula?>');
});
</script>
<table width="100%" border="0">
                  <tr>
                    <td width="20%" rowspan="7" valign="top"><img src="<?php echo $image;?>"/></td>
                    <td width="34%">Molecular Formula:</td>
                    <td width="46%"><strong><?php echo $data['PC_Compounds']['0']['props']['16']['value']['sval'];?></strong></td>
                  </tr>
                  <tr>
                    <td>Molecular Weight:</td>
                    <td><strong><?php echo $data['PC_Compounds']['0']['props']['17']['value']['sval'];?></strong></td>
                  </tr>
                  <tr>
                    <td>Canonical Smiles:</td>
                    <td><strong><?php echo $data['PC_Compounds']['0']['props']['18']['value']['sval'];?></strong></td>
                  </tr>
                  <tr>
                    <td>Mass:</td>
                    <td><strong><?php echo $data['PC_Compounds']['0']['props']['15']['value']['sval'];?></strong></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="2">&nbsp;</td>
                  </tr>
                  <tr>
                   <td colspan="2">&nbsp;</td>
            </tr>
</table>

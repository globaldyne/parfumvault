<?php

if(!$_GET['cas']){
	echo 'Error: Missing CAS number';
	return;
}
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/pvFileGet.php');

$cas = trim($_GET['cas']);
$type = 'PNG';

if(preg_match('/(Mixture|Blend)/i', $cas) === 1){	
	echo  '<div class="alert alert-info">Data not available for mixtures</div>';
	return;
}

$properties = 'MolecularFormula,MolecularWeight,XLogP,IUPACName,CanonicalSMILES,ExactMass';

$image = 'data:image/png;base64,'.base64_encode(pv_file_get_contents($pubChemApi.'/pug/compound/name/'.$cas.'/'.$type.'?record_type='.$settings['pubchem_view'].'&image_size=large'));
$data = json_decode(trim(pv_file_get_contents($pubChemApi.'/pug/compound/name/'.$cas.'/property/'.$properties.'/JSON')),true);

$molecularWeight = $data['PropertyTable']['Properties']['0']['MolecularWeight'];
$logP = $data['PropertyTable']['Properties']['0']['XLogP'];
$molecularFormula = $data['PropertyTable']['Properties']['0']['MolecularFormula'];
$InChI = $data['PropertyTable']['Properties']['0']['IUPACName'];
$CanonicalSMILES = $data['PropertyTable']['Properties']['0']['CanonicalSMILES'];
$ExactMass = $data['PropertyTable']['Properties']['0']['ExactMass'];

if(empty($data)){
	echo  '<div class="alert alert-info">Data not available</div>';
	return;
}
?>
<script>
$(document).ready(function(){
  
$('#pubChemDataJ').on('click', '[id*=btnUpdatePub]', function () {
	$.ajax({ 
		url: 'update_data.php', 
		type: 'POST',
		data: {
			pubChemData: 'update',
			molecularWeight: "<?=$molecularWeight?>",
			logP: "<?=$logP?>",
			molecularFormula: "<?=$molecularFormula?>",
			InChI: "<?=$InChI?>",
			CanonicalSMILES: "<?=$CanonicalSMILES?>",
			ExactMass: "<?=$ExactMass?>",
			cas: "<?=$cas?>",
			},
		dataType: 'html',
		success: function (data) {
			$('#ingMsg').html(data);
			$("#INCI").val("<?=$InChI?>");
			reload_overview();
		}
	  });             
});
     
});
</script>
<table width="100%" border="0" id="pubChemDataJ">
  <tr>
    <td width="20%" rowspan="7" valign="top"><img src="<?php echo $image;?>"/></td>
    <td width="34%">Molecular Formula:</td>
    <td width="46%"><strong><?php echo $molecularFormula;?></strong></td>
  </tr>
  <tr>
    <td>Molecular Weight:</td>
    <td><strong><?php echo $molecularWeight;?></strong></td>
  </tr>
  <tr>
    <td>Canonical Smiles:</td>
    <td><strong><?php echo $CanonicalSMILES;?></strong></td>
  </tr>
  <tr>
    <td>Mass:</td>
    <td><strong><?php echo $ExactMass;?></strong></td>
  </tr>
  <tr>
    <td>XLogP:</td>
    <td><strong><?php echo $logP;?></strong></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><input type="submit" class="btn btn-info" name="btnUpdatePub" id="btnUpdatePub" value="Update data" /></td>
  </tr>
  <tr>
   <td colspan="2">&nbsp;</td>
</tr>
</table>

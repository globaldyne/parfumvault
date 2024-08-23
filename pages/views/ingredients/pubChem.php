<?php

if(!$_GET['cas']){
	echo 'Error: Missing CAS number';
	return;
}
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

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

$p = base64_encode(pv_file_get_contents($pubChemApi.'/pug/compound/name/'.$cas.'/'.$type.'?record_type='.$settings['pubchem_view'].'&image_size=large'));

if(!$p){//FALL BACK TO 2D	
	$p = base64_encode(pv_file_get_contents($pubChemApi.'/pug/compound/name/'.$cas.'/'.$type.'?record_type=2d&image_size=large'));
}
$image = 'data:image/png;base64,'.$p;

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
  
$('#btnUpdatePub').on('click', function () {
	$.ajax({ 
		url: '/pages/update_data.php', 
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
		dataType: 'JSON',
		success: function (data) {
			$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
			$('.toast-header').removeClass().addClass('toast-header alert-success');
			$("#INCI").val("<?=$InChI?>");
			reload_overview();
			$('.toast').toast('show');
		},
		error: function (xhr, status, error) {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
			$('.toast').toast('show');
		}
	  });             
});
     
});
</script>
<h3>Pub Chem Data</h3>
<div class="container">
  <div class="row">
    <div class="col-md-2">
      <img src="<?php echo $image;?>" class="img-fluid d-block" alt="Molecule Image" />
    </div>
    <div class="col-md-10">
      <div class="row mb-2">
        <div class="col-md-2">Molecular Formula</div>
        <div class="col-md-8"><strong><?php echo $molecularFormula;?></strong></div>
      </div>
      <div class="row mb-2">
        <div class="col-md-2">Molecular Weight</div>
        <div class="col-md-8"><strong><?php echo $molecularWeight;?></strong></div>
      </div>
      <div class="row mb-2">
        <div class="col-md-2">Canonical Smiles</div>
        <div class="col-md-8"><strong><?php echo $CanonicalSMILES;?></strong></div>
      </div>
      <div class="row mb-2">
        <div class="col-md-2">Mass</div>
        <div class="col-md-8"><strong><?php echo $ExactMass;?></strong></div>
      </div>
      <div class="row mb-2">
        <div class="col-md-2">XLogP</div>
        <div class="col-md-8"><strong><?php echo $logP;?></strong></div>
      </div>
      <div class="row">
        <div class="col-12">
        	<input class="btn btn-primary mx-2" name="btnUpdatePub" id="btnUpdatePub" value="Update data" />
	    	<a href="https://pubchem.ncbi.nlm.nih.gov/#query=<?=$cas?>" target="_blank">
    			<button class="btn btn-warning" name="btnViewPub" id="btnViewPub">
        			View in PubChem <i class="fa-solid fa-arrow-up-right-from-square"></i>
    			</button>
			</a>
        </div>
      </div>
    </div>
  </div>
</div>




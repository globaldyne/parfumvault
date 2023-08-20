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
<h3>Pub Chem Data</h3>
<div class="col dropdown-divider"></div>
<div class="card rounded" id="pubChemDataJ">
    <div class="card-body"> 
        <div class="col-sm-6">
            <div class="col-sm-5">
                <img alt="Structure image" src="<?php echo $image;?>"/>
            </div>
            <div class="row">
            	<div class="col">
                	Molecular Formula: <label><?php echo $molecularFormula;?></label>
            	</div>
            </div>

             <div class="row">
             	<div class="col">
                	Canonical Smiles: <label><?php echo $CanonicalSMILES;?></label>
            	</div>
             </div>
             <div class="row">
             	<div class="col">
                	Mass: <label><?php echo $ExactMass;?></label>
            	</div>
             </div>
             <div class="row">
             	<div class="col">
                	XLogP: <label><?php echo $logP;?></label>
            	</div>
             </div> 
             <div class="col dropdown-divider"></div>
             <div class="row mt-2">
             	<div class="col">
                	<input type="submit" class="btn btn-info" name="btnUpdatePub" id="btnUpdatePub" value="Update data" />
            	</div>
             </div>                          
        </div>
    </div>
</div>



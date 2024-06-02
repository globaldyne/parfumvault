<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 


require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

if(!$_POST['ingID']){
	echo 'Invalid ID';
	return;
}

$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT id,tenacity,flash_point,chemical_name,formula,logp,soluble,molecularWeight,appearance,rdi FROM ingredients WHERE id = '".$_POST['ingID']."'"));


?>
<h3>Techical Data</h3>
<hr>
<div class="container">
    <div class="row mb-3">
        <label for="tenacity" class="col-sm-2 col-form-label">Tenacity</label>
        <div class="col-sm-10">
            <input name="tenacity" type="text" class="form-control" id="tenacity" value="<?php echo $ing['tenacity']; ?>"/>
        </div>
    </div>
    <div class="row mb-3">
        <label for="rdi" class="col-sm-2 col-form-label">Relative Odor Impact</label>
        <div class="col-sm-10">
            <input name="rdi" type="text" class="form-control" id="rdi" value="<?php echo $ing['rdi']; ?>"/>
        </div>
    </div>
    <div class="row mb-3">
        <label for="flash_point" class="col-sm-2 col-form-label">Flash Point</label>
        <div class="col-sm-10">
            <input name="flash_point" type="text" class="form-control" id="flash_point" value="<?php echo $ing['flash_point']; ?>"/>
        </div>
    </div>
    <div class="row mb-3">
        <label for="chemical_name" class="col-sm-2 col-form-label">Chemical Name</label>
        <div class="col-sm-10">
            <input name="chemical_name" type="text" class="form-control" id="chemical_name" value="<?php echo $ing['chemical_name']; ?>"/>
        </div>
    </div>
    <div class="row mb-3">
        <label for="molecularFormula" class="col-sm-2 col-form-label">Molecular Formula</label>
        <div class="col-sm-10">
            <input name="formula" type="text" class="form-control" id="molecularFormula" value="<?php echo $ing['formula']; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <label for="logp" class="col-sm-2 col-form-label">Log/P</label>
        <div class="col-sm-10">
            <input name="logp" type="text" class="form-control" id="logp" value="<?php echo $ing['logp']; ?>"/>
        </div>
    </div>
    <div class="row mb-3">
        <label for="soluble" class="col-sm-2 col-form-label">Soluble in</label>
        <div class="col-sm-10">
            <input name="soluble" type="text" class="form-control" id="soluble" value="<?php echo $ing['soluble']; ?>"/>
        </div>
    </div>
    <div class="row mb-3">
        <label for="molecularWeight" class="col-sm-2 col-form-label">Molecular Weight</label>
        <div class="col-sm-10">
            <input name="molecularWeight" type="text" class="form-control" id="molecularWeight" value="<?php echo $ing['molecularWeight']; ?>"/>
        </div>
    </div>
    <div class="row mb-3">
        <label for="appearance" class="col-sm-2 col-form-label">Appearance</label>
        <div class="col-sm-10">
            <input name="appearance" type="text" class="form-control" id="appearance" value="<?php echo $ing['appearance']; ?>"/>
        </div>
    </div>
    <hr />
    <input type="submit" name="save" class="btn btn-primary" id="saveTechData" value="Save" />
</div>

    
<script>

$('#saveTechData').click(function() {
	$.ajax({ 
		url: 'update_data.php', 
		type: 'POST',
		data: {
			manage: 'ingredient',
			tab: 'tech_data',
			ingID: '<?=$ing['id'];?>',
			tenacity: $("#tenacity").val(),
			flash_point: $("#flash_point").val(),
			chemical_name: $("#chemical_name").val(),
			formula: $("#molecularFormula").val(),
			logp: $("#logp").val(),
			soluble: $("#soluble").val(),
			molecularWeight: $("#molecularWeight").val(),
			appearance: $("#appearance").val(),
			rdi: $("#rdi").val()
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
				$('.toast-header').removeClass().addClass('toast-header alert-success');
			}else{
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
			}
			$('.toast').toast('show');
		}
	});
});

</script>

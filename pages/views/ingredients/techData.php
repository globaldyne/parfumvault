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
        <div class="col-md-6">
            <label for="tenacity" class="col-form-label">Tenacity</label>
            <input name="tenacity" type="text" class="form-control" id="tenacity" value="<?php echo $ing['tenacity']; ?>"/>
        </div>
        <div class="col-md-6">
            <label for="rdi" class="col-form-label">Relative Odor Impact</label>
            <input name="rdi" type="text" class="form-control" id="rdi" value="<?php echo $ing['rdi']; ?>"/>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="flash_point" class="col-form-label">Flash Point</label>
            <input name="flash_point" type="text" class="form-control" id="flash_point" value="<?php echo $ing['flash_point']; ?>"/>
        </div>
        <div class="col-md-6">
            <label for="chemical_name" class="col-form-label">Chemical Name</label>
            <input name="chemical_name" type="text" class="form-control" id="chemical_name" value="<?php echo $ing['chemical_name']; ?>"/>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="molecularFormula" class="col-form-label">Molecular Formula</label>
            <input name="formula" type="text" class="form-control" id="molecularFormula" value="<?php echo $ing['formula']; ?>">
        </div>
        <div class="col-md-6">
            <label for="logp" class="col-form-label">Log/P</label>
            <input name="logp" type="text" class="form-control" id="logp" value="<?php echo $ing['logp']; ?>"/>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="soluble" class="col-form-label">Soluble in</label>
            <input name="soluble" type="text" class="form-control" id="soluble" value="<?php echo $ing['soluble']; ?>"/>
        </div>
        <div class="col-md-6">
            <label for="molecularWeight" class="col-form-label">Molecular Weight</label>
            <input name="molecularWeight" type="text" class="form-control" id="molecularWeight" value="<?php echo $ing['molecularWeight']; ?>"/>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="appearance" class="col-form-label">Appearance</label>
            <input name="appearance" type="text" class="form-control" id="appearance" value="<?php echo $ing['appearance']; ?>"/>
        </div>
    </div>

    <hr />
    <input type="submit" name="save" class="btn btn-primary" id="saveTechData" value="Save" />
</div>


    
<script>
$(document).ready(function() {

	$('#saveTechData').click(function() {
		$.ajax({ 
			url: '/pages/update_data.php', 
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
					$('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
					$('.toast-header').removeClass().addClass('toast-header alert-success');
				}else{
					$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error);
					$('.toast-header').removeClass().addClass('toast-header alert-danger');
				}
				$('.toast').toast('show');
			}
		});
	});

});

</script>

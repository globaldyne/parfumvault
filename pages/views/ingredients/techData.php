<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 


require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if(!$_GET['ingID']){
	echo 'Invalid ID';
	return;
}

$query = "SELECT id, tenacity, flash_point, chemical_name, formula, logp, soluble, molecularWeight, appearance, rdi, shelf_life FROM ingredients WHERE id = '".$_GET['ingID']."'";
$result = mysqli_query($conn, $query);
$ing = mysqli_fetch_array($result);

$ing['soluble'] = explode(',', $ing['soluble']);

?>
<h3>Technical Data</h3>
<hr>
<div class="container">
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="tenacity" class="form-label">Tenacity</label>
            <input name="tenacity" type="text" class="form-control" id="tenacity" value="<?php echo $ing['tenacity']; ?>" />
        </div>
        <div class="col-md-6">
            <label for="rdi" class="form-label">Relative Odor Impact</label>
            <input name="rdi" type="text" class="form-control" id="rdi" value="<?php echo $ing['rdi']; ?>" />
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="flash_point" class="form-label">Flash Point</label>
            <div class="input-group">
            	<input name="flash_point" type="text" class="form-control" id="flash_point" value="<?php echo $ing['flash_point']; ?>" />
            	<span class="input-group-text" id="flash-point-addon"><?=$settings['temp_sys']?></span>
           	</div>
        </div>
        <div class="col-md-6">
            <label for="chemical_name" class="form-label">Chemical Name</label>
            <input name="chemical_name" type="text" class="form-control" id="chemical_name" value="<?php echo $ing['chemical_name']; ?>" />
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="molecularFormula" class="form-label">Molecular Formula</label>
            <input name="formula" type="text" class="form-control" id="molecularFormula" value="<?php echo $ing['formula']; ?>">
        </div>
        <div class="col-md-6">
            <label for="logp" class="form-label">Log/P</label>
            <input name="logp" type="text" class="form-control" id="logp" value="<?php echo $ing['logp']; ?>" />
        </div>
    </div>

    <div class="row mb-3">
        <label for="soluble" class="form-label">Soluble in</label>
        <div class="col-md-6">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="soluble[]" id="solubleWater" value="1" 
                    <?php if(in_array('1', $ing['soluble'])) echo 'checked'; ?>>
                <label class="form-check-label" for="solubleWater">Water</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="soluble[]" id="solubleEthanol" value="2" 
                    <?php if(in_array('2', $ing['soluble'])) echo 'checked'; ?>>
                <label class="form-check-label" for="solubleEthanol">Ethanol</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="soluble[]" id="solubleDPG" value="3" 
                    <?php if(in_array('3', $ing['soluble'])) echo 'checked'; ?>>
                <label class="form-check-label" for="solubleDPG">DPG</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="soluble[]" id="solubleIPM" value="4" 
                    <?php if(in_array('4', $ing['soluble'])) echo 'checked'; ?>>
                <label class="form-check-label" for="solubleIPM">IPM</label>
            </div>
        </div>
        <div class="col-md-6">
            <label for="molecularWeight" class="form-label">Molecular Weight</label>
            <input name="molecularWeight" type="text" class="form-control" id="molecularWeight" value="<?php echo $ing['molecularWeight']; ?>" />
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="appearance" class="form-label">Appearance</label>
            <input name="appearance" type="text" class="form-control" id="appearance" value="<?php echo $ing['appearance']; ?>" />
        </div>
        <div class="col-md-6">
            <label for="shelf_life" class="form-label">Shelf Life</label>
            <div class="input-group">
            	<input name="shelf_life" type="text" class="form-control" id="shelf_life" value="<?php echo $ing['shelf_life']; ?>" />
            	<span class="input-group-text" id="shelf-life-addon">Months</span>
            </div>
        </div>
    </div>

    <hr />
    <input type="submit" name="save" class="btn btn-primary" id="saveTechData" value="Save" />
</div>




    
<script>
$(document).ready(function() {

	$('#saveTechData').click(function() {
		var solubleValues = [];
		$('input[name="soluble[]"]:checked').each(function() {
			solubleValues.push($(this).val());
		});
		$.ajax({ 
			url: '/core/core.php', 
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
				soluble: solubleValues.join(','),
				molecularWeight: $("#molecularWeight").val(),
				appearance: $("#appearance").val(),
				rdi: $("#rdi").val(),
				shelf_life: $("#shelf_life").val()
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
			},
			error: function (xhr, status, error) {
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		});
	});

});

</script>

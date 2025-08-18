<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 


require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if(!$_GET['ingID']){
	echo 'Invalid ID';
	return;
}
$ingData = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingredients WHERE id = '$ingID' AND owner_id = '$userID'"));

$query = "SELECT id, tenacity, flash_point, chemical_name, formula, logp, soluble, molecularWeight, appearance, rdi, shelf_life FROM ingredients WHERE id = '".$_GET['ingID']."' AND owner_id = '$userID'";
$result = mysqli_query($conn, $query);
$ing = mysqli_fetch_array($result);

$ing['soluble'] = explode(',', $ing['soluble']);

?>
<div class="d-flex align-items-center justify-content-between">
  <h3 class="mb-0">Technical Data</h3>
 <!--
  <button type="button" id="ai-tech-btn" class="btn btn-outline-secondary ai-border ms-3">
    <i class="fa fa-robot"></i> AI Fill
  </button>
-->
</div>
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
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		});
	});

	$('#ai-tech-btn').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-robot fa-spin"></i> AI Fill');
        $.post('/core/core.php', { action: 'aiChat', message: "Get technical data for <?=$ingData['name']?>" }, function(resp) {
            try {
                var parsed = JSON.parse(resp);
                if (parsed.success) {
                    var aiData = parsed.success[0] ? parsed.success[0] : parsed.success;
                    if (aiData.tenacity) $("#tenacity").val(aiData.tenacity);
                    if (aiData.rdi) $("#rdi").val(aiData.rdi);
                    if (aiData.flash_point) $("#flash_point").val(aiData.flash_point);
                    if (aiData.chemical_name) $("#chemical_name").val(aiData.chemical_name);
                    if (aiData.formula) $("#molecularFormula").val(aiData.formula);
                    if (aiData.logp) $("#logp").val(aiData.logp);
                    if (aiData.molecularWeight) $("#molecularWeight").val(aiData.molecularWeight);
                    if (aiData.appearance) $("#appearance").val(aiData.appearance);
                    if (aiData.shelf_life) $("#shelf_life").val(aiData.shelf_life);

                    // Solubility checkboxes
                    if (aiData.soluble) {
                        var solArr = Array.isArray(aiData.soluble) ? aiData.soluble : String(aiData.soluble).split(',');
                        $('input[name="soluble[]"]').prop('checked', false);
                        solArr.forEach(function(val) {
                            val = String(val).trim();
                            if (val === "Water" || val === "1") $('#solubleWater').prop('checked', true);
                            if (val === "Ethanol" || val === "2") $('#solubleEthanol').prop('checked', true);
                            if (val === "DPG" || val === "3") $('#solubleDPG').prop('checked', true);
                            if (val === "IPM" || val === "4") $('#solubleIPM').prop('checked', true);
                        });
                    }
                } else if (parsed.error) {
                    $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>' + parsed.error);
                    $('.toast-header').removeClass().addClass('toast-header alert-danger');
                    $('.toast').toast('show');
                }
            } catch (e) {
                $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>AI response error.');
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
                $('.toast').toast('show');
            }
            $btn.prop('disabled', false).html('<i class="fa fa-robot"></i> AI Fill');
        });
    });
});

</script>

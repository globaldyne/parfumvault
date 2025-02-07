<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
if(!$_GET['ingID']){
	$response["error"] = 'Invalid ID';
	echo json_encode($response);
	return;
}

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/inc/settings.php');

$StandardIFRACategories = mysqli_query($conn, "SELECT name,description,type FROM IFRACategories WHERE type = '1' ORDER BY id ASC"); //PUBLIC
while($cats_res = mysqli_fetch_array($StandardIFRACategories)){
	$cats[] = $cats_res;
}

$rows = count($cats);
$counter = 0;
$cols = 3;
$usageStyle = array('even_ing','odd_ing');
$defCatClass = $settings['defCatClass'];

$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT id, cas,name,usage_type,noUsageLimit,byPassIFRA,flavor_use,allergen,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12 FROM ingredients WHERE id = '".$_GET['ingID']."' AND owner_id = '$userID'"));

$usageLimit = searchIFRA($ing['cas'],$ing['name'],null,$defCatClass);

if($usageLimit){ 
	$noLimit = 'disabled'; 
	$byPass = 'enabled';
}else{
	$byPass = 'disabled';
}
?>

<h3>Usage & Limits</h3>
<hr>
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-1">
            Bypass IFRA 
            <i rel="tip" title="Enable this to bypass IFRA values and set your own. This is not recommended though." class="pv_point_gen fas fa-info-circle"></i>
        </div>
        <div class="col-sm-1">
            <input name="byPassIFRA" type="checkbox" <?php echo $byPass ?: 'disabled'; ?> id="byPassIFRA" value="1" <?php if($ing['byPassIFRA'] == '1'){ ?> checked="checked" <?php } ?>/>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-sm-1">
            No usage limit
            <i rel="tip" title="This will set all values to 100% if no IFRA entries found or IFRA lookup is bypassed." class="pv_point_gen fas fa-info-circle"></i>
        </div>
        <div class="col-sm-1">
            <input name="noUsageLimit" type="checkbox" <?php echo $noLimit; ?> id="noUsageLimit" value="1" <?php if($ing['noUsageLimit'] == '1'){ ?> checked="checked" <?php } ?>/>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-sm-1">
            Flavor use
        </div>
        <div class="col-sm-1">
            <input name="flavor_use" type="checkbox" id="flavor_use" value="1" <?php if($ing['flavor_use'] == '1'){ ?> checked="checked" <?php } ?>/>
        </div>
    </div>
    <div class="row mb-2">
    	<div class="col-sm-1">
        	<label class="form-check-label" for="isAllergen" >To Declare</label>
            <i class="fa-solid fa-circle-info mx-2 pv_point_gen" rel="tip" title="If enabled, ingredient name will be printed in the box label."></i>
        </div>
        <div class="col-sm-1">
            <input name="isAllergen" type="checkbox" id="isAllergen" value="1" <?php if($ing['allergen'] == '1'){; ?> checked="checked"  <?php } ?>/>
        </div>    
    </div>

    <div class="row mb-3">
        <div class="col-sm-1">
            Usage classification
        </div>
        <div class="col-sm-4">
            <div id="class_bypass"><?php echo $usageLimit['type'].' - '.$usageLimit['risk']; ?></div>
            <div id="usage_type_bypass">
                <select name="usage_type" id="usage_type" class="form-select">
                    <option value="1" <?php if($ing['usage_type'] == "1") echo 'selected'; ?>>Recommendation</option>
                    <option value="2" <?php if($ing['usage_type'] == "2") echo 'selected'; ?>>Restriction</option>
                    <option value="3" <?php if($ing['usage_type'] == "3") echo 'selected'; ?>>Specification</option>
                    <option value="4" <?php if($ing['usage_type'] == "4") echo 'selected'; ?>>Prohibition</option>
                </select>
            </div>
        </div>
    </div>
</div>
<hr />
<div class="container-fluid">
    <table class="table">
        <?php for($i = 0; $i < $rows / $cols; $i++) { ?>
            <tr <?php if($usageLimit['type']){ ?>class="<?php echo $usageStyle[$i % 2]; ?>" <?php }?>>
                <?php for($j = 0; $j < $cols && $counter <= $rows; $j++, $counter++) { ?>
                    <td align="center">
                        <a href="#" rel="tip" title="<?php echo $cats[$counter]['description']; ?>">Cat<?php echo $cats[$counter]['name']; ?></a>
                    </td>
                    <td>
                    <div class="input-group">
                        <?php
                        if($ing['byPassIFRA'] == 0 && $limit = searchIFRA($ing['cas'], $ing['name'], null, 'cat'.$cats[$counter]['name'])){
                        ?>
                            <input name="cat<?php echo $cats[$counter]['name']; ?>" type="text" class="form-control" id="cat<?php echo $cats[$counter]['name']; ?>" disabled value="<?php echo number_format((float)$limit['val'], 4); ?>" aria-label="cat<?php echo $cats[$counter]['name']; ?>" aria-describedby="cat-addon">
                        <?php } else { ?>
                            <input name="cat<?php echo $cats[$counter]['name']; ?>" type="text" class="form-control" id="cat<?php echo $cats[$counter]['name']; ?>" value="<?php echo number_format($ing['cat'.$cats[$counter]['name']], 4); ?>" aria-label="cat<?php echo $cats[$counter]['name']; ?>" aria-describedby="cat-addon">
                        <?php } ?>
                       <span class="input-group-text" id="cat-addon">%</span>
                    </div>    
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
</div>
<hr />
<p><input type="submit" name="saveUsage" class="btn btn-primary" id="saveUsage" value="Save"></p>

<script>
var byPassIFRA = <?=$ing['byPassIFRA']?>;
var byPassState = '<?=$byPass?>';
var disLimits = <?=$ing['noUsageLimit']?>;
var ingID = '<?=$ing['id'];?>';

$(document).ready(function() {
	$('[rel=tip]').tooltip({placement: 'auto'});

	function unlimited_usage(status,maxulimit){
		$('#usage_type').prop('disabled', status);
		$("input[id^='cat']").prop('disabled', status).val(maxulimit);
	}
	
	function byPassCheck(s){
		if (s === true){
			$('#noUsageLimit').prop('disabled', false);
			$("input[id^='cat']").prop('disabled', false);
			$('#usage_type_bypass').show();
			$('#class_bypass').hide();
		}else if (s === false){
			$('#noUsageLimit').prop('disabled', true);
			$("input[id^='cat']").prop('disabled', true);
			$('#usage_type_bypass').hide();
			$('#class_bypass').show();
		}
		if (byPassState === 'disabled'){
			$('#noUsageLimit').prop('disabled', false);
			$("input[id^='cat']").prop('disabled', false);
			$('#usage_type_bypass').show();
			$('#class_bypass').hide();
		}
	}
	
	if (byPassIFRA === 1){
		byPassCheck(true);
	}else if (byPassIFRA === 0){
		byPassCheck(false);
	}  
	  
	if ($('#usage_type').val() === "4") {
		$("input[id^='cat']").prop('disabled', true).val('0.0000');
	}
	
	if(disLimits === 1){
		$('#noUsageLimit').prop('checked', true);
		unlimited_usage(true,'100.0000');
	}
	
	$('#byPassIFRA').click(function(){
		if($(this).is(':checked')){
			byPassCheck(true);
		}else{
			byPassCheck(false);
		}
	});
		
	$('#noUsageLimit').click(function(){
		if($(this).is(':checked')){
			unlimited_usage(true,'100.0000');
		}else{
			unlimited_usage(false,'100.0000');
		}
	});
	
	$('#usage_type').click('change', handleUsageTypeChange);

    function handleUsageTypeChange() {		
        if ($('#usage_type').val() === "4") {
   			$("input[id^='cat']").prop('disabled', true).val('0.0000');
        } else {
		    $("input[id^='cat']").prop('disabled', false);
        }
    }
	
	$('#usage_limits').on('click', '[id*=saveUsage]', function () {
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				manage: 'ingredient',
				tab: 'usage_limits',
				ingID: ingID,
				usage_type: $("#usage_type").val(),
				flavor_use: $("#flavor_use").is(':checked'),
				noUsageLimit: $("#noUsageLimit").is(':checked'),
				byPassIFRA: $("#byPassIFRA").is(':checked'),
				isAllergen: $("#isAllergen").is(':checked'),
				<?php foreach ($cats as $cat) {?>
					cat<?php echo $cat['name'];?>: parseFloat($("#cat<?php echo $cat['name'];?>").val()),
				<?php } ?>
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

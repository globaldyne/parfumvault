<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
if(!$_POST['ingID']){
	echo 'Invalid ID';
	return;
}

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/inc/settings.php');

$StandardIFRACategories = mysqli_query($conn, "SELECT name,description,type FROM IFRACategories WHERE type = '1' ORDER BY id ASC");
while($cats_res = mysqli_fetch_array($StandardIFRACategories)){
	$cats[] = $cats_res;
}

$rows = count($cats);
$counter = 0;
$cols = 3;
$usageStyle = array('even_ing','odd_ing');
$defCatClass = $settings['defCatClass'];

$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT id, cas,name,usage_type,noUsageLimit,byPassIFRA,flavor_use,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12 FROM ingredients WHERE id = '".$_POST['ingID']."'"));

$rType = searchIFRA($ing['cas'],$ing['name'],'type', $defCatClass);
$limit = searchIFRA($ing['cas'],$ing['name'],null, 'cat'.$cats[$counter]['name']);
if($reason = searchIFRA($ing['cas'],$ing['name'],null,$defCatClass)){
	$reason = explode(' - ',$reason);
}

if($usageLimit = searchIFRA($ing['cas'],$ing['name'],null,$defCatClass)){ 
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
        <div class="col-sm-2">
            Bypass IFRA 
            <i rel="tip" title="Enable this to bypass IFRA values and set your own. This is not recommended though." class="pv_point_gen fas fa-info-circle"></i>
        </div>
        <div class="col-md-4">
            <input name="byPassIFRA" type="checkbox" <?php echo $byPass ?: 'disabled'; ?> id="byPassIFRA" value="1" <?php if($ing['byPassIFRA'] == '1'){ ?> checked="checked" <?php } ?>/>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-sm-2">
            No usage limit
            <i rel="tip" title="This will set all values to 100% if no IFRA entries found or IFRA lookup is bypassed." class="pv_point_gen fas fa-info-circle"></i>
        </div>
        <div class="col-sm-2">
            <input name="noUsageLimit" type="checkbox" <?php echo $noLimit; ?> id="noUsageLimit" value="1" <?php if($ing['noUsageLimit'] == '1'){ ?> checked="checked" <?php } ?>/>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-sm-2">
            Flavor use
        </div>
        <div class="col-sm-2">
            <input name="flavor_use" type="checkbox" id="flavor_use" value="1" <?php if($ing['flavor_use'] == '1'){ ?> checked="checked" <?php } ?>/>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-sm-2">
            Usage classification
        </div>
        <div class="col-sm-4">
            <div id="class_bypass"><?php echo $rType.' - '.$reason['1']; ?></div>
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
            <tr <?php if($rType){ ?>class="<?php echo $usageStyle[$i % 2]; ?>" <?php }?>>
                <?php for($j = 0; $j < $cols && $counter <= $rows; $j++, $counter++) { ?>
                    <td align="center">
                        <a href="#" rel="tip" title="<?php echo $cats[$counter]['description']; ?>">Cat<?php echo $cats[$counter]['name']; ?> %:</a>
                    </td>
                    <td>
                        <?php
                        if($ing['byPassIFRA'] == 0 && $limit = searchIFRA($ing['cas'], $ing['name'], null, 'cat'.$cats[$counter]['name'])){
                            $limit = explode(' - ', $limit);
                        ?>
                            <input name="cat<?php echo $cats[$counter]['name']; ?>" type="text" class="form-control" id="cat<?php echo $cats[$counter]['name']; ?>" disabled value="<?php echo number_format((float)$limit[0], 4); ?>" />
                        <?php } else { ?>
                            <input name="cat<?php echo $cats[$counter]['name']; ?>" type="text" class="form-control" id="cat<?php echo $cats[$counter]['name']; ?>" value="<?php echo number_format($ing['cat'.$cats[$counter]['name']], 4); ?>" />
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
</div>
<hr />
<p>To set a category to zero, please type <strong>0.0</strong> instead of 0</p>
<hr />
<p><input type="submit" name="save" class="btn btn-primary" id="saveUsage" value="Save" /></p>

<script>
var byPassIFRA = <?=$ing['byPassIFRA']?>;
var byPassState = '<?=$byPass?>';
var disLimits = <?=$ing['noUsageLimit']?>;
var ingID = '<?=$ing['id'];?>';

$(document).ready(function() {
	$('[rel=tip]').tooltip({placement: 'auto'});

	function unlimited_usage(status,maxulimit){
		$('#usage_type').prop('disabled', status);
		<?php foreach ($cats as $cat) {?>
			$('#cat<?php echo $cat['name'];?>').prop('readonly', status).val(maxulimit);
		<?php } ?>
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
	
	if(disLimits === 1){
		$('#noUsageLimit').prop('checked', true);
		unlimited_usage(true,'100');
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
			unlimited_usage(true,'100.00');
		}else{
			unlimited_usage(false,'100.00');
		}
	});
	
	
	$('#usage_limits').on('click', '[id*=saveUsage]', function () {
		$.ajax({ 
			url: 'update_data.php', 
			type: 'POST',
			data: {
				manage: 'ingredient',
				tab: 'usage_limits',
				ingID: ingID,
				usage_type: $("#usage_type").val(),
				flavor_use: $("#flavor_use").is(':checked'),
				noUsageLimit: $("#noUsageLimit").is(':checked'),
				byPassIFRA: $("#byPassIFRA").is(':checked'),
				<?php foreach ($cats as $cat) {?>
					cat<?php echo $cat['name'];?>: $("#cat<?php echo $cat['name'];?>").val(),
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
			}
		});
	});
});

</script>

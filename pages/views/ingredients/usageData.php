<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
if(!$_POST['ingID']){
	echo 'Invalid ID';
	return;
}

require(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
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

$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT id, cas,name,usage_type,noUsageLimit,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12 FROM ingredients WHERE id = '".$_POST['ingID']."'"));


?>

<h3>Usage &amp; Limits</h3>
<hr>
<table width="100%" border="0">
    <tr>
        <td width="15%" height="32">No usage limit:</td>
        <?php if($usageLimit = searchIFRA($ing['cas'],$ing['name'],null,$conn, $defCatClass)){ $chk = 'disabled'; }?>
        <td><input name="noUsageLimit" type="checkbox" <?php echo $chk; ?> id="noUsageLimit" value="1" <?php if($ing['noUsageLimit'] == '1'){; ?> checked="checked"  <?php } ?>/></td>
    </tr>
    <tr>
        <td height="32">Flavor use:</td>
        <td><input name="flavor_use" type="checkbox" id="flavor_use" value="1" <?php if($ing['flavor_use'] == '1'){; ?> checked="checked"  <?php } ?>/></td>
    </tr>
    <tr>
        <td height="32">Usage classification:</td>
        <td><?php if($rType = searchIFRA($ing['cas'],$ing['name'],'type',$conn, $defCatClass)){
            if($reason = searchIFRA($ing['cas'],$ing['name'],null,$conn, $defCatClass)){
                $reason = explode(' - ',$reason);
            }
            echo $rType.' - '.$reason['1'];
        }else{
            ?>
            <select name="usage_type" id="usage_type" class="form-control">
                <option value="1" <?php if($ing['usage_type']=="1") echo 'selected="selected"'; ?> >Recommendation</option>
                <option value="2" <?php if($ing['usage_type']=="2") echo 'selected="selected"'; ?> >Restriction</option>
                <option value="3" <?php if($ing['usage_type']=="3") echo 'selected="selected"'; ?> >Specification</option>
                <option value="4" <?php if($ing['usage_type']=="4") echo 'selected="selected"'; ?> >Prohibition</option>
            </select>
        <?php } ?>
    </td>
</tr>
</table>
<hr />
<table width="100%" border="0">
<?php for($i = 0; $i < $rows/$cols; $i++) { ?>
    <tr <?php if($rType){ ?>class="<?php echo $usageStyle[$i % 2]; ?>" <?php }?>>
        <?php for($j=0; $j < $cols && $counter <= $rows; $j++, $counter++) {?>
            <td align="center"><a href="#" rel="tipsy" title="<?php echo $cats[$counter]['description'];?>">Cat<?php echo $cats[$counter]['name'];?> %:</a></td>
            <td><?php
            if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat'.$cats[$counter]['name'])){
                $limit = explode(' - ',$limit);
                echo $limit['0'];
            }else{
                ?>
                <input name="cat<?php echo $cats[$counter]['name'];?>" type="text" class="form-control" id="cat<?php echo $cats[$counter]['name'];?>" value="<?php echo number_format($ing['cat'.$cats[$counter]['name']],2); ?>" />
            </td>
            <?php 
        } 
    } 
    ?>
</tr>
<?php } ?>
</table>
<hr />
<p> To set a category to zero, please type <strong>0.0</strong> instead of 0</p>
<hr />
<p><input type="submit" name="save" class="btn btn-info" id="saveUsage" value="Save" /></p>
<script>
$(document).ready(function() {
	$('[rel=tipsy]').tooltip({placement: 'auto'});

	function unlimited_usage(status,maxulimit){
		$('#usage_type').prop('disabled', status);
		<?php foreach ($cats as $cat) {?>
			$('#cat<?php echo $cat['name'];?>').prop('readonly', status).val(maxulimit);
		<?php } ?>
	}

	<?php if($ing['noUsageLimit']){ ?>
		$('#noUsageLimit').prop('checked', true);
		unlimited_usage(true,'100');
	<?php } ?>
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
				ingID: '<?=$ing['id'];?>',
				usage_type: $("#usage_type").val(),
				flavor_use: $("#flavor_use").is(':checked'),
				noUsageLimit: $("#noUsageLimit").is(':checked'),
				<?php foreach ($cats as $cat) {?>
					cat<?php echo $cat['name'];?>: $("#cat<?php echo $cat['name'];?>").val(),
				<?php } ?>
			},
			dataType: 'json',
			success: function (data) {
				if(data.success){
					var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				}else{
					var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				}
				$('#ingMsg').html(msg);
			}
		});
	});
});

</script>
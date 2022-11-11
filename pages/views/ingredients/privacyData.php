<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
if(!$_POST['ingID']){
	echo 'Invalid ID';
	return;
}

require(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');


$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isPrivate FROM ingredients WHERE id = '".$_POST['ingID']."'"));

?>
<h3>Privacy</h3>
	<hr>
	<table width="100%" border="0">
		<tr>
			<td width="9%" height="31"><a href="#" rel="tipsy" title="If enabled, ingredient will automatically excluded if you choose to upload your ingredients to PV Online.">Private:</a></td>
			<td width="91%" colspan="5"><input name="isPrivate" type="checkbox" id="isPrivate" value="1" <?php if($ing['isPrivate'] == '1'){; ?> checked="checked"  <?php } ?>/></td>
		</tr>
	</table>
	<hr />
	<p><input type="submit" name="save" class="btn btn-info" id="savePrivacy" value="Save" /></p>
    
<script>
$('[rel=tipsy]').tooltip({placement: 'auto'});

$('#privacy').on('click', '[id*=savePrivacy]', function () {
	$.ajax({ 
		url: 'update_data.php', 
		type: 'POST',
		data: {
			manage: 'ingredient',
			tab: 'privacy',
			ingID: '<?=$ing['id'];?>',
			isPrivate: $("#isPrivate").is(':checked'),
		},
		dataType: 'html',
		success: function (data) {
			$('#ingMsg').html(data);
		}
	});
});
</script>
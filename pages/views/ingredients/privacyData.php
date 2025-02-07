<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
if(!$_GET['ingID']){
	echo 'Invalid ID';
	return;
}

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isPrivate FROM ingredients WHERE id = '".$_GET['ingID']."' AND owner_id = '$userID'"));

?>
<h3>Privacy</h3>
<div id="prvMsg"></div>
<hr />
	<div class="row">
    	<div class="col-xs-4">
      		<a href="#" rel="tipsy" title="If enabled, will be kept private from other users in your local installation if you create any.">Private:</a>
			<input name="isPrivate" type="checkbox" id="isPrivate" value="1" <?php if($ing['isPrivate'] == '1'){; ?> checked="checked"  <?php } ?>/>
		</div>
    </div>
	<hr />
	<input type="submit" name="save" class="btn btn-primary" id="savePrivacy" value="Save" />
    
<script>
	$(document).ready(function() {

$('[rel=tipsy]').tooltip({placement: 'auto'});
	$('#privacy').on('click', '[id*=savePrivacy]', function () {
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				manage: 'ingredient',
				tab: 'privacy',
				ingID: '<?=$ing['id'];?>',
				isPrivate: $("#isPrivate").is(':checked'),
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

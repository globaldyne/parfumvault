<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
$sds_settings = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM sdsSettings WHERE owner_id = '$userID'"));

?>

<h3>SDS Settings</h3>
<hr>
<div class="card-body">
	<div class="form-floating mb-3">
		<textarea class="form-control" name="sds_disc_content" id="sds_disc_content" rows="6" placeholder="PLEASE ADD A PROPER DISCLAIMER MESSAGE"><?=$sds_settings['sds_disclaimer']?></textarea>
		<label for="sds_disc_content">SDS Disclaimer</label>
	</div>
	<input type="submit" name="button" class="btn btn-primary" id="sds_set_update" value="Save">
</div>

<script>
$(document).ready(function() {

	$('#sds_set_update').click(function() {

	$.ajax({ 
		url: '/core/core.php', 
		type: 'POST',
		data: {
			action: 'sdsDisclaimerContent',
			sds_disc_content: $("#sds_disc_content").val(),
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
				$('.toast-header').removeClass().addClass('toast-header alert-success');
			} else if(data.error) {
				$('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
			}
			$('.toast').toast('show');
		},
		error: function (xhr, status, error) {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
			$('.toast').toast('show');
		}
	  });
	});

});

</script>

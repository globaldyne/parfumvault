<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

require_once(__ROOT__.'/inc/settings.php');

?>

<h3>SDS Settings</h3>
<hr>
<div class="card-body">
	<div class="mb-3">
  		<label for="sds_disc_content" class="form-label">SDS Disclaimer</label>
        <textarea class="form-control" name="sds_disc_content" id="sds_disc_content" rows="4"><?=$settings['sds_disclaimer']?></textarea>
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
			settings: 'sds',
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
		}
	  });
	});

});

</script>

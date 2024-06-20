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
            <textarea class="form-control" name="sds_disc_content" id="sds_disc_content" rows="4"><?=$settings['sds_disclaimer']?>
            </textarea>
		</div>
  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="sds_set_update" value="Add">
      </div>
</div>

<script>
$(document).ready(function() {
		

  



$('#sds_set_update').click(function() {

	$.ajax({ 
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			sds: 'settings',
			sds_disc_content: $("#sds_disc_content").val(),
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
			}else{
				var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
			$('#tmpl_inf').html(msg);
		}
	  });
	});

});

</script>
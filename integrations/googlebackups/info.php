<?php 
define('__ROOT__', dirname(dirname(dirname(__FILE__)))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if ($role !== 1){
  echo json_encode(['success' => false, 'error' => 'Not authorised']);
  return;
}
?>
<div class="card">
	<div class="card-header">
		Google Backups Info
	</div>
	<div class="card-body">
		<div class="row" id="srv_info">
			<div class="col-md-4 mb-3">
				<label for="version" class="form-label">Version:</label>
				<div id="version" class="form-text"></div>
			</div>
			<div class="col-md-4 mb-3">
				<label for="backup_folder" class="form-label">Backup Folder:</label>
				<div id="backup_folder" class="form-text"></div>
			</div>
			<div class="col-md-4 mb-3">
				<label for="next_run" class="form-label">Next Run:</label>
				<div id="next_run" class="form-text"></div>
			</div>
		</div>
	</div>
</div>
 

<script>
$(document).ready(function() {
	$.ajax({
		url: "/integrations/googlebackups/manage.php?action=info",
		type: "GET",
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$("#version").text(data.data.version);
				$("#backup_folder").text(data.data.backup_folder);
				$("#next_run").text(data.data.next_run);
			} else {
				$('#srv_info').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Service not available, please make sure the service is installed and running</div>');
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$('#srv_info').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Service not available, please make sure the service is installed and running or publicly available.</div>');
		}
	});
	
});

</script>
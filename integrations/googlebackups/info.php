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
<div class="card-body">
    <div class="row" id="srv_info">
        <div class="col-mb-3">
          <label for="bk-ver" class="form-label mx-2">Version:</label>
          <div id="dataVer" style="display: inline;"></div>
        </div>
        <div class="col-mb-3">
          <label for="bk-build" class="form-label mx-2">Build:</label>
          <div id="dataBuild" style="display: inline;"></div>
        </div>
        <div class="col-mb-3">
          <label for="bk-changelog" class="form-label mx-2">Release notes:</label>
          <div id="dataChangelog"></div>
        </div>
    </div>
</div>
 

<script>
$(document).ready(function() {
	$('#srv_info').html('<div class="spinner-grow mx-2"></div>Please Wait...');
	$.ajax({
		url: "/integrations/googlebackups/manage.php?action=version",
		type: "GET",
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$("#dataVer").text(data.data.version);
				$("#dataBuild").text(data.data.build);
				$("#dataChangelog").text(data.data.changelog);
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
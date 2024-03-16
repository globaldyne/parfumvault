<div class="card-body">
    <div class="row" id="srv_avail">
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

	$.ajax({
		url: "/pages/views/backup_providers/manage.php?action=version",
		type: "GET",
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$("#dataVer").text(data.data.version);
				$("#dataBuild").text(data.data.build);
				$("#dataChangelog").text(data.data.changelog);
			} else {
				$('#srv_avail').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Service not available, please make sure the service is installed and running</div>');
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$('#srv_avail').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Service not available, please make sure the service is installed and running or publicly available.</div>');
		}
	});
});

</script>
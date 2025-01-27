
<script>
$(document).ready(function() {
	var SERV_AVAIL;
	
	$("#edit").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const name = e.relatedTarget.dataset.name;
	
		$.get("/pages/views/backup_providers/edit.php")
			.then(data => {
			$("#editLabel", this).html(name);
			$(".modal-body", this).html(data);
		});
	});

	$("#info").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const name = e.relatedTarget.dataset.name;
	
		$.get("/pages/views/backup_providers/info.php")
			.then(data => {
			$(".modal-body", this).html(data);
		});
	});

	$("#configure").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const name = e.relatedTarget.dataset.name;
	
		$.get("/pages/views/backup_providers/configure.php")
			.then(data => {
			$(".modal-body", this).html(data);
		});
	});

	
	$("#listBackup").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const name = e.relatedTarget.dataset.name;
	
		$.get("/pages/views/backup_providers/listBackups.php")
			.then(data => {
			$(".modal-body", this).html(data);
		});
	});

	$('#runBackup').on('click', '[id*=cBK]', function () {
		$.ajax({
			url: "/pages/views/backup_providers/manage.php?action=version",
			type: "GET",
			dataType: 'json',
			success: function (data) {
				if(data.success){
					SERV_AVAIL = true;
					try_backup();
				} else {
					$('#bk_inf_run').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Service not available, please make sure the service is installed and running</div>');
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('#bk_inf_run').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Service not available, please make sure the service is installed and running or publicly available.</div>');
			}
		});
		
		function try_backup(){
			$("#cBK").prop("disabled", true);
			$("#bk_inf_run").html('<div class="alert alert-info"><div class="spinner-grow mx-2"></div>Please wait, this may take a while depending the size of your database and your internet connection.</div>');
			$.ajax({
				url: "/pages/views/backup_providers/manage.php?action=createBackup",
				type: "GET",
				dataType: 'json',
				//timeout: 10000,
				success: function (data) {
					if(data.success){
						
						$("#bk_inf_run").html('<div class="alert alert-success">' + data.message + '</div>');
						$("#cBK").prop("disabled", false);
		
					} else {
						var errorMsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>Error: ' + response.message + '</div>';
						$('#bk_inf_run').html(errorMsg);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					if (textStatus === 'timeout') {
						var errorMsg = '<div class="alert alert-warning">The request timed out. Please check your internet connection.</div>';
						$('#bk_inf_run').html(errorMsg);
					} else {
						var errorMsg = '<div class="alert alert-danger">Error getting data, please make sure the backup service is installed and running. Error: ' + textStatus + ' - ' + errorThrown + '</div>';
						$('#bk_inf_run').html(errorMsg);
					}
				}
			
			});
		};
	});


	$('#restart').on('click', '[id*=cRS]', function () {
		$("#cRS").prop("disabled", true);
		$("#bk_inf_rs").html('<div class="alert alert-info"><div class="spinner-grow mx-2"></div>Please wait, this may take a while.</div>');
	
	function fetchVersionAfterRestart() {
		setTimeout(function() {
			$.ajax({
				url: "/pages/views/backup_providers/manage.php?action=version",
				type: "GET",
				dataType: 'json',
				timeout: 1000,
				success: function (data) {
					if(data){
						$("#bk_inf_rs").html('<div class="alert alert-success">Service is now available</div>');
						$("#cRS").prop("disabled", false);
					} else {
						var errorMsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>Error getting data (001)</div>';
						$('#bk_inf_rs').html(errorMsg);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					if (textStatus === 'timeout') {
						var errorMsg = '<div class="alert alert-warning">The request timed out. Please check your internet connection.</div>';
						$('#bk_inf_rs').html(errorMsg);
					} else {
						var errorMsg = '<div class="alert alert-danger">Error getting data, please make sure the backup service is installed and running. Error: ' + textStatus + ' - ' + errorThrown + '</div>';
						$('#bk_inf_rs').html(errorMsg);
					}
				}
			});
		}, 10000); // 10 seconds delay
	}

	$.ajax({
		url: "/pages/views/backup_providers/manage.php?action=version",
		type: "GET",
		dataType: 'json',
		success: function (data) {
			if(data.success){
				SERV_AVAIL = true;
				try_restart();
			} else {
				$('#bk_inf_rs').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Service not available, please make sure the service is installed and running</div>');
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$('#bk_inf_rs').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Service not available, please make sure the service is installed and running or publicly available.</div>');
		}
	});
	
	function try_restart(){
		$.ajax({
			url: "/pages/views/backup_providers/manage.php?action=restart",
			type: "GET",
			dataType: 'json',
			success: function (data) {
				fetchVersionAfterRestart();
			}
		});
	};
	
	});
});

</script>


<!--LIST BK MODAL-->            
<div class="modal fade" id="listBackup" data-bs-backdrop="static" tabindex="-1" aria-labelledby="listBackupLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="listBackupLabel">Available backups</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!--CONFIGURE MODAL-->            
<div class="modal fade" id="configure" data-bs-backdrop="static" tabindex="-1" aria-labelledby="configureLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="configureLabel">Configure</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
      </div>
    </div>
  </div>
</div>


<!--INFO MODAL-->            
<div class="modal fade" id="info" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="infoLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="infoLabel">Info</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!--EDIT MODAL-->            
<div class="modal fade" id="edit" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="editLabel">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL RESTART -->
<div class="modal fade" id="restart" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="restartLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="runBackupLabel">Restart service</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div id="bk_inf_rs"></div>
           <div class="alert alert-info">This will restart the backup service, please note any backups already in progress will fail.</div>
          <div class="dropdown-divider"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="cRS">Restart</button>
          </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL FOR RUN A BACKUP -->
<div class="modal fade" id="runBackup" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="runBackupLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="runBackupLabel">Take a backup</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div id="bk_inf_run"></div>
           <div class="alert alert-info">Manually take a backup. This will not affect your current schedule.</div>
          <div class="dropdown-divider"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="cBK">Backup</button>
          </div>
      </div>
    </div>
  </div>
</div>
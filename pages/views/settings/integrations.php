<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$bkData = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM backup_provider WHERE id = '1'"));

if($bkData['enabled']){
	$state = '<span class="card-subtitle badge badge-success ml-2">Enabled</span>';
}else{
	$state = '<span class="card-subtitle badge badge-danger ml-2">Disabled</span>';
}

if($settings['pv_scale_enabled']){
	$scaleState = '<span class="card-subtitle badge badge-success ml-2">Enabled</span>';
}else{
	$scaleState = '<span class="card-subtitle badge badge-danger ml-2">Disabled</span>';
}
?>

<h3>Integrations</h3>
<hr>
<div class="card-body" id="main_area">
    <div class="row">
        <div class="col-md-2">
            <div id="backups">
                <div class="card" style="width: 18rem;">
                   <div class="mx-4">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path fill="#74C0FC"
                                d="M339 314.9L175.4 32h161.2l163.6 282.9H339zm-137.5 23.6L120.9 480h310.5L512 338.5H201.5zM154.1 67.4L0 338.5 80.6 480 237 208.8 154.1 67.4z" />
                        </svg>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Backups<?php echo $state; ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">Google Drive Backups</h6>
                        <p class="card-text">Backup PV database automatically in Google Drive.</p>
                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#configure"><i
                                class="fas fa-gears mx-2"></i>Configure</a>
                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#listBackup"><i
                                class="fas fa-list-check mx-2"></i>List backups</a>
                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#runBackup"><i
                                class="fas fa-person-running mx-2"></i>Take a backup</a>
                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#info"><i
                                class="fas fa-circle-info mx-2"></i>Info</a>
                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#restart"><i
                                class="fas fa-arrows-rotate mx-2"></i>Restart</a>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="col-md-2">
            <div id="pvscale">
                <div class="card" style="width: 18rem;">
                    <div class="mx-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#63E6BE" d="M128 176a128 128 0 1 1 256 0 128 128 0 1 1 -256 0zM391.8 64C359.5 24.9 310.7 0 256 0S152.5 24.9 120.2 64H64C28.7 64 0 92.7 0 128V448c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H391.8zM296 224c0-10.6-4.1-20.2-10.9-27.4l33.6-78.3c3.5-8.1-.3-17.5-8.4-21s-17.5 .3-21 8.4L255.7 184c-22 .1-39.7 18-39.7 40c0 22.1 17.9 40 40 40s40-17.9 40-40z"/></svg>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">PV Scale<?php echo $scaleState; ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage your PV Scale</h6>
                        <p class="card-text">Connect your PV Scale to update the formula in the Making section and inventory in real time while you making the formula</p>
                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#configureScale"><i
                                class="fas fa-gears mx-2"></i>Configure</a>
                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#buyScale"><i
                                class="fas fa-cart-shopping mx-2"></i>Buy a PV Scale</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
 	
</div>


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
	
	$("#configureScale").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const name = e.relatedTarget.dataset.name;
	
		$.get("/pages/views/pvscale/configure.php")
			.then(data => {
			$(".modal-body", this).html(data);
		});
	});

	$("#buyScale").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const name = e.relatedTarget.dataset.name;
	
		$.get("/pages/views/pvscale/buy.php")
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
<div class="modal fade" id="listBackup" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="listBackupLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="listBackupLabel">Available backups</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
      </div>
    </div>
  </div>
</div>

<!--CONFIGURE MODAL-->            
<div class="modal fade" id="configure" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="configureLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="configureLabel">Configure</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
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
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
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
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
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
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
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
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
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

<!-- SCALE BUY MODAL -->            
<div class="modal fade" id="buyScale" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="buyScale" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="buyScaleLabel">Buy PV Scale</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
      </div>
    </div>
  </div>
</div>

<!-- SCALE CONFIGURE MODAL -->            
<div class="modal fade" id="configureScale" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="configureScaleLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="configureScaleLabel">Configure Scale</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
      </div>
    </div>
  </div>
</div>
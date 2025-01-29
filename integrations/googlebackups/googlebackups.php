<?php
 
define('__ROOT__', dirname(dirname(dirname(__FILE__)))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if ($role !== 1){
  echo json_encode(['success' => false, 'error' => 'Not authorised']);
  return;
}

if($integrations_settings['googlebackups_enabled']){
	$googlebackups_state = '<span class="card-subtitle badge badge-success ml-2">Enabled</span>';
}else{
	$googlebackups_state = '<span class="card-subtitle badge badge-danger ml-2">Disabled</span>';
}

?>

<script>
$(document).ready(function() {
	var SERV_AVAIL;

	$("#info").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const name = e.relatedTarget.dataset.name;
		
		$.get("/integrations/googlebackups/info.php", { id: id, name: name })
			.done(data => {
				$(".modal-body", this).html(data);
			})
			.fail(() => {
				$(".modal-body", this).html('<div class="alert alert-danger">Unable to get data</div>');
			});
	});

	$("#configure").on("show.bs.modal", function(e) {
		$.get("/integrations/googlebackups/configure.php")
			.done(data => {
				$(".modal-body", this).html(data);
			})
			.fail(() => {
				$(".modal-body", this).html('<div class="alert alert-danger">Unable to get data</div>');
			});
	});

	$("#listBackup").on("show.bs.modal", function(e) {
		$.get("/integrations/googlebackups/listBackups.php")
			.done(data => {
				$(".modal-body", this).html(data);
			})
			.fail(() => {
				$(".modal-body", this).html('<div class="alert alert-danger">Unable to get data</div>');
			});
	});

	$('#runBackup').on('click', '[id*=cBK]', function () {
		$.ajax({
			url: "/integrations/googlebackups/manage.php?action=info",
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
			error: function() {
				$('#bk_inf_run').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Service not available, please make sure the service is installed and running or publicly available.</div>');
			}
		});
		
		function try_backup(){
			$("#cBK").prop("disabled", true);
			$("#bk_inf_run").html('<div class="alert alert-info"><div class="spinner-grow mx-2"></div>Please wait, this may take a while depending on the size of your database and your internet connection.</div>');
			$.ajax({
				url: "/integrations/googlebackups/manage.php?action=createBackup",
				type: "GET",
				dataType: 'json',
				success: function (data) {
					if(data.success){
						$("#bk_inf_run").html('');
						$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.message);
						$('.toast-header').removeClass().addClass('toast-header alert-success');
						$('.toast').toast('show');
						$('.modal').modal('hide');
					} else {
						$('#bk_inf_run').html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>Error: ' + data.message + '</div>');
					}
					$("#cBK").prop("disabled", false);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					let errorMsg;
					if (textStatus === 'timeout') {
						errorMsg = '<div class="alert alert-warning">The request timed out. Please check your internet connection.</div>';
					} else {
						errorMsg = '<div class="alert alert-danger">Error getting data, please make sure the backup service is installed and running. Error: ' + textStatus + ' - ' + errorThrown + '</div>';
					}
					$('#bk_inf_run').html(errorMsg);
					$("#cBK").prop("disabled", false);
				}
			});
		}
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
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>


<!--INFO MODAL-->            
<div class="modal fade" id="info" data-bs-backdrop="static" tabindex="-1" aria-labelledby="infoLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
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

<!-- MODAL FOR RUN A BACKUP -->
<div class="modal fade" id="runBackup" data-bs-backdrop="static" tabindex="-1" aria-labelledby="runBackupLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="runBackupLabel">Initiate Backup</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
					<div id="bk_inf_run"></div>
					<div class="alert alert-info"><i class="fa-solid fa-info-circle mx-2"></i>Manually initiate a backup. This action will not interfere with your current schedule.</div>
					<div class="dropdown-divider"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="cBK">Backup</button>
			</div>
		</div>
	</div>
</div>
<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');

?>

<h3>Backup providers</h3>
<hr>
<div id="srv_avail"></div>
<div class="card-body" id="main_area">
  <div class="text-right">
    <div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mr2"></i>Actions</button>
        <div class="dropdown-menu dropdown-menu-right">
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addBKProvider"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
            <li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#info"><i class="fas fa-circle-info mx-2"></i>Info</a></li>
		<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#runBackup"><i class="fas fa-person-running mx-2"></i>Run a backup</a></li>
		<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#listBackup"><i class="fas fa-list-check mx-2"></i>List backups</a></li>
		<div class="dropdown-divider"></div>
		<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#restart"><i class="fas fa-arrows-rotate mx-2"></i>Restart</a></li>
        </div>
    </div>
  </div>
</div>
<table id="tdProv" class="table table-striped table-bordered" style="width:100%">
  <thead>
      <tr>
          <th>Provider</th>
          <th>Schedule</th>
          <th>State</th>
          <th>Description</th>
          <th></th>
      </tr>
   </thead>
</table>
<script>
$(document).ready(function() {
	var tdProv = $('#tdProv').DataTable({
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [4] }
		],
		dom: 'lfrtip',
		processing: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: 'No providers found.',
			search: 'Search:'
			},
		ajax: {	url: '/core/list_backup_providers_data.php' },
		columns: [
				  { data : 'provider', title: 'Provider', render: provider },
				  { data : 'schedule', title: 'Schedule', render: schedule},
				  { data : 'state', title: 'State', render: state},
				  { data : 'description', title: 'Description', render: description},
				  { data : null, title: '', render: actions},		   
				 ],
		order: [[ 1, 'asc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
		pageLength: 20,
		displayLength: 20,
		drawCallback: function( settings ) {
				extrasShow();
			},
		stateSave: true,
		stateDuration: -1,
		stateLoadCallback: function (settings, callback) {
			$.ajax( {
				url: '/core/update_user_settings.php?set=listBKProviders&action=load',
				dataType: 'json',
				success: function (json) {
					callback( json );
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			 url: "/core/update_user_settings.php?set=listBKProviders&action=save",
			 data: data,
			 dataType: "json",
			 type: "POST"
		  });
		},	
	});
	
	$.ajax({
		url: "/pages/views/backup_providers/manage.php?action=version",
		type: "GET",
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$("#srv_avail").hide();
				$("#srv_avail").html('');
				$("#updateBtn").hide();
				$("#dataVer").text(data.data.version);
				$("#dataBuild").text(data.data.build);
				$("#dataChangelog").text(data.data.changelog);
			} else {
				$("#main_area, #tdProv").hide();
				$("#main_area, #tdProv").html('');
				$('#srv_avail').html('<div class="alert alert-danger">Service not available, please make sure the service is installed and running</div>');
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$("#main_area, #tdProv").hide();
			$("#main_area, #tdProv").html('');
			$('#srv_avail').html('<div class="alert alert-danger">Service not available, please make sure the service is installed and running or publicly available.</div>');
		}
	});
 
	
	$('#listBackup').on('show.bs.modal', function (e) {
	
			var tdlistBackup = $('#backupTable').DataTable({
			columnDefs: [
				{ className: 'text-center', targets: '_all' },
				{ orderable: false, targets: [3, 4] }
			],
			dom: 'lfrtip',
			processing: true,
			language: {
				loadingRecords: '&nbsp;',
				processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
				emptyTable: 'No backups found.',
				search: 'Search:'
				},
			ajax: {	url: '/pages/views/backup_providers/manage.php?action=getRemoteBackups' },
			columns: [
					  { data : 'file_name', title: 'File name' },
					  { data : 'file_id', title: 'File ID'},
					  { data : 'file_size', title: 'Size'},
					  { data : null, title: '', render: action_download},
					  { data : null, title: '', render: action_delete},		   
					 ],
			order: [[ 1, 'asc' ]],
			lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
			pageLength: 20,
			displayLength: 20,
		});
		
	});
	
	$('#backupTable tbody').on('click', '.delete-btn', function() {
		var fileId = $(this).data('file-id');
		// Perform deletion operation using fileId
		$.ajax({
			url: "/pages/views/backup_providers/manage.php?action=deleteRemoteBackup&id=" + fileId,
			type: "GET",
			dataType: 'json',
			success: function (data) {
				if(data.success){
					var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.message + '</div>';
				}else{
					var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.message + '</div>';
				}
				$('#resBK_data').html(msg);
				reload_bk_data();
			}
		});
		
	});
	
});

function action_download(data, type, row){
	return '<a href="' + row.download_link + '" target="_blank">Download</a>';
}

function action_delete(data, type, row){
	return '<a href="#" class="delete-btn" data-file-id="' + row.file_id + '">Delete</a>';
}

function provider(data, type, row){
	return row.provider;    
}

function description(data, type, row){
	return '<a href="#" class="description pv_point_gen" data-name="description" data-type="textarea" data-pk="'+row.id+'">'+row.description+'</a>';    
}

function state(data, type, row){
	if(row.enabled == 0){
		var data = '<span class="pv-label badge badge-danger">Disabled</span>';
	}
	if(row.enabled == 1){
		var data = '<span class="pv-label badge badge-success">Enabled</span>';
	}

	return data;   
}

function schedule(data, type, row){
	return row.schedule;    
}

function actions(data, type, row){	
		data = '<div class="dropdown">' +
        '<button type="button" class="btn btn-primary btn-floating dropdown-toggle hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
            '<ul class="dropdown-menu dropdown-menu-right">';
		data += '<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#edit" rel="tip" title="Edit '+ row.provider +'" data-id='+ row.id +' data-name="'+ row.provider +'"><i class="fas fa-edit mx-2"></i>Edit</a></li>';
		

		data += '<div class="dropdown-divider"></div>';
		data += '<li><a class="dropdown-item text-danger" href="#" id="sDel" rel="tip" title="Delete '+ row.provider +'" data-id='+ row.id +' data-name="'+ row.provider +'"><i class="fas fa-trash mx-2"></i>Delete</a></li>';
		data += '</ul></div>';
	return data;
}



$('#tdProv').on('click', '[id*=sDel]', function () {
	var bk = {};
	bk.ID = $(this).attr('data-id');
	bk.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm provider removal",
       message : 'Delete <strong>'+ bk.Name +'</strong>?',
       buttons :{
           main: {
               label : "Remove",
               className : "btn-danger",
               callback: function (){
	    			
				$.ajax({ 
					url: '/pages/update_data.php', 
					type: 'POST',
					data: {
						bkProv: 'delete',
						id: bk.ID,
						Name: bk.Name
						},
					dataType: 'json',
					success: function (data) {
						reload_data();
					}
				  });
				
                 return true;
               }
           },
           cancel: {
               label : "Cancel",
               className : "btn-secondary",
               callback : function() {
                   return true;
               }
           }   
       },onEscape: function () {return true;}
   });
});

$('#addBKProvider').on('click', '[id*=sAdd]', function () {
	$.ajax({ 
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			bkProv: 'add',
			bk_name: $("#providerSelect").val(),
			bk_creds: $("#bk_creds").val(),
			bk_desc: $("#bk_desc").val(),
			bk_schedule: $("#schedule").val()
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
			}else{
				var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
			$('#bk_inf').html(msg);
			reload_data();
		}
	  });
});

function reload_data() {
    $('#tdProv').DataTable().ajax.reload(null, true);
};

function reload_bk_data() {
    $('#backupTable').DataTable().ajax.reload(null, true);
};

function extrasShow() {
	$('[rel=tip]').tooltip({
         html: true,
		 boundary: "window",
		 overflow: "auto",
		 container: "body",
         delay: {"show": 100, "hide": 0},
     });
};

$("#edit").on("show.bs.modal", function(e) {
	const id = e.relatedTarget.dataset.id;
	const name = e.relatedTarget.dataset.name;

	$.get("/pages/views/backup_providers/edit.php?id=" + id)
		.then(data => {
		$("#editLabel", this).html(name);
		$(".modal-body", this).html(data);
	});
});

$('#runBackup').on('click', '[id*=cBK]', function () {
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
	url: "/pages/views/backup_providers/manage.php?action=restart",
	type: "GET",
	dataType: 'json',
	success: function (data) {
		fetchVersionAfterRestart();
	}
});
	

	
});

</script>
<!-- Modal -->
<div class="modal fade" id="listBackup" tabindex="-1" aria-labelledby="listBackupLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="backupModalLabel">Available Backup Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <div id="resBK_data"></div>
        <table id="backupTable" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>File ID</th>
                    <th>Size</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- MODAL FOR ADDING A BACKUP PROVIDER -->
<div class="modal fade" id="addBKProvider" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addBKProviderLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addBKProviderLabel">Add Backup Provider</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<div class="alert alert-info"><i class="fa-solid fa-circle-question mx-2"></i>Please refer to the<a href="https://developers.google.com/identity/protocols/oauth2/service-account#creatinganaccount" target="_blank"> oficial Google</a> docs how to create valid credentials for the service. <br />
</div>
        <div id="bk_inf"></div>
          <div class="mb-3">
            <label for="providerSelect" class="form-label">Select Provider</label>
            <select class="form-select" id="providerSelect" aria-label="Select provider">
              <option disabled selected>Select provider</option>
              <option value="Google">Google Drive</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="bk_content" class="form-label">Credentials</label>
            <textarea class="form-control" name="bk_creds" id="bk_creds" rows="4"></textarea>
          </div>
          <div class="mb-3">
            <label for="schedule" class="form-label">Scheduled Time</label>
            <input type="time" class="form-control" id="schedule" name="schedule">
          </div>
          <div class="mb-3">
            <label for="bk_desc" class="form-label">Short Description</label>
            <input type="text" class="form-control" id="bk_desc" name="bk_desc">
          </div>
        <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="sAdd">Add</button>
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
        <h5 class="modal-title" id="runBackupLabel">Create a backup</h5>
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

<!-- MODAL FOR INFO -->
<div class="modal fade" id="info" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="infoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="infoLabel">Info</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="res">
            <div class="row">
                <div class="col-mb-3">
                  <label for="bk-ver" class="form-label mx-2">Version:</label>
                  <div id="dataVer" style="display: inline;">
                  </div>
                </div>
                <div class="col-mb-3">
                  <label for="bk-build" class="form-label mx-2">Build:</label>
                  <div id="dataBuild" style="display: inline;">
                  </div>
                </div>
                <div class="col-mb-3">
                  <label for="bk-changelog" class="form-label mx-2">Release notes:</label>
                  <div id="dataChangelog">
                  </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>







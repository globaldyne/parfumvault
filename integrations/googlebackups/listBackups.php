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
    <div class="row" id="srv_avail">
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
 

<script>
$(document).ready(function() {
	var SERV_AVAIL;
	//$('#srv_avail').html('<div class="spinner-grow mx-2"></div>Please Wait...');
	$.fn.dataTable.ext.errMode = 'none';

	$.ajax({
		url: "/integrations/googlebackups/manage.php?action=info",
		type: "GET",
		dataType: 'json',
		success: function (data) {
			if(data.success){
				SERV_AVAIL = true;
				initBackupTable();
			} else {
				$('#srv_avail').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Service not available, please make sure the service is installed and running</div>');
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$('#srv_avail').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Service not available, please make sure the service is installed and running or publicly available.</div>');
		}
	});

	function initBackupTable() {
		var tdlistBackup = $('#backupTable').DataTable({
			columnDefs: [
				{ className: 'text-center', targets: '_all' },
				{ orderable: false, targets: [4] }
			],
			dom: 'lfrti',
			processing: true,
			serverSide: false,
			language: {
				loadingRecords: '&nbsp;',
				processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
				emtyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
				zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No backups found</strong></div></div>',
				search: '',
				placeholder: 'Search...',
			},
			ajax: {	
				url: '/integrations/googlebackups/manage.php?action=getRemoteBackups' 
			},
			columns: [
				{ data : 'name', title: 'File name' },
				{ data : 'id', title: 'File ID'},
				{ data : 'size', title: 'Size', render: bkSize},
				{ data : 'createdTime', title: 'Created Time'},
				{ data : null, title: '', render: actions}
			],
			order: [[ 1, 'asc' ]],
			lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
			pageLength: 20,
			displayLength: 20,
		}).on('error.dt', function(e, settings, techNote, message) {
		var m = message.split(' - ');
			$('#backupTable').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
		});
	}

	function bkSize(data, type, row){
		if (data >= 1073741824) {
			return (data / 1073741824).toFixed(2) + ' GB';
		} else if (data >= 1048576) {
			return (data / 1048576).toFixed(2) + ' MB';
		} else {
			return (data / 1024).toFixed(2) + ' KB';
		}
	}


	function actions(data, type, row) {
		data = '<div class="dropdown">' +
		'<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button>' +
			'<ul class="dropdown-menu">';	
		data += '<li><a class="dropdown-item" href="' + row.DownloadLink + '" target="_blank"><i class="bi bi-download mx-2"></i>Download</a></li>';
		data += '<li><a class="dropdown-item text-danger" href="#" id="deleteBackup" data-id=' + row.id + ' data-name="' + row.name + '"><i class="bi bi-trash mx-2"></i>Delete</a></li>';
		data += '</ul></div>';
		return data;
	};

	$('#backupTable').on('click', '[id*=deleteBackup]', function () {
		var bk = {};
		bk.ID = $(this).attr('data-id');
		bk.Name = $(this).attr('data-name');
		
		bootbox.dialog({
			title: "Confirm provider removal",
			message : 'Delete <strong>'+ bk.Name +'</strong>?',
			buttons : {
				main: {
					label : "Delete",
					className : "btn-danger",
					callback: function (){
						$.ajax({ 
							url: "/integrations/googlebackups/manage.php?action=deleteRemoteBackup&id=" + bk.ID, 
							type: 'GET',
							dataType: 'json',
							success: function (data) {
								reload_bk_data();
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
			},
			onEscape: function () { return true; }
		});
	});

	function reload_bk_data() {
		$('#backupTable').DataTable().ajax.reload(null, true);
	}
});


</script>
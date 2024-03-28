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

	$.ajax({
		url: "/pages/views/backup_providers/manage.php?action=version",
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
	}

	function action_download(data, type, row){
		return '<a href="' + row.download_link + '" target="_blank">Download</a>';
	}
	
	function action_delete(data, type, row){
		return '<a class="dropdown-item text-danger" href="#" id="sDel" rel="tip" title="Delete '+ row.file_name +'" data-id='+ row.file_id +' data-name="'+ row.file_name +'"><i class="fas fa-trash mx-2"></i>Delete</a>';
	}
	
	$('#backupTable').on('click', '[id*=sDel]', function () {
		var bk = {};
		bk.ID = $(this).attr('data-id');
		bk.Name = $(this).attr('data-name');
		
		bootbox.dialog({
			title: "Confirm provider removal",
			message : 'Delete <strong>'+ bk.Name +'</strong>?',
			buttons : {
				main: {
					label : "Remove",
					className : "btn-danger",
					callback: function (){
						$.ajax({ 
							url: "/pages/views/backup_providers/manage.php?action=deleteRemoteBackup&id=" + bk.ID, 
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
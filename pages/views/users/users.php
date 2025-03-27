<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


if($role !== 1){
    echo 'Unauthorised';
    return;
}

?>
<h3>Users</h3>
<hr>
<div class="card-body">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
            <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" id="addUser" data-bs-toggle="modal" data-bs-target="#addUserModal" aria-controls="addUser"><i class="bi bi-plus mx-2"></i>Add new user</a></li>
                    <li><a class="dropdown-item" href="/pages/export.php?format=json&kind=users"><i class="bi bi-download mx-2"></i>Export to JSON</a></li>
                    <li><a class="dropdown-item" href="#" id="importUser" data-bs-toggle="modal" data-bs-target="#importUserModal" aria-controls="importUser"><i class="bi bi-upload mx-2"></i>Import from JSON</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<table id="tdUsers" class="table table-striped" style="width:100%">
  <thead>
      <tr>
        <th>Online</th>
        <th>UUID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Status</th>
        <th>Role</th>
        <th>Verified</th>
        <th>Auth method</th>
        <th>Created</th>
        <th>Last login</th>
        <th></th>
      </tr>
   </thead>
</table>

<div class="modal fade" id="addUserModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add new user</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="addUserModalMsg"></div>
            <div class="modal-body" id="addUserModalBody">
                <!-- Content will be loaded here from the AJAX call -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveNewUser">Save User</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <input type="hidden" name="id" id="id" />
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="editUserModalMsg"></div>
            <div class="modal-body" id="editUserModalBody">
                <!-- Content will be loaded here from the AJAX call -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveUserChanges">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importUserModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="importUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importUserModalLabel">Import Users from JSON</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="importUserModalMsg"></div>
            <div class="modal-body" id="importUserModalBody">
                <form id="importUserForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="jsonFile" class="form-label">Select JSON file</label>
                        <input class="form-control" type="file" id="jsonFile" name="jsonFile" accept=".json">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="importUsers">Import Users</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
	$.fn.dataTable.ext.errMode = 'none';

	var tdUsers = $('#tdUsers').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
		],
		dom: 'lfrtip',
		processing: true,
        //serverSide: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No users added yet</strong></div></div>',
			zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
			search: '',
			searchPlaceholder: 'Search by name...',
		},
		ajax: {	
            url: '/core/users_data.php',
            type: 'POST',
			dataType: 'json',
        },
		columns: [
            { data : 'is_logged_in', title: 'Online', render: is_logged_in},
            { data : 'id', title: 'UUID', render: UUID},
		    { data : 'fullName', title: 'Full name', render: name},
		    { data : 'email', title: 'Username'},
            { data : 'status', title: 'Status', render: status},
            { data : 'role', title: 'Role', render: role},
            { data : 'isVerified', title: 'Verified', render: isVerified},
            { data : 'provider', title: 'Auth method', render: provider},
            { data : 'created_at', title: 'Created', render: created_at},
			{ data : 'last_login', title: 'Last login', render: last_login},
			{ data : null, title: '', render: actions, orderable: false},		   
		],
		order: [[ 2, 'asc' ]],
		lengthMenu: [[20, 50, 100, 200], [20, 50, 100, 200]],
		pageLength: 20,
		displayLength: 20,
		drawCallback: function( settings ) {
			extrasShow();
		},
		stateSave: false,
		stateDuration: -1,
		stateLoadCallback: function (settings, callback) {
			$.ajax( {
				url: '/core/update_user_settings.php?set=listUsersl&action=load',
				dataType: 'json',
				success: function (json) {
					callback( json );
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			 url: "/core/update_user_settings.php?set=listUsers&action=save",
			 data: data,
			 dataType: "json",
			 type: "POST"
		  });
		},	
	}).on('error.dt', function(e, settings, techNote, message) {
		var m = message.split(' - ');
		$('#tdUsers').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
    });


    function is_logged_in (data, type, row) {
        if (row.is_logged_in == 1) {
            data = '<span class="text-success" rel="tip" title="Online"><i class="fa fa-circle mx-2"></i></span>';
            if (row.id !== "<?php echo $userID; ?>") {
                data += '<br><a href="#" rel="tip" title="Session validity">' + row.session_valid_until + '</a>';
            }
            return data;
        } else if (row.is_logged_in == 0) {
            return '<span class="text-danger" rel="tip" title="Offline"><i class="fa fa-circle mx-2"></i></span>';
        }
    };

    function UUID(data, type, row) {
        return '<span class="text-decoration-underline" id="UUID">' + row.id + '</span>';
    };

    function name(data, type, row) {
        var name = row.fullName;
        return name;
    };


    function provider(data, type, row){
        if(row.provider == '1'){
            var data = '<span class="badge rounded-pill d-block p-2 text-bg-primary">Local DB</span>';
        }
        if(row.provider == '2'){
            var data = '<span class="badge rounded-pill d-block p-2 text-bg-warning">SSO</span>';
        }
        return data;
    };

    function status(data, type, row){
        if(row.status == 1){
            var data = '<span class="badge rounded-pill d-block p-2 text-bg-success">Active</span>';
        }
        if(row.status == 0){
            var data = '<span class="badge rounded-pill d-block p-2 text-bg-danger">Inactive</span>';
        }
        return data;
    };
	
	function role(data, type, row){
		if(row.role == 2){
            var data = '<span class="badge rounded-pill d-block p-2 text-bg-secondary">Standard user</span>';
        }
        if(row.role == 1){
            var data = '<span class="badge rounded-pill d-block p-2 badge-shared">System admin</span>';
        }
        return data;
	};

	function isVerified(data, type, row){
        if(row.is_verified == 0){
            var data = '<span class="badge rounded-pill d-block p-2 text-bg-danger">No</span>';
        }
        if(row.is_verified == 1){
            var data = '<span class="badge rounded-pill d-block p-2 text-bg-success">Yes</span>';
        }
        return data;

    };

    function last_login(data, type, row){
        const date = new Date(data);
        if (isNaN(date.getTime())) {
            return '-';
        }
        return date.toLocaleDateString(navigator.language || 'en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        }) + ' ' + date.toLocaleTimeString(navigator.language || 'en-GB', {
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    function updated_at(data, type, row){
        const date = new Date(data);
        if (isNaN(date.getTime())) {
            return '-';
        }
        return date.toLocaleDateString(navigator.language || 'en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    };

    function created_at(data, type, row){
		const date = new Date(data);
		return date.toLocaleDateString(navigator.language || 'en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
	};

	
    function actions(data, type, row) {
        data = '<div class="dropdown">' +
            '<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button>' +
            '<ul class="dropdown-menu">';
        data += '<li><a class="dropdown-item" href="/pages/export.php?id=' + row.id + '&kind=user-data" id="exportUserData"><i class="bi bi-file-earmark-arrow-down mx-2"></i>Export user data</a></li>';
        data += '<li><a class="dropdown-item" href="#" data-bs-target="#editUser" data-bs-toggle="editUser" id="editUser" rel="tip" title="Edit ' + row.fullName + '" data-id=' + row.id + ' data-name="' + row.fullName + '"><i class="bi bi-pencil-square mx-2"></i>Edit</a></li>';
        if (row.id !== "<?php echo $userID; ?>") {
            data += '<li><a class="dropdown-item" href="#" id="impersonateUser" rel="tip" title="Impersonate ' + row.fullName + '" data-id=' + row.id + ' data-name="' + row.fullName + '"><i class="bi bi-person-bounding-box mx-2"></i>Impersonate</a></li>';
        }
        data += '<li><a class="dropdown-item text-danger" href="#" id="deleteUser" rel="tip" title="Delete ' + row.fullName + '" data-id=' + row.id + ' data-name="' + row.fullName + '"><i class="bi bi-trash mx-2"></i>Delete</a></li>';
        data += '</ul></div>';
        return data;
    };
	
    tdUsers.on('click', '#UUID', function (e) {
		let tr = e.target.closest('tr');
		let row = tdUsers.row(tr); 
		if (row.child.isShown()) {
			row.child.hide();
		} else {
			row.child(format(row.data())).show();
		}
	});

    function format(d) {
        var details = '<strong>' + d.email + '</strong><br><hr/>';
        $.each(d.stats, function(i, stats) {
            if (i.includes('_')) {
                i = i.replace(/.*?_/, '');
            }
            details += '<span class="details"><strong>' + stats + ' ' + i + '</span><br>';
        });
        return details;
    };


    $('#tdUsers').on('click', '#impersonateUser', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        bootbox.confirm({
            title: 'Impersonate User',
            message: 'Are you sure you want to impersonate <strong>' + name + '</strong>?',
            buttons: {
                confirm: {
                    label: 'Yes, Impersonate',
                    className: 'btn-primary'
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-secondary'
                }
            },
            callback: function(result) {
                if (result) {
                    $.ajax({
                        url: '/core/core.php',
                        type: 'POST',
                        data: {
                            request: 'impersonateuser',
                            impersonate_user_id: id
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.success) {
                                window.location.href = data.redirect_url;
                            } else {
                                bootbox.alert('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>' + data.error + '</div>');
                            }
                        },
                        error: function(xhr, status, error) {
                            bootbox.alert('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>An error occurred, check server logs for more info. ' + error + '</div>');
                        }
                    });
                }
            }
        });
    });

    
    
    $('#tdUsers').on('click', '#deleteUser', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        bootbox.confirm({
           title: name,
           message : 'Delete <strong>' + name + '</strong>?'+
           '<div id="resp_area"></div>',
           buttons: {
               confirm: {
                   label: "Confirm Delete",
                   className: "btn-danger"
               },
               cancel: {
                   label: "Cancel",
                   className: "btn-secondary"
               }
           },
           callback: function (result) {
               if (result) {
                   $.ajax({ 
                       url: '/core/core.php', 
                       type: 'POST',
                       data: {
                           request: 'deleteuser',
                           user_id: id
                       },
                       dataType: 'json',
                       success: function (data) {
                           if (data.success) {
                               reload_data();
                               bootbox.hideAll(); // Close the dialog on success
                           } else {
                               $('#resp_area').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>' + data.error + '</div>');
                           }
                       },
                       error: function (xhr, status, error) {
                           $('#resp_area').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>An error occurred, check server logs for more info. ' + error + '</div>');
                       }
                   });
                   return false;
               }
           },
           onEscape: function () {
               return true;
           }
       });
    });

    $('#addUser').click(function() {
        $('#addUserModal').modal('show');
        $('#addUserModalBody').html('Loading...');
        addUserModalMsg.innerHTML = '';
        $.ajax({
            url: '/pages/views/users/add_user.php',
            type: 'GET',
            success: function(data) {
                $('#addUserModalBody').html(data);
            },
            error: function(xhr, status, error) {
                $('#addUserModalBody').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>An error occurred while loading the form. ' + error + '</div>');
            }
        });
    });


    $('#tdUsers').on('click', '#editUser', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        editUserModalMsg.innerHTML = '';
        $('#editUserModal #id').val(id);
        $('#editUserModal').modal('show');
        $('#editUserModalLabel').html(name);
        $('#editUserModalBody').html('Loading...');
        $.ajax({
            url: '/pages/views/users/edit_user.php',
            type: 'GET',
            data: { 
                id: id 
            },
            success: function(data) {
                $('#editUserModalBody').html(data);
            },
            error: function(xhr, status, error) {
                $('#editUserModalBody').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>An error occurred while loading user data. ' + error + '</div>');
            }
        });
    
    });
	
    $('#importUsers').click(function() {
        var formData = new FormData($('#importUserForm')[0]);
        $.ajax({
            url: '/pages/views/users/import_users.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.success) {
                    $('#importUserModalMsg').html('<div class="alert alert-success"><i class="bi bi-check-circle mx-2"></i>' + data.message + '</div>');
                    reload_data();
                } else {
                    $('#importUserModalMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>' + data.message + '</div>');
                }
            },
            error: function(xhr, status, error) {
                $('#importUserModalMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle mx-2"></i>An error occurred while importing users. ' + error + '</div>');
            }
        });
    });

    function reload_data() {
        var table = $('#tdUsers').DataTable();
        $.ajax({
            url: '/core/users_data.php',
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                var localData = table.ajax.json().data;
                if (JSON.stringify(localData) !== JSON.stringify(data.data)) {
                    table.ajax.reload(null, true);
                    console.log('Changes detected, data reloaded');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error checking data:', error);
            }
        });
    };

    setInterval(reload_data, 10000); // Check for updates every 10 seconds

	function extrasShow() {
		$('[rel=tip]').tooltip({
			 html: true,
			 boundary: "window",
			 overflow: "auto",
			 container: "body",
			 delay: {"show": 100, "hide": 0},
		 });
	};

});
</script>
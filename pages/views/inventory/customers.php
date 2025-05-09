<div id="content-wrapper" class="d-flex flex-column">
<?php
require_once(__ROOT__.'/pages/top.php');
require_once(__ROOT__.'/func/php-settings.php');

?>
        <div class="container-fluid">
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="#" id="mainTitle">Customers</a></h2>
            </div>
            <div class="card-body">
            	<div class="text-right">
                	<div class="btn-group">
                    	<button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                        <div class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addCustomer"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
                        <li><a class="dropdown-item" id="exportCSV" href="#"><i class="fa-solid fa-file-export mx-2"></i>Export to CSV</a></li>
                        <li><a class="dropdown-item" id="exportJSON" href="/pages/export.php?format=json&kind=customers"><i class="fa-solid fa-file-export mx-2"></i>Export to JSON</a></li>
               			<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importJSON"><i class="fa-solid fa-file-import mx-2"></i>Import from JSON</a></li>
                        </div>
                    </div>
                </div>
                <table class="table table-striped" id="tdDataCustomers" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Address</th>
                      <th>Email</th>
                      <th>Web</th>
                      <th></th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
 
<!-- ADD NEW-->
<div class="modal fade" id="addCustomer" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addCustomerLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCustomerLabel">Add customer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="customer_inf"></div>
        <div id="customerForm">
          <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input class="form-control" name="name" type="text" id="name" />
          </div>
          <div class="mb-3">
            <label for="address" class="form-label">Address:</label>
            <input class="form-control" name="address" type="text" id="address" />
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input class="form-control" name="email" type="email" id="email" />
          </div>
          <div class="mb-3">
            <label for="website" class="form-label">Website:</label>
            <input class="form-control" name="website" type="url" id="website" />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="customer_add">Add</button>
      </div>
    </div>
  </div>
</div>

<!--IMPORT JSON MODAL-->
<div class="modal fade" id="importJSON" data-bs-backdrop="static" tabindex="-1" aria-labelledby="importJSONLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="importJSONLabel">Import customers from a JSON file</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="JSRestMsg"></div>
        <div class="progress mb-3">  
          <div id="uploadProgressBar" class="progress-bar" role="progressbar" aria-valuemin="0"></div>
        </div>
        <div id="backupArea">
          <div class="form-group row mb-3">
            <label for="jsonFile" class="col-md-3 col-form-label">JSON file</label>
            <div class="col-md-8">
              <input type="file" name="jsonFile" id="jsonFile" class="form-control" />
            </div>
          </div>
          <div class="col-md-12">
            <hr />
            <div class="alert alert-info">
                <p><strong>IMPORTANT:</strong></p>
                <ul>
                  <li>
                    <div id="raw" data-size="<?=getMaximumFileUploadSizeRaw()?>">Maximum file size: <strong><?=getMaximumFileUploadSize()?></strong></div>
                  </li>
                  <li>Any customer with a name that already exists, will be updated.</li>
                </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK">Close</button>
        <input type="submit" name="btnRestore" class="btn btn-primary" id="btnImportCustomers" value="Import">
      </div>
    </div>  
  </div>
</div>

<!--EDIT CUSTOMER MODAL-->            
<div class="modal fade" id="editCustomer" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editCustomerLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="editCustomerLabel">Edit customer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
      </div>
    </div>
  </div>
</div>


<script>
$(document).ready(function() {
	$('#mainTitle').click(function() {
	 	reload_data();
  	});
	var tdDataCustomers = $('#tdDataCustomers').DataTable( {
		columnDefs: [
			{ className: 'pv_vertical_middle text-center', targets: '_all' },
			{ orderable: false, targets: [4] },
		],
		dom: 'lrftip',
		buttons: [{
			extend: 'csvHtml5',
			title: "Customers",
			exportOptions: {
				columns: [0, 1, 2, 3]
			},
		}],
		processing: true,
		serverSide: true,
		searching: true,
		mark: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: 'Please Wait...',
			zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No customers added yet</strong></div></div>',
			search: '',
			searchPlaceholder: 'Search by name...',
		},
		ajax: {	
			url: '/core/list_customer_data.php',
			type: 'POST',
			dataType: 'json',
			data: function(d) {
					if (d.order.length>0){
						d.order_by = d.columns[d.order[0].column].data
						d.order_as = d.order[0].dir
					}
				},
		},
		columns: [
			{ data : 'name', title: 'Name' },
			{ data : 'address', title: 'Address' },
			{ data : 'email', title: 'Email' },
			{ data : 'web', title: 'Web' },
			{ data : null, title: '', render: actions }
		],
		order: [[ 0, 'asc' ]],
		lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
		pageLength: 20,
		displayLength: 20,
		drawCallback: function( settings ) {
			extrasShow();
		},
		stateSave: true,
		stateDuration: -1,
		stateLoadCallback: function (settings, callback) {
			$.ajax( {
				url: '/core/update_user_settings.php?set=listCustomers&action=load',
				dataType: 'json',
				success: function (json) {
					callback( json );
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			 url: "/core/update_user_settings.php?set=listCustomers&action=save",
			 data: data,
			 dataType: "json",
			 type: "POST"
		  });
		},	
	});
	
	
	function actions(data, type, row){
			data = '<div class="dropdown">' +
			'<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
				'<ul class="dropdown-menu dropdown-menu-right">';
			data += '<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editCustomer" rel="tip" title="Edit '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-edit mx-2"></i>Edit</a></li>';
			data += '<li><a href="'+ row.web +'" class="dropdown-item" target="_blank" rel="tip" title="Open '+ row.name +' page"><i class="fas fa-shopping-cart mx-2"></i>Go to customer</a></li>';
			data += '<div class="dropdown-divider"></div>';
			data += '<li><a class="dropdown-item link-danger" href="#" id="cDel" rel="tip" title="Delete '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-trash mx-2"></i>Delete</a></li>';
			data += '</ul></div>';
		return data;
	};
	
	function reload_data() {
		$('#tdDataCustomers').DataTable().ajax.reload(null, true);
	};
	
	$('#tdDataCustomers').on('click', '[id*=cDel]', function () {
		var c = {};
		c.ID = $(this).attr('data-id');
		c.Name = $(this).attr('data-name');
		
		bootbox.dialog({
		   title: "Confirm deletion",
		   message : 'Permanently delete <strong>'+ c.Name +'</strong> and its data?',
		   buttons :{
			   main: {
				   label : "Delete",
				   className : "btn-danger",
				   callback: function (){
						
					$.ajax({
						url: '/core/core.php', 
						type: 'POST',
						data: {
							action: "delete",
							type: "customer",
							customer_id: c.ID,
						},
						dataType: 'json',
						success: function (data) {
							if(data.success){
								$('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
								$('.toast-header').removeClass().addClass('toast-header alert-success');
								reload_data();
							}else if(data.error){
								$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error);
								$('.toast-header').removeClass().addClass('toast-header alert-danger');
							}
							$('.toast').toast('show');
						},
						error: function (xhr, status, error) {
							$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error);
							$('.toast-header').removeClass().addClass('toast-header alert-danger');
							$('.toast').toast('show');
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
	  
	
	$('#customer_add').on('click', function () {
		$.ajax({
			url: '/core/core.php',
			type: 'POST',
			dataType: 'json',
			data: {
				customer: 'add',
				name: $("#name").val(),
				address: $("#address").val(),
				email: $("#email").val(),
				web: $("#website").val(),
			},
			success: function(response){
				if(response.success){
				   $("#customer_inf").html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+response.success+'</div>');
					reload_data();
				}else{
					$("#customer_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+response.error+'</div>');
				}
			  },
				error: function (xhr, status, error) {
					$('#customer_inf').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
				}
		   });
		
	});
	
	function extrasShow() {
		$('[rel=tip]').tooltip({
			"html": true,
			"delay": {"show": 100, "hide": 0},
		});
	};
	
	
	$('#exportCSV').click(() => {
		$('#tdDataCustomers').DataTable().button(0).trigger();
	});
	
	$("#editCustomer").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const customer = e.relatedTarget.dataset.name;
	
		$.get("/pages/views/inventory/editCustomer.php?id=" + id)
			.then(data => {
			$("#editCustomerLabel", this).html(customer);
			$(".modal-body", this).html(data);
		});
	});

}); //END DOC

</script>
<script src="/js/import.customers.js"></script>

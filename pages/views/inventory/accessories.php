<div id="content-wrapper" class="d-flex flex-column">
<?php 
require_once(__ROOT__.'/pages/top.php'); 
require_once(__ROOT__.'/func/php-settings.php');

$sup = mysqli_query($conn, "SELECT id,name FROM ingSuppliers ORDER BY name ASC");
while ($suppliers = mysqli_fetch_array($sup)){
	    $supplier[] = $suppliers;
}
?>
 <div class="container-fluid">
    <div class="card shadow mb-4">
    	<div class="card-header py-3">
            <h2 class="m-0 font-weight-bold text-primary"><a href="#" id="mainTitle">Accessories</a></h2>
        </div>
        <div class="card-body">
	        <div class="text-right">
    	        <div class="btn-group">
        	        <button type="button" class="btn btn-primary dropdown-toggle mb-3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                    <div class="dropdown-menu dropdown-menu-right">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addAccessory"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
                    <li><a class="dropdown-item" id="exportCSV" href="#"><i class="fa-solid fa-file-export mx-2"></i>Export to CSV</a></li>
                    <li><a class="dropdown-item" id="exportJSON" href="/pages/export.php?format=json&kind=accessories"><i class="fa-solid fa-file-export mx-2"></i>Export to JSON</a></li>
               <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importJSON"><i class="fa-solid fa-file-import mx-2"></i>Import from JSON</a></li>
                </div>
            </div>        
        </div>   
        <table class="table table-striped" id="tdDataAccessories" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Name</th>
              <th>Accessory</th>
              <th>Price</th>
              <th>Supplier</th>
              <th>Pieces</th>
              <th></th>
            </tr>
          </thead>
        </table>
        </div>
       </div>
      </div>
    </div>
    
<!-- ADD MODAL-->
<div class="modal fade" id="addAccessory" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addAccessoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addAccessoryLabel">Add accessory</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="accessory_inf"></div>
        <div id="accessoryForm">
          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input class="form-control" name="name" type="text" id="name" />
          </div>
          <div class="mb-3">
            <label for="accessory" class="form-label">Accessory</label>
            <select name="accessory" id="accessory" class="form-control">
              <option value="Lid">Bottle lid</option>
              <option value="Ribbon">Ribbon</option>
              <option value="Packaging">Packaging</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input class="form-control" name="price" type="text" id="price" />
          </div>
          <div class="mb-3">
            <label for="pieces" class="form-label">Pieces in stock</label>
            <input class="form-control" name="pieces" type="text" id="pieces" />
          </div>
          <div class="mb-3">
            <label for="supplier" class="form-label">Supplier</label>
            <select name="supplier" id="supplier" class="form-control">
              <option value="" selected></option>
			  <?php
               	foreach($supplier as $sup) {
               		echo '<option value="'.$sup['name'].'">'.$sup['name'].'</option>';
            	}
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="supplier_link" class="form-label">Supplier URL</label>
            <input class="form-control" name="supplier_link" type="text" id="supplier_link" />
          </div>
          <div class="mb-3">
            <label for="pic" class="form-label">Image</label>
            <input type="file" name="pic" id="pic" class="form-control" />
          </div>
        </div>
        <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="accessory_add">Add</button>
      </div>
    </div>
  </div>
</div>

<!--IMPORT JSON MODAL-->
<div class="modal fade" id="importJSON" data-bs-backdrop="static" tabindex="-1" aria-labelledby="importJSONLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="importJSONLabel">Import accessories from a JSON file</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="JSRestMsg"></div>
        <div class="progress mb-3">  
          <div id="uploadProgressBar" class="progress-bar" role="progressbar" aria-valuemin="0"></div>
        </div>
        <div id="backupArea">
          <div class="form-group row mb-3">
            <label for="jsonFile" class="col-md-3 col-form-label">JSON file:</label>
            <div class="col-md-8">
              <input type="file" name="jsonFile" id="jsonFile" class="form-control" />
            </div>
          </div>
          <div class="col-md-12">
            <hr />
            <p><strong>IMPORTANT:</strong></p>
            <ul>
              <li>
                <div id="raw" data-size="<?=getMaximumFileUploadSizeRaw()?>">Maximum file size: <strong><?=getMaximumFileUploadSize()?></strong></div>
              </li>
              <li>Any accessory with a name that already exists, will be ignored.</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK">Close</button>
        <input type="submit" name="btnRestore" class="btn btn-primary" id="btnImportAccessories" value="Import">
      </div>
    </div>  
  </div>
</div>

<!--EDIT MODAL-->            
<div class="modal fade" id="editAccessory" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editAccessoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="editAccessoryLabel">Edit accessory</h5>
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
	
	var tdDataAccessories = $('#tdDataAccessories').DataTable( {
		columnDefs: [
			{ className: 'pv_vertical_middle text-center', targets: '_all' },
			{ orderable: false, targets: [5] },
		],
		dom: 'lrftip',
		buttons: [{
			extend: 'csvHtml5',
			title: "Accessories inventory",
			exportOptions: {
				columns: [0, 1, 2, 3, 4]
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
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No accessories added yet</strong></div></div>',
			search: '',
			searchPlaceholder: 'Search by name...',
		},
		ajax: {	
			url: '/core/list_accessory_data.php',
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
			{ data : 'name', title: 'Name', render: name },
			{ data : 'accessory', title: 'Accessory', render: accessory },
			{ data : 'price', title: 'Price (<?php echo $settings['currency'];?>)' },
			{ data : 'supplier', title: 'Supplier' },
			{ data : 'pieces', title: 'Pieces in stock' },
			{ data : null, title: '', render: actions }
		],
		order: [[ 0, 'asc' ]],
		lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
		pageLength: 20,
		displayLength: 20,
		stateSave: true,
		stateDuration: -1,
		stateLoadCallback: function (settings, callback) {
			$.ajax( {
				url: '/core/update_user_settings.php?set=listAccessories&action=load',
				dataType: 'json',
				success: function (json) {
					callback( json );
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			   url: "/core/update_user_settings.php?set=listAccessories&action=save",
				data: data,
				dataType: "json",
				type: "POST"
			});
		},
		drawCallback: function( settings ) {
			extrasShow();
		},

	});
	
	
	tdDataAccessories.on('requestChild.dt', function (e, row) {
		row.child(format(row.data())).show();
	});
	 
	tdDataAccessories.on('click', '#accessory_name', function (e) {
		let tr = e.target.closest('tr');
		let row = tdDataAccessories.row(tr); 
		if (row.child.isShown()) {
			row.child.hide();
		} else {
			row.child(format(row.data())).show();
		}
	});
	
	
	function format ( d ) {
		details = '<img src="'+d.photo+'" class="img_ifra"/>';
		return details;
	};
	
	function name(data, type, row){
		return '<i class="pv_point_gen pv_gen_li" id="accessory_name">'+row.name+'</i>';
	};
	
	function accessory(data, type, row){
		return '<i class="pv_point_gen pv_gen_li" id="accessory_type">'+row.accessory+'</i>';
	};
	
	function actions(data, type, row){	
			data = '<div class="dropdown">' +
			'<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
				'<ul class="dropdown-menu">';
			data += '<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editAccessory" rel="tip" title="Edit '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-edit mx-2"></i>Edit</a></li>';
			data += '<li><a href="'+ row.supplier_link +'" target="_blank" class="dropdown-item" rel="tip" title="Open '+ row.name +' page"><i class="fas fa-shopping-cart mx-2"></i>Go to supplier</a></li>';
			data += '<div class="dropdown-divider"></div>';
			data += '<li><a class="dropdown-item link-danger" href="#" id="accessoryDel" rel="tip" title="Delete '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-trash mx-2"></i>Delete</a></li>';
			data += '</ul></div>';
		return data;
	};
	
	function reload_data() {
		$('#tdDataAccessories').DataTable().ajax.reload(null, true);
	};
	
	$('#tdDataAccessories').on('click', '[id*=accessoryDel]', function () {
		var accessory = {};
		accessory.ID = $(this).attr('data-id');
		accessory.Name = $(this).attr('data-name');
		
		bootbox.dialog({
		   title: "Confirm deletion",
		   message : 'Permanently delete <strong>'+ accessory.Name +'</strong> and its data?',
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
							type: "accessory",
							accessoryId: accessory.ID,
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
	  
	
	$('#accessory_add').on('click', function () {
	
		$("#accessory_inf").html('<div class="alert alert-info">Please wait, file upload in progress....</div>');
		$("#accessory_add").prop("disabled", true);
		$("#accessory_add").prop('value', 'Please wait...');
			
		var fd = new FormData();
		var files = $('#pic')[0].files;
		var name = $('#name').val();
		var price = $('#price').val();
		var supplier = $('#supplier').val();
		var supplier_link = $('#supplier_link').val();
		var pieces = $('#pieces').val();
		var accessory = $('#accessory').val();
	
		if(files.length > 0 ){
			fd.append('pic_file',files[0]);
	
				$.ajax({
				  url: '/pages/upload.php?type=accessory&name=' + btoa(name) + '&price=' + price + '&supplier=' + btoa(supplier) + '&supplier_link=' + btoa(supplier_link) + '&pieces=' + pieces + '&accessory=' + btoa(accessory),
				  type: 'POST',
				  data: fd,
				  contentType: false,
				  processData: false,
				  cache: false,
				  dataType: 'json',
				  success: function(response){
					 if(response.success){
						$("#accessory_inf").html('<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>'+response.success+'</div>');
						$("#accessory_add").prop("disabled", false);
						$("#accessory_add").prop("value", "Add");
						reload_data();
					 }else{
						$("#accessory_inf").html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>'+response.error+'</div>');
						$("#accessory_add").prop("disabled", false);
						$("#accessory_add").prop("value", 'Add');
					 }
				  },
			   });
			}else{
				$("#accessory_inf").html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Please select a image to upload</div>');
				$("#accessory_add").prop("disabled", false);
				$("#accessory_add").prop("value", "Add");
			}
			
	});
	
	function extrasShow() {
		$('[rel=tip]').tooltip({
			"html": true,
			"delay": {"show": 100, "hide": 0},
		 });
	};
	
	
	$('#exportCSV').click(() => {
		$('#tdDataAccessories').DataTable().button(0).trigger();
	});
	
	$("#editAccessory").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const accessory = e.relatedTarget.dataset.name;
	
		$.get("/pages/views/inventory/editAccessory.php?id=" + id)
			.then(data => {
				$("#editAccessoryLabel", this).html(accessory);
				$(".modal-body", this).html(data);
			});
		});

}); //END DOC
</script>
<script src="/js/import.accessories.js"></script>

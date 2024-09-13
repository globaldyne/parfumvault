<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
<?php
$sup = mysqli_query($conn, "SELECT id,name FROM ingSuppliers ORDER BY name ASC");
while ($suppliers = mysqli_fetch_array($sup)){
	    $supplier[] = $suppliers;
}
?>
  <div class="card shadow mb-4">
      <div class="card-header py-3">
         <h2 class="m-0 font-weight-bold text-primary"><a href="#" id="mainTitle">Bottles</a></h2>
      </div>
      <div class="card-body">
      	<div class="text-right">
           	<div class="btn-group">
               	<button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                 <div class="dropdown-menu dropdown-menu-right">
                 <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addBottle"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
                 <li><a class="dropdown-item" id="exportCSV" href="#"><i class="fa-solid fa-file-export mx-2"></i>Export to CSV</a></li>
                 <li><a class="dropdown-item" id="exportJSON" href="/pages/export.php?format=json&kind=bottles"><i class="fa-solid fa-file-export mx-2"></i>Export to JSON</a></li>
              </div>
            </div>        
        </div>   
        <table class="table table-striped" id="tdDataBottles" width="100%" cellspacing="0">
           <thead>
              <tr>
                <th>Name</th>
                <th>Size (ml)</th>
                <th>Price</th>
                <th>Supplier</th>
                <th>Pieces</th>
                <th>Created</th>
                <th>Updated</th>
                <th></th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>

<!-- ADD BOTTLE MODAL-->
<div class="modal fade" id="addBottle" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addBottleLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addBottleLabel">Add Bottle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="bottle_inf"></div>
        
        <div class="row g-3">
          <div class="col-md-12">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" name="name" id="name" required>
          </div>
          <div class="col-md-4">
            <label for="size" class="form-label">Size (ml)</label>
            <input type="text" class="form-control" name="size" id="size" required>
          </div>
          <div class="col-md-4">
            <label for="price" class="form-label">Price</label>
            <input type="text" class="form-control" name="price" id="price" required>
          </div>
          <div class="col-md-4">
            <label for="height" class="form-label">Height</label>
            <input type="text" class="form-control" name="height" id="height" required>
          </div>
          <div class="col-md-4">
            <label for="width" class="form-label">Width</label>
            <input type="text" class="form-control" name="width" id="width" required>
          </div>
          <div class="col-md-4">
            <label for="diameter" class="form-label">Diameter</label>
            <input type="text" class="form-control" name="diameter" id="diameter" required>
          </div>
          <div class="col-md-4">
            <label for="weight" class="form-label">Weight (grams)</label>
            <input type="text" class="form-control" name="weight" id="weight" required>
          </div>
          <div class="col-md-4">
            <label for="pieces" class="form-label">Stock (pieces)</label>
            <input type="text" class="form-control" name="pieces" id="pieces" required>
          </div>
          <div class="col-md-4">
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
          <div class="col-md-4">
            <label for="supplier_link" class="form-label">Supplier URL</label>
            <input type="text" class="form-control" name="supplier_link" id="supplier_link" required>
          </div>
          <div class="col-md-12">
            <label for="notes" class="form-label">Notes</label>
            <input type="text" class="form-control" name="notes" id="notes" required>
          </div>
          <div class="col-12">
            <input type="file" name="pic" id="pic" class="form-control" />
          </div>
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="bottle_add" value="Add">
      </div>
    </div>
  </div>
</div>


<!--EDIT BOTTLE MODAL-->            
<div class="modal fade" id="editBottle" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editBottleLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="editBottleLabel">Edit bottle</h5>
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
	var tdDataBottles = $('#tdDataBottles').DataTable( {
		columnDefs: [
			{ className: 'pv_vertical_middle text-center', targets: '_all' },
			{ orderable: false, targets: [7] },
		],
		dom: 'lrftip',
		buttons: [{
					extend: 'csvHtml5',
					title: "Bottle Inventory",
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
			zeroRecords: 'Nothing found',
			search: '',
			searchPlaceholder: 'Search by name...',
		},
		ajax: {	
			url: '/core/list_bottle_data.php',
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
			{ data : 'ml', title: 'Size (ml)' },
			{ data : 'price', title: 'Price (<?php echo $settings['currency'];?>)' },
			{ data : 'supplier', title: 'Supplier' },
			{ data : 'pieces', title: 'Pieces in stock' },
			{ data : 'created', title: 'Created' },
			{ data : 'updated', title: 'Updated' },
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
				url: '/core/update_user_settings.php?set=listBottles&action=load',
				dataType: 'json',
				success: function (json) {
					callback( json );
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			 url: "/core/update_user_settings.php?set=listBottles&action=save",
			 data: data,
			 dataType: "json",
			 type: "POST"
		  });
		},	
	});
	
	tdDataBottles.on('requestChild.dt', function (e, row) {
		row.child(format(row.data())).show();
	});
	 
	tdDataBottles.on('click', '#bottle_name', function (e) {
		let tr = e.target.closest('tr');
		let row = tdDataBottles.row(tr); 
		if (row.child.isShown()) {
			row.child.hide();
		} else {
			row.child(format(row.data())).show();
		}
	});



	function format ( d ) {
		details = '<img src="'+d.photo+'" class="img_ifra"/><br><hr/>'+
		'<strong>Height:</strong><br><span class="details">'+d.height+
		'mm</span><br><strong>Width:</strong><br><span class="details">'+d.width+
		'mm</span><br><strong>Diameter:</strong><br><span class="details">'+d.diameter+
		'mm</span><br><strong>Weight:</strong><br><span class="details">'+d.weight+
		'g</span><br><strong>Notes:</strong><br><span class="details">'+d.notes;
	
		return details;
	};
	
	function name(data, type, row){
		return '<i class="pv_point_gen pv_gen_li" id="bottle_name">'+row.name+'</i>';
	};
	
	function actions(data, type, row){	
			data = '<div class="dropdown">' +
			'<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
				'<ul class="dropdown-menu dropdown-menu-right">';
			data += '<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editBottle" rel="tip" title="Edit '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-edit mx-2"></i>Edit</a></li>';
			data += '<li><a href="'+ row.supplier_link +'" class="dropdown-item" target="_blank" rel="tip" title="Open '+ row.supplier +' page"><i class="fas fa-shopping-cart mx-2"></i>Go to supplier</a></li>';
			data += '<div class="dropdown-divider"></div>';
			data += '<li><a class="dropdown-item" href="#" id="btlDel" style="color: #c9302c;" rel="tip" title="Delete '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-trash mx-2"></i>Delete</a></li>';
			data += '</ul></div>';
		return data;
	};
	
	function reload_data() {
		$('#tdDataBottles').DataTable().ajax.reload(null, true);
	};
	
	$('#tdDataBottles').on('click', '[id*=btlDel]', function () {
		var btl = {};
		btl.ID = $(this).attr('data-id');
		btl.Name = $(this).attr('data-name');
		
		bootbox.dialog({
		   title: "Confirm deletion",
		   message : 'Permanently delete <strong>'+ btl.Name +'</strong> and its data?',
		   buttons :{
			   main: {
				   label : "Delete",
				   className : "btn-danger",
				   callback: function (){
						
					$.ajax({
						url: '/pages/update_data.php', 
						type: 'POST',
						data: {
							action: "delete",
							type: "bottle",
							btlId: btl.ID,
							},
						dataType: 'json',
						success: function (data) {
							if(data.success){
								$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
								$('.toast-header').removeClass().addClass('toast-header alert-success');
								reload_data();
							}else if(data.error){
								$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
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
	  
	
	$('#bottle_add').on('click', function () {
	
		$("#bottle_inf").html('<div class="alert alert-info">Please wait, file upload in progress....</div>');
		$("#bottle_add").prop("disabled", true);
		$("#bottle_add").prop('value', 'Please wait...');
			
		var fd = new FormData();
		var files = $('#pic')[0].files;
		var name = $('#name').val();
		var size = $('#size').val();
		var price = $('#price').val();
		var weight = $('#weight').val();
		var supplier = $('#supplier').val();
		var supplier_link = $('#supplier_link').val();
	
		var height = $('#height').val();
		var width = $('#width').val();
		var diameter = $('#diameter').val();
		var notes = $('#notes').val();
		var pieces = $('#pieces').val();
	
		if(files.length > 0 ){
			fd.append('pic_file',files[0]);
	
				$.ajax({
				  url: '/pages/upload.php?type=bottle&name=' + btoa(name) + '&size=' + size + '&price=' + price + '&supplier=' + btoa(supplier) + '&supplier_link=' + btoa(supplier_link)+ '&height=' + height + '&width=' + width + '&diameter=' + diameter + '&notes=' + btoa(notes) + '&pieces=' + pieces + '&weight=' + weight,
				  type: 'POST',
				  data: fd,
				  contentType: false,
				  processData: false,
						cache: false,
				  dataType: 'json',
				  success: function(response){
					 if(response.success){
						$("#bottle_inf").html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+response.success+'</div>');
						$("#bottle_add").prop("disabled", false);
						$("#bottle_add").prop("value", "Add");
						reload_data();
					 }else{
						$("#bottle_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+response.error+'</div>');
						$("#bottle_add").prop("disabled", false);
						$("#bottle_add").prop("value", 'Add');
					 }
				  },
					error: function (xhr, status, error) {
						$('#bottle_inf').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
					}
			   });
			}else{
				$("#bottle_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a image to upload!</div>');
				$("#bottle_add").prop("disabled", false);
				$("#bottle_add").prop("value", "Add");
			}
			
	});
	
	function extrasShow() {
		$('[rel=tip]').tooltip({
			"html": true,
			"delay": {"show": 100, "hide": 0},
		 });
	};
	
	
	$('#exportCSV').click(() => {
		$('#tdDataBottles').DataTable().button(0).trigger();
	});
	
	$("#editBottle").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const bottle = e.relatedTarget.dataset.name;
	
		$.get("/pages/editBottle.php?id=" + id)
			.then(data => {
			$("#editBottleLabel", this).html(bottle);
			$(".modal-body", this).html(data);
		});
	});

}); //END DOC
</script>


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
            <h2 class="m-0 font-weight-bold text-primary"><a href="#" id="mainTitle">Lids</a></h2>
        </div>
        <div class="card-body">
	        <div class="text-right">
    	        <div class="btn-group">
        	        <button type="button" class="btn btn-primary dropdown-toggle mb-3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                    <div class="dropdown-menu dropdown-menu-right">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addLid"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
                    <li><a class="dropdown-item" id="exportCSV" href="#"><i class="fa-solid fa-file-export mx-2"></i>Export to CSV</a></li>
                    <li><a class="dropdown-item" id="exportJSON" href="/pages/export.php?format=json&kind=lids"><i class="fa-solid fa-file-export mx-2"></i>Export to JSON</a></li>
                </div>
            </div>        
        </div>   
        <table class="table table-striped" id="tdDataLids" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Style</th>
              <th>Colour</th>
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
    
<!-- ADD LID MODAL-->
<div class="modal fade" id="addLid" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addLidLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addLidLabel">Add Lid</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="lid_inf"></div>
        <div id="lidForm">
          <div class="mb-3">
            <label for="style" class="form-label">Style:</label>
            <input class="form-control" name="style" type="text" id="style" />
          </div>
          <div class="mb-3">
            <label for="colour" class="form-label">Colour:</label>
            <input class="form-control" name="colour" type="text" id="colour" />
          </div>
          <div class="mb-3">
            <label for="price" class="form-label">Price:</label>
            <input class="form-control" name="price" type="text" id="price" />
          </div>
          <div class="mb-3">
            <label for="pieces" class="form-label">Pieces in stock:</label>
            <input class="form-control" name="pieces" type="text" id="pieces" />
          </div>
          <div class="mb-3">
            <label for="supplier" class="form-label">Supplier:</label>
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
            <label for="supplier_link" class="form-label">Supplier URL:</label>
            <input class="form-control" name="supplier_link" type="text" id="supplier_link" />
          </div>
          <div class="mb-3">
            <label for="pic" class="form-label">Image:</label>
            <input type="file" name="pic" id="pic" class="form-control" />
          </div>
        </div>
        <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="lid_add">Add</button>
      </div>
    </div>
  </div>
</div>



<!--EDIT LID MODAL-->            
<div class="modal fade" id="editLid" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editLidLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="editLidLabel">Edit lid</h5>
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

<script> 
$(document).ready(function() {
	$('#mainTitle').click(function() {
	 	reload_data();
  	});
	
	var tdDataLids = $('#tdDataLids').DataTable( {
		columnDefs: [
			{ className: 'pv_vertical_middle text-center', targets: '_all' },
			{ orderable: false, targets: [5] },
		],
		dom: 'lrftip',
		buttons: [{
					extend: 'csvHtml5',
					title: "Lid Inventory",
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
			search: 'Quick Search:',
			searchPlaceholder: 'Name..',
		},
		ajax: {	
			url: '/core/list_lid_data.php',
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
			{ data : 'style', title: 'Style', render: style },
			{ data : 'colour', title: 'Colour' },
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
				url: '/core/update_user_settings.php?set=listLids&action=load',
				dataType: 'json',
				success: function (json) {
					callback( json );
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			   url: "/core/update_user_settings.php?set=listLids&action=save",
				data: data,
				dataType: "json",
				type: "POST"
			});
		},
		drawCallback: function( settings ) {
			extrasShow();
		},

	});
	
	
	tdDataLids.on('requestChild.dt', function (e, row) {
		row.child(format(row.data())).show();
	});
	 
	tdDataLids.on('click', '#lid_name', function (e) {
		let tr = e.target.closest('tr');
		let row = tdDataLids.row(tr); 
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
	
	function style(data, type, row){
		return '<i class="pv_point_gen pv_gen_li" id="lid_name">'+row.style+'</i>';
	};
	
	function actions(data, type, row){	
			data = '<div class="dropdown">' +
			'<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
				'<ul class="dropdown-menu dropdown-menu-right">';
			data += '<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editLid" rel="tip" title="Edit '+ row.style +'" data-id='+ row.id +' data-name="'+ row.style +'"><i class="fas fa-edit mx-2"></i>Edit</a></li>';
			data += '<li><a href="'+ row.supplier_link +'" target="_blank" class="dropdown-item" rel="tip" title="Open '+ row.style +' page"><i class="fas fa-shopping-cart mx-2"></i>Go to supplier</a></li>';
			data += '<div class="dropdown-divider"></div>';
			data += '<li><a class="dropdown-item" href="#" id="ldlDel" style="color: #c9302c;" rel="tip" title="Delete '+ row.name +'" data-id='+ row.id +' data-name="'+ row.style +'"><i class="fas fa-trash mx-2"></i>Delete</a></li>';
			data += '</ul></div>';
		return data;
	};
	
	function reload_data() {
		$('#tdDataLids').DataTable().ajax.reload(null, true);
	};
	
	$('#tdDataLids').on('click', '[id*=ldlDel]', function () {
		var ldl = {};
		ldl.ID = $(this).attr('data-id');
		ldl.Name = $(this).attr('data-name');
		
		bootbox.dialog({
		   title: "Confirm deletion",
		   message : 'Permanently delete <strong>'+ ldl.Name +'</strong> and its data?',
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
							type: "lid",
							lidId: ldl.ID,
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
	  
	
	$('#lid_add').on('click', function () {
	
		$("#lid_inf").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
		$("#lid_add").prop("disabled", true);
		$("#lid_add").prop('value', 'Please wait...');
			
		var fd = new FormData();
		var files = $('#pic')[0].files;
		var style = $('#style').val();
		var price = $('#price').val();
		var supplier = $('#supplier').val();
		var supplier_link = $('#supplier_link').val();
		var pieces = $('#pieces').val();
		var colour = $('#colour').val();
	
		if(files.length > 0 ){
			fd.append('pic_file',files[0]);
	
				$.ajax({
				  url: '/pages/upload.php?type=lid&style=' + btoa(style) + '&price=' + price + '&supplier=' + btoa(supplier) + '&supplier_link=' + btoa(supplier_link) + '&pieces=' + pieces + '&colour=' + colour,
				  type: 'POST',
				  data: fd,
				  contentType: false,
				  processData: false,
						cache: false,
				  dataType: 'json',
				  success: function(response){
					 if(response.success){
						$("#lid_inf").html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+response.success+'</div>');
						$("#lid_add").prop("disabled", false);
						$("#lid_add").prop("value", "Add");
						reload_data();
					 }else{
						$("#lid_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+response.error+'</div>');
						$("#lid_add").prop("disabled", false);
						$("#lid_add").prop("value", 'Add');
					 }
				  },
			   });
			}else{
				$("#lid_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a image to upload!</div>');
				$("#lid_add").prop("disabled", false);
				$("#lid_add").prop("value", "Add");
			}
			
	});
	
	function extrasShow() {
		$('[rel=tip]').tooltip({
			"html": true,
			"delay": {"show": 100, "hide": 0},
		 });
	};
	
	
	$('#exportCSV').click(() => {
		$('#tdDataLids').DataTable().button(0).trigger();
	});
	
	$("#editLid").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const lid = e.relatedTarget.dataset.style;
	
		$.get("/pages/editLid.php?id=" + id)
			.then(data => {
			$("#editLidLabel", this).html(lid);
			$(".modal-body", this).html(data);
		});
	});

}); //END DOC
</script>

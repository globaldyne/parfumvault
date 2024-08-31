<div id="content-wrapper" class="d-flex flex-column">
<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/pages/top.php'); 
?>
<div class="container-fluid">
  <div>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h2 class="m-0 font-weight-bold text-primary"><a href="#" id="mainTitle">Batch History</a></h2>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped" id="tdDataBatches" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Batch ID</th>
              <th>Product Name</th>
              <th>Created</th>
              <th></th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
</div>
    
<script>
$(document).ready(function() {

	var tdDataCustomers = $('#tdDataBatches').DataTable( {
		columnDefs: [
			{ className: 'pv_vertical_middle text-center', targets: '_all' },
			{ orderable: false, targets: [3]}
		],
		dom: 'lrftip',
		processing: true,
		serverSide: true,
		searching: true,
		mark: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<div class="spinner-grow"></div> Please Wait...',
			zeroRecords: 'Nothing found',
			search: 'Quick Search:',
			searchPlaceholder: 'Batch ID..',
		},
		ajax: {	
			url: '/core/list_batch_data.php',
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
				{ data : 'id', title: 'Batch ID', render: batchID },
				{ data : 'product_name', title: 'Product Name' },
				{ data : 'created', title: 'Created' },
				{ data : 'pdf', title: '', render: actions}
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
					url: '/core/update_user_settings.php?set=listBatches&action=load',
					dataType: 'json',
					success: function (json) {
						callback( json );
					}
				});
			},
			stateSaveCallback: function (settings, data) {
			   $.ajax({
				 url: "/core/update_user_settings.php?set=listBatches&action=save",
				 data: data,
				 dataType: "json",
				 type: "POST"
			  });
			},
		});
	
	
	function batchID(data, type, row){
		if(row.pdf){
			var data = '<a href="/pages/viewDoc.php?type=batch&id='+ row.id +'" target="_blank" rel="tip" title="View file">' + row.id + '<i class="fas fa-file-pdf ml-2"></i></a>'
		}else{
			var data = '<a href="#" rel="tip" title="File not available" class="fas fa-exclamation-triangle"></a>';
		}
		return data;    
	};

	function actions(data, type, row){
		if(row.pdf){
			var data = '<i rel="tip" title="Delete '+ row.id +'" class="pv_point_gen fas fa-trash text-danger" id="batch_del" data-name="'+ row.product_name +'" data-id='+ row.id +'></i>'
		}else{
			var data = '<a href="#" rel="tip" title="File not available" class="fas fa-exclamation-triangle"></a>';
		}
		return data;    
	};
	
	
	function extrasShow() {
		$('[rel=tip]').tooltip({
			"html": true,
			"delay": {"show": 100, "hide": 0},
		 });
	};
	
	function reload_data() {
		$('#tdDataBatches').DataTable().ajax.reload(null, true);
	};
	
	$('#tdDataBatches').on('click', '[id*=batch_del]', function () {
		var batch = {};
		batch.ID = $(this).attr('data-id');
		batch.Name = $(this).attr('data-name');
		
		bootbox.dialog({
		   title: "Confirm delete",
		   message : "Delete bacth <strong>" + batch.ID + "</strong> for product " + batch.Name + "?",
		   buttons :{
			   main: {
				   label : "Remove",
				   className : "btn-danger",
				   callback: function (){
				   $.ajax({
						url: '/pages/update_data.php', 
						type: 'POST',
						data: {
							action: 'batch',
							bid: batch.ID,
							name: batch.Name,
							remove: true,
						},
						dataType: 'json',
						success: function (data) {
							if ( data.success ) {
								$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
								$('.toast-header').removeClass().addClass('toast-header alert-success');
								reload_data();
							} else {
								$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
								$('.toast-header').removeClass().addClass('toast-header alert-danger');
							}
							$('.toast').toast('show');
						},
						error: function (xhr, status, error) {
							$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error);
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
	
}); //END DOC
 
</script>
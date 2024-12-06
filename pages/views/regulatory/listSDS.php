<div id="content-wrapper" class="d-flex flex-column">
<?php 
require_once(__ROOT__.'/pages/top.php');
require_once(__ROOT__.'/func/php-settings.php');

$q = mysqli_query($conn, "SELECT id,name FROM documents WHERE type = '0' AND isSDS = '1'");
while($res = mysqli_fetch_array($q)){
    $data[] = $res;
}


?>
<style>
.mfp-iframe-holder .mfp-content {
    line-height: 0;
    width: 50%;
    max-width: 100%; 
	height: 750px;
}
</style>
<div class="container-fluid">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h2 class="m-0 font-weight-bold text-primary"><a href="#" id="mainTitle">SDSs</a></h2>
    </div>
    <div class="card-body">
      <div class="table-responsive">
       <table class="table table-striped table-bordered">
         <tr class="noBorder">
             <div class="text-right">
              <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <li><a class="dropdown-item popup-link" href="/pages/views/regulatory/wizard.php"><i class="fa-solid fa-plus mx-2"></i>Create new</a></li>
                  </div>
                </div>        
             </div>
         </tr>
        </table>

        <table class="table table-striped" id="tdDataSDS" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Product</th>
              <th>Use</th>
              <th>Download</th>
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




<script> 
$(document).ready(function() {

	$('.popup-link').magnificPopup({
		type: 'iframe',
		closeOnContentClick: false,
		closeOnBgClick: false,
		showCloseBtn: true,
	});
	
	var tdDataSDS = $('#tdDataSDS').DataTable( {
	columnDefs: [
		{ className: 'pv_vertical_middle text-center', targets: '_all' },
		{ orderable: false, targets: [2, 4] },
	],
	dom: 'lrftip',
	processing: true,
	serverSide: true,
	searching: true,
	mark: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: 'Please Wait...',
		zeroRecords: 'Nothing found',
		emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No documents found</strong></div></div>',

		search: '',
		searchPlaceholder: 'Search by name...',
	},
	ajax: {	
		url: '/core/list_SDS_data.php',
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
            { data : 'product_name', title: 'Product', render: name },
            { data : 'product_use', title: 'Product use' },
            { data : 'docID', title: 'Download', render: docData },
			{ data : 'created_at', title: 'Created' },
			{ data : null, title: '', render: actions },
		],
		order: [[ 0, 'asc' ]],
		lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
		pageLength: 20,
		displayLength: 20,
		stateSave: true,
		stateDuration: -1,
		stateLoadCallback: function (settings, callback) {
       		$.ajax( {
           		url: '/core/update_user_settings.php?set=listSDS&action=load',
           		dataType: 'json',
           		success: function (json) {
            	   	callback( json );
           		}
       		});
    	},
    	stateSaveCallback: function (settings, data) {
	   		$.ajax({
		 		url: "/core/update_user_settings.php?set=listSDS&action=save",
		 		data: data,
				dataType: "json",
			 	type: "POST"
	  		});
		},
		drawCallback: function( settings ) {
			extrasShow();
		},

	});
	
	
	
	function reload_data() {
    	$('#tdDataSDS').DataTable().ajax.reload(null, true);
	};
	
	function name(data, type, row){
		return '<i class="pv_point_gen pv_gen_li" id="SDS_name">'+row.product_name+'</i>';
	};
	
	function docData(data, type, row){
		return '<a href="/pages/viewDoc.php?type=sds&id='+row.docID+'" target="_blank" class="fa fa-file-alt"></a>';    
	};
	
	function actions(data, type, row){	
		data = '<div class="dropdown">' +
			'<button type="button" class="btn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
				'<ul class="dropdown-menu dropdown-menu-right">';
		data += '<li><a class="dropdown-item link-danger" href="#" id="cmpDel" rel="tip" title="Delete '+ row.product_name +'" data-id='+ row.id +' data-name="'+ row.product_name +'"><i class="fas fa-trash mx-2"></i>Delete</a></li>';
		data += '</ul></div>';
		return data;
	};



	$('#tdDataSDS').on('click', '[id*=cmpDel]', function () {
		var SDS = {};
		SDS.ID = $(this).attr('data-id');
		SDS.Name = $(this).attr('data-name');
		
		bootbox.dialog({
		   title: "Confirm deletion",
		   message : 'Permanently delete <strong>'+ SDS.Name +'</strong> and its data?',
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
							type: "SDS",
							SDSID: SDS.ID,
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
	
	function extrasShow() {
		$('[rel=tip]').tooltip({
			"html": true,
			"delay": {"show": 100, "hide": 0},
		 });
	};

	$('#mainTitle').click(function() {
	 	reload_data();
  	});
	
}); //END DOC
</script>

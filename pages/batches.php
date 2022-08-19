<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
<div class="container-fluid">
  <div>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h2 class="m-0 font-weight-bold text-primary"><a href="javascript:reload_data()">Batch History</a></h2>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="tdDataBatches" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Batch ID</th>
              <th>Product Name</th>
              <th>Created</th>
              <th>Final Product</th>
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
            { data : 'id', title: 'Batch ID' },
			{ data : 'product_name', title: 'Product Name' },
			{ data : 'created', title: 'Created' },
			{ data : 'pdf', title: 'Final Product', render: actions}
			],
	order: [[ 0, 'asc' ]],
	lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
	pageLength: 20,
	displayLength: 20,
	drawCallback: function( settings ) {
			extrasShow();
    	}
	});
	
}); //END DOC

function actions(data, type, row){
	if(row.state){
		var x = '<a href="'+ row.pdf +'" target="_blank" rel="tip" title="Download file" class="fas fa-file-pdf"></a>'
	}else{
		var x = '<a href="#" rel="tip" title="File not available" class="fas fa-exclamation-triangle"></a>';
	}
	return x;    
}


function extrasShow() {
	$('[rel=tip]').tooltip({
        "html": true,
        "delay": {"show": 100, "hide": 0},
     });
};
function reload_data() {
    $('#tdDataBatches').DataTable().ajax.reload(null, true);
}

 
</script>
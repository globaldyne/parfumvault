<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>   
<div class="container-fluid">
  <div>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h2 class="m-0 font-weight-bold text-primary"><a href="javascript:reload_data()">Scheduled Formulas</a></h2>
    </div>
    <div class="card-body">
      <div class="table-responsive">
      <div id="innermsg"></div>
        <table class="table table-bordered" id="tdDataPending" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Formula Name</th>
              <th>Ingredients Pending</th>
              <th>Progress</th>
              <th>Scheduled</th>
              <th>Actions</th>
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

	var tdDataPending = $('#tdDataPending').DataTable( {
	columnDefs: [
		{ className: 'pv_vertical_middle text-center', targets: '_all' },
		{ orderable: false, targets: [1,4] },
	],
	dom: 'lrftip',
	processing: true,
	serverSide: true,
	searching: true,
	mark: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: 'Please Wait...',
		zeroRecords: 'No pending formulas found',
		search: 'Quick Search:',
		searchPlaceholder: 'Name..',
		},
	ajax: {	
		url: '/core/pending_formulas_data.php?meta=1',
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
            { data : 'name', title: 'Formula Name', render: name },
			{ data : null, title: 'Ingredients remaining', render: ingredients },
			{ data : 'madeOn', title: 'Porgress', render: progress },
			{ data : 'scheduledOn', title: 'Scheduled' },
			{ data : null, title: 'Actions', render: actions },
			],
	order: [[ 0, 'asc' ]],
	lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
	pageLength: 20,
	displayLength: 20,
	});
	
}); //END DOC

function progress(data, type, row){
	
 	const perc = Math.round(100 - (row.total_ingredients_left / row.total_ingredients) * 100);
 	const nowVal = row.total_ingredients_left;
	const maxVal = row.total_ingredients;
	
	if(perc != 0){
		data = '<div class="progress">' + 
			  '<div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: '+perc+'%;" aria-valuenow="'+nowVal+'" aria-valuemin="0" aria-valuemax="100">'+perc+'% Complete</div>' +
			'</div>';
	}else{
		data = '<i class="fas fa-hourglass-start" rel="tip" title="Not started yet"></i>';
	}
	return data;
}

function name(data, type, row){
	data = '<a href="/pages/makeFormula.php?fid='+ row.fid +'" target="_blank">'+row.name+'</a>' ;	
	return data;
}

function ingredients(data, type, row){
	data = row.total_ingredients_left + '/' + row.total_ingredients ;	
	return data;
}

function actions(data, type, row){
	return '<i rel="tip" title="Delete '+ row.name +'" class="pv_point_gen fas fa-trash" style="color: #c9302c;" id="pend_remove" data-name="'+ row.name +'" data-id='+ row.fid +'></i>';    
}

function reload_data() {
    $('#tdDataPending').DataTable().ajax.reload(null, true);
}


$('#tdDataPending').on('click', '[id*=pend_remove]', function () {
	var frm = {};
	frm.ID = $(this).attr('data-id');
	frm.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm removal",
       message : 'Remove <strong>'+ frm.Name +'</strong>?',
       buttons :{
           main: {
               label : "Delete",
               className : "btn-danger",
               callback: function (){
			   $.ajax({
					url: '/pages/manageFormula.php', 
					type: 'POST',
					data: {
						action: 'todo',
						fid: frm.ID,
						name: frm.Name,
						remove: true,
						},
					dataType: 'json',
					success: function (data) {
						if(data.success) {
							var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
								reload_data();
							} else {
								var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				
							}
							$('#innermsg').html(msg);
					}
				});
				
                 return true;
               }
           },
           cancel: {
               label : "Cancel",
               className : "btn-default",
               callback : function() {
                   return true;
               }
           }   
       },onEscape: function () {return true;}
   });
});
</script>
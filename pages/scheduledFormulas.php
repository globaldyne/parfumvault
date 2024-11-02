<div id="content-wrapper" class="d-flex flex-column">
<?php 
require_once(__ROOT__.'/pages/top.php');
require_once(__ROOT__.'/func/php-settings.php');

?>   
<div class="container-fluid">
  <div>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h2 class="m-0 font-weight-bold text-primary-emphasis"><a href="#" id="mainTitle">Scheduled Formulas</a></h2>
    </div>
    <div class="pv_menu_formulas">
        <div class="text-right">
            <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                <div class="dropdown-menu dropdown-menu-right">
                  <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#required_materials"><i class="fa-solid fa-pallet mx-2"></i>Required ingredients</a></li>
                  <div class="dropdown-divider"></div>
                  <li><a class="dropdown-item" href="/pages/operations.php?action=exportMaking"><i class="fa-solid fa-file-export mx-2"></i>Export as JSON</a></li>
                  <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#import_making_json"><i class="fa-solid fa-file-import mx-2"></i>Import from JSON</a></li>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped" id="tdDataScheduled" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Formula Name</th>
              <th>Ingredients Pending</th>
              <th>Progress</th>
              <th>Scheduled</th>
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
	function extrasShow() {
		$('[rel=tip]').tooltip({
			"html": true,
			"delay": {"show": 100, "hide": 0},
		});
	};
	$('#mainTitle').click(function() {
	 	reload_data();
  	});
	var tdDataScheduled = $('#tdDataScheduled').DataTable({
		columnDefs: [
			{ className: 'pv_vertical_middle text-center', targets: '_all' },
			{ orderable: false, targets: [1, 4] },
		],
		dom: 'lrftip',
		processing: true,
		serverSide: true,
		searching: true,
		mark: true,
		responsive: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: 'Please Wait...',
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No scheduled to make formulas</strong></div></div>',
			zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No formulas found</strong></div></div>',
			search: '',
			searchPlaceholder: 'Search by formula name...',
		},
		ajax: {
			url: '/core/pending_formulas_data.php?meta=1',
			type: 'POST',
			dataType: 'json',
			data: function(d) {
				if (d.order.length > 0) {
					d.order_by = d.columns[d.order[0].column].data;
					d.order_as = d.order[0].dir;
				}
			},
		},
		columns: [
			{ data: 'name', title: 'Formula Name', render: name },
			{ data: null, title: 'Ingredients remaining', render: ingredients },
			{ data: 'madeOn', title: 'Progress', render: progress },
			{ data: 'scheduledOn', title: 'Scheduled', render: fDate },
			{ data: null, title: '', render: actions },
		],
		order: [[0, 'asc']],
		lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
		pageLength: 20,
		displayLength: 20,
		stateSave: true,
		stateDuration: -1,
		drawCallback: function( settings ) {
			extrasShow();
		},
		stateLoadCallback: function(settings, callback) {
			$.ajax({
				url: '/core/update_user_settings.php?set=listTodo&action=load',
				dataType: 'json',
				success: function(json) {
					callback(json);
				}
			});
		},
		stateSaveCallback: function(settings, data) {
			$.ajax({
				url: "/core/update_user_settings.php?set=listTodo&action=save",
				data: data,
				dataType: "json",
				type: "POST"
			});
		},
	});

	
	$("#required_materials").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const name = e.relatedTarget.dataset.name;
	
		$.get("/pages/views/formula/pendingMaterials.php")
			.then(data => {
				$(".modal-body", this).html(data);
		});
	});


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
	};
	
	function name(data, type, row){
		
		data ='<div class="btn-group"><a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+row.name+'</a><div class="dropdown-menu dropdown-menu-right">';
		
		data+='<li><a class="dropdown-item" href="/pages/makeFormula.php?fid='+ row.fid +'" target="_blank"><i class="fa-solid fa-flask-vial mx-2"></i>Make formula</a></li>';
	
		data+='<li><a class="dropdown-item" href="/?do=Formula&id='+row.id+'" target="_blank"><i class="fa-solid fa-flask mx-2"></i>Go to formula</a></li>';
							
		data+='</div></div>';
		return data;
	};
	
	function ingredients(data, type, row){
		data = row.total_ingredients_left + '/' + row.total_ingredients ;	
		return data;
	};
	
	function fDate(data, type, row, meta){
	  if(type === 'display'){
		if(data == '0000-00-00 00:00:00'){
		  data = '-';
		}else{
			let dateTimeParts= data.split(/[- :]/); 
			dateTimeParts[1]--; 
			const dateObject = new Date(...dateTimeParts); 
			data = dateObject.toLocaleDateString() + " " + dateObject.toLocaleTimeString();
		}
	  }
	  return data;
	};
	
	function actions(data, type, row){
		data = '<div class="dropdown">' +
			'<button type="button" class="btn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
				'<ul class="dropdown-menu dropdown-menu-right">';
		data += '<li><a class="dropdown-item link-danger" href="#" id="pend_remove" rel="tip" title="Delete '+ row.name +'" data-id='+ row.fid +' data-name="'+ row.name +'"><i class="fas fa-trash mx-2"></i>Delete</a></li>';
		data += '</ul></div>';
		return data;  
	};
	
	function reload_data() {
		$('#tdDataScheduled').DataTable().ajax.reload(null, true);
	};
	
	
	$('#tdDataScheduled').on('click', '[id*=pend_remove]', function () {
		var frm = {};
		frm.ID = $(this).attr('data-id');
		frm.Name = $(this).attr('data-name');
		
		bootbox.dialog({
		   title: "Confirm removal",
		   message : "Remove formula <strong>" + frm.Name + "</strong> from scheduled formulas? <br />Your original formula will not be affected but any progress of making the formula will be lost.",
		   buttons :{
			   main: {
				   label : "Remove",
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
<!-- IMPORT JSON MODAL -->
<div class="modal fade" id="import_making_json" data-bs-backdrop="static" tabindex="-1" aria-labelledby="import_making_json" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import a JSON file</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="JSRestMsg"></div>
        <div class="progress">  
          <div id="uploadProgressBar" class="progress-bar" role="progressbar" aria-valuemin="0"></div>
        </div>
        <div id="backupArea" class="mt-4">
          <div class="form-group row">
            <label for="jsonFile" class="col-auto col-form-label">JSON file</label>
            <div class="col-md">
              <input type="file" name="jsonFile" id="jsonFile" class="form-control" />
            </div>
          </div>
          <div class="col-md-12 mt-3">
            <hr />
            <p><strong>IMPORTANT</strong></p>
            <ul>
              <li>
                <div id="raw" data-size="<?=getMaximumFileUploadSizeRaw()?>">Maximum file size: <strong><?=getMaximumFileUploadSize()?></strong></div>
              </li>
              <li>Please make sure you have taken a backup before importing a JSON file</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK">Close</button>
        <button type="submit" name="btnRestore" class="btn btn-primary" id="btnRestoreMaking">Import</button>
      </div>
    </div>
  </div>
</div>


<!-- REQUIRED MATERIALS MODAL -->
<div class="modal fade" id="required_materials" data-bs-backdrop="static" tabindex="-1" aria-labelledby="required_materials" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Required ingredients for all the pending formulas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">Please wait...</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK">Close</button>
      </div>
    </div>
  </div>
</div>


<script src="/js/import.making.js"></script>

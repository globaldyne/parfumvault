<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');

?>
<h3>Perfume types</h3>
<hr>
<div class="card-body">
  <div class="text-right">
    <div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
        <div class="dropdown-menu">
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addpType"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
          <li><a class="dropdown-item" href="/core/core.php?action=exportPerfTypes"><i class="fa-solid fa-file-code mx-2"></i>Export as JSON</a></li>
        </div>
    </div>                    
  </div>
</div>
<table id="tdperfTypes" class="table table-striped" style="width:100%">
  <thead>
      <tr>
          <th>Name</th>
          <th>Concentration</th>
          <th>Description</th>
          <th></th>
      </tr>
   </thead>
</table>
<script>
$(document).ready(function() {
	$.fn.dataTable.ext.errMode = 'none';

	$('[data-bs-toggle="tooltip"]').tooltip();
	var tdperfTypes = $('#tdperfTypes').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [3] }
		],
		dom: 'lfrtip',
		processing: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No perfume types added yet</strong></div></div>',
			zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
			search: '',
			searchPlaceholder: 'Search by name...',
		},
		ajax: {	url: '/core/list_perfume_types_data.php' },
		columns: [
				  { data : 'name', title: 'Name', render: name },
				  { data : 'concentration', title: 'Concentration', render: concentration},
				  { data : 'description', title: 'Description', render: description},
				  { data : null, title: '', render: actions},		   
		],
		order: [[ 1, 'asc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
		pageLength: 20,
		displayLength: 20,
		stateSave: true,
		stateDuration: -1,
		stateLoadCallback: function (settings, callback) {
			$.ajax( {
				url: '/core/update_user_settings.php?set=listPerfTypes&action=load',
				dataType: 'json',
				success: function (json) {
					callback( json );
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			 url: "/core/update_user_settings.php?set=listPerfTypes&action=save",
			 data: data,
			 dataType: "json",
			 type: "POST"
		  });
		},
	}).on('error.dt', function(e, settings, techNote, message) {
		var m = message.split(' - ');
		$('#tdperfTypes').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
    });

	function name(data, type, row){
		return '<a href="#" class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</a>';
	};
	
	
	function concentration(data, type, row){
		return '<a href="#" class="concentration pv_point_gen" data-name="concentration" data-type="text" data-pk="'+row.id+'">'+row.concentration+'</a>';    
	};
	
	function description(data, type, row){
		return '<a href="#" class="description pv_point_gen" data-name="description" data-type="textarea" data-pk="'+row.id+'">'+row.description+'</a>';    
	};
	
	function actions(data, type, row){
		data = '<div class="dropdown">' +
		'<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
			'<ul class="dropdown-menu">';
		data += '<li><a class="dropdown-item text-danger" href="#" id="sDel" rel="tip" title="Delete '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-trash mx-2"></i>Delete</a></li>';
		data += '</ul></div>';
		return data;
	};
	
	
	$('#tdperfTypes').editable({
		container: 'body',
		selector: 'a.name',
		url: "/core/core.php?perfType=update",
		title: 'Perfume type name',
		ajaxOptions: {
			type: "POST",
			dataType: 'json'
		},
		success: function (data) {
			if (data.success) {
				reload_data();
			} else if (data.error) {
				$('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		},
		error: function (xhr, status, error) {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
			$('.toast').toast('show');
		},		
		validate: function(value){
			if($.trim(value) == ''){
				return 'This field is required';
			}
		}
	});
	  
	$('#tdperfTypes').editable({
		container: 'body',
		selector: 'a.concentration',
		url: "/core/core.php?perfType=update",
		title: 'Concentration in %',
		ajaxOptions: {
			type: "POST",
			dataType: 'json'
		},
		success: function (data) {
			if (data.success) {
				reload_data();
			} else if (data.error) {
				$('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		},
		error: function (xhr, status, error) {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> An error occurred, check server logs for more info. '+ error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
			$('.toast').toast('show');
		},
		validate: function(value){
			if($.trim(value) == ''){
				return 'This field is required';
			}
			if($.isNumeric(value) == '' ){
				return 'Numbers only allowed';
			}
		}
	});
		
	$('#tdperfTypes').editable({
		container: 'body',
		selector: 'a.description',
		url: "/core/core.php?perfType=update",
		title: 'Short description',
		ajaxOptions: {
			type: "POST",
			dataType: 'json'
		},
		success: function (data) {
			if (data.success) {
				reload_data();
			} else if (data.error) {
				$('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		},
		error: function (xhr, status, error) {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> An error occurred, check server logs for more info. '+ error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
			$('.toast').toast('show');
		}
	});
	 
	
		
	$('#tdperfTypes').on('click', '[id*=sDel]', function () {
		var pType = {};
		pType.ID = $(this).attr('data-id');
		pType.Name = $(this).attr('data-name');
		
		bootbox.dialog({
		   title: "Confirm perfume type removal",
		   message : 'Delete <strong>'+ pType.Name +'</strong>?',
		   buttons :{
			   main: {
				   label : "Remove",
				   className : "btn-danger",
				   callback: function (){
						
					$.ajax({ 
						url: '/core/core.php', 
						type: 'POST',
						data: {
							perfType: 'delete',
							pID: pType.ID,
							pName: pType.Name
						},
						dataType: 'json',
						success: function (data) {
							if (data.success) {
								reload_data();
							} else if (data.error) {
								$('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
								$('.toast-header').removeClass().addClass('toast-header alert-danger');
								$('.toast').toast('show');
							}
						},
						error: function (xhr, status, error) {
							$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> An error occurred, check server logs for more info. '+ error);
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
	
	$('#addpType').on('click', '[id*=sAdd]', function () {
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				action: 'perfTypeAdd',
				perfType_name: $("#perfType_name").val(),
				perfType_conc: $("#perfType_conc").val(),
				perfType_desc: $("#perfType_desc").val(),	
			},
			dataType: 'json',
			success: function (data) {
				if(data.success){
					var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
				}else{
					var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
				}
				$('#ptype_inf').html(msg);
				reload_data();
			},
			error: function (xhr, status, error) {
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> An error occurred, check server logs for more info. '+ error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		  });
	});
	
	function reload_data() {
		$('#tdperfTypes').DataTable().ajax.reload(null, true);
	};

});

</script>

<!-- ADD PERFUME TYPE-->
<div class="modal fade" id="addpType" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addpType" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add perfume type</h5>
      </div>
      <div class="modal-body">
      <div id="ptype_inf"></div>
      
          <div class="mb-3">
          	<label for="perfType_name" class="form-label">Name</label>
            <input class="form-control" name="perfType_name" type="text" id="perfType_name" />
          </div>
		  <div class="mb-3">
            <label for="perfType_conc" class="form-label">Concentration</label>
            <input class="form-control" name="perfType_conc" type="text" id="perfType_conc" />
          </div>
		  <div class="mb-3">
            <label for="perfType_desc" class="form-label">Short description</label>
            <input class="form-control" name="perfType_desc" type="text" id="perfType_desc" />
          </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="sAdd" value="Add">
      </div>
    </div>
  </div>
</div>
</div>

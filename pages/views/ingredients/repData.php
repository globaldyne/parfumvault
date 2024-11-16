<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

$ingName = mysqli_real_escape_string($conn, $_GET["id"]);
$ingCAS = mysqli_real_escape_string($conn, $_GET["cas"]);
$ingID = mysqli_real_escape_string($conn, $_GET["ingID"]);

?>
<link href="/css/select2.css" rel="stylesheet">
<script src="/js/select2.js"></script> 

<h3>Possible replacements</h3>
<hr>
<div class="card-body">
 	<div class="text-right mx-2">
  		<div class="btn-group">
   			<button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
    		<div class="dropdown-menu">
        		<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addReplacement"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
    		</div>
  		</div>                    
	</div>
</div>

<table id="tdReplacements" class="table table-striped" style="width:100%">
  <thead>
      <tr>
          <th>Name</th>
          <th>CAS</th>
          <th>Notes</th>
          <th></th>
      </tr>
   </thead>
</table>


<script>
$(document).ready(function() {

	$('[data-bs-toggle="tooltip"]').tooltip();
	var tdReplacements = $('#tdReplacements').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [3]}
		],
		dom: 'lfrtip',
		processing: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No replacements added yet</strong></div></div>',
			search: '',
			searchPlaceholder: 'Search by name...',
		},
		ajax: {	
			url: '/core/list_ing_rep_data.php',
			type: 'POST',
			data: {
				ing_name: '<?=$ingName?>',
				ing_cas: '<?=$ingCAS?>',
				view: 'ingredients',
			},
		},
		columns: [
			{ data : null, title: 'Name', render: repName },
			{ data : 'ing_rep_cas', title: 'CAS' },
			{ data : 'notes', title: 'Notes', render: repNotes },
			{ data : null, title: '', render: repActions },		   
		],
		order: [[ 1, 'asc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
		pageLength: 20,
		displayLength: 20
	});

	
	function repName(data, type, row){
		return row.ing_rep_name;
	};
	
	function repNotes(data, type, row){
		return '<i class="repNotes pv_point_gen" data-name="notes" data-type="textarea" data-pk="'+row.id+'">'+row.notes+'</i>';    
	}
	
	function repActions(data, type, row){
		//return '<a href="#" id="repDel" class="fas fa-trash link-danger" data-id="'+row.id+'" data-name="'+row.ing_rep_name+'"></a>';
		data = '<div class="dropdown">' +
		'<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
			'<ul class="dropdown-menu">';
		data += '<li><a class="dropdown-item text-danger" href="#" id="repDel" rel="tip" title="Delete '+ row.ing_rep_name +'" data-id='+ row.id +' data-name="'+ row.ing_rep_name +'"><i class="fas fa-trash mx-2"></i>Delete</a></li>';
		data += '</ul></div>';
		return data;    
	}
	
	
	$('#tdReplacements').editable({
	   container: 'body',
	   selector: 'i.repNotes',
	   url: "/core/core.php?replacement=update&ing=<?=$ingName;?>",
	   title: 'Notes',
	   ajaxOptions: {
			type: "POST",
			dataType: 'json'
		},
		success: function (data) {
			if ( data.success ) {
				reload_rep_data();
			} else if ( data.error ) {
				$('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		},
		error: function (xhr, status, error) {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
			$('.toast').toast('show');
		}
	});
	
	$('#tdReplacements').on('click', '[id*=repDel]', function () {
		var rep = {};
		rep.ID = $(this).attr('data-id');
		rep.Name = $(this).attr('data-name');
	
		bootbox.dialog({
		   title: "Confirm deletion",
		   message : 'Delete <strong>'+ rep.Name +'</strong>?',
		   buttons :{
			   main: {
				   label : "Delete",
				   className : "btn-danger",
				   callback: function (){
						
					$.ajax({ 
						url: '/core/core.php', 
						type: 'POST',
						data: {
							replacement: 'delete',
							name: rep.Name,
							id: rep.ID,
						},
						dataType: 'json',
						success: function (data) {
							if(data.success){
								$('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
								$('.toast-header').removeClass().addClass('toast-header alert-success');
								reload_rep_data();
							}else{
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
	
	var repCas;
	var repID;
	$("#repName").select2({
		width: '100%',
		placeholder: 'Search for ingredient (name, cas)',
		allowClear: true,
		dropdownAutoWidth: true,
		minimumInputLength: 2,
		dropdownParent: $('#addReplacement .modal-content'),
		ajax: {
			url: '/core/list_ingredients_simple.php',
			dataType: 'json',
			type: 'POST',
			delay: 100,
			quietMillis: 250,
			data: function (data) {
				return {
					search: data
				};
			},
			processResults: function(data) {
				return {
					results: $.map(data.data, function(obj) {
					  return {
						id: obj.name, //TODO: TO BE CHANGED TO ID WHEN THE BACKEND IS READY
						cas: obj.cas,
						ingId: obj.id,
						description: obj.description,
						text: obj.name || 'No ingredient found...',
					  }
					})
				};
			},
			cache: true,
			
		}
		
	}).on('select2:selecting', function (e) {
			 repCas = e.params.args.data.cas;
			 repID = e.params.args.data.ingId;
			 $('#repNotes').html(e.params.args.data.description);
	});
	
	$('#addReplacement').on('click', '[id*=repAdd]', function () {
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				replacement: 'add',
				rName: $("#repName").val(),
				rCAS:  repCas,
				rNotes: $("#repNotes").val(),
				rIngId: repID,
				ing_name: '<?=$ingName?>',
				ing_cas: '<?=$ingCAS?>',
				ing_id: '<?=$ingID?>',
			},
			dataType: 'json',
			success: function (data) {
				if(data.success){
					var msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
					$("#repName").val('');
					$("#repCas").val('');
					$("#repNotes").val('');
					reload_rep_data();
				}else{
					var msg ='<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
				}
				$('#infRep').html(msg);
			},
			error: function (xhr, status, error) {
				$('#infRep').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
			}
		  });
	});
	
	
	function reload_rep_data() {
		$('#tdReplacements').DataTable().ajax.reload(null, true);
	};


});
</script>
<!-- ADD ING REPLACEMENT -->
<div class="modal fade" id="addReplacement" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addReplacementLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addReplacementLabel">Add Replacement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="infRep"></div>
        <div class="mb-3">
          <label for="repName" class="form-label">Ingredient</label>
          <select name="repName" id="repName" class="form-select"></select>
        </div>
        <div class="mb-3">
          <label for="repNotes" class="form-label">Notes</label>
          <textarea name="repNotes" class="form-control" id="repNotes"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="repAdd" value="Add">
      </div>
    </div>
  </div>
</div>

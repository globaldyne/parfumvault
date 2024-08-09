<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

$ingName = mysqli_real_escape_string($conn, $_POST["id"]);
$ingCAS = mysqli_real_escape_string($conn, $_POST["cas"]);
$ingID = mysqli_real_escape_string($conn, $_POST["ingID"]);

?>
<link href="/css/select2.css" rel="stylesheet">
<script src="/js/select2.js"></script> 

<h3>Possible replacements</h3>
<hr>
<div id="infRepOut"></div>
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
		emptyTable: 'No replacements added yet.',
		search: 'Search:'
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
	displayLength: 20,		
	});
});

function repName(data, type, row){
	
	$('#tdReplacements').editable({
		select2: {
			width: '100%',
			placeholder: 'Search for ingredient (name, cas)',
			allowClear: true,
			dropdownAutoWidth: true,
			minimumInputLength: 2,
			dropdownParent: '.popover:last',
			ajax: {
				url: '/core/list_ingredients_simple.php',
				dataType: 'json',
				type: 'POST',
				delay: 100,
				quietMillis: 250,
				data: function (params) {
					return {
						search: params.term
					};
				},
				processResults: function(data) {
					return {
						results: $.map(data.data, function(obj) {
						  return {
							id: obj.name, //TODO: TO BE CHANGED TO ID WHEN THE BACKEND IS READY
							repID: obj.id,
							text: obj.name || 'No ingredient found...',
						  }
						})
					};
				},
				cache: true,
				
			}
		},
		tpl:'<input type="hidden">',
		selector: 'i.replaceIngredient',
		pvnoresp: false,
		highlight: false,
		emptytext: null,
		emptyclass: null,
		url: "update_data.php?replacement=update&ing=<?=$ingName;?>",
		dataType: 'json',
		success: function (data) {
				reload_rep_data();
		},
		validate: function(value){
			if($.trim(value) == ''){
				return 'Ingredient is required';
			}
		}
});
	return '<i class="pv_point_gen replaceIngredient" style="color: #337ab7;" rel="tip" title="Replace '+ row.ing_rep_name +'"  data-name="ing_rep_name" data-type="select2" data-pk="'+ row.id +'" data-title="Choose Ingredient to replace '+ row.ing_rep_name +'">'+ row.ing_rep_name +'</i>'; 
}

function repNotes(data, type, row){
	return '<i class="repNotes pv_point_gen" data-name="notes" data-type="textarea" data-pk="'+row.id+'">'+row.notes+'</i>';    
}

function repActions(data, type, row){
	return '<a href="#" id="repDel" class="fas fa-trash link-danger" data-id="'+row.id+'" data-name="'+row.ing_rep_name+'"></a>';    
}


$('#tdReplacements').editable({
   container: 'body',
   selector: 'i.repNotes',
   type: 'POST',
   url: "update_data.php?replacement=update&ing=<?=$ingName;?>",
   title: 'Notes'
});

$('#tdReplacements').on('click', '[id*=repDel]', function () {
	var rep = {};
	rep.ID = $(this).attr('data-id');
    rep.Name = $(this).attr('data-name');

	bootbox.dialog({
       title: "Confirm removal",
       message : 'Remove <strong>'+ rep.Name +'</strong> from the list?',
       buttons :{
           main: {
               label : "Remove",
               className : "btn-danger",
               callback: function (){
	    			
				$.ajax({ 
					url: 'update_data.php', 
					type: 'POST',
					data: {
						replacement: 'delete',
						name: rep.Name,
						id: rep.ID,
						},
					dataType: 'json',
					success: function (data) {
						if(data.success){
							var msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
							reload_rep_data();
						}else{
							var msg ='<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
						}
						$('#infRepOut').html(msg);
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
		url: 'update_data.php', 
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
		}
	  });
});


function reload_rep_data() {
    $('#tdReplacements').DataTable().ajax.reload(null, true);
};



</script>
<!-- ADD ING REPLACEMENT -->
<div class="modal fade" id="addReplacement" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addReplacement" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add replacement</h5>
      </div>
      <div class="modal-body">
      <div id="infRep"></div>
      <div class="col-sm">
      	<div class="form-row">
        	<div class="col">
            	<label for="repName">Ingredient</label> 
            	<select name="repName" id="repName" class="pv-form-control"></select>
        	</div>
         </div>
         <div class="form-row">
        	<div class="col">
            	<label for="repNotes">Notes</label>
            	<textarea name="repNotes" class="form-control" id="repNotes"></textarea>
            </div>
         </div>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="repAdd" value="Add">
      </div>
    </div>
  </div>
</div>
</div>

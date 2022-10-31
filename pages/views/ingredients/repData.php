<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

$ingName = mysqli_real_escape_string($conn, $_POST["id"]);
$ingCAS = mysqli_real_escape_string($conn, $_POST["cas"]);

?>

<h3>Possible replacements</h3>
<hr>
<div id="infRepOut"></div>
<div class="card-body">
 	<div class="text-right">
  		<div class="btn-group">
   			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
    		<div class="dropdown-menu dropdown-menu-right">
        		<a class="dropdown-item" href="#" data-toggle="modal" data-target="#addReplacement">Add new</a>
    		</div>
  		</div>                    
	</div>
</div>

<table id="tdReplacements" class="table table-striped table-bordered" style="width:100%">
  <thead>
      <tr>
          <th>Name</th>
          <th>CAS</th>
          <th>Notes</th>
          <th>Actions</th>
      </tr>
   </thead>
</table>


<script type="text/javascript" language="javascript" >
$(document).ready(function() {

$('[data-toggle="tooltip"]').tooltip();
var tdReplacements = $('#tdReplacements').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
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
			  { data : 'ing_rep_name', title: 'Name', render: repName },
			  { data : 'ing_rep_cas', title: 'CAS', render: repCAS},
			  { data : 'notes', title: 'Notes', render: repNotes},
			  { data : null, title: 'Actions', render: repActions},		   
			 ],
	order: [[ 1, 'asc' ]],
	lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
	pageLength: 20,
	displayLength: 20,		
	});
});

function repName(data, type, row){
	return '<i class="repName pv_point_gen" data-name="ing_rep_name" data-type="text" data-pk="'+row.id+'">'+row.ing_rep_name+'</i>';    
}

function repCAS(data, type, row){
	return '<i class="repCAS pv_point_gen" data-name="ing_rep_cas" data-type="text" data-pk="'+row.id+'">'+row.ing_rep_cas+'</i>';    
}

function repNotes(data, type, row){
	return '<i class="repNotes pv_point_gen" data-name="notes" data-type="text" data-pk="'+row.id+'">'+row.notes+'</i>';    
}

function repActions(data, type, row){
	return '<a href="#" id="repDel" class="fas fa-trash" style="color: #c9302c;" data-id="'+row.id+'" data-name="'+row.ing_rep_name+'"></a>';    
}

$('#tdReplacements').editable({
	container: 'body',
    selector: 'i.repName',
    type: 'POST',
    url: "update_data.php?replacement=update&ing=<?=$ingName;?>",
    title: 'Name'
});

$('#tdReplacements').editable({
   container: 'body',
   selector: 'i.repCAS',
   type: 'POST',
   url: "update_data.php?replacement=update&ing=<?=$ingName;?>",
   title: 'CAS'
});

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
							var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
							reload_rep_data();
						}else{
							var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
						}
						$('#infRepOut').html(msg);
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



$('#addReplacement').on('click', '[id*=repAdd]', function () {
	$.ajax({ 
		url: 'update_data.php', 
		type: 'POST',
		data: {
			replacement: 'add',
			rName: $("#repName").val(),
			rCAS: $("#repCas").val(),
			rNotes: $("#repNotes").val(),
			ing_name: '<?=$ingName?>',
			ing_cas: '<?=$ingCAS?>',
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				$("#repName").val('');
				$("#repCas").val('');
				$("#repNotes").val('');
				reload_rep_data();
			}else{
				var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
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
<div class="modal fade" id="addReplacement" tabindex="-1" role="dialog" aria-labelledby="addReplacement" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add replacement</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="infRep"></div>
            Name: 
            <input class="form-control" name="repName" type="text" id="repName" />
        <p>
            CAS: 
            <input class="form-control" name="repCas" type="text" id="repCas" />
            </p>
            Notes: 
            <textarea name="repNotes" class="form-control" id="repNotes"></textarea>
            </p>
        <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="repAdd" value="Add">
      </div>
    </div>
  </div>
</div>
</div>

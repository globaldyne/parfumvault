<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

$CAS = mysqli_real_escape_string($conn, $_GET["cas"]);
$ingName = mysqli_real_escape_string($conn, $_GET["name"]);

?>

<h3>Synonyms</h3>
<hr>
<div class="card-body">
 	<div class="text-right">
  		<div class="btn-group">
   			<button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
    		<div class="dropdown-menu">
        		<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addSynonym"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
                <?php if(preg_match('/(Mixture|Blend)/i', $CAS) === 0){	?>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#pubchem_import"><i class="fa-solid fa-file-import mx-2"></i>Import from PubChem</a></li>

                <?php } ?>
    		</div>
  		</div>                    
	</div>
</div>

<table id="tdSynonyms" class="table table-striped" style="width:100%">
  <thead>
      <tr>
          <th>Synonym</th>
          <th>Source</th>
          <th></th>
      </tr>
   </thead>
</table>


<script>
$(document).ready(function() {
	$.fn.dataTable.ext.errMode = 'none';

	var tdSynonyms = $('#tdSynonyms').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [2]}
		],
		dom: 'lfrtip',
		processing: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No synonyms added yet</strong></div></div>',
			search: '',
			searchPlaceholder: 'Search by name...',
		},
		ajax: {	url: '/core/list_ing_synonyms_data.php?id=<?=$ingName?>' },
		columns: [
			  { data : 'synonym', title: 'Synonym', render: synName },
			  { data : 'source', title: 'Source', render: synSource},
			  { data : null, title: '', render: synActions},		   
		],
		order: [[ 1, 'asc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
		pageLength: 20,
		displayLength: 20,
		scrollCollapse: false,
    	scrollY: '500px'
	}).on('error.dt', function(e, settings, techNote, message) {
		var m = message.split(' - ');
		$('#tdSynonyms').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
	});


	function synName(data, type, row){
		return '<i class="synonym pv_point_gen" data-name="synonym" data-type="text" data-pk="'+row.id+'">'+row.synonym+'</i>';    
	};

	function synSource(data, type, row){
		return '<i class="source pv_point_gen" data-name="source" data-type="text" data-pk="'+row.id+'">'+row.source+'</i>';    
	};


	function synActions(data, type, row){
		data = '<div class="dropdown">' +
		'<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
			'<ul class="dropdown-menu">';
		data += '<li><a class="dropdown-item text-danger" href="#" id="synDel" data-name="'+row.synonym+'" data-id="'+row.id+'" ><i class="fas fa-trash mx-2"></i>Delete</a></li>';
		data += '</ul></div>';
		return data;
	};

	$('#tdSynonyms').editable({
		container: 'body',
		selector: 'i.synonym',
		ajaxOptions: {
			type: "POST",
			dataType: 'json'
		},
		success: function (data) {
			if ( data.success ) {
				reload_data();
			} else if ( data.error ) {
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
		url: "/core/core.php?synonym=update&ing=<?=$ingName;?>",
		title: 'Synonym'
	});

	$('#tdSynonyms').editable({
	   container: 'body',
	   selector: 'i.source',
	   ajaxOptions: {
			type: "POST",
			dataType: 'json'
		},
		success: function (data) {
			if ( data.success ) {
				reload_data();
			} else if ( data.error ) {
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
	    url: "/core/core.php?synonym=update&ing=<?=$ingName;?>",
	    title: 'Source'
	});


	$('#tdSynonyms').on('click', '[id*=synDel]', function () {
		var syn = {};
		syn.ID = $(this).attr('data-id');
		syn.Name = $(this).attr('data-name');
	
		bootbox.dialog({
		   title: "Confirm delete",
		   message : 'Delete <strong>'+ syn.Name +'</strong> from the list?',
		   buttons :{
			   main: {
				   label : "Delete",
				   className : "btn-danger",
				   callback: function (){
				      $.ajax({ 
						url: '/core/core.php', 
						type: 'GET',
						data: {
							synonym: 'delete',
							id: syn.ID,
						},
						dataType: 'html',
						success: function (data) {
							reload_data();
						},
						error: function (xhr, status, error) {
							$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error);
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


	$('#import').on('click', '[id*=importPubChem]', function () {
		$('#importPubChem').attr('disabled', true);
		$('#pvImportMsg').html('<div class="alert alert-info">Please wait...</div>');			
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				synonym: 'import',
				method: 'pubchem',
				ing: '<?=$ingName?>',
				cas: '<?=$CAS?>',
			},
			dataType: 'json',
			success: function (data) {
				if (data.success) {
					var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><i class="fa-solid fa-check mx-2"></i>' + data.success + '</div>';
					reload_data();
					reload_overview();
				}else{
					var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
				}
				$('#pvImportMsg').html(msg);
				$('#importPubChem').attr('disabled', false);
			},
			error: function (xhr, status, error) {
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		});		
				 
	});
	
	$('#addSynonym').on('click', '[id*=synAdd]', function () {
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				synonym: 'add',
				sName: $("#synonym").val(),
				source: $("#source").val(),
				ing: '<?=$ingName?>'
			},
			dataType: 'json',
			success: function (data) {
				if (data.success) {
					var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><i class="fa-solid fa-check mx-2"></i>' + data.success + '</div>';
					$("#synonym").val('');
					$("#source").val('');
					reload_data();
				}else{
					var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
				}
				$('#infSyn').html(msg);				
			},
			error: function (xhr, status, error) {
				$('#infSyn').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error +'</div>');
			}
		  });
	});
	
	
	function reload_data() {
		$('#tdSynonyms').DataTable().ajax.reload(null, true);
	};
	
});
</script>
<!-- ADD SYNONYM -->
<div class="modal fade" id="addSynonym" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addSynonymLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSynonymLabel">Add Synonym</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="infSyn"></div>
        <div class="mb-3">
          <label for="synonym" class="form-label">Name</label>
          <input class="form-control" name="synonym" type="text" id="synonym" />
        </div>
        <div class="mb-3">
          <label for="source" class="form-label">Source</label>
          <input class="form-control" name="source" type="text" id="source" />
        </div>
        <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="synAdd" value="Add">
      </div>
    </div>
  </div>
</div>


<!-- PUBCHEM SYNONYM IMPORT -->
<div class="modal fade" id="pubchem_import" data-bs-backdrop="static" tabindex="-1" aria-labelledby="pubchemImportLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pubchemImportLabel">Import Synonyms from PubChem</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="pvImportMsg"></div>
        <strong>WARNING:</strong><br />
        You are about to import data from PubChem.com. If your local database already contains the same data, new data will not be imported.
        <p></p>
        <div class="dropdown-divider"></div>
        For more info regarding PubChem REST API, please refer to its documentation <a href="https://pubchemdocs.ncbi.nlm.nih.gov/about" target="_blank">here.</a>
      </div>
      <div class="modal-footer" id="import">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="importPubChem" value="Import">
      </div>
    </div>
  </div>
</div>

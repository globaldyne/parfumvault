<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

$ingID = mysqli_real_escape_string($conn, $_GET["id"]);
?>

<h3>Documents</h3>
<hr>
<div class="card-body">
  <div class="text-right">
    <div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
        <div class="dropdown-menu">
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addDoc"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
        </div>
    </div>                    
  </div>


</div>
<table id="tdIngDocs" class="table table-striped" style="width:100%">
  <thead>
      <tr>
          <th>Document</th>
          <th>File</th>
          <th>Notes</th>
          <th>Size</th>
          <th></th>
      </tr>
   </thead>
</table>
<script>
$(document).ready(function() {
	
	$('[data-bs-toggle="tooltip"]').tooltip();
	var tdIngDocs = $('#tdIngDocs').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [4]}
		],
		dom: 'lfrtip',
		processing: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: 'No documents added yet.',
			search: '',
			searchPlaceholder: 'Search by name...',
		},
		ajax: {	url: '/core/list_ing_doc_data.php?id=<?=$ingID?>' },
		columns: [
				  { data : 'name', title: 'Document', render: dName },
				  { data : 'docData', title: 'File', render: docData},
				  { data : 'notes', title: 'Notes', render: dNotes},
				  { data : 'docSize', title: 'Size'},
				  { data : null, title: '', render: dActions},		   
		],
		order: [[ 1, 'asc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
		pageLength: 20,
		displayLength: 20,
		scrollCollapse: false,
    	scrollY: '500px'
	});
	
	function dName(data, type, row){
		return '<i class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</i>';    
	};
	
	function docData(data, type, row){
		return '<a href="/pages/viewDoc.php?id='+row.id+'" target="_blank" class="fa fa-file-alt"></a>';    
	};
	
	function dNotes(data, type, row){
		return '<i class="notes pv_point_gen" data-name="notes" data-type="textarea" data-pk="'+row.id+'">'+row.notes+'</i>'  
	};
	
	function dActions(data, type, row){
		data = '<div class="dropdown">' +
		'<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
			'<ul class="dropdown-menu">';
		data += '<li><a class="dropdown-item text-danger" href="#" id="dDel" rel="tip" title="Remove '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-trash mx-2"></i>Remove</a></li>';
		data += '</ul></div>';
		return data;
	};

	$('#tdIngDocs').editable({
		container: 'body',
	  	selector: 'i.name',
	  	type: 'POST',
	  	url: "/pages/update_data.php?ingDoc=update&ingID=<?=$ingID;?>",
	  	title: 'Document name',
 	});
  
	$('#tdIngDocs').editable({
	  container: 'body',
	  selector: 'i.notes',
	  type: 'POST',
	  url: "/pages/update_data.php?ingDoc=update&ingID=<?=$ingID;?>",
	  title: 'Notes',
	});

	
	$('#tdIngDocs').on('click', '[id*=dDel]', function () {
		var d = {};
		d.ID = $(this).attr('data-id');
		d.Name = $(this).attr('data-name');
	
		bootbox.dialog({
		   title: "Confirm document removal",
		   message : 'Remove <strong>'+ d.Name +'</strong> from the list?',
		   buttons :{
			   main: {
				   label : "Remove",
				   className : "btn-danger",
				   callback: function (){
						
					$.ajax({ 
						url: '/pages/update_data.php', 
						type: 'GET',
						data: {
							doc: 'delete',
							id: d.ID,
							ownerID: '<?=$ingID?>'
						},
						dataType: 'html',
						success: function (data) {
							$('#msg_doc').html(data);
							reload_doc_data();
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

	$('#addDoc').on('click', '[id*=doc_upload]', function () {
			
		$("#doc_inf").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
		$("#doc_upload").prop("disabled", true);
		$("#doc_upload").prop('value', 'Please wait...');
			
		var fd = new FormData();
		var files = $('#doc_file')[0].files;
		var doc_name = $('#doc_name').val();
		var doc_notes = $('#doc_notes').val();
	
		if(files.length > 0 ){
			fd.append('doc_file',files[0]);
	
				$.ajax({
				  url: '/pages/upload.php?type=1&doc_name=' + btoa(doc_name) + '&doc_notes=' + btoa(doc_notes) + '&id=<?=$ingID;?>',
				  type: 'POST',
				  data: fd,
				  dataType: 'json',
				  contentType: false,
				  processData: false,
						cache: false,
				  success: function(response){
					 if(response.success){
						var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>' + response.success + '</strong></div>';
						$("#doc_upload").prop("disabled", false);
						$("#doc_upload").prop('value', 'Upload');
						reload_doc_data();
					 }else{
						$("#doc_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + response.error + '</div>');
						$("#doc_upload").prop("disabled", false);
						$("#doc_upload").prop('value', 'Upload');
					 }
					 $('#doc_inf').html(msg);
				  },
			   });
			}else{
				$("#doc_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a file to upload!</div>');
				$("#doc_upload").prop("disabled", false);
				$("#doc_upload").prop('value', 'Upload');
			}
			
	});

	function reload_doc_data() {
		$('#tdIngDocs').DataTable().ajax.reload(null, true);
	};
	
});

</script>


<!-- ADD DOCUMENT-->
<div class="modal fade" id="addDoc" tabindex="-1" data-bs-backdrop="static" aria-labelledby="addDocLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addDocLabel">Add Document</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="doc_inf"></div>
        <div class="mb-2">
          <label for="doc_name" class="form-label">Document Name</label>
          <input class="form-control" name="doc_name" type="text" id="doc_name" />
        </div>
        <div class="mb-2">
          <label for="doc_notes" class="form-label">Notes</label>
          <textarea class="form-control" name="doc_notes" id="doc_notes"></textarea>
        </div>
        <div class="mb-2">
          <label for="doc_file" class="form-label">File</label>
          <input type="file" name="doc_file" id="doc_file" class="form-control" />
        </div>
        <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="doc_upload" value="Upload">
      </div>
    </div>
  </div>
</div>

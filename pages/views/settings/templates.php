<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');

?>

<h3>HTML Templates</h3>
<hr>
<div class="card-body">
  <div class="text-right">
    <div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
        <div class="dropdown-menu">
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addTmpl"><i class="fa-solid fa-plus mx-2"></i>Add new template</a></li>
        </div>
    </div>                    
  </div>
</div>
<table id="tdTempls" class="table table-striped" style="width:100%">
  <thead>
      <tr>
          <th>Name</th>
          <th>Description</th>
          <th>Created</th>
          <th></th>
      </tr>
   </thead>
</table>
<script>
$(document).ready(function() {
	$.fn.dataTable.ext.errMode = 'none';

	var tdTempls = $('#tdTempls').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [3] }
		],
		dom: 'lfrtip',
		processing: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No templates added yet</strong></div></div>',
			zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
			search: '',
			searchPlaceholder: 'Search by name...',
		},
		ajax: {	url: '/core/list_templates_data.php' },
		columns: [
				  { data : 'name', title: 'Name', render: name },
				  { data : 'description', title: 'Description', render: description},
				  { data : 'created', title: 'Created', render: formatDate},
				  { data : null, title: '', render: actions},		   
		],
		order: [[ 1, 'asc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
		pageLength: 20,
		displayLength: 20,
		drawCallback: function( settings ) {
				extrasShow();
		},
		stateSave: true,
		stateDuration: -1,
		stateLoadCallback: function (settings, callback) {
			$.ajax( {
				url: '/core/update_user_settings.php?set=listHTMLTmpl&action=load',
				dataType: 'json',
				success: function (json) {
					callback( json );
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			 url: "/core/update_user_settings.php?set=listHTMLTmpl&action=save",
			 data: data,
			 dataType: "json",
			 type: "POST"
		  });
		},	
	}).on('error.dt', function(e, settings, techNote, message) {
		var m = message.split(' - ');
		$('#tdTempls').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
    });


	function name(data, type, row){
		return '<a href="#" class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</a>';    
	};
	
	function description(data, type, row){
		return '<a href="#" class="description pv_point_gen" data-name="description" data-type="textarea" data-pk="'+row.id+'">'+row.description+'</a>';    
	};
	
	function formatDate(data, type) {
	  if (type === 'display') {
		if (data === '0000-00-00 00:00:00') {
		  return '-';
		}
		
		try {
		  const [year, month, day] = data.split(/[- :]/).map(Number);
		  const dateObject = new Date(year, month - 1, day);
	
		  return dateObject.toLocaleDateString();
		} catch (error) {
		  console.error("Date parsing error:", error);
		  return data; // Return original data if parsing fails
		}
	  }
	
	  return data;
	}

	function actions(data, type, row){	
			data = '<div class="dropdown">' +
			'<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
				'<ul class="dropdown-menu">';
			data += '<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editTmpl" rel="tip" title="Edit '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-edit mx-2"></i>Edit</a></li>';
			data += '<div class="dropdown-divider"></div>';
			data += '<li><a class="dropdown-item text-danger" href="#" id="sDel" rel="tip" title="Delete '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-trash mx-2"></i>Delete</a></li>';
			data += '</ul></div>';
		return data;
	};
	
	
	$('#tdTempls').editable({
		container: 'body',
		selector: 'a.name',
		url: "/core/core.php?action=htmlTmplUpdate",
		title: 'Template name',
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
	  
	
	$('#tdTempls').editable({
		container: 'body',
		selector: 'a.description',
		ajaxOptions: {
			type: "POST",
			dataType: 'json'
		},
		url: "/core/core.php?action=htmlTmplUpdate",
		title: 'Short description',
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
	 
	
	$('#tdTempls').on('click', '[id*=sDel]', function () {
		var tmpl = {};
		tmpl.ID = $(this).attr('data-id');
		tmpl.Name = $(this).attr('data-name');
		
		bootbox.dialog({
		   title: "Confirm template deletion",
		   message : 'Delete <strong>'+ tmpl.Name +'</strong>?',
		   buttons :{
			   main: {
				   label : "Delete",
				   className : "btn-danger",
				   callback: function (){
					$.ajax({ 
						url: '/core/core.php', 
						type: 'POST',
						data: {
							action: 'htmlTmplDelete',
							tmplID: tmpl.ID,
							tmplName: tmpl.Name
						},
						dataType: 'json',
						success: function (data) {
							reload_data();
						},
						error: function (xhr, status, error) {
							$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
							$('.toast-header').removeClass().addClass('toast-header alert-danger');
							$('.toast').toast('show');
						},
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
	
	$('#addTmpl').on('click', '[id*=sAdd]', function () {
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				action: 'htmlTmplAdd',
				tmpl_name: $("#tmpl_name").val(),
				tmpl_content: $("#tmpl_content").val(),
				tmpl_desc: $("#tmpl_desc").val(),	
				},
			dataType: 'json',
			success: function (data) {
				if(data.success){
					var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
					reload_data();
				}else{
					var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
				}
				$('#tmpl_inf').html(msg);
			},
			error: function (xhr, status, error) {
				$('#tmpl_inf').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error +'</div>');
			}
		  });
	});
	
	function reload_data() {
		$('#tdTempls').DataTable().ajax.reload(null, true);
	};
	
	function extrasShow() {
		$('[rel=tip]').tooltip({
			 html: true,
			 boundary: "window",
			 overflow: "auto",
			 container: "body",
			 delay: {"show": 100, "hide": 0},
		 });
	};
	
	$("#editTmpl").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const name = e.relatedTarget.dataset.name;
	
		$.get("/pages/views/settings/editHtmlTmpl.php?id=" + id)
			.then(data => {
			$("#editTmplLabel", this).html(name);
			$(".modal-body", this).html(data);
		});
	});

});
</script>

<!-- ADD TEMPLATE -->
<div class="modal fade" id="addTmpl" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addTmplLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add new template</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="tmpl_inf"></div>

        <div class="mb-3">
          <label for="tmpl_name" class="form-label">Name</label>
          <input class="form-control" name="tmpl_name" type="text" id="tmpl_name" />
        </div>
        <div class="mb-3">
          <label for="tmpl_content" class="form-label">HTML Content</label>
          <textarea class="form-control" name="tmpl_content" id="tmpl_content" rows="4"></textarea>
        </div>
        <div class="mb-3">
          <label for="tmpl_desc" class="form-label">Short description</label>
          <input class="form-control" name="tmpl_desc" type="text" id="tmpl_desc" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="sAdd" value="Add">
      </div>
    </div>
  </div>
</div>

<!--EDIT MODAL-->            
<div class="modal fade" id="editTmpl" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editTmplLabel" aria-hidden="true">
  <div class="modal-dialog pv-modal-xxl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="editTmplLabel">Edit template</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
      </div>
    </div>
  </div>
</div>

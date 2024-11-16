<?php 

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
?>
<h3>Formula categories</h3>
<hr>
<div class="card-body">
  <div class="text-right">
    <div class="btn-group" id="menu">
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mr2"></i>Actions</button>
        <div class="dropdown-menu">
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#add_formula_cat"><i class="fa-solid fa-plus mx-2"></i>Add formula category</a></li>
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#import_categories_json"><i class="fa-solid fa-file-import mx-2"></i>Import from JSON</a></li>
          <li><a class="dropdown-item" href="/core/core.php?action=exportFrmCat"><i class="fa-solid fa-file-code mx-2"></i>Export as JSON</a></li>
        </div>
    </div>
	</div>
</div>
    <div class="card-body">
    <div id="fcatMsg"></div>
    <table id="frmDataCat" class="table table-striped" style="width:100%">
      <thead>
        <tr>
          <th>Name</th>
          <th>Type</th>
          <th>Colour</th>
          <th></th>
        </tr>
      </thead>
    </table>
</div>
 
<script>
$(document).ready(function() {
	var frmDataCat = $('#frmDataCat').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [2,3] }
        ],
		dom: 'lfrtip',
		processing: true,
        language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw">',
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No categories added yet</strong></div></div>',
			zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
			search: '',
			searchPlaceholder: 'Search by name...',
		},
    	ajax: {	url: '/core/list_frmCat_data.php' },
		columns: [
			{ data : 'name', title: 'Name', render: cName },
    		{ data : 'type', title: 'Type', render: cType},
    		{ data : 'colorKey', title: 'Colour Key', render: fKey},
   			{ data : null, title: '', render: cActions},		   
		],
        order: [[ 1, 'asc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
        pageLength: 20,
		displayLength: 20,
		stateSave: true,
		stateDuration: -1,
		stateLoadCallback: function (settings, callback) {
			$.ajax( {
				url: '/core/update_user_settings.php?set=listFormulaCat&action=load',
				dataType: 'json',
				success: function (json) {
					callback( json );
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			 url: "/core/update_user_settings.php?set=listFormulaCat&action=save",
			 data: data,
			 dataType: "json",
			 type: "POST"
		  });
		},
	});



	function cName(data, type, row){
		return '<a class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</a>';    
	};
	
	function cType(data, type, row){
		return '<a class="type pv_point_gen" data-name="type" data-type="select" data-pk="'+row.id+'">'+row.type+'</a>';    
	};
	
	function cActions(data, type, row){
		data = '<div class="dropdown">' +
		'<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
			'<ul class="dropdown-menu">';
		data += '<li><a class="dropdown-item text-danger" href="#" id="catDel" rel="tip" title="Delete '+ row.name +'" data-id='+ row.id +' data-name="'+ row.name +'"><i class="fas fa-trash mx-2"></i>Delete</a></li>';
		data += '</ul></div>';
		return data;
	};
	
	function fKey(data, type, row){
		return '<a href="#" class="colorKey" style="background-color: '+row.colorKey+'" id="colorKey" data-name="colorKey" data-type="select" data-pk="'+row.id+'" data-title="Choose Colour Key for '+row.name+'"></a>';    
	};
	
	$('#add-fcat').click(function() {
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				manage: 'add_frmcategory',
				category: $("#fcatName").val(),
				cat_type: $("#cat_type").val(),
			},
			dataType: 'json',
			success: function (data) {
				if ( data.success ) {
					$('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
					$('.toast-header').removeClass().addClass('toast-header alert-success');
					reload_data();
					$('#add_formula_cat').modal('toggle');
					$('.toast').toast('show');
				} else {
					$('#fcatMsgIn').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>'+data.error+'</div>');
				}
			},
			error: function (xhr, status, error) {
				$('#fcatMsgIn').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
			}
		});
	});
	
	
	$('#frmDataCat').editable({
		container: 'body',
	  	selector: 'a.name',
	  	url: "/core/core.php?settings=fcat&action=updateFormulaCategory",
	  	title: 'Category name',
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
	 
	
	//Change type
	$('#frmDataCat').editable({
		pvnoresp: false,
		highlight: false,
		title: 'Category type',
		selector: 'a.type',
		emptytext: "",
		emptyclass: "",
		url: "/core/core.php?settings=fcat&action=updateFormulaCategory",
		source: [
        	{value: 'gender', text: 'Gender'},
            {value: 'profile', text: 'Profile'},
		],
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
	
	//Change colorKey
	$('#frmDataCat').editable({
		pvnoresp: false,
		highlight: false,
		selector: 'a.colorKey',
		emptytext: "",
		emptyclass: "",
		url: "/core/core.php?settings=fcat&action=updateFormulaCategory",
		source: [
			<?php
			$getCK = mysqli_query($conn, "SELECT name,rgb FROM colorKey ORDER BY name ASC");
			while ($r = mysqli_fetch_array($getCK)){
				echo '{value: "'.$r['rgb'].'", text: "'.$r['name'].'", ck: "color: rgb('.$r['rgb'].')"},';
			}
			?>
		],
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
		}
	});
		
	$('#frmDataCat').on('click', '[id*=catDel]', function () {
		var cat = {};
		cat.ID = $(this).attr('data-id');
		cat.Name = $(this).attr('data-name');
		
		bootbox.dialog({
		   title: "Confirm category deletion",
		   message : 'Delete <strong>'+ $(this).attr('data-name') +'</strong> category?',
		   buttons :{
			   main: {
				   label : "Delete",
				   className : "btn-danger",
				   callback: function (){
					$.ajax({ 
						url: '/core/core.php', 
						type: 'POST',
						data: {
							action: "del_frmcategory",
							catId: cat.ID,
						},
						dataType: 'json',
						success: function (data) {
							if ( data.success ) {
								$('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
								$('.toast-header').removeClass().addClass('toast-header alert-success');
								reload_data();
							} else {
								$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error);
								$('.toast-header').removeClass().addClass('toast-header alert-danger');
							}
							$('.toast').toast('show');
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
	
	function reload_data() {
		$('#frmDataCat').DataTable().ajax.reload(null, true);
	};

});

</script>

<!--ADD CATEGORY MODAL-->
<div class="modal fade" id="add_formula_cat" data-bs-backdrop="static" tabindex="-1" aria-labelledby="add_formula_cat_label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add new category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <div id="fcatMsgIn"></div>
        <div class="mb-3">
          <label for="fcatName" class="form-label">Name</label>
          <input name="fcatName" id="fcatName" type="text" class="form-control" />
        </div>
        <div class="mb-3">
          <label for="cat_type" class="form-label">Type</label>
          <select name="cat_type" id="cat_type" class="form-select">
            <option value="profile">Profile</option>
            <option value="gender">Gender</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close_cat">Close</button>
        <button type="submit" name="add-fcat" class="btn btn-primary" id="add-fcat">Create</button>
      </div>
    </div>
  </div>
</div>

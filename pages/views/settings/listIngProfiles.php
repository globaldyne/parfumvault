<?php 

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
?>
<h3>Ingredient Profiles</h3>
<hr>
<div class="card-body">
  <div class="text-right">
    <div class="btn-group" id="menu">
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mr2"></i>Actions</button>
        <div class="dropdown-menu">
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#add_ingredient_prof"><i class="fa-solid fa-plus mx-2"></i>Add ingredient profile</a></li>
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#import_categories_json"><i class="fa-solid fa-file-import mx-2"></i>Import from JSON</a></li>
          <li><a class="dropdown-item" href="/pages/operations.php?action=exportIngProf"><i class="fa-solid fa-file-code mx-2"></i>Export as JSON</a></li>
        </div>
    </div>
	</div>
</div>
    <div class="card-body">
    <table id="ingDataProf" class="table table-striped" style="width:100%">
      <thead>
        <tr>
          <th>Name</th>
          <th>Description</th>
          <th>Image</th>
          <th></th>
        </tr>
      </thead>
    </table>
</div>
 
<script>
$(document).ready(function() {
		var ingDataProf = $('#ingDataProf').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [2,3] }
        ],
		dom: 'lfrtip',
		processing: true,
        language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw">',
			emptyTable: 'No groups yet.',
			search: 'Search:'
		},
    	ajax: {	url: '/core/list_ingProf_data.php' },
		columns: [
			{ data : 'name', title: 'Name', render: name },
    		{ data : 'description', title: 'Description', render: description},
    		{ data : 'image', title: 'Image', render: image},
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
				url: '/core/update_user_settings.php?set=listIngProf&action=load',
				dataType: 'json',
				success: function (json) {
					callback( json );
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			 url: "/core/update_user_settings.php?set=listIngProf&action=save",
			 data: data,
			 dataType: "json",
			 type: "POST"
		  });
		},
	});



	function name(data, type, row){
		return '<a class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</a>';    
	};
	
	function description(data, type, row){
		return '<a class="notes pv_point_gen" data-name="notes" data-type="text" data-pk="'+row.id+'">'+row.notes+'</a>';    
	};
	
	function image(data, type, row){
		if(row.image){
			var cimg = '<img src="' + row.image + '" class="img_ing">';
		}else{
			var cimg = '<img src="/img/molecule.png" class="img_ing">';
		}
		
		return '<a href="#" data-id="'+row.id+'" data-bs-toggle="modal" data-bs-target="#editProfile">' + cimg + '</a>';    
	};
	
	function actions(data, type, row){
		return '<i id="profDel" class="pv_point_gen fas fa-trash link-danger" data-id="'+row.id+'" data-name="'+row.name+'"></i>';    
	};
	

	$("#editProfile").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const name = e.relatedTarget.dataset.name;
	
		$.get("/pages/views/settings/editProfile.php?id=" + id)
			.then(data => {
			$("#editProfileLabel", this).html(name);
			$(".modal-body", this).html(data);
		});
	});
	
	$('#add-prof').click(function() {
		$.ajax({ 
			url: '/pages/update_settings.php', 
				type: 'POST',
				data: {
					manage: 'add_ingprof',
					profile: $("#profName").val(),
					description: $("#description").val(),
				},
				dataType: 'json',
				success: function (data) {
					if ( data.success ) {
						$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
						$('.toast-header').removeClass().addClass('toast-header alert-success');
						reload_data();
						$('#add_ingredient_prof').modal('toggle');
						$('.toast').toast('show');
					} else {
						var msg = '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mr-2"></i>'+data.error+'</div>';
						$('#profMsgIn').html(msg);
					}
					
				}
			});
	});
	
	
	
	$('#ingDataProf').editable({
	  	container: 'body',
	  	selector: 'a.name',
	  	url: "/pages/update_data.php?settings=prof",
	  	title: 'Profile name',
	  	type: "POST",
	  	dataType: 'json',
	  	validate: function(value){
	   		if($.trim(value) == ''){
				return 'This field is required';
	   		}
	  	}
	});
	
	$('#ingDataProf').editable({
		container: 'body',
	  	selector: 'a.notes',
	  	url: "/pages/update_data.php?settings=prof",
	  	title: 'Profile description',
	  	type: "POST",
	  	dataType: 'json',
	  	validate: function(value){
	   		if($.trim(value) == ''){
				return 'This field is required';
	   		}
	  	}
	});	
		
	$('#ingDataProf').on('click', '[id*=profDel]', function () {
		var prof = {};
		prof.ID = $(this).attr('data-id');
		prof.Name = $(this).attr('data-name');
		
		bootbox.dialog({
		   title: "Confirm profile deletion",
		   message : 'Delete <strong>'+ $(this).attr('data-name') +'</strong> Profile?',
		   buttons :{
			   main: {
				   label : "Delete",
				   className : "btn-danger",
				   callback: function (){
						
					$.ajax({ 
						url: '/pages/update_settings.php', 
						type: 'POST',
						data: {
							action: "ingProfile",
							profId: prof.ID,
						},
						dataType: 'json',
						success: function (data) {
							if ( data.success ) {
								$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
								$('.toast-header').removeClass().addClass('toast-header alert-success');
								reload_data();
							} else {
								$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error);
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
	
	function reload_data() {
		$('#ingDataProf').DataTable().ajax.reload(null, true);
	};

});


</script>
<!--ADD PROFILE MODAL-->
<div class="modal fade" id="add_ingredient_prof" data-bs-backdrop="static" tabindex="-1" aria-labelledby="add_ingredient_prof_label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add new profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <div id="profMsgIn"></div>
        <div class="mb-3">
          <label for="profName" class="form-label">Name</label>
          <input name="profName" id="profName" type="text" class="form-control" />
        </div>
        <div class="mb-3">
          <label for="description" class="form-label">Description</label>
          <input name="description" id="description" type="text" class="form-control" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close_prof">Close</button>
        <button type="submit" name="add-prof" class="btn btn-primary" id="add-prof">Create</button>
      </div>
    </div>
  </div>
</div>


<!--EDIT MODAL-->            
<div class="modal fade" id="editProfile" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editProfileLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="editProfileLabel">Edit profile</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
      </div>
    </div>
  </div>
</div>

<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');

?>
<h3>Perfume types</h3>
<hr>
<div class="card-body">
  <div class="text-right">
    <div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mr2"></i>Actions</button>
        <div class="dropdown-menu dropdown-menu-right">
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addpType"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
          <li><a class="dropdown-item" href="/pages/operations.php?action=exportPerfTypes"><i class="fa-solid fa-file-code mx-2"></i>Export as JSON</a></li>
        </div>
    </div>                    
  </div>
</div>
<table id="tdperfTypes" class="table table-striped table-bordered" style="width:100%">
  <thead>
      <tr>
          <th>Name</th>
          <th>Concentration</th>
          <th>Description</th>
          <th></th>
      </tr>
   </thead>
</table>
<script type="text/javascript" language="javascript" >
$(document).ready(function() {
		
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
		emptyTable: 'No perfume types found.',
		search: 'Search:'
		},
	ajax: {	url: '/core/list_perfume_types_data.php' },
	columns: [
			  { data : 'name', title: 'Name', render: name },
			  { data : 'concentration', title: 'Concentration', render: concentration},
			  { data : 'description', title: 'Description', render: description},
			  { data : null, title: 'Actions', render: actions},		   
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
	});
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
	return '<a href="#" id="sDel" class="fas fa-trash link-danger" data-id="'+row.id+'" data-name="'+row.name+'"></a>';
};


$('#tdperfTypes').editable({
	  container: 'body',
	  selector: 'a.name',
	  type: 'POST',
	  url: "/pages/update_data.php?perfType=update",
	  title: 'Perfume type name',
	  success: function (data) {
			reload_data();
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
	  type: 'POST',
	  url: "/pages/update_data.php?perfType=update",
	  title: 'Concentration in %',
	  success: function (data) {
			reload_data();
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
  	type: 'POST',
	url: "/pages/update_data.php?perfType=update",
	title: 'Short description',
	success: function (data) {
			reload_data();
	},
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
					url: '/pages/update_data.php', 
					type: 'POST',
					data: {
						perfType: 'delete',
						pID: pType.ID,
						pName: pType.Name
						},
					dataType: 'json',
					success: function (data) {
						reload_data();
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
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			perfType: 'add',
			perfType_name: $("#perfType_name").val(),
			perfType_conc: $("#perfType_conc").val(),
			perfType_desc: $("#perfType_desc").val(),	
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
			}else{
				var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
			$('#ptype_inf').html(msg);
			reload_data();
		}
	  });
});

function reload_data() {
    $('#tdperfTypes').DataTable().ajax.reload(null, true);
};
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
          	<label for="formFile" class="form-label">Name</label>
            <input class="form-control" name="perfType_name" type="text" id="perfType_name" />
          </div>
		  <div class="mb-3">
            <label for="formFile" class="form-label">Concentration</label>
            <input class="form-control" name="perfType_conc" type="text" id="perfType_conc" />
          </div>
		  <div class="mb-3">
            <label for="formFile" class="form-label">Short description</label>
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




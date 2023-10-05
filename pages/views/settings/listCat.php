<?php 

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

?>
<h3>Ingredient Categories</h3>
<hr>
<div class="card-body">
  <div class="text-right">
    <div class="btn-group" id="menu">
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mr2"></i>Actions</button>
        <div class="dropdown-menu dropdown-menu-right">
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#add_ingredient_cat"><i class="fa-solid fa-plus mx-2"></i>Add ingredient category</a></li>
        </div>
    </div>
  </div>
</div>
<div class="card-body">
   <div id="catMsg"></div>
	<table id="tdDataCat" class="table table-striped table-bordered nowrap" style="width:100%">
      <thead>
        <tr>
          <th>Image</th>
          <th>Colour Key</th>
          <th>Name</th>
          <th>Description</th>
          <th></th>
        </tr>
      </thead>
    </table>
</div>
                
<script type="text/javascript" language="javascript" >
$(document).ready(function() {
		var tdDataCat = $('#tdDataCat').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [0,1,4] }
        ],
		dom: 'lfrtip',
		processing: true,
        language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
			emptyTable: "No categories yet.",
			search: "Search:"
			},
    	ajax: {	url: '/core/list_ingCat_data.php' },
		 columns: [
				   { data : 'image', title: 'Image', render: ciImage },
    			   { data : 'colorKey', title: 'Colour Key', render: ciKey},
				   { data : 'name', title: 'Name', render: ciName},
				   { data : 'notes', title: 'Description', render: ciNotes},
   				   { data : null, title: '', render: ciActions},		   
				  ],
        order: [[ 2, 'asc' ]],
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
				url: '/core/update_user_settings.php?set=listCat&action=load',
				dataType: 'json',
				success: function (json) {
					callback( json );
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			 url: "/core/update_user_settings.php?set=listCat&action=save",
			 data: data,
			 dataType: "json",
			 type: "POST"
		  });
		},

	});
});

function ciImage(data, type, row){
	if(row.image){
		var cimg = '<img src="' + row.image + '" class="img_ing">';
	}else{
		var cimg = '<img src="/img/molecule.png" class="img_ing">';
	}
	
	return '<a href="#" data-id="'+row.id+'" data-bs-toggle="modal" data-bs-target="#editCategory">' + cimg + '</a>';    
}

function ciKey(data, type, row){
	return '<a href="#" class="colorKey" style="background-color: rgb('+row.colorKey+')" id="colorKey" data-name="colorKey" data-type="select" data-pk="'+row.id+'" data-title="Choose Colour Key for '+row.name+'"></a>';
};

function ciName(data, type, row){
	return '<a class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</a>';    
};

function ciNotes(data, type, row){
	return '<a class="notes pv_point_gen" data-name="notes" data-type="textarea" data-pk="'+row.id+'">'+row.notes+'</a>';  
};

function ciActions(data, type, row){
	return '<i id="catDel" class="pv_point_gen fas fa-trash link-danger" data-id="'+row.id+'" data-name="'+row.name+'"></i>';    
};

$('#add-category').click(function() {
$.ajax({ 
	url: '/pages/update_settings.php', 
		type: 'POST',
		data: {
			manage: 'category',
			category: $("#category").val(),
			cat_notes: $("#cat_notes").val(),
			},
		dataType: 'json',
		success: function (data) {
			
			if(data.error){
				var msg = '<div class="alert alert-danger">'+data.error+'</div>';
				$('#catMsgIn').html(msg);
			}else if(data.success){
				var msg = '<div class="alert alert-success">'+data.success+'</div>';
				$('#add_ingredient_cat').modal('toggle');
				$('#catMsg').html(msg);
				reload_cat_data();
			}
			
			
		}
	});
});



$('#tdDataCat').editable({
  container: 'body',
  selector: 'a.name',
  url: "/pages/update_data.php?settings=cat",
  title: 'Category',
  type: "POST",
  dataType: 'json',
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
});
 
$('#tdDataCat').editable({
  container: 'body',
  selector: 'a.notes',
  url: "/pages/update_data.php?settings=cat",
  title: 'Description',
  type: "POST",
  dataType: 'json',
});

//Change colorKey
$('#tdDataCat').editable({
	pvnoresp: false,
	highlight: false,
	selector: 'a.colorKey',
	type: "POST",
	emptytext: "",
	emptyclass: "",
  	url: "pages/update_data.php?settings=cat",
    source: [
			 <?php
				$getCK = mysqli_query($conn, "SELECT name,rgb FROM colorKey ORDER BY name ASC");
				while ($r = mysqli_fetch_array($getCK)){
				echo '{value: "'.$r['rgb'].'", text: "'.$r['name'].'", ck: "color: rgb('.$r['rgb'].')"},';
			}
			?>
          ],
	dataType: 'html',
	success: function () {
		reload_cat_data();
	}
});

	
$('#tdDataCat').on('click', '[id*=catDel]', function () {
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
					url: '/pages/update_settings.php', 
					type: 'POST',
					data: {
						action: "delete",
						catId: cat.ID,
						},
					dataType: 'html',
					success: function (data) {
						$('#catMsg').html(data);
						reload_cat_data();
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

function reload_cat_data() {
    $('#tdDataCat').DataTable().ajax.reload(null, true);
};

function extrasShow() {
	$('[rel=tip]').tooltip({
        "html": true,
        "delay": {"show": 100, "hide": 0},
     });
};
$("#editCategory").on("show.bs.modal", function(e) {
	const id = e.relatedTarget.dataset.id;
	const name = e.relatedTarget.dataset.name;

	$.get("/pages/editCat.php?id=" + id)
		.then(data => {
		$("#editCategoryLabel", this).html(name);
		$(".modal-body", this).html(data);
	});
});
</script>
<!--ADD CATEGORY MODAL-->
<div class="modal fade" id="add_ingredient_cat" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="add_ingredient_cat" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add new category</h5>
      </div>
      
      <div class="modal-body">
      	<div id="catMsgIn"></div>
        <div class="form-group">
              <label class="col-md-3 control-label">Name:</label>
              <div class="col-md-8">
              	<input type="text" name="category" id="category" class="form-control"/>
              </div>
              <label class="col-md-3 control-label">Description:</label>
             <div class="col-md-8">
              <input type="text" name="cat_notes" id="cat_notes" class="form-control"/>
    		</div>
		</div>
      </div>
	  <div class="modal-footer">
        <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close_cat" value="Close">
        <input type="submit" name="add-category" class="btn btn-primary" id="add-category" value="Create">
      </div>   
  </div>
</div>
</div>

<!--EDIT MODAL-->            
<div class="modal fade" id="editCategory" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editCategoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="editCategoryLabel">Edit category</h5>
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
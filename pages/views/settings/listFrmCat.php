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
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
        <div class="dropdown-menu dropdown-menu-right">
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#add_formula_cat"><i class="fa-solid fa-plus mx-2"></i>Add formula category</a></li>
        </div>
    </div>
	</div>
</div>
    <div class="card-body">
    <div id="fcatMsg"></div>
    <table id="frmDataCat" class="table table-bordered" style="width:100%">
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
 
<script type="text/javascript" language="javascript" >
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
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: 'No groups yet.',
			search: 'Search:'
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
	});
});



function cName(data, type, row){
	return '<a class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</a>';    
}

function cType(data, type, row){
	return '<a class="type pv_point_gen" data-name="type" data-type="select" data-pk="'+row.id+'">'+row.type+'</a>';    
}

function cActions(data, type, row){
	return '<i id="catDel" class="pv_point_gen fas fa-trash link-danger" data-id="'+row.id+'" data-name="'+row.name+'"></i>';    
}

function fKey(data, type, row){
	return '<a href="#" class="colorKey" style="background-color: '+row.colorKey+'" id="colorKey" data-name="colorKey" data-type="select" data-pk="'+row.id+'" data-title="Choose Colour Key for '+row.name+'"></a>';    
}

$('#add-fcat').click(function() {
$.ajax({ 
	url: '/pages/update_settings.php', 
		type: 'POST',
		data: {
			manage: 'add_frmcategory',
			category: $("#fcatName").val(),
			cat_type: $("#cat_type").val(),
			},
		dataType: 'json',
		success: function (data) {
			if(data.error){
				var msg = '<div class="alert alert-danger">'+data.error+'</div>';
				$('#fcatMsgIn').html(msg);
			}else if(data.success){
				var msg = '<div class="alert alert-success">'+data.success+'</div>';
				$('#add_formula_cat').modal('toggle');
				$('#fcatMsg').html(msg);
				reload_fcat_data();
			}
			
		}
	});
});



$('#frmDataCat').editable({
  container: 'body',
  selector: 'a.name',
  url: "/pages/update_data.php?settings=fcat",
  title: 'Category name',
  type: "POST",
  dataType: 'json',
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
	type: "POST",
	emptytext: "",
	emptyclass: "",
  	url: "/pages/update_data.php?settings=fcat",
    source: [
			 <?php
				$getCK = mysqli_query($conn, "SELECT type FROM formulaCategories GROUP BY type");
				while ($r = mysqli_fetch_array($getCK)){
			 ?>
				{value: "<?=$r['type']?>", text: "<?=$r['type']?>"},
			<?php } ?>
          ],
	dataType: 'html',
	success: function () {
		reload_fcat_data();
	}
});

//Change colorKey
$('#frmDataCat').editable({
	pvnoresp: false,
	highlight: false,
	selector: 'a.colorKey',
	type: "POST",
	emptytext: "",
	emptyclass: "",
  	url: "/pages/update_data.php?settings=fcat",
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
		reload_fcat_data();
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
					url: '/pages/update_settings.php', 
					type: 'POST',
					data: {
						action: "del_frmcategory",
						catId: cat.ID,
						},
					dataType: 'html',
					success: function (data) {
						$('#fcatMsg').html(data);
						reload_fcat_data();
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

function reload_fcat_data() {
    $('#frmDataCat').DataTable().ajax.reload(null, true);
};


</script>
<!--ADD CATEGORY MODAL-->
<div class="modal fade" id="add_formula_cat" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="add_formula_cat" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add new category</h5>
      </div>
      
      <div class="modal-body">
      	<div id="fcatMsgIn"></div>
        <div class="form-group">
              <label class="col-md-3 control-label">Name:</label>
              <div class="col-md-8">
              	<input name="fcatName" id="fcatName" type="text" class="form-control" />
              </div>
              <label class="col-md-3 control-label">Type:</label>
             <div class="col-md-8">
              <select name="cat_type" id="cat_type" class="form-control">
      			<option value="profile">Profile</option>
     			<option value="sex">Gender</option>
    		  </select>
    		</div>
		</div>
      </div>
	  <div class="modal-footer">
        <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close_cat" value="Close">
        <input type="submit" name="add-fcat" class="btn btn-primary" id="add-fcat" value="Create">
      </div>   
  </div>
</div>
</div>

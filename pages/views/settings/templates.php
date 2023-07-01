<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');

?>
<style>
.mfp-iframe-holder .mfp-content {
    line-height: 0;
    width: 1500px;
    max-width: 1500px; 
	height: 1000px;
}
</style>
<h3>HTML Templates</h3>
<hr>
<div class="card-body">
  <div class="text-right">
    <div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mr2"></i>Actions</button>
        <div class="dropdown-menu dropdown-menu-right">
            <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#addTmpl"><i class="fa-solid fa-plus mr2"></i>Add new</a></li>
        </div>
    </div>                    
  </div>
</div>
<table id="tdTempls" class="table table-striped table-bordered" style="width:100%">
  <thead>
      <tr>
          <th>Name</th>
          <th>Description</th>
          <th>Created</th>
          <th>Updated</th>
          <th>Actions</th>
      </tr>
   </thead>
</table>
<script type="text/javascript" language="javascript" >
$(document).ready(function() {
		
	var tdTempls = $('#tdTempls').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
		{ orderable: false, targets: [4] }
	],
	dom: 'lfrtip',
	processing: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
		emptyTable: 'No templates found.',
		search: 'Search:'
		},
	ajax: {	url: '/core/list_templates_data.php' },
	columns: [
			  { data : 'name', title: 'Name', render: name },
			  { data : 'description', title: 'Description', render: description},
			  { data : 'created', title: 'Created', render: created},
			  { data : 'updated', title: 'Updated', render: updated},
			  { data : null, title: 'Actions', render: actions},		   
			 ],
	order: [[ 1, 'asc' ]],
	lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
	pageLength: 20,
	displayLength: 20,
	drawCallback: function( settings ) {
			extrasShow();
     	},
	});
	
});

function name(data, type, row){
	return '<a href="#" class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</a>';    
}

function description(data, type, row){
	return '<a href="#" class="description pv_point_gen" data-name="description" data-type="textarea" data-pk="'+row.id+'">'+row.description+'</a>';    
}

function created(data, type, row){
	return row.created;    
}

function updated(data, type, row){
	return row.updated;    
}

function actions(data, type, row){
	return '<a href="/pages/editHtmlTmpl.php?id='+row.id+'" id="editTmpl" class="fas fa-edit popup-link" data-id="'+row.id+'" data-name="'+row.name+'" rel="tip" title="Edit '+row.name+'"></a> <a href="#" id="sDel" class="fas fa-trash" data-id="'+row.id+'" data-name="'+row.name+'" rel="tip" title="Delete '+row.name+'"></a>';
}


$('#tdTempls').editable({
	  container: 'body',
	  selector: 'a.name',
	  type: 'POST',
	  url: "/pages/update_data.php?tmpl=update",
	  title: 'Template name',
	  success: function (data) {
		reload_data();
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
  	type: 'POST',
	url: "/pages/update_data.php?tmpl=update",
	title: 'Short description',
	success: function (data) {
		reload_data();
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
       title: "Confirm template removal",
       message : 'Delete <strong>'+ tmpl.Name +'</strong>?',
       buttons :{
           main: {
               label : "Remove",
               className : "btn-danger",
               callback: function (){
	    			
				$.ajax({ 
					url: '/pages/update_data.php', 
					type: 'POST',
					data: {
						tmpl: 'delete',
						tmplID: tmpl.ID,
						tmplName: tmpl.Name
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
               className : "btn-default",
               callback : function() {
                   return true;
               }
           }   
       },onEscape: function () {return true;}
   });
});

$('#addTmpl').on('click', '[id*=sAdd]', function () {
	$.ajax({ 
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			tmpl: 'add',
			tmpl_name: $("#tmpl_name").val(),
			tmpl_content: $("#tmpl_content").val(),
			tmpl_desc: $("#tmpl_desc").val(),	
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
			}else{
				var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
			$('#tmpl_inf').html(msg);
			reload_data();
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
	$('.popup-link').magnificPopup({
		type: 'iframe',
		closeOnContentClick: false,
		closeOnBgClick: false,
		showCloseBtn: true,
	});
};
</script>

<!-- ADD TEMPLATE -->
<div class="modal fade" id="addTmpl" tabindex="-1" role="dialog" aria-labelledby="addTmpl" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add new template</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="tmpl_inf"></div>
          <p>
            <p>
            Name: 
            <input class="form-control" name="tmpl_name" type="text" id="tmpl_name" />
            </p>
            <p>
            HTML Content:
            <textarea class="form-control" name="tmpl_content" id="tmpl_content" rows="4"></textarea>
            </p>
            <p>
            Short description:
            <input class="form-control" name="tmpl_desc" type="text" id="tmpl_desc" />
            </p>
            <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="sAdd" value="Add">
      </div>
    </div>
  </div>
</div>
</div>




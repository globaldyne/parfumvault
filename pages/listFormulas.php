<?php 

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/php-settings.php');

$getFCats = mysqli_query($conn, "SELECT name,cname,type FROM formulaCategories");
while($fcats = mysqli_fetch_array($getFCats)){
	$fcat[] =$fcats;
}
$cats_q = mysqli_query($conn, "SELECT id,name,description,type FROM IFRACategories ORDER BY id ASC");
while($cats_res = mysqli_fetch_array($cats_q)){
    $cats[] = $cats_res;
}
$cust = mysqli_query($conn, "SELECT id,name FROM customers ORDER BY id ASC");
while($customers = mysqli_fetch_array($cust)){
    $customer[] = $customers;
}
$fTypes_q = mysqli_query($conn, "SELECT id,name,description,concentration FROM perfumeTypes ORDER BY id ASC");
while($fTypes_res = mysqli_fetch_array($fTypes_q)){
    $fTypes[] = $fTypes_res;
}

$cFormoulas = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData"));
?>
<script src="/js/raty/jquery.raty.js"></script>
<script src="/js/rating.js"></script>
<link href="/js/raty/jquery.raty.css" rel="stylesheet">


<div class="card-header py-3">
  <h2 class="m-0 font-weight-bold text-primary"><a href="#" id="mainTitle">Formulas</a></h2>
</div>
            
<div class="pv_menu_formulas">
    <div class="text-right">
        <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
            <div class="dropdown-menu dropdown-menu-right">
              <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#add_formula"><i class="fa-solid fa-plus mx-2"></i>Add new formula</a></li>
              <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#add_formula_csv"><i class="fa-solid fa-file-csv mx-2"></i>Import from CSV</a></li>
              <div class="dropdown-divider"></div>
              <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#add_formula_cat"><i class="fa-solid fa-circle-plus mx-2"></i>Create formula category</a></li>
              <div class="dropdown-divider"></div>
        	  <li><a class="dropdown-item" href="/pages/operations.php?action=exportFormulas"><i class="fa-solid fa-file-export mx-2"></i>Export Formulas as JSON</a></li>
        	  <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#import_formulas_json"><i class="fa-solid fa-file-import mx-2"></i>Import Formulas from JSON</a></li>
              <?php if($cFormoulas){ ?>
 			  <div class="dropdown-divider"></div>
        	  <li><a class="dropdown-item text-danger" href="#" id="wipe_all_formulas"><i class="fa-solid fa-trash mx-2"></i>Delete all</a></li>
              <?php } ?>
            </div>
        </div>
    </div>
</div>


<?php
if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients")))){
	echo '<div class="row g-3"><div class="alert alert-info alert-dismissible"><i class="fa-solid fa-circle-info mx-2"></i><strong>No ingredients yet, click <a href="/?do=ingredients">here</a> to create a new one </strong></div></div>';
}
if(empty($cFormoulas)){
	echo '<div class="row g-3"><div class="alert alert-info alert-dismissible"><i class="fa-solid fa-circle-info mx-2"></i><strong>No formulas yet, click <a href="#" data-bs-toggle="modal" data-bs-target="#add_formula">here</a> to add or use the <a href="/?do=marketplace">Marketplace</a> to import a demo one</strong></div></div>';
}else{
?>
<div id="listFormulas">
<ul>
 <li class="tabs">
    <a href="#tab-all">All formulas</a>
 </li>
 <?php foreach ($fcat as $cat) { ?>
 <li class="tabs" data-source="/core/list_formula_data.php?filter=1&<?=$cat['type']?>=<?=$cat['cname']?>" data-table="<?=$cat['cname']?>-table">
    <a href="#tab-<?=$cat['cname']?>"><?=$cat['name']?></a>
 <?php } ?>       
</ul>
<div class="tab-content table-responsive">
    <div class="tab-pane" id="tab-all">
        <table id="all-table" class="table table-striped" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Formula Name</th>
                    <th>Product Name</th>
                    <th>Status</th>
                    <th>Ingredients</th>
                    <th>Revision</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th>Made</th>
                    <th>Rating</th>
          			<th data-priority="1"></th>
                </tr>
            </thead>
        </table>
    </div>
    <?php foreach ($fcat as $cat) {?>
    <div class="tab-pane" id="tab-<?=$cat['cname']?>">
        <table id="<?=$cat['cname']?>-table" class="table table-striped" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Formula Name</th>
                    <th>Product Name</th>
                    <th>Status</th>
                    <th>Ingredients</th>
                    <th>Revision</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th>Made</th>
                    <th>Rating</th>
          			<th data-priority="1"></th>
                </tr>
            </thead>
        </table>
    </div>
    <?php } ?>
  </div>
</div>

<?php } ?>
<script>
$(document).ready(function() {

	$('#mainTitle').click(function() {
		reload_formulas_data();
	});
	$('.selectpicker').selectpicker('refresh');
	function extrasShow() {
		$('[rel=tip]').tooltip({
			"html": true,
			"delay": {"show": 100, "hide": 0},
		});
	};
	
	$(".tabs").click(function() {
		 var src = $(this).data("source");
		 var tableId = $(this).data("table");
		 initTable(tableId, src);
	});
	
	function initTable(tableId, src) {
		var table = $("#" + tableId).DataTable({
		   ajax: {
			   url: src,
			   type: 'POST',
			   dataType: 'json',
			   data: function(d) {
				   if (d.order.length>0){
						d.order_by = d.columns[d.order[0].column].data
						d.order_as = d.order[0].dir
					}
				},
			},
			columns: [
			   { data : 'name', title: 'Formula Name', render: fName },
			   { data : 'product_name', title: 'Product Name', render: pName},
			   { data : 'status', title: 'Status', render: fStatus},
			   { data : 'ingredients', title: 'Ingredients'},
			   { data : 'revision', title: 'Revision'},
			   { data : 'isMade', title: 'Made', render: fMade},
			   { data : 'rating', title: 'Rating', render: rating},
			   { data : 'created', title: 'Created', render: fDate},
			   { data : 'updated', title: 'Updated', render: fDate},
			   { data : null, title: '', render: fActions},				   
			],
			processing: true,
			serverSide: true,
			searching: true,
			responsive: true,
			language: {
				loadingRecords: '&nbsp;',
				processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
				emptyTable: '<div class="row g-3"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No formulas yet, click <a href="#" data-bs-toggle="modal" data-bs-target="#add_formula">here</a> to add or use the <a href="/?do=marketplace">Marketplace</a> to import a demo one</strong></div></div>',
				zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No formulas found</strong></div></div>',
				searchPlaceholder: 'Search by formula, or product name...',
				search: ''
			},
			order: [0,'asc'],
			columnDefs: [
				{ orderable: false, targets: [2, 3, 9] },
				{ className: 'text-center', targets: '_all' },
				{ responsivePriority: 1, targets: 0 }
			],
			destroy: true,
			paging:  true,
			info:   true,
			lengthMenu: [[20, 40, 60, 100], [20, 40, 60, 100]],
			pageLength: 20,
			displayLength: 20,
			drawCallback: function( settings ) {
				extrasShow();
			},
			createdRow: function(row, data, dataIndex){
				initRating(row);
			},
			stateSave: true,
			stateDuration: -1,
			stateLoadCallback: function (settings, callback) {
				$.ajax( {
					url: "/core/update_user_settings.php?set=listFormulas&action=load&tableId=" + tableId,
					dataType: "json",
					success: function (json) {
						callback( json );
					}
				});
			},
			stateSaveCallback: function (settings, data) {
			   $.ajax({
				 url: "/core/update_user_settings.php?set=listFormulas&action=save&tableId=" + tableId,
				 data: data,
				 dataType: "json",
				 type: "POST"
			  });
			},
		});
	}
	
	initTable("all-table", "/core/list_formula_data.php");
	
	$("#listFormulas").tabs();
	
	function rating(data, type, row, meta){
		data = '<span class="rating" data-id='+row.id+' data-score="'+row.rating+'"></span>';
		return data;
	};
	
	function fName(data, type, row, meta){
		if(type === 'display'){
			if(row.isProtected == 1){
				var pad = 'class="fas fa-lock" rel="tip" title="Formula is protected"';
			}else{
				var pad = 'class="fas fa-unlock"  rel="tip" title="Formula is not protected"';
			}
			data = '<div '+ pad +'></div><a href="/?do=Formula&id=' + row.id + '" target="_blank"> ' + data + '</a>';
		}
	  return data;
	};
	
	
	function pName(data, type, row, meta){
		data = '<i class="pv_point_gen_color" data-bs-toggle="modal" data-bs-target="#getFormMeta" data-id="' + row.id + '" data-formula="'+row.name+'">'+row.product_name+'</i>';
		
	  return data;
	};
	
	function fMade(data, type, row, meta){
		if(type === 'display'){
			if(row.isMade == 1){
				var data = '<i class="fas fa-check-circle text-success-emphasis" rel="tip" title="Formula last made on ' + row.madeOn + '"></i>';
			}else{
				var data = '<i class="fas fa-hourglass-start" rel="tip" title="Formula is not made yet"></i>';
			}
		}
	  return data;
	};
	
	function fStatus(data, type, row, meta){
		if(row.status == 0){
			var data = '<span class="badge rounded-pill d-block p-2 badge-secondary">Scheduled<span class="ms-1 fas fa-hourglass-end" data-fa-transform="shrink-2"></span></span>';
		}
		if(row.status == 1){
			var data = '<span class="badge rounded-pill d-block p-2 badge-primary">Under Development<span class="ms-1 fas fa-spinner" data-fa-transform="shrink-2"></span></span>';
		}
		if(row.status == 2){
			var data = '<span class="badge rounded-pill d-block p-2 badge-info">Under Evaluation<span class="ms-1 fas fa-user-check" data-fa-transform="shrink-2"></span></span>';
		}
		if(row.status == 3){
			var data = '<span class="badge rounded-pill d-block p-2 badge-success">In Production<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span></span>';
		}
		if(row.status == 4){
			var data = '<span class="badge rounded-pill d-block p-2 badge-warning">To be reformulated<span class="ms-1 fas fa-redo" data-fa-transform="shrink-2"></span></span>';
		}
		if(row.status == 5){
			var data = '<span class="badge rounded-pill d-block p-2 badge-danger">Failure<span class="ms-1 fas fa-ban" data-fa-transform="shrink-2"></span></span>';
		}
		
		return data;
	};
	
	function fDate(data, type, row, meta){
	  if(type === 'display'){
		if(data == '0000-00-00 00:00:00'){
		  data = '-';
		}else{
			let dateTimeParts= data.split(/[- :]/); 
			dateTimeParts[1]--; 
			const dateObject = new Date(...dateTimeParts); 
			data = dateObject.toLocaleDateString() + " " + dateObject.toLocaleTimeString();
		}
	  }
	  return data;
	};
	
	function fActions(data, type, row, meta){
			data = '<div class="dropdown">' +
			'<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
				'<ul class="dropdown-menu dropdown-menu-right">';
				
			data += '<li><a class="pv_point_gen dropdown-item" data-bs-toggle="modal" data-bs-target="#getFormMeta" data-formula="'+row.name+'" data-id="' + row.id + '"><i class="fas fa-cogs mx-2"></i>Settings</a></li>';
	
			data += '<li><a class="dropdown-item" href="/pages/operations.php?action=exportFormulas&fid=' + row.fid + '" rel="tip" title="Export '+ row.name +' as JSON" ><i class="fas fa-download mx-2"></i>Export as JSON</a></li>';
			
			data += '<li><a class="dropdown-item" href="#" id="addTODO" rel="tip" title="Schedule '+ row.name +' to make" data-id='+ row.fid +' data-name="'+ row.name +'"><i class="fas fa-tasks mx-2"></i>Schedule to make</a></li>';
			
			data += '<li><a class="dropdown-item" href="#" id="cloneMe" rel="tip" title="Duplicate '+ row.name +'" data-id='+ row.fid +' data-name="'+ row.name +'"><i class="fas fa-copy mx-2"></i>Duplicate formula</a></li>';
			
			data += '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#genQRC" data-id="'+ row.fid +'" data-formula="'+ row.name +'"><i class="fa-solid fa-qrcode mx-2"></i>Generate QR Code</a></li>';
	
	
			data += '<div class="dropdown-divider"></div>';
			data += '<li><a class="dropdown-item link-danger" href="#" id="deleteMe" rel="tip" title="Delete '+ row.name +'" data-id="'+ row.fid +'" data-name="'+ row.name +'"><i class="fas fa-trash mx-2"></i>Permanently delete formula</a></li>';
			data += '</ul></div>';
		
		return data;
	};
	
	//Generate a QR
	$("#genQRC").on("show.bs.modal", function(e) {
		const id = e.relatedTarget.dataset.id;
		const formula = e.relatedTarget.dataset.formula;
	
	  $.get("/pages/views/generic/qrcode.php?type=formula&id=" + id)
		.then(data => {
		  $("#genQRLabel", this).html(formula);
		  $(".modal-body", this).html(data);
		 // $("#msg_settings_info", this).html('');
		});
	
	});
	
	//Duplicate
	$('table.table').on('click', '[id*=cloneMe]', function () {
		var formula = {};
		formula.ID = $(this).attr('data-id');
		formula.Name = $(this).attr('data-name');
		
		$.ajax({ 
			url: '/pages/manageFormula.php', 
			type: 'POST',
			data: {
				action: "clone",
				fid: formula.ID,
				fname: formula.Name,
			},
			dataType: 'json',
			success: function (data) {
				if ( data.success ) {
					$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
					$('.toast-header').removeClass().addClass('toast-header alert-success');
					reload_formulas_data();
				} else {
					$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
					$('.toast-header').removeClass().addClass('toast-header alert-danger');
				}
				$('.toast').toast('show');
			},
			error: function (xhr, status, error) {
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		  });
	});
	
	$('table.table').on('click', '[id*=deleteMe]', function () {
		var formula = {};
		formula.ID = $(this).attr('data-id');
		formula.Name = $(this).attr('data-name');
		
		bootbox.dialog({
		   title: "Confirm formula deletion",
		   message : '<div class="alert alert-warning"><i class="fa-solid fa-triangle-exclamation mx-2"></i>WARNING, this action cannot be reverted unless you have a backup.</div><p>Permantly delete <strong>'+ $(this).attr('data-name') +'</strong> formula?</p>' +
		   '<div class="form-group col-sm">' + 
			'<input name="archiveFormula" id="archiveFormula" type="checkbox" value="1">'+
			'<label class="form-check-label mx-2" for="archiveFormula">Archive formula</label>'+
		   '</div>',
		   buttons :{
			   main: {
				   label : "DELETE",
				   className : "btn-danger",
				   callback: function (){
						
					$.ajax({ 
						url: '/pages/manageFormula.php', 
						type: 'POST',
						data: {
							action: "deleteFormula",
							fid: formula.ID,
							fname: formula.Name,
							archiveFormula: $("#archiveFormula").is(':checked'),
						},
						dataType: 'json',
						success: function (data) {
							if ( data.success ) {
								$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
								$('.toast-header').removeClass().addClass('toast-header alert-success');
								reload_formulas_data();
							} else {
								$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
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
	
	$('#wipe_all_formulas').click(function() {
		
		bootbox.dialog({
		   title: "Confirm formulas wipe",
		   message : 'This will remove ALL of your fromulas from the database.\nThis cannot be reverted so please make sure you have taken a backup first.',
		   buttons :{
			   main: {
				   label : "DELETE ALL",
				   className : "btn-danger",
				   callback: function (){
						
					$.ajax({
						url: '/pages/update_data.php', 
						type: 'POST',
						data: {
							formulas_wipe: "true",
						},
						dataType: 'json',
						success: function (data) {
							if ( data.success ) {
								$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
								$('.toast-header').removeClass().addClass('toast-header alert-success');
								reload_formulas_data();
							} else {
								$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
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
	
	$('table.table').on('click', '[id*=addTODO]', function () {
		var formula = {};
		formula.ID = $(this).attr('data-id');
		formula.Name = $(this).attr('data-name');
		$.ajax({ 
		url: '/pages/manageFormula.php', 
		type: 'POST',
		data: {
			action: 'todo',
			fid: formula.ID,
			fname: formula.Name,
			add: true,
		},
		dataType: 'json',
		success: function (data) {
			if ( data.success ) {
				$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
				$('.toast-header').removeClass().addClass('toast-header alert-success');
			} else {
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
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
	});
	
	$('#add_formula').on('click', '[id*=btnAdd]', function () {
		$.ajax({ 
		url: '/pages/manageFormula.php', 
		type: 'POST',
		data: {
			action: 'addFormula',
			name: $("#formula-name").val(),
			profile: $("#profile").val(),
			catClass: $("#catClass").val(),
			finalType: $("#finalType").val(),
			notes: $("#notes").val(),
			customer: $("#customer").val(),
		},
		dataType: 'json',
		success: function (data) {
			if(data.error){
				var rmsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
			}else if(data.success){
				var rmsg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><a href="/?do=Formula&id='+data.success.id+'">'+data.success.msg+'</a></div>';
				reload_formulas_data();
				if($("#go_to_formula").prop("checked")){
					window.location = "/?do=Formula&id=" + data.success.id
				}
			}
			$('#addFormulaMsg').html(rmsg);
		},
		error: function (xhr, status, error) {
			$('#addFormulaMsg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
		}
	  });
	});
	
	$("#btnImport").prop("disabled", true);
	$("#btnImport").hide();
		
	$("input[type=file]").on('change',function(){	
		var fd = new FormData();
		var files = $('#CSVFile')[0].files;
		var formula_name = $('#CSVname').val();
		
		if ( formula_name == '') {
			var filename = $('input[type=file]').val().replace(/C:\\fakepath\\/i, '').replace(/.csv/i, '');
			$('#CSVname').val(filename);
		}
		
		if(files.length > 0 ){
			fd.append('CSVFile',files[0]);
			$.ajax({
			   url: '/pages/upload.php?type=frmCSVImport&step=upload',
			   type: 'POST',
			   data: fd,
			   contentType: false,
			   processData: false,
					 cache: false,
			   success: function(response){
				 if(response != 0){
					$("#CSVImportMsg").html('');
					$("#step_upload").html(response);
					$("#btnImport").show();
				  }else{
					$("#CSVImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> File upload failed!</div>');
				  }
			},
		 });
		}else{
			$("#CSVImportMsg").html('<div class="alert alert-danger">Please select a file to upload!</div>');
		}
	});
	
	
	var total_selection = 0;
	var ingredient = 0;
	var concentration = 0;
	var dilutant = 0;
	var quantity = 0;
	
	var column_data = [];
	
	$(document).on('change', '.set_column_data', function(){
		var column_name = $(this).val();
		var column_number = $(this).data('column_number');
		if(column_name in column_data) {
		  $('#CSVImportMsg').html('<div class="alert alert-danger"><strong>'+column_name+'</strong> is already assigned.</div>');
		  $(this).val('');
		  return false;
		}else{
			$('#CSVImportMsg').html('');
		}
	
		if(column_name != '') {
		  column_data[column_name] = column_number;
		} else {
		  const entries = Object.entries(column_data);
	
		  for(const [key, value] of entries) {
			if(value == column_number) {
			  delete column_data[key];
			}
		  }
		}
	
		total_selection = Object.keys(column_data).length;
	
		if(total_selection == 4) {
			$('#btnImport').prop("disabled", false);
			ingredient = column_data.ingredient;
			concentration = column_data.concentration;
			dilutant = column_data.dilutant;
			quantity = column_data.quantity;
		} else {
			$('#btnImport').prop("disabled", true);
		}
	
	  });
	
	$(document).on('click', '#btnImport', function(event){
	
		event.preventDefault();
		var formula_name = $('#CSVname').val();
		var formula_profile = $('#CSVProfile').val();
		
		$.ajax({
			url: "/pages/upload.php?type=frmCSVImport&step=import",
		  	method: "POST",
		  	data:{		  
			  formula_name: formula_name,
			  formula_profile: formula_profile,
			  ingredient: ingredient, 
			  concentration: concentration, 
			  dilutant: dilutant, 
			  quantity: quantity
			},
		  	beforeSend:function(){
				$('#btnImport').prop("disabled", true);
		  	},
		  	success:function(data) {
			  if (data.indexOf('Error:') > -1) {
				  $('#btnImport').prop("disabled", false);
				  $('#CSVImportMsg').html(data);
			  }else{
				$('#btnImport').prop("disabled", false);
				$('#btnImport').hide();
				$('#btnCloseCsv').prop('value', 'Close');
				$('#process_area').css('display', 'none');
				$('#CSVImportMsg').html(data);
				reload_formulas_data();
			  }
		  }
		})
	
	  });
	  
	function reload_formulas_data() {
		$('#all-table').DataTable().ajax.reload(null, true);
	};
	
	$('#close_export_json').click(function() {
		$('#JSONExportMsg').html('');
	});
	
	$('#add_formula_cat').on('click', '[id*=add-fcat]', function () {
	
		$.ajax({ 
			url: '/pages/update_settings.php', 
				type: 'POST',
				data: {
					manage: 'add_frmcategory',
					category: $("#fcatName").val(),
					cat_type: 'profile',
			},
			dataType: 'json',
			success: function (data) {
				if(data.error){
					msg = '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>'+data.error+'</div>';
				}else if(data.success){
					$('#add_formula_cat').modal('toggle');
					$('.modal-backdrop').hide();
					list_formulas();
				}
				$('#fcatMsg').html(msg);
			},
			error: function (xhr, status, error) {
				$('#fcatMsg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error + '</div>');
			}
		});
	});
	
	$("#getFormMeta").on("show.bs.modal", function(e) {
	  const id = e.relatedTarget.dataset.id;
	  const formula = e.relatedTarget.dataset.formula;
	
	  $.get("/pages/views/formula/getFormMeta.php?id=" + id)
		.then(data => {
		  $("#getFormMetaLabel", this).html(formula);
		  $(".modal-body", this).html(data);
		  $("#msg_settings_info", this).html('');
		});
		
	});
	
	$("#formula-name").keyup(function(){
		var currentText = $(this).val();
		if (currentText == ""){
			currentText = "Add new formula";
		}
		$("#new-formula-name").text(currentText);
	});

});
</script>

<!--GEN QRCODE MODAL-->            
<div class="modal fade" id="genQRC" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="getFormMetalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="genQRLabel">Please wait...</h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex justify-content-center">
        <div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i>Loading...</div>
      </div>
    </div>
  </div>
</div>

<!--GET FORMULA SETTINGS MODAL-->            
<div class="modal fade" id="getFormMeta" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="getFormMetalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="getFormMetaLabel">Formula settings</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i>Loading...</div>
      </div>
    </div>
  </div>
</div>

<!--ADD FORMULA MODAL-->
<div class="modal fade" id="add_formula" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="add_formula" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="new-formula-name">Add new formula</h5>
      </div>
      <div class="modal-body">
      	<div id="addFormulaMsg"></div>
      
      	<div class="form">

          <div class="row mb-3">
            <div class="col-sm">
                <label for="formula-name" class="form-label">Formula name</label>
                <input name="formula-name" id="formula-name" type="text" class="form-control" />
            </div>
          </div>
      <div class="row mb-3">
        <div class="col-sm">
          <label for="profile" class="form-label">Profile</label>
          <select name="profile" id="profile" class="form-control selectpicker" data-live-search="true">
             <?php foreach ($fcat as $cat) { if($cat['type'] == 'profile'){?>		
                 <option value="<?=$cat['cname']?>"><?=$cat['name']?></option>
             <?php } }?>
          </select>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-sm">
          <label for="catClass" class="form-label">Purpose</label>
          <select name="catClass" id="catClass" class="form-control selectpicker" data-live-search="true">
            <?php foreach ($cats as $IFRACategories) {?>
                <option value="cat<?php echo $IFRACategories['name'];?>" <?php echo ($settings['defCatClass']=='cat'.$IFRACategories['name'])?"selected=\"selected\"":""; ?>><?php echo 'Cat'.$IFRACategories['name'].' - '.$IFRACategories['description'];?></option>
            <?php }	?>
          </select>
        </div>
      </div>
      <div class="row mb-3">
        	<div class="col-sm">
                <label for="finalType" class="form-label">Final type</label>
          		<select name="finalType" id="finalType" class="form-control selectpicker" data-live-search="true">  
            		<option value="100">Concentrated (100%)</option>
            		<?php foreach ($fTypes as $fType) {?>
                	<option value="<?php echo $fType['concentration'];?>" <?php echo ($info['finalType']==$fType['concentration'])?"selected=\"selected\"":""; ?>><?php echo $fType['name'].' ('.$fType['concentration'];?>%)</option>
             		<?php } ?>			
          		</select>
        	</div>
      </div>
      
      <div class="row mb-3">
        <div class="col-sm">
          <label for="customer" class="form-label">Customer</label>
          <select name="customer" id="customer" class="form-control selectpicker" data-live-search="true">
            <option value="0">Internal use</option>
            <?php foreach ((array)$customer as $c) {?>
                <option value="<?=$c['id'];?>"><?=$c['name']?></option>
            <?php }	?>
          </select>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-sm">
          <label for="notes" class="form-label">Notes</label>
          <textarea name="notes" id="notes" cols="45" rows="5" class="form-control"></textarea>
        </div>
      </div>
    </div>

      <hr/>
      <div class="row mb-3">
        <div class="mx-4">
    		<input type="checkbox" class="form-check-input" id="go_to_formula" checked>
   			<label class="form-check-label" for="go_to_formula">Go to formula when created</label>
  		</div>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="btnAdd" value="Add formula">
      </div>
    </div>
  </div>
</div>
</div>

<!--IMPORT FORMULA CSV MODAL-->
<div class="modal fade" id="add_formula_csv" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import formula from CSV</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <div id="CSVImportMsg"></div>
        <div id="process_area">
          <div class="mb-3 row">
            <label for="CSVname" class="col-md-3 col-form-label">Formula name</label>
            <div class="col-md-9">
              <input type="text" name="CSVname" id="CSVname" class="form-control"/>
            </div>
          </div>
          
          <div class="mb-3 row">
            <label for="CSVProfile" class="col-md-3 col-form-label">Profile</label>
            <div class="col-md-9">
              <select name="CSVProfile" id="CSVProfile" class="form-control selectpicker" data-live-search="true">
                <?php foreach ($fcat as $cat) { if($cat['type'] == 'profile'){?>
                <option value="<?=$cat['cname']?>"><?=$cat['name']?></option>
                <?php } }?>
              </select>
            </div>
          </div>
          
          <div class="mb-3 row">
            <label for="CSVFile" class="col-md-3 col-form-label">CSV file</label>
            <div class="col-md-9">
              <input type="file" name="CSVFile" id="CSVFile" class="form-control" />
            </div>
          </div>

          <div id="step_upload" class="modal-body"></div>
          <div class="col-md-12">
            <hr />
            <p>CSV format: <strong>ingredient, concentration, dilutant, quantity</strong></p>
            <p>Example: <em><strong>Ambroxan, 10, TEC, 0.15</strong></em></p>
          </div>
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseCsv">Cancel</button>
        <button type="submit" name="btnImport" class="btn btn-primary" id="btnImport">Import</button>
      </div>
    </div>
  </div>
</div>


<!--ADD CATEGORY MODAL-->
<div class="modal fade" id="add_formula_cat" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create new formula category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <div id="fcatMsg"></div>
        <div class="mb-3 row">
          <label for="fcatName" class="col-sm-4 col-form-label">Category name</label>
          <div class="col-md-12">
            <input name="fcatName" id="fcatName" type="text" class="form-control" />
          </div>
        </div>
      </div>
	  
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close_cat">Cancel</button>
        <button type="submit" name="add-fcat" class="btn btn-primary" id="add-fcat">Create</button>
      </div>
    </div>
  </div>
</div>



<!-- IMPORT JSON MODAL -->
<div class="modal fade" id="import_formulas_json" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="import_formulas_json" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import formulas from a JSON file</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="JSRestMsg"></div>
        <div class="progress">  
          <div id="uploadProgressBar" class="progress-bar" role="progressbar" aria-valuemin="0"></div>
        </div>
        <div id="backupArea" class="mt-4">
          <div class="form-group row">
            <label for="backupFile" class="col-auto col-form-label">JSON file</label>
            <div class="col-md">
              <input type="file" name="backupFile" id="backupFile" class="form-control" />
            </div>
          </div>
          <div class="col-md-12 mt-3">
            <hr />
            <p><strong>IMPORTANT</strong></p>
            <ul>
              <li>
                <div id="raw" data-size="<?=getMaximumFileUploadSizeRaw()?>">Maximum file size: <strong><?=getMaximumFileUploadSize()?></strong></div>
              </li>
              <li>Any formula with the same id will be replaced. Please make sure you have taken a backup before importing a JSON file.</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK">Cancel</button>
        <button type="submit" name="btnRestore" class="btn btn-primary" id="btnRestoreFormulas">Import</button>
      </div>
    </div>
  </div>
</div>

<script src="/js/import.formulas.js"></script>

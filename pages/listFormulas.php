<?php 

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
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

?>
<script src="/js/raty/jquery.raty.js"></script>
<script src="/js/rating.js"></script>
<link href="/js/raty/jquery.raty.css" rel="stylesheet">
  
<div class="card-header py-3">
  <h2 class="m-0 font-weight-bold text-primary"><a href="javascript:list_formulas()">Formulas</a></h2>
  <div id="inMsg"></div>
</div>
            
<div class="pv_menu_formulas">
    <div class="text-right">
        <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item" href="#" data-toggle="modal" data-target="#add_formula">Add new formula</a>
              <a class="dropdown-item" href="#" data-toggle="modal" data-target="#add_formula_csv">Import from CSV</a>
              <div class="dropdown-divider"></div>
        	  <a class="dropdown-item" href="#" data-toggle="modal" data-target="#export_formulas_json">Export Formulas as JSON</a>
        	  <a class="dropdown-item" href="#" data-toggle="modal" data-target="#import_formulas_json">Import Formulas from JSON</a>

            </div>
        </div>
    </div>
</div>


<?php
if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients")))){
	echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> no ingredients yet, click <a href="?do=ingredients">here</a> to add.</div>';
}elseif(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData")))){
	echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> no formulas yet, click <a href="#" data-toggle="modal" data-target="#add_formula">here</a> to add.</div>';
}else{
?>
<div id="listFormulas">
<ul>
 <li class="tabs">
    <a href="#tab-all">All formulas</a>
 </li>
 <?php foreach ($fcat as $cat) { ?>
 <li class="tabs" data-source="core/list_formula_data.php?filter=1&<?=$cat['type']?>=<?=$cat['cname']?>" data-table="<?=$cat['cname']?>-table">
    <a href="#tab-<?=$cat['cname']?>"><?=$cat['name']?></a>
 <?php } ?>       
</ul>
<div class="tab-content table-responsive">
    <div class="tab-pane" id="tab-all">
        <table id="all-table" class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Formula Name</th>
                    <th>Product Name</th>
                    <th>Status</th>
                    <th>Ingredients</th>
                    <th>Class</th>
                    <th>Created</th>
                    <th>Made</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php foreach ($fcat as $cat) {?>
    <div class="tab-pane" id="tab-<?=$cat['cname']?>">
        <table id="<?=$cat['cname']?>-table" class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Formula Name</th>
                    <th>Product Name</th>
                    <th>Status</th>
                    <th>Ingredients</th>
                    <th>Class</th>
                    <th>Created</th>
                    <th>Made</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php } ?>
  </div>
</div>

<?php } ?>
<script type="text/javascript" language="javascript" >
function extrasShow() {
	$('[rel=tip]').tooltip({
		"html": true,
		"delay": {"show": 100, "hide": 0},
	});
	$('.popup-link').magnificPopup({
		type: 'iframe',
		closeOnContentClick: false,
		closeOnBgClick: false,
		showCloseBtn: true,
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
			   { data : 'catClass', title: 'Class'},
			   { data : 'isMade', title: 'Made', render: fMade},
   			   { data : 'rating', title: 'Rating', render: rating},
			   { data : 'created', title: 'Created'},
			   { data : null, title: 'Actions', render: fActions},				   
			  ],
			 processing: true,
	         serverSide: true,
			 searching: true,
			 language: {
				loadingRecords: '&nbsp;',
				processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Blending...</span>',
				emptyTable: "No formulas added yet.",
				searchPlaceholder: 'Name, or product name..',
				search: "Search for formula:"
			},
           order: [0,'asc'],
           columnDefs: [
				{ orderable: false, targets: [3, 7] },
				{ className: 'text-center', targets: '_all' },				  
				],
	    destroy: true,
        bFilter: true,
        paging:  true,
		info:   true,
		lengthMenu: [[20, 40, 60, 100], [20, 40, 60, 100]],
		drawCallback: function( settings ) {
			extrasShow();
     	},
		createdRow: function(row, data, dataIndex){
            initRating(row);
        },
	});
}

initTable("all-table", "/core/list_formula_data.php");

$("#listFormulas").tabs();

function rating(data, type, row, meta){
  	data = '<span class="rating" data-id='+row.id+' data-score="'+row.rating+'"></span>';
	return data;
}

function fName(data, type, row, meta){
	if(type === 'display'){
		if(row.isProtected == 1){
			var pad = 'class="fas fa-lock" rel="tip" title="Formula is protected"';
		}else{
			var pad = 'class="fas fa-unlock"  rel="tip" title="Formula is not protected"';
		}
		data = '<div '+ pad +'</div><a href="/?do=Formula&id=' + row.id + '" > ' + data + '</a>';
	}
  return data;
}
function pName(data, type, row, meta){
	if(type === 'display'){
		data = '<a class="popup-link" href="/pages/getFormMeta.php?id=' + row.id + '">' + data + '</a>';
	}
  return data;
}

function fMade(data, type, row, meta){
	if(type === 'display'){
		if(row.isMade == 1){
			var data = '<i class="fas fa-check-circle" rel="tip" title="Formula last made on ' + row.madeOn + '"></i>';
		}else{
			var data = '<i class="fas fa-hourglass-start" rel="tip" title="Formula is not made yet"></i>';
		}
	}
  return data;
}

function fStatus(data, type, row, meta){
	if(row.status == 0){
		var data = '<span class="label label-default">Schedulled</span>';
	}
	if(row.status == 1){
		var data = '<span class="label label-primary">Under Development</span>';
	}
	if(row.status == 2){
		var data = '<span class="label label-info">Under Evaluation</span>';
	}
	if(row.status == 3){
		var data = '<span class="label label-success">In Production</span>';
	}
	if(row.status == 4){
		var data = '<span class="label label-warning">To be reformulated</span>';
	}
	if(row.status == 5){
		var data = '<span class="label label-danger">Failure</span>';
	}
	
	return data;
}

function fActions(data, type, row, meta){
	if(type === 'display'){
		data = '<a href="/pages/getFormMeta.php?id=' + row.id + '" rel="tip" title="Show details of '+ row.name +'" class="fas fa-cogs popup-link mr2"></a><a href="#" id="addTODO" class="fas fa-tasks mr2" rel="tip" title="Add '+ row.name +' to the make list" data-id='+ row.fid +' data-name="'+ row.name +'"></a><a href="#" id="cloneMe" class="fas fa-copy mr2" rel="tip" title="Clone '+ row.name +'" data-id='+ row.fid +' data-name="'+ row.name +'"></a><i id="deleteMe" class="pv_point_gen fas fa-trash"  style="color: #c9302c;" rel="tip" title="Delete '+ row.name +'" data-id='+ row.fid +' data-name="'+ row.name +'"></i>';
	}
    return data;
}

//Clone
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
			if (data.success) {
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				reload_formulas_data();
			}else{
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
			$('#inMsg').html(msg);
		}
	  });
});

$('table.table').on('click', '[id*=deleteMe]', function () {
	var formula = {};
	formula.ID = $(this).attr('data-id');
	formula.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm formula deletion",
       message : 'Permantly delete <strong>'+ $(this).attr('data-name') +'</strong> formula?',
       buttons :{
           main: {
               label : "Delete",
               className : "btn-danger",
               callback: function (){
	    			
				$.ajax({ 
					url: '/pages/manageFormula.php', 
					type: 'POST',
					data: {
						action: "delete",
						fid: formula.ID,
						fname: formula.Name,
						},
					dataType: 'json',
					success: function (data) {
						//$('#inMsg').html(data);
						//reload_formulas_data();
						if (data.success) {
							var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
							reload_formulas_data();
						}else{
							var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
						}
						$('#inMsg').html(msg);
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
	  	if (data.success) {
	  		var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
			reload_formulas_data();
		}else{
			var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
		}
		$('#inMsg').html(msg);
    }
  });
});

$('#add_formula').on('click', '[id*=btnAdd]', function () {
	$.ajax({ 
    url: '/pages/manageFormula.php', 
	type: 'POST',
    data: {
		action: 'addFormula',
		name: $("#name").val(),
		profile: $("#profile").val(),
		catClass: $("#catClass").val(),
		finalType: $("#finalType").val(),
		notes: $("#notes").val(),
		customer: $("#customer").val(),
		},
	dataType: 'json',
    success: function (data) {
		if(data.error){
			var rmsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
		}else if(data.success){
			var rmsg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><a href="?do=Formula&id='+data.success.id+'">'+data.success.msg+'</a></div>';
			reload_formulas_data();
		}
	  	$('#addFormulaMsg').html(rmsg);
		
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
				$("#CSVImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> File upload failed!</div>');
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

$('#export_json').click(function() {
	$('#JSONExportMsg').html('<div class="alert alert-info"><img src="/img/loading.gif"/>Please wait, export in progress....</div>');					 
	$.ajax({ 
    url: '/pages/operations.php', 
	type: 'GET',
    data: {
		action: 'exportFormulas',
		},
	dataType: 'json',
    success: function (data) {
		if(data.error){
			var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
		}else if(data.success){
			var msg = '<div class="alert alert-success">'+data.success+'</div>';
		}
	  	$('#JSONExportMsg').html(msg);
    }
  });
});

</script>
            
<!--ADD FORMULA MODAL-->
<div class="modal fade" id="add_formula" tabindex="-1" role="dialog" aria-labelledby="add_formula" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add a new formula</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="addFormulaMsg"></div>
        <table width="100%" border="0">  
          <tr>
            <td width="11%">Name:</td>
            <td width="89%"><input name="name" id="name" type="text" class="form-control" /></td>
          </tr>
          <tr>
            <td>Profile:</td>
            <td>
            <select name="profile" id="profile" class="form-control">
            <?php foreach ($fcat as $cat) { if($cat['type'] == 'profile'){?>		
                <option value="<?=$cat['cname']?>"><?=$cat['name']?></option>
            <?php } }?>
            </select>
            </td>
          </tr>
           <tr>
             <td>Purpose: </td>
             <td><select name="catClass" id="catClass" class="form-control ellipsis">
            <?php foreach ($cats as $IFRACategories) {?>
                <option value="cat<?php echo $IFRACategories['name'];?>" <?php echo ($settings['defCatClass']=='cat'.$IFRACategories['name'])?"selected=\"selected\"":""; ?>><?php echo 'Cat'.$IFRACategories['name'].' - '.$IFRACategories['description'];?></option>
            <?php }	?>
            </select></td>
           </tr>
            <tr>
                <td>Final type:</td>
                <td>
                <select name="finalType" id="finalType" class="form-control ellipsis">  
                    <option value="100">Concentrated (100%)</option>
					<?php foreach ($fTypes as $fType) {?>
                        <option value="<?php echo $fType['concentration'];?>" <?php echo ($info['finalType']==$fType['concentration'])?"selected=\"selected\"":""; ?>><?php echo $fType['name'].' ('.$fType['concentration'];?>%)</option>
                    <?php }	?>			
                </select>
                </td>
          </tr>     
          <tr>
            <td valign="top">Customer:</td>
              <td><select name="customer" id="customer" class="form-control ellipsis">
                <option value="0">Internal use</option>
                <?php foreach ($customer as $c) {?>
                <option value="<?=$c['id'];?>"><?=$c['name']?></option>
                <?php }	?>
              </select></td>
          </tr>
          <tr>
            <td valign="top">Notes:</td>
            <td><textarea name="notes" id="notes" cols="45" rows="5" class="form-control"></textarea></td>
          </tr>  
        </table>  
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="btnAdd" value="Add">
      </div>
    </div>
  </div>
</div>
</div>

<!--IMPORT FORMULA CSV MODAL-->
<div class="modal fade" id="add_formula_csv" tabindex="-1" role="dialog" aria-labelledby="add_formula_csv" aria-hidden="true">
  <div class="modal-dialog pv-modal-xxl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import formula from CSV</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="CSVImportMsg"></div>
      <div id=process_area>
      
        <div class="form-group">
            <label class="col-md-3 control-label">Formula name:</label>
            <div class="col-md-8">
              <input type="text" name="CSVname" id="CSVname" class="form-control"/>
            </div>
		</div>  
        <div class="form-group">
            <label class="col-md-3 control-label">Profile:</label>
            <div class="col-md-8">
             <select name="CSVProfile" id="CSVProfile" class="form-control">
             <?php foreach ($fcat as $cat) { if($cat['type'] == 'profile'){?>		
                <option value="<?=$cat['cname']?>"><?=$cat['name']?></option>
             <?php } }?>
             </select>
            </div>
		</div>
        <div class="form-group">
            <label class="col-md-3 control-label">CSV file:</label>
            <div class="col-md-8">
              <input type="file" name="CSVFile" id="CSVFile" class="form-control" />
            </div>
		</div>
        <div id="step_upload" class="modal-body"></div>
        <div class="col-md-12">
           <hr />
           <p>CSV format: <strong>ingredient,concentration,dilutant,quantity</strong></p>
           <p>Example: <em><strong>Ambroxan,10,TEC,0.15</strong></em></p>
        </div>
        
        </div>
        
      </div>
      
	  <div class="modal-footer">
        <input type="button" class="btn btn-secondary" data-dismiss="modal" id="btnCloseCsv" value="Cancel">
        <input type="submit" name="btnImport" class="btn btn-primary" id="btnImport" value="Import">
      </div>
   
  </div>
  
</div>
</div>

<!--EXPORT JSON MODAL-->
<div class="modal fade" id="export_formulas_json" tabindex="-1" role="dialog" aria-labelledby="export_formulas_json" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Export formulas as a JSON file</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group" id="JSONExportMsg">
            <p>This will generate a JSON file from your formulas. Once the file is generated you should download it to your computer.</p>
		</div>
      </div>
	  <div class="modal-footer">
        <input type="button" class="btn btn-secondary" data-dismiss="modal" id="btnCloseJSON" value="Cancel">
        <input type="submit" name="btnExport" class="btn btn-primary" id="export_json" value="Export">
      </div>   
  </div>
</div>
</div>

<!--IMPORT JSON MODAL-->
<div class="modal fade" id="import_formulas_json" tabindex="-1" role="dialog" aria-labelledby="import_formulas_json" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import formulas from a JSON file</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="JSRestMsg"></div>
      <div class="progress">  
         <div id="uploadProgressBar" class="progress-bar" role="progressbar" aria-valuemin="0"></div>
      </div>
      <div id="backupArea">
      
          <div class="form-group">
              <label class="col-md-3 control-label">JSON file:</label>
              <div class="col-md-8">
                 <input type="file" name="backupFile" id="backupFile" class="form-control" />
              </div>
          </div>
          <div class="col-md-12">
             <hr />
             <p><strong>IMPORTANT:</strong></p>
              <ul>
                <li><div id="raw" data-size="<?=getMaximumFileUploadSizeRaw()?>">Maximum file size: <strong><?=getMaximumFileUploadSize()?></strong></div></li>
                <li>Any formula with the same id will be replaced. Please make sure you have taken a backup before imporing a JSON file.</li>
              </ul>
    			<p>&nbsp;</p>
            </div>
          </div>
      
      </div>
	  <div class="modal-footer">
        <input type="button" class="btn btn-secondary" data-dismiss="modal" id="btnCloseBK" value="Cancel">
        <input type="submit" name="btnRestore" class="btn btn-primary" id="btnRestoreFormulas" value="Import">
      </div>
   
  </div>
  
</div>
</div>
<script src="/js/import.formulas.js"></script>

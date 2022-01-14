<?php 

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$getFCats = mysqli_query($conn, "SELECT name,cname,type FROM formulaCategories");
while($fcats = mysqli_fetch_array($getFCats)){
	$fcat[] =$fcats;
}
$cats_q = mysqli_query($conn, "SELECT id,name,description,type FROM IFRACategories ORDER BY id ASC");
while($cats_res = mysqli_fetch_array($cats_q)){
    $cats[] = $cats_res;
}
?>
            <table width="100%" border="0">
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td width="98%"><div class="text-right">
                  <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                    <div class="dropdown-menu dropdown-menu-right">
	                  <a class="dropdown-item" href="#" data-toggle="modal" data-target="#add_formula">Add new formula</a>
                      <a class="dropdown-item" href="#" data-toggle="modal" data-target="#add_formula_csv">Import from CSV</a>
                    </div>
                    </div>
                </div></td>
                <td width="2%">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2">
            <div class="card-body">
              <div class="table-responsive">
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
        <div class="tab-content">
            <div class="tab-pane" id="tab-all">
                <table id="all-table" class="table table-striped table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Formula Name</th>
                            <th>Product Name</th>
                            <th>Ingredients</th>
                            <th>Class</th>
                            <th>Created</th>
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
                            <th>Ingredients</th>
                            <th>Class</th>
                            <th>Created</th>
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
$( document ).ajaxComplete(function() {
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
});
$(".tabs").click(function() {
     var src = $(this).data("source");
     var tableId = $(this).data("table");
     initTable(tableId, src);
});
function initTable(tableId, src) {
    var table = $("#" + tableId).DataTable({
           ajax: {url: src},
			columns: [
			   { data : 'name', title: 'Formula Name', render: fName },
			   { data : 'product_name', title: 'Product Name', render: pName},
    		   { data : 'ingredients', title: 'Ingredients'},
			   { data : 'catClass', title: 'Class'},
			   { data : 'created', title: 'Created'},
			   { data : null, title: 'Actions', render: fActions},				   
			  ],
			 language: {
				loadingRecords: '&nbsp;',
				processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Blending...</span>',
				emptyTable: "No formulas added yet.",
				search: "Search for formula:"
			},
           order: [0,'asc'],
           columnDefs: [
				{ orderable: true, targets: [0] },
				{ className: 'text-center', targets: '_all' },				  
				],
	    destroy: true,
        bFilter: true,
        paging:  true,
		info:   true,
		lengthMenu: [[20, 35, 60, -1], [20, 35, 60, "All"]]
     });
}
initTable("all-table", "core/list_formula_data.php");

$("#listFormulas").tabs();


function fName(data, type, row, meta){
	if(type === 'display'){
		if(row.isProtected == 1){
			var pad = 'class="fas fa-lock" rel="tip" title="Formula is protected"';
		}else{
			var pad = 'class="fas fa-unlock"  rel="tip" title="Formula is not protected"';
		}
		data = '<div '+ pad +'</div><a href="?do=Formula&id=' + row.id + '" > ' + data + '</a>';
	}
  return data;
}
function pName(data, type, row, meta){
	if(type === 'display'){
		data = '<a class="popup-link" href="pages/getFormMeta.php?id=' + row.id + '">' + data + '</a>';
	}
  return data;
}
function fActions(data, type, row, meta){
	if(type === 'display'){
		data = '<a href="pages/getFormMeta.php?id=' + row.id + '" rel="tip" title="Show details of '+ row.name +'" class="fas fa-comment-dots popup-link"></a> &nbsp; <a href="#" id="addTODO" class="fas fa-list" rel="tip" title="Add '+ row.name +' to the make list" data-id='+ row.fid +'></a> &nbsp; <a href="#" id="cloneMe" class="fas fa-copy" rel="tip" title="Clone '+ row.name +'" data-id='+ row.fid +'></a> &nbsp; <a href="#" id="deleteMe" class="fas fa-trash" rel="tip" title="Delete '+ row.name +'" data-id='+ row.fid +' data-name="'+ row.name +'"></a>';
	}
    return data;
}

//Clone
$('table.table').on('click', '[id*=cloneMe]', function () {
	var formula = {};
	formula.ID = $(this).attr('data-id');
	$.ajax({ 
		url: 'pages/manageFormula.php', 
		type: 'GET',
		data: {
			action: "clone",
			formula: formula.ID,
			},
		dataType: 'html',
		success: function (data) {
			if ( data.indexOf("Error") > -1 ) {
				$('#inMsg').html(data); 
			}else{
				$('#inMsg').html(data);
				reload_formulas_data();
			}
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
					url: 'pages/manageFormula.php', 
					type: 'GET',
					data: {
						action: "delete",
						fid: formula.ID,
						},
					dataType: 'html',
					success: function (data) {
						$('#inMsg').html(data);
						reload_formulas_data();
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
	$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'GET',
    data: {
		action: 'todo',
		fid: formula.ID,
		add: true,
		},
	dataType: 'html',
    success: function (data) {
	  	$('#inMsg').html(data);
    }
  });
});

function add_formula() {
	$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'POST',
    data: {
		action: 'addFormula',
		name: $("#name").val(),
		profile: $("#profile").val(),
		catClass: $("#catClass").val(),
		finalType: $("#finalType").val(),
		notes: $("#notes").val(),
		},
	dataType: 'html',
    success: function (data) {
	  	$('#addFormulaMsg').html(data);
    }
  });
};

function add_formula_csv() {
    $("#CSVImportMsg").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
	$("#btnImport").prop("disabled", true);
		
	var fd = new FormData();
    var files = $('#CSVFile')[0].files;
    var name = $('#CSVname').val();
    var profile = $('#CSVProfile').val();
	var addMissIng = $('#addMissIng').is(':checked');

       if(files.length > 0 ){
        fd.append('CSVFile',files[0]);
        $.ajax({
           url: 'pages/upload.php?type=frmCSVImport&name=' + name + '&profile=' + profile + '&addMissIng=' + addMissIng,
           type: 'post',
           data: fd,
           contentType: false,
           processData: false,
		         cache: false,
           success: function(response){
             if(response != 0){
               $("#CSVImportMsg").html(response);
				$("#btnImport").prop("disabled", false);
              }else{
                $("#CSVImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> File upload failed!</div>');
				$("#btnImport").prop("disabled", false);
              }
            },
         });
  }else{
	$("#CSVImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a file to upload!</div>');
	$("#btnImport").prop("disabled", false);
  }
};

function reload_formulas_data() {
	
    $('#all-table').DataTable().ajax.reload(null, true);
	
};

</script>
            
<!--ADD FORMULA MODAL-->
<div class="modal fade" id="add_formula" tabindex="-1" role="dialog" aria-labelledby="add_formula" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="add_formula">Add a new formula</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="addFormulaMsg"></div>
  	  <form action="javascript:add_formula()" id="form1">
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
                        <option value="<?=$settings['Parfum']?>" <?php if($settings['Parfum'] == $meta['finalType']){ echo 'selected';}?>>Parfum (<?=$settings['Parfum']?>%)</option>
                        <option value="<?=$settings['EDP']?>" <?php if($settings['Parfum'] == $meta['finalType']){ echo 'selected';}?>>EDP (<?=$settings['EDP']?>%)</option>
                        <option value="<?=$settings['EDT']?>" <?php if($settings['Parfum'] == $meta['finalType']){ echo 'selected';}?>>EDT (<?=$settings['EDT']?>%)</option>
                        <option value="<?=$settings['EDC']?>" <?php if($settings['Parfum'] == $meta['finalType']){ echo 'selected';}?>>EDC (<?=$settings['EDC']?>%)</option>		
                </select>
                </td>
              </tr>     
        <tr>
           	<td valign="top">Notes:</td>
            <td><textarea name="notes" id="notes" cols="45" rows="5" class="form-control"></textarea></td>
           </tr>  
      </table>  
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="btnAdd" value="Add">
      </div>
     </form>
    </div>
  </div>
</div>
</div>

<!--IMPORT FORMULA CSV MODAL-->
<div class="modal fade" id="add_formula_csv" tabindex="-1" role="dialog" aria-labelledby="add_formula_csv" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="add_formula_csv">Import formula from CSV</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="CSVImportMsg"></div>
	<form method="post" action="javascript:add_formula_csv()" enctype="multipart/form-data" id="csvform">
               <table width="100%" border="0">
                              <tr>
                                <td>Name:</td>
                                <td><input type="text" name="CSVname" id="CSVname" class="form-control"/></td>
                              </tr>
                              <tr>
                                <td>Profile:</td>
                                <td>
                                <select name="CSVProfile" id="CSVProfile" class="form-control">
                                 <?php foreach ($fcat as $cat) { if($cat['type'] == 'profile'){?>		
                                    <option value="<?=$cat['cname']?>"><?=$cat['name']?></option>
            					 <?php } }?>
                                 </select>
                                </td>
                              </tr>
                              <tr>
                                <td width="22%">Choose file:</td>
                                <td width="78%">
                                  <input type="file" name="CSVFile" id="CSVFile" class="form-control" />
                                </td>
                              </tr>
                              <tr>
                                <td>Add missing ingredients:</td>
                                <td><input name="addMissIng" type="checkbox" id="addMissIng" /></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td colspan="2"><p>CSV format: <strong>ingredient,concentration,dilutant,quantity</strong></p>
                                <p>Example: <em><strong>Ambroxan,10,TEC,0.15</strong></em></p></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                 </table>
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="btnImport" class="btn btn-primary" id="btnImport" value="Import">
      </div>
     </form>
    </div>
  </div>
</div>
</div>
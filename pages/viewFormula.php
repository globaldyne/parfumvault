<?php 
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('..//inc/opendb.php');
require_once('../inc/settings.php');

if(!$_GET['id']){
	echo 'Formula id is missing.';
	return;
}
	
$id = mysqli_real_escape_string($conn, $_GET['id']);

if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE id = '$id'")) == 0){
	echo '<div class="alert alert-info alert-dismissible">Incomplete formula. Please add ingredients.</div>';
	return;
}
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,fid,name,isProtected,finalType,defView FROM formulasMetaData WHERE id = '$id'"));
$f_name = base64_decode($meta['fid']);


?>
<?php if(isset($_GET['standalone'])){ ?>
<link href="../css/fontawesome-free/css/all.min.css" rel="stylesheet">
<script src="../js/jquery/jquery.min.js"></script>
<link rel="stylesheet" href="../css/bootstrap.min.css">
<script src="../js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="../css/datatables.min.css"/>
<script type="text/javascript" src="../js/datatables.min.js"></script>
<link rel="stylesheet" href="../css/vault.css">
<script src="../js/magnific-popup.js"></script>
<link href="../css/magnific-popup.css" rel="stylesheet" />
<link href="../css/bootstrap-editable.css" rel="stylesheet">
<script type="text/javascript" src="../js/bootbox.min.js"></script>
<script src="../js/bootstrap-editable.js"></script>
<?php } ?>

<script>

$(document).ready(function() {
  var groupColumn = 0;
  var formula_table = $('#formula').DataTable( {
		"columnDefs": [
            { visible: false, targets: groupColumn },
			{ className: 'text-center', targets: '_all' },
        ],
		dom: 'lfrtip',
		"processing": true,
        "language": {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: "Incomplete formula. Please add ingredients.",
			search: "Search in formula:"
			},
    	ajax: {
    		url: '/core/full_formula_data.php?id=<?=$id?>'
 		 },
		 columns: [
				   { data : 'profile', title: 'Profile' },
				   { data : 'ingredient', title: 'Ingredient', render: ingName},
    			   { data : 'cas', title: 'CAS#'},
				   { data : 'purity', title: 'Purity %', render: ingConc},
				   { data : 'dilutant', title: 'Dilutant', render: ingSolvent},
				   { data : 'quantity', title: 'Quantity (<?=$settings['mUnit']?>)', render: ingQuantity},
				   { data : 'concentration', title: 'Concentration %'},
				   { data : 'final_concentration', title: 'Final Concentration <?=$meta['finalType']?>%'},
				   { data : 'cost', title: 'Cost (<?=$settings['currency']?>)'},
				   { data : 'desc', title: 'Properties', render: ingNotes},
   				   { data : null, title: 'Actions', className: 'text-center noexport', render: ingActions},		   
				   
				  ],

  		footerCallback : function( tfoot, data, start, end, display ) {    
      
		  var response = this.api().ajax.json();
		  if(response){
			 var $td = $(tfoot).find('th');
			 $td.eq(0).html("Total Ingredients: " + response.meta['total_ingredients'] );
			 $td.eq(4).html("Total: " + response.meta['total_quantity']);// + response.meta['quantity_unit'] );
			 $td.eq(5).html("Total: " + response.meta['concentration'] + "%" );
			 $td.eq(7).html("Total: " + response.meta['currency'] + response.meta['total_cost'] );
		 }
      },
	  
        "order": [[ groupColumn, 'desc' ]],
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
        "pageLength": 100,
		"displayLength": 100,
		"createdRow": function( row, data, dataIndex){
			if( data['usage_regulator'] == "IFRA" && parseFloat(data['usage_limit']) < parseFloat(data['concentration'])){
				$(row).find('td:eq(5)').addClass('alert-danger').append(' <a href="#" rel="tip" title="Max usage: ' + data['usage_limit'] +'%" class="fas fa-info-circle"></a></div>');
			}else if( data['usage_regulator'] == "PV" && parseFloat(data['usage_limit']) < parseFloat(data['concentration'])){
				$(row).find('td:eq(5)').addClass('alert-info');
            }else{
				$(row).find('td:eq(5)').addClass('alert-success');
			}
			
			if( data['usage_regulator'] == "IFRA" && parseFloat(data['usage_limit']) < parseFloat(data['final_concentration'])){
				$(row).find('td:eq(6)').addClass('alert-danger');
			}else if( data['usage_regulator'] == "PV" && parseFloat(data['usage_limit']) < parseFloat(data['final_concentration'])){
				$(row).find('td:eq(6)').addClass('alert-info');
			}else{
				$(row).find('td:eq(6)').addClass('alert-success');
			}
       },
	   "drawCallback": function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last = null;
 
            api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group noexport"><td colspan="' + rows.columns()[0].length +'">' + group + ' Notes</td></tr>'
                    );
                    last = group;
                }
            } );
	   }
});

// Order by the grouping
$('#formula tbody').on( 'click', 'tr.group', function () {
    var currentOrder = table.order()[0];
    if ( currentOrder[0] === groupColumn && currentOrder[1] === 'asc' ) {
         table.order( [ groupColumn, 'desc' ] ).draw();
    }else {
         table.order( [ groupColumn, 'asc' ] ).draw();
    }
});
	
	
$('#formula').on('click', '[id*=rmIng]', function () {
	var ing = {};
	ing.ID = $(this).attr('data-id');
	ing.Name = $(this).attr('data-name');
    
	bootbox.dialog({
       title: "Confirm ingredient removal",
       message : 'Remove <strong>'+ $(this).attr('data-name') +'</strong> from formula?',
       buttons :{
           main: {
               label : "Remove",
               className : "btn-primary",
               callback: function (){
	    			
				$.ajax({ 
					url: 'pages/manageFormula.php', 
					type: 'GET',
					data: {
						action: "deleteIng",
						fname: "<?=$meta['name']?>",
						ingID: ing.ID,
						ing: ing.Name
						},
					dataType: 'html',
					success: function (data) {
						$('#msgInfo').html(data);
						reload_formula_data();
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
	
	update_bar();
	
});//doc ready

function update_bar(){
     $.getJSON("/core/full_formula_data.php?id=<?=$id?>&stats_only=1", function (json) {
		
        var top = Math.round(json.stats.top);
		var top_max = Math.round(json.stats.top_max);
		
		var heart = Math.round(json.stats.heart);
		var heart_max = Math.round(json.stats.heart_max);
		
        var base = Math.round(json.stats.base);
        var base_max = Math.round(json.stats.base_max);

		//console.log(top_max);

		$('#top_bar').attr('aria-valuenow', top).css('width', top+'%').attr('aria-valuemax', top_max);
		$('#heart_bar').attr('aria-valuenow', heart).css('width', heart+'%').attr('aria-valuemax', heart_max);
		$('#base_bar').attr('aria-valuenow', base).css('width', base+'%').attr('aria-valuemax', base_max);;
        
		$('.top-label').html(top + "% Top Notes");
        $('.heart-label').html(heart + "% Heart Notes");
        $('.base-label').html(base + "% Base Notes");
	}); 
};

function reload_formula_data() {
    $('#formula').DataTable().ajax.reload(null, true);
	update_bar();
};


</script>

<table class="table table-striped table-bordered nowrap" width="100%" cellspacing="0">
	<thead>
   	 <tr class="noexport">
       <th colspan="10">
        <div class="progress">  
              <div id="base_bar" class="progress-bar pv_bar_base_notes" role="progressbar" aria-valuemin="0"><span><div class="base-label"></div></span></div>
              <div id="heart_bar" class="progress-bar pv_bar_heart_notes" role="progressbar" aria-valuemin="0"><span><div class="heart-label"></div></span></div>
              <div id="top_bar" class="progress-bar pv_bar_top_notes" role="progressbar" aria-valuemin="0"><span><div class="top-label"></div></span></div>
        </div>
	</th>
	<th>
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
            <div class="dropdown-menu dropdown-menu-left">
               <a class="dropdown-item" href="javascript:export_as('csv')">Export to CSV</a>
               <a class="dropdown-item" href="javascript:export_as('pdf')">Export to PDF</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="javascript:manageQuantity('multiply')">Multiply x2</a>
               <a class="dropdown-item" href="javascript:manageQuantity('divide')">Divide x2</a>
               <a class="dropdown-item" href="javascript:cloneMe()">Clone Formula</a>
               <a class="dropdown-item" href="#" data-toggle="modal" data-target="#amount_to_make">Amount to make</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="#" data-toggle="modal" data-target="#create_accord">Create Accord</a>
               <a class="dropdown-item" href="#" data-toggle="modal" data-target="#conv_ingredient">Convert to ingredient</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="javascript:addTODO()">Add to the make list</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="pages/viewHistory.php?id=<?=$meta['id']?>" target="_blank">View history</a>
            </div>
        </div>
	</th>
</tr>
</table>
<div id="msgInfo"></div>
<table id="formula" class="table table-striped table-bordered nowrap viewFormula" style="width:100%">
        <thead>
            <tr>
                <th>Profile</th>
                <th>Ingredient</th>
                <th>CAS</th>
                <th>Purity %</th>
                <th>Dilutant</th>
                <th>Quantity</th>
                <th>Concentration %*</th>
                <th>Final Concentration %</th>
                <th>Cost</th>
                <th>Properties</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tfoot>
        	<tr>
            <th>Total ingredients:</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Total ml:</th>
            <th>Total conc %</th>
            <th></th>
            <th>Cost: </th>
            <th></th>
            <th></th>
            </tr>
        </tfoot>
</table>

<!--Amount To Make-->
<div class="modal fade" id="amount_to_make" tabindex="-1" role="dialog" aria-labelledby="amount_to_make" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="amount_to_make">Total amount to make</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="amountToMakeMsg"></div>
  	  <form action="javascript:amountToMake()" method="get" name="form1" target="_self" id="form_amount_to_make"><p></p>
        <table width="313" border="0">
          <tr>
	       <td width="66" height="31"><strong>SG<span class="sup">*</span> :</strong></td>
	       <td width="237"><input name="sg" type="text" id="sg" value="0.985" />
            <strong>ml</strong></td>
          </tr>
	     <tr>
	       <td><strong>Amount:</strong></td>
	       <td><input name="totalAmount" type="text" id="totalAmount" value="100" />
            <strong>ml</strong></td>
          </tr>
        </table>
	    <p>&nbsp;</p>
	    <p>*<a href="https://www.jbparfum.com/knowledge-base/3-specific-gravity-sg/" target="_blank">Specific Gravity of Ethanol</a></p>
	    <div class="modal-footer">
	     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
  		 <input type="submit" name="button" class="btn btn-primary" id="btnUpdate" value="Update Formula">
	   </div>
     </form>
    </div>
  </div>
 </div>
</div>

<!--Create accord-->
<div class="modal fade" id="create_accord" tabindex="-1" role="dialog" aria-labelledby="create_accord" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="create_accord">Create accord</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="accordMsg"></div>
  	  <form action="javascript:createAccord()" method="get" name="form1" target="_self" id="form_create_accord"><p></p>
        <table width="313" border="0">
          <tr>
	       <td width="106" height="31"><strong>Accord from:</strong></td>
	       <td width="197"><label>
	         <select name="accordProfile" id="accordProfile" class="form-control">
	           <option value="Top">Top notes</option>
	           <option value="Heart">Heart Notes</option>
	           <option value="Base">Base Notes</option>
	           </select>
	         </label></td>
          </tr>
	     <tr>
	       <td><strong>Name:</strong></td>
	       <td><input name="accordName" type="text" class="form-control" id="accordName" value="<?=$f_name?> accord" /></td>
          </tr>
        </table>
	    <p>&nbsp;</p>
	    <div class="modal-footer">
	     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
  		 <input type="submit" name="button" class="btn btn-primary" id="btnUpdate" value="Create">
	   </div>
     </form>
    </div>
  </div>
 </div>
</div>

<!--Convert to ingredient-->
<div class="modal fade" id="conv_ingredient" tabindex="-1" role="dialog" aria-labelledby="conv_ingredient" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="conv_ingredient">Convert formula to ingredient</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="cnvMsg"></div>
  	  <form action="javascript:conv2ing()" method="get" name="form1" target="_self" id="form_conv_ingredient"><p></p>
        <table width="313" border="0">
	     <tr>
	       <td><strong>Name:</strong></td>
	       <td><input name="ingName" type="text" class="form-control" id="ingName" value="<?=$f_name?>" /></td>
          </tr>
        </table>
	    <p>&nbsp;</p>
	    <div class="modal-footer">
	     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
  		 <input type="submit" name="button" class="btn btn-primary" id="btnUpdate" value="Convert">
	   </div>
     </form>
    </div>
  </div>
 </div>
</div>


<script>
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
  
$('#formula').editable({
	  container: 'body',
	  selector: 'a.concentration',
	  url: "pages/update_data.php?formula=<?=$meta['fid']?>",
	  title: 'Purity %',
	  type: "POST",
	  dataType: 'json',
			success: function(response, newValue) {
			if(response.status == 'error'){
				return response.msg; 
			}else{
				reload_formula_data();
			}
		},
	  validate: function(value){
	   if($.trim(value) == ''){
		return 'This field is required';
	   }
	   if($.isNumeric(value) == '' ){
		return 'Numbers only!';
	   }
	  }
	});
	
	$('#formula').editable({
		container: 'body',
		selector: 'a.solvent',
		type: 'POST',
		emptytext: "",
		emptyclass: "",
		url: "pages/update_data.php?formula=<?=$meta['fid']?>",
		title: 'Choose solvent',
		source: [
				 <?php
					$res_ing = mysqli_query($conn, "SELECT id, name FROM ingredients WHERE type = 'Solvent' OR type = 'Carrier' ORDER BY name ASC");
					while ($r_ing = mysqli_fetch_array($res_ing)){
					echo '{value: "'.$r_ing['name'].'", text: "'.$r_ing['name'].'"},';
				}
				?>
			  ],
		dataType: 'json',
		success: function(response, newValue) {
			if(response.status == 'error'){
				return response.msg; 
			}else{
				reload_formula_data();
			}
		}
    
	});
	
	$('#formula').editable({
	  container: 'body',
	  selector: 'a.quantity',
	  url: "pages/update_data.php?formula=<?=$meta['fid']?>",
	  title: 'Quantity in <?=$settings['mUnit']?>',
	  type: "POST",
	  dataType: 'json',
		  success: function(response, newValue) {
			if(response.status == 'error'){
				return response.msg; 
			}else{ 
				reload_formula_data();
			}
		},
	  validate: function(value){
	   if($.trim(value) == ''){
		return 'This field is required';
	   }
	   if($.isNumeric(value) == '' ){
		return 'Numbers only!';
	   }
	  }
    });
		
	$('#formula').editable({
	  container: 'body',
	  selector: 'a.notes',
	  url: "pages/update_data.php?formula=<?=base64_encode($f_name)?>",
	  title: 'Notes',
	  type: "POST",
	  dataType: 'json',
			success: function(response, newValue) {
			if(response.status == 'error'){
				return response.msg; 
			}else{
				reload_formula_data();
			}
		},
	});

	function ingName(data, type, row, meta){
		
		if(row.chk_ingredient){
			var chkIng = '<a href="#" class="fas fa-exclamation" rel="tip" title="'+row.chk_ingredient+'"></a>';
		}else{
			var chkIng = '';
		}
		if(row.profile_plain){
			var profile_class = '<a href="#" class="'+row.profile_plain+'"></a>';
		}else{
			var profile_class ='';
		}
		if(type === 'display'){
			data = '<a class="popup-link" href="pages/mgmIngredient.php?id=' + row.enc_id + '">' + data + '</a> '+ chkIng + profile_class;
		}

  	  return data;
  }

	function ingConc(data, type, row, meta){
	  if(type === 'display'){
		  <?php if($meta['isProtected'] == FALSE){?>
		  data = '<a href="#" data-name="concentration" class="concentration" data-type="text" data-pk="' + row.ingredient + '">' + data + '</a>';
		  <?php } ?>
	  }

  	  return data;
  	}
  
  function ingSolvent(data, type, row, meta){
	  if(type === 'display'){
		<?php if($meta['isProtected'] == FALSE){?>
		  if(row.purity !== 100){
		  	data = '<a href="#" data-name="dilutant" class="solvent" data-type="select" data-pk="' + row.ingredient + '">' + data + '</a>';
	  	}else{
			data = 'None';
		}
		<?php } ?>
	  }
  	  return data;
  }
  
  function ingQuantity(data, type, row, meta){
	if(type === 'display'){
		<?php if($meta['isProtected'] == FALSE){?>
		data = '<a href="#" data-name="quantity" class="quantity" data-type="text" data-pk="' + row.ingredient + '">' + data + '</a>';
		<?php } ?>
	}
    return data;
  }

  function ingNotes(data, type, row, meta){
	 if(type === 'display'){
	  <?php if($meta['defView'] == '1'){ $show = 'properties'; }elseif($meta['defView'] == '2'){ $show = 'notes';}?>
	  <?php if($meta['isProtected'] == FALSE){?>
	  data = '<a href="#" data-name="<?=$show?>" class="<?=$show?>" data-type="textarea" data-pk="' + row.ingredient + '">' + data + '</a>';
	  <?php } ?>
	 }
   return data;
  }
  function ingActions(data, type, row, meta){
	//Change ingredient
	$('#formula').editable({
		selector: 'a.replaceIngredient',
		pvnoresp: false,
		highlight: false,
		type: 'get',
		emptytext: "",
		emptyclass: "",
		url: "pages/manageFormula.php?action=repIng&fname=<?=$meta['name']?>",
		source: [
				 <?php
					$res_ing = mysqli_query($conn, "SELECT name FROM ingredients ORDER BY name ASC");
					while ($r_ing = mysqli_fetch_array($res_ing)){
						echo '{value: "'.htmlspecialchars($r_ing['name']).'", text: "'.htmlspecialchars($r_ing['name']).'"},';
				}
				?>
			  ],
		dataType: 'html',
		success: function (data) {
			if ( data.indexOf("Error") > -1 ) {
				$('#msgInfo').html(data); 
			}else{
				$('#msgInfo').html(data);
				reload_formula_data();
			}
		}
	});

	if(type === 'display'){
		data = '<a href="'+ row.pref_supplier_link +'" target="_blank" rel="tip" title="Open '+ row.pref_supplier +' page" class="fas fa-shopping-cart"></a>';
		<?php if($meta['isProtected'] == FALSE){?>
		data += '&nbsp; <a href="#" class="fas fa-exchange-alt replaceIngredient" rel="tip" title="Replace '+ row.ingredient +'"  data-name="'+ row.ingredient +'" data-type="select" data-pk="'+ row.ingredient +'" data-title="Choose Ingredient to replace '+ row.ingredient +'"></a> &nbsp; <a href="#" rel="tip" title="Remove '+ row.ingredient +'" class="fas fa-trash" id="rmIng" data-name="'+ row.ingredient +'" data-id='+ row.formula_ingredient_id +'></a>';
		<?php } ?>
	}
    return data;
  }

//MULTIPLY - DIVIDE
function manageQuantity(quantity) {
	$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		do: quantity,
		formula: "<?php echo $f_name; ?>",
		},
	dataType: 'html',
    success: function (data) {
	  	$('#msgInfo').html(data);
		reload_formula_data();
    }
  });
};

//AMOUNT TO MAKE
function amountToMake() {
	if($("#sg").val().trim() == '' ){
        $('#sg').focus();
	  	$('#amountToMakeMsg').html('<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> all fields required!</div>');
	}else if($("#totalAmount").val().trim() == '' ){
 		$('#totalAmount').focus();
	  	$('#amountToMakeMsg').html('<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> all fields required!</div>');		
	}else{
		$.ajax({ 
		url: 'pages/manageFormula.php', 
		type: 'get',
		cache: false,
		data: {
			fid: "<?php echo base64_encode($f_name); ?>",
			SG: $("#sg").val(),
			amount: $("#totalAmount").val(),
			},
		dataType: 'html',
		success: function (data) {
			$('#amountToMakeMsg').html(data);
			$('#amount_to_make').modal('toggle');
			reload_formula_data();
		}
	  });
	}
};


//Create Accord
function createAccord() {
	if($("#accordName").val().trim() == '' ){
        $('#accordName').focus();
	  	$('#accordMsg').html('<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> Accord name required!</div>');	
	}else{
		$.ajax({ 
		url: 'pages/manageFormula.php', 
		type: 'POST',
		cache: false,
		data: {
			fid: "<?php echo base64_encode($f_name); ?>",
			accordName: $("#accordName").val(),
			accordProfile: $("#accordProfile").val(),
			},
		dataType: 'html',
		success: function (data) {
			$('#accordMsg').html(data);
			//$('#createAccord').modal('toggle');
		}
	  });
	}
};

//Convert to ingredient
function conv2ing() {	  
if($("#ingName").val().trim() == '' ){
        $('#ingName').focus();
	  	$('#cnvMsg').html('<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> Ingredient name required!</div>');	
	}else{
		$.ajax({ 
		url: 'pages/manageFormula.php', 
		type: 'POST',
		cache: false,
		data: {
			formula: "<?=base64_encode($f_name)?>",
			ingName: $("#ingName").val(),
			action: 'conv2ing',
			},
		dataType: 'html',
		success: function (data) {
			$('#cnvMsg').html(data);
			//$('#conv_ingredient').modal('toggle');
		}
	  });
	}
};

//Clone
function cloneMe() {	  
$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: "clone",
		formula: "<?=$meta['fid']?>",
		},
	dataType: 'html',
    success: function (data) {
        if ( data.indexOf("Error") > -1 ) {
			$('#msgInfo').html(data); 
		}else{
			$('#msgInfo').html(data);
		}
    }
  });
};

//Add in TODO
function addTODO() {
	$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: 'todo',
		fid: "<?php echo base64_encode($f_name); ?>",
		add: true,
		},
	dataType: 'html',
    success: function (data) {
	  	$('#msgInfo').html(data);
    }
  });
};


function export_as(type) {
  $("#formula").tableHTMLExport({
	type: type,
	filename:'<?php echo $f_name; ?>.csv',
	separator: ',',
  	newline: '\r\n',
  	trimContent: true,
  	quoteFields: true,
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',
	htmlContent: false,
	orientation: 'l',
	maintitle: '<?=$f_name?>',
  });
};
</script>
<?php 
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');

require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


$id = mysqli_real_escape_string($conn, $_GET['id']);


$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,fid,name,isProtected,finalType,defView,notes FROM formulasMetaData WHERE id = '$id'"));
$f_name = $meta['name'];
$fid = $meta['fid'];
?>

<script>
var myFID = "<?=$meta['fid']?>";
var myFNAME = "<?=$meta['name']?>";
var myID = "<?=$meta['id']?>";
var isProtected = true;
<?php if($meta['isProtected'] == FALSE){?>
	 isProtected = false;
<?php } ?>
var reCalc=0;
$(document).ready(function() {
						   
  var groupColumn = 0;
  var formula_table = $('#formula').DataTable( {
		columnDefs: [
            { visible: false, targets: groupColumn },
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [10, 11] },
        ],
		search: {
    		search: "<?=$_GET['search']?>"
  		},
		dom: 'lfrtip',
			buttons: [{
				extend: 'print',
				title: "<?=$meta['name']?>",
				exportOptions: {
     				columns: [1, 2, 3, 4, 5, 6, 7, 8, 10]
  				},
			  }],
		processing: true,
			  mark: true,
        language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Blending...</span>',
			emptyTable: '<div class="alert alert-warning"><strong>Empty formula. Please add ingredients.</strong></div>',
			search: "Search in formula:",
			searchPlaceholder: "CAS, Ingredient, etc.."
			},
    	ajax: {
    		url: '/core/full_formula_data.php?id=<?=$id?>'
 		 },
		 columns: [
				   { data : 'ingredient.profile', title: 'Profile' },
				   { data : 'ingredient.name', title: 'Ingredient', render: ingName},
    			   { data : 'ingredient.cas', title: 'CAS #', render: ingCAS},
				   { data : 'purity', title: 'Purity %', render: ingConc},
				   { data : 'dilutant', title: 'Dilutant', render: ingSolvent},
				   { data : 'quantity', title: 'Quantity (<?=$settings['mUnit']?>)', render: ingQuantity},
				   { data : 'concentration', title: 'Concentration 100%', render: ingSetConc},
				   { data : 'final_concentration', title: 'Final Concentration <?=$meta['finalType']?>%'},
				   { data : 'cost', title: 'Cost (<?=$settings['currency']?>)'},
				   { data : 'ingredient.inventory.stock', title: 'Inventory', className: 'text-center noexport', render: ingInv },
				   { data : 'ingredient.desc', title: 'Properties', render: ingNotes},
   				   { data : null, title: '', className: 'text-center noexport', render: ingActions},		   
				   
				  ],
		fixedHeader: {
			"header": true,
            "footer": true
        },
  		footerCallback : function( tfoot, data, start, end, display ) {    
      
		  var response = this.api().ajax.json();
		  if(response){
			 var $td = $(tfoot).find('th');
			 $td.eq(0).html("Ingredients: " + response.meta['total_ingredients'] );
			 $td.eq(4).html("Total: " + response.meta['total_quantity']);// + response.meta['quantity_unit'] );
			 $td.eq(5).html("Total: " + response.meta['concentration'] + "%" );
			 $td.eq(7).html("Total: " + response.meta['currency'] + response.meta['total_cost'] + ' <i rel="tip" title="The total price for the 100% concentration." class="pv_point_gen fas fa-info-circle"></i>');
			 $(formula_table.columns(7).header()).html("Final Concentration " + response.meta['product_concentration'] + "%");
		 }
      },
	  
        order: [[ groupColumn, 'desc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
        pageLength: 100,
		displayLength: 100,
		createdRow: function( row, data, dataIndex){
			if( data['usage_regulator'] == "IFRA" && parseFloat(data['usage_limit']) < parseFloat(data['concentration'])){
				$(row).find('td:eq(5)').addClass('alert-danger').append(' <i rel="tip" title="Max usage: ' + data['usage_limit'] +'% IFRA Regulated" class="pv_point_gen fas fa-info-circle"></i></div>');
			}else if( data['usage_regulator'] == "PV" && parseFloat(data['usage_limit']) < parseFloat(data['concentration'])){
				if(data['usage_restriction'] == 1){
					$(row).find('td:eq(5)').addClass('alert-info').append(' <i rel="tip" title="Recommended usage: ' + data['usage_limit'] +'%" class="pv_point_gen fas fa-info-circle"></i></div>');
				}
				if(data['usage_restriction'] == 2){
					$(row).find('td:eq(5)').addClass('alert-danger').append(' <i rel="tip" title="Restricted usage: ' + data['usage_limit'] +'%" class="pv_point_gen fas fa-info-circle"></i></div>');
				}
				if(data['usage_restriction'] == 3){
					$(row).find('td:eq(5)').addClass('alert-warning').append(' <i rel="tip" title="Specification: ' + data['usage_limit'] +'%" class="pv_point_gen fas fa-info-circle"></i></div>');
				}

            }else{
				$(row).find('td:eq(5)').addClass('alert-success');
			}
			
			if(data.ingredient.classification == 4){
				$(row).find('td:eq(0),td:eq(1),td:eq(5),td:eq(6)').addClass('alert-danger').append(' <i rel="tip" title="This material is prohibited" class="pv_point_gen fas fa-ban"></i></div>');
            }
			
			if( data['usage_regulator'] == "IFRA" && parseFloat(data['usage_limit']) < parseFloat(data['final_concentration'])){
				$(row).find('td:eq(6)').addClass('alert-danger').append(' <i rel="tip" title="Max usage: ' + data['usage_limit'] +'% IFRA Regulated" class="pv_point_gen fas fa-info-circle"></i></div>');
			}else if( data['usage_regulator'] == "PV" && parseFloat(data['usage_limit']) < parseFloat(data['final_concentration'])){
				if(data['usage_restriction'] == 1){
					$(row).find('td:eq(6)').addClass('alert-info').append(' <i rel="tip" title="Recommended usage: ' + data['usage_limit'] +'%" class="pv_point_gen fas fa-info-circle"></i></div>');
				}
				if(data['usage_restriction'] == 2){
					$(row).find('td:eq(6)').addClass('alert-danger').append(' <i rel="tip" title="Restricted usage: ' + data['usage_limit'] +'%" class="pv_point_gen fas fa-info-circle"></i></div>');
				}
				if(data['usage_restriction'] == 3){
					$(row).find('td:eq(6)').addClass('alert-warning').append(' <i rel="tip" title="Specification: ' + data['usage_limit'] +'%" class="pv_point_gen fas fa-info-circle"></i></div>');
				}
			}else{
				$(row).find('td:eq(6)').addClass('alert-success');
			}
			
       },
	   drawCallback: function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last = null;
         	$("#formula").wrap( "<div class='table-responsive'></div>" );

            api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group noexport"><td colspan="' + rows.columns()[0].length +'"><div class="' + group + '_notes">' + group + ' Notes</div></td></tr>'
                    );
                    last = group;
                }
            });
			extrasShow();
	   }
});

$('#formula_tab').on( 'click', function () {
	formula_table.fixedHeader.enable();
});

$('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
	formula_table.fixedHeader.adjust();
});

// Order by the grouping
$('#formula tbody').on( 'click', 'tr.group', function () {
    var currentOrder = formula_table.order()[0];
    if ( currentOrder[0] === groupColumn && currentOrder[1] === 'asc' ) {
         formula_table.order( [ groupColumn, 'desc' ] ).draw();
    }else {
         formula_table.order( [ groupColumn, 'asc' ] ).draw();
    }
});


$('#formula').on('click', '[id*=rmIng]', function () {

	var ing = {};
	ing.ID = $(this).attr('data-id');
	ing.Name = $(this).attr('data-name');
    ing.ingredient_id = $(this).attr('data-ingredient-id');
	
	bootbox.dialog({
       title: "Confirm ingredient removal",
       message : '<div id="err"></div>'+
	   			 'Remove <strong>'+ ing.Name +'</strong> from formula?' +
	   			 '<div class="dropdown-divider"></div>'+
        		  '<input type="checkbox" name="reCalcDel" id="reCalcDel" value="1" data-val="1" /> Adjust solvent'+
        			'<div id="slvMetaDel">'+
        				'<div class="dropdown-divider"></div>'+
        				'<input name="formulaSolventsDel" id="formulaSolventsDel" type="text" class="formulaSolventsDel pv-form-control">'+
        				'<div class="dropdown-divider"></div>'+
        				'<div id="explain" class="alert alert-info">The deducted ingredient quantity will be added to the selected solvent.</div></div>',
       buttons :{
           main: {
               label : "Remove",
               className : "btn-danger",
               callback: function (){
	    			
				$.ajax({ 
					url: '/pages/manageFormula.php', 
					type: 'POST',
					data: {
						action: "deleteIng",
						fid: myFID,
						ingID: ing.ID,
						reCalc: $("#reCalcDel").prop('checked'),
						formulaSolventID: $("#formulaSolventsDel").val(),
						ingredient_id: ing.ingredient_id,
						ing: ing.Name
						},
					dataType: 'json',
					success: function (data) {
						if(data.success){
							msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
							$('#msgInfo').html(msg);
							reload_formula_data();
							bootbox.hideAll();
						}else{
							$('#err').html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>');
						}
						
					}						
				  });
                 return false;
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
   }).on('shown.bs.modal', function(e){
			$("#slvMetaDel").hide();
			$("#reCalcDel").click(function() {
    		if($(this).is(":checked")) {
        		$("#slvMetaDel").show();
    		} else {
        		$("#slvMetaDel").hide();
    		}
		});
    
		$("#formulaSolventsDel").select2({
			width: '250px',
			placeholder: 'Available solvents in formula',
			allowClear: true,
			dropdownAutoWidth: true,
			containerCssClass: "formulaSolvents",
			minimumResultsForSearch: Infinity,
			ajax: {
				url: '/core/full_formula_data.php',
				dataType: 'json',
				type: 'POST',
				delay: 100,
				quietMillis: 250,
				data: function (data) {
					return {
						id: myID,
						solvents_only: true
					};
				},
				processResults: function(data) {
					return {
						results: $.map(data.data, function(obj) {
						  return {
							id: obj.ingredient_id,
							text: obj.ingredient || 'No solvent(s) found in formula',
						  }
						})
					};
				},
				cache: true,	
			}	
		});
	
	});	
});

$('#formula').on('click', '[id*=exIng]', function () {
	var ing = {};
	ing.ID = $(this).attr('data-id');
	ing.Name = $(this).attr('data-name');
	ing.Status = $(this).attr('data-status');
			
		$.ajax({ 
			url: '/pages/manageFormula.php', 
			type: 'POST',
			data: {
				action: "excIng",
				fid: "<?=$meta['fid']?>",
				ingID: ing.ID,
				ingName: ing.Name,
				status: ing.Status
				},
			dataType: 'json',
			success: function (data) {
				if(data.success) {
					var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				}else{
					var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';	
				}
				$('#msgInfo').html(msg);
				reload_formula_data();
			}
		  });
				
});


	update_bar();
	
});//doc ready

$('#isMade').click(function() {
	bootbox.dialog({
       title: "Confirm formula is made?",
       message : 'If confirmed, ingredients amount will be deducted from the inventory accordingly, where enough in stock.',
       buttons :{
           main: {
               label : "Confirm",
               className : "btn-primary",
               callback: function (){
	    			
				$.ajax({ 
					url: '/pages/manageFormula.php', 
					type: 'POST',
					data: {
						isMade: "1",
						fid: "<?=$meta['fid']?>",
						},
					dataType: 'json',
					success: function (data) {
						if(data.success) {
							var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
						}else{
							var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';	
						}
						$('#msgInfo').html(msg);
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

function update_bar(){
     $.getJSON("/core/full_formula_data.php?id="+myID+"&stats_only=1", function (json) {
		
		$('#formula_name').html(json.stats.formula_name || "Unnamed");
		$('#formula_desc').html(json.stats.formula_description);

		if(json.stats.top || json.stats.heart || json.stats.base){
			$('#progress-area').show();
			
			var top = Math.round(json.stats.top);
			var top_max = Math.round(json.stats.top_max);
			
			var heart = Math.round(json.stats.heart);
			var heart_max = Math.round(json.stats.heart_max);
			
			var base = Math.round(json.stats.base);
			var base_max = Math.round(json.stats.base_max);
	
			$('#top_bar').attr('aria-valuenow', top).css('width', top+'%').attr('aria-valuemax', top_max);
			$('#heart_bar').attr('aria-valuenow', heart).css('width', heart+'%').attr('aria-valuemax', heart_max);
			$('#base_bar').attr('aria-valuenow', base).css('width', base+'%').attr('aria-valuemax', base_max);;
			
			$('.top-label').html(top + "% Top Notes");
			$('.heart-label').html(heart + "% Heart Notes");
			$('.base-label').html(base + "% Base Notes");
		}else{
			$('#progress-area').hide();
		}
		
	}); 
};

function reload_formula_data() {
    $('#formula').DataTable().ajax.reload(null, true);
	update_bar();
	reset_solv();
};

$('#print').click(() => {
    $('#formula').DataTable().button(0).trigger();
});
</script>

<div class="card-body">
	<div class="col-sm-10" id="progress-area">
      <div class="progress">
          <div id="base_bar" class="progress-bar pv_bar_base_notes" role="progressbar" aria-valuemin="0"><span><div class="base-label"></div></span></div>
          <div id="heart_bar" class="progress-bar pv_bar_heart_notes" role="progressbar" aria-valuemin="0"><span><div class="heart-label"></div></span></div>
          <div id="top_bar" class="progress-bar pv_bar_top_notes" role="progressbar" aria-valuemin="0"><span><div class="top-label"></div></span></div>
      </div>
    </div>
    <div class="text-right">
      <div class="btn-group" id="menu">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mr2"></i>Actions</button>
        <div class="dropdown-menu dropdown-menu-right">
           <li class="dropdown-header">Export</li> 
           <li><a class="dropdown-item export_as" href="#" data-format="csv"><i class="fa-solid fa-file-csv mr2"></i>Export as CSV</a></li>
           <li><a class="dropdown-item export_as" href="#" data-format="pdf"><i class="fa-solid fa-file-pdf mr2"></i>Export as PDF</a></li>
           <li><a class="dropdown-item" href="/pages/operations.php?action=exportFormulas&fid=<?=$meta['fid']?>"><i class="fa-solid fa-file-code mr2"></i>Export as JSON</a></li>
           <li><a class="dropdown-item" href="#" id="print"><i class="fa-solid fa-print mr2"></i>Print Formula</a></li>
           <div class="dropdown-divider"></div>
           <li class="dropdown-header">Scale Formula</li> 
           <li><a class="dropdown-item manageQuantity" href="#" data-action="multiply"><i class="fa-solid fa-xmark mr2"></i>Multiply x2</a></li>
           <li><a class="dropdown-item manageQuantity" href="#" data-action="divide"><i class="fa-solid fa-divide mr2"></i>Divide x2</a></li>
           <li><a class="dropdown-item" href="#" data-backdrop="static" data-toggle="modal" data-target="#amount_to_make"><i class="fa-solid fa-calculator mr2"></i>Advanced</a></li>
           <div class="dropdown-divider"></div>
           <li><a class="dropdown-item" href="#" data-backdrop="static" data-toggle="modal" data-target="#create_accord"><i class="fa-solid fa-list-check mr2"></i>Create accord</a></li>
           <li><a class="dropdown-item" href="#" data-backdrop="static" data-toggle="modal" data-target="#conv_ingredient"><i class="fa-solid fa-list-check mr2"></i>Create ingredient</a></li>
           <div class="dropdown-divider"></div>
           <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#schedule_to_make"><i class="fa-regular fa-calendar-plus mr2"></i>Schedule to make</a></li>
           <li><a class="dropdown-item" href="#" id="isMade"><i class="fa-solid fa-check mr2"></i>Mark formula as made</a></li>
           <div class="dropdown-divider"></div>
           <li><a class="dropdown-item" href="#" id="cloneMe"><i class="fa-solid fa-copy mr2"></i>Clone Formula</a></li>
        </div>
        </div>            
    </div>
</div>
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
                <th>Inventory</th>
                <th>Properties</th>
                <th></th>
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
            <th></th>
            </tr>
        </tfoot>
</table>

<!--Schedule Formula-->
<div class="modal fade" id="schedule_to_make" tabindex="-1" role="dialog" aria-labelledby="schedule_to_make" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Schedule formula to make</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="scheduleToMakeMsg"></div>
      <p>This will add the current formulation to scheduled formulas. Any changes in this formula will not be replicated to the scheduled version. If you make changes here, you have to remove it and re-add it for making.</p>
      <hr />
	    <div class="modal-footer">
	     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
  		 <input type="submit" name="button" class="btn btn-primary" id="addTODO" value="Schedule Formula">
	   </div>
    </div>
  </div>
 </div>
</div>

<!--Scale Formula-->
<div class="modal fade" id="amount_to_make" tabindex="-1" role="dialog" aria-labelledby="amount_to_make" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Scale formula</h5>
      </div>
      <div class="modal-body">
      <div id="amountToMakeMsg"></div>
      <p>This will re-calculate the ingredients quantity as per the new total.</p>
      <hr />
        <table width="313" border="0">
          <tr>
	       <td width="66" height="31"><strong>SG<span class="sup">*</span> :</strong></td>
	       <td width="237"><input name="sg" type="text" id="sg" value="1.000" />
            <strong><?=$settings['mUnit']?></strong></td>
          </tr>
	     <tr>
	       <td><strong>Amount:</strong></td>
	       <td><input name="totalAmount" type="text" id="totalAmount" value="100" />
            <strong><?=$settings['mUnit']?></strong></td>
          </tr>
        </table>
	    <p>&nbsp;</p>
	    <p>*<a href="https://www.jbparfum.com/knowledge-base/3-specific-gravity-sg/" target="_blank">Specific Gravity of Ethanol</a></p>
	    <div class="modal-footer">
	     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
  		 <input type="submit" name="button" class="btn btn-primary" id="amountToMake" value="Scale Formula">
	   </div>
    </div>
  </div>
 </div>
</div>

<!--Create accord-->
<div class="modal fade" id="create_accord" tabindex="-1" role="dialog" aria-labelledby="create_accord" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create accord</h5>
      </div>
      <div class="modal-body">
      <div id="accordMsg"></div>
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
	    <hr />
	    <div class="alert alert-info">This will create a new formula from the notes you choose. <br/>The current formula will stay intact.</div>
	    <div class="modal-footer">
	     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
  		 <input type="submit" name="button" class="btn btn-primary" id="createAccord" value="Create">
	   </div>
    </div>
  </div>
 </div>
</div>

<!--Convert to ingredient-->
<div class="modal fade" id="conv_ingredient" tabindex="-1" role="dialog" aria-labelledby="conv_ingredient" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create ingredient from formula</h5>
      </div>
      <div class="modal-body">
      <div id="cnvMsg"></div>
        <table width="313" border="0">
	     <tr>
	       <td><strong>Name:</strong></td>
	       <td><input name="ingName" type="text" class="form-control" id="ingName" value="<?=$f_name?>" /></td>
          </tr>
        </table>
        <hr />
        <div class="alert alert-info">The original formula will not be affected.</div>
	    <div class="modal-footer">
	     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
  		 <input type="submit" name="button" class="btn btn-primary" id="conv2ing" value="Create">
	   </div>
    </div>
  </div>
 </div>
</div>


<script>

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
  
$('#formula').editable({
  container: 'body',
  selector: 'a.concentration',
  url: "/pages/update_data.php?formula=<?=$meta['fid']?>",
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
<?php if($settings['editor'] == '1'){?>
$('#formula').editable({
  container: 'body',
  selector: 'a.quantity',
  url: "/pages/update_data.php?formula=<?=$meta['fid']?>",
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
<?php } ?>
$('#formula').editable({
	container: 'body',
	selector: 'a.solvent',
	type: 'POST',
	emptytext: "",
	emptyclass: "",
	url: "/pages/update_data.php?formula=<?=$meta['fid']?>",
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
  selector: 'i.notes',
  url: "/pages/update_data.php?formula=<?=$fid?>",
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
	if(row.exclude_from_calculation == 1){
		var ex = 'pv_ing_exc';
	}
	if(row.chk_ingredient){
		var chkIng = '<i class="fas fa-exclamation" rel="tip" title="'+row.chk_ingredient+'"></i>';
	}else{
		var chkIng = '';
	}
	if(row.ingredient.profile_plain){
		var profile_class = '<a href="#" class="'+row.ingredient.profile_plain+'"></a>';
	}else{
		var profile_class ='';
	}
	if(row.ingredient.enc_id){
		data = '<a class="popup-link '+ex+'" href="/pages/mgmIngredient.php?id=' + row.ingredient.enc_id + '">' + data + '</a> '+ chkIng + profile_class;
	}else{
		data = '<a class="popup-link '+ex+'" href="/pages/mgmIngredient.php?id=' + btoa(data) + '">' + data + '</a> '+ chkIng + profile_class;

	}

  return data;
}

function ingCAS(data, type, row, meta){
	if(type === 'display'){
		data = '<i class="pv_point_gen" rel="tip" title="Click to copy" id="cCAS" data-name="'+row.ingredient.cas+'">'+row.ingredient.cas+'</i>';
	}
  	 return data;
}
  
function ingConc(data, type, row, meta){
  if( isProtected == false ){
	  if( row.ingredient.profile == "Solvent"){
		  data = 100;
	  }else{
	  	data = '<a href="#" data-name="concentration" class="concentration" data-type="text" data-pk="' + row.formula_ingredient_id + '">' + data + '</a>';
	  }
  }

  return data;
}

function ingSolvent(data, type, row, meta){
	if( isProtected == false ){
	  if(row.purity !== 100){
		if(row.ingredient.profile == "Solvent"){
			data = 'None';
		}else{
			data = '<a href="#" data-name="dilutant" class="solvent" data-type="select" data-pk="' + row.formula_ingredient_id + '">' + data + '</a>';
		}
	 }else{
		data = 'None';
	  }
	}else{
		if(row.purity === 100){
			data = 'None';
		}
	}
  return data;
}
  
function ingQuantity(data, type, row, meta){
	if( isProtected == false ){
		<?php if($settings['editor'] == '1'){?>
			data = '<a href="#" data-name="quantity" class="quantity" data-type="text" data-pk="' + row.formula_ingredient_id + '">' + data + '</a>';
		<?php }else{?>
			data = '<a href="#" data-name="quantity" data-toggle="modal" data-target="#manage-quantity" data-backdrop="static" class="open-quantity-dialog" data-type="text" data-ingid="' + row.formula_ingredient_id + '" data-value="' + row.quantity + '" data-ing="' + row.ingredient.name + '" data-mainingid="'+row.ingredient.id+'">' + row.quantity + '</a>';
		<?php } ?>
	} 

	return data;
}

function ingSetConc(data, type, row, meta){
	return data;
}

function ingNotes(data, type, row, meta){
	 if(type === 'display'){
	  <?php if($meta['defView'] == '1'){ $show = 'properties'; }elseif($meta['defView'] == '2'){ $show = 'notes';}?>
	  <?php if($meta['isProtected'] == FALSE){?>
	  data = '<i data-name="<?=$show?>" class="pv_point_gen <?=$show?>" data-type="textarea" data-pk="' + row.formula_ingredient_id + '">' + data + '</i>';
	  <?php } ?>
	 }
	return data;
}
  
  
function ingInv(data, type, row, meta){
	if (row.ingredient.physical_state == 1){
		var mUnit = 'ml';
	}else if (row.ingredient.physical_state == 2){
		var mUnit = 'gr';
	}
	if(row.ingredient.inventory.stock >= row.quantity){
		var inv = '<i class="fa fa-check inv-ok" rel="tip" title="Available in stock: '+row.ingredient.inventory.stock+mUnit+'"></i>';
	}else if(row.ingredient.inventory.stock <= row.quantity){
		var inv = '<i class="fa fa-times inv-out" rel="tip" data-html="true" title="Not enough in stock from the prefered supplier.<br/> Available: '+row.ingredient.inventory.stock + mUnit +'"></i>';
	}

	if(type === 'display'){
		data = inv;
	}

  return data;
}

function ingActions(data, type, row, meta){

	data = '<div class="dropdown">' +
        '<button type="button" class="btn btn-primary btn-floating dropdown-toggle hidden-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
            '<ul class="dropdown-menu dropdown-menu-right">';

	data += '<li><a class="dropdown-item link-dark" href="'+ row.ingredient.pref_supplier_link +'" target="_blank" rel="tip" title="Open '+ row.ingredient.pref_supplier +' page"><i class="fas fa-shopping-cart mr2"></i>Go to supplier</a></li>';
	
	<?php if($meta['isProtected'] == FALSE){?>
	if(row.exclude_from_calculation == 0){
	 	var ex = '<li><i class="dropdown-item pv_point_gen link-dark" rel="tip" id="exIng" title="Exclude '+ row.ingredient.name +'" data-name="'+ row.ingredient.name +'" data-status="1" data-id="'+ row.formula_ingredient_id +'"><i class="pv_point_gen fas fa-eye-slash mr2"></i>Exlude</i></li>';
		
	}else if(row.exclude_from_calculation == 1){
	 	var ex = '<li><i class="dropdown-item pv_point_gen link-dark" rel="tip" id="exIng" title="Include '+ row.ingredient.name +'" data-name="'+ row.ingredient.name +'" data-status="0" data-id="'+ row.formula_ingredient_id +'"><i class="pv_point_gen fas fa-eye mr2"></i>Include</i></li>';
	}
	
	data += ex + '<li><i data-toggle="modal" data-target="#replaceIng" data-backdrop="static" class="dropdown-item pv_point_gen open-replace-dialog text-info" rel="tip" title="Replace '+ row.ingredient.name +'"  data-name="'+ row.ingredient.name +'" data-id="'+ row.formula_ingredient_id +'" data-cas="'+row.ingredient.cas+'" data-desc="'+row.ingredient.desc+'"><i class="pv_pont_gen fas fa-exchange-alt text-info mr2"></i>Replace ingredient</i></li>'
	
	+ '<li><i data-toggle="modal" data-target="#mrgIng" data-backdrop="static" rel="tip" title="Merge '+ row.ingredient.name +'" class="dropdown-item pv_point_gen open-merge-dialog text-warning" data-name="'+ row.ingredient.name +'" data-id="'+ row.formula_ingredient_id +'"><i class="pv_point_gen fas fa-object-group alert-warning mr2"></i>Merge ingredients</i></li>'
	
	+'<div class="dropdown-divider"></div>'
	+ '<li><i rel="tip" title="Remove '+ row.ingredient.name +'" class="dropdown-item text-danger pv_point_gen" id="rmIng" data-name="'+ row.ingredient.name +'" data-id="'+ row.formula_ingredient_id +'" data-ingredient-id="'+row.ingredient.id+'"><i class="pv_point_gen fas fa-trash mr2 text-danger"></i>Delete</i></li>';
	<?php } ?>
	data += '</ul></div>';

   return data;
}

</script>
<script src="/js/fullformula.view.js"></script>

<script src="/js/mark/jquery.mark.min.js"></script>
<script src="/js/mark/datatables.mark.js"></script>

<div class="modal fade" id="manage-quantity" tabindex="-1" role="dialog" aria-labelledby="manage-quantity" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><div id="ingQuantityName"></div></h5>
      </div>
      <div class="modal-body">
      	<div id="msgQuantity"></div>
        <input type="hidden" name="ingQuantityID" id="ingQuantityID" />
        <input type="hidden" name="ingQuantityName" id="ingQuantityName" />
        <input type="hidden" name="ingQuantity" id="ingQuantity" />
        <input type="hidden" name="mainingid" id="mainingid" />
        <input type="hidden" name="curQuantity" id="curQuantity" />
      	
        Quantity in <?=$settings['mUnit']?> 
        <input name="ingQuantity" type="text" class="ingQuantity form-control" id="ingQuantity">
        
        <div class="dropdown-divider"></div>
        <input type="checkbox" name="reCalc" id="reCalc" value="1" data-val="1" /> Adjust solvent
        
        <div id="slvMeta">
        	<div class="dropdown-divider"></div>
        	<input name="formulaSolvents" id="formulaSolvents" type="text" class="formulaSolvents pv-form-control">
        	<div class="dropdown-divider"></div>
        	<div id="explain" class="alert alert-info">Auto adjust total quantity by increasing or decreasing quantity from the selected solvent if enough available.<br>For example, if you add 1 more ml to the current ingredient, the selected solvent's quantity will be deducted by 1ml equally.</div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="quantityConfirm" value="Update">
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="mrgIng" tabindex="-1" role="dialog" aria-labelledby="mrgIng" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Merge ingredients</h5>
      </div>
      <div class="modal-body">
      	<div id="msgMerge"></div>
        <input type="hidden" name="ingSrcID" id="ingSrcID" />
        <input type="hidden" name="ingSrcName" id="ingSrcName" />
      	<div class="alert alert-info">You can merge <div id="srcIng"></div>'s quantity with another material in formula. Use this method if materials are similar. Please note, this action cannot be reverted, quanity will sum up to the target ingredient's quantity.</div>
        Merge <div id="srcIng"></div> with: 
        <input name="mrgIngName" id="mrgIngName" type="text" class="mrgIngName pv-form-control">
        <p>
        <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="mergeConfirm" value="Merge ingredients">
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="replaceIng" tabindex="-1" role="dialog" aria-labelledby="replaceIng" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Replace <div id="ingRepName"></div></h5>
      </div>
      <div class="modal-body">
      	<div id="msgRepl"></div>
        <input type="hidden" name="ingRepID" id="ingRepID" />
        <input type="hidden" name="ingRepName" id="ingRepName" />
      	<div class="alert alert-info">Replace <div id="ingRepName"></div> with another ingredient, quantity and dilution values will not be affected.</div>
        Replace <div id="ingRepName"></div> with: 
        <input name="repIngNameDest" id="repIngNameDest" type="text" class="repIngNameDest pv-form-control">
        <p>
        <div class="dropdown-divider"></div>
        
        <div id="repGrid" class="card card-inverse card-reping">
         <div class="row">
            <div class="col-sm">
              <div id="ingSrcInfo"></div>
            </div>
            <div class="col-1 row justify-content-center align-self-center">
              <i class="fa-solid fa-right-long fa-2xl"></i>
            </div>
            <div class="col-sm">
              <div id="ingTargInfo"></div>
            </div>
          </div>
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="replaceConfirm" value="Replace ingredient">
      </div>
    </div>
  </div>
</div>
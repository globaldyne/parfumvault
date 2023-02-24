<?php 
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if(!$_GET['id']){
	echo 'Formula id is missing.';
	return;
}
	
$id = mysqli_real_escape_string($conn, $_GET['id']);

if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE id = '$id'")) == 0){
	echo '<div class="alert alert-info alert-dismissible">Incomplete formula. Please add ingredients.</div>';
	return;
}
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,fid,name,isProtected,finalType,defView,notes FROM formulasMetaData WHERE id = '$id'"));
$f_name = $meta['name'];
$fid = $meta['fid'];
?>

  

<script>
var myFID = "<?=$meta['fid']?>";
var myFNAME = "<?=$meta['name']?>";
var isProtected;
<?php if($meta['isProtected'] == FALSE){?>
	var isProtected = false;
<?php } ?>

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
    			   { data : 'ingredient.cas', title: 'CAS#', render: ingCAS},
				   { data : 'purity', title: 'Purity %', render: ingConc},
				   { data : 'dilutant', title: 'Dilutant', render: ingSolvent},
				   { data : 'quantity', title: 'Quantity (<?=$settings['mUnit']?>)', render: ingQuantity},
				   { data : 'concentration', title: 'Concentration 100%', render: ingSetConc},
				   { data : 'final_concentration', title: 'Final Concentration <?=$meta['finalType']?>%'},
				   { data : 'cost', title: 'Cost (<?=$settings['currency']?>)'},
				   { data : 'ingredient.inventory.stock', title: 'Inventory', className: 'text-center noexport', render: ingInv },
				   { data : 'ingredient.desc', title: 'Properties', render: ingNotes},
   				   { data : null, title: 'Actions', className: 'text-center noexport', render: ingActions},		   
				   
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
			 $(formula_table.columns(7).header()).html("Final Concentration: " + response.meta['product_concentration'] + "%");
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
    
	bootbox.dialog({
       title: "Confirm ingredient removal",
       message : 'Remove <strong>'+ ing.Name +'</strong> from formula?',
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
						fid: "<?=$meta['fid']?>",
						ingID: ing.ID,
						ing: ing.Name
						},
					dataType: 'json',
					success: function (data) {
						if(data.success){
							var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
						}else{
							var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
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
function isMade() {
			 
	bootbox.dialog({
       title: "Confirm formula is made?",
       message : 'If confirmed, ingredients amount will be deducted from the inventory accordingly, where enough in stock.',
       buttons :{
           main: {
               label : "Confirm",
               className : "btn-primary",
               callback: function (){
	    			
				$.ajax({ 
					url: 'pages/manageFormula.php', 
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
			 
}

function update_bar(){
     $.getJSON("/core/full_formula_data.php?id=<?=$id?>&stats_only=1", function (json) {
		
		$('#formula_name').html(json.stats.formula_name);
		$('#formula_desc').html(json.stats.formula_description);

		
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
	}); 
};

function reload_formula_data() {
    $('#formula').DataTable().ajax.reload(null, true);
	update_bar();
};

$('#print').click(() => {
    $('#formula').DataTable().button(0).trigger();
});
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
        <div class="btn-group" id="menu">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
            <div class="dropdown-menu dropdown-menu-left">
	           <a class="dropdown-item popup-link" href="pages/getFormMeta.php?id=<?=$meta['id']?>">Settings</a>
               <div class="dropdown-divider"></div>
               <li class="dropdown-header">Export</li> 
               <a class="dropdown-item" href="javascript:export_as('csv')">Export to CSV</a>
               <a class="dropdown-item" href="javascript:export_as('pdf')">Export to PDF</a>
               <a class="dropdown-item" href="#" id="print">Print Formula</a>
               <div class="dropdown-divider"></div>
               <li class="dropdown-header">Scale Formula</li> 
               <a class="dropdown-item" href="javascript:manageQuantity('multiply')">Multiply x2</a>
               <a class="dropdown-item" href="javascript:manageQuantity('divide')">Divide x2</a>
               <a class="dropdown-item" href="#" data-toggle="modal" data-target="#amount_to_make">Advanced</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="#" data-toggle="modal" data-target="#create_accord">Create Accord</a>
               <a class="dropdown-item" href="#" data-toggle="modal" data-target="#conv_ingredient">Convert to ingredient</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="javascript:addTODO()">Add to the make list</a>
               <a class="dropdown-item" href="javascript:isMade()">Mark formula as made</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="pages/viewHistory.php?id=<?=$meta['id']?>" target="_blank">View history</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="javascript:cloneMe()">Clone Formula</a>
               <div class="dropdown-divider"></div>
               <?php if($pv_online['enabled'] == '1'){?>
               <li class="dropdown-header">PV Online</li> 
               <a class="dropdown-item" href="#" data-toggle="modal" data-target="#share_to_user">Share with someone</a>
               <div class="dropdown-divider"></div>
               <?php } ?>
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
                <th>Inventory</th>
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
            <th></th>
            </tr>
        </tfoot>
</table>

<?php if($pv_online['enabled'] == '1'){?>
<!--Share with a user-->
<div class="modal fade" id="share_to_user" tabindex="-1" role="dialog" aria-labelledby="share_to_user" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Share with a PV Online user</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="techpreview"><div class="alert alert-warning alert-dismissible"><strong>THIS IS A TECH PREVIEW FEATURE. USE ONLY FOR TESTING.</strong></div></div>
      <div id="shareMsg"></div>
        <table width="100%" border="0">
          <tr>
	       <td height="31" colspan="2"><p>Select PV Online user to share the formula with.</p>
            <p>The formula will be sent to PV Online servers and will be automatically deleted when all the users you sharing the formula with, downloads the formula.</p>
            <p>&nbsp;</p></td>
          </tr>
	     <tr>
	       <td width="125">Share with:</td>
	       <td width="895"><input name="pvUsers" id="pvUsers" class="pv-form-control"></td>
          </tr>
	     <tr>
	       <td valign="top">Comments:</td>
	       <td><textarea name="pvShareComment" id="pvShareComment" cols="45" rows="5" placeholder="Short description of your formula or any other comments" class="form-control"><?=$meta['notes']?></textarea></td>
          </tr>
        </table>
	    <p>&nbsp;</p>
	    <p><a href="#" data-toggle="modal" data-target="#invite_to_pv">Invite someone to PV Online</a></p>
	    <div class="modal-footer">
	      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	      <input type="submit" name="button" class="btn btn-primary" id="sharePVOnline" value="Share">
        </div>
    </div>
  </div>
 </div>
</div>


<!--INVITE TO PV ONLINE-->
<div class="modal fade" id="invite_to_pv" tabindex="-1" role="dialog" aria-labelledby="invite_to_pv" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Invite someone to PV Online</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="invMsg"></div>
        <table width="100%" border="0">
          <tr>
	       <td width="66" height="31"><strong>Email<span class="sup"></span> :</strong></td>
	       <td width="237"><input name="invEmail" type="text" id="invEmail" /></td>
          </tr>
	     <tr>
	       <td><strong>Name:</strong></td>
	       <td><input name="invName" type="text" id="invName" /></td>
          </tr>
        </table>
        <hr />
	    <p><strong>Please Note:</strong></p>
	    <p>If the person your are trying to invite is already registered, the invitation will not be send.</p>
       	<p>You can only send one invitation per email/person.</p>
	    <div class="modal-footer">
	     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
  		 <input type="submit" name="button" class="btn btn-primary" id="invToPV" value="Send Invitation">
	   </div>
    </div>
  </div>
 </div>
</div>

<?php } ?>
<!--Scale Formula-->
<div class="modal fade" id="amount_to_make" tabindex="-1" role="dialog" aria-labelledby="amount_to_make" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Scale formula</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="amountToMakeMsg"></div>
      <p>This will re-calculate the ingredients quantity as per the new total.</p>
      <hr />
        <table width="313" border="0">
          <tr>
	       <td width="66" height="31"><strong>SG<span class="sup">*</span> :</strong></td>
	       <td width="237"><input name="sg" type="text" id="sg" value="0.985" />
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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
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
        <h5 class="modal-title">Convert formula to ingredient</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
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
  		 <input type="submit" name="button" class="btn btn-primary" id="conv2ing" value="Convert">
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
  selector: 'i.notes',
  url: "pages/update_data.php?formula=<?=$fid?>",
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
		data = '<a class="popup-link '+ex+'" href="pages/mgmIngredient.php?id=' + row.ingredient.enc_id + '">' + data + '</a> '+ chkIng + profile_class;
	}else{
		data = '<a class="popup-link '+ex+'" href="pages/mgmIngredient.php?id=' + btoa(data) + '">' + data + '</a> '+ chkIng + profile_class;

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
	if(type === 'display'){
		<?php if($meta['isProtected'] == FALSE){?>
		data = '<a href="#" data-name="quantity" class="quantity" data-type="text" data-pk="' + row.formula_ingredient_id + '">' + data + '</a>';
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
//Change ingredient
$('#formula').editable({
	select2: {
    	width: '250px',
        placeholder: 'Search for ingredient (name, cas)',
        allowClear: true,
    	dropdownAutoWidth: true,
		minimumInputLength: 2,
		ajax: {
			url: '/core/list_ingredients_simple.php',
			dataType: 'json',
			type: 'POST',
			delay: 100,
			quietMillis: 250,
			data: function (data) {
				return {
					search: data
				};
			},
			processResults: function(data) {
				return {
					results: $.map(data.data, function(obj) {
					  return {
						id: obj.name, //TODO: TO BE CHANGED TO ID WHEN THE BACKEND IS READY
						text: obj.name || 'No ingredient found...',
					  }
					})
				};
			},
			cache: true,
			
    	}
    },
	tpl:'<input type="hidden">',
	placement: 'left',
	selector: 'i.replaceIngredient',
	pvnoresp: false,
	highlight: false,
	emptytext: null,
	emptyclass: null,
	url: "pages/manageFormula.php?action=repIng&fid=" + myFID ,
	success: function (data) {
		if ( data.indexOf("Error") > -1 ) {
			$('#msgInfo').html(data); 
		}else{
			$('#msgInfo').html(data);
			reload_formula_data();
		}
	},
	validate: function(value){
   		if($.trim(value) == ''){
			return 'Ingredient is required';
   		}
	}
});

if(type === 'display'){
	data = '<a href="'+ row.ingredient.pref_supplier_link +'" target="_blank" rel="tip" title="Open '+ row.ingredient.pref_supplier +' page" class="fas fa-shopping-cart"></a>';
	<?php if($meta['isProtected'] == FALSE){?>
	if(row.exclude_from_calculation == 0){
	 	var ex = '&nbsp; <i class="pv_point_gen fas fa-eye-slash" style="color: #337ab7;" rel="tip" id="exIng" title="Exclude '+ row.ingredient.name +'" data-name="'+ row.ingredient.name +'" data-status="1" data-id="'+ row.formula_ingredient_id +'"></i>';
	}else if(row.exclude_from_calculation == 1){
	 	var ex = '&nbsp; <i class="pv_point_gen fas fa-eye" style="color: #337ab7;" rel="tip" id="exIng" title="Include '+ row.ingredient.name +'" data-name="'+ row.ingredient.name +'" data-status="0" data-id="'+ row.formula_ingredient_id +'"></i>';
	}
	data += ex + '&nbsp; <i class="pv_point_gen fas fa-exchange-alt replaceIngredient" style="color: #337ab7;" rel="tip" title="Replace '+ row.ingredient.name +'"  data-name="'+ row.ingredient.name +'" data-type="select2" data-pk="'+ row.ingredient.name +'" data-title="Choose Ingredient to replace '+ row.ingredient.name +'"></i> &nbsp; <i rel="tip" title="Remove '+ row.ingredient.name +'" class="pv_point_gen fas fa-trash" style="color: #c9302c;" id="rmIng" data-name="'+ row.ingredient.name +'" data-id='+ row.formula_ingredient_id +'></i>';
	<?php } ?>
}
   return data;
}

//MULTIPLY - DIVIDE
function manageQuantity(quantity) {
	$.ajax({ 
    url: '/pages/manageFormula.php', 
	type: 'POST',
    data: {
		do: 'scale',
		scale: quantity,
		formula: myFID,
		},
	dataType: 'html',
    success: function (data) {
	  	$('#msgInfo').html(data);
		reload_formula_data();
    }
  });
};

//AMOUNT TO MAKE
$('#amount_to_make').on('click', '[id*=amountToMake]', function () {
	if($("#sg").val().trim() == '' ){
        $('#sg').focus();
	  	$('#amountToMakeMsg').html('<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> all fields required!</div>');
	}else if($("#totalAmount").val().trim() == '' ){
 		$('#totalAmount').focus();
	  	$('#amountToMakeMsg').html('<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> all fields required!</div>');		
	}else{
		$.ajax({ 
		url: '/pages/manageFormula.php', 
		type: 'POST',
		cache: false,
		data: {
			fid: myFID,
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
});


//Create Accord 
$('#create_accord').on('click', '[id*=createAccord]', function () {
	if($("#accordName").val().trim() == '' ){
        $('#accordName').focus();
	  	$('#accordMsg').html('<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> Accord name required!</div>');	
	}else{
		$.ajax({ 
		url: 'pages/manageFormula.php', 
		type: 'POST',
		cache: false,
		data: {
			fid: "<?php echo $fid; ?>",
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
});

//Convert to ingredient
$('#conv_ingredient').on('click', '[id*=conv2ing]', function () {
	if($("#ingName").val().trim() == '' ){
        $('#ingName').focus();
	  	$('#cnvMsg').html('<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> Ingredient name required!</div>');	
	}else{
		$.ajax({ 
		url: 'pages/manageFormula.php', 
		type: 'POST',
		cache: false,
		data: {
			fid: "<?=$fid?>",
			fname: "<?=$f_name?>",
			ingName: $("#ingName").val(),
			action: 'conv2ing',
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>';
			}else if(data.error){
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
			}
			$('#cnvMsg').html(msg);
		}
	  });
	}
});

//Clone
function cloneMe() {	  
$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'POST',
    data: {
		action: "clone",
		fname: "<?=$f_name?>",
		fid: "<?=$meta['fid']?>",
		},
	dataType: 'json',
    success: function (data) {
		if (data.success) {
			var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
		}else{
			var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
		}
		$('#msgInfo').html(msg);
	}

  });
};

//Add in TODO
function addTODO() {
	$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'GET',
    data: {
		action: 'todo',
		fname: "<?=$f_name?>",
		fid: "<?=$meta['fid']?>",
		add: true,
		},
	dataType: 'html',
    success: function (data) {
	  	$('#msgInfo').html(data);
    }
  });
};

$('#formula').on('click', '[id*=cCAS]', function () {
	var copy = {};
	copy.Name = $(this).attr('data-name');
	const el = document.createElement('textarea');
    el.value = copy.Name;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
});

$("#pvUsers").select2({
	placeholder: "Search for users",
    width: '250px',
    placeholder: 'Search for PV Online users',
	formatResult: formatPVUsers, 
    formatSelection: formatPVUsersSelection, 
    allowClear: true,
    dropdownAutoWidth: false,
	tags: true,
	minimumInputLength: 2,
	ajax: {
		url: '<?=$pvOnlineAPI?>',
		dataType: 'json',
		type: 'POST',
		delay: 300,
		quietMillis: 250,
		cache: false,
   		data: function (term) {
            return {
				username: '<?=$pv_online['email']?>',
				password: '<?=$pv_online['password']?>',
				do: 'getUsers',
            	search: term,
			};
		},
		processResults: function(data) {
			return {
				results: $.map(data.users, function(obj) {
					return {
						id: obj.id,
						name: obj.nickname,
						userBio: obj.userBio,
						avatar: obj.avatar
					}
				})
			};
		},	
    }
});

function formatPVUsers (userData) {
  if (userData.loading) {
    return userData.name;
  }
  
  if (!userData.name){
	return 'User not found...';
  }
  
  var $container = $(
    "<div class='select_result_igredient clearfix'><div class='col-sm-1 profile-avatar-thumb'><img src='data:image/png;base64,"+userData.avatar+"' class='img-profile-avatar-thumb'/></div><strong>" +userData.name+
      "</strong><div class='select_result_igredient_meta'>" +
        "<div class='select_result_igredient_description'>" +userData.userBio+ "</div>" +
    "</div>"
  );

  return $container;
}

function formatPVUsersSelection (userData) {
  return userData.name;
}
<?php if($pv_online['enabled'] == '1'){?>

$('#share_to_user').on('click', '[id*=sharePVOnline]', function () {
	$('#sharePVOnline').attr('disabled', true);
	$('#shareMsg').html('<div class="alert alert-info"><img src="/img/loading.gif"/> Please wait, this may take a while...</div>');

	$.ajax({
		url: 'pages/pvonline.php', 
		type: 'POST',
		data: {
			action: 'share',
			fid: '<?=$fid?>',
			users: $("#pvUsers").val(),
			comments: $("#pvShareComment").val(),
			},
		dataType: 'json',
		success: function (data) {
				if(data.error){
					$('#sharePVOnline').attr('disabled', false);
					var rmsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';

				}else if(data.success){
					$('#sharePVOnline').hide();
					var rmsg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>';
				}
			
		  	$('#shareMsg').html(rmsg);
		}
	  });
	
});

$('#invToPV').click(function() {
	$.ajax({
		url: 'pages/pvonline.php', 
		type: 'POST',
		data: {
			action: 'invToPv',
			invEmail: $("#invEmail").val(),
			invName: $("#invName").val(),
			},
		dataType: 'json',
		success: function (data) {
				if(data.error){
					var rmsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
				}else if(data.success){
					var rmsg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>';
				}
			
		  	$('#invMsg').html(rmsg);
		}
	  });

});

<?php } ?>
function export_as(type) {
  $("#formula").tableHTMLExport({
	type: type,
	filename: myFNAME + "." + type,
	separator: ',',
  	newline: '\r\n',
  	trimContent: true,
  	quoteFields: true,
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',
	htmlContent: false,
	orientation: 'l',
	maintitle: myFNAME,
  });
};
</script>
<script src="js/mark/jquery.mark.min.js"></script>
<script src="js/mark/datatables.mark.js"></script>

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



<div class="card-body">
	<div class="col-sm-10" id="progress-area">
      <div class="progress">
          <div id="base_bar" class="progress-bar pv_bar_base_notes" role="progressbar" aria-valuemin="0">
          	<span><div class="base-label"></div></span>
          </div>
          <div id="heart_bar" class="progress-bar pv_bar_heart_notes" role="progressbar" aria-valuemin="0">
          	<span><div class="heart-label"></div></span>
          </div>
          <div id="top_bar" class="progress-bar pv_bar_top_notes" role="progressbar" aria-valuemin="0">
          	<span><div class="top-label"></div></span>
          </div>
      </div>
    </div>
    
    <div class="mt-1 mb-1 dropdown-divider"></div>
    
    <div class="col text-right">
      <div class="btn-group" id="menu">
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
        <div class="dropdown-menu dropdown-menu-end">
           <li class="dropdown-header">Export</li> 
           <li><a class="dropdown-item export_as" href="#" data-format="csv"><i class="fa-solid fa-file-csv mx-2"></i>Export as CSV</a></li>
           <li><a class="dropdown-item export_as" href="#" data-format="pdf"><i class="fa-solid fa-file-pdf mx-2"></i>Export as PDF</a></li>
           <li><a class="dropdown-item" href="/pages/operations.php?action=exportFormulas&fid=<?=$meta['fid']?>"><i class="fa-solid fa-file-code mx-2"></i>Export as JSON</a></li>
           <li><a class="dropdown-item" href="#" id="print"><i class="fa-solid fa-print mx-2"></i>Print Formula</a></li>
           <div class="dropdown-divider"></div>
           <li class="dropdown-header">Scale Formula</li> 
           <li><a class="dropdown-item manageQuantity" href="#" data-action="multiply"><i class="fa-solid fa-xmark mx-2"></i>Multiply x2</a></li>
           <li><a class="dropdown-item manageQuantity" href="#" data-action="divide"><i class="fa-solid fa-divide mx-2"></i>Divide x2</a></li>
           <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#amount_to_make"><i class="fa-solid fa-calculator mx-2"></i>Advanced</a></li>
           <div class="dropdown-divider"></div>
           <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#create_accord"><i class="fa-solid fa-list-check mx-2"></i>Create accord</a></li>
           <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#conv_ingredient"><i class="fa-solid fa-list-check mx-2"></i>Create ingredient</a></li>
           <div class="dropdown-divider"></div>
           <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#schedule_to_make"><i class="fa-regular fa-calendar-plus mx-2"></i>Schedule to make</a></li>
           <li><a class="dropdown-item" href="#" id="isMade"><i class="fa-solid fa-check mx-2"></i>Mark formula as made</a></li>
           <div class="dropdown-divider"></div>
           <li><a class="dropdown-item" href="#" id="cloneMe"><i class="fa-solid fa-copy mx-2"></i>Duplicate Formula</a></li>
        </div>
      </div>            
    </div>
</div>

<div id="row pv_search">
	<div class="text-right">
    	<label for="pvCustomSearch" class="mx-2"><a href="#" class="link-secondary" rel="tip" title="Use comma (,) separated values to search for different ingredients">Search in formula:</a></label>
    	<input type="text" id="pvCustomSearch" placeholder="CAS, Ingredient, etc..">
	</div>
</div>
<table id="formula" class="table table-hover table-striped nowrap viewFormula" style="width:100%">
        <thead class="table-primary">
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
          		<th data-priority="1"></th>
            </tr>
        </thead>
        <tfoot class="table-secondary">
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
    		search: "<?=$_GET['search']?>",
			regex: true
  		},
		dom: 'lrtip',
			buttons: [{
				extend: 'print',
				title: myFNAME,
				exportOptions: {
     				columns: [1, 2, 3, 4, 5, 6, 7, 8, 10]
  				},
		}],
		processing: true,
		mark: false,
		responsive: false,
        language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Blending...</span>',
			emptyTable: '<div class="alert alert-warning"><strong>Empty formula. Please add ingredients.</strong></div>',
			search: "Search in formula:",
			searchPlaceholder: "CAS, Ingredient, etc.."
		},
    	ajax: {
    		url: '/core/full_formula_data.php',
			type: 'POST',
			data:{
		   		id: "<?=$meta['id']?>",
			}
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
			 $td.eq(7).html("Total: " + response.meta['currency'] + response.meta['total_cost'] + '<i rel="tip" title="The total price for the 100% concentration." class="mx-2 pv_point_gen fas fa-info-circle"></i>');
			 $(formula_table.columns(7).header()).html("Final Concentration " + response.meta['product_concentration'] + "%");
			 //$('#compliance').html('<div class="alert alert-warning"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + response.compliance.message + '</div>');
			 //console.log(response.compliance);
		 }
      },
	  
        order: [[ groupColumn, 'desc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
        pageLength: 100,
		displayLength: 100,
		createdRow: function(row, data, dataIndex) {
		  const setAlertClassAndIcon = (selector, alertClass, title) => {
			$(row).find(selector).addClass(alertClass).append(`<i rel="tip" title="${title}" class="mx-2 pv_point_gen fas fa-info-circle"></i>`);
		  };
		
		  const checkUsage = (selector, regulator, limit, concentration, restriction) => {
			if (regulator === "IFRA" && parseFloat(limit) < parseFloat(concentration)) {
			  setAlertClassAndIcon(selector, 'alert-danger', `Max usage: ${limit}% IFRA Regulated,  ${restriction}`);
			} else if (regulator === "PV" && parseFloat(limit) < parseFloat(concentration)) {
			  switch (restriction) {
				case 1:
				  setAlertClassAndIcon(selector, 'alert-info', `Recommended usage: ${limit}% PV Regulated`);
				  break;
				case 2:
				  setAlertClassAndIcon(selector, 'alert-danger', `Restricted usage: ${limit}% PV Regulated`);
				  break;
				case 3:
				  setAlertClassAndIcon(selector, 'alert-warning', `Specification: ${limit}% PV Regulated`);
				  break;
				case 4:
				  setAlertClassAndIcon(selector, 'alert-warning', `Prohibited or Banned - PV Regulated`);
				  break;  
				default:
				  setAlertClassAndIcon(selector, 'alert-success', '');
			  }
			} else {
			  $(row).find(selector).addClass('alert-success');
			}
		  };
		
		  // Check initial usage
		  checkUsage('td:eq(5)', data['usage_regulator'], data['usage_limit'], data['concentration'], data['usage_restriction']);
		
		  // Check ingredient classification
		  if (data.ingredient.classification == 4 || data['usage_restriction_type'] == 'PROHIBITION') {
			$(row).find('td').not('td:eq(7),td:eq(8),td:eq(9),:eq(10)').addClass('bg-banned text-light').append('<i rel="tip" title="This material is prohibited" class="mx-2 pv_point_gen fas fa-ban"></i>');
		  }
		
		  // Check final usage
		  checkUsage('td:eq(6)', data['usage_regulator'], data['usage_limit'], data['final_concentration'], data['usage_restriction']);
		},

	   drawCallback: function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last = null;
         	$("#formula").wrap( "<div class='table-responsive'></div>" );

            api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
					var groupCount = api.rows({ page:'current' }).nodes().toArray().filter(function (row) {
                		return api.cell(row, groupColumn).data() === group;
            		}).length;

                    $(rows).eq( i ).before(
                        '<tr class="group noexport"><td colspan="' + rows.columns()[0].length +'"><div class="' + group + '_notes">' + group + ' Notes (' + groupCount + ')</div></td></tr>'
                    );
                    last = group;
                }
            });
			extrasShow();
	   }
});

formula_table.on('click', '.expandAccord', function (e) {
    let tr = $(this).closest('tr');
    let row = formula_table.row(tr);
    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    } else {
        showChildRow(row, tr);
    }
});

function showChildRow(row, tr) {
    $.ajax({
        url: '/core/list_ing_compos_data.php',
        type: 'GET',
        data: {
            id: btoa(row.data().ingredient.name)
        },
        dataType: 'json',
        success: function (data) {
            row.child(format(data)).show();
            tr.addClass('shown');
        }
    });
}

function format(d) {
    var details = '';
    for (var i = 0; i < d.data.length; i++) {
        details += '<div class="ingredient">';
        details += '<i class="bi bi-arrow-return-right mx-2"></i><span class="details"><a href="#" id="compoundName" data-query="'+ d.data[i].name +'">' + d.data[i].name + '</a>' ;
        details += ' - <a href="#" id="compoundCAS" data-query="'+ d.data[i].cas +'">' + d.data[i].cas + '</a>' ;
        details += ' - ' + d.data[i].avg_percentage + '%<br>';
        details += '</div>';
    }
	details += '<br />Total sub ingredients: ' + i;
    return details;
}


	
$('#pvCustomSearch').on('keyup redraw', function() {
	var searchString = '(' + $('#pvCustomSearch').val().split(/\s*,\s*/).join('|') + ')';
	formula_table.search(searchString, true).draw(true);
});

$('#formula_tab').on( 'click', function () {
	formula_table.fixedHeader.enable();
});

$('a[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
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

$('#formula').on('click', '[id*=compoundCAS], [id*=compoundName]', function (e) {
    event.preventDefault();
	cmpQuery = $(this).attr('data-query');
	//console.log(cmpQuery);
	formula_table.cells().nodes().to$().removeClass('highlight');
    if (cmpQuery !== '') {
		formula_table.rows().every(function(rowIdx, tableLoop, rowLoop) {
    		$(this.node()).find('td').each(function() {
            	var cellText = $(this).text();
                if (cellText.includes(cmpQuery)) {
                	$(this).addClass('highlight');
					var rowTop = $(this).closest('tr').offset().top;
                    var rowHeight = $(this).closest('tr').outerHeight();
                    var windowHeight = $(window).height();
                    var scrollTo = rowTop - (windowHeight / 2) + (rowHeight / 2);
                    $('html, body').animate({
                        scrollTop: scrollTo
                    }, 200);
                }
				
            });
        });
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
        		  '<input type="checkbox" name="reCalcDel" id="reCalcDel" value="1" data-val="1" />'+
				  '<label for="reCalcDel" class="form-label mx-2">Adjust solvent</label>'+
        			'<div id="slvMetaDel">'+
        				'<div class="dropdown-divider"></div>'+
        				'<select name="formulaSolventsDel" id="formulaSolventsDel" class="formulaSolventsDel pv-form-control"/></select>'+
        				'<div class="dropdown-divider"></div>'+
        				'<div id="explain" class="mt-3 alert alert-info">The deducted ingredient quantity will be added to the selected solvent.</div></div>',
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
							$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
							$('.toast-header').removeClass().addClass('toast-header alert-success');
							reload_formula_data();
							bootbox.hideAll();
						}else{
            				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
							$('.toast-header').removeClass().addClass('toast-header alert-danger');
						}
						$('.toast').toast('show');
					}						
				  });
                 return false;
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
			width: '100%',
			placeholder: 'Available solvents in formula',
			allowClear: true,
			dropdownAutoWidth: true,
			containerCssClass: "formulaSolvents",
			minimumResultsForSearch: Infinity,
			dropdownParent: $('.bootbox .modal-content'),
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
				fid: myFID,
				ingID: ing.ID,
				ingName: ing.Name,
				status: ing.Status
				},
			dataType: 'json',
			success: function (data) {
				if(data.success) {
            		$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
					$('.toast-header').removeClass().addClass('toast-header alert-success');
				}else{
            		$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
					$('.toast-header').removeClass().addClass('toast-header alert-danger');
				}
				$('.toast').toast('show');
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
						fid: myFID,
						},
					dataType: 'json',
					success: function (data) {
						if(data.success) {
							$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
							$('.toast-header').removeClass().addClass('toast-header alert-success');
						}else{
							$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
							$('.toast-header').removeClass().addClass('toast-header alert-danger');
						}
						$('.toast').toast('show');
						reload_formula_data();
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


function reload_formula_data() {
    $('#formula').DataTable().ajax.reload(null, true);
	update_bar();
	reset_solv();
};

$('#print').click(() => {
    $('#formula').DataTable().button(0).trigger();
});


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
  url: "/pages/update_data.php?formula=" + myFID,
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
  	url: "/pages/update_data.php?formula=" + myFID,
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
	url: "/pages/update_data.php?formula=" + myFID,
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
  url: "/pages/update_data.php?formula=" + myFID,
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
	var ex = '';
	var contains = '';
	var chkIng = '';
	var profile_class ='';
	
	
	if(row.ingredient.containsOthers.total){
		contains = '<i class="fa-solid fa-th-list expandAccord mx-2 pv_point_gen" rel="tip" title="Show/hide sub igredients"></i>';	
	}
	
	if(row.exclude_from_calculation == 1){
		ex = 'pv_ing_exc';
	}
	
	if(row.chk_ingredient){
		chkIng = '<i class="fas fa-exclamation" rel="tip" title="'+row.chk_ingredient+'"></i>';
	}else{
		chkIng = '';
	}
	
	if(row.ingredient.profile_plain){
		profile_class = '<a href="#" class="'+row.ingredient.profile_plain+'"></a>';
	}else{
		profile_class ='';
	}
	
	if(row.ingredient.enc_id){
		data = contains + '<a class="popup-link '+ex+'" href="/pages/mgmIngredient.php?id=' + row.ingredient.enc_id + '">' + data + '</a> '+ chkIng + profile_class;
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
			data = '<a href="#" data-name="quantity" data-bs-toggle="modal" data-bs-target="#manage-quantity" class="open-quantity-dialog" data-type="text" data-ingid="' + row.formula_ingredient_id + '" data-value="' + row.quantity + '" data-ing="' + row.ingredient.name + '" data-mainingid="'+row.ingredient.id+'">' + row.quantity + '</a>';
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
	  data = '<i data-name="<?=$show?>" class="pv_point_gen text-wrap <?=$show?>" data-type="textarea" data-pk="' + row.formula_ingredient_id + '">' + data + '</i>';
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
		
	}else if(row.ingredient.inventory.stock == 0){
		var inv = '<i class="fa fa-times inv-out" rel="tip" data-html="true" title="Not in stock from the prefered supplier.<br/> Available: '+row.ingredient.inventory.stock + mUnit +'"></i>';
		
	}else if(row.ingredient.inventory.stock <= row.quantity){
		var inv = '<i class="fa fa-triangle-exclamation inv-low" rel="tip" data-html="true" title="Not enough in stock from the prefered supplier.<br/> Available: '+row.ingredient.inventory.stock + mUnit +'"></i>';
		
	}
	
	if(type === 'display'){
		data = inv;
	}

  return data;
}

function ingActions(data, type, row, meta){

	data = '<div class="dropdown">' +
        '<button type="button" class="btn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
            '<ul class="dropdown-menu dropdown-menu-end">';

	data += '<li><a class="dropdown-item" href="'+ row.ingredient.pref_supplier_link +'" target="_blank" rel="tip" title="Open '+ row.ingredient.pref_supplier +' page"><i class="fas fa-shopping-cart mx-2"></i>Go to supplier</a></li>';
	
	<?php if($meta['isProtected'] == FALSE){?>
	if(row.exclude_from_calculation == 0){
	 	var ex = '<li><i class="dropdown-item pv_point_gen" rel="tip" id="exIng" title="Exclude '+ row.ingredient.name +'" data-name="'+ row.ingredient.name +'" data-status="1" data-id="'+ row.formula_ingredient_id +'"><i class="pv_point_gen fas fa-eye-slash mx-2"></i>Exlude</i></li>';
		
	}else if(row.exclude_from_calculation == 1){
	 	var ex = '<li><i class="dropdown-item pv_point_gen" rel="tip" id="exIng" title="Include '+ row.ingredient.name +'" data-name="'+ row.ingredient.name +'" data-status="0" data-id="'+ row.formula_ingredient_id +'"><i class="pv_point_gen fas fa-eye mx-2"></i>Include</i></li>';
	}
	
	data += ex + '<li><i data-bs-toggle="modal" data-bs-target="#replaceIng" class="dropdown-item pv_point_gen open-replace-dialog text-info-emphasis" rel="tip" title="Replace '+ row.ingredient.name +'"  data-name="'+ row.ingredient.name +'" data-id="'+ row.formula_ingredient_id +'" data-cas="'+row.ingredient.cas+'" data-desc="'+row.ingredient.desc+'"><i class="pv_pont_gen fas fa-exchange-alt text-info-emphasis mx-2"></i>Replace ingredient</i></li>'
	
	+ '<li><i data-bs-toggle="modal" data-bs-target="#mrgIng" rel="tip" title="Merge '+ row.ingredient.name +'" class="dropdown-item pv_point_gen open-merge-dialog text-warning-emphasis" data-name="'+ row.ingredient.name +'" data-id="'+ row.formula_ingredient_id +'"><i class="pv_point_gen fas fa-object-group alert-warning mx-2"></i>Merge ingredients</i></li>'
	
	+'<div class="dropdown-divider"></div>'
	+ '<li><i rel="tip" title="Remove '+ row.ingredient.name +'" class="dropdown-item text-danger pv_point_gen" id="rmIng" data-name="'+ row.ingredient.name +'" data-id="'+ row.formula_ingredient_id +'" data-ingredient-id="'+row.ingredient.id+'"><i class="pv_point_gen fas fa-trash mx-2 text-danger"></i>Delete</i></li>';
	<?php } ?>
	data += '</ul></div>';

   return data;
}

</script>
<script src="/js/fullformula.view.js"></script>
<script src="/js/mark/jquery.mark.min.js"></script>
<script src="/js/mark/datatables.mark.js"></script>

<!--Schedule Formula-->
<div class="modal fade" id="schedule_to_make" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="schedule_to_make" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Schedule formula to make</h5>
      </div>
      <div class="modal-body">
      <div id="scheduleToMakeMsg"></div>
      <div class="alert alert-info">This will add the current formulation to scheduled formulas. Any changes in this formula will not be replicated to the scheduled version. If you make changes here, you have to remove it and re-add it for making.</div>
	    <div class="modal-footer">
	     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
  		 <input type="submit" name="button" class="btn btn-primary" id="addTODO" value="Schedule Formula">
	   </div>
    </div>
  </div>
 </div>
</div>

<!--Scale Formula-->
<div class="modal fade" id="amount_to_make" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="amount_to_make" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Scale formula</h5>
      </div>
      <div class="modal-body">
        <div id="amountToMakeMsg"></div>
        <p>This will re-calculate the ingredients quantity as per the new total.</p>
        <hr />
        <div class="mb-3">
          <label for="sg" class="form-label"><strong>SG<span class="sup">*</span></strong></label>
          <div class="input-group">
            <input name="sg" type="text" class="form-control" id="sg" value="1.000" />
            <span class="input-group-text"><strong><?=$settings['mUnit']?></strong></span>
          </div>
        </div>
        <div class="mb-3">
          <label for="totalAmount" class="form-label"><strong>New amount</strong></label>
          <div class="input-group">
            <input name="totalAmount" type="text" class="form-control" id="totalAmount" value="100" />
            <span class="input-group-text"><strong><?=$settings['mUnit']?></strong></span>
          </div>
        </div>
        <hr />
        <p>*<a href="https://www.perfumersvault.com/knowledge-base/3-specific-gravity-sg/" target="_blank">Specific Gravity of Ethanol</a></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="amountToMake" value="Scale Formula">
      </div>
    </div>
  </div>
</div>


<!--Create accord-->
<div class="modal fade" id="create_accord" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="create_accord" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create accord</h5>
      </div>
      <div class="modal-body">
        <div id="accordMsg"></div>
        <div class="mb-3">
          <label for="accordProfile" class="form-label"><strong>Accord from</strong></label>
          <select name="accordProfile" id="accordProfile" class="form-control">
            <option value="Top">Top notes</option>
            <option value="Heart">Heart Notes</option>
            <option value="Base">Base Notes</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="accordName" class="form-label"><strong>Name</strong></label>
          <input name="accordName" type="text" class="form-control" id="accordName" value="<?=$f_name?> accord" />
        </div>
        <hr />
        <div class="alert alert-info"><i class="fa-solid fa-circle-exclamation mr-2"></i>This will create a new formula from the notes you choose. <br/>The current formula will stay intact.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="createAccord" value="Create">
      </div>
    </div>
  </div>
</div>


<!--Convert to ingredient-->
<div class="modal fade" id="conv_ingredient" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="conv_ingredient" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create ingredient from formula</h5>
      </div>
      <div class="modal-body">
        <div id="cnvMsg"></div>
        <div class="mb-3">
          <label for="ingName" class="form-label"><strong>Name</strong></label>
          <input name="ingName" type="text" class="form-control" id="ingName" value="<?=$f_name?>" />
        </div>
        <hr />
        <div class="alert alert-info">
          <i class="fa-solid fa-circle-exclamation mr-2"></i>The original formula will not be affected.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="conv2ing" value="Create">
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="manage-quantity" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="manage-quantity" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><div id="ingQuantityName"></div></h5>
      </div>
      <div class="card-body">
	
      	<div id="msgQuantity"></div>
        <input type="hidden" name="ingQuantityID" id="ingQuantityID" />
        <input type="hidden" name="ingQuantityName" id="ingQuantityName" />
        <input type="hidden" name="ingQuantity" id="ingQuantity" />
        <input type="hidden" name="mainingid" id="mainingid" />
        <input type="hidden" name="curQuantity" id="curQuantity" />
      	<div class="col">
        	<label for="ingQuantity" class="form-label mb-2">Quantity in <?=$settings['mUnit']?></label>
        	<input name="ingQuantity" type="text" class="ingQuantity form-control mb-3" id="ingQuantity">

            <div class="form-row">
        		<div class="col-md mb-3">
					<label for="reCalc" class="form-label">Adjust solvent</label>
        			<input type="checkbox" name="reCalc" id="reCalc" value="1" data-val="1" /> 
        
                    <div id="slvMeta">
                        <select name="formulaSolvents" id="formulaSolvents" class="formulaSolvents form-control"></select>
                        <div id="explain" class="mt-3 alert alert-info">Auto adjust total quantity by increasing or decreasing quantity from the selected solvent if enough available.<br>For example, if you add 1 more ml to the current ingredient, the selected solvent's quantity will be deducted by 1ml equally.</div>
                    </div>
				</div>
			</div>
      	</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="quantityConfirm" value="Update">
      </div>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="mrgIng" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="mrgIng" aria-hidden="true">
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
        <select name="mrgIngName" id="mrgIngName" class="mrgIngName pv-form-control"></select>
        <p>
        <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="mergeConfirm" value="Merge ingredients">
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="replaceIng" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="replaceIng" aria-hidden="true">
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
        <select name="repIngNameDest" id="repIngNameDest" class="repIngNameDest pv-form-control"></select>
        <p>
        <div class="dropdown-divider"></div>
        
        <div id="repGrid" class="card card-inverse card-reping">
         <div class="row mt-3">
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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="replaceConfirm" value="Replace ingredient">
      </div>
    </div>
  </div>
</div>

<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');

require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/php-settings.php');


$id = mysqli_real_escape_string($conn, $_GET['id']);


$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,fid,name,isProtected,finalType,defView,notes FROM formulasMetaData WHERE id = '$id'"));
$f_name = $meta['name'];
$fid = $meta['fid'];
?>



<div class="card-body">
	<div class="col-sm-10" id="progress-area">
      <div class="formula-progress-bar">
          <div id="base_bar" class="progress-bar pv_bar_base_notes" role="progressbar" aria-valuemin="0">
          	<span><div id="base_label"></div></span>
          </div>
          <div id="heart_bar" class="progress-bar pv_bar_heart_notes" role="progressbar" aria-valuemin="0">
          	<span><div id="heart_label"></div></span>
          </div>
          <div id="top_bar" class="progress-bar pv_bar_top_notes" role="progressbar" aria-valuemin="0">
          	<span><div id="top_label"></div></span>
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
           <li><a class="dropdown-item" href="/core/core.php?action=exportFormulas&fid=<?=$meta['fid']?>"><i class="fa-solid fa-file-code mx-2"></i>Export as JSON</a></li>
           <li><a class="dropdown-item" href="#" id="print"><i class="fa-solid fa-print mx-2"></i>Print fFormula</a></li>
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
           <li><a class="dropdown-item" href="#" id="cloneMe"><i class="fa-solid fa-copy mx-2"></i>Duplicate formula</a></li>
           <?php if($meta['isProtected'] == TRUE){?>
           <li><a class="dropdown-item" href="#" id="toggle-obs"><i class="fa-solid fa-user-ninja mx-2"></i>Toggle obscure Formula</a></li>
           <?php } ?>
        </div>
      </div>            
    </div>
</div>

<div id="row pv_search">
	<div class="text-right">
    	<label for="pvCustomSearch" class="mx-2"><a href="#" class="text-light-emphasis fs-6" rel="tip" title="Use comma (,) separated values to search for different ingredients"><i class="fa-solid fa-circle-info"></i></a></label>
    	<input type="text" id="pvCustomSearch" class="pvCustomSearch" placeholder="Search by CAS, Ingredient, etc..">
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
	$.fn.dataTable.ext.errMode = 'none';

  	var groupColumn = 0;
  	var formula_table = $('#formula').DataTable( {
		columnDefs: [
            { visible: false, targets: groupColumn },
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [10, 11] },
			<?php if($meta['defView'] == '3'){ ?>
			{ targets: [10], visible: false }
			<?php } ?>
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
			zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-warning"><strong><i class="fa-solid fa-circle-info mx-2"></i>Empty formula. Please add ingredients.</strong></div></div>',
			search: '',
			searchPlaceholder: "Search by CAS, Ingredient..."
		},
    	ajax: {
    		url: '/core/full_formula_data.php',
			type: 'POST',
			data:{
		   		id: myID,
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
			{ data : 'final_concentration', title: 'Final Concentration <?=$meta['finalType']?>%', render: ingFinalSetConc},
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
			if (response && response.meta) {
				var $tfoot = $(tfoot);
				if ($tfoot.length > 0) {
					var $td = $tfoot.find('th');
					
					// Update "Ingredients"
					if ($td.eq(0)) {
						$td.eq(0).html("Ingredients: " + (response.meta.total_ingredients || 0));
					}
					// Update "Total Quantity"
					if ($td.eq(4)) {
						$td.eq(4).html("Total: " + (response.meta.total_quantity || 0));
					}
					// Update "Total Concentration"
					if ($td.eq(5)) {
						$td.eq(5).html("Total: " + (response.meta.concentration || 0) + "%");
					}
					// Update "Total Cost"
					if ($td.eq(7)) {
						$td.eq(7).html("Total: " + (response.meta.currency || '') + (response.meta.total_cost || 0) + 
							'<i rel="tip" title="The total price for the 100% concentration." class="mx-2 pv_point_gen fas fa-info-circle"></i>'
						);
					}
					// Update table column header
					if (formula_table && formula_table.columns(7).header()) {
						$(formula_table.columns(7).header()).html("Final Concentration " + (response.meta.product_concentration || 0) + "%");
					}
					// Update "Max Usage"
					$('#max_usage').html('Max usage: ' + (response.meta.max_usage || 0) + '% ' +
						'<i rel="tip" title="This represents the maximum allowed usage in a final product for the selected IFRA category. <p>If your database contains missing or incomplete ingredient data, this will fail.</p>" class="mx-2 pv_point_gen fas fa-info-circle"></i>'
					);
				}
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
			  setAlertClassAndIcon(selector, 'alert-danger', `Max usage: ${limit}% <p>IFRA Regulated</p> ${restriction}`);
			} else if (regulator === "PV" && parseFloat(limit) < parseFloat(concentration)) {
			  switch (restriction) {
				case 1:
				  setAlertClassAndIcon(selector, 'alert-info', `Recommended usage: ${limit}% <p>PV Regulated</p>`);
				  break;
				case 2:
				  setAlertClassAndIcon(selector, 'alert-danger', `Restricted usage: ${limit}% <p>PV Regulated</p>`);
				  break;
				case 3:
				  setAlertClassAndIcon(selector, 'alert-warning', `Specification: ${limit}% <p>PV Regulated</p>`);
				  break;
				case 4:
				  setAlertClassAndIcon(selector, 'alert-warning', `Prohibited or Banned - <p>PV Regulated</p>`);
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
	}).on('error.dt', function(e, settings, techNote, message) {
            var m = message.split(' - ');
            $('#fetch_formula').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
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
						url: '/core/core.php', 
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
								$('.toast').toast('show');
							}else{
								$('#err').html('<div class="alert alert-danger"><strong>' + data.error + '</strong></div>');
							}
							
						},
						error: function (xhr, status, error) {
							$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error);
							$('.toast-header').removeClass().addClass('toast-header alert-danger');
							$('.toast').toast('show');
						},
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
			url: '/core/core.php', 
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
			},
			error: function (xhr, status, error) {
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			},
		  });					
	});
	
	function ingName(data, type, row, meta){
		var ex = '';
		var contains = '';
		var chkIng = '';
		var profile_class ='';
		var IFRAbyPASSED = '';
		
		if(row.ingredient.containsOthers.total){
			contains = '<i class="fa-solid fa-th-list expandAccord mx-2 pv_point_gen" rel="tip" title="Show/hide sub igredients"></i>';	
		}
		
		if(row.isIFRAbyPass === 1){
			IFRAbyPASSED = '<i class="ml-2 fas fa-triangle-exclamation" rel="tip" title="IFRA is by-passed"></i>';	
		}else{
			IFRAbyPASSED = '';
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
		
		if(row.chk_ingredient_code === 4){
			data ='<div class="btn-group"><a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+row.ingredient.name+'</a><div class="dropdown-menu dropdown-menu-right">';
			
			data+='<li><a class="dropdown-item popup-link" href="/pages/mgmIngredient.php?newIngName='+ btoa(row.ingredient.name) +'&newIngCAS='+ row.ingredient.cas +'"><i class="fa-solid fa-flask-vial mx-2"></i>Create ingredient</a></li>';
		
			data+='<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#import_ingredients_json"><i class="bi bi-filetype-json mx-2"></i>Import from JSON</a></li>';
			data+='<li><a class="dropdown-item" href="https://library.perfumersvault.com/query/' + row.ingredient.name+ '" target="_blank"><i class="fa-solid fa-cloud-arrow-down mx-2"></i>Search PV Library<i class="ml-2 fa-solid fa-arrow-up-right-from-square"></i></a></li>';
					
			data+='</div></div>';
			return data;
		}
		
		if(row.ingredient.enc_id){
			data = contains + '<a class="popup-link '+ex+'" href="/pages/mgmIngredient.php?id=' + row.ingredient.id + '">' + data + '</a> '+ chkIng + IFRAbyPASSED + profile_class;
		}else{
			data = '<a class="popup-link '+ex+'" href="/pages/mgmIngredient.php?id=' + btoa(data) + '">' + data + '</a> '+ chkIng + profile_class;
	
		}
	
	  return data;
	};
	
	function ingCAS(data, type, row, meta){
		if(type === 'display'){
			data = '<i class="pv_point_gen" rel="tip" title="Click to copy" id="cCAS" data-name="'+row.ingredient.cas+'">'+row.ingredient.cas+'</i>';
		}
		return data;
	};
	  
	function ingConc(data, type, row, meta){
	  if( isProtected == false ){
		  if( row.ingredient.profile == "Solvent"){
			  data = 100;
		  }else{
			data = '<a href="#" data-name="concentration" class="concentration" data-type="text" data-pk="' + row.formula_ingredient_id + '">' + data + '</a>';
		  }
	  }
	
	  return data;
	};
	
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
	};
	  
	function ingQuantity(data, type, row, meta){
		if( isProtected == false ){
			<?php if($settings['editor'] == '1'){?>
				data = '<a href="#" data-name="quantity" class="quantity" data-type="text" data-pk="' + row.formula_ingredient_id + '">' + row.quantity + '</a>';
			<?php }else{?>
				data = '<a href="#" data-name="quantity" data-bs-toggle="modal" data-bs-target="#manage-quantity" class="open-quantity-dialog" data-type="text" data-ingid="' + row.formula_ingredient_id + '" data-value="' + row.quantity + '" data-ing="' + row.ingredient.name + '" data-mainingid="'+row.ingredient.id+'">' + row.quantity + '</a>';
			<?php } ?>
		} else {
			data = '<div class="quantity-details">' + row.quantity + '</div>';	
		}
	
		return data;
	};
	
	function ingSetConc(data, type, row, meta){
		return '<div class="concentration-details">' + row.concentration + '</div>';
	};
	
	function ingFinalSetConc(data, type, row, meta){
		return '<div class="final-concentration-details">' + row.final_concentration + '</div>';
	};
	
	function ingNotes(data, type, row, meta){
		 if(type === 'display'){
		  <?php if($meta['defView'] == '1'){ $show = 'properties'; }elseif($meta['defView'] == '2'){ $show = 'notes';}?>
		  <?php if($meta['isProtected'] == FALSE){?>
		  data = '<i data-name="<?=$show?>" class="pv_point_gen text-wrap <?=$show?>" data-type="textarea" data-pk="' + row.formula_ingredient_id + '">' + data + '</i>';
		  <?php } ?>
		 }
		return data;
	};
	  
	  
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
	};
	
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
		
		var obsDiv = $(".quantity-details, .concentration-details, .final-concentration-details");
		
		if (sessionStorage.getItem("obsState") === "true") {
			obsDiv.addClass("div-obs");
		} else {
			obsDiv.removeClass("div-obs");
		}
		if(isProtected == false){
			 if (obsDiv.hasClass("div-obs")) {
				obsDiv.removeClass("div-obs");
				sessionStorage.setItem("obsState", "false");
			}
		}
		$("#toggle-obs").click(function (e) {
			e.preventDefault();
	
			if (obsDiv.hasClass("div-obs")) {
				obsDiv.removeClass("div-obs");
				sessionStorage.setItem("obsState", "false");
			} else {
				obsDiv.addClass("div-obs");
				sessionStorage.setItem("obsState", "true");
			}
		});
	};
	  
	$('#formula').editable({
		container: 'body',
		selector: 'a.concentration',
		url: "/core/core.php?formula=" + myFID,
		title: 'Purity %',
		ajaxOptions: {
			type: "POST",
			dataType: 'json'
		},
		success: function (data) {
			if ( data.success ) {
				reload_formula_data();
			} else if ( data.error ) {
				$('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		},
		error: function (xhr, status, error) {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
			$('.toast').toast('show');
		},
		validate: function(value){
			if($.trim(value) == ''){
				return 'This field is required';
			}
			if($.isNumeric(value) == '' ){
				return 'Numbers only';
			}
		}
	});
	<?php if($settings['editor'] == '1'){?>
	$('#formula').editable({
		container: 'body',
		selector: 'a.quantity',
		url: "/core/core.php?formula=" + myFID,
		title: 'Quantity in <?=$settings['mUnit']?>',
		ajaxOptions: {
			type: "POST",
			dataType: 'json'
		},
		success: function (data) {
			if ( data.success ) {
				reload_formula_data();
			} else if ( data.error ) {
				$('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		},
		error: function (xhr, status, error) {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
			$('.toast').toast('show');
		},
		validate: function(value){
			if($.trim(value) == ''){
				return 'This field is required';
			}
			if($.isNumeric(value) == '' ){
				return 'Numbers only';
			}
		}
	});
	
	<?php } ?>
	$('#formula').editable({
		container: 'body',
		selector: 'a.solvent',
		emptytext: "",
		emptyclass: "",
		url: "/core/core.php?formula=" + myFID,
		title: 'Choose solvent',
		source: [
		<?php
			$res_ing = mysqli_query($conn, "SELECT id, name FROM ingredients WHERE type = 'Solvent' OR type = 'Carrier' ORDER BY name ASC");
			while ($r_ing = mysqli_fetch_array($res_ing)){
			echo '{value: "'.$r_ing['name'].'", text: "'.$r_ing['name'].'"},';
		}
		?>
		],
		ajaxOptions: {
			type: "POST",
			dataType: 'json'
		},
		success: function (data) {
			if ( data.success ) {
				reload_formula_data();
			} else if ( data.error ) {
				$('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		},
		error: function (xhr, status, error) {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
			$('.toast').toast('show');
		},
	
	});
	
	$('#formula').editable({
	  container: 'body',
	  selector: 'i.notes',
	  url: "/core/core.php?formula=" + myFID,
	  title: 'Notes',
		ajaxOptions: {
			type: "POST",
			dataType: 'json'
		},
		success: function (data) {
			if ( data.success ) {
				reload_formula_data();
			} else if ( data.error ) {
				$('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		},
		error: function (xhr, status, error) {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
			$('.toast').toast('show');
		},
	});
	
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
						url: '/core/core.php', 
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
						},
						error: function (xhr, status, error) {
							$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred, check server logs for more info. '+ error);
							$('.toast-header').removeClass().addClass('toast-header alert-danger');
							$('.toast').toast('show');
						},
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
	
	$('#print').click(() => {
		$('#formula').DataTable().button(0).trigger();
	});
	
});//doc ready


function reload_formula_data() {
    $('#formula').DataTable().ajax.reload(null, true);
	update_bar();
	reset_solv();
};


</script>
<script src="/js/fullformula.view.js"></script>
<script src="/js/mark/jquery.mark.min.js"></script>
<script src="/js/mark/datatables.mark.js"></script>
<script src="/js/import.ingredients.js"></script>

<!--Schedule Formula-->
<div class="modal fade" id="schedule_to_make" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="schedule_to_make" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Schedule formula to make</h5>
      </div>
      <div class="modal-body">
      <div id="scheduleToMakeMsg"></div>
      <div class="alert alert-info"><i class="fa-solid fa-circle-exclamation mx-2"></i>This will add the current formulation to scheduled formulas. Any changes in this formula will not be replicated to the scheduled version. If you make changes here, you have to remove it and re-add it for making.</div>
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


<div class="modal fade" id="manage-quantity" data-bs-backdrop="static" tabindex="-1" aria-labelledby="manageQuantityLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="manageQuantityLabel"><span id="ingQuantityName"></span></h5>
      </div>
      <div class="modal-body">
        <div id="msgQuantity"></div>
        
        <input type="hidden" name="ingQuantityID" id="ingQuantityID" />
        <input type="hidden" name="ingQuantityName" id="ingQuantityNameHidden" />
        <input type="hidden" name="ingQuantity" id="ingQuantityHidden" />
        <input type="hidden" name="mainingid" id="mainingid" />
        <input type="hidden" name="curQuantity" id="curQuantity" />
        
        <div class="mb-3">
          	<label for="ingQuantity" class="form-label">Quantity in <?= $settings['mUnit'] ?></label>
           	<div class="input-group">
          		<input name="ingQuantity" type="text" class="form-control" id="ingQuantity">
          		<span class="input-group-text" id="quantity-addon"><?=$settings['mUnit']?></span>
			</div>
        </div>

        <div class="form-check mb-3">
          <input type="checkbox" class="form-check-input" name="reCalc" id="reCalc" value="1" data-val="1">
          <label class="form-check-label" for="reCalc">Adjust solvent</label>
        </div>

        <div id="slvMeta" class="mb-3">
          <label for="formulaSolvents" class="form-label">Select Solvent</label>
          <select name="formulaSolvents" id="formulaSolvents" class="form-select"></select>
          <div id="explain" class="mt-3 alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i>Auto adjust the total quantity by increasing or decreasing quantity from the selected solvent if enough is available.<br>
            For example, if you add 1 more <?= $settings['mUnit'] ?> to the current ingredient, the selected solvent's quantity will be deducted by 1<?= $settings['mUnit'] ?> equally.
          </div>
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" id="quantityConfirm">Update</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="mrgIng" data-bs-backdrop="static" tabindex="-1" aria-labelledby="mrgIngLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mrgIngLabel">Merge ingredients</h5>
      </div>
      <div class="modal-body">
        <div id="msgMerge"></div>
        <input type="hidden" name="ingSrcID" id="ingSrcID" />
        <input type="hidden" name="ingSrcName" id="ingSrcName" />
        
        <div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i>You can merge <span id="srcIng"></span>'s quantity with another material in the formula. Use this method if the materials are similar. Please note, this action cannot be reverted, and the quantity will be added to the target ingredient's quantity.
        </div>
        
        <div class="mb-3">
          Merge <span id="srcIng"></span> with: 
          <select name="mrgIngName" id="mrgIngName" class="form-select"></select>
        </div>
        
        <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" id="mergeConfirm">Merge ingredients</button>
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
      	<div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i>Replace <div id="ingRepName"></div> with another ingredient, quantity and dilution values will not be affected.</div>
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

<!--IMPORT JSON MODAL-->
<div class="modal fade" id="import_ingredients_json" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="import_ingredients_json" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import ingredients from a JSON file</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                	<li>Any ingredient with the same id will be replaced. Please make sure you have taken a backup before imporing a JSON file.</li>
              	</ul>
            </div>
          </div>
      	</div>
	  		<div class="modal-footer">
        		<input type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK" value="Cancel">
        		<input type="submit" name="btnRestore" class="btn btn-primary" id="btnRestoreIngredients" value="Import">
      		</div>
  		</div>  
	</div>
</div>

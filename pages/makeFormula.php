<?php
define('pvault_panel', TRUE);
session_start();

define('__ROOT__', dirname(dirname(__FILE__))); 

if(!isset($_SESSION['parfumvault'])){
	$redirect = '?url=/pages/makeFormula.php?fid='.$_GET['fid'];
	$login = '/login.php'.$redirect;
	header('Location: '.$login);
	exit;
}
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');


$fid = mysqli_real_escape_string($conn, $_GET['fid']);
if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid'")) == FALSE){
	echo 'Formula doesn\'t exist';
	return;
}
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE fid = '$fid'"));

if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid' AND toAdd = '1'"))){
		$msg = '<div class="alert alert-warning"><a href="#" id="markComplete"><strong>All materials added. Mark formula as complete?</strong></a></div>';

}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
    <head>
	  <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
      <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
      <meta name="description" content="<?php echo trim($product).' - '.trim($ver);?>">
      <meta name="author" content="<?php echo trim($product).' - '.trim($ver);?>">
      <title><?php echo $meta['name'];?></title>
        <link href="/css/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" type="text/css">

      <link href="/css/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
      <link href="/css/sb-admin-2.css" rel="stylesheet">
      <script src="/js/jquery/jquery.min.js"></script>
      <script src="/js/bootstrap.bundle.min.js"></script>
      <link href="/css/bootstrap.min.css" rel="stylesheet">
      <script src="/js/jquery-ui.js"></script>
      <link href="/css/jquery-ui.css" rel="stylesheet">
      <script src="/js/datatables.min.js"></script>
      <link href="/css/datatables.min.css" rel="stylesheet"/>
      <link href="/css/vault.css" rel="stylesheet">
      <link href="/css/select2.css" rel="stylesheet">

      <script src="/js/tableHTMLExport.js"></script>
      <script src="/js/jspdf.min.js"></script>
      <script src="/js/jspdf.plugin.autotable.js"></script>
      <script src="/js/bootbox.min.js"></script>
      <script src="/js/select2.js"></script> 

      <style>
        .table {
            --bs-table-bg:  initial;
        }
        table.dataTable {
            font-size: x-large !important;
            font-weight: bold;
        }
        .mr {
            margin: 20px 20px 20px 20px;
            display: inline;
        }
        @media print {
            table, table tr, table td {
                border-top: #000 solid 2px;
                border-bottom: #000 solid 2px;
                border-left: #000 solid 2px;
                border-right: #000 solid 2px;
                font-family: arial, sans-serif;
                font-weight: bold;
                width: 50%;
                margin-left: 1px;
                font-size: 15pt;
                page-break-inside: auto;
                page-break-inside: avoid; 
                page-break-after: auto;
            }
        }
        
      </style>
</head>

    <div class="container-fluid">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h2 class="m-0 font-weight-bold text-primary"><a href="#" id="mainTitle"><?php echo $meta['name']; ?></a></h2>
        </div>
        <div class="card-body">
          <div class="table-responsive">
          <div id="errors"></div>
          <div id="msg"><?=$msg?></div>
          <div class="btn-group mb-3" id="menu">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
            <div class="dropdown-menu dropdown-menu-left">
               <li><a class="dropdown-item" href="#" id="markCompleteMenu"><i class="fa-solid fa-check mx-2"></i>Mark formula as complete</a></li>
               <div class="dropdown-divider"></div>
               <li class="dropdown-header">Options</li>
               <li><a class="dropdown-item" href="#" id="toggleAdded"><i class="bi bi-list-check mx-2"></i>Show/hide added</a></li>

               <div class="dropdown-divider"></div>
               <li class="dropdown-header">Export</li>
          		<li><a class="dropdown-item" href="/pages/operations.php?action=exportMaking&fid=<?=$fid?>"><i class="fa-solid fa-file-code mx-2"></i>Export as JSON</a></li>
               <li><a class="dropdown-item export_as" href="#" data-format="csv"><i class="fa-solid fa-file-csv mx-2"></i>Export as CSV</a></li>
               <li><a class="dropdown-item export_as" href="#" data-format="pdf"><i class="fa-solid fa-file-code mx-2"></i>Export as PDF</a></li>
               <li><a class="dropdown-item" href="#" id="print"><i class="fa-solid fa-print mx-2"></i>Print Formula</a></li>
            </div>
        </div>
            <table class="table table-bordered" id="tdDataPending" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Ingredient</th>
                  <th>Purity</th>
                  <th>Quantity</th>
                  <th>Availability</th>
                  <th data-priority="1"></th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                <th>Total ingredients:</th>
                <th></th>
                <th>Quantity:</th>
                <th></th>
                <th></th>
                </tr>
            </tfoot>
            </table>
          </div>
        </div>
      </div>
      
     <footer class="pvScale-data-footer mx-2 bg-dark p-5 text-dark-emphasis bg-dark-subtle border border-dark-subtle rounded-3" id="pvScale-data-footer">
        <h3 class="text-dark" id="scale-reading"></h3> 
    </footer>
    
   </div>
    
<script>
var myFNAME = "<?=$meta['name']?>";
$(document).ready(function() {
	$('#pvScale-data-footer').addClass('d-none');
	$('#mainTitle').click(function() {
	 	reload_data();
  	});
	
	$('#liveToast').toast({
		delay: 10000
	});
	var tdDataPending = $('#tdDataPending').DataTable( {
	columnDefs: [
		{ className: 'pv_vertical_middle text-center', targets: '_all' },
		{ orderable: false, targets: [3,4] },
		{ responsivePriority: 1, targets: 0 }
	],
	dom: 'lrft',
	buttons: [{
				extend: 'print',
				title: myFNAME,
				exportOptions: {
					columns: [0, 1, 2, 3]
				},
			  }],
	processing: true,
	serverSide: true,
	searching: true,
	mark: true,
	responsive: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: 'Please Wait...',
		zeroRecords: 'No pending ingredients found',
		search: 'Quick Search:',
		searchPlaceholder: 'Ingredient..',
		},
	ajax: {	
		url: '/core/pending_formulas_data.php?meta=0&fid=<?=$fid?>',
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
			{ data : 'ingredient', title: 'Ingredient', render: ingredient},
			{ data : 'concentration', title: 'Purity %' },
			{ data : 'quantity', title: 'Quantity (<?=$settings['mUnit']?>)', render: quantity },
			{ data : 'inventory.stock', title: 'Availability', render: stock },
			{ data : null, title: 'Actions', className: 'text-center noexport', render: actions },
		],
	  	footerCallback : function( tfoot, data, start, end, display ) {    
		  var response = this.api().ajax.json();
		  	if(response){
			 	var $td = $(tfoot).find('th');
			 	$td.eq(0).html("Ingredients left: "+ response.meta['total_ingredients_left'] + ' of ' + response.meta['total_ingredients'] );
			 	$td.eq(2).html("Total left: " + response.meta['total_quantity_left'] + ' of ' + response.meta['total_quantity'] + response.meta['quantity_unit'] );
			  	total_quantity = response.meta['total_quantity'];
		 	}
	  	},
	  	fnRowCallback : function (row, data, display) {
			if (data.toAdd == 0) {
				$(row).find('td:eq(0),td:eq(1),td:eq(2),td:eq(3)').addClass('strikeout');
		  	}
		  	if (data.toSkip == 1) {
			  	$(row).find('td:eq(0),td:eq(1),td:eq(2),td:eq(3)').addClass('skipped');
		  	}
		  	$(row).addClass('pv-zoom');
			if (data.toAdd == 0) {
        		$(row).addClass('d-none');
    		}
			$('#toggleAdded').click(function() {
				if (data.toAdd == 0) {
					$(row).toggleClass('d-none');
				}
			});
	 	},
	 	order: [[ 0, 'asc' ]],
	 	lengthMenu: [[200, 500, 1000], [200, 500, 1000]],
	 	pageLength: 200,
	 	displayLength: 200,
	});
	
	$('#tdDataPending').on('mouseenter', '.pv-zoom', function() {
		$(this).addClass('pv-transition');
	});
	
	$('#tdDataPending').on('mouseleave', '.pv-zoom', function() {
		$(this).removeClass('pv-transition');
	});
	<?php if($settings['pv_scale_enabled']) { ?>
	$('#tdDataPending tbody').on('click', 'tr', function () {
		var rowData = tdDataPending.row(this).data();
		rowClickedFunction(rowData);
	});
	<?php } ?>



	function ingredient(data, type, row){
		data = '<a href="#infoModal" id="ingInfo" data-bs-toggle="modal" data-id="'+row.ingID+'" data-name="'+row.ingredient+'" class="listIngNameCas-with-separator">' + row.ingredient + '</a><span class="listIngHeaderSub"> CAS: <i class="subHeaderCAS">'+row.cas+'</i></span>';
		return data;
	}
	
	function quantity(data, type, row){
		var overdose = '';
		if(row.overdose != 0 ){
			var overdose = '<span class="ing_alg"> <i rel="tip" title="Overdosed, added '+row.overdose+', instead" class="fas fa-exclamation-triangle"></i></span>';
		}
		
		data = row.quantity + overdose;
		return data;
	}
	
	function actions(data, type, row){
		var data;
		//if (row.quantity != row.originalQuantity) {
			data = '<i id="undo_add" data-row-id="'+row.id+'" data-ingredient="'+row.ingredient+'" data-originalQuantity="'+row.originalQuantity+'" data-rep-name = "'+row.repName+'" data-rep-id = "'+row.repID+'" data-ingID = "'+row.ingID+'" class="mr fas fa-undo pv_point_gen" title="Reset original quantity for '+row.ingredient+'"></i>';
		//}
		
		if (row.toAdd == 1 && row.toSkip == 0) {
			data += '<i data-bs-toggle="modal" data-bs-target="#confirm_add" data-quantity="'+row.quantity+'" data-ingredient="'+row.ingredient+'" data-row-id="'+row.id+'" data-ing-id="'+row.ingID+'" data-qr="'+row.quantity+'" class="mr fas fa-check pv_point_gen" title="Confirm add '+row.ingredient+'"></i>';
			
		   data += '<i data-bs-toggle="modal" data-bs-target="#confirm_skip" data-quantity="'+row.quantity+'" data-ingredient="'+row.ingredient+'" data-row-id="'+row.id+'" data-ing-id="'+row.ingID+'" data-qr="'+row.quantity+'" class="mr fas fa-forward pv_point_gen" title="Skip '+row.ingredient+'"></i>';
	
		}
		
						  
		data += '<i data-ingredient="'+row.ingredient+'" data-quantity="'+row.quantity+'" data-concentration="'+row.concentration+'" data-ingID="'+row.ingID+'" id="addToCart" class="mr fas fa-shopping-cart pv_point_gen"></i>'; 
						 
		return data;    
	}
	
	function stock(data, type, row){
	
		var st;
		
		if (parseFloat(row.inventory.stock) >= parseFloat(row.quantity)){
			st = '<i class = "stock2 badge badge-instock">Enough in stock: '+row.inventory.stock+''+row.inventory.mUnit+'</i>';
		
		}else if (parseFloat(row.inventory.stock) < parseFloat(row.quantity) && row.inventory.stock != 0){
			st = '<i class = "stock2 badge badge-notenoughstock">Not Enough in stock: '+row.inventory.stock+''+row.inventory.mUnit+'</i>';
		
		}else{
			st = '<i class = "stock2 badge badge-nostock">Not in stock: '+row.inventory.stock+''+row.inventory.mUnit+'</i>';
		}
		
		return st;
	}
	
	function reload_data() {
		$('#tdDataPending').DataTable().ajax.reload(null, true);
	}
	
	
	<?php if($settings['pv_scale_enabled']) { ?>
		var pvScaleHost = "<?php echo $settings['pv_scale_host']?: '0.0.0.0'; ?>";	
		var ws = new WebSocket("ws://" + pvScaleHost + ":81");
		$('#scale-reading').addClass("spinner-border spinner-border-sm");
		
		$.ajax({
			url: '/pages/views/pvscale/manage.php',
			type: 'POST',
			data: {
				ping: 1,
				pv_scale_host: pvScaleHost
			},
			dataType: 'json',
			success: function(data) {
				if (data.success === true) {
					ws.onmessage = function(r) {			
						$('#scale-reading').removeClass("spinner-border spinner-border-sm");
	
						console.log("Received reading: " + r.data);
						$('#scale-reading').html('<strong>' + r.data + '</strong>');
					};
					// When WebSocket connection is closed
					ws.onclose = function() {
						console.log("WS closed");
					};
				} else {
					$('#scale-reading').removeClass("spinner-border spinner-border-sm");
					$('#scale-reading').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Connection failed</div>');
				}
			},
			error: function() {
				$('#scale-reading').removeClass("spinner-border spinner-border-sm");
				$('#scale-reading').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Network error</div>');
			},
			complete: function() {
	
			   
			}
		});
		
	
		
		
		
			
		$('#pvScale-data-footer').removeClass('d-none');
		function pollReloadSignal() {
			setInterval(function() {
				$.ajax({
					url: '/pages/views/pvscale/manage.php?action=check_reload_signal',
					type: 'GET',
					success: function(response) {
						if (response === 'reload') {
							reload_data();
							$.ajax({
								url: '/pages/views/pvscale/manage.php?action=update_reload_signal',
								type: 'GET'
							});
						}
					},
					error: function(xhr, status, error) {
						console.error(xhr.responseText);
					}
				});
			}, 5000);
		}
		
		pollReloadSignal();
	
			
	function rowClickedFunction(data) {
		$('#toast-title').html('<i class="fa-solid fa-circle-info mr-2"></i>Connecting to the PV Scale...');
		$('.toast-header').removeClass().addClass('toast-header alert-warning');
		$.ajax({
			type: 'POST',
			url: "/pages/views/pvscale/manage.php?action=send2PVScale",
			data: JSON.stringify({
				"formulas": [
					{
						"id": data.id,
						"fid": data.fid,
						"name": data.name,
						"cas" : data.cas,
						"odor" : data.odor,
						"ingredient": data.ingredient,
						"ingredient_id": data.ingID,
						"concentration": data.concentration,
						"dilutant": "-",
						"quantity": data.quantity,
						"stock" : data.inventory.stock,
						"mUnit" : data.inventory.mUnit,
						"pending" : data.toAdd,
						"ApiKey" : "<?php echo $settings['api_key'];?>"
					},
				],
				"pvMeta": {
					"ingredients": 1,
					"host": "<?=$settings['pv_host']?>",
					"mUnit" : "<?php echo $settings['mUnit'];?>"
				}
			}),
			contentType: 'application/json',
			dataType: 'json',
			success: function(data) {
				if(data.success == true){
					$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>Scale data updated');
					$('.toast-header').removeClass().addClass('toast-header alert-success');
				}else if(data.success == false){
					$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
					$('.toast-header').removeClass().addClass('toast-header alert-danger');
				}
			},
			error: function(err) {
				//console.log(err);
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>Unable to communicate with the scale.');
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
			},
			timeout: 3000
		});
		$('.toast').toast('show');
	};
	<?php } ?>
	
	
	$('#tdDataPending').on('click', '[id*=ingInfo]', function () {
		var id = $(this).data('id');
		var name = $(this).data('name');
		
		$('.modal-title').html(name);   
		$('.modal-body-info').html('loading');
		
		$.ajax({
		   type: 'GET',
		   url: '/pages/views/ingredients/getIngInfo.php',
		   data:{
			   ingID: id
			},
		   success: function(data) {
			 $('.modal-body-info').html(data);
		   },
		   error:function(err){
			data = '<div class="alert alert-danger">Unable to get ingredient info</div>';
			$('.modal-body-info').html(data);
		   }
		})
	 });
	
	
	$('#title').click(function() {
		$('#msg').html('');
	});
	
	$('#print').click(() => {
		$('#tdDataPending').DataTable().button(0).trigger();
	});
	
	var repName;
	var repID;
	$('#tdDataPending').on('click', '[data-bs-target*=confirm_add]', function () {
		$('#errMsg').html('');																
		$("#ingAdded").text($(this).attr('data-ingredient'));
		$("#ingID").text($(this).attr('data-ing-id'));
		$("#idRow").text($(this).attr('data-row-id'));
		$("#amountAdded").val($(this).attr('data-quantity'));
		$("#qr").text($(this).attr('data-qr'));
		$("#updateStock").prop( "checked", true );
		$("#notes").val('');
		$("#collapseAdvanced").removeClass('show');
		
		$('#msgReplace').html('');
		$("#replacement").val('');
	
		var ingSrcName = $(this).attr('data-ingredient')
		var ingSrcID = $(this).attr('data-ing-id')	
		
		repName = "";
		repID = "";
		
		$("#replacement").select2({
			width: '100%',
			placeholder: 'Search for ingredient (name)..',
			allowClear: true,
			dropdownAutoWidth: true,
			containerCssClass: "replacement",
			dropdownParent: $('#confirm_add .modal-content'),
			templateResult: formatIngredients,
			templateSelection: formatIngredientsSelection,
			ajax: {
				url: '/pages/views/ingredients/getIngInfo.php',
				dataType: 'json',
				type: 'GET',
				delay: 100,
				quietMillis: 250,
				data: function (params) {
					return {
						ingID: ingSrcID,
						replacementsOnly: true,
						search: params.term
					};
				},
				processResults: function(data) {
					return {
						results: $.map(data.data, function(obj) {
						  return {
							id: obj.id,
							stock: obj.stock,
							name: obj.name,
						  }
						})
					};
				},
				cache: false,
				
			}
			
		}).on('select2:selecting', function (e) {
			repName = e.params.args.data.name;
			repID = e.params.args.data.id;
		});
	});
	
	function formatIngredients (ingredientData) {
		if (ingredientData.loading) {
			return ingredientData.name;
		}
	 
		//extrasShow();
	
		if (!ingredientData.name){
			return 'No replacement found...';
		}
		
		
		var $container = $(
			"<div class='select_result_igredient clearfix'>" +
			  "<div class='select_result_igredient_meta'>" +
				"<div class='select_igredient_title'></div>" +
				"<span id='stock'></span></div>"+
			  "</div>" +
			"</div>"
		  );
		
		  $container.find(".select_igredient_title").text(ingredientData.name);
		  if(ingredientData.stock  > 0){
			$container.find("#stock").text('In stock ('+ingredientData.stock+')');
			$container.find("#stock").attr("class", "stock badge badge-instock");
		  }else{
			$container.find("#stock").text('Not in stock ('+ingredientData.stock+')');
			$container.find("#stock").attr("class", "stock badge badge-nostock");
		  }
	
		  return $container;
	}
	
	
	function formatIngredientsSelection (ingredientData) {
		return ingredientData.name;
	}
	
	$('#tdDataPending').on('click', '[data-bs-target*=confirm_skip]', function () {
		$('#errMsg').html('');																
		$("#ingSkipped").text($(this).attr('data-ingredient'));
		$("#ingID").text($(this).attr('data-ing-id'));
		$("#idRow").text($(this).attr('data-row-id'));
		$("#notes").val('');
	});
	
	//UPDATE AMOUNT
	$('#addedToFormula').click(function() {
		$.ajax({ 
		url: '/pages/manageFormula.php', 
		type: 'POST',
		data: {
			action: "makeFormula",
			q: $("#amountAdded").val(),
			notes: $("#notes").val(),
			qr: $("#qr").text(),
			updateStock: $("#updateStock").is(':checked'),
			ing: $("#ingAdded").text(),
			id: $("#idRow").text(),
			repName: repName,
			repID: repID,
			ingId: $("#ingID").text(),
			fid: "<?php echo $fid; ?>",
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
				$('.toast-header').removeClass().addClass('toast-header alert-success');
				$('#confirm_add').modal('toggle');
				reload_data();
				$('.toast').toast('show');
			} else if(data.error) {
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				$('#errMsg').html(msg);
			}
		}
	  });
	});
	
	//SKIP ADD
	$('#skippedFromFormula').click(function() {
		$.ajax({ 
		url: '/pages/manageFormula.php', 
		type: 'POST',
		data: {
			action: "skipMaterial",
			notes: $("#skip_notes").val(),
			ing: $("#ingSkipped").text(),
			id: $("#idRow").text(),
			ingId: $("#ingID").text(),
			fid: "<?php echo $fid; ?>",
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
				$('.toast-header').removeClass().addClass('toast-header alert-success');
				$('#confirm_skip').modal('toggle');
				reload_data();
				$('.toast').toast('show');
			} else if(data.error) {
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				$('#errMsg').html(msg);
			}
		}
	  });
	});
	//ADD TO CART
	$('#tdDataPending').on('click', '[id*=addToCart]', function () {
	$.ajax({ 
		url: '/pages/manageFormula.php', 
		type: 'POST',
		data: {
			action: "addToCart",
			material: $(this).attr('data-ingredient'),
			purity: $(this).attr('data-concentration'),
			quantity: $(this).attr('data-quantity'),
			ingID: $(this).attr('data-ingID')
			},
		dataType: 'json',
		success: function (data) {
			if(data.success) {
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				reload_data();
			} else {
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
			$('#msg').html(msg);
			
		}
	  });
	});
	

	 
	$('#tdDataPending').on('click', '[id*=undo_add]', function () {
		var d = {};
		d.ID = $(this).attr('data-row-id');
		d.ingName = $(this).attr('data-ingredient');
		d.ingID = $(this).attr('data-ingID');
		d.repID = $(this).attr('data-rep-id');
		d.repName = $(this).attr('data-rep-name');
	
		d.originalQuantity = $(this).attr('data-originalQuantity');
		bootbox.dialog({
		   title: "Confirm reset quantity",
		   message : 'Reset <strong>'+ d.ingName +'\'s</strong> quantity to <strong>'+ d.originalQuantity +'</strong>?' +
		   '<hr />' 
		   +'<input name="resetStock" id="resetStock" type="checkbox" value="1" checked> Reset stock',
		   buttons :{
			   main: {
			   label : "Reset",
			   className : "btn-danger",
			 callback: function (){
			 $.ajax({ 
				url: '/pages/manageFormula.php', 
					type: 'POST',
					data: {
						action: "makeFormula",
						undo: 1,
						ing: d.ingName,
						ingID: d.ingID,
						repID: d.repID,
						repName: d.repName,
						originalQuantity: d.originalQuantity,
						resetStock: $("#resetStock").is(':checked'),
						ID: d.ID
					},
					dataType: 'json',
					success: function (data) {
						if(data.success){
							$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
							$('.toast-header').removeClass().addClass('toast-header alert-success');
							reload_data();
							bootbox.hideAll();
							$('.toast').toast('show');
						}else{
							$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
							 var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
						}	
						$('#msg').html(msg);
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

	
	$('#markComplete, #markCompleteMenu').click(function() {
		   bootbox.dialog({
		   title: "Confirm formula completion",
		   message : "Mark formula <strong> <?php echo $meta['name'];?></strong> as complete?",
		   buttons :{
			  main: {
			  label : "Mark as complete",
			  className : "btn-warning",
			 callback: function (){
			 $.ajax({ 
				url: 'manageFormula.php', 
					type: 'POST',
					data: {
						action: "todo",
						markComplete: 1,
						totalQuantity: total_quantity,
						fid: "<?php echo $fid; ?>"
					},
					dataType: 'json',
					success: function (data) {
						if(data.success) {
							var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
							reload_data();
						} else {
							var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
						}	
						$('#msg').html(msg);
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
	
	$('.export_as').click(function() {	
	  var format = $(this).attr('data-format');
	  $("#tdDataPending").tableHTMLExport({
		type: format,
		filename: myFNAME + "." + format,
		separator: ',',
		newline: '\r\n',
		trimContent: true,
		quoteFields: true,
		ignoreColumns: '.noexport',
		ignoreRows: '.noexport',
		htmlContent: false,
		orientation: 'l',
		subtitle: 'Created with Perfumer\'s Vault Pro',
		maintitle: myFNAME,
	  });
	});

	
});//DOC END


 
</script>
<script src="/js/validate-session.js"></script>
<script src="/js/mark/jquery.mark.min.js"></script>
<script src="/js/mark/datatables.mark.js"></script>
    
    <!-- Modal ING Info -->
    <div class="modal fade" id="infoModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                    <div class="modal-body-info">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- TOAST -->
    <div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 11">
      <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
        <div class="toast-header">
          <strong class="me-auto" id="toast-title">...</strong>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>

    <!-- Modal Confirm amount-->
    <div class="modal fade" id="confirm_add" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="confirm_add" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
          <div style="display: none;" id="ingID"></div>
          <div style="display: none;" id="idRow"></div>
          <div style="display: none;" id="qr"></div>
          <h5 class="modal-title" id="ingAdded"></h5>
        </div>
        <div class="modal-body">
        <div id="errMsg"></div>
            
              <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="amountAdded">Amount added</label>
                    <input name="amountAdded" type="text" id="amountAdded" />
                </div>
              </div>
              
              <div class="dropdown-divider"></div>
              
              <div class="form-row">
                <div class="form-group col-md-6">
                    <input name="updateStock" id="updateStock" type="checkbox" value="1" checked>
                    <label class="form-check-label" for="updateStock">Update stock</label>
                </div>
              </div>
              <hr class="border border-default border-1 opacity-75">
              
              <div class="form-group">
                <label for="notes">Notes</label>
                <textarea class="form-control" id="notes" rows="3"></textarea>
              </div>
              <hr class="border border-default border-1 opacity-75">
    		  <a class="link-primary" data-bs-toggle="collapse" href="#collapseAdvanced" aria-expanded="false" aria-controls="collapseAdvanced">Advanced</a>
              
                <div class="collapse" id="collapseAdvanced">
                
                <div class="card card-body">
        		  <label for="replacement" class="form-label">Select a replacement</label>
   				  <select name="replacement" id="replacement" class="replacement pv-form-control"></select>
  				</div>
                </div>
              
              <hr class="border border-default border-1 opacity-75">
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <input type="submit" name="addedToFormula" class="btn btn-primary" id="addedToFormula" value="Confirm">
              </div>
         
        </div>
      </div>
    </div>
  </div>
  
      <!-- Modal Skip material-->
    <div class="modal fade" id="confirm_skip" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="confirm_skip" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
          <div style="display: none;" id="ingID"></div>
          <div style="display: none;" id="idRow"></div>
          <h5 class="modal-title" id="ingSkipped"></h5>
        </div>
        <div class="modal-body">
        <div id="errMsg"></div>
              
          <div class="form-group">
            <label for="notes">Notes</label>
            <textarea class="form-control" id="skip_notes" rows="3"></textarea>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <input type="submit" name="skippedFromFormula" class="btn btn-primary" id="skippedFromFormula" value="Skip">
          </div>
         
        </div>
      </div>
    </div>
  </div>
  
  </body>
</html>
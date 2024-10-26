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

$qS = mysqli_query($conn, "SELECT id,name FROM ingSuppliers ORDER BY name ASC");
while($res = mysqli_fetch_array($qS)){
    $res_ingSupplier[] = $res;
}

?>
<!doctype html>
	<html lang="en" data-bs-theme="<?=$settings['bs_theme']?>">
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
      <link href="/css/makeFormula.css" rel="stylesheet">
  	  <link href="/css/magnific-popup.css" rel="stylesheet">

      <script src="/js/tableHTMLExport.js"></script>
      <script src="/js/jspdf.min.js"></script>
      <script src="/js/jspdf.plugin.autotable.js"></script>
      <script src="/js/bootbox.min.js"></script>
      <script src="/js/select2.js"></script>
      <script src="/js/magnific-popup.js"></script>

   
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
               <li><a class="dropdown-item" href="#" id="print"><i class="fa-solid fa-print mx-2"></i>Print formula</a></li>
            </div>
        </div>
            <table class="table table-striped" id="tdDataPending" width="100%" cellspacing="0">
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
var fid = "<?=$fid?>";
var repName;
var repID;

$(document).ready(function() {
	
	$('#updateStock').click(function(){
		if($(this).is(':checked')){
			$('#supplier').prop('disabled', false);
		}else{
			$('#supplier').prop('disabled', true);
		}
	});
	
	
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
			search: '',
			searchPlaceholder: 'Search by ingredient...',
		},
		ajax: {	
			url: '/core/pending_formulas_data.php?meta=0&fid=' + fid,
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
				if (data.toAdd == 0 || data.toSkip == 1) {
					$(row).addClass('d-none');
				}
				$('#toggleAdded').click(function() {
					if (data.toAdd == 0 || data.toSkip == 1) {
						$(row).toggleClass('d-none');
					}
				});
			},
			drawCallback: function( settings ) {
				extrasShow();
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
		data ='<div class="listIngNameCas-with-separator"><a href="#" class="dropdown-toggle " data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+row.ingredient+'</a><span class="listIngHeaderSub"> CAS: <i class="subHeaderCAS">'+row.cas+'</i></span><div class="dropdown-menu dropdown-menu-right">';
		data+='<li><a class="dropdown-item " href="#infoModal" id="ingInfo" data-bs-toggle="modal" data-id="'+row.ingID+'" data-name="'+row.ingredient+'" ><i class="fa-solid fa-circle-info mx-2"></i>Quick Info</a></li>';
		data+='<li><a class="dropdown-item popup-link" href="/pages/mgmIngredient.php?id='+row.ingID+'" target="_blank"><i class="fa-solid fa-eye mx-2"></i>Go to ingredient</a></li>';
		data+='</div></div>';
		return data;
	};
	
	function quantity(data, type, row){
		var overdose = '';
		if(row.overdose != 0 ){
			var overdose = '<span class="ing_alg"> <i rel="tip" title="Overdosed, added '+row.overdose+', instead" class="fas fa-exclamation-triangle"></i></span>';
		}
		
		data = row.quantity + overdose;
		return data;
	};
	
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
	};
	
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
	};
	
	function reload_data() {
		$('#tdDataPending').DataTable().ajax.reload(null, true);
	};
	
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
		   + '<input name="resetStock" id="resetStock" class="mx-2" type="checkbox" value="1" checked>'
		   + '<label for="resetStock">Reset stock</label>'
		   + '<div class="mt-2 form-row col-auto">'
           +     '<label for="supplier_res">Supplier</label>'
           +     '<select name="supplier_res" id="supplier_res" class="form-control selectpicker" data-live-search="true">'
           +     '<?php foreach ($res_ingSupplier as $rs) { ?>'
           +        '<option value="<?=$rs['id']?>"><?=$rs['name'];?></option>'
           +     '<?php } ?>'
           +    '</select>'
           +   '</div>',
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
						supplier: $("#supplier_res").val(),
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
		$('#resetStock').click(function(){
			if($(this).is(':checked')){
				$('#supplier_res').prop('disabled', false);
			}else{
				$('#supplier_res').prop('disabled', true);
			}
		});
	});

	
});//DOC END


 
</script>
<script src="/js/validate-session.js"></script>
<script src="/js/mark/jquery.mark.min.js"></script>
<script src="/js/mark/datatables.mark.js"></script>
<script src="/js/makeFormula.js"></script>

<!-- Modal ING Info -->
<div class="modal fade" id="infoModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="infoModalLabel"><div id="infoModalTitle">Ingredienet name</div></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="infoModalBody"></div>
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
<div class="modal fade" id="confirm_add" data-bs-backdrop="static" tabindex="-1" aria-labelledby="confirm_add" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ingAdded"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div style="display: none;" id="ingID"></div>
      <div style="display: none;" id="idRow"></div>
      <div style="display: none;" id="qr"></div>
      <div class="modal-body">
        <div id="errMsg"></div>
        
        <div class="mb-3">
          <label for="amountAdded" class="form-label">Amount added</label>
          <input name="amountAdded" type="text" id="amountAdded" class="form-control" />
        </div>

        <div class="dropdown-divider"></div>

        <div class="mb-3 form-check">
          <input name="updateStock" id="updateStock" type="checkbox" class="form-check-input" value="1" checked>
          <label class="form-check-label" for="updateStock">Update stock</label>
        </div>
        
        <div class="mb-3">
          <label for="supplier" class="form-label">Supplier</label>
          <select name="supplier" id="supplier" class="form-select"></select>
        </div>

        <hr class="border border-default border-1 opacity-75">
        
        <div class="mb-3">
          <label for="notes" class="form-label">Notes</label>
          <textarea class="form-control" id="notes" rows="3"></textarea>
        </div>

        <hr class="border border-default border-1 opacity-75">
        
        <a class="link-primary" data-bs-toggle="collapse" href="#collapseAdvanced" aria-expanded="false" aria-controls="collapseAdvanced">Advanced</a>
        <div class="collapse" id="collapseAdvanced">
          <div class="card card-body mt-3">
            <label for="replacement" class="form-label">Select a replacement</label>
            <select name="replacement" id="replacement" class="form-select"></select>
          </div>
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

  
<!-- Modal Skip material-->
<div class="modal fade" id="confirm_skip" data-bs-backdrop="static" tabindex="-1" aria-labelledby="confirm_skip" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ingSkipped"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div style="display: none;" id="ingID"></div>
      <div style="display: none;" id="idRow"></div>

      <div class="modal-body">
        <div id="errSkip"></div>
        
        <div class="form-group">
          <label for="skip_notes" class="form-label">Notes</label>
          <textarea class="form-control" id="skip_notes" rows="3"></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <input type="submit" name="skippedFromFormula" class="btn btn-primary" id="skippedFromFormula" value="Skip">
      </div>
    </div>
  </div>
</div>

  
  </body>
</html>
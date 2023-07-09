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
require_once(__ROOT__.'/inc/config.php');
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
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="<?php echo trim($product).' - '.trim($ver);?>">
  <meta name="author" content="<?php echo trim($product).' - '.trim($ver);?>">
  <title><?php echo $meta['name'];?></title>
  
  <link href="/css/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="/css/sb-admin-2.css" rel="stylesheet">
  <script src="/js/jquery/jquery.min.js"></script>
  <script src="/js/bootstrap.min.js"></script>
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <script src="/js/jquery-ui.js"></script>
  <link href="/css/jquery-ui.css" rel="stylesheet">
  <script src="/js/datatables.min.js"></script>
  <link href="/css/datatables.min.css" rel="stylesheet"/>
  <link href="/css/vault.css" rel="stylesheet">
  <script src="/js/tableHTMLExport.js"></script>
  <script src="/js/jspdf.min.js"></script>
  <script src="/js/jspdf.plugin.autotable.js"></script>
  <script src="/js/bootbox.min.js"></script>

  <style>
  	table.dataTable {
  		font-size: x-large !important;
		font-weight: bold;
		color: #494b51;
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
	
	table.dataTable thead tr, tfoot tr {
		background-color: #337ab7c9;
		color: white;
	}
  </style>
</head>


<div id="content-wrapper" class="d-flex flex-column">
    <div class="container-fluid">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h2 class="m-0 font-weight-bold text-primary"><a href="javascript:reload_data()" id="title"><?php echo $meta['name']; ?></a></h2>
        </div>
        <div class="card-body">
          <div class="table-responsive">
          <div id="errors"></div>
          <div id="msg"><?=$msg?></div>
          <div class="btn-group" id="menu">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mr2"></i>Actions</button>
            <div class="dropdown-menu dropdown-menu-left">
               <li><a class="dropdown-item" href="#" id="markCompleteMenu"><i class="fa-solid fa-check mr2"></i>Mark formula as complete</a></li>
               <div class="dropdown-divider"></div>
               <li class="dropdown-header">Export</li>
               <li><a class="dropdown-item" href="javascript:export_as('csv')"><i class="fa-solid fa-file-csv mr2"></i>Export as CSV</a></li>
               <li><a class="dropdown-item" href="javascript:export_as('pdf')"><i class="fa-solid fa-file-code mr2"></i>Export as PDF</a></li>
               <li><a class="dropdown-item" href="#" id="print"><i class="fa-solid fa-print mr2"></i>Print Formula</a></li>
            </div>
        </div>
        <p></p>
            <table class="table table-bordered" id="tdDataPending" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Ingredient</th>
                  <th>Purity</th>
                  <th>Quantity</th>
                  <th>Availability</th>
                  <th>Actions</th>
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
    </div>
</div>

<script>
var myFNAME = "<?=$meta['name']?>";
$(document).ready(function() {
	
	var tdDataPending = $('#tdDataPending').DataTable( {
	columnDefs: [
		{ className: 'pv_vertical_middle text-center', targets: '_all' },
		{ orderable: false, targets: [3,4] },
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
		 }
      },
	  fnRowCallback : function (row, data, display) {
		  if (data.toAdd == 0) {
			  $(row).find('td:eq(0),td:eq(1),td:eq(2),td:eq(3)').addClass('strikeout');
		  }
		  $(row).addClass('pv-zoom');
	  },
	 order: [[ 0, 'asc' ]],
	 lengthMenu: [[200, 500, 1000], [200, 500, 1000]],
	 pageLength: 200,
	 displayLength: 200,
	});
	
	$('#tdDataPending').on('mouseenter', '.pv-zoom', function() {
		$(this).addClass('pv-transition')
	});
	
	$('#tdDataPending').on('mouseleave', '.pv-zoom', function() {
		$(this).removeClass('pv-transition')
	});
	
	
	
});


function ingredient(data, type, row){

	data = '<a href="#infoModal" id="ingInfo" data-backdrop="static" data-toggle="modal" data-id="'+row.ingID+'" data-name="'+row.ingredient+'" class="listIngNameCas-with-separator">' + row.ingredient + '</a><span class="listIngHeaderSub"> CAS: <i class="subHeaderCAS">'+row.cas+'</i></span>';
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
		data = '<i id="undo_add" data-row-id="'+row.id+'" data-ingredient="'+row.ingredient+'" data-originalQuantity="'+row.originalQuantity+'" data-ingID = '+row.ingID+' class="mr fas fa-undo pv_point_gen" title="Reset original quantity for '+row.ingredient+'"></i>';
	//}
	
	if (row.toAdd == 1) {
		data += '<i data-toggle="modal" data-backdrop="static" data-target="#confirm_add" data-quantity="'+row.quantity+'" data-ingredient="'+row.ingredient+'" data-row-id="'+row.id+'" data-ing-id="'+row.ingID+'" data-qr="'+row.quantity+'" class="mr fas fa-check pv_point_gen" title="Confirm add '+row.ingredient+'"></i>';
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

$('#tdDataPending').on('click', '[data-target*=confirm_add]', function () {
	$('#errMsg').html('');																
	$("#ingAdded").text($(this).attr('data-ingredient'));
	$("#ingID").text($(this).attr('data-ing-id'));
	$("#idRow").text($(this).attr('data-row-id'));
	$("#amountAdded").val($(this).attr('data-quantity'));
	$("#qr").text($(this).attr('data-qr'));
});

 
//UPDATE AMOUNT
function addedToFormula() {
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

		ingId: $("#ingID").text(),
		fid: "<?php echo $fid; ?>",
		},
	dataType: 'json',
    success: function (data) {
		if(data.success) {
			var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
			$('#msg').html(msg);
			$('#confirm_add').modal('toggle');
			reload_data();
		
		} else if(data.error) {
			var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			$('#errMsg').html(msg);
		}
		
    }
  });
};


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
			var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
			reload_data();
		} else {
			var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
		}
		$('#msg').html(msg);
    }
  });
});

function export_as(type) {
  $("#tdDataPending").tableHTMLExport({
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
 
$('#tdDataPending').on('click', '[id*=undo_add]', function () {
	var d = {};
	d.ID = $(this).attr('data-row-id');
    d.ingName = $(this).attr('data-ingredient');
	d.ingID = $(this).attr('data-ingID');
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
					originalQuantity: d.originalQuantity,
					resetStock: $("#resetStock").is(':checked'),
					ID: d.ID
				},
				dataType: 'json',
				success: function (data) {
					if(data.success) {
						var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
						reload_data();
					} else {
						var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
					}
					$('#msg').html(msg);
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
					fid: "<?php echo $fid; ?>"
				},
				dataType: 'json',
				success: function (data) {
					if(data.success) {
						var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
						reload_data();
					} else {
						var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
					}	
					$('#msg').html(msg);
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


</script>
<script src="/js/validate-session.js"></script>
<script src="/js/mark/jquery.mark.min.js"></script>
<script src="/js/mark/datatables.mark.js"></script>

<!-- Modal ING Info -->
<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            	<div class="modal-body-info">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirm amount-->
<div class="modal fade" id="confirm_add" tabindex="-1" role="dialog" aria-labelledby="confirm_add" aria-hidden="true">
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
     	<form action="javascript:addedToFormula()" method="GET" target="_self">
        
          <div class="form-row">
            <div class="form-group col-md-6">
                <label for="amountAdded"> Amount added</label>
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
          <div class="dropdown-divider"></div>
          
          <div class="form-group">
		  	<label for="notes">Notes</label>
    		<textarea class="form-control" id="notes" rows="3"></textarea>
  		  </div>

          <div class="modal-footer">
       		<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
       		<input type="submit" name="button" class="btn btn-primary" id="button" value="Confirm">
     	  </div>
     
     	</form>
    </div>
  </div>
</div>

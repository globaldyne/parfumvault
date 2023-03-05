<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
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

?><head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="<?php echo trim($product).' - '.trim($ver);?>">
  <meta name="author" content="JBPARFUM">
  <title>Making of <?php echo $meta['name'];?></title>
  
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

  	<style>
  	table.dataTable {
  		font-size: x-large !important;
		font-weight: bold;
		color: #494b51;
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


<div id="content-wrapper" class="d-flex flex-column">
    <div class="container-fluid">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h2 class="m-0 font-weight-bold text-primary"><a href="javascript:reload_data()"><?php echo $meta['name']; ?></a></h2>
        </div>
        <div class="card-body">
          <div class="table-responsive">
          <div id="msg"></div>
          
          <div class="btn-group" id="menu">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
            <div class="dropdown-menu dropdown-menu-left">
               <div class="dropdown-divider"></div>
               <li class="dropdown-header">Export</li> 
               <a class="dropdown-item" href="javascript:export_as('csv')">Export as CSV</a>
               <a class="dropdown-item" href="javascript:export_as('pdf')">Export as PDF</a>
               <a class="dropdown-item" href="#" id="print">Print Formula</a>
               
            </div>
        </div>
            <table class="table table-bordered table-print" id="tdDataPending" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Ingredient</th>
                  <th>CAS</th>
                  <th>Purity</th>
                  <th>Quantity</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                <th>Total ingredients:</th>
                <th></th>
                <th></th>
                <th>Quantity:</th>
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
		{ orderable: false, targets: [1,4] },
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
		zeroRecords: 'No pending formulas found',
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
            { data : 'ingredient', title: 'Ingredient' },
			{ data : 'cas', title: 'CAS' },
            { data : 'concentration', title: 'Purity %' },
            { data : 'quantity', title: 'Quantity (<?=$settings['mUnit']?>)' },
			{ data : null, title: 'Actions', className: 'text-center noexport', render: actions },
			],
	   footerCallback : function( tfoot, data, start, end, display ) {    
		  var response = this.api().ajax.json();
		  if(response){
			 var $td = $(tfoot).find('th');
			 $td.eq(0).html("Ingredients: " + response.meta['total_ingredients'] );
			 $td.eq(3).html("Total left: " + response.meta['total_quantity_left'] + ' of ' + response.meta['total_quantity'] + response.meta['quantity_unit'] );
		 }
      },
	  fnRowCallback : function (row, data, display) {
		  if (data.toAdd == 0) {
			  $(row).find('td:eq(0),td:eq(1),td:eq(2),td:eq(3)').addClass('strikeout');
		  } 
	  },
	 order: [[ 0, 'asc' ]],
	 lengthMenu: [[200, 500, 1000], [200, 500, 1000]],
	 pageLength: 200,
	 displayLength: 200,
	});
	
});


function actions(data, type, row){
	if (row.toAdd == 1) {
		data = '<a href="#" data-toggle="modal" data-target="#confirm_add" data-quantity="'+row.quantity+'" data-ingredient="'+row.ingredient+'" data-row-id="'+row.id+'" data-ing-id="'+row.ingID+'" data-qr="'+row.quantity+'" class="fas fa-check" title="Add '+row.ingredient+'"></a> ';
	}else{
		data = '<a href="#" id="undo_add" data-row-id="'+row.id+'" data-ingredient="'+row.ingredient+'" class="fas fa-undo" title="Undo original quantity for '+row.ingredient+'"></a>';
	}
					  
	data += ' <a href="javascript:addToCart(\''+row.ingredient+'\',\''+row.quantity+'\',\''+row.concentration+'\',\''+row.ingID+'\')" class="fas fa-shopping-cart"></a>'; 
					 
	return data;    
}

function reload_data() {
    $('#tdDataPending').DataTable().ajax.reload(null, true);
}

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
    url: 'manageFormula.php', 
	type: 'POST',
    data: {
		action: "makeFormula",
		q: $("#amountAdded").val(),
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
		} else {
			var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			$('#errMsg').html(msg);
		}
		
    }
  });
};

function addToCart(material, quantity, purity, ingID) {
$.ajax({ 
    url: 'manageFormula.php', 
	type: 'POST',
    data: {
		action: "addToCart",
		material: material,
		purity: purity,
		quantity: quantity,
		ingID: ingID
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
};

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
	
	$.ajax({ 
		url: 'manageFormula.php', 
		type: 'POST',
		data: {
			action: "makeFormula",
			undo: 1,
			ing: d.ingName,
			ID: d.ID
			},
		dataType: 'json',
		success: function (data) {
			if(data.success) {
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				$('#msg').html(msg);
				reload_data();
			} else {
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				$('#errMsg').html(msg);
			}
			
		}
	  });
});
</script>

<script src="/js/mark/jquery.mark.min.js"></script>
<script src="/js/mark/datatables.mark.js"></script>

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
        Amount added:
     	<form action="javascript:addedToFormula()" method="GET" target="_self">
       		<input name="amountAdded" type="text" id="amountAdded" />
            <hr />
            <input name="updateStock" id="updateStock" type="checkbox" value="1" checked>
            Update stock
            <div class="modal-footer">
       			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
       			<input type="submit" name="button" class="btn btn-primary" id="button" value="Confirm">
     		</div>
     	</form>
    </div>
  </div>
</div>
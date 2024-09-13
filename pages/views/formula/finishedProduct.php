<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');


?>

<div id="content-wrapper" class="d-flex flex-column">
	<div class="container-fluid">
    	<div class="card shadow mb-4">
        	<div class="card-header py-3"> 
              <h2 class="m-0 font-weight-bold">Generate Finished Product</a>
            </div>
              <div class="mt-4 mr-4 text-right">
              	<div class="btn-group">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                  <div class="dropdown-menu dropdown-menu-left">
           			<li><a href="#" class="dropdown-item" id="export_pdf"><i class="fa-solid fa-file-export mx-2"></i>Export to PDF</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#IFRA"><i class="fa-solid fa-certificate mx-2"></i>IFRA Document</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#ViewBoxLabel"><i class="fa-solid fa-font mx-2"></i>View Box Back Label</a></li>
                  </div>
                </div>
            </div>
            <div class="card-body">
           		<div id="compliance"></div>
                <table id="formula" class="table table-striped nowrap" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th>Ingredient</th>
                            <th>CAS</th>
                            <th>Quantity</th>
                            <th>Concentration %</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="5">&nbsp;</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th class="fw-medium text-info-emphasis text-right">Sub Total</th>
                            <th class="fw-medium text-info-emphasis text-center" id="sub-total-quantity"></th>
                            <th class="fw-medium text-info-emphasis text-center" id="sub-total-concentration"></th>
                            <th class="fw-medium text-info-emphasis text-center" id="sub-total-cost"></th>
                        </tr>
                        <tr>
                            <th></th>
                            <th class="fw-medium text-info-emphasis text-right">Carrier/Solvent</th>
                            <th class="fw-medium text-info-emphasis text-center" id="carrier-quantity"></th>
                            <th class="fw-medium text-info-emphasis text-center" id="carrier-concentration"></th>
                            <th class="fw-medium text-info-emphasis text-center" id="carrier-cost"></th>
                        </tr>
                        <tr>
                            <th></th>
                            <th class="fw-medium text-info-emphasis text-right">Bottle</th>
                            <th class="fw-medium text-info-emphasis text-center" id="bottle-quantity"></th>
                            <th class="fw-medium text-info-emphasis text-center">-</th>
                            <th class="fw-medium text-info-emphasis text-center" id="bottle-cost"></th>
                        </tr>
                        <tr>
                            <th></th>
                            <th class="fw-medium text-info-emphasis text-right">Lid</th>
                            <th class="fw-medium text-info-emphasis text-center" id="lid-style"></th>
                            <th class="fw-medium text-info-emphasis text-center">-</th>
                            <th class="fw-medium text-info-emphasis text-center" id="lid-cost"></th>
                        </tr>
                        <tr>
                            <th></th>
                            <th class="fw-medium text-info-emphasis text-right">Batch No</th>
                            <th class="fw-medium text-info-emphasis text-center" id="batch-no"></th>
                            <th class="fw-medium text-info-emphasis text-center">-</th>
                            <th class="fw-medium text-info-emphasis text-center">-</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th class="fw-bold text-info-emphasis text-right">Total</th>
                            <th class="fw-bold text-info-emphasis text-center" id="total-quantity"></th>
                            <th class="fw-bold text-info-emphasis text-center" id="total-concentration"></th>
                            <th class="fw-bold text-info-emphasis text-center" id="total-cost"></th>
                        </tr>
                   </tfoot>
                </table>

          		<div class="mt-4 mb-4 dropdown-divider"></div>
           		<p>*Values in: <strong class="alert alert-danger mx-2">red</strong> exceeds usage level, <strong class="alert bg-banned mx-2">dark red</strong> banned/prohibited, <strong class="alert alert-warning mx-2">yellow</strong> Specification,<strong class="alert alert-success mx-2">green</strong> are within usage level,<strong class="alert alert-info mx-2">blue</strong> are exceeding recommended usage level</p>
                
			</div>
		</div>
	</div>
</div>

<!--VIEW BOX LABEL MODAL-->            
<div class="modal fade" id="ViewBoxLabel" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ViewBoxLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mgmIngHeader mgmIngHeader-with-separator" id="headerLabel">View Label</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body modal-Label-body-data">
        <div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i>Loading...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      	<button type="button" class="btn btn-primary" id="printLabel" data-print-target=".modal-Label-body-data">Print<i class="fa-solid fa-arrow-up-right-from-square ml-2"></i></button>
	  </div>
    </div>
  </div>
</div>


<!-- Modal IFRA DOC-->
<div class="modal fade" id="IFRA" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="IFRA" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Generate IFRA Document</h5>
      </div>
      <div class="modal-body">
      <div class="alert alert-info"><i class="fa-solid fa-circle-exclamation mr-2"></i>IMPORTANT: The generated document isn't an official IFRA certificate and needs to be reviewed by a certified person. Also, data needs to be properly verified to make sure there are no errors.</div>

	  <div class="mb-3">
  		<label for="customer" class="form-label">Select customer</label>
        <select class="form-control" name="customer" id="customer">
            <?php
				$res = mysqli_query($conn, "SELECT id, name FROM customers ORDER BY name ASC");
				while ($q = mysqli_fetch_array($res)){
				echo '<option value="'.$q['id'].'">'.$q['name'].'</option>';
			}
			?>
        </select>
       </div>
	   <div class="mb-3">
  			<label for="template" class="form-label">Select IFRA document template</label>
            <select class="form-control" name="template" id="template">
            <?php
				$res = mysqli_query($conn, "SELECT id, name FROM templates ORDER BY name ASC");
				while ($q = mysqli_fetch_array($res)){
				echo '<option value="'.$q['id'].'">'.$q['name'].'</option>';
			}
			?>
            </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="generateDoc">Generate</button>
      </div>
    </div>
  </div>
</div>
           
      
<!-- Modal IFRA Doc -->
<div class="modal fade" id="ifraDocModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ifraDocModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-fullscreen" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ifraDocModalLabel">IFRA Document</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body modal-IFRA-body-data">
        Loading...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="printIFRADoc" data-print-target=".modal-IFRA-body-data">
          Print<i class="fa-solid fa-arrow-up-right-from-square ms-2"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
var id = "<?=$_POST['fid']?>";
var bottle_id = "<?=$_POST['bottle_id']?>";
var carrier_id = "<?=$_POST['carrier_id']?>";
var lid_id = "<?=$_POST['lid_id']?>";
var concentration = "<?=$_POST['concentration']?>";
var defCatClass = "<?=$_POST['defCatClass']?>";
var supplier_id = "<?=$_POST['supplier_id']?>";
var batch_id = "<?=$_POST['batch_id']?>";
var formula_name;
var product_name;
var carrier_concentration ;
var batchNo;
var fid;

$(document).ready(function() {
						   
  var formula_table = $('#formula').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: '_all' },
        ],
		processing: true,
		responsive: false,
		searching: false,
		paging: false,
        language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Blending...</span>',
			emptyTable: '<div class="alert alert-warning"><strong>Empty formula</strong></div>',
		},
		//dom: 'B',
		buttons: [
			{
				extend: 'pdfHtml5',
				customize: function (doc) {
					doc.pageMargins = [50,20,50,20];
					doc.content[1].table.widths = ['*', '*', '*', '*', '*' ];
					
					doc.styles.tableHeader.fontSize = 9;
					doc.styles.tableFooter.fontSize = 9;
					var footerData = [];
					$('#formula tfoot tr').each(function () {
						var rowData = [];
						$(this).find('th').each(function (index) {
							var cellData = $(this).text() || ' ';
							rowData.push({ text: cellData, style: 'tableFooter' });
						});
						while (rowData.length < 5) {
							rowData.push({ text: ' ', style: 'tableFooter' });
						}
						footerData.push(rowData);
					});
	
					footerData.forEach(function (row) {
						doc.content[1].table.body.push(row.map(function (cell) {
							return { text: cell, style: 'tableFooter' };
						}));
					});
	
					doc.styles.tableFooter = {
						bold: true,
						fontSize: 9,
						alignment: 'center',
						//color: 'blue',
						margin: [0, 0, 0, 2],
						border: [false, false, false, true],
						borderColor: '#000000',
						borderWidth: 1
					};
				}
			}
        ],
    	ajax: {
    		url: '/core/finished_formula_data.php',
			type: 'POST',
			data:{
		   		id: id,
				bottle_id: $("#bottle_id").val(),
				carrier_id: $("#carrier_id").val(),
				lid_id: $("#lid_id").val(),
				concentration: $("#concentration").val(),
				defCatClass: $("#defCatClass").val(),
				supplier_id: $("#supplier_id").val(),
				batch_id: $("#batch_id").val()
			 }
 		 },
		 columns: [
			   { data : 'ingredient.name', title: 'Ingredient'},
			   { data : 'ingredient.cas', title: 'CAS #'},
			   { data : 'quantity', title: 'Quantity'},
			   { data : 'final_concentration', title: 'Concentration'},
			   { data : 'cost', title: 'Cost'}				   
		],
  		footerCallback : function( tfoot, data, start, end, display ) {    
      
			  var response = this.api().ajax.json();
			  if(response){
				formula_name = response.meta['formula_name'];
				product_name = response.meta['product_name'];
				carrier_concentration = response.meta['carrier_concentration'];
				batchNo = response.meta['batchNo'];
				fid = response.meta['fid'];
			
				$('#sub-total-quantity').text(response.meta['sub_total_quantity'] + response.meta['quantity_unit']);
				$('#carrier-quantity').text(response.meta['carrier_quantity'] + response.meta['quantity_unit']);
				$('#bottle-quantity').text(response.meta['bottle_quantity'] + response.meta['quantity_unit']);
				$('#lid-cost').html(response.meta['currency'] + response.meta['lid_cost']);
				$('#lid-style').text(response.meta['lid_style']);
				
				if(response.meta['batchNo']){
					$('#batch-no').html('<a href="/pages/viewDoc.php?type=batch&id=' + response.meta['batchNo'] + '" target="_blank">' + response.meta['batchNo'] + '</a>');
				} else {
					$('#batch-no').text('Not generated');
				}
				
				$('#total-quantity').text(response.meta['total_quantity'] + response.meta['quantity_unit']);
				$('#sub-total-concentration').text(response.meta['sub_concentration'] + '%');
				$('#carrier-concentration').text(response.meta['carrier_concentration'] + '%');
				$('#carrier-cost').html(response.meta['currency'] + response.meta['carrier_cost']);
				$('#sub-total-cost').html(response.meta['currency'] + response.meta['sub_cost']);
				$('#bottle-cost').html(response.meta['currency'] + response.meta['bottle_cost']);
				$('#total-cost').html(response.meta['currency'] + response.meta['total_cost']);
		
				createAlertBox(response);
		
			 }
      },
      
      createdRow: function(row, data, dataIndex) {
		  const setAlertClassAndIcon = (selector, alertClass, title) => {
			$(row).find(selector).addClass(alertClass).append(`<i rel="tip" title="${title}" class="mx-2 pv_point_gen fas fa-info-circle"></i>`);
		  };
		
		  const checkUsage = (selector, regulator, limit, concentration, restriction) => {
			if (regulator === "IFRA" && parseFloat(limit) < parseFloat(concentration)) {
			  setAlertClassAndIcon(selector, 'alert-danger', `Max usage: ${limit}% IFRA Regulated`);
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
		  checkUsage('td:eq(3)', data['usage_regulator'], data['usage_limit'], data['concentration'], data['usage_restriction']);
		
		  // Check ingredient classification
		  if (data.ingredient.classification == 4 || data['usage_restriction_type'] == 'PROHIBITION') {
			$(row).find('td').not('td:eq(5)').addClass('bg-banned text-light').append('<i rel="tip" title="This material is prohibited" class="mx-2 pv_point_gen fas fa-ban"></i>');
		  }
		
		  // Check final usage
		  checkUsage('td:eq(3)', data['usage_regulator'], data['usage_limit'], data['final_concentration'], data['usage_restriction']);
		},

	  	drawCallback: function ( settings ) {
			extrasShow();
	   }
});

	
	function extrasShow() {
		$('[rel=tip]').tooltip({
			 html: true,
			 boundary: "window",
			 overflow: "auto",
			 container: "body",
			 delay: {"show": 100, "hide": 0},
		 });
	};

	function createAlertBox(response) {
	  var alertBox = '<div class="alert alert-' + response.compliance.slug + '">' +
					 '<i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + 
					 response.compliance.message + '</strong>';
	
	  if (response.compliance.inval_materials.data && response.compliance.inval_materials.data.length > 0) {
		alertBox += '<br><a data-bs-toggle="collapse" href="#invalidMaterials" role="button" aria-expanded="false" aria-controls="invalidMaterials">' +
					'Show details</a>' +
					'<div class="collapse" id="invalidMaterials">' +
					'<ul>';
	
		response.compliance.inval_materials.data.forEach(function(material) {
		  alertBox += '<li>' + material + '</li>';
		});
	
		alertBox += '</ul></div>';
	  }
	
	  alertBox += '</div>';
	
	  $('#compliance').html(alertBox);
	};


	$("#ViewBoxLabel").on("show.bs.modal", function(e) {
		
	  const action = "viewBoxLabel"; 
	
	  const url = "/pages/manageFormula.php?action="+ action + "&batchID=" + batchNo +"&fid=" + fid + "&carrier=" + carrier_concentration +"&download=text";
	
	  $.get(url)
		.then(data => {
		  $("#headerLabel", this).html(formula_name);
		  $(".modal-body", this).html(data);
		});
		
	});

	$('#generateDoc').click(function() {
		$('.modal-IFRA-body-data').html('loading');
		$("#generateDoc").prop("disabled", true);
 		$('#generateDoc').append('<span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span>');
		$("#template").prop("disabled", true);
		$("#customer").prop("disabled", true);
		$.ajax({
		   type: 'POST',
		   url: '/pages/views/IFRA/genIFRAdoc.php',
		   data:{
			   fid: fid,
			   conc: concentration,
			   bottle: bottle_id,
			   defCatClass: defCatClass,
			   template: $("#template").val(),
			   customer: $("#customer").val()
		   },
		   success: function(data) {
				$('.modal-IFRA-body-data').html(data);
				$('#ifraDocModal').modal('show'); 
				$("#generateDoc").prop("disabled", false);
 				$("#generateDoc span").remove();
				$("#template").prop("disabled", false);
				$("#customer").prop("disabled", false);
		   },
		   error:function(xhr, status, error){
				data = '<div class="alert alert-danger">Unable to generate data, ' + error + '</div>';
				$('.modal-IFRA-body-data').html(data);
				$('#ifraDocModal').modal('show'); 
				$("#generateDoc").prop("disabled", false);
 				$("#generateDoc span").remove();
				$("#template").prop("disabled", false);
				$("#customer").prop("disabled", false);
		   }
		})
	 });
		 
		 
	function printContent(contentClass) {
		var printContents = $(contentClass).html();
		var printWindow = window.open('', '_blank', 'width=800,height=600');
		
		printWindow.document.open();
		printWindow.document.write('<html><head><title>Print</title>');
		printWindow.document.write('</head><body>');
		printWindow.document.write(printContents);
		printWindow.document.write('</body></html>');
		printWindow.document.close();
	
		printWindow.focus();
		printWindow.print();
		printWindow.close();
	}
	
	$('#printLabel, #printIFRADoc').click(function() {
		var contentClass = $(this).data('print-target');
		printContent(contentClass);
	});

	$('#export_pdf').on('click',function(){
		$('#formula').DataTable().button(0).trigger();
	});

});//doc ready
</script>

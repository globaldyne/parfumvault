<?php 

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');

?>
<div class="card-body">
 	<div class="text-right">
      <div class="btn-group">
         <button type="button" class="btn btn-primary dropdown-toggle mb-3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
          <div class="dropdown-menu">
           <li><a class="dropdown-item" id="exportCSV" href="#"><i class="fa-solid fa-file-export mx-2"></i>Export to CSV</a></li>
       </div>
   	</div> 
	<table id="tdDataM" class="table table-striped" style="width:100%">
      <thead>
        <tr>
          <th>Formula</th>
          <th>Ingredient</th>
          <th>CAS#</th>
          <th>Quantity required</th>
          <th>Quantity in stock</th>
          <th>Buy</th>
        </tr>
      </thead>
    </table>
</div>
                
<script>

$(document).ready(function() {
		var tdDataM = $("#tdDataM").DataTable( {
		columnDefs: [
			{ className: "text-center", targets: "_all" },
			{ orderable: false, targets: [5]},
        ],
		dom: "lfrtip",
		buttons: [{
			extend: "csvHtml5",
			title: "Pending materials",
			exportOptions: {
				columns: [0, 1, 2, 3, 4]
			},
		}],
		processing: true,
		mark: true,
        language: {
			loadingRecords: "&nbsp;",
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
			emptyTable: "No pending materials",
			search: "Search:"
		},
    	ajax: {	url: "/core/list_pending_materials_data.php" },
		columns: [
			   { data : "formula", title: "Formula", render: formulaName, name: "formula" },
    		   { data : "ingredient", title: "Ingredient", render: ingredientName },
    		   { data : "cas", title: "CAS#", render: ingredientCAS },
			   { data : "quantity", title: "Quantity required"},
			   { data : "inventory.stock", title: "Quantity in stock", render: stock},
			   { data : null, title: "Supplier(s)", render: iSuppliers}
		],
		rowsGroup: [
      		'formula:name'
    	],
        order: [[ 0, "asc" ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
        pageLength: 20,
		displayLength: 20
	});
	
	function formulaName(data, type, row){
		return row.formula;
	};
	
	function ingredientName(data, type, row){
		return row.ingredient;
	};
	
	function ingredientCAS(data, type, row){
		return row.cas;
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
	
	function iSuppliers(data, type, row){
		if(row.supplier){
			data = '<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-store mx-2"></i><span class="badge badge-light">' + row.supplier.length + '</span></button><div class="dropdown-menu">';
			for (var key in row.supplier) {
				if (row.supplier.hasOwnProperty(key)) {
					data += '<li><a class="dropdown-item" target="_blank" href="' + row.supplier[key].link + '"><i class="fa fa-store mx-2"></i>' + row.supplier[key].name + '</a></li>';
				}
			}                
			data += "</div></div></td>";
		}else{
			data = "N/A";
		}
		return data;
	};

	$("#exportCSV").click(() => {
		$("#tdDataM").DataTable().button(0).trigger();
	});
	
});

</script>
<script src="/js/mark/jquery.mark.min.js"></script>
<script src="/js/mark/datatables.mark.js"></script>
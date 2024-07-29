<?php 

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

?>
<div class="card-body">
 	<div class="text-right">
      <div class="btn-group">
         <button type="button" class="btn btn-primary dropdown-toggle mb-3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
          <div class="dropdown-menu dropdown-menu-right">
           <li><a class="dropdown-item" id="exportCSV" href="#"><i class="fa-solid fa-file-export mx-2"></i>Export to CSV</a></li>
       </div>
   	</div> 
	<table id="tdDataM" class="table table-striped table-bordered nowrap" style="width:100%">
      <thead>
        <tr>
          <th>Formula</th>
          <th>Ingredient</th>
          <th>CAS#</th>
          <th>Quantity</th>
          <th>Buy</th>
        </tr>
      </thead>
    </table>
</div>
                
<script type="text/javascript">
$(document).ready(function() {
		var tdDataM = $("#tdDataM").DataTable( {
		columnDefs: [
			{ className: "text-center", targets: "_all" },
			{ orderable: false, targets: [4]},
        ],
		dom: "lfrtip",
		buttons: [{
			extend: "csvHtml5",
			title: "Pending materials",
			exportOptions: {
				columns: [0, 1, 2, 3]
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
			   { data : "formula", title: "Formula", render: formulaName },
    		   { data : "ingredient", title: "Ingredient", render: ingredientName },
    		   { data : "cas", title: "CAS#", render: ingredientCAS },
			   { data : "quantity", title: "Quantity"},
			   { data : null, title: "Supplier(s)", render: iSuppliers}
		],
        order: [[ 0, "asc" ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
        pageLength: 20,
		displayLength: 20,
			drawCallback: function( settings ) {
    	},
	});
	
	function formulaName(data, type, row){
		return row.formula;
	}
	
	function ingredientName(data, type, row){
		return row.ingredient;
	}
	
	function ingredientCAS(data, type, row){
		return row.cas;
	}
	
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
	}

	$("#exportCSV").click(() => {
		$("#tdDataM").DataTable().button(0).trigger();
	});
	
});

</script>
<script src="/js/mark/jquery.mark.min.js"></script>
<script src="/js/mark/datatables.mark.js"></script>
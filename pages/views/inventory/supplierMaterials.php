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

     
    <table id="tdDataSM" class="table table-striped" style="width:100%">
    	<thead>
        	<tr>
          		<th>Material</th>
          		<th>Buy</th>
        	</tr>
      	</thead>
    </table>

</div>
                
<script>

$(document).ready(function() {
	
	var tdDataSM = $("#tdDataSM").DataTable( {
		columnDefs: [
			{ className: "text-center", targets: "_all" },
			{ orderable: false, targets: [1]},
        ],
		dom: "lfrtip",
		buttons: [{
			extend: "csvHtml5",
			title: "Supplier materials",
			exportOptions: {
				columns: [0, 1]
			},
		}],
		processing: true,
		mark: true,
        language: {
			loadingRecords: "&nbsp;",
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
			zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No materials</strong></div></div>',
			search: "",
			searchPlaceholder: 'Search by formula, ingredient or CAS...',
		},
    	ajax: {	
			url: "/core/list_suppliers_materials_data.php",
			data: {
				supplier_id: '<?=(int)$_GET['id']?>'
			}
		},
		columns: [
			{ data : "material", title: "Material"},
			{ data : null, title: "Buy", render: buy_material}
		],
        order: [[ 0, "asc" ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
        pageLength: 20,
		displayLength: 20
	});
	


	function buy_material(data, type, row){
		if(row.supplier_link){
			data = '<a href="' + row.supplier_link + '" target="_blank"><i class="fa-solid fa-cart-shopping"></i></a>';
		}else{
			data = "N/A";
		}
		return data;
	};

	$("#exportOverallCSV").click(() => {
		$("#tdDataSM").DataTable().button(0).trigger();
	});
	

});

</script>
<script src="/js/mark/jquery.mark.min.js"></script>
<script src="/js/mark/datatables.mark.js"></script>
<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');


$fid = mysqli_real_escape_string($conn, $_GET["fid"]);

?>

<h3>Composition analysis</h3>
<hr>

<table id="tdAnalysis" class="table table-striped table-bordered" style="width:100%">
  <thead>
      <tr>
          <th>Main Ingredient</th>
          <th>Contains</th>
          <th>Percentage in formula</th>
          <th>Max Allowed</th>
      </tr>
   </thead>
</table>


<script>
$(document).ready(function() {

	var tdAnalysis = $('#tdAnalysis').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: '_all' }
		],
		dom: 'lfrtip',
		processing: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: 'No sub materials found',
			search: 'Search:'
		},
		ajax: {	
			url: '/core/list_formula_analysis_data.php',
			type: 'POST',
			data: {
				fid: '<?=$fid?>',
			},
		},
		columns: [
			  { data : 'main_ing', title: 'Main Ingredient', render: mainName, name: 'main_ing' },
			  { data : 'sub_ing', title: 'Contains', render: subIng },
			  { data : 'contained_percentage', title: 'Percentage in formula(%)' },
			  { data : 'max_allowed', title: 'Max allowed(%)' },

		],
		rowsGroup: [
      		'main_ing:name'
    	],
		drawCallback: function ( settings ) {
			extrasShow();
		},
		order: [[ 0, 'asc' ]],
		lengthMenu: [[50, 100, 200, -1], [50, 100, 200, "All"]],
		pageLength: 50,
		displayLength: 50
	});


	function mainName(data, type, row){
		return '<i class="ing_name pv_point_gen" data-name="ing_name" data-type="text" data-pk="'+row.id+'">'+row.main_ing+'</i>';    
	};
	
	function subIng(data, type, row){
		return '<a class="ing_rep_name" href="#" >' + row.sub_ing + '</a><i class="fas fa-info-circle pv_point_gen mx-2" rel="tip" title="CAS: ' + row.cas + '"></i>';
	};
	
	
	
});
</script>

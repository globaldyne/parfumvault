<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');

$fid = $_GET["fid"];

?>

<h3>Composition analysis</h3>
<hr>

<table id="tdAnalysis" class="table table-striped" style="width:100%">
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
		mark: true,
		columns: [
			  { data : 'main_ing', title: 'Main Ingredient', render: mainName, name: 'main_ing' },
			  { data : 'sub_ing', title: 'Contains', render: subIng },
			  { data : 'contained_percentage', title: 'Percentage in formula(%)' },
			  { data : 'max_allowed_val', title: 'Max allowed(%)', render: maxAllowedReason },

		],
		rowsGroup: [
      		'main_ing:name'
    	],
		drawCallback: function ( settings ) {
			extrasShow();
		},
		order: [[ 0, 'asc' ]],
		lengthMenu: [[150, 250, 350, -1], [150, 250, 350, "All"]],
		pageLength: 150,
		displayLength: 150
	});


	function mainName(data, type, row){
		return '<i class="ing_name pv_point_gen" data-name="ing_name" data-type="text" data-pk="'+row.id+'">'+row.main_ing+'</i>';    
	};
	
	function subIng(data, type, row){
		return '<a class="ing_rep_name" href="#" >' + row.sub_ing + '</a><i class="fas fa-info-circle pv_point_gen mx-2" rel="tip" title="CAS: ' + row.cas + '"></i>';
	};
	
	function maxAllowedReason(data, type, row){
		if(row.max_allowed_reason) {
			maxData = '<a class="ing_rep_name" href="#" >' + row.max_allowed_val + '</a><i class="fas fa-info-circle pv_point_gen mx-2" rel="tip" title="' + row.max_allowed_reason + '"></i>';
		} else {
			maxData = '<a class="ing_rep_name" href="#" >' + row.max_allowed_val + '</a>';
		}
		return maxData;
	};
	
});
</script>

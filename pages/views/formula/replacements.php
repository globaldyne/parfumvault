<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

$fid = mysqli_real_escape_string($conn, $_POST["fid"]);

?>

<h3>Possible replacements</h3>
<hr>

<table id="tdReplacements" class="table table-striped table-bordered" style="width:100%">
  <thead>
      <tr>
          <th>Main Ingredient</th>
          <th>Possible Replacement</th>
      </tr>
   </thead>
</table>


<script type="text/javascript" language="javascript" >
$(document).ready(function() {

$('[data-bs-toggle="tooltip"]').tooltip();
var tdReplacements = $('#tdReplacements').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
	],
	dom: 'lfrtip',
	processing: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
		emptyTable: 'No replacements found.',
		search: 'Search:'
		},
	ajax: {	
		url: '/core/list_ing_rep_data.php',
		type: 'POST',
		data: {
				fid: '<?=$fid?>',
				view: 'formula',
			},
		},
	columns: [
			  { data : 'ing_name', title: 'Main Ingredient', render: repName, name: 'main_ing' },
			  { data : 'ing_rep_name', title: 'Possible Replacement', render: repIng }
			 ],
	rowsGroup: [
      'main_ing:name'
    ],
	drawCallback: function ( settings ) {
			extrasShow();
	},
	order: [[ 0, 'asc' ]],
	lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
	pageLength: 20,
	displayLength: 20
	});
});

function repName(data, type, row){
	return '<i class="ing_name pv_point_gen" data-name="ing_name" data-type="text" data-pk="'+row.id+'">'+row.ing_name+'</i>';    
}

function repIng(data, type, row){
	return '<a class="popup-link ing_rep_name pv_point_gen" href="/pages/mgmIngredient.php?id=' + btoa(row.ing_rep_name) + '">' + row.ing_rep_name + '</a> <i class="fas fa-info-circle pv_point_gen" title="'+row.notes+'"></i>';
}


</script>

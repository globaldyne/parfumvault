<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

$fid = mysqli_real_escape_string($conn, $_POST["fid"]);

?>
<script src="/js/dataTables.rowsGroup.js"></script>

<h3>Possible replacements</h3>
<hr>

<table id="tdReplacements" class="table table-striped table-bordered" style="width:100%">
  <thead>
      <tr>

          <th>Ingredient</th>
          <th>Replacement</th>
      </tr>
   </thead>
</table>


<script type="text/javascript" language="javascript" >
$(document).ready(function() {

$('[data-toggle="tooltip"]').tooltip();
var tdReplacements = $('#tdReplacements').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
	],
	dom: 'lfrtip',
	processing: true,
	language: {
		loadingRecords: '&nbsp;',
		processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
		emptyTable: 'No replacements added yet.',
		search: 'Search:'
		},
	ajax: {	
		url: '/core/list_ing_rep_data.php',
		type: 'POST',
		data: {
				fid: 'tcd6fzdn42lmk6htijrl8vq9zvhru0fvik0l6el7',
				view: 'formula',
			},
		},
	columns: [
			  { data : 'ing_name', title: 'Ingredient', name: 'main_ing'},
			  { data : 'ing_rep_name', title: 'Replacement'}
			 ],
	rowsGroup: [
      'main_ing:name'
    ],
	order: [[ 0, 'asc' ]],
	lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
	pageLength: 20,
	displayLength: 20
	});
});

function repName(data, type, row){
	return '<i class="repName pv_point_gen" data-name="ing_rep_name" data-type="text" data-pk="'+row.id+'">'+row.ing_rep_name+'</i>';    
}

function repCAS(data, type, row){
	return '<i class="repCAS pv_point_gen" data-name="ing_rep_cas" data-type="text" data-pk="'+row.id+'">'+row.ing_rep_cas+'</i>';    
}


</script>

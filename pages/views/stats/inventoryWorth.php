<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');

?>
<h3>Inventory Worth</h3>
<hr>
<table id="tdInvWorth" class="table table-striped table-bordered" style="width:100%">
  <thead>
      <tr>
          <th>Type</th>
          <th>Worth</th>
      </tr>
   </thead>
</table>
<script type="text/javascript" language="javascript" >
$(document).ready(function() {
		
	var tdInvWorth = $('#tdInvWorth').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
	],
	searching: false,
	processing: true,
	paging: false,
    ordering: false,
    info: false,
	language: {
		loadingRecords: '&nbsp;',
		processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
		emptyTable: 'No data found.',
		},
	ajax: {	url: '/core/stats_inv_data.php' },
	columns: [
			  { data : 'type', title: 'Type', render: type },
			  { data : 'worth', title: 'Worth', render: worth},
			 ],
	});
});

function type(data, type, row){
	return "Ingredients";    
}


function worth(data, type, row){
	return row.currency + row.ingredients.total_worth;    
}


</script>


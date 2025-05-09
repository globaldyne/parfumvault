<?php
/**
 * File: advisory.php
 * Description: Advisory page for formula.
 * Author: John B.
 * License: MIT License
 * Copyright: 2025 John B.
 */

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');

?>
<div class="text-right">
	<div class="btn-group">
      <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
      <div class="dropdown-menu">                                	  
        <li><a class="dropdown-item" id="exportCSV" href="#"><i class="fa-solid fa-file-export mx-2"></i>Export as CSV</a></li>
        <li><a class="dropdown-item" id="exportPDF" href="#"><i class="fa-solid fa-file-pdf mx-2"></i>Export as PDF</a></li>
      </div>
  </div>
</div>
<h3>Composition advisor</h3>
<hr>

<table id="tdAdvisor" class="table table-striped" style="width:100%">
  <thead>
      <tr>
          <th>Ingredient</th>
          <th>Quantity</th>
          <th>Issue detected</th>
          <th>Recommendation</th>
      </tr>
   </thead>
</table>


<script>
$(document).ready(function() {
	$.fn.dataTable.ext.errMode = 'none';

	var fid = '<?=$_GET["fid"]?>';
	var tdAdvisor = $('#tdAdvisor').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: '_all' }
		],
		dom: 'lfrtip',
	   	buttons: [
        	{
				extend: 'csvHtml5',
				title: "Formula Advisor"
			},
			{
            	extend: 'pdfHtml5',
            	title: "Formula Advisor"
        	}
    	],
		processing: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No issues found</strong></div></div>',
			search: '',
			searchPlaceholder: 'Search by ingredient...',
		},
		ajax: {	
			url: '/core/list_formula_advisor_data.php',
			type: 'POST',
			data: {
				fid: fid,
			},
		},
		mark: true,
		columns: [
			  { data : 'ingredient', title: 'Ingredient' },
			  { data : 'quantity', title: 'Quantity' },
			  { data : 'advisory', title: 'Issue detected' },
			  { data : 'recommendation', title: 'Recommendation' },
		],
        
		drawCallback: function ( settings ) {
			extrasShow();
		},
		stateSave: false,
		order: [[ 0, 'asc' ]],
		lengthMenu: [[150, 250, 350, -1], [150, 250, 350, "All"]],
		pageLength: 150,
		displayLength: 150
	}).on('error.dt', function(e, settings, techNote, message) {
		var m = message.split(' - ');
		$('#tdAdvisor').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
	});


    // Export buttons
	$("#exportCSV").click(() => {
		$("#tdAdvisor").DataTable().button(0).trigger();
	});
	
	$("#exportPDF").click(() => {
		$("#tdAdvisor").DataTable().button(1).trigger();
	});
	
	function extrasShow() {
		$('[rel=tip]').tooltip({
			 html: true,
			 boundary: "window",
			 overflow: "auto",
			 container: "body",
			 delay: {"show": 100, "hide": 0},
		 });
	}
});
</script>

<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

$fid = mysqli_real_escape_string($conn, $_POST["fid"]);

?>

<h3>Possible replacements</h3>
<hr>

<table id="tdReplacements" class="table table-striped" style="width:100%">
  <thead>
      <tr>
          <th>Main Ingredient</th>
          <th>Possible Replacement</th>
      </tr>
   </thead>
</table>


<script>
$(document).ready(function() {
	$.fn.dataTable.ext.errMode = 'none';

	var tdReplacements = $('#tdReplacements').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
		],
		dom: 'lfrtip',
		processing: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No replacements found</strong></div></div>',
			search: '',
			searchPlaceholder: 'Search by ingredient...',
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
	}).on('error.dt', function(e, settings, techNote, message) {
		var m = message.split(' - ');
		$('#tdReplacements').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
	});


	function repName(data, type, row){
		if (row.original_id == 0 || row.original_id == '' || typeof row.original_id === 'undefined') {
			return row.ing_name;
		} else {
			return '<a class="popup-link" href="/pages/mgmIngredient.php?id=' + row.original_id + '">'+row.ing_name+'</a> <i class="fas fa-external-link-alt"></i>';
		}
	};

	function repIng(data, type, row){
		return '<a class="popup-link ing_rep_name pv_point_gen" href="/pages/mgmIngredient.php?id=' + row.replacement_id + '">' + row.ing_rep_name + '</a> <i class="fas fa-info-circle pv_point_gen" rel="tip" title="'+row.notes+'"></i>';
	};
	
	function extrasShow() {
		
		$('[rel=tip]').tooltip({
			"html": true,
			"delay": {"show": 100, "hide": 0},
		});

		$('.popup-link').magnificPopup({
			type: 'iframe',
			closeOnContentClick: false,
			closeOnBgClick: false,
			showCloseBtn: true,
		});
	};

});

</script>

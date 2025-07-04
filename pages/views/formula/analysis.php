<?php
/**
 * File: analysis.php
 * Description: Composition analysis page for formula.
 * Author: John B.
 * License: MIT License
 * Copyright: 2023 John B.
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
	$.fn.dataTable.ext.errMode = 'none';

	var fid = '<?=$_GET["fid"]?>';
	var tdAnalysis = $('#tdAnalysis').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: '_all' }
		],
		dom: 'lfrtip',
	   	buttons: [
        	{
				extend: 'csvHtml5',
				title: "Formula Analysis"
			},
			{
            	extend: 'pdfHtml5',
            	title: "Formula Analysis"
        	}
    	],
		processing: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
			emptyTable: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>No sub materials found</strong></div></div>',
			search: '',
			searchPlaceholder: 'Search by ingredient...',
		},
		ajax: {	
			url: '/core/list_formula_analysis_data.php',
			type: 'POST',
			data: {
				fid: fid,
			},
		},
		mark: true,
		columns: [
			  { data : 'main_ing', title: 'Main Ingredient', render: mainName, name: 'main_ing' },
			  { data : 'sub_ing', title: 'Contains', render: subIng },
			{ 
				data: 'contained_percentage', 
				title: 'Percentage in formula(%) <i class="fas fa-info-circle pv_point_gen mx-1" rel="tip" title="This is the percentage of this compound in the overall formula, calculated as if the formula were at 100% concentration"></i>', 
				render: percInFormula 
			},
			  { data : 'max_allowed_val', title: 'Max allowed(%)', render: maxAllowedReason },
		],
		rowsGroup: [
      		'main_ing:name'
    	],
		drawCallback: function ( settings ) {
			extrasShow();
		},
		stateSave: true,
		stateDuration: -1,
		stateLoadCallback: function (settings, callback) {
			$.ajax( {
				url: "/core/update_user_settings.php?set=formulaAnalysis&action=load&tableId=" + fid,
				dataType: "json",
				success: function (json) {
					callback( json );
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			 url: "/core/update_user_settings.php?set=formulaAnalysis&action=save&tableId=" + fid,
			 data: data,
			 dataType: "json",
			 type: "POST"
		  });
		},
		order: [[ 0, 'asc' ]],
		lengthMenu: [[150, 250, 350, -1], [150, 250, 350, "All"]],
		pageLength: 150,
		displayLength: 150
	}).on('error.dt', function(e, settings, techNote, message) {
		var m = message.split(' - ');
		$('#tdAnalysis').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
	});


	function mainName(data, type, row){
		return '<a class="ing_name pv_point_gen popup-link" href="/pages/mgmIngredient.php?id=' + row.id + '">'+row.main_ing + '</a>';    
	};
	
	function subIng(data, type, row){
		// Add a unique id for the name and info icon
		let nameId = 'name-' + Math.random().toString(36).substr(2, 9);
		let infoId = 'info-' + Math.random().toString(36).substr(2, 9);
		let casTooltip = row.cas ? 'CAS: ' + row.cas : 'No CAS available';

		// Attach click event for modal on the name (delegated, so works after redraw)
		setTimeout(function() {
			$(document).off('click.' + nameId).on('click.' + nameId, '#' + nameId, function(e) {
				e.preventDefault();
				let $name = $(this);
				// Show a message that AI is being queried
				showIngredientModal(
					row.sub_ing,
					'<div class="d-flex align-items-center"><i class="fa fa-robot fa-spin me-2"></i> Querying AI for ingredient information...</div>'
				);
				$.post('/core/core.php', { action: 'aiChat', message: "Tell me about " + row.sub_ing + " ingredient. CAS, Molecular Weight, Molecular Structure and IFRA restrictions." }, (resp) => {
					let tip = 'No description available.';
					try {
						let parsed = JSON.parse(resp);
						let info = null;
						if (parsed && parsed.success) {
							// If array
							if (Array.isArray(parsed.success) && parsed.success[0]) {
								info = parsed.success[0];
							}
							// If object with numeric keys
							else if (typeof parsed.success === 'object' && parsed.success.description === undefined) {
								let firstKey = Object.keys(parsed.success).find(k => !isNaN(k));
								if (firstKey) info = parsed.success[firstKey];
							}
							// If flat object
							else if (typeof parsed.success === 'object' && parsed.success.description) {
								info = parsed.success;
							}
						}
						if (info) {
							tip = `
								<div>
									<strong>Description:</strong> ${info.description || '-'}<br>
									<strong>Physical State:</strong> ${info.physical_state || '-'}<br>
									<strong>Color:</strong> ${info.color || '-'}<br>
									<strong>Category:</strong> ${info.category || '-'}<br>
									<strong>CAS:</strong> ${info.cas || '-'}<br>
									<strong>Olfactory Type:</strong> ${info.olfactory_type || '-'}
								</div>
							`;
						} else if (typeof parsed.error !== 'undefined') {
							tip = `<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>${parsed.error}</strong></div>`;
						}
					} catch (e) {}
					showIngredientModal(row.sub_ing, tip);
				});
			});
		}, 0);

		// Info icon: shows CAS as tooltip on hover
		return '<a class="ing_rep_name" href="#" id="' + nameId + '">' + row.sub_ing + '</a>' +
			'<i id="' + infoId + '" class="fas fa-info-circle pv_point_gen mx-2" rel="tip" title="' + casTooltip + '" style="cursor:pointer"></i>';
	};
	
	function percInFormula(data, type, row){
		if(row.contained_percentage > row.max_allowed_val) {
			return '<span class="badge bg-danger">' + row.contained_percentage + '</span>';
		} else if(row.contained_percentage <= row.max_allowed_val) {
			return '<span class="badge bg-success">' + row.contained_percentage + '</span>';
		} else if(row.contained_percentage > 0) {
			return '<span class="badge bg-warning">' + row.contained_percentage + '</span>';
		} else {
			return '<span class="badge bg-secondary">0</span>';
		}
	};

	function maxAllowedReason(data, type, row){
		if(row.max_allowed_reason) {
			maxData = '<a class="ing_rep_name" href="#" >' + row.max_allowed_val + '</a><i class="fas fa-info-circle pv_point_gen mx-2" rel="tip" title="' + row.max_allowed_reason + '"></i>';
		} else {
			maxData = '<a class="ing_rep_name" href="#" >' + row.max_allowed_val + '</a>';
		}
		return maxData;
	};
	
	$("#exportCSV").click(() => {
		$("#tdAnalysis").DataTable().button(0).trigger();
	});
	
	$("#exportPDF").click(() => {
		$("#tdAnalysis").DataTable().button(1).trigger();
	});
	
	function extrasShow() {
		$('[rel=tip]').tooltip({
			 html: true,
			 boundary: "window",
			 overflow: "auto",
			 container: "body",
			 delay: {"show": 100, "hide": 0},
		 });

		$('.popup-link').magnificPopup({
			type: 'iframe',
			closeOnContentClick: false,
			closeOnBgClick: false,
			showCloseBtn: true,
		});
	}

	// Modal HTML and show function
	if (!document.getElementById('ingredientModal')) {
		$('body').append(`
			<div class="modal fade" id="ingredientModal" tabindex="-1" aria-labelledby="ingredientModalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-lg modal-dialog-centered">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="ingredientModalLabel">Ingredient Info</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				  </div>
				  <div class="modal-body" id="ingredientModalBody">
					Loading...
				  </div>
				</div>
			  </div>
			</div>
		`);
	}

	function showIngredientModal(title, body) {
		$('#ingredientModalLabel').text(title);
		$('#ingredientModalBody').html(body);
		let modalEl = document.getElementById('ingredientModal');
		let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
		modal.show();

		// Ensure modal is properly dismissed on close
		$('#ingredientModal .btn-close').off('click.dismiss').on('click.dismiss', function() {
			modal.hide();
		});
		// Also dismiss on backdrop click or ESC (Bootstrap handles this by default, but this is explicit)
		$(modalEl).off('hidden.bs.modal.dismiss').on('hidden.bs.modal.dismiss', function() {
			modal.dispose();
		});
	}
});
</script>

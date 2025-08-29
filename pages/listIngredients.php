<?php 

define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/loadModules.php');

$defCatClass = $settings['defCatClass'];
$cIngredients = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE owner_id = '$userID'"));

?>
<div class="col mb-4">
	<div class="text-right">
     <div class="btn-group">
     	<button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
      	<div class="dropdown-menu dropdown-menu-right">
       	<li><a class="dropdown-item popup-link" href="/pages/mgmIngredient.php"><i class="fa-solid fa-plus mx-2"></i>Create new ingredient</a></li>
        <div class="dropdown-divider"></div>
        <li><a class="dropdown-item" id="csv_export" href="/pages/export.php?format=csv&kind=ingredients"><i class="fa-solid fa-file-csv mx-2"></i>Export to CSV</a></li>
		<li><a class="dropdown-item" id="json_export" href="#" data-bs-toggle="modal" data-bs-target="#export_options_modal"><i class="fa-solid fa-file-code mx-2"></i>Export to JSON</a></li>
        <div class="dropdown-divider"></div>
        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#csv_import"><i class="fa-solid fa-file-import mx-2"></i>Import from CSV</a></li>
        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#import_ingredients_json"><i class="fa-solid fa-file-import mx-2"></i>Import from JSON</a></li>
		<?php if ($cIngredients) { ?>
			<div class="dropdown-divider"></div>
        	<li><a class="dropdown-item text-danger" href="#" id="wipe_all_ing"><i class="fa-solid fa-trash mx-2"></i>Delete all ingredients</a></li>
		<?php } ?>
      </div>
     </div>                    
    </div>
</div>

<div class="dropdown-divider"></div>

<div id="row pv_search">
	<div class="text-right">
        <div class="pv_input_grp">   
          <div class="btn-group input-group-btn">
          	<input name="ing_search" type="text" class="form-control input-sm pv_input_sm" id="ing_search" value="<?=$_GET['search']?>" placeholder="Ingredient name, CAS...">
            <button class="btn btn-search btn-primary col" id="pv_search_btn" data-provider="local">
            	<span class="label-icon">
                    <i class="fas fa-database mx-2"></i>
                    <a href="#" class="btn-search">Local DB</a>
                </span>
            </button>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
   				<span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <?php foreach (loadModules('providers') as $provider){ ?>
                <li>
                    <a href="#" class="supplier dropdown-item" data-provider="<?=$provider['fileName']?>">
                        <span class="<?=$provider['icon']?>"></span>
                        <span class="label-icon"><?=$provider['name']?></span>
                    </a>
                </li>
                <?php } ?>
            </ul>
          </div>
   	  </div>
	</div>
</div>

<table id="tdDataIng" class="table table-striped" style="width:100%">
  <thead>
      <tr>
          <th>Name</th>
          <th>IUPAC</th>
          <th>Description</th>
          <th>Profile</th>
          <th>Category</th>
          <th>Stock</th>
          <th><?=ucfirst($defCatClass)?></th>
          <th>Supplier(s)</th>
          <th>Document(s)</th>
          <th data-priority="1"></th>
      </tr>
   </thead>
</table>

<!-- EXPORT MODAL -->
<div class="modal fade" id="export_options_modal" tabindex="-1" aria-labelledby="exportOptionsLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exportOptionsLabel">Export Options</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="export_options_form">
					<div id="exportmsg"></div>
					<div class="mb-3">
						<label class="form-label">Include</label>
						<div class="form-check">
						<input class="form-check-input" type="checkbox" value="suppliers" id="include_suppliers" name="include_suppliers">
						<label class="form-check-label" for="include_suppliers">
						Suppliers
						</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" value="compositions" id="include_compositions" name="include_compositions">
						<label class="form-check-label" for="include_compositions">
						Compositions
						</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" value="documents" id="include_documents" name="include_documents">
						<label class="form-check-label" for="include_documents">
						Documents
						</label>
					</div>
					<div id="documents_warning" class="alert alert-warning mt-3" style="display: none;">
						<i class="fa-solid fa-triangle-exclamation mx-2"></i>Exporting documents may take a while and result in a large file size. This may require a large amount of server memory and will only export documents as the allocated memory allows.
					</div>
					</div>
				</form>
			</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary" id="export_confirm_btn">Export</button>
		</div>
	</div>
	</div>
</div>

<script>
$(document).ready(function() {
	$.fn.dataTable.ext.errMode = 'none';

	var tdDataIng = $('#tdDataIng').DataTable( {
		columnDefs: [
			{ className: 'pv_vertical_middle text-center', targets: '_all' },
			{ orderable: false, targets: [1,2,5,6,7,8,9]},
			{ responsivePriority: 1, targets: 0 }
		],
		search: {
			search: "<?=$_GET['search']?>"
		},
		dom: 'lr<"#advanced_search">tip',
		processing: true,
		serverSide: true,
		searching: true,
		responsive: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: 'Blending...',
			zeroRecords: '<div class="alert alert-warning mt-2"><i class="fa-solid fa-triangle-exclamation mx-2"></i><strong>Nothing found, try <a href="#" data-bs-toggle="modal" data-bs-target="#adv_search">advanced</a> search instead?</strong></div>',
			search: 'Quick Search:',
			searchPlaceholder: 'Name, CAS, EINECS, IUPAC...',
		},
		ajax: {	
			url: '/core/list_ingredients_data.php',
			type: 'POST',
			data: function(d) {
				d.pvSearch =  '<?=$_GET['search']?>'
				d.provider = $('#pv_search_btn').attr('data-provider')
				d.advanced = '<?=htmlspecialchars($_POST['advanced']?:0, ENT_QUOTES, 'UTF-8')?>'
				d.profile = '<?=htmlspecialchars($_POST['profile']?:null, ENT_QUOTES, 'UTF-8')?>'
				d.name = '<?=htmlspecialchars($_POST['name']?:null, ENT_QUOTES, 'UTF-8')?>'
				d.cas = '<?=htmlspecialchars($_POST['cas']?:null, ENT_QUOTES, 'UTF-8')?>'
				d.einecs = '<?=htmlspecialchars($_POST['einecs']?:null, ENT_QUOTES, 'UTF-8')?>'
				d.cat = '<?=htmlspecialchars($_POST['cat']?:null, ENT_QUOTES, 'UTF-8')?>'
				d.synonym = '<?=htmlspecialchars($_POST['synonym']?:null, ENT_QUOTES, 'UTF-8')?>'
				d.notes = '<?=htmlspecialchars($_POST['notes']?:null, ENT_QUOTES, 'UTF-8')?>'
				if (d.order.length>0){
					d.order_by = d.columns[d.order[0].column].data
					d.order_as = d.order[0].dir
				}
			},
			dataType: 'json',
		},
		columns: [
			  { data : 'name', title: 'Name', render: iName },
			  { data : 'IUPAC', title: 'IUPAC' },
			  { data : 'labels', title: 'Labels', render: labels },
			  { data : 'profile', title: 'Profile', render: iProfile },
			  { data : 'category', title: 'Category', render: iCategory },
			  { data : 'usage.limit', title: '<?=ucfirst($defCatClass)?>(%)', render: iLimit},
			  { data : 'stock', title: 'In Stock <i rel="tip" title="The total amount available in stock from all suppliers." class="fas fa-info-circle"></i></span>', render: iStock},
			  { data : null, title: 'Supplier(s)', render: iSuppliers},
			  { data : null, title: 'Document(s)', render: iDocs},
	
			  { data : null, title: '', render: actions},		   
		],
		order: [[ 0, 'asc' ]],
		lengthMenu: [[20, 50, 100, 200, 400], [20, 50, 100, 200, 400]],
		pageLength: 20,
		displayLength: 20,
		drawCallback: function( settings ) {
			extrasShow();
		},
		initComplete: function( settings, json ) {
		
						$("#advanced_search").html(`
				<span>
					<hr />
					<div id="filter" class="d-flex flex-wrap mb-2"></div>
					<a href="#" class="advanced_search_box" data-bs-toggle="modal" data-bs-target="#adv_search">
						<i class="fa-solid fa-magnifying-glass mx-2"></i>Advanced Search
					</a>
				</span>
			`);
		
			$("#tdDataIng_filter").detach().appendTo('#pv_search');
		
			var filters = {
				"Ingredient name": $('#ing_name').val(),
				"CAS#": $('#ing_cas').val(),
				"EINECS": $('#ing_einecs').val(),
				"Profile": $('#ing_profile').val(),
				"Category": $('#ing_category').find('option:selected').data('text'),
				"Synonym": $('#ing_synonym').val()
			};

			$.each(filters, function(key, value) {
				if (value) {
					$('#filter').append(`
						<span class="badge rounded-pill d-block p-2 mx-2 badge-primary">
							${key}: ${value}
							<span class="ms-1" data-fa-transform="shrink-2"></span>
							<button type="button" class="btn-close btn-close-white mt-2 mx-2" aria-label="Remove" data-field="${key}"></button>
						</span>
						</span>
					`);
				}
			});
	
			$('#filter').on('click', '.btn-close', function() {
				var field = $(this).data('field');
		
				switch (field) {
					case "Ingredient name":
						$('#ing_name').val('');
						break;
					case "CAS#":
						$('#ing_cas').val('');
						break;
					case "EINECS":
						$('#ing_einecs').val('');
						break;
					case "Profile":
						$('#ing_profile').val('').trigger('change');
						break;
						
					case "Category":
						$('#ing_category').val('').trigger('change');
						break;
						
					case "Synonym":
						$('#ing_synonym').val('');
						break;
				}
		
				tdDataIng.search('').draw();
				$('#btnAdvSearch').trigger('click');
				$(this).parent().remove();
			});
			
			$('#tdDataIng').on('click', '[id*=show-all-labels]', function () {
				const $button = $(this);
				const labels = $button.data('labels');
				const $row = $button.closest('tr'); // Find the closest table row
				const $labelContainer = $row.find('.expanded-labels'); // Check if expanded labels already exist

				if ($labelContainer.length) {
					// If already expanded, collapse by removing the labels and updating the button text
					$labelContainer.remove();
					$button.text(`+${labels.length - 4} more`);
				} else {
					// Expand by appending the remaining labels and updating the button text
					if (Array.isArray(labels)) {
						const html = labels
							.map(label => `<span class="badge bg-info text-dark mt-2 mx-1 my-2">${label}</span>`)
							.join('');
						$row.find('td').eq(2).append(`<div class="expanded-labels">${html}</div>`);
					}
					$button.text('Less');
				}
			});
		},
		stateSave: true,
		stateDuration : -1,
		stateLoadCallback: function (settings, callback) {
			$.ajax( {
				url: '/core/update_user_settings.php?set=listIngredients&action=load',
				dataType: 'json',
				success: function (json) {
					callback( json );
					if(json.search.search !== undefined){
						$('#ing_search').val(json.search.search);
					}
				}
			});
		},
		stateSaveCallback: function (settings, data) {
		   $.ajax({
			 url: "/core/update_user_settings.php?set=listIngredients&action=save",
			 data: data,
			 dataType: "json",
			 type: "POST"
		  });
		},

	}).on('error.dt', function(e, settings, techNote, message) {
		var m = message.split(' - ');
		$('#tdDataIng').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i><strong>' + m[1] + '</strong></div>');
	});
	    
	$('#ing_search').keyup(function() {
        tdDataIng.search($(this).val()).draw();
    });
	
	$("#pv_search_btn").click(function () {
		var ingSearch = {
        	txt: $('#ing_search').val()
    	};
    	if (tdDataIng) {
        	tdDataIng.search(ingSearch.txt).draw();
    	}
	});
	
 					   
	function iName(data, type, row, meta) {
		// Ensure data and row are valid
		if (!data || !row) return "";
		// Helper function to escape HTML
		const escapeHTML = (str) => {
			return String(str)
				.replace(/&/g, "&amp;")
				.replace(/</g, "&lt;")
				.replace(/>/g, "&gt;")
				.replace(/"/g, "&quot;")
				.replace(/'/g, "&#039;");
		};
	
		// Initialize variables for additional indicators
		let allergenIndicator = "";
		let errorIndicator = "";
	
		// Check if ingredient has allergen
		if (row.allergen == 1) {
			allergenIndicator =
				'<span class="ing_alg"><i rel="tip" title="Allergen" class="fas fa-exclamation-triangle"></i></span>';
		}
	
		// Check if ingredient has error
		if (row.error) {
			errorIndicator = `<span class="ing_alg"><i rel="tip" title="${escapeHTML(
				row.error
			)}" class="fas fa-xmark text-danger"></i></span>`;
		}
	
		// Determine source and construct the link and additional details
		const isLocal = meta?.settings?.json?.source === "local";
		const ingredientName = escapeHTML(row.name);
		const casNumber = escapeHTML(row.cas || "N/A");
		const einecsNumber = escapeHTML(row.einecs || "N/A");
	
		if (isLocal) {
			return `
				<a class="popup-link listIngName listIngName-with-separator" href="/pages/mgmIngredient.php?id=${row.id}">
					${ingredientName}
				</a>
				${allergenIndicator}
				${errorIndicator}
				<span class="listIngHeaderSub">
					CAS: <i class="pv_point_gen subHeaderCAS" rel="tip" title="Click to copy CAS" id="copyCAS" data-name="${casNumber}">${casNumber}</i> 
					| EINECS: <i class="pv_point_gen subHeaderCAS">${einecsNumber}</i>
				</span>`;
		} else {
			return `
				<a class="listIngName listIngName-with-separator" href="#">
					${ingredientName}
				</a>
				${allergenIndicator}
				<span class="listIngHeaderSub">
					CAS: <i class="pv_point_gen subHeaderCAS" rel="tip" title="Click to copy CAS" id="copyCAS" data-name="${casNumber}">${casNumber}</i> 
					| EINECS: <i class="pv_point_gen subHeaderCAS">${einecsNumber}</i>
				</span>`;
		}
	};
	
	function iProfile(data, type, row, meta){
		if(meta.settings.json.source == 'local'){
			if(row.profile){
				return '<img src="/img/Pyramid/Pyramid_Slice_'+row.profile+'.png" class="img_ing_prof"/>';    
			}else{
				return '<img src="/img/pv_molecule.png" class="img_ing_prof"/>';
			}
		}else{
			return '<i class="pv_point_gen" rel="tip" title="Not available in PV Library">N/A</i>';
		}
	};
	
	function iStock(data, type, row, meta){
		if (row.physical_state == 1) {
			var ingUnit = "ml";
		}else if (row.physical_state == 2) {
			var ingUnit = "gr";
		}
		if(meta.settings.json.source == 'local'){
			return '<a class="popup-link" rel="tip" title="'+ingUnit+'" href="/pages/views/ingredients/ingSuppliers.php?id=' + row.id + '&standAlone=1">' + data + '</a>';
		}else{
			return '<i class="pv_point_gen" rel="tip" title="Not available in PV Library">N/A</i>';
		}
	};
	
	function iCategory(data, type, row){
		if(row.category.image){
			return '<i rel="tip" title="'+row.category.name+'"><img class="img_ing ing_ico_list" src="'+row.category.image+'" /></i>';    
		}else{
			return '<img src="/img/pv_molecule.png" class="img_ing_prof"/>';
		}
	};
	
	function iLimit(data, type, row){
		var byPassIFRA = '';
		if(row.info.byPassIFRA == 1){
			var byPassIFRA = '<span class="ing_alg"> <i rel="tip" title="IFRA record is bypassed" class="fas fa-exclamation-triangle"></i></span>';	
		}
		
		if(row.usage.reason == 1){
			var reason = 'Recommendation';
		}else if(row.usage.reason == 2){
			var reason = 'Restriction';
		}else if(row.usage.reason == 3){
			var reason = 'Specification';
		}else if(row.usage.reason == 4){
			var reason = 'Prohibition';
		}else{
			var reason = row.usage.reason;
		}
		
		return '<i class="pv_point_gen pv_gen_li" rel="tip" title="'+reason+'">'+row.usage.limit+'</i>'+byPassIFRA;
	};
	
	function iSuppliers(data, type, row){
		if(row.supplier){
		data ='<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-store mx-2"></i><span class="badge badge-light">'+row.supplier.length+'</span></button><div class="dropdown-menu dropdown-menu-right">';
		for (var key in row.supplier) {
			if (row.supplier.hasOwnProperty(key)) {
				data+='<li><a class="dropdown-item" target="_blank" href="'+row.supplier[key].link+'"><i class="fa fa-store mx-2"></i>'+row.supplier[key].name+'</a></li>';
			}
		}                
		data+='</div></div></td>';
	}else{
			data = 'N/A';
		}
		return data;
	};
	
	function iDocs(data, type, row){
		if(row.document){	
			data ='<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-alt mx-2"></i><span class="badge badge-light">'+row.document.length+'</span></button><div class="dropdown-menu dropdown-menu-right">';
			for (var key in row.document) {
				if (row.document.hasOwnProperty(key)) {
					data+='<a class="dropdown-item popup-link" href="/pages/viewDoc.php?id='+row.document[key].id+'"><i class="fa fa-file-alt mx-2"></i>'+row.document[key].name+'</a>';
				}
			}                
			data+='</div></div></td>';
		
			}else{
				data = 'N/A';
			}
		
		return data;
	};
	
	// Function to handle the display of labels
	function labels(data, type, row) {
		if (Array.isArray(data) && data.length > 0) {
			const maxLabels = 4; // Maximum number of labels to show initially
			const labelList = data;
			const labelSummary = labelList.slice(0, maxLabels);
			let html = '';

			labelSummary.forEach(label => {
				html += `<span class="badge bg-info text-dark mt-2 mx-1">${label}</span>`;
			});

			// "+N more" button with data attribute for jQuery handler
			if (labelList.length > maxLabels) {
				html += `
					<button id="show-all-labels" class="btn btn-sm btn-link p-0 mt-2 mx-1 show-all-labels" 
							type="button" 
							data-labels='${JSON.stringify(labelList)}'>
						+${labelList.length - maxLabels} more
					</button>
				`;
			}

			return html;
		} else {
			return 'N/A';
		}
	};



	function actions(data, type, row, meta){
		data = '<div class="dropdown">' +
			'<button type="button" class="btn btn-floating hidden-arrow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>' +
				'<ul class="dropdown-menu dropdown-menu-right">';
		data += '<li><a href="/pages/mgmIngredient.php?id=' + row.id + '" class="dropdown-item popup-link"><i class="fas fa-edit mx-2"></i>Manage</a></li>';
		data += '<li><a class="dropdown-item" href="/pages/export.php?format=json&kind=single-ingredient&id=' + row.id + '" rel="tip" title="Export '+ row.name +' as JSON" ><i class="fas fa-download mx-2"></i>Export as JSON</a></li>';
		data += '<div class="dropdown-divider"></div>';
		data += '<li><a rel="tip" title="Remove '+ row.name +'" class="dropdown-item pv_point_gen text-danger" id="rmIng" data-name="'+ row.name +'" data-id='+ row.id +'><i class="fas fa-trash mx-2"></i>Delete</a></li>'; 
		data += '</ul></div>';
		
		return data;
	};
	
	
	$('#tdDataIng').on('click', '[id*=copyCAS]', function () {
		const casNumber = $(this).attr('data-name'); // Retrieve the CAS number
	
		if (!casNumber) {
			console.warn("No CAS number found for copying.");
			return;
		}
	
		// Create a temporary textarea to hold the CAS number
		const tempTextarea = document.createElement('textarea');
		tempTextarea.value = casNumber;
		tempTextarea.style.position = 'absolute';
		tempTextarea.style.left = '-9999px'; // Position off-screen to avoid UI disruption
	
		document.body.appendChild(tempTextarea); // Append to the DOM
		tempTextarea.select(); // Select the content
	
		try {
			// Execute the copy command
			const successful = document.execCommand('copy');
			if (successful) {
				// Show Bootstrap tooltip
				const $element = $(this);
				$element.attr('data-bs-original-title', 'Copied!'); // Set tooltip text
				const tooltip = bootstrap.Tooltip.getInstance($element[0]) || new bootstrap.Tooltip($element[0]);
				tooltip.show();
	
				// Hide tooltip after 4 seconds
				setTimeout(() => {
					tooltip.hide();
					$element.removeAttr('data-bs-original-title'); // Clear tooltip to reset for future use
				}, 4000);
			} else {
				console.warn("Copy command was unsuccessful.");
			}
		} catch (err) {
			console.error("Error copying CAS number:", err);
		}
	
		document.body.removeChild(tempTextarea); // Remove the temporary element
	});

	
	
	$('#tdDataIng').on('click', '[id*=rmIng]', function () {
		var ing = {};
		ing.ID = $(this).attr('data-id');
		ing.Name = $(this).attr('data-name');
		
		bootbox.dialog({
		   title: "Confirm ingredient deletion",
		   message : '<div class="alert alert-warning"><i class="fa-solid fa-triangle-exclamation mx-2"></i>WARNING, this action cannot be reverted.</div><p>Permantly delete <strong>'+ ing.Name +'</strong> and its data?</p>' +
		   '<div class="form-group col-sm">' + 
			'<input name="forceDelIng" id="forceDelIng" type="checkbox" value="1">'+
			'<label class="form-check-label mx-2" for="forceDelIng">Force delete ingredient in use</label>'+
		   '</div>',
		   buttons :{
			   main: {
				   label : "Delete",
				   className : "btn-danger",
				   callback: function (){
						
					$.ajax({
						url: '/core/core.php', 
						type: 'POST',
						data: {
							ingredient: "delete",
							ing_id: ing.ID,
							forceDelIng: $("#forceDelIng").is(':checked')
						},
						dataType: 'json',
						success: function (data) {
							if ( data.success ) {
								$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
								$('.toast-header').removeClass().addClass('toast-header alert-success');
								reload_ingredients_data();
							} else {
								$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
								$('.toast-header').removeClass().addClass('toast-header alert-danger');
							}
							$('.toast').toast('show');
						},
						error: function (xhr, status, error) {
							$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error);
							$('.toast-header').removeClass().addClass('toast-header alert-danger');
							$('.toast').toast('show');
						}
					});
					
					 return true;
				   }
			   },
			   cancel: {
				   label : "Cancel",
				   className : "btn-secondary",
				   callback : function() {
					   return true;
				   }
			   }   
		   },onEscape: function () {return true;}
	   });
	});
	
	function reload_ingredients_data() {
		$('#tdDataIng').DataTable().ajax.reload(null, false);
	}
	
	$(".input-group-btn .dropdown-menu li a").click(function () {
		var selText = $(this).html();
		var provider = $(this).attr('data-provider');
		  
		$(this).parents(".input-group-btn").find(".btn-search").html(selText);
		$(this).parents(".input-group-btn").find(".btn-search").attr('data-provider',provider);
		
		$('#pv_search_btn').trigger('click');
		if($('#pv_search_btn').data('provider') === 'local'){
			$("#advanced_search").html('<span><hr /><a href="#" class="advanced_search_box" data-bs-toggle="modal" data-bs-target="#adv_search">Advanced Search</a></span>');
		}else{
			$("#advanced_search").html('');
			tdDataIng.language = {
    			zeroRecords: '<div class="alert alert-warning mt-2"><i class="fa-solid fa-triangle-exclamation mx-2"></i><strong>Nothing found, try <a href="#" data-bs-toggle="modal" data-bs-target="#adv_search">advanced</a> search instead?</strong></div>',
			};
		}
		
	});
	
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
	
	
	$('#wipe_all_ing').click(function() {
		
		bootbox.dialog({
		   title: "Confirm ingredient wipe",
		   message : '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>This will remove ALL your ingredients and their related data from the database.\nThis cannot be reverted so please make sure you have taken a backup first.</div>',
		   buttons :{
			   main: {
				   label : "DELETE ALL",
				   className : "btn-danger",
				   callback: function (){
						
					$.ajax({
						url: '/core/core.php', 
						type: 'POST',
						data: {
							ingredient_wipe: "true",
						},
						dataType: 'json',
						success: function (data) {
							if ( data.success ) {
								$('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
								$('.toast-header').removeClass().addClass('toast-header alert-success');
								$('#tdDataIng').DataTable().ajax.reload(null, false);
							} else {
								$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error);
								$('.toast-header').removeClass().addClass('toast-header alert-danger');
							}
							$('.toast').toast('show');
						},
						error: function (xhr, status, error) {
							$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error);
							$('.toast-header').removeClass().addClass('toast-header alert-danger');
							$('.toast').toast('show');
						}
					});
					
					 return true;
				   }
			   },
			   cancel: {
				   label : "Cancel",
				   className : "btn-secondary",
				   callback : function() {
					   return true;
				   }
			   }   
		   },onEscape: function () {return true;}
	   });
	});

	$('#include_documents').change(function() {
		if ($(this).is(':checked')) {
			$('#documents_warning').show();
		} else {
			$('#documents_warning').hide();
		}
	});

	$('#export_confirm_btn').click(function() {
		var includeSuppliers = $('#include_suppliers').is(':checked') ? 1 : 0;
		var includeCompositions = $('#include_compositions').is(':checked') ? 1 : 0;
		var includeDocuments = $('#include_documents').is(':checked') ? 1 : 0;
		var url = '/pages/export.php?format=json&kind=ingredients&includeSuppliers=' + includeSuppliers + '&includeCompositions=' + includeCompositions + '&includeDocuments=' + includeDocuments;
		
		$('#export_confirm_btn').prop('disabled', true).text('Please wait...');
		
		$.ajax({
			url: url,
			type: 'GET',
			dataType: 'json',
			success: function(data) {
				if(data.error) {
					$('#exportmsg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error + '</div>');
					$('#export_confirm_btn').prop('disabled', false).text('Export');
					return;
				}
				window.location.href = url;
				$('#export_options_modal').modal('hide');
				$('#export_confirm_btn').prop('disabled', false).text('Export');
			},
			error: function(xhr, status, error) {
				$('#exportmsg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An error occurred: ' + error + '</div>');
				$('#export_confirm_btn').prop('disabled', false).text('Export');
			}
		});
	});

});
</script>
<script src="/js/import.ingredients.js"></script>

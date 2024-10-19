/*
PV Ingredient Select
Requires Select2 v3 lib
*/

$(document).ready(function(){
	var isDeep = false;
	
	function extrasShow() {	
	
		$('[rel=tip]').tooltip();
		$('.select2-search').on('click', '[id*=select_search_deep]', function () {	 
		  $('#isDeep').each(function () { this.checked = !this.checked; });
		  // $('#s2id_autogen1_search').trigger("keydown");
		   if ($('#isDeep').is(':checked')) {
			   isDeep = $('#isDeep').is(':checked');
		   } else {
				isDeep = $('#isDeep').is(':checked');
		   }
		});
		
		$('.popup-link').magnificPopup({
			type: 'iframe',
			closeOnContentClick: false,
			closeOnBgClick: false,
			showCloseBtn: true,
		});
		
		$('#add_new_ing_sel a').click(function(){
			$('#ingredient').select2("close");
		});
		
	};



	$("#ingredient").select2({
		minimumInputLength: 2,
		dropdownAutoWidth: true,
		allowClear: true,
		placeholder: '',
		templateResult: formatIngredients,
		templateSelection: formatIngredientsSelection,
		ajax: {
			url: '/core/list_ingredients_simple.php',
			dataType: 'json',
			type: 'POST',
			delay: 300,
			data: function(data) {
				return {
					search: data,
					isDeepQ: isDeep
				};
			},
			processResults: function(data) {
				if (data.recordsTotal) {
					$(".select2-totalRecords").html('Ingredients found: <strong>' + data.recordsTotal + '</strong>');
				} else {
					$(".select2-totalRecords").html('');
				}
				return {
					results: $.map(data.data, function(ingData) {
						return {
							id: ingData.id,
							name: ingData.name,
							IUPAC: ingData.IUPAC,
							cas: ingData.cas,
							ingType: ingData.type,
							description: ingData.description,
							physical_state: ingData.physical_state,
							stock: ingData.stock,
							profile: ingData.profile,
							mUnit: ingData.mUnit
						}
					})
				};
			},
			cache: true
		}
	}).on('select2:open', () => {
		// Delay to ensure the select2 elements have rendered
		setTimeout(() => {
			// Check if the content has already been added to prevent duplication
			if (!$(".select2-search").find("#add_new_ing_sel").length) {
				$(".select2-search").prepend(`
					<div id="add_new_ing_sel" class="select_add_new_ingredient mb-2">
						<a href="/pages/mgmIngredient.php" class="popup-link add-new-ing-sel">
							<i class="fa fa-plus mx-2"></i>Create new ingredient
						</a>
					</div>
				`);
			}
			
			// Append total records and deep search options if not already present
			if (!$(".select2-search").find(".select2-totalRecords").length) {
				$(".select2-search").append(`
					<div class="select2-totalRecords"></div>
					<div class="select_deep_ingredient">
						<span>
							<div id="select_search_deep" class="select_search_deep">
								<i class="pv_point_gen mx-2" rel="tip" data-placement="bottom" title="Extend search in synonyms">
									<input data-default="true" type="checkbox" id="isDeep">
									<label for="isDeep">Deep Search</label>
								</i>
							</div>
						</span>
					</div>
				`);
			}
	
			// Reset the checkbox and clear total records
			$('#isDeep').prop('checked', false);
			isDeep = false;
			$(".select2-totalRecords").html('');
			$('.popover').hide();
	
			extrasShow();
			
		}, 100); // Small delay to ensure elements are rendered
	}).on('select2:selecting', function (e) {
		var id = e.params.args.data.id;
		var type = e.params.args.data.ingType;
		
		$(this).attr('ing-id', id);
		$(this).attr('ing-type', type);
	});

	

	
	function formatIngredients (ingredientData) {
		if (ingredientData.loading) {
			return ingredientData.name;
		}
	 
		//extrasShow();
	
		if (!ingredientData.name){
			return 'No ingredient found...';
		}
		
		let measureIn = ingredientData.mUnit || (ingredientData.physical_state == '1' ? 'mL' : ingredientData.physical_state == '2' ? 'grams' : '');

		var $container = $(
			"<div class='select_result_igredient clearfix'>" +
			  "<div class='select_result_igredient_meta'>" +
				"<div class='select_igredient_title'></div>" +
				"<span id='stock'></span></div>"+
				"<div class='select_result_igredient_description'></div>" +
				"<div class='select_result_igredient_info'>" +
				  "<div class='select_result_igredient_cas'></div>" +
				  "<div class='select_result_igredient_iupac'></div>" +
				  "<div class='select_result_igredient_profile'></div>" +
				"</div>" +
			  "</div>" +
			"</div>"
		  );
		
		  $container.find(".select_igredient_title").text(ingredientData.name);
		  if(ingredientData.stock  > 0){
		  	$container.find("#stock").text('In stock ('+ingredientData.stock + measureIn +')');
			$container.find("#stock").attr("class", "stock badge badge-instock");
		  }else{
			$container.find("#stock").text('Not in stock ('+ingredientData.stock + measureIn +')');
			$container.find("#stock").attr("class", "stock badge badge-nostock");
		  }
		  $container.find(".select_result_igredient_description").text(ingredientData.description);
		  $container.find(".select_result_igredient_cas").append("CAS: " + ingredientData.cas);
		  $container.find(".select_result_igredient_iupac").append("IUPAC: " + ingredientData.IUPAC);
		  $container.find(".select_result_igredient_profile").append("Profile: " + ingredientData.profile + " note");

		  return $container;
	}
	
	
	function formatIngredientsSelection (ingredientData) {
		if (ingredientData.id === '') {
      		return 'Search ingredients (name, cas)';
    	}

		return ingredientData.name;
	}
	
	//UPDATE PURITY
	$('#ingredient').on('select2:selecting', function(e) {
		var ingType = $(e.currentTarget).attr('ing-type');
		var ingID = $(e.currentTarget).attr('ing-id');
		
		$.ajax({
			url: '/core/getIngInfo.php',
			type: 'GET',
			data: {
				filter: "purity,solvent",
				id: ingID
			},
			dataType: 'json',
			success: function(data) {
				if (ingType === 'Solvent') {
					$("#concentration").prop("disabled", true).val(100);
					$("#dilutant").prop("disabled", true).val('none').selectpicker("refresh");
				} else {
					$("#concentration").prop("disabled", false).val(data.purity).trigger("input");
					//$("#dilutant").prop("disabled", false).val(data.solvent).selectpicker("refresh");
				}
	
				$("#quantity").prop("disabled", false);
			},
			error: function(xhr, status, error) {
				console.error('Error fetching ingredient info:', error);
			}
		});
	});
	
	$('#concentration').on('input change', function() {
		const MAX_CONCENTRATION = 100;
		var concentration = parseFloat($(this).val());
	
		if (!isNaN(concentration)) {
			if (concentration >= MAX_CONCENTRATION) {
				$("#dilutant").prop("disabled", true).val('none').selectpicker("refresh");
			} else {
				$("#dilutant").prop("disabled", false).selectpicker("refresh");
			}
		} else {
			console.warn('Invalid concentration input');
		}
	});



});

/*
PV Ingredient Select
Requires Select2 v3 lib
*/

$(document).ready(function(){
	var isDeep = false;
	
	function extrasShow() {	
	
		$('[rel=tip]').tooltip();
		$('.select2-with-searchbox').on('click', '[id*=select_search_deep]', function () {	 
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
		//containerCssClass : "form-select mb-3",
        allowClear: true,
		placeholder: 'Choose ingredient (name, cas)',
		templateResult: formatIngredients,
		templateSelection: formatIngredientsSelection,
		ajax: {
			url: '/core/list_ingredients_simple.php',
			dataType: 'json',
			type: 'POST',
			quietMillis: 250,
			delay: 300,
			data: function (data) {
				return {
					search: data,
					isDeepQ: isDeep
				};
			},
			processResults: function(data) {
				if(data.recordsTotal){
					$(".select2-totalRecords").html('Ingredients found: <strong>' + data.recordsTotal + '</strong>');
				}else{
					$(".select2-totalRecords").html('');
				}
				return {
					results: $.map(data.data, function(obj) {
						return {
							id: obj.id,
							name: obj.name,
							IUPAC: obj.IUPAC,
							cas: obj.cas,
							ingType: obj.type,
							description: obj.description,
							physical_state: obj.physical_state,
							stock: obj.stock,
							profile: obj.profile
						}
					})
				};
			},
			cache: true
		}
	  
	}).on('select2:open', () => {
		$(".select2-search:not(:has(a))").prepend('<div id="add_new_ing_sel" class="select_add_new_ingredient mb-2"><a class="text-primary fa fa-plus mx-2"></a><a href="/pages/mgmIngredient.php" class="popup-link text-primary add-new-ing-sel">Create new ingredient</a></div>');
		
		$(".select2-search:not(:has(i))").append('<div class="select2-totalRecords"></div><div class="select_deep_ingredient"><span><div id="select_search_deep" class="select_search_deep"><i class="pv_point_gen mx-2" rel="tip" data-placement="bottom" title="Extend search in synonyms"><input data-default="true" type="checkbox" id="isDeep"></i>Deep Search</div></span></div>');
		
		$('#isDeep').prop('checked', false);
		isDeep = false;
		$(".select2-totalRecords").html('');
		
		$('.popover').hide();
		extrasShow();

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
		
		var measureIn;
		if (ingredientData.physical_state == '1'){
			measureIn = 'mL';
		}else if (ingredientData.physical_state == '2'){
			measureIn = 'grams';
		}
		
		var $container = $(
			"<div class='select_result_igredient clearfix'>" +
			  "<div class='select_result_igredient_meta'>" +
				"<div class='select_igredient_title'></div>" +
				"<span id='stock' ></span></div>"+
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
		return ingredientData.name;
	}
	
	//UPDATE PURITY
	$('#ingredient').on('select2:selecting', function(e){
		var ingType = $(e.currentTarget).attr('ing-type');
		var ingID = $(e.currentTarget).attr('ing-id');
		//console.log(e);
		$.ajax({ 
			url: '/pages/getIngInfo.php', 
			type: 'GET',
			data: {
				filter: "purity",
				id: ingID
				},
			dataType: 'json',
			success: function (data) {
			  if(ingType == 'Solvent'){
				$("#concentration").prop("disabled", true); 
				$("#dilutant").prop("disabled", true);
				$("#concentration").val(100);
				$("#dilutant").val('None');
			  }else{
				$("#concentration").prop("disabled", false);
				$("#concentration").val(data.purity).trigger("input");;
			  }
			 $("#quantity").prop("disabled", false);
			 $("#quantity").val();
			}
		  });
		
		$.ajax({ 
			url: '/pages/getIngInfo.php', 
			type: 'GET',
			data: {
				filter: "solvent",
				id: ingID
				},
			dataType: 'json',
			success: function (data) {
			  $('#dilutant').val(data.solvent);
			}
		});
	
	});
	
	$('#concentration').bind('input', function() {
		var concentration = $('#concentration').val();
		if(concentration >= 100){
			$("#dilutant").prop("disabled", true); 
			$("#dilutant").val('').selectpicker("refresh");
		}else{
			$("#dilutant").prop("disabled", false).selectpicker('refresh');
		}
		//$('.selectpicker').selectpicker('refresh');
	});

});

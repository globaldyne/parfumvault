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
		theme: 'bootstrap',
        allowClear: true,
		placeholder: 'Choose ingredient (name, cas)',
		formatResult: formatIngredients,
		formatSelection: formatIngredientsSelection,
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
							type: obj.type,
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
	  
	}).on('select2-open', () => {
		$(".select2-with-searchbox:not(:has(a))").prepend('<div id="add_new_ing_sel" class="select_add_new_ingredient "><a class="text-primary fa fa-plus ml2"></a><a href="/pages/mgmIngredient.php" class="popup-link text-primary add-new-ing-sel">Create new ingredient</a></div>');
		
		$(".select2-with-searchbox:not(:has(i))").append('<div class="select2-totalRecords"></div><div class="select_deep_ingredient"><span><div id="select_search_deep" class="select_search_deep"><i class="pv_point_gen" rel="tip" data-placement="bottom" title="Extend search in synonyms"><input data-default="true" type="checkbox" id="isDeep"> Deep Search</i></div></span></div>');
		
		$('#isDeep').prop('checked', false);
		isDeep = false;
		$(".select2-totalRecords").html('');
		
		$('.popover').hide();
		extrasShow();

	}).on('select2-selected', function (data) {
  		var id = data.choice.id;
   		var type = data.choice.type
		
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
	$('#ingredient').on('select2-selected', function(data){
		var ingType = $(data.currentTarget).attr('ing-type');
		var ingID = $(data.currentTarget).attr('ing-id');
		
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
		var purity = $(this).val();
		if(purity == 100){
			$("#dilutant").prop("disabled", true); 
			$("#dilutant").val('');
		}else{
			$("#dilutant").prop("disabled", false);
		}
		$('.selectpicker').selectpicker('refresh');
	});


});

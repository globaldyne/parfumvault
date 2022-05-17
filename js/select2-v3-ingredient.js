/*
PV Ingredient Select
Requires Select2 v3 lib
*/

$(document).ready(function(){

	function extrasShow() {	
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
		//width: '500px',
    	dropdownAutoWidth: true,
		theme: 'bootstrap',

		placeholder: 'Choose ingredient',
		formatResult: formatIngredients,
		formatSelection: formatIngredientsSelection,
		ajax: {
			url: '/core/list_ingredients_simple.php',
			dataType: 'json',
			type: 'GET',
			quietMillis: 250,
			delay: 300,
			data: function (data) {
				return {
					search: data
				};
			},
			processResults: function(data) {
				return {
					results: $.map(data.data, function(obj) {
						return {
							id: obj.id,
							name: obj.name,
							IUPAC: obj.IUPAC,
							cas: obj.cas,
							type: obj.type,
							description: obj.description
						}
					})
				};
			},
			cache: true
		}
	  
	}).on('select2-open', () => {
		$(".select2-with-searchbox:not(:has(a))").prepend('<div id="add_new_ing_sel" class="select_add_new_ingredient"><a href="/pages/mgmIngredient.php" class="popup-link fa fa-plus text-primary add-new-ing-sel"> Create new ingredient</a></div>');
		
	$('.popover').hide();
	
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
	 
		extrasShow();
	
		if (!ingredientData.name){
			return 'No ingredient found...';
		}
	
		var $container = $(
			"<div class='select_result_igredient clearfix'>" +
			  "<div class='select_result_igredient_meta'>" +
				"<div class='select_igredient_title'></div>" +
				"<div class='select_result_igredient_description'></div>" +
				"<div class='select_result_igredient_info'>" +
				  "<div class='select_result_igredient_cas'></div>" +
				  "<div class='select_result_igredient_iupac'></div>" +
				"</div>" +
			  "</div>" +
			"</div>"
		  );
		
		  $container.find(".select_igredient_title").text(ingredientData.name);
		  $container.find(".select_result_igredient_description").text(ingredientData.description);
		  $container.find(".select_result_igredient_cas").append("CAS: " + ingredientData.cas);
		  $container.find(".select_result_igredient_iupac").append("IUPAC: " + ingredientData.IUPAC);
		
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
			dataType: 'html',
			success: function (data) {
			  if(ingType == 'Solvent'){
				$("#concentration").prop("disabled", true); 
				$("#dilutant").prop("disabled", true);
				$("#concentration").val(100);
				$("#dilutant").val('None');
			  }else{
				$("#concentration").prop("disabled", false);
				$("#dilutant").prop("disabled", false); 
				$("#concentration").val(data);
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
			dataType: 'html',
			success: function (data) {
			  $('#dilutant').val(data);
			}
		});
	
	});

})

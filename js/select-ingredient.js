/*
PV Ingredient Select
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
		placeholder: 'Search for ingredient',
		templateResult: formatIngredients,
		templateSelection: formatIngredientsSelection,
		ajax: {
			url: '/core/list_ingredients_simple.php',
			dataType: 'json',
			type: "GET",
			delay: 300,
			data: function (data) {
				return {
					search: data.term
				};
			},
			processResults: function(data) {
				return {
					results: $.map(data.data, function(obj) {
						return {
							ingId: obj.id,
							id: obj.name, //TODO: TO BE CHANGED TO ID WHEN THE BACKEND IS READY
							name: obj.name,
							IUPAC: obj.IUPAC,
							cas: obj.cas,
							type: obj.type,
							description: obj.description
						}
					})
				};
			},
			//cache: true
		}
	  
	}).on('select2:open', () => {
		$(".select2-results:not(:has(a))").prepend('<div id="add_new_ing_sel" class="select_add_new_ingredient"><a href="/pages/mgmIngredient.php" class="popup-link fa fa-plus text-primary add-new-ing-sel"> Create new ingredient</a></div>');
			
	}).on('select2:select', function (e) {
	   var id = e.params.data.ingId;
	   var type = e.params.data.type;
	
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
	$('#ingredient').on('select2:select', function(e){
		var ingType = $(e.currentTarget).attr('ing-type');
		var ingID = $(e.currentTarget).attr('ing-id');
		
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
/*
PV Ingredient Select
*/

function extrasShow() {	
	$('.popup-link').magnificPopup({
		type: 'iframe',
		closeOnContentClick: false,
		closeOnBgClick: false,
		showCloseBtn: true,
	});
};
		
$( ".add-new-ing-sel" ).on("click",function(){
	$('#ingredient').select2("close");
	console.log('ssssss');
});

$("#ingredient").select2({
	//width: "500px",
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
                        id: obj.name,
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
        $(".select2-results:not(:has(a))").prepend('<div class="select_add_new_ingredient"><a href="/pages/mgmIngredient.php" class="popup-link fa fa-plus text-primary add-new-ing-sel"> Create new ingredient</a></div>');
		
}).on('select2:select', function (e) {
   var id = e.params.data.ingId;
   var type = e.params.data.type;

   $(this).attr('ing-id', id);
   $(this).attr('ing-type', type);
});

function formatIngredients (igredientData) {
  if (igredientData.loading) {
    return igredientData.name;
  }
			extrasShow();

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

  $container.find(".select_igredient_title").text(igredientData.name);
  $container.find(".select_result_igredient_description").text(igredientData.description);
  $container.find(".select_result_igredient_cas").append("CAS: " + igredientData.cas);
  $container.find(".select_result_igredient_iupac").append("IUPAC: " + igredientData.IUPAC);

  return $container;
}

function formatIngredientsSelection (igredientData) {
  return igredientData.name || igredientData.cas;
}

//UPDATE PURITY
$('#ingredient').on('select2:select', function(e){
	var ingType = $(e.currentTarget).attr('ing-type');
	var ingID = $(e.currentTarget).attr('ing-id');


	$.ajax({ 
		url: 'pages/getIngInfo.php', 
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
			$('#concentration').val(100);
            $('#dilutant').val('None');
		  }else{
            $("#concentration").prop("disabled", false);
			$("#dilutant").prop("disabled", false); 
			$('#concentration').val(data);
		  }
		 $("#quantity").prop("disabled", false);
         $("#quantity").val();
		}
	  });
	
	$.ajax({ 
		url: 'pages/getIngInfo.php', 
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
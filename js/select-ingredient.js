/*
PV Ingredient Select
*/

let ingredientsLit = $('#ingredient');
ingredientsLit.empty();
ingredientsLit.append('<option selected="true" disabled>Choose ingredient</option>');
ingredientsLit.prop('selectedIndex', 0);

$.ajax({
    url:'/core/list_ingredients_simple.php',
    type:'GET',
    datatype:'json',
    success:function(data) {
        $.each(data.data, function(key, ing) {
   			 ingredientsLit.append($('<option ing-id="'+ing.id+'" ing-type="'+ing.type+'" data-subtext="'+ing.IUPAC+'"></option>').val(ing.name).html(ing.name + ' ('+ing.cas+')'));
  		})
 		ingredientsLit.selectpicker('refresh');
    }
});

//UPDATE PURITY
$('#ingredient').on('change', function(){
	var ingType = $("#ingredient").find('option:selected').attr('ing-type');
	var ingID = $("#ingredient").find('option:selected').attr('ing-id');

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
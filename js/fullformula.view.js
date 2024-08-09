/*!
 * PV v7.4 (https://www.perfumersvault.com)
 * Copyright 2017-2023
 * Licensed under the MIT license
 * 
 * Full formula js helpers
 */

//MULTIPLY - DIVIDE
$('.manageQuantity').click(function() {
	$.ajax({ 
    url: '/pages/manageFormula.php', 
	type: 'POST',
    data: {
		do: 'scale',
		scale: $(this).attr('data-action'),
		formula: myFID,
		},
    success: function (data) {
		reload_formula_data();
    }
  });
});

//AMOUNT TO MAKE
$('#amount_to_make').on('click', '[id*=amountToMake]', function () {
	if($("#sg").val().trim() == '' ){
        $('#sg').focus();
	  	$('#amountToMakeMsg').html('<div class="alert alert-danger"><strong>Error:</strong> all fields required!</div>');
	}else if($("#totalAmount").val().trim() == '' ){
 		$('#totalAmount').focus();
	  	$('#amountToMakeMsg').html('<div class="alert alert-danger"><strong>Error:</strong> all fields required!</div>');		
	}else{
		$.ajax({ 
		url: '/pages/manageFormula.php', 
		type: 'POST',
		cache: false,
		data: {
			fid: myFID,
			SG: $("#sg").val(),
			amount: $("#totalAmount").val(),
			},
		success: function (data) {
			$('#amountToMakeMsg').html(data);
			$('#amount_to_make').modal('toggle');
			reload_formula_data();
		}
	  });
	}
});


//Create Accord 
$('#create_accord').on('click', '[id*=createAccord]', function () {
	if($("#accordName").val().trim() == '' ){
        $('#accordName').focus();
	  	$('#accordMsg').html('<div class="alert alert-danger"><strong>Error:</strong> Accord name required!</div>');	
	}else{
		$.ajax({ 
		url: '/pages/manageFormula.php', 
		type: 'POST',
		cache: false,
		data: {
			fid: myFID,
			accordName: $("#accordName").val(),
			accordProfile: $("#accordProfile").val(),
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
			var msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>'+data.success+'</div>';
			reload_formula_data();
		}else if(data.error){
			var msg = '<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-triangle-exclamation mx-2"></i>'+data.error+'</div>';
		}
		$('#accordMsg').html(msg);
		}
	  });
	}
});

//Convert to ingredient
$('#conv_ingredient').on('click', '[id*=conv2ing]', function () {
	if($("#ingName").val().trim() == '' ){
        $('#ingName').focus();
	  	$('#cnvMsg').html('<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> Ingredient name required!</div>');	
	}else{
		$.ajax({ 
		url: '/pages/manageFormula.php', 
		type: 'POST',
		cache: false,
		data: {
			fid: myFID,
			fname: myFNAME,
			ingName: $("#ingName").val(),
			action: 'conv2ing',
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-check mx-2"></i>'+data.success+'</div>';
			}else if(data.error){
				var msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>'+data.error+'</div>';
			}
			$('#cnvMsg').html(msg);
		}
	  });
	}
});

//Clone
$('#cloneMe').click(function() {
$.ajax({ 
    url: '/pages/manageFormula.php', 
	type: 'POST',
    data: {
		action: "clone",
		fname: myFNAME,
		fid: myFID,
		},
	dataType: 'json',
    success: function (data) {
		if ( data.success ) {
			$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
			$('.toast-header').removeClass().addClass('toast-header alert-success');
		} else {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
		}
		$('.toast').toast('show');
	}

  });
});

//Add in Schedule
$('#schedule_to_make').on('click', '[id*=addTODO]', function () {
	$.ajax({ 
    url: '/pages/manageFormula.php', 
	type: 'POST',
    data: {
		action: 'todo',
		fname: myFNAME,
		fid: myFID,
		add: true,
		},
	dataType: 'json',
    success: function (data) {
	
		if ( data.success ) {
			$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
			$('.toast-header').removeClass().addClass('toast-header alert-success');
			
		} else {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
		}
		$('#schedule_to_make').modal('toggle');
		$('.toast').toast('show');
    }
  });
});

$('#formula').on('click', '[id*=cCAS]', function () {
	var copy = {};
	copy.Name = $(this).attr('data-name');
	const el = document.createElement('textarea');
    el.value = copy.Name;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
});

$('#replaceIng').on('click', '[id*=replaceConfirm]', function () {
	$.ajax({ 
		url: "/pages/manageFormula.php" , 
		type: 'POST',
		data: {
			action: "repIng",
			dest: $("#repIngNameDest").val(),
			ingSrcName: $("#ingRepName").val(),
			ingSrcID: $("#ingRepID").val(),
			fid: myFID,
			},
		dataType: 'json',
		success: function (data) {
			if ( data.success ) {
            	$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
				$('.toast-header').removeClass().addClass('toast-header alert-success');
				reload_formula_data();
				$('#replaceIng').modal('hide'); 
				$('.toast').toast('show');
			} else {
            	var msg ='<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
				$('#msgRepl').html(msg);
			}
			
		},
	  });
});

$("#formula").on("click", ".open-replace-dialog", function () {
	$('#msgInfo').html('');
	$('#msgRepl').html('');
	$("#replaceIng #ingTargInfo").html('');
	$("#replaceIng #repIngNameDest").val( '' );
	$("#repGrid").hide();

	
	var ingRepName = $(this).data('name');
	var ingRepID = $(this).data('id');
	
	var ingSrcCas = $(this).data('cas');
	var ingSrcDesc = $(this).data('desc');

	var repName;
	var repID;
	
	$("#replaceIng #ingRepID").val( ingRepID );
	$("#replaceIng #ingRepName").val( ingRepName );
	$("#replaceIng #ingRepName").html( ingRepName );
	
	$("#replaceIng #ingSrcInfo").html('<strong>'+ingRepName+'</strong><p><strong>CAS: </strong>' + ingSrcCas + '</p><p> <strong>Description: </strong>' + ingSrcDesc +'</p>');

	
	$("#repIngNameDest").select2({
		width: '100%',
		placeholder: 'Search for ingredient (name, cas)',
		allowClear: true,
		dropdownAutoWidth: true,
		containerCssClass: "repIngNameDest",
		minimumInputLength: 2,
		templateResult: formatIngredients,
		templateSelection: formatIngredientsSelection,
		dropdownParent: $('#replaceIng .modal-content'),
		ajax: {
			url: '/core/list_ingredients_simple.php',
			dataType: 'json',
			type: 'POST',
			delay: 100,
			quietMillis: 250,
			data: function (data) {
				return {
					search: data,
					isDeepQ: "false"
				};
			},
			processResults: function(data) {
				return {
					results: $.map(data.data, function(ingData) {
					  return {
						id: ingData.name,
						description: ingData.description,
						cas: ingData.cas,
						stock: ingData.stock,
						physical_state: ingData.physical_state,
						name: ingData.name,
						mUnit: ingData.mUnit

					  }
					})
				};
			},
			cache: true,
			
		}
		
	}).on('select2:selecting', function (e) {
			 repName = e.params.args.data.name;
			 repID = e.params.args.data.name; //NEEDS ID?!
			 $("#repGrid").show();
			 $("#replaceIng #ingTargInfo").html('<strong>'+e.params.args.data.name+'</strong><p><strong>CAS:</strong> ' + e.params.args.data.cas + '</p><p> <strong>Description: </strong>' +e.params.args.data.description +'</p>');
	});
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

		  return $container;
	}
	
	
	function formatIngredientsSelection (ingredientData) {
		return ingredientData.name;
	}
	
	

$('#mrgIng').on('click', '[id*=mergeConfirm]', function () {
	$.ajax({ 
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			merge: "true",
			dest: $("#mrgIngName").val(),
			ingSrcName: $("#ingSrcName").val(),
			ingSrcID: $("#ingSrcID").val(),
			fid: myFID,
			},
		dataType: 'json',
		success: function (data) {
			if ( data.success ) {
            	$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
				$('.toast-header').removeClass().addClass('toast-header alert-success');
				reload_formula_data();
				$('#mrgIng').modal('hide'); 
				$('.toast').toast('show');
			} else {
            	var msg ='<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
				$('#msgMerge').html(msg);
			}
			
		},
	  });
});

$("#formula").on("click", ".open-merge-dialog", function () {
	$('#msgInfo').html('');
	$('#msgMerge').html('');
	$("#mrgIng #mrgIngName").val('');

	var ingSrcName = $(this).data('name');
	var ingSrcID = $(this).data('id');
	var mrgName;
	var mrgID;
	
	$("#mrgIng #ingSrcID").val( ingSrcID );
	$("#mrgIng #ingSrcName").val( ingSrcName );
	$("#mrgIng #srcIng").html( ingSrcName );

	
	$("#mrgIngName").select2({
		width: '100%',
		placeholder: 'Search for ingredient (name)..',
		allowClear: true,
		dropdownAutoWidth: true,
		containerCssClass: "mrgIngName",
		dropdownParent: $('#mrgIng .modal-content'),
		ajax: {
			url: '/core/full_formula_data.php',
			dataType: 'json',
			type: 'POST',
			delay: 100,
			quietMillis: 250,
			data: function (params) {
				return {
					id: myID,
					search: params.term
				};
			},
			processResults: function(data) {
				return {
					results: $.map(data.data, function(obj) {
					  return {
						id: obj.formula_ingredient_id,
						ingId: obj.ingredient.ingredient_id,
						text: obj.ingredient.name || 'No ingredient found...',
					  }
					})
				};
			},
			cache: false,
			
		}
		
	}).on('select2:selected', function (data) {
			 mrgName = data.params.text;
			 mrgID = data.params.ingId;
	});
});

$('#manage-quantity').on('click', '[id*=quantityConfirm]', function () {
	$.ajax({ 
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			updateQuantity: "true",
			ingQuantity: $(".ingQuantity").val(),
			ingQuantityName: $("#ingQuantityName").val(),
			ingQuantityID: $("#ingQuantityID").val(),
			ingID: $("#mainingid").val(),
			curQuantity: $("#curQuantity").val(),
			ingReCalc: $("#reCalc").prop('checked'),
			formulaSolventID: $("#formulaSolvents").val(),
			fid: myFID,
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
				$('#manage-quantity').modal('hide'); 
				reload_formula_data();
				
			}else{
				msg ='<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
				$('#msgQuantity').html(msg);
			}
			
		},
	  });
});

$("#formula").on("click", ".open-quantity-dialog", function () {
	$('#msgQuantity').html('');
	$('#manage-quantity #reCalc').prop( "checked", false );
	$("#slvMeta").hide();
	$("#formulaSolvents").val('');

	var ingQuantity = $(this).data('value');
	var ingQuantityID = $(this).data('ingid');
	var ingQuantityName = $(this).data('ing');
	var mainingid = $(this).data('mainingid');
	var curQuantity = $(this).data('value');

	$("#manage-quantity #ingQuantity").val( ingQuantity );
	$("#manage-quantity #ingQuantityID").val( ingQuantityID );
	$("#manage-quantity #ingQuantityName").val( ingQuantityName );
	$("#manage-quantity #ingQuantityName").html( ingQuantityName );
	$("#manage-quantity #mainingid").val( mainingid );
	$("#manage-quantity #curQuantity").val( curQuantity );

	$("#formulaSolvents").select2({
		width: '100%',
		placeholder: 'Available solvents in formula',
		allowClear: true,
		dropdownAutoWidth: true,
		containerCssClass: "formulaSolvents",
		minimumResultsForSearch: Infinity,
		theme: "classic",
		dropdownParent: $('#manage-quantity .modal-content'),
		ajax: {
			url: '/core/full_formula_data.php',
			dataType: 'json',
			type: 'POST',
			delay: 100,
			quietMillis: 250,
			data: function (data) {
				return {
					id: myID,
					solvents_only: true
				};
			},
			processResults: function(data) {
				return {
					results: $.map(data.data, function(obj) {
					  return {
						id: obj.ingredient_id,
						text: obj.ingredient || 'No solvent(s) found in formula',
					  }
					})
				};
			},
			cache: true,
		}		
	});
	
});


$('.export_as').click(function() {	
  var format = $(this).attr('data-format');
  $("#formula").tableHTMLExport({
	type: format,
	filename: myFNAME + "." + format,
	separator: ',',
  	newline: '\r\n',
  	trimContent: true,
  	quoteFields: true,
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',
	htmlContent: false,
	orientation: 'l',
	subtitle: 'Created with Perfumer\'s Vault Pro',
	maintitle: myFNAME,
  });
});

$("#slvMeta, #slvMetaAdd").hide();

$("#reCalc").click(function() {
    if($(this).is(":checked")) {
        $("#slvMeta").show();
    } else {
        $("#slvMeta").hide();
    }
});


$("#reCalcAdd").click(function() {
    if($(this).is(":checked")) {
        $("#slvMetaAdd").show();
    } else {
        $("#slvMetaAdd").hide();
    }
	
	$("#formulaSolventsAdd").select2({
		width: '100%',
		placeholder: 'Available solvents in formula',
		allowClear: true,
		dropdownAutoWidth: true,
		containerCssClass: "formulaSolvents",
		dropdownParent: $('#add_ing'),
		minimumResultsForSearch: Infinity,
		ajax: {
			url: '/core/full_formula_data.php',
			dataType: 'json',
			type: 'POST',
			delay: 100,
			quietMillis: 250,
			data: function (data) {
				return {
					id: myID,
					solvents_only: true
				};
			},
			processResults: function(data) {
				return {
					results: $.map(data.data, function(obj) {
					  return {
						id: obj.ingredient_id,
						text: obj.ingredient || 'No solvent(s) found in formula',
					  }
					})
				};
			},
			cache: true,
		}
	});
	
});

function reset_solv(){
	$("#reCalcAdd").prop( "checked", false );
	$("#slvMetaAdd").hide();
	$("#formulaSolventsAdd").val('');
};


$('.table').on('show.bs.dropdown', function () {
	 $('.table-responsive').css( "overflow", "inherit" );
});

$('.table').on('hide.bs.dropdown', function () {
	 $('.table-responsive').css( "overflow", "auto" );
});


function update_bar(){
     $.getJSON("/core/full_formula_data.php?id="+myID+"&stats_only=1", function (json) {
		
		$('#formula_name').html(json.stats.formula_name || "Unnamed");
		$('#formula_desc').html(json.stats.formula_description);

		if (json.stats.data) {
			$('#progress-area').show();
	
			var top = Math.round(json.stats.data.top.current);
			var top_max = Math.round(json.stats.data.top.max);
	
			var heart = Math.round(json.stats.data.heart.current);
			var heart_max = Math.round(json.stats.data.heart.max);
	
			var base = Math.round(json.stats.data.base.current);
			var base_max = Math.round(json.stats.data.base.max);
	
			$('#top_bar').attr('aria-valuenow', top).css('width', top + '%').attr('aria-valuemax', top_max);
			$('#heart_bar').attr('aria-valuenow', heart).css('width', heart + '%').attr('aria-valuemax', heart_max);
			$('#base_bar').attr('aria-valuenow', base).css('width', base + '%').attr('aria-valuemax', base_max);
	
			$('.top-label').html(top + "% Top Notes");
			$('.heart-label').html(heart + "% Heart Notes");
			$('.base-label').html(base + "% Base Notes");
			
			//$('.Top_notes').html("Top Notes");
			//$('.Heart_notes').html("Heart Notes");
			//$('.Base_notes').html("Base Notes");

		} else {
			$('#progress-area').hide();
		}
		
	}); 
};
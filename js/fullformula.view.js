/*!
 * PV v7.4 (https://www.jbparfum.com)
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
	  	$('#amountToMakeMsg').html('<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> all fields required!</div>');
	}else if($("#totalAmount").val().trim() == '' ){
 		$('#totalAmount').focus();
	  	$('#amountToMakeMsg').html('<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> all fields required!</div>');		
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
	  	$('#accordMsg').html('<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> Accord name required!</div>');	
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
			var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>';
			reload_formula_data();
		}else if(data.error){
			var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
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
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>';
			}else if(data.error){
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
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
		if (data.success) {
			var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
		}else{
			var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
		}
		$('#msgInfo').html(msg);
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
		if (data.success) {
	  		var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
			$('#schedule_to_make').modal('toggle');
			$('#msgInfo').html(msg);
		}else{
			var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			$('#scheduleToMakeMsg').html(msg);
		}
		
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
			if(data.success){
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				$('#replaceIng').modal('hide'); 
				reload_formula_data();
				$('#msgInfo').html(msg);
			}else{
				var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
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
		width: '250px',
		placeholder: 'Search for ingredient (name, cas)',
		allowClear: true,
		dropdownAutoWidth: true,
		containerCssClass: "repIngNameDest",
		minimumInputLength: 2,
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
					results: $.map(data.data, function(obj) {
					  return {
						id: obj.name,
						desc: obj.description,
						cas: obj.cas,
						text: obj.name || 'No ingredient found...',
					  }
					})
				};
			},
			cache: true,
			
		}
		
	}).on('select2-selected', function (data) {
			 repName = data.choice.text;
			 repID = data.choice.text; //NEEDS ID?!
			 $("#repGrid").show();
			 $("#replaceIng #ingTargInfo").html('<strong>'+data.choice.text+'</strong><p><strong>CAS:</strong> ' + data.choice.cas + '</p><p> <strong>Description: </strong>' + data.choice.desc +'</p>');
	});
});



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
			if(data.success){
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				$('#mrgIng').modal('hide'); 
				reload_formula_data();
				$('#msgInfo').html(msg);
			}else{
				var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
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
		width: '250px',
		placeholder: 'Search for ingredient (name)',
		allowClear: true,
		dropdownAutoWidth: true,
		containerCssClass: "mrgIngName",
		ajax: {
			url: '/core/full_formula_data.php',
			dataType: 'json',
			type: 'POST',
			delay: 100,
			quietMillis: 250,
			data: function (data) {
				return {
					id: myID,
					search: data
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
		
	}).on('select2-selected', function (data) {
			 mrgName = data.choice.text;
			 mrgID = data.choice.ingId;
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
				msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				$('#manage-quantity').modal('hide'); 
				reload_formula_data();
				
			}else{
				msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
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
		width: '250px',
		placeholder: 'Available solvents in formula',
		allowClear: true,
		dropdownAutoWidth: true,
		containerCssClass: "formulaSolvents",
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
	maintitle: myFNAME,
  });
});

$("#slvMeta").hide();

$("#reCalc").click(function() {
    if($(this).is(":checked")) {
        $("#slvMeta").show();
    } else {
        $("#slvMeta").hide();
    }
});

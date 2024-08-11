//MAKE FORMULA js HELPERS
$(document).ready(function() {


$('#tdDataPending').on('click', '[data-bs-target*=confirm_add]', function () {
    var ingID = $(this).data('ing-id');
    var ingredient = $(this).data('ingredient');
    var rowID = $(this).data('row-id');
    var quantity = $(this).data('quantity');
    var qr = $(this).data('qr');
    
    // Clear previous error messages and reset input fields
    $('#errMsg').html('');
    $("#ingAdded").text(ingredient);
    $("#ingID").text(ingID);
    $("#idRow").text(rowID);
    $("#amountAdded").val(quantity);
    $("#qr").text(qr);
    $("#updateStock").prop("checked", true);
    $('#supplier').prop('disabled', false);
    $("#notes").val('');
    $("#collapseAdvanced").removeClass('show');
    
    $('#msgReplace').html('');
    $("#replacement").val('');
 
    
    // Initialize the replacement select2 component
    $("#replacement").select2({
        width: '100%',
        placeholder: 'Search for ingredient (name)..',
        allowClear: true,
        dropdownAutoWidth: true,
        containerCssClass: "replacement",
        dropdownParent: $('#confirm_add .modal-content'),
        templateResult: formatIngredients,
        templateSelection: formatIngredientsSelection,
        ajax: {
            url: '/pages/views/ingredients/getIngInfo.php',
            dataType: 'json',
            type: 'GET',
            delay: 100,
            quietMillis: 250,
            data: function (params) {
                return {
                    ingID: ingID,
                    replacementsOnly: true,
                    search: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data.data, function(obj) {
                        return {
                            id: obj.id,
                            stock: obj.stock,
                            name: obj.name
                        };
                    })
                };
            },
            cache: false,
        }
    }).on('select2:selecting', function (e) {
        repName = e.params.args.data.name;
        repID = e.params.args.data.id;
    });
    
    // Initialize the supplier select2 component
    $("#supplier").select2({
        width: '100%',
        placeholder: 'Search for supplier (name)',
        allowClear: true,
        dropdownAutoWidth: true,
        containerCssClass: "supplier",
        templateResult: formatSuppliers,
        templateSelection: formatSuppliersSelection,
        dropdownParent: $('#confirm_add .modal-content'),
        minimumResultsForSearch: -1,
        ajax: {
            url: '/core/list_ing_suppliers_data.php',
            dataType: 'json',
            type: 'GET',
            delay: 100,
            quietMillis: 250,
            data: function (params) {
                return {
                    id: ingID
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data.data, function(obj) {
                        return {
                            id: obj.ingSupplierID,
                            name: obj.supplierName
                        };
                    })
                };
            },
            cache: true,
        }
    }).on('select2:selecting', function (e) {
        suppName = e.params.args.data.name;
        suppID = e.params.args.data.id;
    });
});




function formatIngredients (ingredientData) {
	if (ingredientData.loading) {
		return ingredientData.name;
	}
 
	if (!ingredientData.name){
		return 'No replacement found...';
	}
	
	
	var $container = $(
		"<div class='select_result_igredient clearfix'>" +
		  "<div class='select_result_igredient_meta'>" +
			"<div class='select_igredient_title'></div>" +
			"<span id='stock'></span></div>"+
		  "</div>" +
		"</div>"
	);
	
	$container.find(".select_igredient_title").text(ingredientData.name);
	if(ingredientData.stock  > 0){
		$container.find("#stock").text('In stock ('+ingredientData.stock+')');
		$container.find("#stock").attr("class", "stock badge badge-instock");
	}else{
		$container.find("#stock").text('Not in stock ('+ingredientData.stock+')');
		$container.find("#stock").attr("class", "stock badge badge-nostock");
	}

	return $container;
}


function formatIngredientsSelection (ingredientData) {
	return ingredientData.name;
}
	
function formatSuppliers (supplierData) {
	if (supplierData.loading) {
		return supplierData.name;
	}

	if (!supplierData.name){
		return 'No supplier found...';
	}
	
	
	var $container = $(
		"<div class='select_result_supplier clearfix'>" +
			"<div class='select_supplier_name'></div>" +
		"</div>"
	);
	
	  $container.find(".select_supplier_name").text(supplierData.name);
	  
	  return $container;
}


function formatSuppliersSelection (supplierData) {
	return supplierData.name;
}

	//UPDATE AMOUNT
	$('#addedToFormula').click(function() {
		$.ajax({ 
		url: '/pages/manageFormula.php', 
		type: 'POST',
		data: {
			action: "makeFormula",
			q: $("#amountAdded").val(),
			notes: $("#notes").val(),
			qr: $("#qr").text(),
			updateStock: $("#updateStock").is(':checked'),
			supplier: $("#supplier").val(),
			ing: $("#ingAdded").text(),
			id: $("#idRow").text(),
			repName: repName,
			repID: repID,
			ingId: $("#ingID").text(),
			fid: fid,
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
				$('.toast-header').removeClass().addClass('toast-header alert-success');
				$('#confirm_add').modal('toggle');
				reload_data();
				$('.toast').toast('show');
			} else if(data.error) {
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				$('#errMsg').html(msg);
			}
		}
	  });
	});
	
	
		$('#markComplete, #markCompleteMenu').click(function() {
		   bootbox.dialog({
		   title: "Confirm formula completion",
		   message : "Mark formula <strong> <?php echo $meta['name'];?></strong> as complete?",
		   buttons :{
			  main: {
			  label : "Mark as complete",
			  className : "btn-warning",
			 callback: function (){
			 $.ajax({ 
				url: '/pages/manageFormula.php', 
					type: 'POST',
					data: {
						action: "todo",
						markComplete: 1,
						totalQuantity: total_quantity,
						fid: fid
					},
					dataType: 'json',
					success: function (data) {
						if(data.success) {
							var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
							reload_data();
						} else {
							var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
						}	
						$('#msg').html(msg);
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
	
	//SKIP ADD
	$('#skippedFromFormula').click(function() {
		$.ajax({ 
		url: '/pages/manageFormula.php', 
		type: 'POST',
		data: {
			action: "skipMaterial",
			notes: $("#skip_notes").val(),
			ing: $("#ingSkipped").text(),
			id: $("#idRow").text(),
			ingId: $("#ingID").text(),
			fid: fid,
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
				$('.toast-header').removeClass().addClass('toast-header alert-success');
				$('#confirm_skip').modal('toggle');
				reload_data();
				$('.toast').toast('show');
			} else if(data.error) {
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				$('#errMsg').html(msg);
			}
		}
	  });
	});
	
	//ADD TO CART
	$('#tdDataPending').on('click', '[id*=addToCart]', function () {
		$.ajax({ 
			url: '/pages/manageFormula.php', 
			type: 'POST',
			data: {
				action: "addToCart",
				material: $(this).attr('data-ingredient'),
				purity: $(this).attr('data-concentration'),
				quantity: $(this).attr('data-quantity'),
				ingID: $(this).attr('data-ingID')
			},
			dataType: 'json',
			success: function (data) {
				if(data.success){
					$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
					$('.toast-header').removeClass().addClass('toast-header alert-success');
					reload_data();
				}else if(data.error){
					$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
					$('.toast-header').removeClass().addClass('toast-header alert-danger');
				}
				$('.toast').toast('show');
			}
	  	});
	});
	
	$('#tdDataPending').on('click', '[id*=ingInfo]', function () {
		var id = $(this).data('id');
		var name = $(this).data('name');
		
		$('.modal-title').html(name);   
		$('.modal-body-info').html('loading');
		
		$.ajax({
		   type: 'GET',
		   url: '/pages/views/ingredients/getIngInfo.php',
		   data:{
			   ingID: id
			},
		   success: function(data) {
			 $('.modal-body-info').html(data);
		   },
		   error:function(err){
			data = '<div class="alert alert-danger">Unable to get ingredient info</div>';
			$('.modal-body-info').html(data);
		   }
		})
	 });
	
	
	$('#title').click(function() {
		$('#msg').html('');
	});
	
	$('#print').click(() => {
		$('#tdDataPending').DataTable().button(0).trigger();
	});
	
	
	$('#tdDataPending').on('click', '[data-bs-target*=confirm_skip]', function () {
		$('#errMsg').html('');																
		$("#ingSkipped").text($(this).attr('data-ingredient'));
		$("#ingID").text($(this).attr('data-ing-id'));
		$("#idRow").text($(this).attr('data-row-id'));
		$("#notes").val('');
	});
	
	function reload_data() {
		$('#tdDataPending').DataTable().ajax.reload(null, true);
	}
	
	
	$('.export_as').click(function() {	
	  var format = $(this).attr('data-format');
	  $("#tdDataPending").tableHTMLExport({
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
	
	
});
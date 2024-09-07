$(function () {
 
  $('#supplierContinue, #product_tab').click(function (e) {
    e.preventDefault();
	if( $('#address').val() === "" ||  $('#po').val() === "" ||  $('#country').val() === "" ||  $('#telephone').val() === "" ||  $('#email').val() === "" ||  $('#url').val() === ""){
		$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>All fields required');
		$('.toast-header').removeClass().addClass('toast-header alert-danger');
		$('.toast').toast('show');
		return;
	}
	$('.toast').toast('hide');
    $('#SDSTabs a[href="#productPanel"]').tab('show');
  });

  $('#productContinue, #cmps_tab').click(function (e) {
    e.preventDefault();
	if( $('#prodName').val() === "" ||  $('#prodUse').val() === "" ||  $('#sdsCountry').val() === "" ||  $('#sdsLang').val() === "" ){
		$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>All fields required');
		$('.toast-header').removeClass().addClass('toast-header alert-danger');
		$('.toast').toast('show');
		return;
	}
	$('.toast').toast('hide');
    $('#SDSTabs a[href="#tech_composition"]').tab('show');
  });

  $('#compoContinue, #safety_tab').click(function (e) {
    e.preventDefault();
	if( $('#prodName').val() === "" ||  $('#prodUse').val() === "" ||  $('#sdsCountry').val() === "" ||  $('#sdsLang').val() === "" ){
		$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>All fields required');
		$('.toast-header').removeClass().addClass('toast-header alert-danger');
		$('.toast').toast('show');
		return;
	}
	$('.toast').toast('hide');
    $('#SDSTabs a[href="#safety_info"]').tab('show');
  });

  $('#ghsContinue, #gen_tab').click(function (e) {
    e.preventDefault();
	if( $('#prodName').val() === "" ||  $('#prodUse').val() === "" ||  $('#sdsCountry').val() === "" ||  $('#sdsLang').val() === "" ){
		$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>All fields required');
		$('.toast-header').removeClass().addClass('toast-header alert-danger');
		$('.toast').toast('show');
		return;
	}
	$('.toast').toast('hide');
    $('#SDSTabs a[href="#reviewPanel"]').tab('show');
  })

  

})


function fetch_safety(){
	$.ajax({ 
		url: '/pages/views/ingredients/safetyData.php', 
		type: 'GET',
		data: {
			ingID: ingID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_safety').html(data);
		},
	});
};

function fetch_cmps(){
	$.ajax({ 
		url: '/pages/views/ingredients/compos.php', 
		type: 'GET',
		data: {
			name:  btoa(prodName),
			id: ingID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_composition').html(data);
		},
	});
}

$(function () {
 

  $('#supplierContinue').click(function (e) {
    e.preventDefault();
	if( $('#address').val() === "" ||  $('#po').val() === "" ||  $('#country').val() === "" ||  $('#telephone').val() === "" ||  $('#email').val() === "" ||  $('#url').val() === ""){
		$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>All fields required');
		$('.toast-header').removeClass().addClass('toast-header alert-danger');
		$('.toast').toast('show');
		return;
	}
	$('.toast').toast('hide');
    $('.progress-bar').css('width', '40%');
    $('.progress-bar').html('Step 2 of 5');
    $('#SDSTabs a[href="#productPanel"]').tab('show');
  });

  $('#productContinue').click(function (e) {
    e.preventDefault();
		if( $('#prodName').val() === "" ||  $('#prodUse').val() === "" ||  $('#sdsCountry').val() === "" ||  $('#sdsLang').val() === "" ){
		$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>All fields required');
		$('.toast-header').removeClass().addClass('toast-header alert-danger');
		$('.toast').toast('show');
		return;
	}
	$('.toast').toast('hide');
    $('.progress-bar').css('width', '60%');
    $('.progress-bar').html('Step 3 of 5');
    $('#SDSTabs a[href="#tech_composition"]').tab('show');
  });

  $('#compoContinue').click(function (e) {
    e.preventDefault();
    $('.progress-bar').css('width', '80%');
    $('.progress-bar').html('Step 4 of 5');
    $('#SDSTabs a[href="#safety_info"]').tab('show');
  });

  $('#ghsContinue').click(function (e) {
    e.preventDefault();
    $('.progress-bar').css('width', '100%');
    $('.progress-bar').html('Step 5 of 5');
    $('#SDSTabs a[href="#reviewPanel"]').tab('show');
  })

  

})


function fetch_safety(){
	$.ajax({ 
		url: '/pages/views/ingredients/safetyData.php', 
		type: 'POST',
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

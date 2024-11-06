$('#dbUpBtn').click(function() {

	$('#dbUpdMsg').html('<div class="alert alert-info"><img src="/img/loading.gif"/><strong> DB schema upgrade in progress. Please wait, this may take a while...</strong></div>');
	$('#dbUpBtn').hide();
	$('#dbBkBtn').hide();
	$('#dbUpOk').hide();
	$.ajax({ 
		url: '/core/core.php', 
		type: 'GET',
		data: {
			'do': "db_update"
		},
		dataType: 'json',
		success: function (data) {
			if(data.success) {
				var msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.success + '</div>';
				$('#dbUpOk').show();
			} else {
				var msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
				$('#dbUpBtn').show();
				$('#dbBkBtn').show();
				$('#dbUpOk').hide();
			}
			$('#dbUpdMsg').html(msg);
		},
					
		error: function (request, status, error) {
			$('#dbUpdMsg').html( '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + error + '</div>');
		},
  	});
	
});

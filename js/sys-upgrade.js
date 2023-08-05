$('#dbUpBtn').click(function() {

	$('#dbUpdMsg').html('<div class="alert alert-info"><img src="/img/loading.gif"/><strong> DB schema upgrade in progress. Please wait, this may take a while...</strong></div>');
	$('#dbUpBtn').hide();
	$('#dbBkBtn').hide();
	$('#dbUpOk').hide();
	$.ajax({ 
		url: '/pages/operations.php', 
		type: 'GET',
		data: {
			'do': "db_update"
			},
		dataType: 'json',
		success: function (data) {
			if(data.success) {
				var msg = '<div class="alert alert-success">' + data.success + '</div>';
				$('#dbUpOk').show();
			} else {
				var msg = '<div class="alert alert-danger">' + data.error + '</div>';
				$('#dbUpBtn').show();
				$('#dbBkBtn').show();
				$('#dbUpOk').hide();
			}
			$('#dbUpdMsg').html(msg);
		},
					
		error: function (request, status, error) {
			$('#dbUpdMsg').html( '<div class="alert alert-danger">' + error + '</div>');
		},
  	});
	
});
//
//PV SCALE JS
//

$(document).ready(function() {
	var msg = "";
	pvScaleConnVal();
	
	$('#chkConn').click(function(event) {
	  pvScaleConnVal();
	});


	$('#chkFirm').click(function() {
		$('#scmsg').html('<div class="alert alert-info"><i class="spinner-border spinner-border-sm mx-2"></i>Please wait...</div>');

		$.ajax({
			url: '/pages/views/pvscale/manage.php',
			type: 'GET',
			data: {
				action: 'firmwareCheck'
			},
			dataType: 'json',
			success: function(data) {
				if (data.success === true) {
					if(data.response.isUpgradable) {
						msg = '<div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i>Upgrade available (' + data.response.ver + '). <a href="#" id="updFirm">Upgrade now</a></div>';
					} else {
					msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.response.message + ' (' + data.response.ver + ')</div>';
					}
				} else {
					msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Unable to get data</div>';
				}
				$('#scmsg').html(msg);
				$('#updFirm').click(function(e) {
					e.preventDefault();
					$('#scmsg').html('<div class="alert alert-info"><i class="spinner-border spinner-border-sm mx-2"></i>Please wait...</div>');
			
					$.ajax({
						url: '/pages/views/pvscale/manage.php',
						type: 'GET',
						data: {
							action: 'firmwareUpdate'
						},
						dataType: 'json',
						success: function(data) {
							if (data.success === true) {
								if(data.response.isUpgradable) {
									msg = '<div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i>Upgrade available (' + data.response.ver + '). <a href="#" id="updFirm">Upgrade now</a></div>';
								} else {
								msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.response + ' (' + data.response.ver + ')</div>';
								}
							} else {
								msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.response + '</div>';
							}
							$('#scmsg').html(msg);
						}
					});
						
				});
			}
		});
			
	});
	


	
	$('#subScale').click(function() {
		pvScaleConnVal(function(success) {
			if (success == true) {
	
				var pv_scale_enabled = $('#pv_scale_enabled').is(':checked') ? '1' : '0';
			} else {
				var pv_scale_enabled = '0';
			}
				$.ajax({
					url: '/pages/views/pvscale/manage.php',
					type: 'POST',
					data: {
						action: 'update',
						enabled: pv_scale_enabled,
						pv_scale_host: $("#pv_scale_host").val()
					},
					dataType: 'json',
					success: function(data) {
						if (data.success) {
							msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
						} else {
							msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
						}
						$('#scmsg').html(msg);
					}
				});
			//}
			
		});
		
	});
	
	function pvScaleConnVal(callback) {
    //event.preventDefault(); // Prevent form submission
    var success = false;

    $('#scmsg').html('<div class="alert alert-info"><i class="spinner-border spinner-border-sm mx-2"></i>Trying to connect...</div>');

    $('#chkConn').addClass('d-none');
    $('#connSpinner').removeClass('d-none');

    $.ajax({
        url: '/pages/views/pvscale/manage.php',
        type: 'POST',
        data: {
            ping: 1,
            pv_scale_host: $("#pv_scale_host").val()
        },
        dataType: 'json',
        success: function(data) {
            if (data.success === true) {
                var sysData = data.sysData;
                $('#sysData').html(
                    '<p>MAC: ' + sysData.mac + '</p>' +
                    '<p>SSID: ' + sysData.ssid + '</p>' +
                    '<p>IP: ' + sysData.ip + '</p>' +
                    '<p>Calibration Factor: ' + sysData.calibration_factor + '</p>' +
                    '<p>PV Scale Version: ' + sysData.pvScaleVersion + '</p>'
                );
             //   $('#scmsg').html('<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.msg + '</div>');
			 	$('#scmsg').html('');
                success = true;
            } else {
				$('#sysData').html('');
                $('#scmsg').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Connection failed</div>');
            }
        },
        error: function() {
			$('#sysData').html('');
            $('#scmsg').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Network error</div>');
        },
        complete: function() {
            $('#chkConn').removeClass('d-none');
            $('#connSpinner').addClass('d-none');
            if (typeof callback === 'function') {
                callback(success);
            }
        }
    });
}

});

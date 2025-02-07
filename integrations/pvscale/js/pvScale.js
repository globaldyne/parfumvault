//
//PV SCALE JS
//


$(document).ready(function () {
	var msg = "";
	pvScaleConnVal();

	$('#chkConn').click(function (event) {
		pvScaleConnVal();
	});

	$('#scaleScreenOn').click(function (event) {
		$('#scmsg').html('<div class="alert alert-info"><i class="spinner-border spinner-border-sm mx-2"></i>Turning screen on...</div>');
		$.ajax({
			url: '/integrations/pvscale/manage.php',
			type: 'GET',
			data: {
				action: 'screen',
				status: 1
			},
			dataType: 'json',
			success: function (data) {
				if (data.success) {
					msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>Screen on</div>';
				} else {
					msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
				}
				$('#scmsg').html(msg);
			},
			error: function () {
				$('#scmsg').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Network error</div>');
			}
		});
	});

	$('#scaleScreenOff').click(function (event) {
		$('#scmsg').html('<div class="alert alert-info"><i class="spinner-border spinner-border-sm mx-2"></i>Turning screen off...</div>');
		$.ajax({
			url: '/integrations/pvscale/manage.php',
			type: 'GET',
			data: {
				action: 'screen',
				status: 0
			},
			dataType: 'json',
			success: function (data) {
				if (data.success) {
					msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>Screen off</div>';
				} else {
					msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
				}
				$('#scmsg').html(msg);
			},
			error: function () {
				$('#scmsg').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Network error</div>');
			}
		});
	});

	$('#chkFirm').click(function () {
		$('#scmsg').html('<div class="alert alert-info"><i class="spinner-border spinner-border-sm mx-2"></i>Please wait...</div>');

		$.ajax({
			url: '/integrations/pvscale/manage.php',
			type: 'GET',
			data: {
				action: 'firmwareCheck'
			},
			dataType: 'json',
			success: function (data) {
				if (data.success === true) {
					if (data.response.isUpgradable) {
						msg = '<div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i>Upgrade available (' + data.response.ver + '). <a href="#" id="updFirm">Upgrade now</a></div>';
					} else {
						msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.response.message + ' (' + data.response.ver + ')</div>';
					}
				} else {
					msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Unable to get data</div>';
				}
				$('#scmsg').html(msg);
				$('#updFirm').click(function (e) {
					e.preventDefault();
					$('#scmsg').html('<div class="alert alert-info"><i class="spinner-border spinner-border-sm mx-2"></i>Please wait...</div>');

					$.ajax({
						url: '/integrations/pvscale/manage.php',
						type: 'GET',
						data: {
							action: 'firmwareUpdate'
						},
						dataType: 'json',
						success: function (data) {
							if (data.success === true) {
								if (data.response.isUpgradable) {
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



	$('#subScale').click(function () {
		pvScaleConnVal(function (success) {
			var pv_scale_enabled = success ? ($('#pv_scale_enabled').is(':checked') ? '1' : '0') : '0';

			$.ajax({
				url: '/integrations/pvscale/manage.php',
				type: 'POST',
				data: {
					action: 'update',
					pv_scale_enabled: pv_scale_enabled,
					pv_scale_host: $("#pv_scale_host").val()
				},
				dataType: 'json',
				success: function (data) {
					if (data.success) {
						msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
					} else {
						msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
						console.error('Error:', data.error);
					}
					$('#scmsg').html(msg);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Network error</div>';
					$('#scmsg').html(msg);
					console.error('AJAX error:', textStatus, errorThrown);
				}
			});
		});
	});

	/**
	 * Attempts to connect to the PV Scale and updates the UI based on the connection status.
	 * 
	 * @param {function(boolean): void} callback - A callback function that is called after the connection attempt is complete. 
	 * The callback receives a boolean indicating whether the connection was successful.
	 */
	function pvScaleConnVal(callback) {
		var success = false;

		$('#scmsg').html('<div class="alert alert-info"><i class="spinner-border spinner-border-sm mx-2"></i>Trying to connect...</div>');
		$('#chkConn').addClass('d-none');
		$('#connSpinner').removeClass('d-none');
		$('#controlScale').addClass('d-none');

		$.ajax({
			url: '/integrations/pvscale/manage.php',
			type: 'POST',
			data: {
				ping: 1,
				pv_scale_host: $("#pv_scale_host").val()
			},
			dataType: 'json',
			success: function (data) {
				if (data.success) {
					var sysData = data.sysData;
					$('#sysData').html(
						'<p>MAC: ' + sysData.mac + '</p>' +
						'<p>SSID: ' + sysData.ssid + '</p>' +
						'<p>IP: ' + sysData.ip + '</p>' +
						'<p>Calibration Factor: ' + sysData.calibration_factor + '</p>' +
						'<p>PV Scale Version: ' + sysData.pvScaleVersion + '</p>'
					);
					$('#controlScale').removeClass('d-none');
					$('#scmsg').html('');
					success = true;

					$.ajax({
						url: '/integrations/pvscale/manage.php',
						type: 'GET',
						data: {
							action: 'completeSetup',
						},
						dataType: 'json'
					});
				} else {
					$('#sysData').html('');
					$('#controlScale').addClass('d-none');
					$('#scmsg').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Connection failed</div>');
				}
			},
			error: function () {
				$('#sysData').html('');
				$('#scmsg').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Network error</div>');
			},
			complete: function () {
				$('#chkConn').removeClass('d-none');
				$('#connSpinner').addClass('d-none');
				if (typeof callback === 'function') {
					callback(success);
				}
			}
		});
	}

});

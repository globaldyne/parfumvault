<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


?>
<div class="card-body">
  <div id="scmsg"></div>
  <div class="row g-2">
 
    <div class="col-sm">
      <div class="mb-3">
        <label for="pv_scale_host" class="form-label">Scale IP</label>
        <input name="pv_scale_host" type="pv_scale_host" class="form-control" id="pv_scale_host" value="<?=$settings['pv_scale_host']?>">
      </div> 
      <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="pv_scale_enabled" name="pv_scale_enabled" <?php if ($settings['pv_scale_enabled'] == '1') echo 'checked'; ?>>
        <label class="form-check-label" for="pv_scale_enabled">Enabled</label>
      </div>
      <div id="sysData"></div>
    </div>
    
    <div class="col-sm">
   
		<div class="d-grid gap-2 col-6 mx-auto">
            <input type="submit" name="btnCal" class="btn btn-warning" id="chkConn" value="Calibrate">
            <input type="submit" name="btnFirm" class="btn btn-info" id="chkFirm" value="Firmware update">
      	</div>
      
    </div>
  </div>
  <div class="dropdown-divider"></div>
  <div class="modal-footer">
    <input type="submit" name="chkConn" class="btn btn-warning" id="chkConn" value="Validate connection">
    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="connSpinner"></span>
    <input type="submit" name="subScale" class="btn btn-primary" id="subScale" value="Save changes">
  </div>
</div>


<script>
$(document).ready(function() {
  var msg = "";
  pvScaleConnVal();
  $('#chkConn').click(function(event) {
	  pvScaleConnVal();
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
</script>

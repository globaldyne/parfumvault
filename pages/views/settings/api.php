<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

?>

<h6>API can be used to access PV Pro from other apps like PV Mobile APP</h6>
<hr />

<div class="row mb-3">
    <div class="col-2">
        <label for="pv_api">Enable API</label>
        <input class="mx-2" name="pv_api" type="checkbox" id="pv_api" value="1" 
        <?php if($settings['api'] == '1'){ ?> checked="checked" <?php } ?>/>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4">
        <label for="pv_api_key">API Key</label>
        <div class="col-md-8 password-input-container">
        	<input name="pv_api_key" type="password" class="form-control password-input" id="pv_api_key" value="<?=$settings['api_key']?>" />
        	<i class="toggle-password fa fa-eye"></i>
        </div>
    </div>
</div>
<div class="col-2">
    <input type="submit" name="save-api" id="save-api" value="Save" class="btn btn-primary"/>
</div>


<script>
$(document).ready(function() {
	$(".toggle-password").click(function () {
        var passwordInput = $($(this).siblings(".password-input"));
        var icon = $(this);
        if (passwordInput.attr("type") == "password") {
            passwordInput.attr("type", "text");
            icon.removeClass("fa-eye").addClass("fa-eye-slash");
        } else {
            passwordInput.attr("type", "password");
            icon.removeClass("fa-eye-slash").addClass("fa-eye");
        }
    });
	$('#save-api').click(function() {
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				manage: 'api',		
				api: $("#pv_api").is(':checked'),
				api_key: $("#pv_api_key").val(),
			},
			dataType: 'json',
			success: function (data) {
				if(data.success){
					$('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
					$('.toast-header').removeClass().addClass('toast-header alert-success');
				} else if(data.error) {
					$('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
					$('.toast-header').removeClass().addClass('toast-header alert-danger');
				}
				$('.toast').toast('show');
			},
			error: function (xhr, status, error) {
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		});
	});
});

</script>

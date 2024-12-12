<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

?>

<h5>API can be used to access PV Pro from other apps like PV Mobile APP</h5>
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
    <div class="col-6">
        <h6>Available API calls</h6>
        <table id="endpointsTable" class="table table-striped nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Type</th>
                    <th>Syntax</th>
                    <th>Method</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<div class="col-2">
    <input type="submit" name="save-api" id="save-api" value="Save" class="btn btn-primary"/>
</div>


<script>
$(document).ready(function() {
	var	api_key = 'xxxxxx';

	$(".toggle-password").click(function () {
        var passwordInput = $($(this).siblings(".password-input"));
        
		var icon = $(this);
		reload_data();
        if (passwordInput.attr("type") == "password") {
            passwordInput.attr("type", "text");
            icon.removeClass("fa-eye").addClass("fa-eye-slash");
			api_key = $("#pv_api_key").val();
        } else {
            passwordInput.attr("type", "password");
            icon.removeClass("fa-eye-slash").addClass("fa-eye");
			api_key = 'xxxxxx';
        }
    });
	
   $('#endpointsTable').DataTable({
	   	dom: '',
		processing: true,
        language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
			zeroRecords: '<div class="row g-3 mt-1"><div class="alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i><strong>Nothing found</strong></div></div>',
			search: '',
			searchPlaceholder: 'Search by name...',
		},
		ajax: {
			url: '/api.php',
			dataSrc: 'valid_endpoints'
		},
		columns: [
			{ data: 'method', title: 'Method' },
			{ data: 'do', title: 'Action' },
			{ data: 'type', title: 'Type' },
			{ data: '', title: 'Syntax', render: syntax}
		]
	});
	
	function syntax(data, type, row){
		var furl = '/api.php?key=' + api_key + '&do=' + row.do + '&type=' + row.type;
		data = '<a href="' + furl + '" target="_blank" class="text-info-emphasis">' + furl + '<i class="fa-solid fa-arrow-up-right-from-square mx-2"></i></a>';
		return data;
	}
	
	function reload_data() {
		$('#endpointsTable').DataTable().ajax.reload(null, true);
	};
	
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

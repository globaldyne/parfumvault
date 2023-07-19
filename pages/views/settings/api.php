<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

require_once(__ROOT__.'/inc/settings.php');

?>

<h6>API can be used to access PV Pro from other apps like PV Mobile APP</h6>
<div class="dropdown-divider"></div>

	   <table width="100%" border="0">
		<tr>
	      <td colspan="3"><div id="pvAPIMsg"></div></td>
	      </tr>
	    <tr>
	      <td width="9%" height="28">Enable API</td>
	      <td width="9%" valign="middle"><input name="pv_api" type="checkbox" id="pv_api" value="1" <?php if($settings['api'] == '1'){ ?> checked="checked" <?php } ?>/></td>
	      <td width="82%">&nbsp;</td>
	      </tr>
	    <tr>
	      <td>API Key</td>
	      <td valign="middle"><input name="pv_api_key" type="text" class="form-control" id="pv_api_key" value="<?=$settings['api_key']?>" /></td>
	      <td>&nbsp;</td>
	      </tr>
	    <tr>
	      <td>&nbsp;</td>
	      <td valign="middle">&nbsp;</td>
	      <td>&nbsp;</td>
	      </tr>
	    <tr>
	      <td><input type="submit" name="save-api" id="save-api" value="Submit" class="btn btn-info"/></td>
	      <td valign="middle">&nbsp;</td>
	      <td>&nbsp;</td>
	      </tr>
	    </table> 

<script>

$('#save-api').click(function() {
	$.ajax({ 
		url: '/pages/update_settings.php', 
		type: 'POST',
		data: {
			manage: 'api',		
			api: $("#pv_api").is(':checked'),
			api_key: $("#pv_api_key").val(),
		},
		dataType: 'json',
		success: function (data) {
			if(data.success) {
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
			} else {
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
			}
			$('#pvAPIMsg').html(msg);
		}
	});
});

</script>
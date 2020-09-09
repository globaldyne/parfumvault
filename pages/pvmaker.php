<?php 
if (!defined('pvault_panel')){ die('Not Found');}


?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=pvmaker">PV Maker</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <form action="javascript:addAllergen()" method="get" enctype="application/x-www-form-urlencoded" name="form1" id="form1">
                 <table width="100%" border="0">
                  <tr>
                    <td colspan="3">&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="16%">DPG:</td>
                    <td width="9%"><input name="pvm_dpg" type="text" class="form-control" id="pvm_dpg"/></td>
                    <td width="75%">&nbsp;</td>
                  </tr>
                  <tr>
                    <td>Ethanol:</td>
                    <td><input name="pvm_ethanol" type="text" class="form-control" id="pvm_ethanol"/></td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>Water:</td>
                    <td><input name="pvm_water" type="text" class="form-control" id="pvm_water"/></td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>Compound:</td>
                    <td><input name="pvm_compound" type="text" class="form-control" id="pvm_compound"/></td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><input type="submit" name="button" id="button" value="Submit" class="btn btn-info"/></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                </table>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<script>
function addAllergen() {	  
$.ajax({ 
    url: 'pages/pvm.php', 
	type: 'GET',
    data: {
		queue: 'add',
		dpg: $("#pvm_dpg").val(),
		ethanol: $("#pvm_ethanol").val(),
		water: $("#pvm_water").val(),
		compound: $("#pvm_compound").val(),
		},
	dataType: 'html',
    success: function (data) {
		//location.reload();
	  	$('#msg').html(data);
    }
  });
};
</script>
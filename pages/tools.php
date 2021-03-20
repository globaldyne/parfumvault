<?php
//if (!defined('pvault_panel')){ die('Not Found');}
if(is_numeric($_GET['wis']) && is_numeric($_GET['ofp'])){
	
	echo $_GET['wis']/100*$_GET['ofp'];
	exit;
}elseif(is_numeric($_GET['pof']) && is_numeric($_GET['quantity'])){
	
	echo $_GET['pof'] / $_GET['quantity']*100;
	exit;	
}
	
?>
<script>
function calc1() {	  
$("#res").val('Loading...');
$.ajax({ 
    url: 'pages/tools.php', 
	type: 'get',
    data: {
		wis: $("#wis").val(),
		ofp: $("#ofp").val(),
		},
	dataType: 'html',
    success: function (data) {
	  $('#res').html(data);
    }
  });
};


function calc2() {	  
$("#res").val('Loading...');
$.ajax({ 
    url: 'pages/tools.php', 
	type: 'get',
    data: {
		pof: $("#pof").val(),
		quantity: $("#quantity").val()
		},
	dataType: 'html',
    success: function (data) {
	  $('#res').html(data);
    }
  });
};
</script>

<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=tools">Calculation Tools</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
<table width="100%" border="0">
  <tr>
    <td width="29%">What is 
      <input name="wis" type="text" id="wis"> 
      % of 
      <input name="ofp" type="text" id="ofp"></td>
    <td colspan="2"><a href="javascript:calc1()" id="calc1">Calculate</a></td>
    </tr>
  <tr>
    <td colspan="3"><hr></td>
    </tr>
  <tr>
    <td><input name="pof" type="text" id="pof"> is what percent of 
      <input name="quantity" type="text" id="quantity"></td>
    <td colspan="2"><a href="javascript:calc2()" id="calc2">Calculate</a></td>
    </tr>
  <tr>
    <td colspan="3"><hr></td>
    </tr>
  <tr>
    <td><strong><div id="res"></div></strong></td>
    <td width="6%">&nbsp;</td>
    <td width="65%">&nbsp;</td>
  </tr>
</table>


              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

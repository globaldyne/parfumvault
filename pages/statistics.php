<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<script>
$(function() {
  $("#statistics").tabs();
});

function fetch_invWorth(){
$.ajax({ 
    url: '/pages/views/stats/inventoryWorth.php', 
	type: 'GET',
	dataType: 'html',
		success: function (data) {
			$('#invCosts_tab').html(data);
		}
	});
}

function fetch_ifraStats(){
$.ajax({ 
    url: '/pages/views/stats/ifra.php', 
	type: 'GET',
	dataType: 'html',
		success: function (data) {
			$('#ifra_tab').html(data);
		}
	});
}

function fetch_ingUsage(){
$.ajax({ 
    url: '/pages/views/stats/ingUsage.php', 
	type: 'GET',
	dataType: 'html',
		success: function (data) {
			$('#ingUsage_tab').html(data);
		}
	});
}

fetch_ingUsage();
fetch_invWorth();
fetch_ifraStats();

</script>

<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
<style>
#chartIngUsage, #chartIFRA{
  width		: 100%;
  height	: 500px;
  font-size	: 11px;
}
</style>

<script src="js/amcharts_3.21.15.free/amcharts/amcharts.js"></script>
<script src="js/amcharts_3.21.15.free/amcharts/serial.js"></script>
<script src="js/amcharts_3.21.15.free/amcharts/pie.js"></script>
<script src="js/amcharts_3.21.15.free/amcharts/themes/light.js"></script>

<div class="container-fluid">
	<h2 class="m-0 mb-4 text-primary">Statistics</h2>
     <div id="statistics">
         <ul>
             <li><a href="#ingUsage_tab"><span>Ingredients Usage</span></a></li>
             <li><a href="#ifra_tab"><span>IFRA</span></a></li>
             <li><a href="#invCosts_tab">Inventory worth</a></li>
         </ul>
     	 <div id="ingUsage_tab"></div>
     	 <div id="ifra_tab"></div>
     	 <div id="invCosts_tab"></div>
	 </div>
    </div>
    
</div>

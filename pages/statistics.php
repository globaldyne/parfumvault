<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<script>
$(function() {
  $("#statistics").tabs();
});
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

<script>
var chart = AmCharts.makeChart( "chartIngUsage", {
  "type": "serial",
  "theme": "none",
  "dataProvider": [ 
<?php
$ing = mysqli_query($conn, "SELECT DISTINCT ingredient AS name FROM formulas ORDER BY ingredient ASC");
while($allIng =  mysqli_fetch_array($ing)){
?>
{
    "ingredient": "<?php echo $allIng['name'];?>",
    "usage": "<?php getIngUsage($allIng['name'],$conn); ?>"
}, 
<?php } ?>				   

  ],
  "valueAxes": [ {
    "gridColor": "#FFFFFF",
    "axisAlpha": 0,
    "position": "left",
    "title": "Ingredient usage"

  } ],
  "gridAboveGraphs": true,
  "startDuration": 1,
  "graphs": [ {
    "balloonText": "[[category]]: <b>[[value]]</b>",
    "fillAlphas": 0.8,
    "lineAlpha": 0.2,
    "type": "column",
    "valueField": "usage"
  } ],
  "chartCursor": {
    "categoryBalloonEnabled": false,
    "cursorAlpha": 0,
    "zoomable": true
  },
  "categoryField": "ingredient",
  "categoryAxis": {
    "gridPosition": "start",
    "gridAlpha": 0,
    "tickPosition": "start",
    "tickLength": 20,
	"labelRotation": 45
  },
  "export": {
    "enabled": false
  }

} );


var chart = AmCharts.makeChart("chartIFRA",{
  "type"    : "pie",
  "titleField"  : "type",
  "valueField"  : "value",
  "dataProvider"  : [
<?php
$ifratypes = mysqli_query($conn, "SELECT DISTINCT type FROM IFRALibrary");
while($types =  mysqli_fetch_array($ifratypes)){
?>    {
      "type": "<?php echo $types['type'];?>",
      "value": "<?php getIFRAtypes($types['type'],$conn);?>"
    },
<?php } ?>
  ],
});
</script>
<div class="container-fluid">

<h2 class="m-0 mb-4 text-primary">Statistics</h2>

     <div id="statistics">
     <ul>
         <li><a href="#ingUsage"><span>Ingredients Usage</span></a></li>
         <li><a href="#IFRA"><span>IFRA</span></a></li>
     </ul>
     <div id="ingUsage">
	 <?php 
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulas"))== 0){
		echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> to generate statistics, add at least one formula first.</div>';
	}else{
	?>
	<div id="chartIngUsage"></div>
	<?php } ?>
	</div>
     <div id="IFRA">
    <?php 
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM IFRALibrary"))== 0){
		echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> You need to <a href="pages/maintenance.php?do=IFRA" class="popup-link">import</a> the IFRA xls first.</div>';
	}else{
	?>
     <div id="chartIFRA"></div></div>
     <?php } ?>
    </div>



</div>
</div>

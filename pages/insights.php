<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
<style>
#chartdiv {
  width		: 100%;
  height	: 500px;
  font-size	: 11px;
}						
</style>
<script src="../js/amcharts_3.21.15.free/amcharts/amcharts.js"></script>
<script src="../js/amcharts_3.21.15.free/amcharts/serial.js"></script>
<script src="../js/amcharts_3.21.15.free/amcharts/themes/light.js"></script>

<script>
var chart = AmCharts.makeChart( "chartdiv", {
  "type": "serial",
  "theme": "none",
  "dataProvider": [ 
<?php
$ing = mysqli_query($conn, "SELECT DISTINCT ingredient AS name FROM formulas ORDER BY ingredient ASC");
while($allIng =  mysqli_fetch_array($ing)){
?>
{
    "ingredient": "<?php echo $allIng['name'];?>",
    "usage": "<?php getIngUsage($allIng['name'],$dbhost,$dbuser,$dbpass,$dbname); ?>"
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
</script>

<div class="container-fluid">

<h2 class="m-0 mb-4 text-primary">Insights</h2>
<div id="insights">
<div id="general">
<?php 
if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulas"))== 0){
	echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> to generate insights, add at least one formula first.</div>';
}else{
?>
<div id="chartdiv"></div>					
<?php } ?>
</div>
</div>
</div>


</div>
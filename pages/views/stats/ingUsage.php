<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/getIngUsage.php');


if(!mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulas"))){
	echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> to generate statistics, add at least one formula first.</div>';
	return;
}
?>
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
  "valueAxes": [{
    "gridColor": "#FFFFFF",
    "axisAlpha": 0,
    "position": "left",
    "title": "Ingredient usage"

  }],
  "gridAboveGraphs": true,
  "startDuration": 1,
  "graphs": [{
    "balloonText": "[[category]]: <b>[[value]]</b>",
    "fillAlphas": 0.8,
    "lineAlpha": 0.2,
    "type": "column",
    "valueField": "usage"
  }],
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

});
</script>
<div id="chartIngUsage"></div>

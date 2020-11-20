<?php

require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../func/getIngUsage.php');

$fid = mysqli_real_escape_string($conn, $_GET['id']);
?>
<style>
#chartImpact {
  width		: 100%;
  height	: 500px;
  font-size	: 11px;
}
</style>

<script src="../js/amcharts_3.21.15.free/amcharts/amcharts.js"></script>
<script src="../js/amcharts_3.21.15.free/amcharts/serial.js"></script>
<script src="../js/amcharts_3.21.15.free/amcharts/themes/light.js"></script>

<script src="../js/amcharts_3.21.15.free/amcharts/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="../js/amcharts_3.21.15.free/amcharts/plugins/export/export.css" type="text/css" media="all" /> 
<script>
function replaceBulk( str, findArray, replaceArray ){
  var i, regex = [], map = {}; 
  for( i=0; i<findArray.length; i++ ){ 
    regex.push( findArray[i].replace(/([-[\]{}()*+?.\\^$|#,])/g,'\\$1') );
    map[findArray[i]] = replaceArray[i]; 
  }
  regex = regex.join('|');
  str = str.replace( new RegExp( regex, 'g' ), function(matched){
    return map[matched];
  });
  return str;
}

//console.log( replaceBulk("10", ['10','50','100'],['low','medium','high']) );

var chart = AmCharts.makeChart( "chartImpact", {
  "type": "serial",
  "theme": "none",
  "dataProvider": [ 
<?php
$ing = mysqli_query($conn, "SELECT ingredient AS name FROM formulas WHERE fid = '$fid' ORDER BY ingredient ASC");
while($allIng =  mysqli_fetch_array($ing)){
?>
{
    "ingredient": "<?=$allIng['name'];?>",
    "impact_top": "<?=getNoteImpact($allIng['name'],'top',$conn)['int']; ?>",
    "impact_heart": "<?=getNoteImpact($allIng['name'],'heart',$conn)['int']; ?>",
    "impact_base": "<?=getNoteImpact($allIng['name'],'base',$conn)['int']; ?>",
},
<?php } ?>				   

  ],
  "valueAxes": [ {
    "gridColor": "#FFFFFF",
    "axisAlpha": 0,
    "position": "left",
    "title": "Notes Impact"

  } ],
  "gridAboveGraphs": true,
  "startDuration": 1,
  "graphs": [ {
    "balloonText": "Top note impact",
    "fillAlphas": 0.8,
    "lineAlpha": 0.2,
    "type": "column",
    "valueField": "impact_top"
  },
  {
    "balloonText": "Heart note impact",
    "fillAlphas": 0.8,
    "lineAlpha": 0.2,
    "type": "column",
    "valueField": "impact_heart"
  },
  {
    "balloonText": "Base note impact",
    "fillAlphas": 0.8,
    "lineAlpha": 0.2,
    "type": "column",
    "valueField": "impact_base"
  }],
  
  "chartCursor": {
    "categoryBalloonEnabled": true,
    "cursorAlpha": 0,
    "zoomable": false
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
    "enabled": true
  },
  "legend": {
    "useGraphSettings": true,
	//"valueText": "[[description]]"
  },

} );

</script>

<div id="chartImpact"></div>

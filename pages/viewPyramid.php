<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

require_once(__ROOT__.'/func/calcPerc.php');
require_once(__ROOT__.'/func/getFormula.php');
if(!$_GET['formula']){
	echo 'No valid formula provided';
	return;
}
$formula = mysqli_real_escape_string($conn, $_GET['formula']);
$fid = mysqli_real_escape_string($conn, $_GET['fid']);
if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulas WHERE fid = '$fid'")))){
	echo '<div class="alert alert-info alert-dismissible">Incomplete formula.</div>';
	return;
}
?>

  
<!-- Styles -->
<style>
#chartdiv {
  width: 100%;
  height: 1000px;
  font-size: 11px;
}						
</style>
<script src="../js/amcharts_3.21.15.free/amcharts/amcharts.js"></script>
<script src="../js/amcharts_3.21.15.free/amcharts/funnel.js"></script>
<script src="../js/amcharts_3.21.15.free/amcharts/themes/light.js"></script>

<script src="../js/amcharts_3.21.15.free/amcharts/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="../js/amcharts_3.21.15.free/amcharts/plugins/export/export.css" type="text/css" media="all" />  
<script>
var chart = AmCharts.makeChart( "chartdiv", {
  "type": "funnel",
  "theme": "light",
  "dataProvider": [ {
    "title": "Base notes",
    "value": <?php echo calcPerc($formula, 'Base', $settings['base_n'], $conn);?>,
	"desc": "%",
	"notes": "<?php getFormula($_GET['formula'],'Base',$conn);?>"
  }, 
  {
    "title": "Heart Notes",
    "value": <?php echo calcPerc($formula, 'Heart', $settings['heart_n'], $conn);?>,
	"desc": "%",
	"notes": "<?php getFormula($_GET['formula'],'Heart',$conn);?>"
  }, 
  {
    "title": "Top Notes",
    "value": <?php echo calcPerc($formula, 'Top', $settings['top_n'], $conn);?>,
	"desc": "%",
	"notes": "<?php getFormula($_GET['formula'],'Top',$conn);?>"
  }
  ],
  "balloon": {
    "fixedPosition": true
  },
  "valueField": "value",
  "titleField": "title",
  "marginRight": 240,
  "marginLeft": 50,
  "startX": -500,
  "rotate": true,
  "labelPosition": "right",
  "balloonText": "[[title]]: [[value]][[desc]]",
  "labelText": "[[notes]]",
  "export": {
    "enabled": true
  }
} );
</script>

<div id="wrapper">
	<div id="content-wrapper">
		<div id="chartdiv"></div>
	</div>
</div>

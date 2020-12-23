<?php
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/product.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');

require_once('../func/calcPerc.php');
require_once('../func/getFormula.php');
if(!$_GET['formula']){
	die('No valid formula provided');
}
$formula = mysqli_real_escape_string($conn, $_GET['formula']);
if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulas WHERE name = '$formula'")) == 0){
	echo '<div class="alert alert-info alert-dismissible">Incomplete formula.</div>';
	return;
}
?>

  
<!-- Styles -->
<style>
#chartdiv {
  width		: 100%;
  height		: 500px;
  font-size	: 11px;
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
</head>
<body id="page-top">

  <div id="wrapper">
	<div id="content-wrapper">
		<div id="chartdiv"></div>
	</div>
  </div>

<?php

require_once('../inc/config.php');
require_once('../inc/product.php');
require_once('../inc/opendb.php');
require_once('../func/sizeformat.php');
require_once('../func/calcPerc.php');
require_once('../func/getFormula.php');

$formula = mysqli_real_escape_string($conn, $_GET['formula']);

$formula_q = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE name = '$formula'");


while ($formula = mysqli_fetch_array($formula_q)) {
	$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT name,profile FROM ingredients WHERE name = '$formula[ingredient]'"));
	$prf[] = $ing_q['profile'];
}
$pyr = array_count_values($prf); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="<?php echo $product.' - '.$ver;?>">
  <meta name="author" content="JBPARFUM">
  <title><?php echo $product;?> - Dashboard</title>
  <link href="../css/sb-admin-2.css" rel="stylesheet">
  
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
    "value": <?php echo calcPerc($pyr['Base'],$base_n);?>,
	"desc": "%",
	"notes": "<?php getFormula($_GET['formula'],'Base',$dbhost,$dbuser,$dbpass,$dbname);?>"
  }, 
  {
    "title": "Heart Notes",
    "value": <?php echo calcPerc($pyr['Heart'],$heart_n);?>,
	"desc": "%",
	"notes": "<?php getFormula($_GET['formula'],'Heart',$dbhost,$dbuser,$dbpass,$dbname);?>"
  }, 
  {
    "title": "Top Notes",
    "value": <?php echo calcPerc($pyr['Top'],$top_n);?>,
	"desc": "%",
	"notes": "<?php getFormula($_GET['formula'],'Top',$dbhost,$dbuser,$dbpass,$dbname);?>"
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
  
</body>
</html>
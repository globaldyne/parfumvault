<?php 
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/checkIng.php');
require_once(__ROOT__.'/func/calcCosts.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Generate</title>
  <link href="../css/sb-admin-2.css" rel="stylesheet">
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  
    <script src="../js/bootstrap.min.js"></script>

  <script src="../js/jquery-ui.js"></script>
    <script src="../js/tableHTMLExport.js"></script>


</head>

<body>
<?php 
$f_name =  mysqli_real_escape_string($conn, $_GET['name']);

$formula_q = mysqli_query($conn, "SELECT * FROM formulas WHERE name = '$f_name' ORDER BY ingredient ASC");

$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE name = '$f_name'"));
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE name = '$f_name'"));

$bottle = '30';
$type = '20';
?>
<div id="content-wrapper" class="d-flex flex-column">
<?php //require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
		<div>
          <div class="card shadow mb-4">
            <div class="card-header py-3"> 
                                    
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=Formula&name=<?php echo $f_name; ?>"><?php echo $f_name; ?></a></h2>
            
            </div>
            <div class="card-body">
           <div id="msg"></div>
              <div>
                  <tr>
                    <th colspan="6">
                      </th>
                    </tr>
                <table class="table table-bordered" id="formula" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noexport">
                      <th colspan="6">                      </th>
                    </tr>
                    <tr>
                      <th width="22%">Ingredient</th>
                      <th width="11%">Purity %</th>
                      <th width="15%">Quantity</th>
                      <th width="15%">Concentration*</th>
                      <th colspan="2">Cost</th>
                    </tr>
                  </thead>
                  <tbody id="formula_data">
                  <?php while ($formula = mysqli_fetch_array($formula_q)) {
					  
					  	$cas = mysqli_fetch_array(mysqli_query($conn, "SELECT cas FROM ingredients WHERE name = '$formula[ingredient]'"));
					 
						$limitIFRA = searchIFRA($cas['cas'],$formula['ingredient'],$dbhost,$dbuser,$dbpass,$dbname);
						$limit = explode(' - ', $limitIFRA);
					    $limit = $limit['0'];
					  
					  	$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT IFRA,price,ml,profile,profile FROM ingredients WHERE name = '$formula[ingredient]'"));
						$new_conc = $bottle/100*$type;
						
					    $new_quantity = 6/$formula['quantity'];
											
					  	$conc = number_format($new_quantity/$mg['total_mg'] * 100, 2);
					  	$conc_p = number_format($formula['concentration'] / 100 * $conc, 2);
					 	
						echo'<tr>
                      <td align="center" class="'.$ing_q['profile'].'" id="ingredient">'.$formula['ingredient'].'</td>
                      <td align="center">'.$formula['concentration'].'</td>';
					  if($limit != null){
						 if($limit < $conc_p){
							$IFRA_WARN = 'class="alert-danger"';//VALUE IS TO HIGH AGAINST IFRA
					  	}else{
							$IFRA_WARN = 'class="alert-success"'; //VALUE IS OK
						}
					  }else
					  if($ing_q['IFRA'] != null){
					  	if($ing_q['IFRA'] < $conc_p){
							$IFRA_WARN = 'class="alert-danger"'; //VALUE IS TO HIGH AGAINST LOCAL DB
					  	}else{
							$IFRA_WARN = 'class="alert-success"'; //VALUE IS OK
						}
					  }else{
						  $IFRA_WARN = 'class="alert-warning"'; //NO RECORD FOUND
					  }
					  echo'<td align="center">'.$new_quantity.'</td>';
					  echo'<td align="center" '.$IFRA_WARN.'>'.$conc_p.'%</td>';
					  echo '<td align="center">'.utf8_encode($settings['currency']).calcCosts($ing_q['price'],$new_quantity, $formula['concentration'], $ing_q['ml']).'</td>';
					  echo '</tr>';
					$tot[] = calcCosts($ing_q['price'],$new_quantity, $formula['concentration'], $ing_q['ml']);
					$conc_tot[] = $conc_p;
					$new_tot[] = $new_quantity;
				  }
                  ?>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th></th>
                      <th></th>
                      <th align="right">&nbsp;</th>
                      <th>&nbsp;</th>
                      <th colspan="2" align="right">&nbsp;</th>
                    </tr>
                    <tr>
                      <th>Ethanol (?)</th>
                      <th></th>
                      <th align="right">mg</th>
                      <th>&nbsp;</th>
                      <th colspan="2" align="right">&nbsp;</th>
                    </tr>
                    <tr>
                      <th width="22%"></th>
                      <th></th>
                      <th width="15%" align="right"><p>Total: <?php echo number_format(array_sum($new_tot), 2); ?>mg</p></th>
                      <th width="15%">Total <?php echo array_sum($conc_tot);?>%</th>
                      <th colspan="2" align="right">Cost: <?php echo utf8_encode($settings['currency']).number_format(array_sum($tot),2);?> <a href="#" class="fas fa-question-circle" rel="tipsy" title="Total cost"></a></th>
                    </tr>
                  </tfoot>                                    
                </table> 
                <div>
                <p></p>
                <p>*Values in: <strong class="alert alert-danger">red</strong> exceeds IFRA limit,   <strong class="alert alert-warning">yellow</strong> have no IFRA limit set,   <strong class="alert alert-success">green</strong> are within IFRA limits</p>
                </div>
            </div>
          </div>
        </div>
      </div>
   </div>
  </div>
<script type="text/javascript" language="javascript" >


$('#csv').on('click',function(){
  $("#formula").tableHTMLExport({
	type:'csv',
	filename:'<?php echo $f_name; ?>.csv',
	separator: ',',
  	newline: '\r\n',
  	trimContent: true,
  	quoteFields: true,
	
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',
	
	htmlContent: false,
  
  	// debug
  	consoleLog: true   
});
 
})

</script>
</body>
</html>
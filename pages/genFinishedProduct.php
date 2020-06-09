<?php 
if (!defined('pvault_panel')){ die('Not Found');}  

$f_name =  mysqli_real_escape_string($conn, $_POST['formula']);

$formula_q = mysqli_query($conn, "SELECT * FROM formulas WHERE name = '$f_name' ORDER BY ingredient ASC");

$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE name = '$f_name'"));
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE name = '$f_name'"));

$bottle = $_POST['bottle'];
$type = $_POST['type'];
$new_conc = $bottle/100*$type;
$carrier = $bottle - $new_conc;
?>
<script>
function printLabel() {
	<?php if(empty($settings['label_printer_addr']) || empty($settings['label_printer_model'])){?>
	$("#msg").html('<div class="alert alert-danger alert-dismissible">Please configure printer details in <a href="/?do=settings">settings<a> page</div>');
	<?php }else{ ?>
	$("#msg").html('<div class="alert alert-info alert-dismissible">Printing...</div>');

$.ajax({ 
    url: '/pages/manageFormula.php', 
	type: 'get',
    data: {
		action: "printLabel",
		name: "<?php echo $f_name; ?>"
		},
	dataType: 'html',
    success: function (data) {
	  $('#msg').html(data);
    }
  });
	<?php } ?>
};
</script>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
		<div>
          <div class="card shadow mb-4">
            <div class="card-header py-3"> 
			<?php if($_GET['generate']){?>
             <h2 class="m-0 font-weight-bold text-primary"><a href="/?do=genFinishedProduct"><?php echo $f_name;?> Finished Product</a></h2>
             <h5 class="m-1 text-primary"><?php echo "Bottle: ".$bottle."ml Concentration: ".$type."%";?></h5>
        	<?php }else{ ?>
              <h2 class="m-0 font-weight-bold text-primary"><a href="/?do=genFinishedProduct">Generate Finished Product</a></h2>
            <?php } ?>
            </div>
            <div class="card-body">
           <div id="msg"></div>
           <?php if($_GET['generate']){?>
              <div>
                <tr>
                    <th colspan="6">
                    </th>
                </tr>
                <table class="table table-bordered" id="formula" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noexport">
                      <th colspan="6"></th>
                    </tr>
                    <tr class="noexport">
                      <th colspan="6">
                     <div class="text-left">
                      <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" id="pdf" href="#">Export to PDF</a>
                        <a class="dropdown-item" href="javascript:printLabel();" onclick="return confirm('Print label?');">Print Label</a>
                      </div>
                    </div>
                    </div>
                    </th>
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
					    $new_quantity = $formula['quantity']/$mg['total_mg']*$new_conc;
						
					  	$conc = $new_quantity/$bottle * 100;
						
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
					  echo'<td align="center">'.number_format($new_quantity, 2).'</td>';
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
                      <th align="center">Sub Total: <?php echo number_format(array_sum($new_tot), 2); ?></th>
                      <th>&nbsp;</th>
                      <th colspan="2" align="center">&nbsp;</th>
                    </tr>
                    <tr>
                      <th>&nbsp;</th>
                      <th></th>
                      <th align="center">Carrier: <?php echo $carrier; ?></th>
                      <th>&nbsp;</th>
                      <th colspan="2" align="center">&nbsp;</th>
                    </tr>
                    <tr>
                      <th width="22%"></th>
                      <th></th>
                      <th width="15%" align="right"><p>Total: <?php echo number_format(array_sum($new_tot)+ $carrier, 2); ?>mg</p></th>
                      <th width="15%">Total <?php echo array_sum($conc_tot);?>%</th>
                      <th colspan="2" align="right">Total Cost: <?php echo utf8_encode($settings['currency']).number_format(array_sum($tot),2);?> <a href="#" class="fas fa-question-circle" rel="tipsy" title="Total cost"></a></th>
                    </tr>
                  </tfoot>                                    
                </table> 
                <div>
                <p></p>
                <p>*Values in: <strong class="alert alert-danger">red</strong> exceeds IFRA limit,   <strong class="alert alert-warning">yellow</strong> have no IFRA limit set,   <strong class="alert alert-success">green</strong> are within IFRA limits</p>
                </div>
            </div>
            <?php }else{ ?>
           <form action="/?do=genFinishedProduct&generate=1" method="post" enctype="multipart/form-data" target="_self">
           
           <table width="100%" border="0">
  <tr>
    <td width="9%">Formula:</td>
    <td width="24%">
    <select name="formula" id="formula" class="form-control selectpicker" data-live-search="true">
     <?php
		$sql = mysqli_query($conn, "SELECT name FROM formulasMetaData ORDER BY name ASC");
		while ($formula = mysqli_fetch_array($sql)){
			echo '<option value="'.$formula['name'].'">'.$formula['name'].'</option>';
		}
	  ?>
     </select>
   </td>
    <td width="67%">&nbsp;</td>
  </tr>
  <tr>
    <td>Concentration:</td>
    <td>
        <select name="type" id="type" class="form-control selectpicker" data-live-search="true">
     <?php
	 		echo '<option value="'.$settings['Parfum'].'">Parfum ('.$settings['Parfum'].'%)</option>';
			echo '<option value="'.$settings['EDP'].'">EDP ('.$settings['EDP'].'%)</option>';
			echo '<option value="'.$settings['EDT'].'">EDT ('.$settings['EDT'].'%)</option>';
			echo '<option value="'.$settings['EDC'].'">EDC ('.$settings['EDC'].'%)</option>';

	  ?>
     </select>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Bottle:</td>
    <td>    
    <select name="bottle" id="bottle" class="form-control selectpicker" data-live-search="true">
     <?php
		$sql = mysqli_query($conn, "SELECT name,ml FROM bottles ORDER BY name ASC");
		while ($bottle = mysqli_fetch_array($sql)){
			echo '<option value="'.$bottle['ml'].'">'.$bottle['name'].' ('.$bottle['ml'].'ml)</option>';
		}
	  ?>
     </select>
     </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td><input type="submit" name="button" id="button" value="Generate"></td>
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
           </form>          
            <?php } ?>
           </div>
        </div>
      </div>
   </div>
  </div>
<script type="text/javascript" language="javascript" >


$('#pdf').on('click',function(){
  $("#formula").tableHTMLExport({
	type:'pdf',
	filename:'<?php echo $f_name; ?>.pdf',
	  orientation: 'p',
	
	
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',
	/*
	 var doc = new jsPDF()
  doc.autoTable({ html: '#my-table' })
  doc.save('table.pdf')
  */
});
 
})

</script>
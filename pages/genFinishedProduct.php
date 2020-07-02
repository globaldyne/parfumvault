<?php 
if (!defined('pvault_panel')){ die('Not Found');}  
if($_POST['formula']){
	$f_name =  mysqli_real_escape_string($conn, $_POST['formula']);
	
	$formula_q = mysqli_query($conn, "SELECT * FROM formulas WHERE name = '$f_name' ORDER BY ingredient ASC");
	
	$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE name = '$f_name'"));
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE name = '$f_name'"));
	
	$bottle = mysqli_real_escape_string($conn, $_POST['bottle']);
	$type = mysqli_real_escape_string($conn, $_POST['type']);
	$carrier_id = mysqli_real_escape_string($conn, $_POST['carrier']);
	$lid_id = mysqli_real_escape_string($conn, $_POST['lid']);
	$bottle_cost = mysqli_fetch_array(mysqli_query($conn, "SELECT  price,ml,name FROM bottles WHERE id = '$bottle'"));
	$carrier_cost = mysqli_fetch_array(mysqli_query($conn, "SELECT  price,ml FROM ingredients WHERE id = '$carrier_id'"));
	
	if($_POST['lid']){
		$lid_cost = mysqli_fetch_array(mysqli_query($conn, "SELECT  price,style FROM lids WHERE id = '$lid_id'"));
	}else{
		$lid_cost['price'] = 0;
		$lid_cost['style'] = 'none';
	}
	
	if($_POST['batchID'] == '1'){
		$batchID = genBatchID();
		//TODO
		//GENERATE PDF
		//CREATE DB ENTRY
		//EXTEND UI
	}else{
		$batchID = 'N/A';
	}
			   
	$bottle = $bottle_cost['ml'];
	$new_conc = $bottle/100*$type;
	$carrier = $bottle - $new_conc;
}
?>
<script>
function printLabel() {
	<?php if(empty($settings['label_printer_addr']) || empty($settings['label_printer_model'])){?>
	$("#msg").html('<div class="alert alert-danger alert-dismissible">Please configure printer details in <a href="?do=settings">settings<a> page</div>');
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

function printBoxLabel() {
	<?php if(empty($settings['label_printer_addr']) || empty($settings['label_printer_model']) || $settings['label_printer_size'] != '62 --red'){?>
	$("#msg").html('<div class="alert alert-danger alert-dismissible">Please configure printer details in <a href="?do=settings">settings<a> page. Note: For this label you need 62mm label</div>');
	<?php }else{ ?>
	$("#msg").html('<div class="alert alert-info alert-dismissible">Printing...</div>');

$.ajax({ 
    url: '/pages/manageFormula.php', 
	type: 'get',
    data: {
		action: "printBoxLabel",
		batchID: "<?php echo $batchID; ?>",
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
             <h2 class="m-0 font-weight-bold text-primary"><a href="?do=genFinishedProduct"><?php echo $f_name;?> Finished Product</a></h2>
             <h5 class="m-1 text-primary"><?php echo "Bottle: ".$bottle."ml Concentration: ".$type."%";?></h5>
             <h5 class="m-1 text-primary"><?php echo "Batch ID: ".$batchID;?></h5>
        	<?php }else{ ?>
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=genFinishedProduct">Generate Finished Product</a></h2>
            <?php } ?>
            </div>
            <div class="card-body">
           <div id="msg"></div>
           <?php if($_GET['generate']){?>
              <div>
                <table class="table table-bordered" id="formula" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noexport">
                      <th colspan="7">
                     <div class="text-left">
                      <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" id="pdf" href="#">Export to PDF</a>
                        <a class="dropdown-item" href="javascript:printLabel();" onclick="return confirm('Print label?');">Print Label</a>
                        <a class="dropdown-item" href="javascript:printBoxLabel();" onclick="return confirm('Print Box label?');">Print Box Label</a>
                      </div>
                    </div>
                    </div>
                    </th>
                    </tr>
                    <tr>
                      <th width="22%">Ingredient</th>
                      <th width="11%">CAS#</th>
                      <th width="11%">Purity %</th>
                      <th width="11%">Quantity</th>
                      <th width="11%">Concentration*</th>
                      <th colspan="2">Cost</th>
                    </tr>
                  </thead>
                  <?php while ($formula = mysqli_fetch_array($formula_q)) {
					  
					  	$cas = mysqli_fetch_array(mysqli_query($conn, "SELECT cas FROM ingredients WHERE name = '$formula[ingredient]'"));
					 
						$limitIFRA = searchIFRA($cas['cas'],$formula['ingredient'],$conn);
						$limit = explode(' - ', $limitIFRA);
					    $limit = $limit['0'];
					  
					  	$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT IFRA,price,ml,profile,profile FROM ingredients WHERE name = '$formula[ingredient]'"));
					    $new_quantity = $formula['quantity']/$mg['total_mg']*$new_conc;
						
					  	$conc = $new_quantity/$bottle * 100;
						
					  	$conc_p = number_format($formula['concentration'] / 100 * $conc, 3);
					 	
						echo'<tr>
                      <td align="center">'.$formula['ingredient'].'</td>
					  <td align="center">'.$cas['cas'].'</td>
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
					  echo'<td align="center">'.number_format($new_quantity, 3).'</td>';
					  echo'<td align="center" '.$IFRA_WARN.'>'.$conc_p.'%</td>';
					  echo '<td align="center">'.utf8_encode($settings['currency']).calcCosts($ing_q['price'],$new_quantity, $formula['concentration'], $ing_q['ml']).'</td>';
					  echo '</tr>';
					$tot[] = calcCosts($ing_q['price'],$new_quantity, $formula['concentration'], $ing_q['ml']);
					$conc_tot[] = $conc_p;
					$new_tot[] = $new_quantity;
				  }
                  ?>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td align="center" class="m-1 text-primary">Sub Total: </td>
                      <td align="center" class="m-1 text-primary"><?php echo number_format(array_sum($new_tot), 3); ?>ml</td>
                      <td align="center" class="m-1 text-primary"><?php echo array_sum($conc_tot);?>%</td>
                      <td colspan="2" align="center" class="m-1 text-primary"><?php echo utf8_encode($settings['currency']).number_format(array_sum($tot),2);?></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td align="center" class="m-1 text-primary">Carrier: </td>
                      <td align="center" class="m-1 text-primary"><?php echo $carrier; ?>ml</td>
                      <td align="center" class="m-1 text-primary"><?php echo $carrier*100/$bottle;?>%</td>
                      <td colspan="2" align="center" class="m-1 text-primary"><?php $carrier_sub_cost = $carrier_cost['price'] / $carrier_cost['ml'] * $carrier; echo utf8_encode($settings['currency']).number_format($carrier_sub_cost, 2);?></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td align="center" class="m-0 text-primary">Bottle:</td>
                      <td align="center" class="m-0 text-primary"><?php echo $bottle_cost['ml'];?>ml</td>
                      <td align="center" class="m-0 text-primary">-</td>
                      <td colspan="2" align="center" class="m-0 text-primary"><?php echo  utf8_encode($settings['currency']).$bottle_cost['price']; ?></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td align="center" class="m-0 text-primary">Lid:</td>
                      <td align="center" class="m-0 text-primary"><?php echo $lid_cost['style'];?></td>
                      <td align="center" class="m-0 text-primary">-</td>
                      <td colspan="2" align="center" class="m-0 text-primary"><?php echo $settings['currency'].$lid_cost['price'];?></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td align="center" class="m-0 text-primary">Batch No:</td>
                      <td align="center" class="m-0 text-primary"><?php echo $meta['batchNo'];?></td>
                      <td align="center" class="m-0 text-primary">-</td>
                      <td colspan="2" align="center" class="m-0 text-primary">-</td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td align="center" class="m-0 font-weight-bold text-primary">Total: </td>
                      <td width="15%" align="center" class="m-0 font-weight-bold text-primary"><?php echo number_format(array_sum($new_tot)+ $carrier, 3); ?>ml</td>
                      <td width="15%" align="center" class="m-0 font-weight-bold text-primary"><?php echo $carrier*100/$bottle + array_sum($conc_tot); ?>%</td>
                      <td colspan="2" align="center" class="m-0 font-weight-bold text-primary"><?php echo $settings['currency'].number_format(array_sum($tot)+$lid_cost['price']+$carrier_sub_cost+$bottle_cost['price'],2);?></td>
                    </tr>
                  </tfoot>                                    
                </table> 
                <div>
                <p></p>
                <p>*Values in: <strong class="alert alert-danger">red</strong> exceeds usage level, <strong class="alert alert-warning">yellow</strong> have no usage level set, <strong class="alert alert-success">green</strong> are within usage level</p>
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
    <td>Batch ID:</td>
    <td><select name="batchID" id="batchID" class="form-control selectpicker" data-live-search="false">
      <option value="0">Do Not Generate</option>
      <option value="1">Generate</option>
    </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Bottle:</td>
    <td>    
    <select name="bottle" id="bottle" class="form-control selectpicker" data-live-search="true">
     <?php
		$sql = mysqli_query($conn, "SELECT id,name,ml FROM bottles ORDER BY ml DESC");
		while ($bottle = mysqli_fetch_array($sql)){
			echo '<option value="'.$bottle['id'].'">'.$bottle['name'].' ('.$bottle['ml'].'ml)</option>';
		}
	  ?>
     </select>
     </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Carrier:</td>
    <td>
    <select name="carrier" id="carrier" class="form-control selectpicker" data-live-search="true">
      <?php
		$sql = mysqli_query($conn, "SELECT name,id FROM ingredients WHERE type = 'Carrier' ORDER BY name ASC");
		while ($carrier = mysqli_fetch_array($sql)){
			echo '<option value="'.$carrier['id'].'">'.$carrier['name'].'</option>';
		}
	  ?>
    </select>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Bottle Lid:</td>
    <td><select name="lid" id="lid" class="form-control selectpicker" data-live-search="true">
      <option value="0" selected="selected">None</option>
      <?php
		$sql = mysqli_query($conn, "SELECT style,id FROM lids ORDER BY style ASC");
		while ($lid = mysqli_fetch_array($sql)){
			echo '<option value="'.$lid['id'].'">'.$lid['style'].'</option>';
		}
	  ?>
    </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td><input type="submit" name="button" class="btn btn-info" id="button" value="Generate"></td>
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
	trimContent: true,
    quoteFields: true,
	
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',
	htmlContent: true,

	/*
	 var doc = new jsPDF()
  doc.autoTable({ html: '#formula' })
  doc.save('table.pdf')
  */
});
 
})

</script>

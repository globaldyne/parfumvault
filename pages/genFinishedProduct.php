<?php 
if (!defined('pvault_panel')){ die('Not Found');}
require_once(__ROOT__.'/libs/fpdf.php');
require_once(__ROOT__.'/func/genBatchID.php');
require_once(__ROOT__.'/func/genBatchPDF.php');
require_once(__ROOT__.'/func/validateFormula.php');
require_once(__ROOT__.'/func/calcPerc.php');
require_once(__ROOT__.'/func/calcCosts.php');
if($_POST['formula']){
	$f_name =  mysqli_real_escape_string($conn, $_POST['formula']);
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$f_name'"));

	$formula_q = mysqli_query($conn, "SELECT * FROM formulas WHERE fid = '$f_name' ORDER BY ingredient ASC");
	while ($formula = mysqli_fetch_array($formula_q)){
	    $form[] = $formula;
	}
	$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '$f_name'"));
	
	$bottle = mysqli_real_escape_string($conn, $_POST['bottle']);
	$type = mysqli_real_escape_string($conn, $_POST['type']);
	$carrier_id = mysqli_real_escape_string($conn, $_POST['carrier']);
	$lid_id = mysqli_real_escape_string($conn, $_POST['lid']);
	$bottle_cost = mysqli_fetch_array(mysqli_query($conn, "SELECT  price,ml,name FROM bottles WHERE id = '$bottle'"));
	
	$carrier_cost = mysqli_fetch_array(mysqli_query($conn, "SELECT  price,size FROM suppliers WHERE ingID = '$carrier_id'"));
	
	$defCatClass = mysqli_real_escape_string($conn, $_POST['defCatClass']);

	if(empty($defCatClass)){
		$defCatClass = $settings['defCatClass'];
	}
	
	if($_POST['lid']){
		$lid_cost = mysqli_fetch_array(mysqli_query($conn, "SELECT  price,style FROM lids WHERE id = '$lid_id'"));
	}else{
		$lid_cost['price'] = 0;
		$lid_cost['style'] = 'none';
	}
	
	$bottle = $bottle_cost['ml'];
	$new_conc = $bottle/100*$type;
	$carrier = $bottle - $new_conc;
	
	if(validateFormula($meta['fid'], $bottle, $new_conc, $mg['total_mg'], $defCatClass, $settings['qStep'], $conn) == TRUE){
		$msg =  '<div class="alert alert-danger alert-dismissible">Your formula contains materials, exceeding and/or missing IFRA standards. Please alter your formula.</div>';
	}
	
	if($_POST['ingSup']){
		$sid = $_POST['ingSup'];
	}

if($_POST['batchID'] == '1'){

	define('FPDF_FONTPATH',__ROOT__.'/fonts');
	$batchID = genBatchID();
	
	genBatchPDF($f_name,$batchID,$bottle,$new_conc,$mg['total_mg'],$ver,$defCatClass,$settings['qStep'],$conn);
	
}else{
	$batchID = 'N/A';
}
	
?>
    
<script>
function printLabel() {
	<?php if(empty($settings['label_printer_addr']) || empty($settings['label_printer_model'])){?>
	$("#inf").html('<div class="alert alert-danger alert-dismissible">Please configure printer details in <a href="?do=settings">settings<a> page</div>');
	<?php }else{ ?>
	$("#inf").html('<div class="alert alert-info alert-dismissible">Printing...</div>');

$.ajax({ 
    url: '/pages/manageFormula.php', 
	type: 'GET',
    data: {
		action: "printLabel",
		batchID: "<?php echo $batchID; ?>",
		name: "<?php echo $f_name; ?>"
		},
	dataType: 'html',
    success: function (data) {
	  $('#inf').html(data);
    }
  });
	<?php } ?>
};

function BoxLabel(download) {
	<?php if(empty($settings['label_printer_addr']) || empty($settings['label_printer_model']) || $settings['label_printer_size'] != '62'){?>
	$("#inf").html('<div class="alert alert-danger alert-dismissible">Please configure printer details in <a href="?do=settings">settings<a> page. Note: For this label you need 62mm label</div>');
	<?php }else{ ?>
	if(download == null){
		$("#inf").html('<div class="alert alert-info alert-dismissible">Printing...</div>');
	}else{
		$("#inf").html('<div class="alert alert-info alert-dismissible">Generating label...</div>');
	}

$.ajax({ 
    url: '/pages/manageFormula.php', 
	type: 'GET',
    data: {
		action: "printBoxLabel",
		batchID: "<?php echo $batchID; ?>",
		name: "<?php echo $f_name; ?>",
		carrier: "<?php echo $carrier*100/$bottle;?>",
		copies: $("#copiesToPrint").val(),
		download: download
		},
	dataType: 'html',
    success: function (data) {
	  $('#BoxLabel').modal('toggle');
	  $('#inf').html(data);
    }
  });
	<?php } ?>
};
</script>
<?php } ?>

<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
		<div>
          <div class="card shadow mb-4">
            <div class="card-header py-3"> 
            <?php if($_GET['generate'] && $_POST['formula']){?>
             <h2 class="m-0 font-weight-bold text-primary"><a href="/?do=genFinishedProduct"><?php echo $meta['product_name'];?></a></h2>
             <h5 class="m-1 text-primary">Formula name: <strong><?php echo $meta['name'];?></strong></h5>
             <h5 class="m-1 text-primary">Bottle: <strong><?php echo $bottle; ?><?=$settings['mUnit']?></strong></h5>
			 <h5 class="m-1 text-primary">Concentration: <strong><?php echo $type; ?>%</h5>
     		 <h5 class="m-1 text-primary">Batch ID: <?php if($_POST['batchID'] == '1'){ echo '<a href="/pages/viewDoc.php?type=batch&id='.$batchID.'" target="_blank">'.$batchID.'</a>'; }else{ echo 'N/A';}?></h5>
             <h5 class="m-1 text-primary">Category Class: <strong><?php echo ucfirst($defCatClass);?></strong></h5>
             <?php if($sid){?>
             <h5 class="m-1 text-primary">Supplier: <strong><?=getSupplierByID($sid,$conn)['name']?></strong></h5>
             <?php } ?>
        	<?php }else{ ?>
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=genFinishedProduct">Generate Finished Product</a></h2>
            <?php } ?>
            </div>
            <div class="card-body">
           <div id="inf"><?php if($msg){ echo $msg; }?></div>
            <?php if($_GET['generate'] && $_POST['formula']){?>
              <div>
                <table class="table table-bordered" id="formula" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noexport">
                      <th colspan="8">
                     <div class="text-right">
                      <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
                      <div class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" id="pdf" href="#"><i class="fa-solid fa-file-pdf mx-2"></i>Export to PDF</a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#IFRA"><i class="fa-solid fa-certificate mx-2"></i>IFRA Certificate</a></li>
                        <li><a class="dropdown-item" href="javascript:printLabel()" onclick="return confirm('Print label?')"><i class="fa-solid fa-print mx-2"></i>Print Label</a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#printBoxLabel"><i class="fa-solid fa-print mx-2"></i>Print Box Label</a></li>
                        <li><a class="dropdown-item" href="javascript:BoxLabel('text')"><i class="fa-solid fa-font mx-2"></i>View Box Label as text</a></li>
                      </div>
                    </div>
                    </div>
                    </th>
                    </tr>
                    <tr>
                      <th width="22%">Ingredient</th>
                      <th width="10%">CAS#</th>
                      <th width="10%">Purity %</th>
                      <th width="10%">Dilutant</th>
                      <th width="10%">Quantity</th>
                      <th width="10%">Concentration*</th>
                      <th colspan="2">Cost</th>
                    </tr>
                  </thead>
                  <?php foreach ($form as $formula){
					    $ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT id,cas,$defCatClass,byPassIFRA FROM ingredients WHERE name = '".$formula['ingredient']."'"));

						$limit = explode(' - ',searchIFRA($ing_q['cas'],$formula['ingredient'],null,$conn,$defCatClass));
					  
					    $new_quantity = $formula['quantity']/$mg['total_mg']*$new_conc;
					  	$conc = $new_quantity/$bottle * 100;						
					  	$conc_p = number_format($formula['concentration'] / 100 * $conc, $settings['qStep']);
					 	
						if($settings['multi_dim_perc'] == '1'){
							$conc_p   += multi_dim_perc($conn, $form, $ing_q['cas'], $settings['qStep'])[$ing_q['cas']];
						}
					?>
					  <tr>
                      <td align="center"><?=$formula['ingredient']?></td>
					  <td align="center"><?=$ing_q['cas']?></td>
                      <td align="center"><?=$formula['concentration']?></td>
					<?php if($formula['concentration'] == '100'){ ?>
                      <td align="center">None</td>
					<?php }else{ ?>
					   <td data-name="dilutant" class="dilutant" data-type="select" align="center" data-pk="<?=$formula['ingredient']?>"><?=$formula['dilutant']?></td>
					 <?php
                      }
					  if($limit['0'] != null && $ing_q['byPassIFRA'] == 0){
						 if($limit['0'] < $conc_p){
							$IFRA_WARN = 'class="alert-danger"';//VALUE IS TO HIGH AGAINST IFRA
					  	}else{
							$IFRA_WARN = 'class="alert-success"'; //VALUE IS OK
						}
					  }else
					  if($ing_q[$defCatClass] != null){
					  	if($ing_q[$defCatClass] < $conc_p){
							$IFRA_WARN = 'class="alert-info"'; //VALUE IS TO HIGH AGAINST LOCAL DB
					  	}else{
							$IFRA_WARN = 'class="alert-success"'; //VALUE IS OK
						}
					  }else{
						  $IFRA_WARN = 'class="alert-warning"'; //NO RECORD FOUND
					  }
					  ?>
					  <td align="center"><?=number_format($new_quantity, $settings['qStep'])?></td>
					  <td align="center" <?=$IFRA_WARN?>><?=$conc_p?>%</td>
                      <?php if($sid){ ?>
					  	<td align="center"><?=calcCosts(getSingleSupplier($sid,$ing_q['id'],$conn)['price'],$new_quantity, $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']);?></td>
					  <?php }else{ ?>
					  	<td align="center"><?=calcCosts(getPrefSupplier($ing_q['id'],$conn)['price'],$new_quantity, $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']);?></td>
                      <?php } ?>
                    </tr>
					<?php
						if($sid){
                    		$tot[] = calcCosts(getSingleSupplier($sid,$ing_q['id'],$conn)['price'],$new_quantity, $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']);
						}else{
						   	$tot[] = calcCosts(getPrefSupplier($ing_q['id'],$conn)['price'],$new_quantity, $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']);
						}
						
						$conc_tot[] = $conc_p;
						$new_tot[] = $new_quantity;
				  	}
                  	?>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td align="center" class="m-1 text-primary">Sub Total: </td>
                      <td align="center" class="m-1 text-primary"><?php echo number_format(array_sum($new_tot), $settings['qStep']); ?> <?=$settings['mUnit']?></td>
                      <td align="center" class="m-1 text-primary"><?php echo array_sum($conc_tot);?>%</td>
                      <td colspan="2" align="center" class="m-1 text-primary"><?php echo utf8_encode($settings['currency']).number_format(array_sum($tot),$settings['qStep']);?></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td align="center" class="m-1 text-primary">Carrier/Solvent: </td>
                      <td align="center" class="m-1 text-primary"><?php echo $carrier; ?> <?=$settings['mUnit']?></td>
                      <td align="center" class="m-1 text-primary"><?php echo $carrier*100/$bottle;?>%</td>
                      <td colspan="2" align="center" class="m-1 text-primary"><?php $carrier_sub_cost = $carrier_cost['price'] / $carrier_cost['size'] * $carrier; echo utf8_encode($settings['currency']).number_format($carrier_sub_cost, $settings['qStep']);?></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td align="center" class="m-0 text-primary">Bottle:</td>
                      <td align="center" class="m-0 text-primary"><?php echo $bottle_cost['ml'];?> <?=$settings['mUnit']?></td>
                      <td align="center" class="m-0 text-primary">-</td>
                      <td colspan="2" align="center" class="m-0 text-primary"><?php echo  utf8_encode($settings['currency']).$bottle_cost['price']; ?></td>
                    </tr>
                    <tr>
                      <td></td>
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
                      <td></td>
                      <td align="center" class="m-0 text-primary">Batch No:</td>
                      <td align="center" class="m-0 text-primary"><?php echo $meta['batchNo'];?></td>
                      <td align="center" class="m-0 text-primary">-</td>
                      <td colspan="2" align="center" class="m-0 text-primary">-</td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td align="center" class="m-0 font-weight-bold text-primary">Total: </td>
                      <td width="15%" align="center" class="m-0 font-weight-bold text-primary"><?php echo number_format(array_sum($new_tot)+ $carrier, $settings['qStep']); ?> <?=$settings['mUnit']?></td>
                      <td width="15%" align="center" class="m-0 font-weight-bold text-primary"><?php echo $carrier*100/$bottle + array_sum($conc_tot); ?>%</td>
                      <td colspan="2" align="center" class="m-0 font-weight-bold text-primary"><?php echo $settings['currency'].number_format(array_sum($tot)+$lid_cost['price']+$carrier_sub_cost+$bottle_cost['price'],$settings['qStep']);?></td>
                    </tr>
                  </tfoot>                                    
                </table> 
                <div>
                <p></p>
                <p>*Values in: <strong class="alert alert-danger">red</strong> exceeds usage level,   <strong class="alert alert-warning">yellow</strong> have no usage level set,   <strong class="alert alert-success">green</strong> are within usage level, <strong class="alert alert-info">blue</strong> are exceeding recommended usage level</p>
                </div>
            </div>
            
<!-- Modal PRINT-->
<div class="modal fade" id="printBoxLabel" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="printBoxLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Print Box Label</h5>
      </div>
      <div class="modal-body">
        Copies to print:
          <form action="javascript:BoxLabel()" method="get" name="form1" target="_self" id="form1">
          <input name="copiesToPrint" type="text" id="copiesToPrint" value="1" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="button" value="Print">
      </div>
     </form>
    </div>
  </div>
</div>

<!-- Modal IFRA CERT-->
<div class="modal fade" id="IFRA" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="IFRA" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Generate IFRA Certification</h5>
      </div>
      <div class="modal-body">
          Select customer:
          <form action="/pages/genIFRAcert.php?fid=<?php echo $meta['fid'];?>&conc=<?php echo $type; ?>&bottle=<?php echo $bottle;?>&defCatClass=<?=$defCatClass?>" method="POST" target="_blank">
            <select class="form-control" name="customer" id="customer">
            <?php
				$res = mysqli_query($conn, "SELECT id, name FROM customers ORDER BY name ASC");
				while ($q = mysqli_fetch_array($res)){
				echo '<option value="'.$q['id'].'">'.$q['name'].'</option>';
			}
			?>
            </select>
        	<br/>
            Select IFRA Certification template:
            <select class="form-control" name="template" id="template">
            <?php
				$res = mysqli_query($conn, "SELECT id, name FROM templates ORDER BY name ASC");
				while ($q = mysqli_fetch_array($res)){
				echo '<option value="'.$q['id'].'">'.$q['name'].'</option>';
			}
			?>
            </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="button" value="Generate">
      </div>
     </form>
    </div>
  </div>
</div>
            <?php 
			}else{ 
				if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData"))== 0){
					echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> You need to <a href="?do=listFormulas">create</a> at least one formula first.</div>';
					return;
				}
				if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM bottles"))== 0){
					echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> You need to <a href="?do=bottles">add</a> at least one bottle in your inventory first.</div>';
					return;
				}
				if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE type = 'Carrier' OR type = 'Solvent'"))== 0){
					echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> You need to <a href="?do=ingredients">add</a> at least one solvent or carrier first.</div>';
					return;
				}
				
				$cats_q = mysqli_query($conn, "SELECT name,description FROM IFRACategories ORDER BY id ASC");
				while($cats_res = mysqli_fetch_array($cats_q)){
					$cats[] = $cats_res;
				}
				$sup_q = mysqli_query($conn, "SELECT id,name FROM ingSuppliers ORDER BY id ASC");
				while($r = mysqli_fetch_array($sup_q)){
					$suppliers[] = $r;
				}
				$fTypes_q = mysqli_query($conn, "SELECT id,name,description,concentration FROM perfumeTypes ORDER BY id ASC");
					while($fTypes_res = mysqli_fetch_array($fTypes_q)){
   					$fTypes[] = $fTypes_res;
				}
			?>
           <form action="?do=genFinishedProduct&generate=1" method="post" enctype="multipart/form-data" target="_blank">
           
           <table width="100%" border="0">
  <tr>
    <td width="9%">Formula:</td>
    <td width="24%">
    <select name="formula" id="formula" class="form-control selectpicker" data-live-search="true">
     <?php
		$sql = mysqli_query($conn, "SELECT fid,name,product_name FROM formulasMetaData WHERE product_name IS NOT NULL ORDER BY name ASC");
		while ($formula = mysqli_fetch_array($sql)){
			echo '<option value="'.$formula['fid'].'">'.$formula['name'].' ('.$formula['product_name'].')</option>';
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
     		<option value="100">Concentrated (100%)</option>
			<?php foreach ($fTypes as $fType) {?>
              <option value="<?php echo $fType['concentration'];?>" <?php echo ($info['finalType']==$fType['concentration'])?"selected=\"selected\"":""; ?>><?php echo $fType['name'].' ('.$fType['concentration'];?>%)</option>
            <?php }	?>	
     	</select>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Ingredients Supplier:</td>
    <td><select name="ingSup" id="ingSup" class="form-control selectpicker" data-live-search="true">
       <option value="0" selected="selected">Formula Defaults</option>
      <?php foreach ($suppliers as $supplier) {?>
      <option value="<?=$supplier['id'];?>"><?=$supplier['name'];?></option>
      <?php	}	?>
    </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Category Class:</td>
    <td><select name="defCatClass" id="defCatClass" class="form-control selectpicker" data-live-search="true">
		<?php foreach ($cats as $IFRACategories) {?>
				<option value="cat<?php echo $IFRACategories['name'];?>" <?php echo ($settings['defCatClass']=='cat'.$IFRACategories['name'])?"selected=\"selected\"":""; ?>><?php echo 'Cat '.$IFRACategories['name'].' - '.$IFRACategories['description'];?></option>
		  <?php	}	?>
            </select></td>
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
		$sql = mysqli_query($conn, "SELECT name,id FROM ingredients WHERE type = 'Carrier' OR type = 'Solvent' ORDER BY name ASC");
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
		filename:'<?=base64_decode($f_name)?>.pdf',
		orientation: 'p',
		trimContent: true,
    	quoteFields: true,
		ignoreColumns: '.noexport',
  		ignoreRows: '.noexport',
		htmlContent: true,
		maintitle: '<?=base64_decode($f_name)?>',
		product: '<?php echo trim($product).' '.trim($ver);?>'
	});
});

</script>

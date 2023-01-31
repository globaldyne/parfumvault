<?php 
if (!defined('pvault_panel')){ die('Not Found');}  
if($_POST['formula']){
	$fid =  mysqli_real_escape_string($conn, $_POST['formula']);
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$fid'"));
	$formula_q = mysqli_query($conn, "SELECT * FROM formulas WHERE fid = '".$meta['fid']."' ORDER BY ingredient ASC");
	$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '".$meta['fid']."'"));
	$defCatClass = $settings['defCatClass'];
	$customer = mysqli_fetch_array(mysqli_query($conn,"SELECT name FROM customers WHERE id = '".mysqli_real_escape_string($conn, $_POST['customer'])."'"));
}
?>

<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
		<div>
          <div class="card shadow mb-4">
            <div class="card-header py-3"> 
            <?php if($_GET['generate'] && $_POST['formula']){?>
             <h2 class="m-0 font-weight-bold text-primary"><a href="?do=sellFormula"><?php echo $meta['product_name'];?></a></h2>
             <h5 class="m-1 text-primary">Formula name: <strong><?php echo $meta['name'];?></strong></h5>
             <h5 class="m-1 text-primary">Sell to: <strong><?php echo $customer['name'];?></strong></h5>
        	<?php }else{ ?>
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=sellFormula">Sell Formula</a></h2>
            <?php } ?>
            </div>
            <div class="card-body">
            <?php if($_GET['generate'] && $_POST['formula']){?>
              <div>
                <table class="table table-bordered" id="formula" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noexport">
                      <th colspan="6">
                     <div class="text-left">
                      <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" id="pdf" href="#">Export to PDF</a>
                      </div>
                    </div>
                    </div>
                    </th>
                    </tr>
                    <tr>
                      <th width="22%">Ingredient</th>
                      <th width="10%">CAS#</th>
                      <th width="10%">Purity (%)</th>
                      <th width="10%">Dilutant</th>
                      <th width="10%">Quantity (<?=$settings['mUnit']?>)</th>
                      <th width="10%">Concentration (%)</th>
                      <th width="10%">Properties</th>
                    </tr>
                  </thead>
                  <?php while ($formula = mysqli_fetch_array($formula_q)) {
					    $ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT cas,$defCatClass,odor FROM ingredients WHERE name = '".$formula['ingredient']."'"));
						$conc = number_format($formula['quantity']/$mg['total_mg'] * 100, $settings['qStep']);
					  	$conc_p = number_format($formula['concentration'] / 100 * $conc, $settings['qStep']);
				  ?>
					<tr>
                      <td align="center"><?php echo $formula['ingredient']; ?></td>
					  <td align="center"><?php echo $ing_q['cas']; ?></td>
                      <td align="center"><?php echo $formula['concentration']; ?></td>
					  <?php if($formula['concentration'] == '100'){ ?>
					  <td align="center">None</td>
					  <?php }else{ ?>
					  <td align="center"><?php echo $formula['dilutant']; ?></td>
					  <?php } ?>
           			    <td align="center"><?php echo number_format($formula['quantity'],$settings['qStep']);?></td>
					  	<td align="center"><?php echo $conc_p;?></td>
                        <td align="center"><?php echo $ing_q['odor'];?></td>
					  </tr>
					  <?php }  ?>
                    </tr>
                  <tfoot>
                    <tr>
                      <th width="22%">Total: <?php echo countElement("formulas WHERE fid = '$fid'",$conn);?></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th width="15%" align="right"><p>Total: <?php echo ml2l($mg['total_mg'], 3, $settings['mUnit']); ?></p></th>
                      <th></th>
                    </tr>
                  </tfoot> 
                </table> 
            </div>
            <?php 
			}else{ 
				if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData"))== 0){
					echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> You need to <a href="?do=listFormulas">create</a> at least one formula first.</div>';
					return;
				}
				if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM customers")) == 0){
					echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> You need to <a href="?do=customers">create</a> at least one customer first.</div>';
					return;
				}				
			?>
<form action="?do=sellFormula&generate=1" method="post" enctype="multipart/form-data" target="_self">           
<table width="100%" border="0">
  <tr>
    <td width="9%">Formula:</td>
    <td width="24%">
    <select name="formula" id="formula" class="form-control selectpicker" data-live-search="true">
     <?php
		$sql = mysqli_query($conn, "SELECT fid,name,product_name FROM formulasMetaData ORDER BY name ASC");
		while ($formula = mysqli_fetch_array($sql)){
			echo '<option value="'.$formula['fid'].'">'.$formula['name'].' ('.$formula['product_name'].')</option>';
		}
	  ?>
     </select>
   </td>
    <td width="67%">&nbsp;</td>
  </tr>
  <tr>
    <td>Customer:</td>
    <td>
      <select name="customer" id="customer" class="form-control selectpicker" data-live-search="true">
        <?php
				$res = mysqli_query($conn, "SELECT id, name FROM customers ORDER BY name ASC");
				while ($q = mysqli_fetch_array($res)){
					echo '<option value="'.$q['id'].'">'.$q['name'].'</option>';
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
	filename:'<?php echo trim(base64_decode($fid)); ?>.pdf',
	orientation: 'p',
	trimContent: true,
    quoteFields: true,
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',
	htmlContent: true,
	cover: '<?php echo base64_encode(wordwrap($meta['notes'],100));?>',
	maintitle: '<?php echo trim(base64_decode($fid)); ?>',
	subtitle: '<?php echo $customer['name'];?>',
	product: '<?php echo trim($product).' '.trim($ver);?>'
  });
})

</script>

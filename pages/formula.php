<?php if (!defined('pvault_panel')){ die('Not Found');}  ?>
<?php
$f_name =  mysqli_real_escape_string($conn, $_GET['name']);

if($_GET['action'] == 'printLabel' && $_GET['name']){
	$lbl = imagecreatetruecolor(720, 260);

	$white = imagecolorallocate($lbl, 255, 255, 255);
	$black = imagecolorallocate($lbl, 0, 0, 0);	
	
	imagefilledrectangle($lbl, 0, 0, 720, 260, $white);
	
	$text = trim($_GET['name']);
	$font = './fonts/Arial.ttf';
	
	imagettftext($lbl, $settings['label_printer_font_size'], 0, 0, 150, $black, $font, $text);
	$lblF = imagerotate($lbl, 90 ,0);
	
	$save = "tmp/labels/".base64_encode($text.'png');
	if(imagepng($lblF, $save)){
		imagedestroy($lblF);
		shell_exec('/usr/bin/brother_ql -m '.$settings['label_printer_model'].' -p tcp://'.$settings['label_printer_addr'].' print -l '.$settings['label_printer_size'].' '. $save);
			$msg = '<div class="alert alert-success alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
					Print sent!
					</div>';
	}
}

if($_GET['action'] == 'deleteIng' && $_GET['id'] && $_GET['ing']){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	$ing = mysqli_real_escape_string($conn, $_GET['ing']);
	$fname = mysqli_real_escape_string($conn, $_GET['name']);

	if(mysqli_query($conn, "DELETE FROM formulas WHERE id = '$id'")){
		
		$msg = '<div class="alert alert-success alert-dismissible">
				<a href="?do=Formula&name='.$fname.'" class="close" data-dismiss="alert" aria-label="close">x</a>
				'.$ing.' removed from the formula!
				</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">
				<a href="?do=Formula&name='.$fname.'" class="close" data-dismiss="alert" aria-label="close">x</a>
				'.$ing.' cannot be removed from the formula!
				</div>';
	}
}

if($_GET['action'] == 'addIng' && $_POST['concentration'] && $_POST['quantity'] && $_POST['ingredient']){
	$fname = mysqli_real_escape_string($conn, $_GET['name']);
	$quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
	$concentration = mysqli_real_escape_string($conn, $_POST['concentration']);
	$ingredient = mysqli_real_escape_string($conn, $_POST['ingredient']);
	//$ingredient_id = mysqli_real_escape_string($conn, $_POST['ingredient']);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '$ingredient' AND name = '$fname'"))){
		$msg='<div class="alert alert-danger alert-dismissible">
		<a href="?do=Formula&name='.$fname.'" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>Error: </strong>'.$ingredient.' already exists in formula!
		'.mysqli_error($conn).'
		</div>';
	}else{

		if(mysqli_query($conn,"INSERT INTO formulas(fid,name,ingredient,ingredient_id,concentration,quantity) VALUES('".base64_encode($fname)."','$fname','$ingredient','$ingredient_id','$concentration','$quantity')")){
			$msg = '<div class="alert alert-success alert-dismissible">
					<a href="?do=Formula&name='.$fname.'" class="close" data-dismiss="alert" aria-label="close">x</a>
					'.$ingredient.' added to formula!
					</div>';
		}else{
			$msg = '<div class="alert alert-danger alert-dismissible">
					<a href="?do=Formula&name='.$fname.'" class="close" data-dismiss="alert" aria-label="close">x</a>
					Error adding '.$ingredient.'!
					</div>';
		}
	}
}


$formula_q = mysqli_query($conn, "SELECT * FROM formulas WHERE name = '$f_name' ORDER BY ingredient ASC");

$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE name = '$f_name'"));

?>

<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=Formula&name=<?php echo $f_name; ?>"><?php echo $f_name; ?></a></h2>
            </div>
            <div class="card-body">
            <?php echo $msg; ?>
              <div>
                  <tr>
                    <th colspan="6">
                      <form action="/?do=Formula&name=<?php echo $f_name; ?>&action=addIng" method="post" enctype="multipart/form-data" name="form1" id="form1">
                         <table width="100%" border="0" class="table table-bordered">
                                    <tr>  
                                         <td>
                                         <select name="ingredient" id="ingredient" class="form-control selectpicker" data-live-search="true">
                                         <?php
										 	$res_ing = mysqli_query($conn, "SELECT id,name FROM ingredients ORDER BY name ASC");
										 	while ($r_ing = mysqli_fetch_array($res_ing)){
												echo '<option value="'.$r_ing['name'].'">'.$r_ing['name'].'</option>';
											}
										 ?>
                                         </select>                                         
                                         </td>
                                         <td><input type="text" name="concentration" placeholder="Concentration %" class="form-control" /></td>
                                         <td><input type="text" name="quantity" placeholder="Quantity" class="form-control" /></td>  
                                         <td><input type="submit" name="add" id="add" class="btn btn-info" value="Add" /> </td>  
                                    </tr>  
                               </table>  
                      </form>
                      </th>
                    </tr>
                <table class="table table-bordered" id="formula" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noexport">
                      <th colspan="5"></th>
                      <th>
                      <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
                        <a class="dropdown-item" href="/?do=Formula&action=printLabel&name=<?php echo $f_name; ?>" onclick="return confirm('Print label?');">Print Label</a>
                        <a class="dropdown-item popup-link" href="/pages/viewPyramid.php?formula=<?php echo $f_name; ?>">View Pyramid</a>
                        <a class="dropdown-item" href="/pages/manageFormula.php?do=multiply&formula=<?php echo $f_name; ?>">Multiply x2</a>
                        <a class="dropdown-item" href="/pages/manageFormula.php?do=divide&formula=<?php echo $f_name; ?>">Divide x2</a>

                      </div>
                    </div>
                    </tr>
                    <tr>
                      <th width="22%">Ingredient</th>
                      <th width="11%">Strength %</th>
                      <th width="15%">Quantity</th>
                      <th width="15%">Concentration*</th>
                      <th width="15%">Cost</th>
                      <th class="noexport" width="15%">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="formula_data">
                  <?php while ($formula = mysqli_fetch_array($formula_q)) {
					  echo'
                    <tr>
                      <td align="center"><a href="/pages/editIngredient.php?id='.$formula['ingredient'].'" class="popup-link">'.$formula['ingredient'].'</a> '.checkIng($formula['ingredient'],$dbhost, $dbuser, $dbpass, $dbname).'</td>
                      <td data-name="concentration" class="concentration" data-type="text" align="center" data-pk="'.$formula['ingredient'].'">'.$formula['concentration'].'</td>';
					  //TODO: Search by cas as well
					  if($limit = searchIFRA(null,$formula['ingredient'],$dbhost,$dbuser,$dbpass,$dbname)){

						//$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT price,ml,profile FROM ingredients WHERE name = '$formula[ingredient]'"));

					  }//else{
						  
					 // 	$limit_local = mysqli_fetch_array(mysqli_query($conn, "SELECT IFRA,price,ml,profile FROM ingredients WHERE name = '$formula[ingredient]'"));
					  
					  //}
					  //TODO: FIX THIS
					  $ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT IFRA,price,ml,profile FROM ingredients WHERE name = '$formula[ingredient]'"));
					  $limit_local = $ing_q;
					  $conc = number_format($formula['quantity']/$mg['total_mg'] * 100, 2);
					  //$total = $concentration / 100 * $sub;
					  $conc_p = number_format($formula['concentration'] / 100 * $conc, 2);
					  
					  if($limit != null){
						 if($limit < $conc_p){
							$IFRA_WARN = 'class="alert-danger"';
					  	}else{
							$IFRA_WARN = 'class="alert-success"';
						}
					  }else
					  if($limit_local['IFRA'] != null){
					  	if($limit_local['IFRA'] < $conc_p){
							$IFRA_WARN = 'class="alert-danger"';
					  	}else{
							$IFRA_WARN = 'class="alert-success"';
						}
					  }else{
						  $IFRA_WARN = 'class="alert-warning"';
					  }
					  echo'<td data-name="quantity" class="quantity" data-type="text" align="center" data-pk="'.$formula['ingredient'].'">'.$formula['quantity'].'</td>';
					  echo'<td align="center" '.$IFRA_WARN.'>'.$conc_p.'%</td>';
					  echo '<td align="center">'.utf8_encode($settings['currency']).calcCosts($ing_q['price'],$formula['quantity'], $formula['concentration'], $ing_q['ml']).'</td>';
					  echo '<td class="noexport" align="center"><a href="/?do=Formula&action=deleteIng&name='.$formula['name'].'&id='.$formula['id'].'&ing='.$formula['ingredient'].'" onclick="return confirm(\'Remove '.$formula['ingredient'].' from formula?\');" class="fas fa-trash" rel="tipsy" title="Remove '.$formula['ingredient'].'"></a></td>
                    </tr>';
					$tot[] = calcCosts($ing_q['price'],$formula['quantity'], $formula['concentration'], $ing_q['ml']);
				  }
                  ?>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th width="22%"></th>
                      <th></th>
                      <th width="15%" align="right"><p>Total: <?php echo number_format($mg['total_mg'], 2); ?> mg</p></th>
                      <th width="15%"></th>
                      <th width="15%" align="right">Cost: <?php echo utf8_encode($settings['currency']).number_format(array_sum($tot),2);?> <a href="#" class="fas fa-question-circle" rel="tipsy" title="Total cost"></a></th>
                      <th class="noexport" width="15%"></th>
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
$(document).ready(function(){
 
  $('#formula_data').editable({
  container: 'body',
  selector: 'td.quantity',
  url: "/pages/update_data.php?formula=<?php echo $f_name; ?>",
  title: 'mg',
  type: "POST",
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg;
    },
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
   if($.isNumeric(value) == '' ){
    return 'Numbers only!';
   }
  }
 });
 
  $('#formula_data').editable({
  container: 'body',
  selector: 'td.concentration',
  url: "/pages/update_data.php?formula=<?php echo $f_name; ?>",
  title: 'Strength %',
  type: "POST",
  dataType: 'json',
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
   if($.isNumeric(value) == '' ){
    return 'Numbers only!';
   }
  }
 });
 
});

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
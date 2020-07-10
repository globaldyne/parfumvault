<?php 
if (!defined('pvault_panel')){ die('Not Found');}  
$f_name =  mysqli_real_escape_string($conn, $_GET['name']);
$fid = base64_encode($f_name);

if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid'")) == FALSE){
	echo 'Formula doesn\'t exist';
	exit;
}
$formula_q = mysqli_query($conn, "SELECT * FROM formulas WHERE name = '$f_name' ORDER BY ingredient ASC");
                    

$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE name = '$f_name'"));
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE name = '$f_name'"));

$top_calc = calcPerc($f_name, 'Top', $settings['top_n'], $conn);
$heart_calc = calcPerc($f_name, 'Heart', $settings['heart_n'], $conn);
$base_calc = calcPerc($f_name, 'Base', $settings['base_n'], $conn);

?>
<script>
function printLabel() {
	<?php if(empty($settings['label_printer_addr']) || empty($settings['label_printer_model'])){?>
	$("#msg").html('<div class="alert alert-danger alert-dismissible">Please configure printer details in <a href="/?do=settings">settings<a> page</div>');
	<?php }else{ ?>
	$("#msg").html('<div class="alert alert-info alert-dismissible">Printing...</div>');

$.ajax({ 
    url: 'pages/manageFormula.php', 
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
//MULTIPLY - DIVIDE
function manageQuantity(quantity) {
	$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		do: quantity,
		formula: "<?php echo $f_name; ?>",
		},
	dataType: 'html',
    success: function (data) {
		location.reload();
	  	//$('#msg').html(data);
    }
  });

};

//Delete ingredient
function deleteING(ingName,ingID) {	  
$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: "deleteIng",
		fname: "<?php echo $f_name; ?>",
		ingID: ingID,
		ing: ingName
		},
	dataType: 'html',
    success: function (data) {
		location.reload();
	  	$('#msg').html(data);
    }
  });

};
//Add ingredient
function addING(ingName,ingID) {	  
$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: "addIng",
		fname: "<?php echo $f_name; ?>",
		quantity: $("#quantity").val(),
		concentration: $("#concentration").val(),
		ingredient: $("#ingredient").val(),
		dilutant: $("#dilutant").val()
		},
	dataType: 'html',
    success: function (data) {
        if ( data.indexOf("Error") > -1 ) {
			$('#msg').html(data); 
		}else{
			$('#msg').html(data);
			location.reload();
		}
    }
  });

};
//Clone
function cloneMe() {	  
$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: "clone",
		formula: "<?php echo $f_name; ?>",
		},
	dataType: 'html',
    success: function (data) {
        if ( data.indexOf("Error") > -1 ) {
			$('#msg').html(data); 
		}else{
			$('#msg').html(data);
			location.reload();
		}
    }
  });
};

//
$(document).ready(function(){
$('#ingredient').on('change', function(){

$.ajax({ 
    url: 'pages/getIngInfo.php', 
	type: 'get',
    data: {
		filter: "purity",
		name: $(this).val()
		},
	dataType: 'html',
    success: function (data) {
	  $('#concentration').val(data);
    }
  });									   
})

$('.replaceIngredient').editable({
	//value: "",
	type: 'get',
	emptytext: "",
	emptyclass: "",
  	url: "pages/manageFormula.php?action=repIng&fname=<?php echo $f_name; ?>",
    source: [
			 <?php
				$res_ing = mysqli_query($conn, "SELECT id, name, chemical_name FROM ingredients ORDER BY name ASC");
				while ($r_ing = mysqli_fetch_array($res_ing)){
				echo '{value: "'.$r_ing['name'].'", text: "'.$r_ing['name'].'"},';
			}
			?>
          ],
	dataType: 'html',
    success: function (data) {
        if ( data.indexOf("Error") > -1 ) {
			$('#msg').html(data); 
		}else{
			$('#msg').html(data);
			location.reload();
		}
	}
    });
});

 
 
</script>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
		<div>
          <div class="card shadow mb-4">
            <div class="card-header py-3"> 
			  <?php if($meta['image']){?><div class="img-formula"><img class="img-perfume" src="<?php echo $meta['image']; ?>"/></div><?php } ?>
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=Formula&name=<?php echo $f_name; ?>"><?php echo $f_name; ?></a></h2>
              <h5 class="m-1 text-primary"><a href="pages/getFormMeta.php?id=<?php echo $meta['id'];?>" class="popup-link">Details</a></h5>
            </div>
            <div class="card-body">
           <div id="msg"></div>
              <div>
                  <tr>
                    <th colspan="6">
                      <form action="javascript:addING();" enctype="multipart/form-data" name="form1" id="form1">
                         <table width="100%" border="0" class="table">
                                    <tr>  
                                         <td>
                                         <select name="ingredient" id="ingredient" class="form-control selectpicker" data-live-search="true">
                                         <option value="" selected disabled>Ingredient</option>
                                         <?php
										 	$res_ing = mysqli_query($conn, "SELECT id, name, profile, chemical_name FROM ingredients ORDER BY name ASC");
										 	while ($r_ing = mysqli_fetch_array($res_ing)){
												/*
												if($settings['chem_vs_brand'] == '1'){
													if($r_ing['chemical_name']){
														$r_ing['name'] = $r_ing['chemical_name'];
													}
												}
												*/
												echo '<option value="'.$r_ing['name'].'">'.$r_ing['name'].' ('.$r_ing['profile'].')</option>';
											}
										 ?>
                                         </select>                                         
                                         </td>
                                         <td><input type="text" name="concentration" id="concentration" placeholder="Purity %" class="form-control" /></td>
                                      <td>
                                         <select name="dilutant" id="dilutant" class="form-control selectpicker" data-live-search="true">
                                         <option value="" selected disabled>Dilutant</option>
                                         <option value="none">None</option>
                                         <?php
										 	$res_dil = mysqli_query($conn, "SELECT id, name FROM ingredients WHERE type = 'Solvent' OR type = 'Carrier' ORDER BY name ASC");
										 	while ($r_dil = mysqli_fetch_array($res_dil)){
											
												echo '<option value="'.$r_dil['name'].'">'.$r_dil['name'].'</option>';
											}
										 ?>
                                         </select>
                                      </td>
                                         <td><input type="text" name="quantity" id="quantity" placeholder="Quantity" class="form-control" /></td>  
                                         <td><input type="submit" name="add" id="add" class="btn btn-info" value="Add" /> </td>  
                                    </tr>  
                               </table>  
                      </form>
                      </th>
                    </tr>
                    <?php if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulas WHERE fid = '$fid'"))){?>
                <table class="table table-bordered" id="formula" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noexport">
                      <th colspan="6"><div class="progress">
  <div class="progress-bar" role="progressbar" style="width: <?php echo $base_calc; ?>%" aria-valuenow="<?php echo $base_calc;?>" aria-valuemin="0" aria-valuemax="<?php echo $settings['base_n'];?>"><span><?php echo $base_calc;?>% Base Notes</span></div>
  <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $heart_calc; ?>%" aria-valuenow="<?php echo $heart_calc; ?>" aria-valuemin="0" aria-valuemax="<?php echo $settings['heart_n'];?>"><span><?php echo $heart_calc;?>% Heart Notes</span></div>
  <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $top_calc; ?>%" aria-valuenow="<?php echo $top_calc; ?>" aria-valuemin="0" aria-valuemax="<?php echo $settings['top_n'];?>"><span><?php echo $top_calc;?>% Top Notes</span></div>
</div></th>
                      <th>
                      <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
                   <!--     <a class="dropdown-item" href="javascript:printLabel();" onclick="return confirm('Print label?');">Print Label</a> -->
                        <a class="dropdown-item popup-link" href="pages/viewPyramid.php?formula=<?php echo $f_name; ?>">View Pyramid</a>                                                
                        <a class="dropdown-item" href="javascript:manageQuantity('multiply')">Multiply x2</a>
                        <a class="dropdown-item" href="javascript:manageQuantity('divide')">Divide x2</a>
                        <a class="dropdown-item" href="javascript:cloneMe();">Clone Formula</a>
                      </div>
                    </div>
                    </tr>
                    <tr>
                      <th width="22%">Ingredient</th>
                      <th width="10%">Purity %</th>
                      <th width="10%">Dilutant</th>
                      <th width="10%">Quantity</th>
                      <th width="10%">Concentration*</th>
                      <th width="10%">Cost</th>
                      <th class="noexport" width="15%">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="formula_data">
                  <?php while ($formula = mysqli_fetch_array($formula_q)) {
					  	//$cas = mysqli_fetch_array(mysqli_query($conn, "SELECT cas FROM ingredients WHERE name = '".$formula['ingredient']."'"));
					 	$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT cas, IFRA, price, ml, profile, profile FROM ingredients WHERE BINARY name = '".$formula['ingredient']."'"));

						$limitIFRA = searchIFRA($ing_q['cas'],$formula['ingredient'],$conn);
						$limit = explode(' - ', $limitIFRA);
					    $limit = $limit['0'];
					  
					  	$conc = number_format($formula['quantity']/$mg['total_mg'] * 100, 2);
					  	$conc_p = number_format($formula['concentration'] / 100 * $conc, 2);
						
					 	if($settings['chem_vs_brand'] == '1'){
							$chName = mysqli_fetch_array(mysqli_query($conn,"SELECT chemical_name FROM ingredients WHERE name = '".$formula['ingredient']."'"));
							if($chName['chemical_name']){
								$ingName = $chName['chemical_name'];
							}else{
								$ingName = $formula['ingredient'];
							}
						}else{
							$ingName = $formula['ingredient'];
						}
						
						echo'<tr>
                      <td align="center" class="'.$ing_q['profile'].'" id="ingredient"><a href="pages/editIngredient.php?id='.$formula['ingredient'].'" class="popup-link">'.$ingName.'</a> '.checkIng($formula['ingredient'],$conn).'</td>
                      <td data-name="concentration" class="concentration" data-type="text" align="center" data-pk="'.$formula['ingredient'].'">'.$formula['concentration'].'</td>';
					  if($formula['concentration'] == '100'){
						  echo '<td align="center">None</td>';
					  }else{
						  echo '<td data-name="dilutant" class="dilutant" data-type="select" align="center" data-pk="'.$formula['ingredient'].'">'.$formula['dilutant'].'</td>';
					  }
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
					  echo'<td data-name="quantity" class="quantity" data-type="text" align="center" data-pk="'.$formula['ingredient'].'">'.$formula['quantity'].'</td>';
					  echo'<td align="center" '.$IFRA_WARN.'>'.$conc_p.'%</td>';
					  echo '<td align="center">'.utf8_encode($settings['currency']).calcCosts($ing_q['price'],$formula['quantity'], $formula['concentration'], $ing_q['ml']).'</td>';
					  echo '<td class="noexport" align="center"><a href="#" class="fas fa-exchange-alt replaceIngredient" rel="tipsy" title="Replace '.$formula['ingredient'].'" id="replaceIngredient" data-name="'.$formula['ingredient'].'" data-type="select" data-pk="'.$formula['ingredient'].'" data-title="Choose Ingredient"></a> &nbsp; <a href="'.goShopping($formula['ingredient'],$conn).'" target="_blank" class="fas fa-shopping-cart"></a> &nbsp; <a href="javascript:deleteING(\''.$formula['ingredient'].'\', \''.$formula['id'].'\')" onclick="return confirm(\'Remove '.$formula['ingredient'].' from formula?\');" class="fas fa-trash" rel="tipsy" title="Remove '.$formula['ingredient'].'"></a></td>
                    </tr>';
					$tot[] = calcCosts($ing_q['price'],$formula['quantity'], $formula['concentration'], $ing_q['ml']);
					$conc_tot[] = $conc_p;
				  }
                  ?>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th width="22%">Total: <?php echo countElement("formulas WHERE name = '$f_name'" ,$conn);?></th>
                      <th></th>
                      <th></th>
                      <th width="15%" align="right"><p>Total: <?php echo number_format($mg['total_mg'], 2); ?>ml</p></th>
                      <th width="15%">Total <?php echo array_sum($conc_tot);?>%</th>
                      <th width="15%" align="right">Cost: <?php echo utf8_encode($settings['currency']).number_format(array_sum($tot),2);?> <a href="#" class="fas fa-question-circle" rel="tipsy" title="Total cost"></a></th>
                      <th class="noexport" width="15%"></th>
                    </tr>
                  </tfoot>                                    
                </table> 
                
                <div>
                <p></p>
                <p>*Values in: <strong class="alert alert-danger">red</strong> exceeds usage level,   <strong class="alert alert-warning">yellow</strong> have no usage level set,   <strong class="alert alert-success">green</strong> are within usage level</p>
                </div>
                <?php } ?>
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
  url: "pages/update_data.php?formula=<?php echo $f_name; ?>",
  title: 'ml',
  type: "POST",
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg; else location.reload();
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
  url: "pages/update_data.php?formula=<?php echo $f_name; ?>",
  title: 'Purity %',
  type: "POST",
  dataType: 'json',
        success: function(response, newValue) {
        if(response.status == 'error') return response.msg; else location.reload();
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
 //
 $('#formula_data').editable({
	container: 'body',
	selector: 'td.dilutant',
	type: 'POST',
	emptytext: "",
	emptyclass: "",
  	url: "pages/update_data.php?formula=<?php echo $f_name; ?>",
    source: [
			 <?php
				$res_ing = mysqli_query($conn, "SELECT id, name FROM ingredients WHERE type = 'Solvent' OR type = 'Carrier' ORDER BY name ASC");
				while ($r_ing = mysqli_fetch_array($res_ing)){
				echo '{value: "'.$r_ing['name'].'", text: "'.$r_ing['name'].'"},';
			}
			?>
          ],
	dataType: 'json',
    
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
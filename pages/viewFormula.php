<?php

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

require_once(__ROOT__.'/func/calcCosts.php');
require_once(__ROOT__.'/func/calcPerc.php');
require_once(__ROOT__.'/func/checkIng.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/goShopping.php');
require_once(__ROOT__.'/func/ml2L.php');
require_once(__ROOT__.'/func/countElement.php');
require_once(__ROOT__.'/func/getIngSupplier.php');

if(!$_GET['id']){
	echo 'Formula id is missing.';
	return;
}
	
$fid = mysqli_real_escape_string($conn, $_GET['id']);
$f_name = base64_decode($fid);

if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulas WHERE fid = '$fid'")) == 0){
	echo '<div class="alert alert-info alert-dismissible">Incomplete formula. Please add ingredients.</div>';
	return;
}

$formula_q = mysqli_query($conn, "SELECT * FROM formulas WHERE fid = '$fid' ORDER BY ingredient ASC");
while ($formula = mysqli_fetch_array($formula_q)){
	    $form[] = $formula;
}

$defCatClass = $settings['defCatClass'];

$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '$fid'"));
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$fid'"));

$top_calc = calcPerc($f_name, 'Top', $settings['top_n'], $conn);
$heart_calc = calcPerc($f_name, 'Heart', $settings['heart_n'], $conn);
$base_calc = calcPerc($f_name, 'Base', $settings['base_n'], $conn);
?>
 
<script type='text/javascript'>
$(document).ready(function() {
						   
	var groupColumn = 0;
    var table = $('#formula').DataTable({
		responsive: false,
        "columnDefs": [
            { "visible": false, "targets": groupColumn }
        ],
        "order": [[ groupColumn, 'desc' ]],
        "displayLength": 50,
        "drawCallback": function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;
 
            api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group noexport"><td colspan="' + rows.columns()[0].length +'">' + group + ' Notes</td></tr>'
                    );
 
                    last = group;
                }
            } );
        }
    } );

    // Order by the grouping
    $('#formula tbody').on( 'click', 'tr.group', function () {
        var currentOrder = table.order()[0];
        if ( currentOrder[0] === groupColumn && currentOrder[1] === 'asc' ) {
            table.order( [ groupColumn, 'desc' ] ).draw();
        }
        else {
            table.order( [ groupColumn, 'asc' ] ).draw();
        }
    } );
	
	$('a[rel=tipsy]').tipsy();
	
	$('.popup-link').magnificPopup({
		type: 'iframe',
		closeOnContentClick: false,
		closeOnBgClick: false,
  		showCloseBtn: true,
	});
});  


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
	  	$('#msgInfo').html(data);
		fetch_formula();
    }
  });
};

//AMOUNT TO MAKE
function amountToMake() {
	if($("#sg").val().trim() == '' ){
        $('#sg').focus();
	  	$('#amountToMakeMsg').html('<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> all fields required!</div>');
	}else if($("#totalAmount").val().trim() == '' ){
 		$('#totalAmount').focus();
	  	$('#amountToMakeMsg').html('<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> all fields required!</div>');		
	}else{
		$.ajax({ 
		url: 'pages/manageFormula.php', 
		type: 'get',
		cache: false,
		data: {
			fid: "<?php echo base64_encode($f_name); ?>",
			SG: $("#sg").val(),
			amount: $("#totalAmount").val(),
			},
		dataType: 'html',
		success: function (data) {
			$('#amountToMakeMsg').html(data);
			$('#amount_to_make').modal('toggle');
			//fetch_formula();
			location.reload();
		}
	  });
	}
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
	  	$('#msgInfo').html(data);
		fetch_formula();
		fetch_impact();
		fetch_pyramid();
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
			$('#msgInfo').html(data); 
		}else{
			$('#msgInfo').html(data);
		}
    }
  });
};

//Add in TODO
function addTODO() {
	$.ajax({ 
    url: 'pages/manageFormula.php', 
	type: 'get',
    data: {
		action: 'todo',
		fid: "<?php echo base64_encode($f_name); ?>",
		add: true,
		},
	dataType: 'html',
    success: function (data) {
	  	$('#msgInfo').html(data);
    }
  });
};


//Change ingredient
$('.replaceIngredient').editable({
	pvnoresp: false,
	highlight: false,
	type: 'get',
	emptytext: "",
	emptyclass: "",
  	url: "pages/manageFormula.php?action=repIng&fname=<?php echo $f_name; ?>",
    source: [
			 <?php
				$res_ing = mysqli_query($conn, "SELECT name FROM ingredients ORDER BY name ASC");
				while ($r_ing = mysqli_fetch_array($res_ing)){
					echo '{value: "'.htmlspecialchars($r_ing['name']).'", text: "'.htmlspecialchars($r_ing['name']).'"},';
			}
			?>
          ],
	dataType: 'html',
    success: function (data) {
        if ( data.indexOf("Error") > -1 ) {
			$('#msgInfo').html(data); 
		}else{
			$('#msgInfo').html(data);
			fetch_formula();
		}
	}
});

 
</script>

<table class="table table-striped table-bordered nowrap" <?php if($settings['grp_formula'] == '1'){?>id="formula" <?php } ?>width="100%" cellspacing="0">
                  <thead>
                    <tr class="noexport">
                    <?php if($settings['grp_formula'] == '1'){?>
                      <th colspan="9">
                    <?php }else{ ?>
                      <th colspan="8">
                    <?php } ?>                      
                      <div class="progress">
  <div class="progress-bar" role="progressbar" style="width: <?php echo $base_calc; ?>%" aria-valuenow="<?php echo $base_calc;?>" aria-valuemin="0" aria-valuemax="<?php echo $settings['base_n'];?>"><span><?php echo $base_calc;?>% Base Notes</span></div>
  <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $heart_calc; ?>%" aria-valuenow="<?php echo $heart_calc; ?>" aria-valuemin="0" aria-valuemax="<?php echo $settings['heart_n'];?>"><span><?php echo $heart_calc;?>% Heart Notes</span></div>
  <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $top_calc; ?>%" aria-valuenow="<?php echo $top_calc; ?>" aria-valuemin="0" aria-valuemax="<?php echo $settings['top_n'];?>"><span><?php echo $top_calc;?>% Top Notes</span></div>
</div></th>
                      <th>
                      <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                      <div class="dropdown-menu dropdown-menu-left">
                        <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
                        <a class="dropdown-item" href="javascript:manageQuantity('multiply')">Multiply x2</a>
                        <a class="dropdown-item" href="javascript:manageQuantity('divide')">Divide x2</a>
                        <a class="dropdown-item" href="javascript:cloneMe()">Clone Formula</a>
	                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#amount_to_make">Amount to make</a>
             			<div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:addTODO()">Add to the make list</a>
                        <!-- <a class="dropdown-item" href="javascript:addAllToCart()">Add all to cart</a> -->
                      </div>
                    </div>
                    </tr>
                    <tr>
                      <?php if($settings['grp_formula'] == '1'){ echo '<th class="noexport"></th>'; } ?>
                      <th width="22%">Ingredient</th>
                      <th width="5%">CAS#</th>
                      <th width="5%">Purity%</th>
                      <th width="5%">Dilutant</th>
                      <th width="5%">Quantity (<?=$settings['mUnit']?>)</th>
                      <th width="5%">Concentration %*</th>
                      <th width="5%">Cost (<?php echo utf8_encode($settings['currency']);?>)</th>
                      <?php if($meta['defView'] == '1'){?>
                      <th width="5%">Properties</th>
                      <?php }elseif($meta['defView'] == '2'){?>
                      <th width="5%">Notes</th>
                      <?php } ?>
                      <th class="noexport" width="15%">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="formula_data">
                  <?php	foreach ($form as $formula){
						$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT id, cas, $defCatClass, profile, odor FROM ingredients WHERE BINARY name = '".$formula['ingredient']."'"));

						$limit = explode(' - ',searchIFRA($ing_q['cas'],$formula['ingredient'],null,$conn,$defCatClass));
						
					  	$conc = number_format($formula['quantity']/$mg['total_mg'] * 100, 3);
					  	$conc_p = number_format($formula['concentration'] / 100 * $conc, 3);
						
						if($settings['multi_dim_perc'] == '1'){
							$conc_p   += multi_dim_perc($conn, $form)[$formula['ingredient']];
						}
						
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
						?>
						<tr>
						<?php
                        if($settings['grp_formula'] == '1'){
							if(empty($ing_q['profile'])){
						?>
								<td class="noexport">Unknown</td>
						<?php	}else{ ?>
								<td class="noexport"><?php echo $ing_q['profile'];?></td>
						<?php	
                            }
						}
						?>
                      <td align="center" class="<?php if($settings['grp_formula'] == '0'){echo $ing_q['profile'];}?>" id="ingredient"><a href="pages/mgmIngredient.php?id=<?=base64_encode($formula['ingredient'])?>" class="popup-link"><?php echo $ingName;?></a> <?php echo checkIng($formula['ingredient'],$settings['defCatClass'],$conn);?></td>
                      <td align="center"><?php echo $ing_q['cas'];?></td>
                      <td data-name="concentration" class="concentration" data-type="text" align="center" data-pk="<?php echo $formula['ingredient'];?>"><?php echo $formula['concentration'];?></td>
					  <?php if($formula['concentration'] == '100'){ ?>
					   <td align="center">None</td>
					  <?php }else{ ?>
					   <td data-name="dilutant" class="dilutant" data-type="select" align="center" data-pk="<?php echo $formula['ingredient']; ?>"><?php echo $formula['dilutant'];?></td>
					  <?php }
					  if($limit['0'] != null){
						 if($limit['0'] < $conc_p){
							$IFRA_WARN = 'class="alert-danger"';//VALUE IS TO HIGH AGAINST IFRA
					  	}else{
							$IFRA_WARN = 'class="alert-success"'; //VALUE IS OK
						}
					  }else
					  if($ing_q[$defCatClass] != null){
					  	if($ing_q[$defCatClass] < $conc_p){
							$IFRA_WARN = 'class="alert-danger"'; //VALUE IS TO HIGH AGAINST LOCAL DB
					  	}else{
							$IFRA_WARN = 'class="alert-success"'; //VALUE IS OK
						}
					  }else{
						  $IFRA_WARN = 'class="alert-warning"'; //NO RECORD FOUND
					  }
					  ?>
					  <td data-name="quantity" class="quantity" data-type="text" align="center" data-pk="<?php echo $formula['ingredient'];?>"><?php echo number_format($formula['quantity'],$settings['qStep']);?></td>
					  <td align="center" <?php echo $IFRA_WARN;?>><?php echo $conc_p;?></td>
					  <td align="center"><a href="#" data-toggle="tooltip" data-placement="top" title="by <?=getPrefSupplier($ing_q['id'],$conn)['name']?>"><?=calcCosts(getPrefSupplier($ing_q['id'],$conn)['price'],$formula['quantity'], $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']);?></a></td>
                      <?php if($meta['defView'] == '1'){?>
					  <td><?php echo ucfirst($ing_q['odor']);?></td>
					  <?php }elseif($meta['defView'] == '2'){?>
					  <td data-name="notes" class="notes" data-type="text" align="center" data-pk="<?php echo $formula['ingredient'];?>"><?=$formula['notes']?></td>
                      <?php } ?>
                      <?php if($meta['isProtected'] == FALSE){?>
                      <td class="noexport" align="center"><a href="#" class="fas fa-exchange-alt replaceIngredient" rel="tipsy" title="Replace <?php echo $formula['ingredient'];?>" id="replaceIngredient" data-name="<?php echo $formula['ingredient'];?>" data-type="select" data-pk="<?php echo $formula['ingredient'];?>" data-title="Choose Ingredient to replace <?php echo $formula['ingredient'];?>"></a> &nbsp; <a href="<?=getPrefSupplier($ing_q['id'],$conn)['supplierLink']?>" target="_blank" class="fas fa-shopping-cart"></a> &nbsp; <a href="javascript:deleteING('<?=$formula['ingredient']?>', '<?=$formula['id']?>')" onclick="return confirm('Remove <?=$formula['ingredient']?> from formula?')" class="fas fa-trash" rel="tipsy" title="Remove <?=$formula['ingredient']?>"></a>
                      </td>
                      <?php }else{ ?>
                      <td class="noexport" align="center"><a href="<?=getPrefSupplier($ing_q['id'],$conn)['supplierLink']?>" target="_blank" class="fas fa-shopping-cart"></a></td>
                      <?php } ?>
				</tr>
				<?php
					$tot[] = calcCosts(getPrefSupplier($ing_q['id'],$conn)['price'],$formula['quantity'], $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']);
					$conc_tot[] = $conc_p;
				  }
	            ?>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <?php if($settings['grp_formula'] == '1'){ ?>
                      <th>
                      </th> 
                      <?php }?>
                      <th width="22%">Total: <?php echo countElement("formulas WHERE fid = '$fid'",$conn);?></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th width="15%" align="right"><p>Total: <?php echo ml2l($mg['total_mg'], 3, $settings['mUnit']); ?></p></th>
                      <th width="15%">Total: <?php echo array_sum($conc_tot);?>%</th>
                      <th width="15%" align="right">Cost: <?php echo utf8_encode($settings['currency']).number_format(array_sum($tot),3);?></a></th>
                      <th></th>
                      <th class="noexport" width="15%"></th>
                    </tr>
                  </tfoot>                                    
                </table>
                
                
<!--Amount To Make-->
<div class="modal fade" id="amount_to_make" tabindex="-1" role="dialog" aria-labelledby="amount_to_make" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="amount_to_make">Total amount to make</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="amountToMakeMsg"></div>
  	  <form action="javascript:amountToMake()" method="get" name="form1" target="_self" id="form_amount_to_make"><p></p>
        <table width="313" border="0">
          <tr>
	       <td width="66" height="31"><strong>SG<span class="sup">*</span> :</strong></td>
	       <td width="237"><input name="sg" type="text" id="sg" value="0.985" />
            <strong>ml</strong></td>
          </tr>
	     <tr>
	       <td><strong>Amount:</strong></td>
	       <td><input name="totalAmount" type="text" id="totalAmount" value="100" />
            <strong>ml</strong></td>
          </tr>
        </table>
	    <p>&nbsp;</p>
	    <p>*<a href="https://www.jbparfum.com/knowledge-base/3-specific-gravity-sg/" target="_blank">Specific Gravity of Ethanol</a></p>
	    <div class="modal-footer">
	     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
  		 <input type="submit" name="button" class="btn btn-primary" id="btnUpdate" value="Update Formula">
	   </div>
     </form>
    </div>
  </div>
 </div>
</div>



<script type="text/javascript" language="javascript" >
<?php if($meta['isProtected'] == FALSE){?>
$('#formula_data').editable({
  container: 'body',
  selector: 'td.quantity',
  url: "pages/update_data.php?formula=<?php echo $f_name; ?>",
  title: 'ml',
  type: "POST",
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error'){
			return response.msg; 
		}else{ 
			fetch_formula();
		}
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
        if(response.status == 'error'){
			return response.msg; 
		}else{
			fetch_formula();
		}
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
  selector: 'td.notes',
  url: "pages/update_data.php?formula=<?php echo $f_name; ?>",
  title: 'Notes',
  type: "POST",
  dataType: 'json',
        success: function(response, newValue) {
        if(response.status == 'error'){
			return response.msg; 
		}else{
			fetch_formula();
		}
    },
});

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

<?php } ?>
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
	htmlContent: false
  });
});

$('[data-toggle="tooltip"]').tooltip();
</script>

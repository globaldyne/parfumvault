<?php

require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');

require_once('../func/calcCosts.php');
require_once('../func/calcPerc.php');
require_once('../func/checkDupes.php');
require_once('../func/checkIng.php');
require_once('../func/checkAllergen.php');
require_once('../func/getIngUsage.php');
require_once('../func/checkVer.php');
require_once('../func/formulaProfile.php');
require_once('../func/getIFRAtypes.php');
require_once('../func/searchIFRA.php');
require_once('../func/formatBytes.php');
require_once('../func/countElement.php');
require_once('../func/goShopping.php');
require_once('../libs/fpdf.php');
require_once('../func/genBatchID.php');
require_once('../func/genBatchPDF.php');
require_once('../func/ml2L.php');
require_once('../func/validateFormula.php');
require_once('../func/pvFileGet.php');
require_once('../func/countPending.php');
require_once('../func/countCart.php');

$fid = mysqli_real_escape_string($conn, $_GET['id']);
$f_name = base64_decode($fid);

if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulas WHERE fid = '$fid'")) == 0){
	echo '<div class="alert alert-info alert-dismissible">Incomplete formula.</div>';
	return;
}

$formula_q = mysqli_query($conn, "SELECT * FROM formulas WHERE fid = '$fid' ORDER BY ingredient ASC");
                    

$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '$fid'"));
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$fid'"));

$top_calc = calcPerc($f_name, 'Top', $settings['top_n'], $conn);
$heart_calc = calcPerc($f_name, 'Heart', $settings['heart_n'], $conn);
$base_calc = calcPerc($f_name, 'Base', $settings['base_n'], $conn);
?>
 
<script type='text/javascript'>
$(document).ready(function() {
	$('a[rel=tipsy]').tipsy();
	
	$('.popup-link').magnificPopup({
		type: 'iframe',
  		showCloseBtn: 'true',
  		closeOnBgClick: 'false',
	});
	
    $('#tdData,#tdDataSup,#tdDataCat,#tdDataUsers,#tdDataCustomers').DataTable({
	    "paging":   true,
		"info":   true,
		"lengthMenu": [[20, 35, 60, -1], [20, 35, 60, "All"]]
	});
});  

function updateDB() {
$.ajax({ 
    url: 'pages/operations.php', 
	type: 'GET',
    data: {
		do: "db_update"
		},
	dataType: 'html',
    success: function (data) {
	  $('#msgInfo').html(data);
    }
  });
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
	  	$('#msgInfo').html(data);
		fetch_formula();
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
	  	$('#msgInfo').html(data);
		fetch_formula();
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
			$('#msgInfo').html(data); 
		}else{
			$('#msgInfo').html(data);
			fetch_formula();

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
			$('#msgInfo').html(data); 
		}else{
			$('#msgInfo').html(data);
			//location.reload();
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
			$('#msgInfo').html(data); 
		}else{
			$('#msgInfo').html(data);
			fetch_formula();
	//		location.reload();
		}
	}
    });
});

$(document).ready(function() {
    var groupColumn = 0;
    var table = $('#formula').DataTable({
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
                        '<tr class="group noexport"><td colspan="9">'+group+' Notes</td></tr>'
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
} );
 
</script>

<table class="table table-bordered" <?php if($settings['grp_formula'] == '1'){?>id="formula" <?php } ?>width="100%" cellspacing="0">
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
                      <div class="dropdown-menu">
                        <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
                        <a class="dropdown-item" href="javascript:manageQuantity('multiply')">Multiply x2</a>
                        <a class="dropdown-item" href="javascript:manageQuantity('divide')">Divide x2</a>
                        <a class="dropdown-item" href="javascript:cloneMe()">Clone Formula</a>
             			<div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:addTODO()">To Make</a>
                      </div>
                    </div>
                    </tr>
                    <tr>
                      <?php if($settings['grp_formula'] == '1'){ echo '<th class="noexport"></th>'; } ?>
                      <th width="22%">Ingredient</th>
                      <th width="10%">CAS #</th>
                      <th width="10%">Purity %</th>
                      <th width="10%">Dilutant</th>
                      <th width="10%">Quantity (ml)</th>
                      <th width="10%">Concentration*</th>
                      <th width="10%">Cost</th>
                      <th width="10%">Properties</th>
                      <th class="noexport" width="15%">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="formula_data">
                  <?php while ($formula = mysqli_fetch_array($formula_q)) {
					 	$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT cas, IFRA, price, ml, profile, odor FROM ingredients WHERE BINARY name = '".$formula['ingredient']."'"));

						$limitIFRA = searchIFRA($ing_q['cas'],$formula['ingredient'],null,$conn);
						$limit = explode(' - ', $limitIFRA);
					    $limit = $limit['0'];
					  
					  	$conc = number_format($formula['quantity']/$mg['total_mg'] * 100, 3);
					  	$conc_p = number_format($formula['concentration'] / 100 * $conc, 3);
						
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
						
						echo'<tr>';
						if($settings['grp_formula'] == '1'){
							if(empty($ing_q['profile'])){
								echo '<td class="noexport">Unknown</td>';
							}else{
								echo '<td class="noexport">'.$ing_q['profile'].'</td>';
							}
						}
                      echo '<td align="center" class="'.$ing_q['profile'].'" id="ingredient"><a href="pages/editIngredient.php?id='.$formula['ingredient'].'" class="popup-link">'.$ingName.'</a> '.checkIng($formula['ingredient'],$conn).'</td>
					  <td align="center">'.$ing_q['cas'].'</td>
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
					  echo '<td>'.ucfirst($ing_q['odor']).'</td>';
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
                      <?php if($settings['grp_formula'] == '1'){ echo '<th></th>'; }?>
                      <th width="22%">Total: <?php echo countElement("formulas WHERE name = '$f_name'" ,$conn);?></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th width="15%" align="right"><p>Total: <?php echo ml2l($mg['total_mg'], 3); ?></p></th>
                      <th width="15%">Total: <?php echo array_sum($conc_tot);?>%</th>
                      <th width="15%" align="right">Cost: <?php echo utf8_encode($settings['currency']).number_format(array_sum($tot),3);?></a></th>
                      <th></th>
                      <th class="noexport" width="15%"></th>
                    </tr>
                  </tfoot>                                    
                </table>
                
                
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
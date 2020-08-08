<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/product.php');

require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/calcPerc.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/checkIng.php');
require_once(__ROOT__.'/func/calcCosts.php');
require_once(__ROOT__.'/func/goShopping.php');
require_once(__ROOT__.'/func/countElement.php');
require_once(__ROOT__.'/func/ml2L.php');

$fid = mysqli_real_escape_string($conn, $_GET['fid']);
if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid'")) == FALSE){
	echo 'Formula doesn\'t exist';
	exit;
}

$formula_q = mysqli_query($conn, "SELECT * FROM formulas WHERE fid = '$fid' ORDER BY ingredient ASC");
                    

$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '$fid'"));
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$fid'"));


?><head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="<?php echo trim($product).' - '.trim($ver);?>">
  <meta name="author" content="JBPARFUM">
  <title>Making of <?php echo $meta['name'];?></title>
  
  <link href="../css/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../css/sb-admin-2.css" rel="stylesheet">

  <script src="../js/jquery/jquery.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  
  <link rel="stylesheet" type="text/css" href="../css/datatables.min.css"/>
  <script type="text/javascript" src="../js/datatables.min.js"></script>
  
  <link href="../css/bootstrap.min.css" rel="stylesheet">
    
  <script src="../js/jquery-ui.js"></script>
  
  <link href="../css/jquery-ui.css" rel="stylesheet">
  

  <link href="../css/vault.css" rel="stylesheet">
  
<script type='text/javascript'>
$(document).ready(function() {

	
 

});  

function updateDB() {
	$.ajax({ 
    url: 'pages/operations.php', 
	type: 'get',
    data: {
		do: 'db_update',
		},
	dataType: 'html',
    success: function (data) {
		//location.reload();
	  	$('#msg').html(data);
    }
  });

};

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
		name: "<?php echo $meta['name']; ?>"
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
		formula: "<?php echo $meta['name']; ?>",
		},
	dataType: 'html',
    success: function (data) {
		location.reload();
	  	//$('#msg').html(data);
    }
  });

};


$(document).ready(function() {
    var groupColumn = 0;
    var table = $('#formula').DataTable({
        "columnDefs": [
            { "visible": false, "targets": groupColumn }
        ],
        "order": [[ groupColumn, 'desc' ]],
		"paging":   false,
		"info":   false,
        "drawCallback": function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;
 
            api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group"><td colspan="7">'+group+' Notes</td></tr>'
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
</head>


<div id="content-wrapper" class="d-flex flex-column">
        <div class="container-fluid">
		<div>
          <div class="card shadow mb-4">
            <div class="card-header py-3"> 
              <h2 class="m-0 font-weight-bold text-primary"><a href="#"><?php echo $meta['name']; ?></a></h2>
            </div>
            <div class="card-body">
           <div id="msg"></div>
              <div>
                  <tr>
                    <th colspan="6">&nbsp;</th>
                    </tr>
                    <?php if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulas WHERE fid = '$fid'"))){?>
                <table class="table table-bordered makeFormula" <?php if($settings['grp_formula'] == '1'){?>id="formula" <?php } ?>width="100%" cellspacing="0">
                  <thead>
                    <tr>
                    <?php if($settings['grp_formula'] == '1'){?>
                      <th colspan="7">
                        <?php }else{ ?>
                      <th colspan="6">
                    <?php } ?></th>
                      <th></tr>
                    <tr>
                      <?php if($settings['grp_formula'] == '1'){ echo '<th></th>'; } ?>
                      <th width="22%">Ingredient</th>
                      <th width="10%">Purity %</th>
                      <th width="10%">Dilutant</th>
                      <th width="10%">Quantity (ml)</th>
                      <th width="10%">Concentration*</th>
                      <th width="10%">Cost</th>
                      <th width="15%">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="formula_data">
                  <?php while ($formula = mysqli_fetch_array($formula_q)) {
					 	$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT cas, IFRA, price, ml, profile FROM ingredients WHERE BINARY name = '".$formula['ingredient']."'"));

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
								echo '<td>Unknown</td>';
							}else{
								echo '<td>'.$ing_q['profile'].'</td>';
							}
						}
                      echo '<td align="center" class="'.$ing_q['profile'].'" id="ingredient"><a href="pages/editIngredient.php?id='.$formula['ingredient'].'" class="popup-link">'.$ingName.'</a> '.checkIng($formula['ingredient'],$conn).'</td>
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
					  echo '<td align="center"><a href="#" class="fas fa-exchange-alt replaceIngredient" rel="tipsy" title="Replace '.$formula['ingredient'].'" id="replaceIngredient" data-name="'.$formula['ingredient'].'" data-type="select" data-pk="'.$formula['ingredient'].'" data-title="Choose Ingredient"></a> &nbsp; <a href="'.goShopping($formula['ingredient'],$conn).'" target="_blank" class="fas fa-shopping-cart"></a> &nbsp; <a href="javascript:deleteING(\''.$formula['ingredient'].'\', \''.$formula['id'].'\')" onclick="return confirm(\'Remove '.$formula['ingredient'].' from formula?\');" class="fas fa-trash" rel="tipsy" title="Remove '.$formula['ingredient'].'"></a></td>
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
                      <th width="22%">Total: <?php echo countElement("formulas WHERE fid = '$fid'" ,$conn);?></th>
                      <th></th>
                      <th></th>
                      <th width="15%" align="right"><p>Total: <?php echo ml2l($mg['total_mg'], 3); ?></p></th>
                      <th width="15%">Total: <?php echo array_sum($conc_tot);?>%</th>
                      <th width="15%" align="right">Cost: <?php echo utf8_encode($settings['currency']).number_format(array_sum($tot),2);?></a></th>
                      <th width="15%"></th>
                    </tr>
                  </tfoot>                                    
                </table>
                <?php } ?>
            </div>
          </div>
        </div>
      </div>
   </div>
  </div>

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


$formula_q = mysqli_query($conn, "SELECT * FROM makeFormula WHERE fid = '$fid' ORDER BY toAdd DESC");
while ($formula = mysqli_fetch_array($formula_q)){
	    $form[] = $formula;
}
$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '$fid'"));
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$fid'"));

$settings['grp_formula'] = '0';
$defCatClass = $settings['defCatClass'];

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
  
//UPDATE AMOUNT
function addedToFormula() {
	$.ajax({ 
    url: 'manageFormula.php', 
	type: 'get',
    data: {
		action: "makeFormula",
		q: $("#amountAdded").val(),
		qr: $("#qr").text(),
		ing: $("#ingAdded").text(),
		ingId: $("#ingID").text(),
		fid: "<?php echo $fid; ?>",
		},
	dataType: 'html',
    success: function (data) {
		location.reload();
	  	$('#msg').html(data);
		$('#added').modal('toggle');

    }
  });
};

function addToCart(material, quantity, purity) {
$.ajax({ 
    url: 'manageFormula.php', 
	type: 'get',
    data: {
		action: "addToCart",
		material: material,
		purity: purity,
		quantity: quantity
		},
	dataType: 'text',
    success: function (data) {
	  $('#msg').html(data);
    }
  });
};

function printLabel() {
	<?php if(empty($settings['label_printer_addr']) || empty($settings['label_printer_model'])){?>
	$("#msg").html('<div class="alert alert-danger alert-dismissible">Please configure printer details in <a href="?do=settings">settings<a> page</div>');
	<?php }else{ ?>
	$("#msg").html('<div class="alert alert-info alert-dismissible">Printing...</div>');

$.ajax({ 
    url: 'manageFormula.php', 
	type: 'get',
    data: {
		action: "printLabel",
		type: "ingredient",
		dilution: $("#dilution").val(),
		dilutant: $("#dilutant").val(),
		name: "<?php echo $meta['name']; ?>"
		},
	dataType: 'html',
    success: function (data) {
	  $('#msg').html(data);
    }
  });
	<?php } ?>
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
              <h2><a href="javascript:printLabel()" class="fas fa-print" title="Print label"></a></h2>
            </div>
            <div class="card-body">
           <div id="msg"></div>
              <div>
                  <tr>
                    <th colspan="6">&nbsp;</th>
                    </tr>
                    <?php if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE fid = '$fid'"))){?>
                <table class="table table-bordered makeFormula" <?php if($settings['grp_formula'] == '1'){?>id="formula" <?php } ?>width="100%" cellspacing="0">
                  <thead>
                    <tr>
                    <?php if($settings['grp_formula'] == '1'){?>
                      <th colspan="6">
                        <?php }else{ ?>
                      <th colspan="5">
                    <?php } ?></th>
                      <th></tr>
                    <tr>
                      <?php if($settings['grp_formula'] == '1'){ echo '<th></th>'; } ?>
                      <th width="22%">Ingredient</th>
                      <th width="10%">Purity %</th>
                      <th width="10%">Quantity (<?=$settings['mUnit']?>)</th>
                      <th width="10%">Concentration*</th>
                      <th width="10%">Cost</th>
                      <th width="15%">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="formula_data">
                  <?php foreach ($form as $formula){
					 	$ing_q = mysqli_fetch_array(mysqli_query($conn, "SELECT cas, $defCatClass, price, ml, profile FROM ingredients WHERE BINARY name = '".$formula['ingredient']."'"));

						$limitIFRA = searchIFRA($ing_q['cas'],$formula['ingredient'],null,$conn,$defCatClass);
						$limit = explode(' - ', $limitIFRA);
					    $limit = $limit['0'];
					  
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
						
						if($formula['toAdd'] == '0'){
						  	$class = 'strikeout';
					  	}else{
							$class = "";
						}
						
						echo'<tr class="'.$class.'">';
						if($settings['grp_formula'] == '1'){
							if(empty($ing_q['profile'])){
								echo '<td>Unknown</td>';
							}else{
								echo '<td>'.$ing_q['profile'].'</td>';
							}
						}
                      echo '<td align="center" class="'.$ing_q['profile'].'">'.$ingName.'</a> '.checkIng($formula['ingredient'],$defCatClass,$conn).'</td>';
                      echo '<td align="center">'.$formula['concentration'].'</td>';
					  
					  if($limit != null){
						 if($limit < $conc_p){
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

						  
					  echo '<td align="center" >'.$formula['quantity'].'</td>';
					  echo '<td align="center" '.$IFRA_WARN.'>'.$conc_p.'%</td>';
					  echo '<td align="center">'.utf8_encode($settings['currency']).calcCosts($ing_q['price'],$formula['quantity'], $formula['concentration'], $ing_q['ml']).'</td>';
					  echo '<td align="center">';
	                  
					  if($formula['toAdd'] == '1'){
						  echo '<a href="#" data-toggle="modal" data-target="#added" data-quantity='.$formula['quantity'].' data-ingredient="'.$formula['ingredient'].'" data-ing-id="'.$formula['id'].'" data-qr="'.$formula['quantity'].'" class="fas fa-check" title="Added '.$formula['ingredient'].'"></a>';
					  }
					  
					  echo '&nbsp; &nbsp;';
					  echo '<a href="javascript:addToCart(\''.$formula['ingredient'].'\',\''.$formula['quantity'].'\',\''.$formula['concentration'].'\')" class="fas fa-shopping-cart"></a>'; 
					  echo '</td></tr>';
					  $tot[] = calcCosts($ing_q['price'],$formula['quantity'], $formula['concentration'], $ing_q['ml']);
					  $conc_tot[] = $conc_p;
				  }
                  ?>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <?php if($settings['grp_formula'] == '1'){ echo '<th></th>'; }?>
                      <th width="22%">Total: <?php echo countElement("makeFormula WHERE fid = '$fid'" ,$conn);?></th>
                      <th></th>
                      <th width="15%" align="right"><p>Total: <?php echo ml2l($mg['total_mg'], 3, $settings['mUnit']); ?></p></th>
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
  <script>
  $("a[data-target='#added']").on('click',function(){
  	$("#ingAdded").text($(this).attr('data-ingredient'));
	$("#ingID").text($(this).attr('data-ing-id'));
	$("#amountAdded").val($(this).attr('data-quantity'));
	$("#qr").text($(this).attr('data-qr'));

  });
  </script>
<!-- Modal PRINT-->
<div class="modal fade" id="added" tabindex="-1" role="dialog" aria-labelledby="added" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      <div style="display: none;" id="ingID"></div>
      <div style="display: none;" id="qr"></div>
        <h5 class="modal-title" id="ingAdded"></h5>
      </div>
      <div class="modal-body">
        Amount added:
          <form action="javascript:addedToFormula()" method="get" name="form1" target="_self" id="form1">
          <input name="amountAdded" type="text" id="amountAdded"  />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="button" value="Confirm">
      </div>
     </form>
    </div>
  </div>
</div>
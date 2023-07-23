<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');
//require_once(__ROOT__.'/func/validateFormula.php');
//require_once(__ROOT__.'/func/calcPerc.php');
require_once(__ROOT__.'/func/calcCosts.php');
//require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/getIngSupplier.php');
require_once(__ROOT__.'/libs/fpdf.php');
require_once(__ROOT__.'/func/genBatchID.php');
require_once(__ROOT__.'/func/genBatchPDF.php');


$fid =  mysqli_real_escape_string($conn, $_POST['fid']);
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$fid'"));
/*
$formula_q = mysqli_query($conn, "SELECT * FROM formulas WHERE fid = '".$meta['name']."' ORDER BY ingredient ASC");
while ($formula = mysqli_fetch_array($formula_q)){
    $form[] = $formula;
}
*/
$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '".$fid."'"));
	
$bottle = mysqli_real_escape_string($conn, $_POST['bottle']);
$type = mysqli_real_escape_string($conn, $_POST['type']);
$carrier_id = mysqli_real_escape_string($conn, $_POST['carrier']);
$lid_id = mysqli_real_escape_string($conn, $_POST['lid']);
$bottle_cost = mysqli_fetch_array(mysqli_query($conn, "SELECT price,ml,name FROM bottles WHERE id = '$bottle'"));
	
$carrier_cost = mysqli_fetch_array(mysqli_query($conn, "SELECT price,size FROM suppliers WHERE ingID = '$carrier_id'"));

$defCatClass = mysqli_real_escape_string($conn, $_POST['defCatClass']);


if($_POST['lid']){
	$lid_cost = mysqli_fetch_array(mysqli_query($conn, "SELECT price,style FROM lids WHERE id = '$lid_id'"));
}else{
	$lid_cost['price'] = 0;
	$lid_cost['style'] = 'none';
}
	
$bottle = $bottle_cost['ml'];
$new_conc = $bottle/100*$type;
$carrier = $bottle - $new_conc;

if($_POST['ingSup']){
	$sid = $_POST['ingSup'];
}
if($_POST['batchID'] == '1'){

	define('FPDF_FONTPATH',__ROOT__.'/fonts');
	$batchID = genBatchID();
	
	genBatchPDF($fid,$batchID,$bottle,$new_conc,$mg['total_mg'],$ver,$defCatClass,$settings['qStep'],$conn);
	
}else{
	$batchID = 'N/A';
}
	
?>
<div class="card-body">
    <div class="text-right">
      <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mr2"></i>Actions</button>
            <div class="dropdown-menu dropdown-menu-right">
            	<li><a class="dropdown-item" id="pdf" href="#"><i class="fa-solid fa-file-pdf mr2"></i>Export to PDF</a></li>
                <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#IFRA"><i class="fa-solid fa-certificate mr2"></i>IFRA Certificate</a></li>
                <li><a class="dropdown-item" href="javascript:printLabel()" onclick="return confirm('Print label?')"><i class="fa-solid fa-print mr2"></i>Print Label</a></li>
                <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#printBoxLabel"><i class="fa-solid fa-print mr2"></i>Print Box Label</a></li>
                <li><a class="dropdown-item" href="javascript:BoxLabel('text')"><i class="fa-solid fa-font mr2"></i>View Box Label as text</a></li>
            </div>
      </div>                    
    </div>
</div>

 <div class="card shadow mb-4">
   <div class="card-header py-3"> 
     <h2 class="m-0 font-weight-bold text-primary" id="meta_legend_prod_name"></h2>
     <h5 class="m-1 text-primary" id="meta_legend_formula_name"></h5>
     <h5 class="m-1 text-primary" id="meta_legend_bottle"></h5>
     <h5 class="m-1 text-primary" id="meta_legend_final_type"></h5>
     <h5 class="m-1 text-primary" id="meta_legend_checked_for"></h5>

     <h5 class="m-1 text-primary">Batch ID: <?php if($_POST['batchID'] == '1'){ echo '<a href="/pages/viewDoc.php?type=batch&id='.$batchID.'" target="_blank">'.$batchID.'</a>'; }else{ echo 'N/A';}?></h5>
     <?php if($sid){?>
     <h5 class="m-1 text-primary">Supplier: <strong><?=getSupplierByID($sid,$conn)['name']?></strong></h5>
     <?php } ?>
   </div>
 </div>
         
<div id="ifra_val"></div>
<table id="finishedProduct" class="table table-striped table-bordered nowrap">
    <thead>
        <tr>
            <th>Ingredient</th>
            <th>CAS</th>
            <th>Purity %</th>
            <th>Dilutant</th>
            <th>Quantity</th>
            <th>Concentration %*</th>
            <th>Cost</th>
        </tr>
    </thead>
    <tfoot>
       <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
       </tr>
       <tr>
            <th></th>
            <th></th>
            <th></th>
            <th class="text-center link-primary">Ingredients:</th>
            <th class="text-center link-primary" id="foot_ing_total">0</th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th class="text-center link-primary">Sub total:</th>
            <th class="text-center link-primary" id="foot_sub_total"></th>
            <th class="text-center link-primary">-</th>
            <th class="text-center link-primary"></th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th class="text-center link-primary">Carrier/Solvent:</th>
            <th class="text-center link-primary"><?php echo $carrier; ?> <?=$settings['mUnit']?></th>
            <th class="text-center link-primary"><?php echo $carrier*100/$bottle;?>%</th>
            <th class="text-center link-primary"><?php $carrier_sub_cost = $carrier_cost['price'] / $carrier_cost['size'] * $carrier; echo $settings['currency'].number_format($carrier_sub_cost, $settings['qStep']);?></th>
        </tr>
        
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th class="text-center link-primary">Bottle:</th>
            <th class="text-center text-primary"><?php echo $bottle_cost['ml'];?> <?=$settings['mUnit']?></th>
            <th class="text-center link-primary">-</th>
            <th class="text-center link-primary"><?php echo $settings['currency'].$bottle_cost['price']; ?></th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th class="text-center link-primary">Lid:</th>
            <th class="text-center link-primary"><?php echo $lid_cost['style'];?></th>
            <th class="text-center link-primary">-</th>
            <th class="text-center link-primary"><?php echo $settings['currency'].$lid_cost['price'];?></th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th class="alert alert-warning text-center link-primary">Total:</th>
            <th class="alert alert-warning text-center link-primary" id="foot_total"></th>
            <th class="alert alert-warning text-center link-primary">-</th>
            <th class="alert alert-warning text-center link-primary"></th>
        </tr>
    </tfoot>
    
</table>
<div class="dropdown-divider"></div>

<div class="col-sm-8 pt-3 pb-3">
   *Values in: <strong class="alert alert-danger">red</strong> exceeds usage level,   <strong class="alert alert-warning">yellow</strong> have no usage level set,   <strong class="alert alert-success">green</strong> are within usage level, <strong class="alert alert-info">blue</strong> are exceeding recommended usage level
</div>

<script>

var myFID = "<?=$meta['fid']?>";
var myFNAME = "<?=$meta['name']?>";
var myID = "<?=$meta['id']?>";

var finishedProduct = $('#finishedProduct').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
		{ orderable: false, targets: '_all' },
	],
	dom: 'lrt',
	processing: false,
	ajax: {
		type: 'GET',
		url: '/core/full_formula_data.php',
		data: { 
			id:  myID,
			val_cat: 'cat4',
			final_total_ml: '30',
			final_type_conc: '15'
		},
	 },
	 columns: [
			   { data : 'ingredient.name', title: 'Ingredient'},
			   { data : 'ingredient.cas', title: 'CAS #'},
			   { data : 'purity', title: 'Purity %'},
			   { data : 'dilutant', title: 'Dilutant'},
			   { data : 'quantity', title: 'Quantity (<?=$settings['mUnit']?>)'},
			   { data : 'concentration', title: 'Concentration %'},
			   { data : 'cost', title: 'Cost (<?=$settings['currency']?>)', render: costing},
			  ],
	footerCallback : function( tfoot, data, start, end, display ) {    
  
	  var response = this.api().ajax.json();
	  if(response){

		 $('#foot_ing_total').html( response.meta['total_ingredients'] );
		 $('#foot_total').html( response.meta['total_quantity'] );
		 $('#meta_legend_prod_name').html( response.meta['product_name'] );
		 $('#meta_legend_formula_name').html('Formula name: <strong>'+ response.meta['formula_name'] + '</strong>' );
		 $('#meta_legend_bottle').html('Bottle: <strong>'+ response.compliance['final_total_ml'] + '<?=$settings['mUnit']?></strong>');
		 $('#meta_legend_final_type').html('Concentration: <strong>'+ response.compliance['final_type_conc'] +'%</strong>' );
		$('#meta_legend_checked_for').html('Category Class: <strong>'+ response.compliance['checked_for']+ '<strong>' );


		 if(response.compliance['status'] === 0){
		 	val_msg = '<div class="alert alert-success">'+response.compliance['message']+'</div>';
	  	 }else if (response.compliance['status'] === 1){
			val_msg = '<div class="alert alert-danger">'+response.compliance['message']+'</div>';
		 }else{
			val_msg = '<div class="alert alert-danger">Unable to validate against IFRA Library</div>';
		 }
		
		 $('#ifra_val').html( val_msg );

	 }
  },
  
	order: [[ 0, 'asc' ]],
	lengthMenu: [[200, 500, -1], [200, 500, "All"]],
	pageLength: 200,
	displayLength: 200,
	createdRow: function( row, data, dataIndex){
		if( data['usage_regulator'] == "IFRA" && parseFloat(data['usage_limit']) < parseFloat(data['concentration'])){
			$(row).find('td:eq(5)').addClass('alert-danger').append(' <i rel="tip" title="Max usage: ' + data['usage_limit'] +'% IFRA Regulated" class="pv_point_gen fas fa-info-circle"></i></div>');
		}else if( data['usage_regulator'] == "PV" && parseFloat(data['usage_limit']) < parseFloat(data['concentration'])){
			if(data['usage_restriction'] == 1){
				$(row).find('td:eq(5)').addClass('alert-info').append(' <i rel="tip" title="Recommended usage: ' + data['usage_limit'] +'%" class="pv_point_gen fas fa-info-circle"></i></div>');
			}
			if(data['usage_restriction'] == 2){
				$(row).find('td:eq(5)').addClass('alert-danger').append(' <i rel="tip" title="Restricted usage: ' + data['usage_limit'] +'%" class="pv_point_gen fas fa-info-circle"></i></div>');
			}
			if(data['usage_restriction'] == 3){
				$(row).find('td:eq(5)').addClass('alert-warning').append(' <i rel="tip" title="Specification: ' + data['usage_limit'] +'%" class="pv_point_gen fas fa-info-circle"></i></div>');
			}

		}else{
			$(row).find('td:eq(5)').addClass('alert-success');
		}
		
		if(data.ingredient.classification == 4){
			$(row).find('td:eq(0),td:eq(1),td:eq(5)').addClass('alert-danger').append(' <i rel="tip" title="This material is prohibited" class="pv_point_gen fas fa-ban"></i></div>');
		}		
    },
	  
});

function costing(data, type, row, meta){
		//$sub = $price / $ml * $quantity;
		//$total = $concentration / 100 * $sub;
		//return number_format($total,3);
	<?=calcCosts( getSingleSupplier($sid,$ing_q['id'],$conn)['price'], $new_quantity, $formula['concentration'], getPrefSupplier($ing_q['id'],$conn)['size']);?>
	
	var data = row.cost / ;
	
	return data;
}
	
	

$('#pdf').on('click',function(){
  $("#finishedProduct").tableHTMLExport({
		type:'pdf',
		filename: myFNAME +'.pdf',
		orientation: 'p',
		trimContent: true,
    	quoteFields: true,
		ignoreColumns: '.noexport',
  		ignoreRows: '.noexport',
		htmlContent: true,
		maintitle: myFNAME,
		product: '<?php echo trim($product).' '.trim($ver);?>'
	});
});
</script>

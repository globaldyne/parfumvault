<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');

if(!$_POST['id']){
	echo 'Formula id is missing.';
	return;
}
	
$id = mysqli_real_escape_string($conn, $_POST['id']);

if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE id = '$id'")) == FALSE){
	echo '<div class="alert alert-info alert-dismissible">Incomplete formula. Please add ingredients.</div>';
	return;
}
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,fid,name,notes FROM formulasMetaData WHERE id = '$id'"));
$f_name = $meta['name'];
$fid = $meta['fid'];
?>


<div class="mt-4 mr-4 text-right">
   <div class="btn-group" id="menu">
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
        <div class="dropdown-menu dropdown-menu-left">
           <li><a href="#" class="dropdown-item" id="export_pdf"><i class="fa-solid fa-file-export mx-2"></i>Export to PDF</a></li>
        </div>
    </div>
</div>
<hr />
<table id="formula" class="table table-striped">
    <thead>
        <tr>
            <th>Ingredient</th>
            <th>CAS</th>
            <th>Purity %</th>
            <th>Dilutant</th>
            <th>Quantity</th>
            <th>Concentration %*</th>
            <th>Properties</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
        <th>Total ingredients:</th>
        <th></th>
        <th></th>
        <th></th>
        <th>Total ml:</th>
        <th></th>
        <th></th>
        </tr>
    </tfoot>
</table>


<script>
var myFID = "<?=$meta['fid']?>";
var myFNAME = "<?=$meta['name']?>";
var watermarkText = "<?=$_POST['watermarkText']?>";
var watermarkTextOp =  parseFloat("<?=$_POST['watermarkTextOp']?>");
var orientation = "<?=$_POST['orientation']?>";
var qStep = "<?=$_POST['qStep']?>";
var fid = "<?=$id?>";
var fontSize = parseInt("<?=$_POST['fontSize']?>");
var image = "<?php echo $settings['brandLogo'] ?: "data:image/png;base64,".base64_encode(file_get_contents(__ROOT__.'/img/logo_def.png')); ?>";

var logoSizeW = parseInt("<?=$_POST['logoSizeW'] ?: 200 ?>");
var logoSizeH = parseInt("<?=$_POST['logoSizeH'] ?: 200 ?>");

var formula_table = $('#formula').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
		{ orderable: true, targets: '_all' },
	],
	dom: 'lrt',
	buttons: [
      {
        extend: "pdfHtml5",
		footer: true,
		orientation: orientation,
        title: myFNAME,
        messageBottom: function(){return new Date().toString()},
        messageTop: $("#customerID").val(),
		customize: function ( doc ) {
			doc.styles.tableHeader.fontSize = fontSize;
			doc.styles.tableFooter.fontSize = fontSize;
			doc.content.splice( 1, 0, {
				margin: [ 0, 0, 0, 12 ],
				alignment: 'center',
				image: image,
				width: logoSizeW,
				height: logoSizeH,
			});
			doc.watermark =  {text: watermarkText, color: 'blue', opacity: watermarkTextOp,  bold: false, italics: false};
			doc.defaultStyle.fontSize = fontSize;          
		},
      }
    ],
	processing: false,
	ajax: {
		url: '/core/full_formula_data.php',
		data: {
			id: fid,
			qStep: qStep
		}
	 },
	 columns: [
			   { data : 'ingredient.name', title: 'Ingredient'},
			   { data : 'ingredient.cas', title: 'CAS#'},
			   { data : 'purity', title: 'Purity%'},
			   { data : 'dilutant', title: 'Dilutant'},
			   { data : 'quantity', title: 'Quantity(<?=$settings['mUnit']?>)'},
			   { data : 'concentration', title: 'Concentration%'},
			   { data : 'ingredient.desc', title: 'Properties'},
			  ],
	 footerCallback : function( tfoot, data, start, end, display ) {    
  
		 var response = this.api().ajax.json();
		 if(response){
			 var $td = $(tfoot).find('th');
			 $td.eq(0).html("Ingredients: " + response.meta['total_ingredients'] );
			 $td.eq(4).html("Total: " + response.meta['total_quantity']);
		 }
  	},
  
	order: [[ 0, 'asc' ]],
	lengthMenu: [[200, 500, -1], [200, 500, "All"]],
	pageLength: 200,
	displayLength: 200
	  
});

$('#export_pdf').click(() => {
    $('#formula').DataTable().button(0).trigger();
});
</script>
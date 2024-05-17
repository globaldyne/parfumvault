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


<div>
   <div class="btn-group noexport" id="menu">
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
        <div class="dropdown-menu dropdown-menu-left">
           <li><a href="#" class="dropdown-item" id="export_pdf"><i class="fa-solid fa-file-export mx-2"></i>Export to PDF</a></li>
        </div>
    </div>
</div>
<hr />
<table id="formula" class="table table-striped table-bordered nowrap">
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
var watermarkTextSize = "<?=$_POST['watermarkTextSize']?>";
var orientation = "<?=$_POST['orientation']?>";
var qStep = "<?=$_POST['qStep']?>";

var formula_table = $('#formula').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
		{ orderable: true, targets: '_all' },
	],
	dom: 'lrt',
	buttons: [
      {
        extend: "pdfHtml5",
		orientation: "landscape",
        title: myFNAME,
        messageBottom: function(){return new Date().toString()},
        messageTop: $("#customerID").val()
      }
    ],
	processing: false,
	ajax: {
		url: '/core/full_formula_data.php',
		data: {
			id: <?=$id?>,
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


	
$('#export_pdf').on('click',function(){
  $("#formula").tableHTMLExport({
	type:'pdf',
	filename: myFNAME + '.pdf',
	orientation: orientation,
	trimContent: true,
    quoteFields: true,
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',
	htmlContent: true,
	cover: '<?php echo base64_encode(wordwrap($meta['notes'],100));?>',
	maintitle: myFNAME,
	subtitle: $("#customerID").val(),
	product: '<?php echo trim($product).' '.trim($ver);?>',
	watermarkText: watermarkText,
	watermarkTextSize: watermarkTextSize
  });
});

</script>
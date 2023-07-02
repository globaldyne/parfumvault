<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');

if(!$_GET['id']){
	echo 'Formula id is missing.';
	return;
}
	
$id = mysqli_real_escape_string($conn, $_GET['id']);

if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE id = '$id'")) == 0){
	echo '<div class="alert alert-info alert-dismissible">Incomplete formula. Please add ingredients.</div>';
	return;
}
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,fid,name,notes FROM formulasMetaData WHERE id = '$id'"));
$f_name = $meta['name'];
$fid = $meta['fid'];
?>


<div>
   <div class="btn-group noexport" id="menu">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mr2"></i>Actions</button>
        <div class="dropdown-menu dropdown-menu-left">
           <li><a href="#" class="dropdown-item" id="export_pdf"><i class="fa-solid fa-file-export mr2"></i>Export to PDF</a></li>
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

var formula_table = $('#formula').DataTable( {
	columnDefs: [
		{ className: 'text-center', targets: '_all' },
		{ orderable: false, targets: '_all' },
	],
	dom: 'lrt',
	processing: false,
	ajax: {
		url: '/core/full_formula_data.php?id=<?=$id?>'
	 },
	 columns: [
			   { data : 'ingredient.name', title: 'Ingredient'},
			   { data : 'ingredient.cas', title: 'CAS#'},
			   { data : 'purity', title: 'Purity %'},
			   { data : 'dilutant', title: 'Dilutant'},
			   { data : 'quantity', title: 'Quantity (<?=$settings['mUnit']?>)'},
			   { data : 'concentration', title: 'Concentration %'},
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
	orientation: 'p',
	trimContent: true,
    quoteFields: true,
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',
	htmlContent: true,
	cover: '<?php echo base64_encode(wordwrap($meta['notes'],100));?>',
	maintitle: myFNAME,
	subtitle: $("#customerID").val(),
	product: '<?php echo trim($product).' '.trim($ver);?>'
  });
});

</script>
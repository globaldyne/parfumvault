<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

$id_a = mysqli_real_escape_string($conn, $_POST['id_a']);
$id_b = mysqli_real_escape_string($conn, $_POST['id_b']);


?>

<script>

$(document).ready(function() {
  var formula_a_length;
  
  var formula_a_table = $('#formula_a_table').DataTable( {
		dom: 'lfrtip',
        language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: "Incomplete formula.",
			search: "Search in formula:",
		},
		ajax: {
    		url: '/core/full_formula_data.php?id=<?=$id_a?>'
 		 },
		columns: [
				   { data : 'ingredient.name', title: 'Ingredient'},
				   { data : 'purity', title: 'Purity %'},
				   { data : 'quantity', title: 'Quantity'},
				  ],
		lengthMenu: [[50, 100, 200, -1], [50, 100, 200, "All"]],
        pageLength: 100,
		displayLength: 100,
});
  
formula_a_table.on('draw', function () {
	formula_a_length = formula_a_table.rows().count();
});


var url = '/core/full_formula_data.php?id=<?=$id_b?>';
<?php if($_POST['revID']){ ?>
	var url = '/core/full_revision_data.php?fid=<?=$_POST['fid']?>&revID=<?=$_POST['revID']?>';
<?php } ?>
var formula_b_table = $('#formula_b_table').DataTable({
		dom: 'lfrtip',
        language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: "Incomplete formula.",
			search: "Search in formula:",
			},
    	ajax: {
    		url: url
 		},
		columns: [
				   { data : 'ingredient.name', title: 'Ingredient'},
				   { data : 'purity', title: 'Purity %'},
				   { data : 'quantity', title: 'Quantity'},
				  ],
	    lengthMenu: [[50, 100, 200, -1], [50, 100, 200, "All"]],
        pageLength: 100,
		displayLength: 100,
		rowCallback: function (formula_b_tableRow, formula_b_tableData) {
			for ( var y=0; y < formula_a_length; y++ ) {
				var formula_a_data = formula_a_table.row(y).data();
				if (formula_a_data.quantity === formula_b_tableData.quantity &&
				   formula_a_data.ingredient.name === formula_b_tableData.ingredient.name) {
				   $(formula_b_tableRow).removeClass().addClass('badge-success');
				   break; 
				}else{
				   $(formula_b_tableRow).removeClass().addClass('pv_formula_diff');
				}
			}
    	}
	});
	
});//doc ready


</script>
<div class="compare">
    <div class="cmp_a">
        <table id="formula_a_table" class="table table-striped table-bordered nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Ingredient</th>
                    <th>Purity %</th>
                    <th>Quantity</th>
                </tr>
            </thead>
        </table>
    </div>
        <div class="cmp_a">
        <table id="formula_b_table" class="table table-striped table-bordered nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Ingredient</th>
                    <th>Purity %</th>
                    <th>Quantity</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
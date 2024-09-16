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
  var formula_a_name = '<?=$_POST['name_a']?>'; // Replace with dynamic name if needed
  var formula_b_name = '<?=$_POST['name_b']?>'; //
  
  var formula_a_table = $('#formula_a_table').DataTable({
    dom: '<"top"f><"formula-name-a">rt<"bottom"lip><"clear">',
	language: {
      loadingRecords: '&nbsp;',
      processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
      emptyTable: "Incomplete formula.",
      search: "",
      searchPlaceholder: 'Search by name',
    },
    ajax: {
      url: '/core/full_formula_data.php?id=<?=$id_a?>'
    },
    columns: [
      { data: 'ingredient.name', title: 'Ingredient' },
      { data: 'purity', title: 'Purity %' },
      { data: 'concentration', title: 'Concentration %' }
    ],
    lengthMenu: [[50, 100, 200, -1], [50, 100, 200, "All"]],
    pageLength: 100,
    displayLength: 100,
	drawCallback: function() {
		extrasShow();
	},
  });
  
  $('<div class="formula-name" style="float:left; font-weight:bold; margin-top:-35px;"><a href="/?do=Formula&id=<?=$id_a?>" target="_blank">' + formula_a_name + '<i class="fa-solid fa-arrow-up-right-from-square ml-2"></i></a></div>').appendTo('.formula-name-a');

  formula_a_table.on('draw', function () {
    formula_a_length = formula_a_table.rows().count();
  });

  var url = '/core/full_formula_data.php?id=<?=$id_b?>';
  <?php if($_POST['revID']) { ?>
  var url = '/core/full_revision_data.php?fid=<?=$_POST['fid']?>&revID=<?=$_POST['revID']?>';
  <?php } ?>

  var formula_b_table = $('#formula_b_table').DataTable({
    dom: '<"top"f><"formula-name-b">rt<"bottom"lip><"clear">',
    language: {
      loadingRecords: '&nbsp;',
      processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
      emptyTable: "Incomplete formula.",
      search: "",
      searchPlaceholder: 'Search by name'
    },
    ajax: {
      url: url
    },
    columns: [
      { data: 'ingredient.name', title: 'Ingredient' },
      { data: 'purity', title: 'Purity %' },
      { data: 'concentration', title: 'Concentration %' }
    ],
    lengthMenu: [[50, 100, 200, -1], [50, 100, 200, "All"]],
    pageLength: 100,
    displayLength: 100,
	drawCallback: function() {
		extrasShow();
	},
    rowCallback: function (formula_b_tableRow, formula_b_tableData) {
      var isMatching = false;
      var comparisonIcon = '';
      var formula_a_data;

      for (var y = 0; y < formula_a_length; y++) {
        formula_a_data = formula_a_table.row(y).data();

        if (formula_a_data.ingredient.name === formula_b_tableData.ingredient.name) {
          isMatching = true;

          if (parseFloat(formula_b_tableData.concentration) > parseFloat(formula_a_data.concentration)) {
            comparisonIcon = '<i class="fa-solid fa-arrow-trend-up mx-2" rel="tip" title="Value has been increased"></i>';
          } else if (parseFloat(formula_b_tableData.concentration) < parseFloat(formula_a_data.concentration)) {
            comparisonIcon = '<i class="fa-solid fa-arrow-trend-down mx-2" rel="tip" title="Value has been decreased"></i>';
          }

          $('td:eq(2)', formula_b_tableRow).html(formula_b_tableData.concentration + ' ' + comparisonIcon);
          break;
        }
      }

      if (!isMatching) {
        $(formula_b_tableRow).removeClass().addClass('pv_formula_added');
 		var currentHtml = $('td:eq(2)', formula_b_tableRow).html();
        $('td:eq(2)', formula_b_tableRow).html(currentHtml + ' <i class="fa-solid fa-circle-plus mx-2" rel="tip" title="Ingredient has been added"></i>');

      } else {
        if (comparisonIcon !== '') {
          $(formula_b_tableRow).removeClass().addClass('pv_formula_diff');
        } else {
          $(formula_b_tableRow).removeClass().addClass('pv_formula_nodiff');
        }
      }
    }
  });
  
  $('<div class="formula-name" style="float:left; font-weight:bold; margin-top:-35px;"><a href="/?do=Formula&id=<?=$id_b?>" target="_blank">' + formula_b_name + '<i class="fa-solid fa-arrow-up-right-from-square ml-2"></i></a></div>').appendTo('.formula-name-b');

  formula_b_table.on('draw', function () {
    formula_b_length = formula_b_table.rows().count();

    formula_a_table.rows().every(function () {
      var formula_a_data = this.data();
      var isFoundInB = false;

      for (var x = 0; x < formula_b_length; x++) {
        var formula_b_data = formula_b_table.row(x).data();

        if (formula_a_data.ingredient.name === formula_b_data.ingredient.name) {
          isFoundInB = true;
          break;
        }
      }

      if (!isFoundInB) {
        $(this.node()).addClass('pv_formula_missing');
        var currentHtml = $('td:eq(2)', this.node()).html();
        $('td:eq(2)', this.node()).html(currentHtml + ' <i class="fa-solid fa-circle-minus mx-2" rel="tip" title="Ingredient has been removed"></i>');

      }
    });
  });

  function extrasShow() {
	$('[rel=tip]').tooltip({
		"html": true,
		"delay": {"show": 100, "hide": 0},
	 });
  };
	
});


</script>
<div class="compare">
    <div class="cmp_a">
        <table id="formula_a_table" class="table table-striped nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Ingredient</th>
                    <th>Purity %</th>
                    <th>Concentration %</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="cmp_a">
    	<table id="formula_b_table" class="table table-striped nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Ingredient</th>
                    <th>Purity %</th>
                    <th>Concentration %</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
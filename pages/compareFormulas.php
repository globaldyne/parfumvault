<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
	<div class="container-fluid">
		<div>
          <div class="card shadow mb-4">
            <div class="card-header py-3"> 
            <h2 class="m-0 font-weight-bold text-primary"><a href="?do=compareFormulas">Compare formulas</a></h2>
          </div>
          <div class="card-body">
            <table width="100%" border="0">
              <tr>
                <td width="9%">Formula A:</td>
                <td width="24%">
                <select name="formula_a" id="formula_a" class="form-control selectpicker" data-live-search="true">
                 <?php
                    $a = mysqli_query($conn, "SELECT id,name FROM formulasMetaData ORDER BY name ASC");
                    while ($formula_a = mysqli_fetch_array($a)){
                        echo '<option value="'.$formula_a['id'].'">'.$formula_a['name'].'</option>';
                    }
                  ?>
                 </select>
               </td>
                <td width="67%">&nbsp;</td>
              </tr>
              <tr>
                <td>Formula B:</td>
                <td>
                <select name="formula_b" id="formula_b" class="form-control selectpicker" data-live-search="true">
                <?php
                    $b = mysqli_query($conn, "SELECT id,name FROM formulasMetaData ORDER BY name ASC");
                    while ($formula_b = mysqli_fetch_array($b)){
                        echo '<option value="'.$formula_b['id'].'">'.$formula_b['name'].'</option>';
                    }
                ?>
                </select>
                </td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td><input type="submit" name="button" class="btn btn-info" id="compare-btn" value="Compare Formulas"></td>
                <td colspan="2">&nbsp;</td>
              </tr>
            </table>
           	<div id="cmp"></div>
           </div>
        </div>
      </div>
	</div>
</div>
<script>
$('.card-body').on('click', '[id*=compare-btn]', function () {
	$('#cmp').html('Loading...');
	var formula = {};
	formula.A = $("#formula_a").val();
	formula.B = $("#formula_b").val();
		$.ajax({ 
			url: '/pages/cmp_formulas_data.php', 
			type: 'GET',
			data: {
				id_a: formula.A,
				id_b: formula.B,
				},
			dataType: 'html',
			success: function (data) {
				$('#cmp').html(data);
			}
		  });				
});
</script>
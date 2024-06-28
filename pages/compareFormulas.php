<div id="content-wrapper" class="d-flex flex-column">
  <?php require_once(__ROOT__.'/pages/top.php'); ?>
  <div class="container-fluid">
    <div>
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h2 class="m-0 fw-bold text-primary">
            <a href="/?do=compareFormulas">Compare formulas</a>
          </h2>
        </div>
        <div class="card-body">
          <?php 
            if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData"))== 0){
              echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> You need to <a href="?do=listFormulas">create</a> at least one formula first.</div>';
              return;
            }
            
            if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE type = 'Carrier' OR type = 'Solvent'"))== 0){
              echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> You need to <a href="?do=ingredients">add</a> at least one solvent or carrier first.</div>';
              return;
            }
          ?>
          <div>
            <div class="mb-3 row">
              <label for="formula_a" class="col-sm-1 col-form-label">Formula A</label>
              <div class="col-sm-10">
                <select name="formula_a" id="formula_a" class="form-control selectpicker" data-live-search="true">
                  <?php
                    $a = mysqli_query($conn, "SELECT id,name FROM formulasMetaData ORDER BY name ASC");
                    while ($formula_a = mysqli_fetch_array($a)){
                      echo '<option value="'.$formula_a['id'].'">'.$formula_a['name'].'</option>';
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <label for="formula_b" class="col-sm-1 col-form-label">Formula B</label>
              <div class="col-sm-10">
                <select name="formula_b" id="formula_b" class="form-control selectpicker" data-live-search="true">
                  <?php
                    $b = mysqli_query($conn, "SELECT id,name FROM formulasMetaData ORDER BY name ASC");
                    while ($formula_b = mysqli_fetch_array($b)){
                      echo '<option value="'.$formula_b['id'].'">'.$formula_b['name'].'</option>';
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <div class="col-sm-1">
                <button type="submit" class="btn btn-primary" id="btnCMP">Compare</button>
              </div>
            </div>
          </div>
          <div id="cmp_results"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$('#btnCMP').click(function() {
	$.ajax({ 
		url: '/pages/views/formula/cmp_formulas_data.php', 
		type: 'POST',
		data: {
			id_a: $("#formula_a").val(),			
			id_b: $("#formula_b").val(),
			},
		dataType: 'html',
		success: function (data) {
			$('#cmp_results').html(data);
		}
	  });
});
</script>
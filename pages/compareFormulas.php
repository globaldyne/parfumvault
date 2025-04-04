<div id="content-wrapper" class="d-flex flex-column">
  <?php require_once(__ROOT__.'/pages/top.php'); ?>
  <div class="container-fluid">
    <div>
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h2 class="m-0 fw-bold text-primary-emphasis">Compare formulas</h2>
        </div>
        <div class="card-body">
          <?php 
            if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE owner_id = '$userID'")))){
              echo '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>You need to <a href="/?do=listFormulas">create</a> at least one formula first.</div>';
              return;
            }
            
            if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE (type = 'Carrier' OR type = 'Solvent')  AND owner_id = '$userID' ")))){
              echo '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>You need to <a href="/?do=ingredients">add</a> at least one solvent or carrier first.</div>';
              return;
            }
          ?>
          <div>
            <div class="mb-3 row">
              <label for="formula_a" class="col-form-label">Formula A</label>
              <div class="col-sm-10">
                <select name="formula_a" id="formula_a" class="form-control selectpicker" data-live-search="true">
                  <?php
                    $a = mysqli_query($conn, "SELECT id,name FROM formulasMetaData WHERE owner_id = '$userID' ORDER BY name ASC");
                    while ($formula_a = mysqli_fetch_array($a)){
                      echo '<option value="'.$formula_a['id'].'">'.$formula_a['name'].'</option>';
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <label for="formula_b" class="col-form-label">Formula B</label>
              <div class="col-sm-10">
                <select name="formula_b" id="formula_b" class="form-control selectpicker" data-live-search="true">
                  <?php
                    $b = mysqli_query($conn, "SELECT id,name FROM formulasMetaData WHERE owner_id = '$userID' ORDER BY name ASC");
                    while ($formula_b = mysqli_fetch_array($b)){
                      echo '<option value="'.$formula_b['id'].'">'.$formula_b['name'].'</option>';
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <div class="col-sm-1">
                <button type="button" class="btn btn-primary" id="btnCMP">Compare</button>
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
$(document).ready(function() {
    $('#btnCMP').click(function() {
        var formulaA = $("#formula_a").val();
        var formulaB = $("#formula_b").val();

        if (!formulaA || !formulaB) {
            alert("Please select both formulas to compare.");
            return;
        }

        $('#cmp_results').html('<div class="alert alert-info"><i class="fa fa-spinner fa-spin mx-2"></i>Comparing formulas, please wait...</div>');

        $.ajax({ 
            url: '/pages/views/formula/cmp_formulas_data.php', 
            type: 'POST',
            data: {
                id_a: formulaA,
                name_a: $("#formula_a option:selected").text(),
                id_b: formulaB,
                name_b: $("#formula_b option:selected").text(),
            },
            dataType: 'html',
            success: function (data) {
                $('#cmp_results').html(data);
            },
            error: function (xhr, status, error) {
                $('#cmp_results').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle mx-2"></i>Error comparing formulas: ' + error + '</div>');
            }
        });
    });
});
</script>
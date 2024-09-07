<?php 
if (!defined('pvault_panel')){ die('Not Found');}
				
require_once(__ROOT__.'/func/profileImg.php');
require_once(__ROOT__.'/func/php-settings.php');

$res_ingProfiles = mysqli_query($conn, "SELECT id,name FROM ingProfiles ORDER BY name ASC");
$res_ingCategory = mysqli_query($conn, "SELECT id,image,name,notes FROM ingCategory ORDER BY name ASC");

?>
<style>
.mfp-iframe-holder .mfp-content {
    line-height: 0;
    width: 80%;
    max-width: 100%; 
	height: 1300px;
}
</style>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="#" id="mainTitle">Ingredients</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                 <div id="list_ingredients">
                 	<div class="loader-center">
                		<div class="loader"></div>
                    	<div class="loader-text"></div>
                     </div>
                    </div>
                 </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

<!--ADV SEARCH-->
<div class="modal fade" id="adv_search" data-bs-backdrop="static" tabindex="-1" aria-labelledby="adv_search_label" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="adv_search_label">Advanced Search</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12">
          <div id="advsearchmsg"></div>
          <div class="mb-3 row">
            <div class="col-sm">
              <label for="ing_name" class="col-form-label">Ingredient Name</label>
              <input type="text" class="form-control" id="ing_name" placeholder="Any">
            </div>
          </div>
          <div class="mb-3 row">
            <div class="col-sm">
              <label for="ing_cas" class="col-form-label">CAS#</label>
              <input type="text" class="form-control" id="ing_cas" placeholder="Any">
            </div>
          </div>
          <div class="mb-3 row">
            <div class="col-sm">
              <label for="ing_einecs" class="col-form-label">EINECS</label>
              <input type="text" class="form-control" id="ing_einecs" placeholder="Any">
            </div>
          </div>
          <div class="mb-3 row">
            <div class="col-sm">
              <label for="ing_synonym" class="col-form-label">Synonym</label>
              <input type="text" class="form-control" id="ing_synonym" placeholder="Any">
            </div>
          </div>
          <div class="mb-3 row">
            <div class="col-sm">
              <label for="ing_odor" class="col-form-label">Odor</label>
              <input type="text" class="form-control" id="ing_odor" placeholder="Any">
            </div>
          </div>
          <div class="mb-3 row">
            <div class="col-sm">
              <label for="ing_profile" class="col-form-label">Profile</label>
              <select name="profile" id="ing_profile" class="form-control selectpicker" data-live-search="true">
                <option value="" selected>Any</option>
                <?php while ($row_ingProfiles = mysqli_fetch_array($res_ingProfiles)){ ?>
                  <option data-content="<img class='img_ing_sel' src='<?=profileImg($row_ingProfiles['name'])?>'> <?=$row_ingProfiles['name']?>" value="<?=$row_ingProfiles['name']?>"></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="mb-3 row">
            <div class="col-sm">
              <label for="ing_category" class="col-form-label">Category</label>
              <select name="category" id="ing_category" class="form-control selectpicker" data-live-search="true">
                <option value="" selected>Any</option>
                <?php while ($row_ingCategory = mysqli_fetch_array($res_ingCategory)){ ?>
                  <option data-content="<img class='img_ing_sel' src='<?php if($row_ingCategory['image']){ echo $row_ingCategory['image']; }else{ echo '/img/molecule.png';}?>'><?=$row_ingCategory['name']?>" value="<?=$row_ingCategory['id'];?>"></option>
                <?php } ?>
              </select>
            </div>
          </div>  
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="btnAdvSearch" value="Search">
      </div>
    </div>
  </div>
</div>
 



<!--IMPORT JSON MODAL-->
<div class="modal fade" id="import_ingredients_json" data-bs-backdrop="static" tabindex="-1" aria-labelledby="importIngredientsJsonLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="importIngredientsJsonLabel">Import Ingredients from a JSON File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="JSRestMsg"></div>
        <div class="progress mb-3">
          <div id="uploadProgressBar" class="progress-bar" role="progressbar" aria-valuemin="0"></div>
        </div>
        <div id="backupArea">
          <div class="mb-3">
            <label for="backupFile" class="form-label">JSON file:</label>
            <input type="file" name="backupFile" id="backupFile" class="form-control" />
          </div>
          <div>
          <div class="mt-2 alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i>
          	<strong>IMPORTANT</strong>
            <ul>
              <li><div id="raw" data-size="<?=getMaximumFileUploadSizeRaw()?>">Maximum file size: <strong><?=getMaximumFileUploadSize()?></strong></div></li>
              <li>Any ingredient with the same ID will be replaced. Please make sure you have taken a backup before importing a JSON file.</li>
            </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK">Cancel</button>
        <input type="submit" name="btnRestore" class="btn btn-primary" id="btnRestoreIngredients" value="Import">
      </div>
    </div>  
  </div>
</div>


<!--CSV IMPORT-->
<div class="modal fade" id="csv_import" data-bs-backdrop="static" tabindex="-1" aria-labelledby="csvImportLabel" aria-hidden="true">
  <div class="modal-dialog pv-modal-xxl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="csvImportLabel">Import Ingredients from CSV File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="CSVImportMsg"></div>
        <div id="process_area">
          <div class="mb-3">
            <label for="CSVFile" class="form-label">CSV File:</label>
            <input type="file" class="form-control" id="CSVFile" name="CSVFile">
          </div>
        </div>
        <div id="step_upload" class="modal-body"></div>
        <div class="mt-2 alert alert-info"><i class="fa-solid fa-circle-info mx-2"></i>
          Select and match the fields in your CSV file. If a column isn't applicable, set it to <strong>None</strong>. Any existing data in your database will not be replaced or updated if it already exists in the CSV.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseCsv">Cancel</button>
        <input type="submit" class="btn btn-primary" id="btnImportCSV" value="Import">
      </div>
    </div>
  </div>
</div>




<script>
$(document).ready(function() {
	
	$('#mainTitle').click(function() {
		list_ingredients();
	});
	
	list_ingredients();
	var pvOnlineAPI = '<?php echo $pvOnlineAPI; ?>';
	$(function () {
		$(".input-group-btn .dropdown-menu li a").click(function () {
			var selText = $(this).html();
			var provider = $(this).attr('id');
			  
			$(this).parents(".input-group-btn").find(".btn-search").html(selText);
			$(this).parents(".input-group-btn").find(".btn-search").attr('id',provider);
		});
	});
	
	$('#btnAdvSearch').click(function() {
		var name = $('#ing_name').val();
		var cas = $('#ing_cas').val();
		var einecs = $('#ing_einecs').val();
		var odor = $('#ing_odor').val();
		var profile = $('#ing_profile').val();
		var cat = $('#ing_category').val();
		var synonym = $('#ing_synonym').val();
	
		$.ajax({ 
			url: '/pages/listIngredients.php',
			type: 'GET',
			data: {
				"adv": 1,
				"name": name,
				"cas": cas,
				"einecs": einecs,
				"odor": odor,
				"profile": profile,
				"cat": cat,
				"synonym": synonym
			},
			dataType: 'html',
				success: function (data) {
					$('#list_ingredients').html(data);
			},
			error: function (xhr, status, error) {
				$('#advsearchmsg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-xmark mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error +'</div>');
			}
		});
	});
	
	function list_ingredients(page, limit, filter) {
		$('#list_ingredients').html('<img class="loader loader-center" src="/img/Testtube.gif"/>');
		$.ajax({
			url: '/pages/listIngredients.php',
			type: 'GET',
			data: {
				search: '<?= htmlspecialchars($_GET['search']) ?>'
			},
			dataType: 'html',
			success: function (data) {
				$('#list_ingredients').html(data);
			},
			error: function (xhr, status, error) {
				$('#list_ingredients').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-xmark mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error +'</div>');
			}
		});
	};

});

</script>

<script src="/js/ingredients.js"></script>
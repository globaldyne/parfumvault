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
		<div id="innermsg"></div>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="javascript:list_ingredients()">Ingredients</a></h2>
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
<div class="modal fade" id="adv_search" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="adv_search" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Advanced Search</h5>
      </div>
      <div class="modal-body">
          <div class="col-sm-12">
          
    		  <div class="mb-1 row">
                <div class="col-sm">
                	<label for="ing_name" class="col-sm col-form-label">Ingredient name</label>
                  	<input type="text" class="form-control" id="ing_name" placeholder="Any">
                </div>
              </div>
    		  <div class="mb-1 row">
                <div class="col-sm">
                	<label for="ing_cas" class="col-sm col-form-label">CAS#</label>
                    <input type="text" class="form-control" id="ing_cas" placeholder="Any">
                </div>
              </div>
    		  <div class="mb-1 row">
                <div class="col-sm">
                	<label for="ing_einecs" class="col-sm col-form-label">EINECS</label>
                    <input type="text" class="form-control" id="ing_einecs" placeholder="Any">
                </div>
              </div>
    		  <div class="mb-1 row">
                <div class="col-sm">
                	<label for="ing_synonym" class="col-sm col-form-label">Synonym</label>
                    <input type="text" class="form-control" id="ing_synonym" placeholder="Any">
                </div>
              </div>
    		  <div class="mb-1 row">
                <div class="col-sm">
                	<label for="ing_odor" class="col-sm col-form-label">Odor</label>
                    <input type="text" class="form-control" id="ing_odor" placeholder="Any">
                </div>
              </div>
    		  <div class="mb-1 row">
                <div class="col-sm">
                	<label for="ing_profile" class="col-sm col-form-label">Profile</label>
                    <select name="profile" id="ing_profile" class="form-control selectpicker" data-live-search="true">
                       <option value="" selected>Any</option>
                        <?php
                        while ($row_ingProfiles = mysqli_fetch_array($res_ingProfiles)){ ?>
                        <option data-content="<img class='img_ing_sel' src='<?=profileImg($row_ingProfiles['name'])?>'> <?=$row_ingProfiles['name']?>" value="<?=$row_ingProfiles['name']?>"></option>
                        <?php } ?>
                  </select>
                </div>
              </div>
    		  <div class="mb-1 row">
                <div class="col-sm">
                	<label for="ing_category" class="col-sm col-form-label">Category</label>
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
<div class="modal fade" id="import_ingredients_json" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="import_ingredients_json" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import ingredients from a JSON file</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<div id="JSRestMsg"></div>
      	<div class="progress">  
       	  <div id="uploadProgressBar" class="progress-bar" role="progressbar" aria-valuemin="0"></div>
      	</div>
      	<div id="backupArea">
          <div class="form-group">
              <label class="col-md-3 control-label">JSON file:</label>
              <div class="col-md-8">
                 <input type="file" name="backupFile" id="backupFile" class="form-control" />
              </div>
          </div>
          	<div class="col-md-12">
            	 <hr />
             	<p><strong>IMPORTANT:</strong></p>
              	<ul>
                	<li><div id="raw" data-size="<?=getMaximumFileUploadSizeRaw()?>">Maximum file size: <strong><?=getMaximumFileUploadSize()?></strong></div></li>
                	<li>Any ingredient with the same id will be replaced. Please make sure you have taken a backup before imporing a JSON file.</li>
              	</ul>
            </div>
          </div>
      	</div>
	  		<div class="modal-footer">
        		<input type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseBK" value="Cancel">
        		<input type="submit" name="btnRestore" class="btn btn-primary" id="btnRestoreIngredients" value="Import">
      		</div>
  		</div>  
	</div>
</div>

<!--CSV IMPORT-->
<div class="modal fade" id="csv_import" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="csv_import" aria-hidden="true">
  <div class="modal-dialog pv-modal-xxl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import ingredients from CSV file</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <div id="CSVImportMsg"></div>
        <div id=process_area>

           <table width="100%">
                <tr>
                <td width="92" valign="top">CSV File:</td>
                    <td width="1533" colspan="3">
                        <input type="file" id="CSVFile" name="CSVFile" />
                    </td>
                </tr>
            </table>
        
        </div>
        <div id="step_upload" class="modal-body"></div>
        <div class="alert alert-info">Select and match the fields in you CSV file, if a column isn't applicable, set it to <strong>None</strong>. Any existing data in your database will not be replaced and or updated if exists in CSV.</div>
      </div>
      <div class="modal-footer">
        <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseCsv" value="Cancel">
        <input type="submit" class="btn btn-primary" id="btnImportCSV" value="Import">
      </div>
    </div>
  </div>
</div>  

<!--PV ONLINE IMPORT-->
<div class="modal fade" id="pv_online_import" tabindex="-1" role="dialog" aria-labelledby="pv_online_import" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import ingredients from PV Online</h5>
      </div>
      <div class="modal-body">
       <div id="pvImportMsg"></div>
       <div id="pv_online_imp_area">
           <div class="alert alert-warning">
               <strong>WARNING:</strong><br />
              you are about to import data from PV Online, please bear in mind, PV Online is a community driven database therefore may contain unvalidated or incorrect data. <br />
              If your local database contains already an ingredient with the same name, the ingredient data will not be imported.     
          </div>
          <div class="dropdown-divider"></div>
          <div class="form-group">
            <div class="mx-4">
                <div id="ingredientsTotal"></div>
            </div>
            <div class="mx-4">
                <input type="checkbox" class="form-check-input" id="includeSynonyms" name="includeSynonyms" value="1">
                <label class="form-check-label" for="includeSynonyms"><div id="synonymsTotal"></div></label>
            </div>          
            <div class="mx-4">
                <input type="checkbox" class="form-check-input" id="includeCompositions" name="includeCompositions" value="1">
                <label class="form-check-label" for="includeCompositions"><div id="composTotal"></div></label>
            </div>
          </div>
      </div>
      
      </div>
      <div class="modal-footer_2">
	  <?php require('privacy_note.php');?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="importClose">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="btnImport" value="Import">
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" language="javascript" >
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
		}
	});
});


$('#pv_online_import').on('click', '[id*=btnImport]', function () {
	$('#btnImport').attr('disabled', true);
	$('#importClose').attr('disabled', true);
	$('#pvImportMsg').html('<div class="alert alert-info mx-2"><img src="/img/loading.gif"/>Please wait, this may take a while...</div>');
	$.ajax({
		url: '/pages/pvonline.php', 
		type: 'POST',
		data: {
			action: 'import',
			items: 'ingredients,allergens,suppliers,suppliersMeta,synonyms',
			includeSynonyms: $("#includeSynonyms").is(':checked'),
			includeCompositions: $("#includeCompositions").is(':checked'),
			},
		dataType: 'json',
		success: function (data) {
			if(data.error){
				var rmsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
				$('#btnImport').attr('disabled', false);
			}else if(data.warning){
				var rmsg = '<div class="alert alert-warning alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+data.warning+'</div>';
				$('#btnImport').hide();
			}else if(data.success){
				var rmsg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>';
				$('#btnImport').hide();
				list_ingredients();
			}
			
			$('#importClose').attr('disabled', false);
		  	$('#pvImportMsg').html(rmsg);
		},
		error: function (request, status, error) {
			$('#pvImportMsg').html('<div class="alert alert-danger mt-3"><i class="bi bi-exclamation-circle mx-2"></i></i>Unable to handle request, server returned an error: '+request.status+'</div>');
			$('#btnImport').prop('disabled', false);
		},
	  });
});

</script>

<script src="/js/ingredients.js"></script>
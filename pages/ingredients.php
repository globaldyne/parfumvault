<?php 
if (!defined('pvault_panel')){ die('Not Found');}
				
require_once(__ROOT__.'/func/profileImg.php');

$res_ingProfiles = mysqli_query($conn, "SELECT id,name FROM ingProfiles ORDER BY name ASC");
$res_ingCategory = mysqli_query($conn, "SELECT id,image,name,notes FROM ingCategory ORDER BY name ASC");

?>
<style>
.mfp-iframe-holder .mfp-content {
    line-height: 0;
    width: 1450px;
    max-width: 1450px; 
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
<div class="modal fade" id="adv_search" tabindex="-1" role="dialog" aria-labelledby="adv_search" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="adv_search">Advanced Search</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <div id="AdvSearchMsg"></div>
		<form method="post" action="javascript:adv_search()" enctype="multipart/form-data" id="advsearch_form">
       <table width="100%">
       		<tr>
       		  <td valign="top">Name:</td>
       		  <td colspan="3"><input name="ing_name" type="text" class="form-control input-sm" id="ing_name" placeholder="Any" /></td>
   		  </tr>
       		<tr>
       		  <td valign="top">CAS:</td>
       		  <td colspan="3"><input type="text" id="ing_cas" class="form-control input-sm" name="ing_cas" placeholder="Any" /></td>
   		  </tr>
       		<tr>
       		  <td valign="top">EINECS:</td>
       		  <td colspan="3"><input type="text" id="ing_einecs" class="form-control input-sm" name="ing_einecs" placeholder="Any" /></td>
   		  </tr>
       		<tr>
       		  <td valign="top">Synonym:</td>
       		  <td colspan="3"><input type="text" id="ing_synonym" class="form-control input-sm" name="ing_synonym" placeholder="Any" /></td>
   		  </tr>
       		<tr>
       		  <td valign="top">Odor:</td>
       		  <td colspan="3"><input type="text" id="ing_odor" class="form-control input-sm" name="ing_odor" placeholder="Any" /></td>
   		  </tr>
       		<tr>
       		  <td valign="top">Profile:</td>
       		  <td colspan="3">
              <select name="profile" id="ing_profile" class="form-control selectpicker" data-live-search="true">
               <option value="" selected>Any</option>
                <?php
				while ($row_ingProfiles = mysqli_fetch_array($res_ingProfiles)){ ?>
					<option data-content="<img class='img_ing_sel' src='<?=profileImg($row_ingProfiles['name'])?>'> <?=$row_ingProfiles['name']?>" value="<?=$row_ingProfiles['name']?>"></option>
				<?php } ?>
              </select>
              </td>
   		  </tr>
       		<tr>
       		  <td valign="top">Category:</td>
       		  <td colspan="3">
              <select name="category" id="ing_category" class="form-control selectpicker" data-live-search="true">
               <option value="" selected>Any</option>
              <?php while ($row_ingCategory = mysqli_fetch_array($res_ingCategory)){ ?>
				<option data-content="<img class='img_ing_sel' src='<?php if($row_ingCategory['image']){ echo $row_ingCategory['image']; }else{ echo '/img/molecule.png';}?>'><?=$row_ingCategory['name']?>" value="<?=$row_ingCategory['id'];?>"></option>
			  <?php } ?>
              </select>
              </td>
   		  </tr>
       		<tr>
    	   	<td width="92" valign="top">&nbsp;</td>
				<td width="1533" colspan="3">&nbsp;</td>
			</tr>
		</table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="btnAdvSearch" value="Search">
      </div>
      </form>
    </div>
  </div>
</div>  

<!--CSV IMPORT-->
<div class="modal fade" id="csv_import" tabindex="-1" role="dialog" aria-labelledby="csv_import" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="csv_import">Import ingredients from CSV file</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <div id="CSVImportMsg"></div>
		<form method="post" action="javascript:importCSV()" enctype="multipart/form-data" id="csvform">
       <table width="100%">
       		<tr>
    	   	<td width="92" valign="top">CSV File:</td>
				<td width="1533" colspan="3">
                	<input type="file" id="ingCSV" name="ingCSV" />
				</td>
			</tr>
		</table>
         <strong>WARNING:</strong><br />
      		Make sure your CSV file follows the guidelines as documented <a href="https://www.jbparfum.com/knowledge-base/3-ingredients-import-csv" target="_blank">here</a>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="btnImportCSV" value="Import">
      </div>
      </form>
    </div>
  </div>
</div>  
<!--PV ONLINE IMPORT-->
<div class="modal fade" id="pv_online_import" tabindex="-1" role="dialog" aria-labelledby="pv_online_import" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import ingredients from PV Online</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <div id="pvImportMsg"></div>
      <strong>WARNING:</strong><br />
      you are about to import data from PV Online, please bear in mind, PV Online is a community driven database therefore may contain unvalidated or incorrect data. <br />
      If your local database contains already an ingredient with the same name, the ingredient data will not be imported. <p></p>
      <p>Ingredients online: <strong><?php echo pvOnlineStats($pvOnlineAPI, 'ingredientsTotal');?></strong></p>
      <p>Synonyms online: <strong><?php echo pvOnlineStats($pvOnlineAPI, 'synonymsTotal');?></strong></p>
      <p>Compositions online: <strong><?php echo pvOnlineStats($pvOnlineAPI, 'composTotal');?></strong></p>

</div>
	  <div class="modal-footer_2">
	  <?php require('privacy_note.php');?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="btnImport" value="Import">
      </div>
    </div>
  </div>
</div>
<?php if($pv_online['email'] && $pv_online['password'] && $pv_online['enabled'] == '1'){?>
<!--PV ONLINE UPLOAD-->
<div class="modal fade" id="pv_online_upload" tabindex="-1" role="dialog" aria-labelledby="pv_online_upload" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pv_online_upload">Upload my ingredients to PV Online</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <div id="pvUploadMsg"></div>

      <strong>WARNING:</strong><br />
      you are about to upload data to PV Online, please bear in mind, PV Online is a community driven database therefore your data will be available to others. Please make sure you not uploading any sensitive information. <br />
      If PV Online database contains already an ingredient with the same name, the ingredient data will not be uploaded. <p></p>
      Ingredients in your database: <strong><?php echo countElement("ingredients",$conn);?></strong>
</div>
      <div class="dropdown-divider"></div>
      <div class="modal-body pv_exclusions">
       <label>
         <input name="excludeCompositions" type="checkbox" id="excludeCompositions" value="1" />
        Exclude compositions
      </label>
      <label>
      <input name="excludeSynonyms" type="checkbox" id="excludeSynonyms" value="1" />
        Exclude synonyms
      </label>
      <label>
      <input name="excludeSuppliers" type="checkbox" id="excludeSuppliers" value="1" />
        Exclude suppliers
      </label>
      <label>
         <input name="excludeNotes" type="checkbox" id="excludeNotes" value="1" />
        Exclude notes
      </label>
      </div>
	  <div class="modal-footer_2">
	  <?php require('privacy_note.php');?>
      </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <input type="submit" name="button" class="btn btn-primary" id="btnUpload" value="Upload">
        </div>

    </div>
  </div>
</div>
<?php } ?>
<script type="text/javascript" language="javascript" >
list_ingredients();

$(function () {
    $(".input-group-btn .dropdown-menu li a").click(function () {
        var selText = $(this).html();
		var provider = $(this).attr('id');
		  
        $(this).parents(".input-group-btn").find(".btn-search").html(selText);
		$(this).parents(".input-group-btn").find(".btn-search").attr('id',provider);
    });
});

function adv_search() {
    var name = $('#ing_name').val();
    var cas = $('#ing_cas').val();
    var einecs = $('#ing_einecs').val();
    var odor = $('#ing_odor').val();
    var profile = $('#ing_profile').val();
    var cat = $('#ing_category').val();
    var synonym = $('#ing_synonym').val();

	$.ajax({ 
		url: 'pages/listIngredients.php',
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
};


$('#pv_online_import').on('click', '[id*=btnImport]', function () {
	$('#btnImport').attr('disabled', true);
	$('#pvImportMsg').html('<div class="alert alert-info"><img src="/img/loading.gif"/> Please wait, this may take a while...</div>');
	$.ajax({
		url: 'pages/pvonline.php', 
		type: 'POST',
		data: {
			action: 'import',
			items: 'ingredients,allergens,suppliers,suppliersMeta,synonyms'
			},
		dataType: 'json',
		success: function (data) {
			if(data.error){
				var rmsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
				$('#btnImport').attr('disabled', false);
			}else if(data.warning){
				var rmsg = '<div class="alert alert-warning alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.warning+'</div>';
				$('#btnImport').hide();
			}else if(data.success){
				var rmsg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>';
				$('#btnImport').hide();
				list_ingredients();
			}
			
			
		  	$('#pvImportMsg').html(rmsg);
		}
	  });
});

<?php if($pv_online['email'] && $pv_online['password'] && $pv_online['enabled'] == '1'){?>
$(".pv_exclusions input[type=checkbox]:checked").on('change', function () {
	$('#btnUpload').show();
	$('#btnUpload').prop('disabled', false);
});

$('#pv_online_upload').on('click', '[id*=btnUpload]', function () {
	$('#btnUpload').prop('disabled', true);
	$('#pvUploadMsg').html('<div class="alert alert-info"><img src="/img/loading.gif"/> Please wait, this may take a while...</div>');
	$.ajax({
		url: 'pages/pvonline.php', 
		type: 'POST',
		data: {
			action: 'upload',
			items: 'ingredients',
			excludeNotes: $("#excludeNotes").is(':checked'),
			excludeSynonyms: $("#excludeSynonyms").is(':checked'),
			excludeCompositions: $("#excludeCompositions").is(':checked'),
			excludeSuppliers: $("#excludeSuppliers").is(':checked')

			},
		dataType: 'json',
		success: function (data) {
			if(data.error){
				var rmsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
			}else if(data.success){
				var rmsg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>';
				$('#btnUpload').hide();
			}
		  	$('#pvUploadMsg').html(rmsg);
		}
	  });
});
<?php } ?>

function delete_ingredient(id){
	
	$.ajax({
		url: 'pages/update_data.php', 
		type: 'GET',
		data: {
			ingredient: "delete",
			ing_id: id,
			},
		dataType: 'html',
		success: function (data) {
		  	$('#innermsg').html(data);
			list_ingredients();
		}
	  });
};

function importCSV(){
    $("#CSVImportMsg").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
	$("#btnImport").prop("disabled", true);
		
	var fd = new FormData();
    var files = $('#ingCSV')[0].files;
        
       if(files.length > 0 ){
          fd.append('ingCSV',files[0]);

        $.ajax({
           url: 'pages/upload.php?type=ingCSVImport',
           type: 'POST',
           data: fd,
           contentType: false,
           processData: false,
           success: function(response){
             if(response != 0){
               $("#CSVImportMsg").html(response);
				$("#btnImport").prop("disabled", false);
              }else{
                $("#CSVImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> File upload failed!</div>');
				$("#btnImport").prop("disabled", false);
              }
            },
         });
  }else{
	$("#CSVImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a file to upload!</div>');
	$("#btnImport").prop("disabled", false);
  }
};

function importING(name) {	  
	$.ajax({ 
		url: 'pages/update_data.php', 
		type: 'GET',
		data: {
			'import': 'ingredient',
			'name': name,
			},
		dataType: 'html',
		success: function (data) {
			$('#innermsg').html(data);
		}
	  });
};

function setView(view) {
	$.ajax({ 
    url: 'pages/update_settings.php', 
	type: 'GET',
    data: {
		ingView: view,
		},
	dataType: 'html',
    success: function (data) {
		location.reload();
    }
  });
};

</script>

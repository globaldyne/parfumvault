<?php 
if (!defined('pvault_panel')){ die('Not Found');}
?>
<style>
.mfp-iframe-holder .mfp-content {
    line-height: 0;
    width: 1000px;
    max-width: 1000px; 
	height: 750px;
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
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="btnImportCSV" value="Import">
      </div>
      </form>
    </div>
  </div>
</div>  
<?php if($pv_online['email'] && $pv_online['password']){?>
<!--PV ONLINE IMPORT-->
<div class="modal fade" id="pv_online_import" tabindex="-1" role="dialog" aria-labelledby="pv_online_import" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pv_online_import">Import ingredients from PV Online</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <div id="pvImportMsg"></div>
  <form action="javascript:pv_online_import('ingredients,allergens')" method="get" name="form1" target="_self" id="form1">
      <strong>WARNING:</strong><br />
      you are about to import data from PV Online, please bear in mind, PV Online is a community driven database therefore may contain unvalidated or incorrect data. <br />
      If your local database contains already an ingredient with the same name, the ingredient data will not be imported. <p></p>
      Ingredients online: <strong><?php echo pvOnlineStats($pvOnlineAPI, $pv_online['email'], $pv_online['password'], 'ingredients');?></strong>
</div>
	  <div class="modal-footer_2">
	  <?php require('privacy_note.php');?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="btnImport" value="Import">
      </div>
     </form>
    </div>
  </div>
</div>

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
  <form action="javascript:pv_online_upload('ingredients')" method="get" name="form1" target="_self" id="form_pv_online_upload">
      <strong>WARNING:</strong><br />
      you are about to upload data to PV Online, please bear in mind, PV Online is a community driven database therefore your data will be available to others. Please make sure you not uploading any sensitive information. <br />
      If PV Online database contains already an ingredient with the same name, the ingredient data will not be uploaded. <p></p>
      Ingredients in your database: <strong><?php echo countElement("ingredients",$conn);?></strong>
</div>
      <div class="dropdown-divider"></div>
      <div class="modal-body">
      <label>
         <input name="excludeNotes" type="checkbox" id="excludeNotes" value="1" />
        Exclude notes
      </label>
      </div>
	  <div class="modal-footer_2">
	  <?php require('privacy_note.php');?>
      </div>
<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
  <input type="submit" name="button" class="btn btn-primary" id="btnUpload" value="Upload">
</div>
     </form>
    </div>
  </div>
</div>
<?php } ?>
<script type="text/javascript" language="javascript" >
list_ingredients();
<?php if($pv_online['email'] && $pv_online['password']){?>

function pv_online_import(items) {
	$('#btnImport').attr('disabled', true);
	$('#pvImportMsg').html('<div class="alert alert-info">Please wait...</div>');
	$.ajax({ 
		url: 'pages/pvonline.php', 
		type: 'get',
		data: {
			action: "import",
			items: items
			},
		dataType: 'html',
		success: function (data) {
			$('#btnImport').attr('disabled', false);
		  	$('#pvImportMsg').html(data);
		}
	  });
};

function pv_online_upload(items) {
	$('#btnUpload').attr('disabled', true);
	$('#pvUploadMsg').html('<div class="alert alert-info">Please wait...</div>');
	$.ajax({
		url: 'pages/pvonline.php', 
		type: 'get',
		data: {
			action: "upload",
			items: items,
			excludeNotes: $("#excludeNotes").is(':checked')
			},
		dataType: 'html',
		success: function (data) {
			$('#btnUpload').attr('disabled', false);
		  	$('#pvUploadMsg').html(data);
		}
	  });
};
<?php } ?>

function delete_ingredient(id){
	
	$.ajax({
		url: 'pages/update_data.php', 
		type: 'get',
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
           type: 'post',
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

</script>

<?php 
if (!defined('pvault_panel')){ die('Not Found');}

$ingID = mysqli_real_escape_string($conn, $_GET['id']);
$ingName = mysqli_real_escape_string($conn, $_GET['name']);

if($_GET['action'] == "delete" && $_GET['id']){
	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '$ingName'"))){
		$msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$ingName.'</strong> is in use by at least one formula and cannot be removed!</div>';
		
	}elseif(mysqli_query($conn, "DELETE FROM ingredients WHERE id = '$ingID'")){
		$msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Ingredient <strong>'.$ingName.'</strong> removed from the database!</div>';
	}
}
$ingredient_q = mysqli_query($conn, "SELECT * FROM ingredients ORDER BY name ASC");
$defCatClass = $settings['defCatClass'];

?>
<style>
.mfp-iframe-holder .mfp-content {
    line-height: 0;
    width: 1000px;
    max-width: 1000px; 
	height: 700px;
}
</style>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=ingredients">Ingredients</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder noexport">
                      <th colspan="11">
                  		<div class="text-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item popup-link" href="pages/mgmIngredient.php">Add new ingredient</a>
                            <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
	                        <a class="dropdown-item popup-link" href="pages/csvImportIng.php">Import from CSV</a>
                            <?php if($pv_online['email'] && $pv_online['password']){?>
                            <div class="dropdown-divider"></div>
	                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#pv_online_import">Import from PV Online</a>
	                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#pv_online_upload">Upload to PV Online</a>
                            <?php } ?>
                          </div>
                        </div>                    
                        </div>
                        </th>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>INCI</th>
                      <th>CAS #</th>
                      <th>Odor</th>
                      <th>Profile</th>
                      <th>Category</th>
                      <th><?php echo ucfirst($settings['defCatClass']);?> %</th>
                      <th>Supplier</th>
                      <th class="noexport">SDS</th>
                      <th class="noexport">TGSC</th>
                      <th class="noexport">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php while ($ingredient = mysqli_fetch_array($ingredient_q)) { ?>
                    <tr>
                      <td align="center"><a href="pages/mgmIngredient.php?id=<?php echo $ingredient['name'];?>" class="popup-link"><?php echo $ingredient['name'];?></a><?php echo checkAllergen($ingredient['name'],$conn);?></td>
                      <td align="center"><?php echo $ingredient['INCI'];?></td>
					  <?php
                      if($ingredient['cas']){
						  echo '<td align="center">'.$ingredient['cas'].'</td>';
					  }else{
						  echo '<td align="center">N/A</td>';
					  }
					  echo '
					  <td align="center">'.$ingredient['odor'].'</td>
                      <td align="center">'.$ingredient['profile'].'</td>
					  <td align="center">'.$ingredient['category'].'</td>';
  					  if($limit = searchIFRA($ingredient['cas'],$ingredient['name'],null,$conn,$defCatClass)){
						  $limit = explode(' - ', $limit);
						  echo '<td align="center"><a href="#" rel="tipsy" title="'.$limit['1'].'">'.$limit['0'].'<a></td>';
					  }elseif($ingredient[$defCatClass]){
						  echo '<td align="center">'.$ingredient[$defCatClass].'</td>';
					  }else{
						  echo '<td align="center">N/A</a>';
					  }
					  if ($ingredient['supplier'] && $ingredient['supplier_link']){
						  echo '<td align="center"><a href="'.$ingredient['supplier_link'].'" target="_blanc">'.$ingredient['supplier'].'</a></td>';
					  }elseif ($ingredient['supplier']){
						  echo '<td align="center">'.$ingredient['supplier'].'</a></td>';
					  }else{
						  echo '<td align="center">N/A</td>';
					  }	
					  if ($ingredient['SDS']){
						  echo '<td align="center" class="noexport"><a href="'.$ingredient['SDS'].'" target="_blanc" class="fa fa-save"></a></td>';
					  }else{
						  echo '<td align="center" class="noexport">N/A</td>';
					  }	
					  if ($ingredient['cas']){
						  echo '<td align="center" class="noexport"><a href="http://www.thegoodscentscompany.com/search3.php?qName='.$ingredient['cas'].'" target="_blanc" class="fa fa-external-link-alt"></a></td>';
					  }else{
						  echo '<td align="center" class="noexport"><a href="http://www.thegoodscentscompany.com/search3.php?qName='.$ingredient['name'].'" target="_blanc" class="fa fa-external-link-alt"></a></td>';
					  }
                      echo '<td class="noexport" align="center"><a href="pages/mgmIngredient.php?id='.$ingredient['name'].'" class="fas fa-edit popup-link"><a> <a href="?do=ingredients&action=delete&id='.$ingredient['id'].'&name='.$ingredient['name'].'" onclick="return confirm(\'Delete '.$ingredient['name'].'?\');" class="fas fa-trash"></a></td>';
					  echo '</tr>';
				  }
                    ?>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
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
$('#csv').on('click',function(){
  $("#tdData").tableHTMLExport({
	type:'csv',
	filename:'ingredients.csv',
	separator: ',',
  	newline: '\r\n',
  	trimContent: true,
  	quoteFields: true,
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',
	htmlContent: false,
  	// debug
  	consoleLog: false   
  });
})
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
</script>

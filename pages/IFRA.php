<?php
if (!defined('pvault_panel')){ die('Not Found');}

$ifra_q = mysqli_query($conn, "SELECT * FROM IFRALibrary ORDER BY amendment DESC");
$defCatClass = $settings['defCatClass'];

?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
<?php echo $msg; ?>
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=IFRA">IFRA Library</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder noexport">
                      <th colspan="8">
                       <div class="text-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                          <div class="dropdown-menu dropdown-menu-right">
	                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#ifra_import">Import IFRA xls</a>
                            <a class="dropdown-item" id="csv" href="#">Export to CSV</a>
                          </div>
                        </div>                      
                        </div>
                        </th>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>CAS #</th>
                      <th>Amendment</th>
                      <th>Last publication</th>
                      <th>Synonyms</th>
                      <th>IFRA Type</th>
                      <th>Risk</th>
                      <th><?php echo ucfirst($defCatClass); ?>%</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php					
				  while ($IFRA = mysqli_fetch_array($ifra_q)) {
					  echo'
                    <tr>
                      <td align="center"><a href="#">'.$IFRA['name'].'</a></td>
					  <td align="center">'.$IFRA['cas'].'</td>
					  <td align="center">'.$IFRA['amendment'].'</td>
					  <td align="center">'.$IFRA['last_pub'].'</td>
  					  <td align="center">'.$IFRA['synonyms'].'</td>
					  <td align="center">'.$IFRA['type'].'</td>
					  <td align="center">'.$IFRA['risk'].'</td>
					  <td align="center">'.$IFRA[$defCatClass].'</td>';
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
<!--IFRA IMPORT-->
<div class="modal fade" id="ifra_import" tabindex="-1" role="dialog" aria-labelledby="ifra_import" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-ifra" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ifra_import">Import ingredients from CSV file</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <div id="IFRAImportMsg"></div>
		<form method="post" action="javascript:importIFRA()" enctype="multipart/form-data" id="ifra_import_form">
       <table width="100%">
       		<tr>
    	   	<td width="122" valign="top">IFRA xls File:</td>
				<td width="1519" colspan="3">
                	<input type="file" id="ifraXLS" name="ifraXLS" />
				</td>
			</tr>
       		<tr>
       		  <td height="46">Modify file:</td>
              <td><input name="updateCAS" type="checkbox" id="updateCAS" value="1" />
                 <span class="font-italic">*this is required if you are importing the original IFRA file</span>
              </td>
   		  </tr>
		</table>
       <p class="alert-link"><strong>IMPORTANT:</strong></p>
       <p class="alert-link"> This operation will wipe out any data already in your IFRA Library, so please make sure the file you uploading is in the right format and have taken a <a href="pages/maintenance.php?do=backupDB">backup</a> before.</p>
       <p class="alert-link">The IFRA xls can be downloaded from its official <a href="https://ifrafragrance.org/safe-use/standards-guidance" target="_blank">web site</a></p>
       </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="btnImport" class="btn btn-primary" id="btnImportCSV" value="Import">
      </div>
      </form>
    </div>
  </div>
</div>  <script type="text/javascript" language="javascript" >

$('#csv').on('click',function(){
  $("#tdData").tableHTMLExport({
	type:'csv',
	filename:'ifra.csv',
	separator: ',',
  	newline: '\r\n',
  	trimContent: true,
  	quoteFields: true,
	
	ignoreColumns: '.noexport',
  	ignoreRows: '.noexport',
	
	htmlContent: false,  
  	consoleLog: false   
});
 
});

function importIFRA(){
	$("#IFRAImportMsg").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
	$("#btnImport").prop("disabled", true);
		
	var fd = new FormData();
    var files = $('#ifraXLS')[0].files;
    var modify = $('#updateCAS').val();

       if(files.length > 0 ){
        fd.append('ifraXLS',files[0]);
        $.ajax({
           url: 'pages/upload.php?type=IFRA&updateCAS=' + modify,
           type: 'post',
           data: fd,
           contentType: false,
           processData: false,
		         cache: false,
           success: function(response){
             if(response != 0){
               $("#IFRAImportMsg").html(response);
				$("#btnImport").prop("disabled", false);
              }else{
                $("#IFRAImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> File upload failed!</div>');
				$("#btnImport").prop("disabled", false);
              }
            },
         });
  }else{
	$("#IFRAImportMsg").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a file to upload!</div>');
	$("#btnImport").prop("disabled", false);
  }	
}

</script>

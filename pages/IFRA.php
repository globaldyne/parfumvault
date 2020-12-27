<?php
if (!defined('pvault_panel')){ die('Not Found');}

$ifra_q = mysqli_query($conn, "SELECT * FROM IFRALibrary ORDER BY amendment DESC");
$defCatClass = $settings['defCatClass'];

?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
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
                            <a class="dropdown-item popup-link" id="ifraimport" href="pages/maintenance.php?do=IFRA">Import IFRA xls</a>
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
    
<script type="text/javascript" language="javascript" >

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
 
})

</script>

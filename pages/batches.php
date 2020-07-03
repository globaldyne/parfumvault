<?php 
if (!defined('pvault_panel')){ die('Not Found');}

$q = mysqli_query($conn, "SELECT * FROM batchIDHistory");
?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=batches">Batch History</a></h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder">
                    </tr>
                    <tr>
                      <th>Batch ID</th>
                      <th>Product Name</th>
                      <th>Created</th>
                      <th>Final Product</th>
                    </tr>
                  </thead>
                  <tbody id="batch_data">
                    <?php					
				  while ($sql = mysqli_fetch_array($q)) {
					  echo'
                    <tr>
                      <td align="center">'.$sql['id'].'</td>
					  <td align="center">'.base64_decode($sql['fid']).'</td>
					  <td align="center">'.$sql['created'].'</td>
                      <td align="center"><a href="'.$sql['pdf'].'" class="fas fa-file-pdf"></a></td>
					</tr>';
				  }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
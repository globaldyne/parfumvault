<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
        <div class="container-fluid">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="m-0 mb-4 text-primary">Dashboard</h1>
          </div>
          <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Formulas</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $formulas_c; ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-flask fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Aroma Chemicals (A.C.)</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $ac_c; ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-vial fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Essential Oils (E.0.)</div>
                      <div class="row no-gutters align-items-center">
                        <div class="col-auto">
                          <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $eo_c; ?></div>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-eye-dropper fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
			<div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Suppliers</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $sup_c; ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-store fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>        
                
            <div class="col-xl-3 col-md-6 mb-4"></div>
          </div>
          <div>
          <?php echo $msg; ?>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Formulas</h6>
            </div>
            <div class="card-body">
              <div>
              <?php
              	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients"))== 0){
					echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> no ingredients yet, click <a href="/?do=ingredients">here</a> to add.</div>';
				}elseif(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulas"))== 0){
					echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> no formulas yet, click <a href="/?do=addFormula">here</a> to add.</div>';

				}else{	
                ?>
                <table width="100%" border="0" cellspacing="0" class="table table-bordered">
                  <thead>
                    <tr>
                      <th width="14%">Name</th>
                      <th width="20%">Notes</th>
                      <th width="23%">Created</th>
                      <th width="21%">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
		
			 		$formulas_n = mysqli_query($conn, "SELECT * FROM formulas GROUP BY name ORDER by name DESC");
					while ($formula = mysqli_fetch_array($formulas_n)) {
						echo'<tr>
						  <td align="center"><a href="/?do=Formula&name='.$formula['name'].'">'.$formula['name'].'</a></td>';
						  $meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE name = '".$formula['name']."'"));
						  echo '<td align="center"><a href="pages/getFormMeta.php?id='.$meta['id'].'" class="fas fa-comment-dots popup-link"></a></td>';
						  echo '<td align="center">'.$meta['created'].'</td>';
						  echo '<td align="center"><a> <a href="/?do=dashboard&action=delete&name='.$formula['name'].'" onclick="return confirm(\'Delete '.$formula['name'].' Formula?\');" class="fas fa-trash" rel="tipsy" title="Delete '.$formula['name'].'"></a></td>
						</tr>';
					  }
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
	<?php // require_once("./pages/footer.php"); ?>
  </div>
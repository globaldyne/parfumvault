<?php if (!defined('pvault_panel')){ die('Not Found');}?>
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
                      <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Suppliers</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $sup_c; ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-store fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>        
              
			<div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">IFRA Entries</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $ifra_c; ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-university fa-2x text-gray-300"></i>
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
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">All Ingredients</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $all_ing_c; ?></div>
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
                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Uncategorised Ingredients</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $uncat_ing_c; ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-vial fa-2x text-gray-300"></i>
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
                      <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Categories</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $cat_c; ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-puzzle-piece fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
                              
          </div>
          <div>
        </div>
      </div>
  </div>
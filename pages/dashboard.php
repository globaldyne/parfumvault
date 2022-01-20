<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<?php require_once(__ROOT__.'/func/countElement.php');?>
<script src="js/Chart.min.js"></script>
<link href="css/Chart.css" rel="stylesheet">

<div id="content-wrapper" class="d-flex flex-column">
<?php require_once(__ROOT__.'/pages/top.php'); ?>
        <div class="container-fluid">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="m-0 mb-4 text-primary">Dashboard</h1>
          </div>
          <div class="row">
          <?php
if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients"))== 0){
	echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> no ingredients yet, click <a href="?do=ingredients">here</a> to add.</div>';
}elseif(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData"))== 0){
	echo '<div class="alert alert-info alert-dismissible"><strong>INFO: </strong> no formulas added yet</div>';
}else{
	?>
              <div class="dash_charts">
              <div class="shadow-lg p-3 mb-5 bg-white rounded charts_box">
              
                <div class="col-md-6">
                <div class="box" id="formulas-pie">
                    <div class="box-header with-border">
                      <h3 class="box-title">Formulas by category</h3>
                    </div>
                    <div class="box-body">
                      <div class="pull-left">
                        <canvas id="formulasPie" width="358" height="358"></canvas>
                            <div class="cell" id="top10Legend"></div>

                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6">
                <div class="box" id="ingredients-pie">
                    <div class="box-header with-border">
                      <h3 class="box-title">Ingredients by category</h3>
                    </div>
                    <div class="box-body">
                      <div class="pull-left">
                        <canvas id="ingredientsPie" width="358" height="358"></canvas>
                      </div>
                    </div>
                  </div>
                </div>
                </div>
              </div>

      <?php } ?>          
               
           <div class="mt-3 col-md-12"></div>

            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">All Formulas</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo countElement("formulasMetaData",$conn); ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-flask fa-2x text-gray-300"></i>
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
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo countElement("ingSuppliers",$conn); ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-store fa-2x text-gray-300"></i>
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
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">IFRA Entries</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo countElement("IFRALibrary",$conn); ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-university fa-2x text-gray-300"></i>
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
                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">All Ingredients</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo countElement("ingredients",$conn); ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-vial fa-2x text-gray-300"></i>
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
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Categories</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo countElement("ingCategory",$conn); ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-puzzle-piece fa-2x text-gray-300"></i>
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
                      <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Bottles</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo countElement("bottles",$conn); ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-spray-can fa-2x text-gray-300"></i>
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
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Bottle Lids</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo countElement("lids",$conn); ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-prescription-bottle fa-2x text-gray-300"></i>
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
                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Formulas to make</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo countElement("makeFormula WHERE toAdd = '1' GROUP BY name",$conn); ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-flask fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div> 
            
            <div class="mt-4 col-md-12"></div>

          </div>
          <div>
        </div>
      </div>
  </div>
  

<script>
var formulas = document.getElementById('formulasPie');
var ingredients = document.getElementById('ingredientsPie');
 $.ajax({
    url: "core/stats_data.php",
    method: "GET",
    dataType : 'JSON',
    success: function(stats) {
    
		var formula_label = stats.data.map(function(e) {
			return e.name;
		});
		var formula_data = stats.data.map(function(e) {
			return e.count;
		});
		var formula_bkColor = stats.data.map(function(e) {
			return e.colorKey;
		});
		var formula_brdColor = stats.data.map(function(e) {
			return e.borderColor;
		});		

		var formulasChart = new Chart(formulas, {
			type: 'pie',
			data: {
				labels: formula_label,
				datasets: [{
					label: 'Formulas',
					data: formula_data,
					backgroundColor: formula_bkColor,
					borderColor: formula_brdColor,
					borderWidth: 1
				}]
			},
			options: { 
				responsive: true,
				plugins: {
      				legend: {
        					display: true,
        					position: 'right',
      						},
    					} 
					}
		});
	}
});
var ingredientsChart = new Chart(ingredients, {
    type: 'pie',
    data: {
        labels: ['Aroma Chemicals ', 'Essential Oils', 'Unategorised'],
        datasets: [{
            label: 'Ingredients',
            data: [<?php echo countElement("ingredients WHERE type = 'AC'",$conn); ?>, <?php echo countElement("ingredients WHERE type = 'EO'",$conn); ?>, <?php echo countElement("ingredients WHERE type IS NULL",$conn); ?>],
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: { 
			responsive: true,
			plugins: {
      				legend: {
        					display: true,
        					position: 'right',
      						},
    					} 
			 }
});

</script>

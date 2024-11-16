<?php 
if (!defined('pvault_panel')){ die('Not Found');}

require_once(__ROOT__.'/func/countElement.php');
if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients"))){
	$ingredientsConf = TRUE;
}else{
	$ingredientsConf = FALSE;
}
if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData"))){
	$formulasConf = TRUE;
}else{
	$formulasConf = FALSE;
}
?>


<div id="content-wrapper" class="d-flex flex-column">
    <?php require_once(__ROOT__.'/pages/top.php'); ?>
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="m-0 mb-4">Dashboard</h1>
        </div>
        <div class="row mb-4">
            <?php
            if($ingredientsConf == FALSE){
                echo '<div class="alert alert-info"><i class="fa-solid fa-circle-exclamation me-2"></i><strong>No ingredients yet, click <a href="/?do=ingredients">here</a> to add.</strong></div>';
            } elseif($formulasConf == FALSE){
                echo '<div class="alert alert-info"><i class="fa-solid fa-circle-exclamation me-2"></i><strong>No formulas added yet, click <a href="/?do=listFormulas">here</a> to add.</strong></div>';
            } else {
            ?>
            
            <div class="row">
                <div class="col-12 col-sm-6 col-xxl-3 d-flex">
                    <div class="card dashboard flex-fill">
                        <div class="card-body p-0 d-flex flex-fill">
                            <div class="row g-0 w-100">
                                <div class="col-6">
                                    <div class="dashboard-text p-3 m-1">
                                        <h4 class="dashboard-text">Welcome Back, <?php echo explode(" ", $user['fullName'])[0];?>!</h4>
                                        <p class="mb-0"></p>
                                    </div>
                                </div>
                                <div class="col-6 align-self-end text-end">
                                <?php if ($doc['avatar']){ ?>
                                    <img src="<?=$doc['avatar']?: '/img/ICO_TR.png'; ?>" class="img-fluid dashboard-img">
								<?php } else { ?>
                                <svg xmlns="http://www.w3.org/2000/svg" class="img-fluid dashboard-img" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>
                                <?php } ?>	
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xxl-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body py-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h3 class="mb-2"><?php echo countElement("formulasMetaData", $conn); ?></h3>
                                    <p class="mb-2"><a href="/?do=listFormulas">My Formulas</a></p>
                                    <div class="mb-0">
                                        <span class="text-muted">Manage formulas you own or create new ones</span>
                                    </div>
                                </div>
                                <div class="d-inline-block ms-3">
                                    <div class="stat">
                                        <i class="fa-solid fa-flask fa-2x text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xxl-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body py-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h3 class="mb-2"><?php echo countElement("ingredients", $conn); ?></h3>
                                    <p class="mb-2"><a href="/?do=ingredients">My Ingredients</a></p>
                                    <div class="mb-0">
                                        <span class="text-muted">Manage your ingredients inventory, add, edit, delete, etc</span>
                                    </div>
                                </div>
                                <div class="d-inline-block ms-3">
                                    <div class="stat">
                                        <i class="fas fa-vial fa-2x text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xxl-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body py-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h3 class="mb-2"><?php echo countElement("makeFormula WHERE toAdd = '1' GROUP BY name", $conn); ?></h3>
                                    <p class="mb-2"><a href="/?do=scheduledFormulas">Pending Formulas</a></p>
                                    <div class="mb-0">
                                        <span class="text-muted">See and manage formulas you have in schedule to make or started making already</span>
                                    </div>
                                </div>
                                <div class="d-inline-block ms-3">
                                    <div class="stat">
                                        <i class="fa-solid fa-clock fa-2x text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                    
            <div class="row mt-4">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-lg p-3 rounded">
                        <div class="card-header">
                            <h3 class="card-title">Formulas by category</h3>
                        </div>
                        <div class="box-body">
                            <canvas id="formulasPie" width="358" height="358"></canvas>
                            <div id="top10Legend"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-lg p-3 rounded">
                        <div class="card-header">
                            <h3 class="card-title">Ingredients by category</h3>
                        </div>
                        <div class="box-body">
                            <canvas id="ingredientsPie" width="358" height="358"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="row">

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        <a href="/?do=suppliers">Suppliers</a>
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold"><?php echo countElement("ingSuppliers", $conn); ?></div>
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
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        <a href="/?do=IFRA">IFRA Entries</a>
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold"><?php echo countElement("IFRALibrary", $conn); ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-university fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        <a href="/?do=settings#categories">Categories</a>
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold"><?php echo countElement("ingCategory", $conn); ?></div>
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
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        <a href="/?do=bottles">Bottles</a>
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold"><?php echo countElement("bottles", $conn); ?></div>
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
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        <a href="/?do=accessories">Accessories</a>
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold"><?php echo countElement("inventory_accessories", $conn); ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-prescription-bottle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
  
<?php if($ingredientsConf == TRUE && $formulasConf == TRUE){ ?>
<script src="/js/Chart.min.js"></script>
<link href="/css/Chart.css" rel="stylesheet">
<script>
$(document).ready(function() {

	var formulas = document.getElementById('formulasPie');
	var ingredients = document.getElementById('ingredientsPie');
	$.ajax({
		url: "/core/stats_data.php",
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
	
});

</script>
<?php } ?>

<?php 
if (!defined('pvault_panel')){ die('Not Found');}

require_once(__ROOT__.'/func/countElement.php');
if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE owner_id = '$userID'"))){
	$ingredientsConf = TRUE;
}else{
	$ingredientsConf = FALSE;
}
if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE owner_id = '$userID'"))){
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
                                    <h3 class="mb-2"><?php echo countElement("formulasMetaData"); ?></h3>
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
                                    <h3 class="mb-2"><?php echo countElement("ingredients"); ?></h3>
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
                                    <h3 class="mb-2"><?php echo countPending(NULL, NULL); ?></h3>
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
                <!-- Banner Start -->
                 <!-- PLACEHOLDER FOR PV2
                <div class="col-12 mb-4 d-flex justify-content-center">
                    <div id="dashboard-banner" style="width:1600px; height:400px; overflow:hidden; border-radius:12px; box-shadow:0 2px 16px rgba(0,0,0,0.12); background:#f8f9fa; display:flex; align-items:center; justify-content:center;">
                        <img src="https://www.perfumersvault.com/wp-content/uploads/2025/04/beaker-svgrepo-com-1.png" alt="Dashboard Banner" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                </div>
                -->
                <!-- Banner End -->
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
                                    <div class="h5 mb-0 font-weight-bold"><?php echo countElement("ingSuppliers"); ?></div>
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
                                    <div class="h5 mb-0 font-weight-bold"><?php echo countElement("IFRALibrary"); ?></div>
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
                                    <div class="h5 mb-0 font-weight-bold"><?php echo countElement("ingCategory"); ?></div>
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
                                    <div class="h5 mb-0 font-weight-bold"><?php echo countElement("bottles"); ?></div>
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
                                    <div class="h5 mb-0 font-weight-bold"><?php echo countElement("inventory_accessories"); ?></div>
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

            // Try to load colors from localStorage
            var storedColors = localStorage.getItem('formulasPieColors');
            var formula_bkColor, formula_brdColor;

            if (storedColors) {
                try {
                    var parsed = JSON.parse(storedColors);
                    if (Array.isArray(parsed.background) && Array.isArray(parsed.border) && parsed.background.length === formula_data.length) {
                        formula_bkColor = parsed.background;
                        formula_brdColor = parsed.border;
                    } else {
                        throw new Error("Color arrays do not match data length");
                    }
                } catch (e) {
                    formula_bkColor = generateRandomColors(formula_data.length);
                    formula_brdColor = formula_bkColor.map(color => color.replace('0.8', '1'));
                }
            } else {
                formula_bkColor = generateRandomColors(formula_data.length);
                formula_brdColor = formula_bkColor.map(color => color.replace('0.8', '1'));
            }

            // Store colors in localStorage for persistence
            localStorage.setItem('formulasPieColors', JSON.stringify({
                background: formula_bkColor,
                border: formula_brdColor
            }));

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
            labels: ['Aroma Chemicals ', 'Essential Oils', 'Uncategorised'],
            datasets: [{
                label: 'Ingredients',
                data: [<?php echo countElement("ingredients","type = 'AC'"); ?>, <?php echo countElement("ingredients", "type = 'EO'"); ?>, <?php echo countElement("ingredients", "type IS NULL"); ?>],
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
    
    function generateRandomColors(count) {
        const colors = [];
        for (let i = 0; i < count; i++) {
            // Generate pastel-like random colors
            const hue = Math.floor(Math.random() * 360);
            colors.push(`hsla(${hue}, 70%, 70%, 0.8)`);
        }
        return colors;
    }
});
</script>
<?php } ?>

<!-- Modal HTML -->
<div class="modal fade" id="downloadAppModal" tabindex="-1" aria-labelledby="downloadAppModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="downloadAppModalLabel">Download Perfumers Vault App</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <p class="mb-4">Choose your platform to download the Perfumers Vault app:</p>
        <div class="row justify-content-center">
          <div class="col-md-6 mb-3">
            <a href="https://apps.apple.com/us/app/perfumers-vault/id1525381567" target="_blank" class="btn btn-outline-primary w-100 p-3">
              <div class="d-flex align-items-center justify-content-center">
                <i class="bi bi-apple me-2" style="font-size: 1.5rem;"></i>
                <div>
                  <div class="fw-bold">App Store</div>
                  <small class="text-muted">for iOS devices</small>
                </div>
              </div>
            </a>
          </div>
          <div class="col-md-6 mb-3">
            <a href="#" onclick="alert('Play Store version coming soon!')" class="btn btn-outline-success w-100 p-3">
              <div class="d-flex align-items-center justify-content-center">
                <i class="bi bi-google-play me-2" style="font-size: 1.5rem;"></i>
                <div>
                  <div class="fw-bold">Play Store</div>
                  <small class="text-muted">for Android devices</small>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal trigger script -->
<script>
$(document).ready(function() {
  $('#dashboard-banner').css('cursor', 'pointer').on('click', function() {
    $('#downloadAppModal').modal('show');
  });
});
</script>

<?php
define('__ROOT__', dirname(__FILE__)); 

require_once(__ROOT__.'/inc/sec.php');

if(file_exists(__ROOT__.'/inc/config.php') == FALSE && !getenv('DB_HOST') && !getenv('DB_USER') && !getenv('DB_PASS') && !getenv('DB_NAME')){
	
	session_destroy();
	header('Location: /login.php');
}
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/countPending.php');
require_once(__ROOT__.'/func/countCart.php');
require_once(__ROOT__.'/func/getIngSupplier.php');
require_once(__ROOT__.'/inc/settings.php');

if($pv_meta['app_ver'] < trim(file_get_contents(__ROOT__.'/VERSION.md'))){
	$upVerLoc = trim(file_get_contents(__ROOT__.'/VERSION.md'));
	if(mysqli_query($conn, "UPDATE pv_meta SET app_ver = '$upVerLoc'")){
		$show_release_notes = true;
	}
}

?>
<!doctype html>
<html lang="en" data-bs-theme="<?=$settings['bs_theme']?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="<?php echo $product.' - '.$ver;?>">
  <meta name="author" content="<?php echo $product.' - '.$ver;?>">
  <title><?php echo $product;?> - Dashboard</title>
  
  <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
  
  <link href="/css/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" type="text/css">
  <link href="/css/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="/css/sb-admin-2.css" rel="stylesheet" />
  <link href="/css/bootstrap-select.min.css" rel="stylesheet">
  <link href="/css/bootstrap-editable.css" rel="stylesheet">
  <link href="/css/datatables.min.css" rel="stylesheet">
  
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  
  <link href="/css/jquery-ui.css" rel="stylesheet">
  <link href="/css/magnific-popup.css" rel="stylesheet">
  
  <link href="/css/vault.css" rel="stylesheet">
  
  <script src="/js/jquery/jquery.min.js"></script>
  <script src="/js/tableHTMLExport.js"></script>
  <script src="/js/jspdf.min.js"></script>
  <script src="/js/jspdf.plugin.autotable.js"></script>
  <script src="/js/datatables.min.js"></script> 
  <script src="/js/magnific-popup.js"></script>
  <script src="/js/jquery-ui.js"></script>
  
  <script src="/js/bootstrap.bundle.min.js"></script>
  
  <script src="/js/bootstrap-select.js"></script>
  <script src="/js/bootstrap-editable.js"></script>
  <script src="/js/bootbox.min.js"></script>
  
  <script src="/js/validate-session.js"></script>

<script>
$(document).ready(function() {

	$(function () {
		$('[rel=tip]').tooltip();
	
		// Show release notes if necessary
		<?php if ($show_release_notes) { ?>
			$('#release_notes').modal('show');
		<?php } ?>
	
		// Check for version updates if needed
	<?php 
	if (isset($disable_updates)) {
		if ($disable_updates === false && isset($settings['chkVersion']) && $settings['chkVersion'] === '1') {
			// If disable_updates is explicitly false and chkVersion is '1', call chkUpdate()
	?>		
			chkUpdate();
	<?php }
		// If disable_updates is true, do nothing (chkUpdate is not called)
	} elseif (isset($settings['chkVersion']) && $settings['chkVersion'] === '1') {
		// If disable_updates is not set, fall back to chkVersion
	?>
		chkUpdate();
	<?php } ?>


	});
	
	
	// Function to update the system
	function updateSYS() {
		$('#sysUpdMsg').html('<div class="alert alert-info"><img src="/img/loading.gif"/> Core system upgrade in progress. Please wait, DO NOT close, refresh or navigate away from this page. The process may take a while...</div>');
		$('#sysUpBtn, #sysUpOk').hide();
	
		$.ajax({
			url: '/core/UpgradeCore.php',
			type: 'GET',
			dataType: 'json',
			success: function (data) {
				let msg = '';
				if (data.success) {
					msg = `<div class="alert alert-success">${data.success}</div>`;
				} else {
					msg = `<div class="alert alert-danger">${data.error}</div>`;
					$('#sysUpBtn').show();
				}
				$('#sysUpdMsg').html(msg);
			},
			error: function () {
				$('#sysUpdMsg').html('<div class="alert alert-danger">Upgrade failed. Please try again later.</div>');
				$('#sysUpBtn').show();
			}
		});
	};
	
	// Function to check for updates
	function chkUpdate() {
		$.ajax({
			url: '/core/checkVer.php',
			type: 'GET',
			data: {
				app_ver: '<?= $ver ?>'
			},
			dataType: 'json',
			success: function (data) {
				if (data.success) {
					let msg = `<button type="button" class="btn btn-outline-primary">${data.success}</button>`;
					$('#chkUpdMsg').html(msg);
				} else if (data.error) {
					$('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
					$('.toast-header').removeClass().addClass('toast-header alert-danger');
					$('.toast').toast('show');
				}
			},
			error: function (xhr, status, error) {
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		});
	};

});

</script>
</head>
<body id="page-top">
  <div id="wrapper" class="d-flex">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark">
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/index.php">
        <div class="sidebar-brand-icon mt-4 mb-4">
          <div class="pvLogo">
            <img src="/img/logo.png" alt="Logo">
          </div>
        </div>
      </a>
      
      <!-- Divider -->
      <hr class="sidebar-divider my-2">

      <li class="nav-item">
        <a class="nav-link" href="/?do=dashboard">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <?php 
          $formulaPages = ['listFormulas', 'genFinishedProduct', 'compareFormulas', 'Formula', 'sellFormula', 'scheduledFormulas', 'batches'];
          $isActiveFormula = in_array($_GET['do'], $formulaPages);
          
          $expand_f = $isActiveFormula ? 'show' : ''; 
          $class_f = $isActiveFormula ? '' : 'collapsed'; 
          $aria_f = $isActiveFormula ? 'true' : 'false';
        ?> 
        <a class="nav-link <?php echo $class_f; ?>" href="#" data-bs-toggle="collapse" data-bs-target="#collapseFormulas" aria-expanded="<?php echo $aria_f; ?>" aria-controls="collapseFormulas">
          <i class="fas fa-fw fa-flask"></i>
          <span>Formula Management</span>
        </a>
        <div id="collapseFormulas" class="collapse <?php echo $expand_f; ?>">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item <?php echo $_GET['do'] === 'listFormulas' || $_GET['do'] === 'Formula' ? 'active' : ''; ?>" href="/?do=listFormulas">Formulas</a>
            <a class="collapse-item <?php echo $_GET['do'] === 'compareFormulas' ? 'active' : ''; ?>" href="/?do=compareFormulas">Compare Formulas</a>
            <a class="collapse-item <?php echo $_GET['do'] === 'genFinishedProduct' ? 'active' : ''; ?>" href="/?do=genFinishedProduct">Finished Product</a>
            <a class="collapse-item <?php echo $_GET['do'] === 'sellFormula' ? 'active' : ''; ?>" href="/?do=sellFormula">Sell Formula</a>
            <a class="collapse-item <?php echo $_GET['do'] === 'scheduledFormulas' ? 'active' : ''; ?>" href="/?do=scheduledFormulas">Scheduled Formulas 
              <span class="badge badge-danger badge-counter"><?php echo countPending(NULL, NULL, $conn); ?></span>
            </a>
            <a class="collapse-item <?php echo $_GET['do'] === 'batches' ? 'active' : ''; ?>" href="/?do=batches">Batch history</a>
          </div>
        </div>
      </li>

      <li class="nav-item">
        <?php 
          $inventoryPages = ['ingredients', 'bottles', 'accessories', 'suppliers', 'customers', 'compounds'];
          $isActiveInventory = in_array($_GET['do'], $inventoryPages);
          $expand = $isActiveInventory ? 'show' : ''; 
          $class = $isActiveInventory ? '' : 'collapsed'; 
          $aria = $isActiveInventory ? 'true' : 'false';
        ?> 
        <a class="nav-link <?php echo $class; ?>" href="#" data-bs-toggle="collapse" data-bs-target="#collapseInventory" aria-expanded="<?php echo $aria; ?>" aria-controls="collapseInventory">
          <i class="fas fa-fw fa-warehouse"></i>
          <span>Inventory</span>
        </a>
        <div id="collapseInventory" class="collapse <?php echo $expand; ?>">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item <?php echo $_GET['do'] === 'ingredients' ? 'active' : ''; ?>" href="/?do=ingredients">Ingredients</a>
            <a class="collapse-item <?php echo $_GET['do'] === 'suppliers' ? 'active' : ''; ?>" href="/?do=suppliers">Suppliers</a>
            <a class="collapse-item <?php echo $_GET['do'] === 'customers' ? 'active' : ''; ?>" href="/?do=customers">Customers</a>
            <a class="collapse-item <?php echo $_GET['do'] === 'compounds' ? 'active' : ''; ?>" href="/?do=compounds">Compounds</a>
            <a class="collapse-item <?php echo $_GET['do'] === 'bottles' ? 'active' : ''; ?>" href="/?do=bottles">Bottles</a>
            <a class="collapse-item <?php echo $_GET['do'] === 'accessories' ? 'active' : ''; ?>" href="/?do=accessories">Accessories</a>
          </div>
        </div>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="/?do=IFRA">
          <i class="fas fa-fw fa-university"></i>
          <span>IFRA Library</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/?do=statistics">
          <i class="fas fa-fw fa-chart-area"></i>
          <span>Statistics</span>
        </a>
      </li>

      <hr class="sidebar-divider d-none d-md-block">

      <li class="nav-item">
        <a class="nav-link" href="/?do=marketplace">
          <i class="fas fa-fw fa-store"></i>
          <span>Marketplace</span>
        </a>
      </li>

      <hr class="sidebar-divider d-none d-md-block">

      <li class="nav-item">
        <a class="nav-link" href="/?do=genSDS">
          <i class="fas fa-square-poll-horizontal"></i>
          <span>My SDSs</span>
        </a>
      </li>

      <hr class="sidebar-divider d-none d-md-block">
    </ul>

    <?php
      if ($_GET['do'] == 'Formula') {
          require_once(__ROOT__.'/pages/formula.php');
      } elseif ($_GET['do'] == 'ingredients') {
          require_once(__ROOT__.'/pages/ingredients.php');
      } elseif ($_GET['do'] == 'settings') {
          require_once(__ROOT__.'/pages/settings.php');
      } elseif ($_GET['do'] == 'marketplace') {
          require_once(__ROOT__.'/pages/views/pvLibrary/marketPlace.php');
      } elseif ($_GET['do'] == 'statistics') {
          require_once(__ROOT__.'/pages/statistics.php');
      } elseif ($_GET['do'] == 'IFRA') {
          require_once(__ROOT__.'/pages/views/regulatory/IFRA.php');
      } elseif ($_GET['do'] == 'listFormulas') {
          require_once(__ROOT__.'/pages/listFormulas.php');   
      } elseif ($_GET['do'] == 'genFinishedProduct') {
          require_once(__ROOT__.'/pages/genFinishedProduct.php');        
      } elseif ($_GET['do'] == 'bottles') {
          require_once(__ROOT__.'/pages/views/inventory/bottles.php');        
      } elseif ($_GET['do'] == 'accessories') {
          require_once(__ROOT__.'/pages/views/inventory/accessories.php');
      } elseif ($_GET['do'] == 'batches') {
          require_once(__ROOT__.'/pages/views/formula/batches.php');
      } elseif ($_GET['do'] == 'scheduledFormulas') {
          require_once(__ROOT__.'/pages/scheduledFormulas.php');    
      } elseif ($_GET['do'] == 'cart') {
          require_once(__ROOT__.'/pages/cart.php');    
      } elseif ($_GET['do'] == 'suppliers') {
          require_once(__ROOT__.'/pages/views/inventory/suppliers.php');
      } elseif ($_GET['do'] == 'sellFormula') {
          require_once(__ROOT__.'/pages/sellFormula.php');
      } elseif ($_GET['do'] == 'customers') {
          require_once(__ROOT__.'/pages/views/inventory/customers.php');
      } elseif ($_GET['do'] == 'compareFormulas') {
          require_once(__ROOT__.'/pages/compareFormulas.php');
      } elseif ($_GET['do'] == 'compounds') {
          require_once(__ROOT__.'/pages/views/inventory/compounds.php');
      } elseif ($_GET['do'] == 'genSDS') {
          require_once(__ROOT__.'/pages/views/regulatory/listSDS.php');                
      } else {
          require_once(__ROOT__.'/pages/dashboard.php');
      }
      
      require_once(__ROOT__.'/pages/footer.php'); 
    ?>

    <!-- TOAST -->
    <div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 11">
      <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
        <div class="toast-header">
          <strong class="me-auto" id="toast-title">...</strong>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>

    <!-- RELEASE NOTES -->
    <div class="modal fade" id="release_notes" tabindex="-1" aria-labelledby="release_notes_label" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="release_notes_label">Release Notes</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Thanks for updating to version <strong><?php echo $ver;?></strong>
            <p><pre><?php echo file_get_contents('releasenotes.md','r');?></pre></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

</body>

</html>

<?php
require('./inc/sec.php');
if(file_exists('./inc/config.php') == FALSE){
	session_destroy();
	header('Location: login.php');
}
require_once('./inc/config.php');
require_once('./inc/product.php');
require_once('./inc/opendb.php');
require_once('./func/calcCosts.php');
require_once('./func/calcPerc.php');
require_once('./func/checkDupes.php');
require_once('./func/checkIng.php');
require_once('./func/checkAllergen.php');
require_once('./func/getIngUsage.php');
require_once('./func/checkVer.php');
//require_once('./func/formulaProfile.php');
require_once('./func/getIFRAtypes.php');
require_once('./func/searchIFRA.php');
require_once('./func/formatBytes.php');
require_once('./func/countElement.php');
require_once('./func/goShopping.php');
require_once('./libs/fpdf.php');
require_once('./func/genBatchID.php');
require_once('./func/genBatchPDF.php');
require_once('./func/ml2L.php');
require_once('./func/validateFormula.php');
require_once('./func/pvFileGet.php');
require_once('./func/countPending.php');
require_once('./func/countCart.php');
require_once('./func/pvOnline.php');

require('./inc/settings.php');

?>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="<?php echo $product.' - '.$ver;?>">
  <meta name="author" content="JBPARFUM">
  <title><?php echo $product;?> - Dashboard</title>
  
  <link href="css/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="css/sb-admin-2.css" rel="stylesheet">
  <link href="css/bootstrap-select.min.css" rel="stylesheet">
  <link href="css/bootstrap-editable.css" rel="stylesheet">

  <script src="js/jquery/jquery.min.js"></script>
  <script src="js/tableHTMLExport.js"></script>
  <script src="js/jspdf.min.js"></script>
  <script src="js/jspdf.plugin.autotable.js"></script>
  <script src="js/bootstrap.min.js"></script>
  
  <link href="css/datatables.min.css" rel="stylesheet" type="text/css" />
  
  <script src="js/datatables.min.js"></script>
  <script src="js/magnific-popup.js"></script>
 
  <link href="css/bootstrap.min.css" rel="stylesheet">
  
  <script src="js/bootstrap-select.js"></script>
  <script src="js/bootstrap-editable.js"></script>
  
  <script src='js/tipsy.js'></script>
  <script src="js/jquery-ui.js"></script>
  
  <link href="css/jquery-ui.css" rel="stylesheet">
  <link href="css/tipsy.css" rel="stylesheet" />
  <link href="css/magnific-popup.css" rel="stylesheet" />
  <link href="css/vault.css" rel="stylesheet">
  
<script type='text/javascript'>

$(document).ready(function() {
	$('a[rel=tipsy]').tipsy();
	
	$('.popup-link').magnificPopup({
		type: 'iframe',
  		closeOnContentClick: false,
		closeOnBgClick: false,
  		showCloseBtn: true,
	});
	
    $('#tdData,#tdDataSup,#tdDataCat,#tdDataUsers,#tdDataCustomers').DataTable({
	    "paging":   true,
		"info":   true,
		"lengthMenu": [[20, 35, 60, -1], [20, 35, 60, "All"]]
	});
});

function updateDB() {
$.ajax({ 
    url: 'pages/operations.php', 
	type: 'GET',
    data: {
		do: "db_update"
		},
	dataType: 'html',
    success: function (data) {
	  $('#msg').html(data);
    }
  });
};


function list_formulas(){
$.ajax({ 
    url: 'pages/listFormulas.php', 
	dataType: 'html',
		success: function (data) {
			$('#list_formulas').html(data);
		}
	});
}

list_formulas();
</script>
</head>

<body id="page-top">
  <div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark">

      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">            
        <p></p>
        <p></p>
        <p></p>
          <img src="img/logo.png" witdh="150px" height="120px">
        </div>
      </a>        
      <p></p>
      <p></p>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link" href="?do=dashboard">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>
    
        <li class="nav-item">
        <a class="nav-link" href="?do=listFormulas">
          <i class="fas fa-fw fa-flask"></i>
          <span>Formulas</span></a>
      </li>

        <li class="nav-item">
        <a class="nav-link" href="?do=genFinishedProduct">
          <i class="fas fa-fw fa-spray-can"></i>
          <span>Generate Finished Product</span></a>
      </li>
      
      <li class="nav-item">
      <?php 
	  if($_GET['do'] == 'ingredients' || $_GET['do'] == 'bottles' || $_GET['do'] == 'lids' || $_GET['do'] == 'batches' || $_GET['do'] == 'suppliers'){ 
	  	$expand = 'show'; 
		$class = ''; 
		$aria = 'true'; 
	  }else{ 
	  	$exapnd = ''; 
		$class = 'collapsed'; 
		$aria = 'false'; 
	  }
	  ?> 
        <a class="nav-link <?php echo $class; ?>" href="#" data-toggle="collapse" data-target="#collapseInventoty" aria-expanded="<?php echo $aria; ?>" aria-controls="collapseInventoty">
          <i class="fas fa-fw fa-warehouse"></i>
          <span>Inventory</span>
        </a>
        <div id="collapseInventoty" class="collapse <?php echo $expand;?>" aria-labelledby="headingInventory" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item <?php if($_GET['do'] == 'ingredients'){ echo 'active';}?>" href="?do=ingredients">Ingredients</a>
            <a class="collapse-item <?php if($_GET['do'] == 'batches'){ echo 'active';}?>" href="?do=batches">Batch history</a>
            <a class="collapse-item <?php if($_GET['do'] == 'suppliers'){ echo 'active';}?>" href="?do=suppliers">Suppliers</a>
            <a class="collapse-item <?php if($_GET['do'] == 'bottles'){ echo 'active';}?>" href="?do=bottles">Bottles</a>
            <a class="collapse-item <?php if($_GET['do'] == 'lids'){ echo 'active';}?>" href="?do=lids">Bottle Lids</a>
          </div>
        </div>
      </li>
    
        <li class="nav-item">
        <a class="nav-link" href="?do=IFRA">
          <i class="fas fa-fw fa-university"></i>
          <span>IFRA Library</span></a>
        </li>

        <li class="nav-item">
        <a class="nav-link" href="?do=insights">
          <i class="fas fa-fw fa-chart-area"></i>
          <span>Insights</span></a>
        </li>
        <?php if($settings['pv_maker'] && $settings['pv_maker_host']){?>
        <li class="nav-item">
        <a class="nav-link" href="?do=pvmaker">
          <i class="fas fa-fw fa-robot"></i>
          <span>PV Maker</span></a>
        </li>
        <?php } ?>
      <hr class="sidebar-divider d-none d-md-block">
      
        <li class="nav-item">
        <a class="nav-link" href="?do=tools">
          <i class="fas fa-fw fa-tools"></i>
          <span>Calculation Tools</span></a>
       </li>

      <hr class="sidebar-divider d-none d-md-block">
            
        <li class="nav-item">
        <a class="nav-link" href="?do=settings">
          <i class="fas fa-fw fa-cog"></i>
          <span>Settings</span></a>
       </li>
      
      <hr class="sidebar-divider d-none d-md-block">
    </ul>
    <?php
		if($_GET['do'] == 'Formula'){
			require 'pages/formula.php';
		}elseif($_GET['do'] == 'addFormula'){
			require 'pages/addFormula.php';
		}elseif($_GET['do'] == 'ingredients'){
			require 'pages/ingredients.php';
		}elseif($_GET['do'] == 'settings'){
			require 'pages/settings.php';
		}elseif($_GET['do'] == 'insights'){
			require 'pages/insights.php';
		}elseif($_GET['do'] == 'IFRA'){
			require 'pages/IFRA.php';
		}elseif($_GET['do'] == 'listFormulas'){
			//require 'pages/listFormulas.php';
		?>
        <div id="content-wrapper" class="d-flex flex-column">
			<?php require_once('pages/top.php'); ?>
            
        <div class="container-fluid">
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="?do=listFormulas">Formulas</a></h2>
              <div id="inMsg"></div>
            </div>
            <div id="list_formulas"><div class="loader"></div></div>
           </div>
        </div>
      </div>
	</div>
		<?php
		}elseif($_GET['do'] == 'genFinishedProduct'){
			require 'pages/genFinishedProduct.php';		
		}elseif($_GET['do'] == 'bottles'){
			require 'pages/bottles.php';		
		}elseif($_GET['do'] == 'addBottle'){
			require 'pages/addBottle.php';		
		}elseif($_GET['do'] == 'lids'){
			require 'pages/lids.php';				
		}elseif($_GET['do'] == 'addLid'){
			require 'pages/addLid.php';	
		}elseif($_GET['do'] == 'batches'){
			require 'pages/batches.php';				
		}elseif($_GET['do'] == 'tools'){
			require 'pages/tools.php';	
		}elseif($_GET['do'] == 'todo'){
			require 'pages/todo.php';	
		}elseif($_GET['do'] == 'cart'){
			require 'pages/cart.php';	
		}elseif($_GET['do'] == 'suppliers'){
			require 'pages/suppliers.php';
			
		}elseif($_GET['do'] == 'pvmaker' && $settings['pv_maker'] == '1'){
			require 'pages/pvmaker.php';	
			
		}else{
			require 'pages/dashboard.php';
		}
	?>
	<?php require_once("pages/footer.php"); ?>
</body>
</html>

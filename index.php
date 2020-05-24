<?php

require('./inc/sec.php');
if(file_exists('./inc/config.php') == FALSE){
	session_destroy();
	header('Location: /login.php');
}
require_once('./inc/config.php');
require_once('./inc/product.php');
require_once('./inc/opendb.php');
require_once('./func/calcCosts.php');
require_once('./func/calcPerc.php');
require_once('./func/checkDupes.php');
require_once('./func/checkIng.php');
require_once('./func/getIngUsage.php');
require_once('./func/checkVer.php');
require_once('./func/formulaProfile.php');
require_once('./func/getIFRAtypes.php');
require_once('./func/searchIFRA.php');
require_once('./func/formatBytes.php');


require('./inc/settings.php');

if($_GET['action'] == 'delete' && $_GET['name']){
	$dname = mysqli_real_escape_string($conn, $_GET['name']);
	if(mysqli_query($conn, "DELETE FROM formulas WHERE name = '$dname'")){
		mysqli_query($conn, "DELETE FROM formulasMetaData WHERE name = '$dname'");
		$msg = '<div class="alert alert-success alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>Formula: </strong>'.$dname.' deleted!
		</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>Error</strong> deleting '.$dname.' formula!
		</div>';
	}
}

$formulas_c = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM formulas GROUP BY name"));

$ac_c = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE type = 'AC'"));
$eo_c = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE type = 'EO'"));
$sup_c = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingSuppliers"));
$ifra_c = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM IFRALibrary"));
$all_ing_c = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients"));
$uncat_ing_c = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE type IS NULL"));
$cat_c = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingCategory"));

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="<?php echo $product.' - '.$ver;?>">
  <meta name="author" content="JBPARFUM">
  <title><?php echo $product;?> - Dashboard</title>
  
  <link href="css/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

  <link href="css/sb-admin-2.css" rel="stylesheet">
  
  <link href="css/bootstrap-select.min.css" rel="stylesheet">
  <link href="css/bootstrap-editable.css" rel="stylesheet">

  <script src="js/jquery/jquery.min.js"></script>

  <script src="js/tableHTMLExport.js"></script>

  <script src="js/bootstrap.min.js"></script>
  
  <link rel="stylesheet" type="text/css" href="css/datatables.min.css"/>
  <script type="text/javascript" src="js/datatables.min.js"></script>
  
  <script src="js/magnific-popup.js"></script>
 
  <link href="css/bootstrap.min.css" rel="stylesheet">
  
  <script src="js/bootstrap-select.js"></script>
  <script src="js/bootstrap-editable.js"></script>
  
  <script src='js/tipsy.js'></script>
  <script src="./js/jquery-ui.js"></script>
  
  <link rel="stylesheet" href="./css/jquery-ui.css">
  <link href="css/tipsy.css" rel="stylesheet" />
  
  <link href="css/magnific-popup.css" rel="stylesheet" />

  <link href="css/vault.css" rel="stylesheet">
  
<script type='text/javascript'>
$(document).ready(function() {
	$('a[rel=tipsy]').tipsy();
	
	$('.popup-link').magnificPopup({
		type: 'iframe',
  		//modal: 'true',
  		showCloseBtn: 'true',
  		closeOnBgClick: 'false',
  		//closeBtnInside: 'true'
	});
	
	
    $('#tdData').DataTable({
	    "paging":   true,
		"info":   true,
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
	});

});  
</script>
</head>

<body id="page-top">
  <div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark">

      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/">
        <div class="sidebar-brand-icon">            
        <p></p>
        <p></p>
        <p></p>
          <img src="/img/logo.png" witdh="150px" height="120px">
        </div>
      </a>        
      <p></p>
      <p></p>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link" href="/?do=dashboard">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>
    
        <li class="nav-item">
        <a class="nav-link" href="/?do=listFormulas">
          <i class="fas fa-fw fa-flask"></i>
          <span>Formulas</span></a>
      </li>

        <li class="nav-item">
        <a class="nav-link" href="/?do=ingredients">
          <i class="fas fa-fw fa-vial"></i>
          <span>Ingredients</span></a>
      </li>
      
        <li class="nav-item">
        <a class="nav-link" href="/?do=IFRA">
          <i class="fas fa-fw fa-university"></i>
          <span>IFRA Library</span></a>
      </li>

        <li class="nav-item">
        <a class="nav-link" href="/?do=insights">
          <i class="fas fa-fw fa-chart-area"></i>
          <span>Insights</span></a>
      </li>
      
        <li class="nav-item">
        <a class="nav-link" href="/?do=settings">
          <i class="fas fa-fw fa-cog"></i>
          <span>Settings</span></a>
      </li>
      
      <hr class="sidebar-divider d-none d-md-block">
      
        <li class="nav-item">
        <a class="nav-link" href="/?do=logout">
          <i class="fas fa-fw fa-sign-out-alt"></i>
          <span>Logout</span></a>
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
		}elseif($_GET['do'] == 'addIngredient'){
			require 'pages/addIngredient.php';
		}elseif($_GET['do'] == 'insights'){
			require 'pages/insights.php';
		}elseif($_GET['do'] == 'IFRA'){
			require 'pages/IFRA.php';
		}elseif($_GET['do'] == 'listFormulas'){
			require 'pages/listFormulas.php';
		
		
		}elseif($_GET['do'] == 'logout'){
			if(isset($_SESSION['parfumvault'])) {
				unset($_SESSION['parfumvault']);
			}
			session_unset();
			header('Location: /login.php');
	 
		}else{
			require 'pages/dashboard.php';
		}
	?>
		<?php require_once("./pages/footer.php"); ?>

</body>
</html>

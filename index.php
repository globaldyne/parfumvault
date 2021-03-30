<?php
require('./inc/sec.php');
if(file_exists('./inc/config.php') == FALSE){
	session_destroy();
	header('Location: login.php');
}
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/calcCosts.php');
require_once(__ROOT__.'/func/calcPerc.php');
require_once(__ROOT__.'/func/checkDupes.php');
require_once(__ROOT__.'/func/checkIng.php');
require_once(__ROOT__.'/func/checkAllergen.php');
require_once(__ROOT__.'/func/getIngUsage.php');
require_once(__ROOT__.'/func/checkVer.php');
require_once(__ROOT__.'/func/getIFRAtypes.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/func/formatBytes.php');
require_once(__ROOT__.'/func/countElement.php');
require_once(__ROOT__.'/func/goShopping.php');
require_once(__ROOT__.'/libs/fpdf.php');
require_once(__ROOT__.'/func/genBatchID.php');
require_once(__ROOT__.'/func/genBatchPDF.php');
require_once(__ROOT__.'/func/ml2L.php');
require_once(__ROOT__.'/func/validateFormula.php');
require_once(__ROOT__.'/func/pvFileGet.php');
require_once(__ROOT__.'/func/countPending.php');
require_once(__ROOT__.'/func/countCart.php');
require_once(__ROOT__.'/func/pvOnline.php');

require(__ROOT__.'/inc/settings.php');

if($pv_meta['app_ver'] < trim(file_get_contents(__ROOT__.'/VERSION.md'))){
	$upVerLoc = trim(file_get_contents(__ROOT__.'/VERSION.md'));
	$db_ver   = trim(file_get_contents(__ROOT__.'/db/schema.ver'));
  	if(file_exists(__ROOT__.'/db/updates/update_'.$pv_meta['app_ver'].'-'.$db_ver.'.sql') === TRUE){
		if($pv_meta['app_ver'] < $db_ver){	
			$db_up_msg = '<div class="alert alert-warning alert-dismissible"><strong>Your database schema needs to be updated ('.$db_ver.'). Please <a href="pages/maintenance.php?do=backupDB">backup</a> your database first and then click <a href="javascript:updateDB()">here to update the db schema.</a></strong></div>';
			}
		}else{
			mysqli_query($conn, "UPDATE pv_meta SET schema_ver = '$upVerLoc'");
		}
		if(mysqli_query($conn, "UPDATE pv_meta SET app_ver = '$upVerLoc'")){
			$show_release_notes = true;
		}
}
?>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="<?php echo $product.' - '.$ver;?>">
  <meta name="author" content="JBPARFUM">
  <title><?php echo $product;?> - Dashboard</title>
  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">

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
	<?php if($show_release_notes){?>
	$('#release_notes').modal('show');
	<?php } ?>
	$('.popup-link').magnificPopup({
		type: 'iframe',
  		closeOnContentClick: false,
		closeOnBgClick: false,
  		showCloseBtn: true,
	});
	
    $('#tdData,#tdDataSup,#tdDataCustomers').DataTable({
	    "paging":   true,
		"info":   true,
		"lengthMenu": [[20, 35, 60, -1], [20, 35, 60, "All"]]
	});
	
	list_formulas();
	list_ingredients();

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
};
	
function list_ingredients(){
	$.ajax({ 
		url: 'pages/listIngredients.php', 
		dataType: 'html',
			success: function (data) {
				$('#list_ingredients').html(data);
			}
		});
};

function list_users(){
	$.ajax({ 
		url: 'pages/listUsers.php', 
		dataType: 'html',
			success: function (data) {
				$('#list_users').html(data);
			}
		});
};

function list_cat(){
	$.ajax({ 
		url: 'pages/listCat.php', 
		dataType: 'html',
			success: function (data) {
				$('#list_cat').html(data);
			}
		});
};
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
        <a class="nav-link" href="?do=sellFormula">
          <i class="fas fa-fw fa-money-check"></i>
          <span>Sell Formula</span></a>
      </li>
            
      <li class="nav-item">
      <?php 
	  if($_GET['do'] == 'ingredients' || $_GET['do'] == 'bottles' || $_GET['do'] == 'lids' || $_GET['do'] == 'batches' || $_GET['do'] == 'suppliers' || $_GET['do'] == 'customers'){ 
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
            <a class="collapse-item <?php if($_GET['do'] == 'customers'){ echo 'active';}?>" href="?do=customers">Customers</a>
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
        <a class="nav-link" href="?do=statistics">
          <i class="fas fa-fw fa-chart-area"></i>
          <span>Statistics</span></a>
        </li>
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
			require_once(__ROOT__.'/pages/formula.php');
		}elseif($_GET['do'] == 'ingredients'){
			require_once(__ROOT__.'/pages/ingredients.php');
		}elseif($_GET['do'] == 'settings'){
			require_once(__ROOT__.'/pages/settings.php');
		}elseif($_GET['do'] == 'statistics'){
			require_once(__ROOT__.'/pages/statistics.php');
		}elseif($_GET['do'] == 'IFRA'){
			require_once(__ROOT__.'/pages/IFRA.php');
		}elseif($_GET['do'] == 'listFormulas'){
		?>
        <div id="content-wrapper" class="d-flex flex-column">
			<?php require_once(__ROOT__.'/pages/top.php'); ?>
            
        <div class="container-fluid">
          <div>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h2 class="m-0 font-weight-bold text-primary"><a href="javascript:list_formulas()">Formulas</a></h2>
              <div id="inMsg"></div>
            </div>
            <div id="list_formulas">
            	<div class="loader-center">
                	<div class="loader"></div>
                    <div class="loader-text"></div>
                </div>
             </div>
           </div>
          </div>
        </div>
	   </div>
		<?php
		}elseif($_GET['do'] == 'genFinishedProduct'){
			require_once(__ROOT__.'/pages/genFinishedProduct.php');		
		}elseif($_GET['do'] == 'bottles'){
			require_once(__ROOT__.'/pages/bottles.php');		
		}elseif($_GET['do'] == 'addBottle'){
			require_once(__ROOT__.'/pages/addBottle.php');		
		}elseif($_GET['do'] == 'lids'){
			require_once(__ROOT__.'/pages/lids.php');
		}elseif($_GET['do'] == 'addLid'){
			require_once(__ROOT__.'/pages/addLid.php');	
		}elseif($_GET['do'] == 'batches'){
			require_once(__ROOT__.'/pages/batches.php');				
		}elseif($_GET['do'] == 'tools'){
			require_once(__ROOT__.'/pages/tools.php');	
		}elseif($_GET['do'] == 'todo'){
			require_once(__ROOT__.'/pages/todo.php');	
		}elseif($_GET['do'] == 'cart'){
			require_once(__ROOT__.'/pages/cart.php');	
		}elseif($_GET['do'] == 'suppliers'){
			require_once(__ROOT__.'/pages/suppliers.php');
		}elseif($_GET['do'] == 'sellFormula'){
			require_once(__ROOT__.'/pages/sellFormula.php');
		}elseif($_GET['do'] == 'customers'){
			require_once(__ROOT__.'/pages/customers.php');
			
		}else{
			require_once(__ROOT__.'/pages/dashboard.php');
		}
	?>
<?php require_once(__ROOT__.'/pages/footer.php'); ?>
<?php if(isset($show_release_notes)){ ?>
<!--RELEASE NOTES-->
<div class="modal fade" id="release_notes" tabindex="-1" role="dialog" aria-labelledby="release_notes" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="release_notes">Release Notes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		Thanks for updating to version <strong><?php echo $ver;?></strong> 
	    <p><pre><?php echo file_get_contents('releasenotes.md','r');?></pre></p>
	    <div class="modal-footer">
	     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	   </div>
    </div>
  </div>
</div>
<?php } ?>
</body>
</html>

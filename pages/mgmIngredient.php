<?php 
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/formatBytes.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/func/sanChar.php');
require_once(__ROOT__.'/func/profileImg.php');


$ingID = sanChar(mysqli_real_escape_string($conn, base64_decode($_GET["id"])));

$res_ingTypes = mysqli_query($conn, "SELECT id,name FROM ingTypes ORDER BY name ASC");
$res_ingStrength = mysqli_query($conn, "SELECT id,name FROM ingStrength ORDER BY name ASC");
$res_ingCategory = mysqli_query($conn, "SELECT id,image,name,notes FROM ingCategory ORDER BY name ASC");
$res_ingProfiles = mysqli_query($conn, "SELECT id,name FROM ingProfiles ORDER BY id ASC");

$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredients WHERE name = '$ingID'"));

?>
<!doctype html>
<html lang="en" data-bs-theme="<?=$settings['bs_theme']?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
    <?php if($ing['id']){ ?>

		<title><?=$ing['name']?></title>

	<?php }else{ ?>
    
    	<title>Add ingredient</title>

    <?php } ?>    
	<link href="/css/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    
	<script src="/js/jquery/jquery.min.js"></script>
	<script src="/js/bootstrap.bundle.min.js"></script>
	<script src="/js/bootstrap-select.js"></script>
	<script src="/js/bootstrap-editable.js"></script>
	<script src="/js/datatables.min.js"></script>
	<script src="/js/bootbox.min.js"></script>

    <link href="/css/datatables.min.css" rel="stylesheet"/>
	<link href="/css/sb-admin-2.css" rel="stylesheet">
	<link href="/css/bootstrap-select.min.css" rel="stylesheet">
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/vault.css" rel="stylesheet">
	<link href="/css/bootstrap-editable.css" rel="stylesheet">
	<link href="/css/mgmIngredient.css" rel="stylesheet">
	
<script>
var myIngName = "<?=$ing['name']?>";
var newIngName = "<?=$_GET["newIngName"]?>";
var newIngCAS = "<?=$_GET["newIngCAS"]?>";
var myIngID;
<?php if($ing['id']){ ?>

myIngID = "<?=$ing['id']?>";
var myCAS = "<?=$ing['cas']?>";
var myPCH = "<?=$settings['pubChem']?>";
<?php } ?>


</script>
<style>
body {
  overflow-y:hidden;
}

</style>
</head>

<body>
	<div class="mgm-ing-theme mt-4">
		<div class="container mgm-ing-bk">
			<div class="mgm-column mgm-visible-xl mgm-col-xl-5">
				<h1 class="mgmIngHeader mgmIngHeader-with-separator"><?php if($ingID){ echo $ing['name'];?>
				<div class="btn-group">
					<button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
					<div class="dropdown-menu">
						<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#cloneIng"><i class="fa-solid fa-copy mx-2"></i>Duplicate ingredient</a></li>
						<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#renameIng"><i class="fa-regular fa-pen-to-square mx-2"></i>Rename ingredient</a></li>
                        <li><a class="dropdown-item" href="/pages/export.php?format=json&kind=single-ingredient&id=<?=$ing['id']?>"><i class="fas fa-download mx-2"></i>Export as JSON</a></li>
						<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#genDOC"><i class="fa-solid fa-file-prescription mx-2"></i>Generate document</a></li>
						<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#genQRC"><i class="fa-solid fa-qrcode mx-2"></i>Generate QR Code</a></li>
					</div>
				</div>
			<?php }else {?>
				Add ingredient
			<?php } ?>
		</h1>
		<span class="mgmIngHeaderCAS" id="mgmIngHeaderCAS"><?=$ing['cas']?></span>
	</div>

	<div id="ingMsg"><?=$msg?></div>
	<div id="ingOverview"></div>
	<div class="mgmIngHeader-with-separator-full"></div>
	<!-- Nav tabs -->
	<ul class="nav nav-tabs mb-3" role="tablist">
		<li class="nav-item" role="presentation">
        <a href="#general" id="general_tab" class="nav-link active" aria-selected="true" role="tab" data-bs-toggle="tab"><i class="fa fa-table mx-2"></i>General</a>
       	</li>
		<?php if($ingID){?>
		<li class="nav-item" role="presentation">
        	<a href="#usage_limits" id="usage_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-bong mx-2"></i>Usage &amp; Limits</a>
        </li>
        
		<li class="nav-item" role="presentation">
        	<a href="#supply" id="sups_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-shopping-cart mx-2"></i>Supply</a>
        </li>
			<li class="nav-item" role="presentation">
            	<a href="#tech_data" id="techs_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-cog mx-2"></i>Technical Data</a>
            </li>
			<li class="nav-item" role="presentation">
            	<a href="#documents" id="docs_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-file-alt mx-2"></i>Documents</a>
            </li>
			<li class="nav-item" role="presentation">
            	<a href="#synonyms" id="synonyms_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-bookmark mx-2"></i>Synonyms</a>
            </li>
			<li class="nav-item" role="presentation">
            	<a href="#note_impact" id="impact_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-magic mx-2"></i>Note Impact</a>
            </li>
			<li class="nav-item" role="presentation">
            	<a href="#tech_composition" id="cmps_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-th-list mx-2"></i>Composition</a>
            </li>
			<li class="nav-item" role="presentation">
            <a href="#safety_info" id="safety_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-biohazard mx-2"></i>Safety</a>
            </li>
			<?php if($settings['pubChem'] == '1' && $ing['cas']){?>
				<li class="nav-item" role="presentation">
                	<a href="#pubChem" id="pubChem_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-atom mx-2"></i>Pub Chem</a>
                </li>
			<?php } ?>  
			<!--
            <li class="nav-item" role="presentation">
            	<a href="#privacy" id="privacy_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-user-secret mx-2"></i>Privacy</a>
             </li>
             -->
			<li class="nav-item" role="presentation">
            	<a href="#whereUsed" id="whereUsed_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-random mx-2"></i>Where used?</a>
            </li>
            <li class="nav-item" role="presentation">
            	<a href="#ingRep" id="reps_tab" class="nav-link" aria-selected="false" role="tab" data-bs-toggle="tab"><i class="fa fa-exchange-alt mx-2"></i>Replacements</a>
            </li>
		<?php } ?>
	</ul>
	<div class="tab-content">
    
    <div class="tab-pane active" id="general">
        <div id="fetch_generalData">
        	<div class="row justify-content-md-center">
        		<div class="loader"></div>
            </div>
        </div>
    </div>
	<!--general tab-->

	<?php if($ingID){?>
    <div class="tab-pane fade" id="usage_limits">
        <div id="msg_usage"></div>
        <div id="fetch_usageData">
            <div class="row justify-content-md-center">
            	<div class="loader"></div>
         	</div>
    	</div>
    </div>
    
    <div class="tab-pane fade" id="supply">
        <div id="msg_sup"></div>
        <div id="fetch_suppliers">
        	<div class="row justify-content-md-center">
        		<div class="loader"></div>
            </div>
        </div>
    </div>
    
    <div class="tab-pane fade" id="documents">
        <div id="msg_docs"></div>
        <div id="fetch_documents">
        	<div class="row justify-content-md-center">
        		<div class="loader"></div>
            </div>
        </div>
    </div>
    
    <div class="tab-pane fade" id="synonyms">
        <div id="msg_syn"></div>
        <div id="fetch_synonyms">
        	<div class="row justify-content-md-center">
        		<div class="loader"></div>
            </div>
        </div>
    </div>
    
    <div class="tab-pane fade" id="tech_data">
        <div id="fetch_tech_data">
        	<div class="row justify-content-md-center">
        		<div class="loader"></div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="safety_info">
        <div id="fetch_safety">
            <div class="row justify-content-md-center">
                <div class="loader"></div>
             </div>
        </div>
    </div>

    <div class="tab-pane fade" id="note_impact">
        <div id="fetch_impact">
            <div class="row justify-content-md-center">
                <div class="loader"></div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="whereUsed">
        <div id="fetch_whereUsed">
        	<div class="row justify-content-md-center">
        		<div class="loader"></div>
         	</div>
        </div>
    </div>

    <div class="tab-pane fade" id="tech_composition">
        <div id="fetch_composition">
            <div class="row justify-content-md-center">
                <div class="loader"></div>
            </div>
        </div>
    </div>

<?php if($settings['pubChem'] == '1' && $ing['cas']){?>
	<div class="tab-pane fade" id="pubChem">
		<div id="pubChemData">
        	<div class="row justify-content-md-center">
        		<div class="loader"></div>
            </div>
        </div>
	</div>
<?php } ?>

    <div class="tab-pane fade" id="privacy">
        <div id="fetch_privacy">
            <div class="row justify-content-md-center">
                <div class="loader"></div>
            </div>
        </div>
    </div>

<?php } ?>
    
    <div class="tab-pane fade" id="ingRep">
        <div id="fetch_replacements">
            <div class="row justify-content-md-center">
                <div class="loader"></div>
            </div>
        </div>
    </div>

<!-- Modal Duplicate-->
<div class="modal fade" id="cloneIng" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="cloneIng" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Duplicate ingredient <?php echo $ing['name']; ?></h5>
			</div>
			<div class="modal-body">
				<div id="clone_msg"></div>
				<label for="cloneIngName" class="form-label">Name</label>
				<input class="form-control" name="cloneIngName" id="cloneIngName" type="text" value="" />            
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<input type="submit" name="button" class="btn btn-primary" id="cloneME" value="Duplicate">
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Rename-->
<div class="modal fade" id="renameIng" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="renameIng" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Rename ingredient <?php echo $ing['name']; ?></h5>
			</div>
			<div class="modal-body">
            	<div id="warn"><div class="alert alert-warning"><strong>Warning:</strong> If you rename the ingredient, will affect any formulas that using it as well. Please refer to <strong>Where Used</strong> section to get a list of formulas if any.</div></div>
				<div id="rename_msg"></div>
				<label for="renameIngName" class="form-label">New name</label>
				<input class="form-control" name="renameIngName" id="renameIngName" type="text" value="" />            
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<input type="submit" name="button" class="btn btn-primary" id="renameME" value="Rename">
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal QR Code-->
<div class="modal fade" id="genQRC" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="genQRC" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php echo $ing['name']; ?></h5>
			</div>
			<div class="modal-body">
            	<div class="alert alert-info">Use PV APP to scan the QR</div>
				
				<div id="QRC" class="d-flex justify-content-center"></div>   
                <hr />
                <div class="alert alert-info">Download from the App Store</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Gen DOC-->
<div class="modal fade" id="genDOC" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="genDOC" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Generate document for <?php echo $ing['name']; ?></h5>
			</div>
			<div class="modal-body">
                <div id="warn">
                <div class="alert alert-warning"><strong><i class="fa-solid fa-triangle-exclamation mx-2"></i>TECH PREVIEW: This feature its under development and in preview state, use with caution.</strong></div>
				<div id="doc_res"></div>                               
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" id="dis-genDOC" data-bs-dismiss="modal">Close</button>
					<input type="submit" name="button" class="btn btn-primary" id="generateDOC" value="Generate">
				</div>
			</div>
		</div>
	</div>
</div>



<script src="/js/mgmIngredient.js"></script>
<script src="/js/ingredient.tabs.js"></script>

</div>
<!-- TOAST -->
<div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 11">
  	<div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
    	<div class="toast-header">
      		<strong class="me-auto" id="toast-title">...</strong>
      		<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
  		</div>
	</div>
</div>

</div>
</body>
</html>

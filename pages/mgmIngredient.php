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
if($ingID){
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$ingID'")))){
		if(mysqli_query($conn, "INSERT INTO ingredients (name) VALUES ('$ingID')")){
			$msg='<div class="alert alert-info alert-dismissible"><strong>Info:</strong> ingredient '.$ingID.' added</div>';
		}
	}
}

$res_ingTypes = mysqli_query($conn, "SELECT id,name FROM ingTypes ORDER BY name ASC");
$res_ingStrength = mysqli_query($conn, "SELECT id,name FROM ingStrength ORDER BY name ASC");
$res_ingCategory = mysqli_query($conn, "SELECT id,image,name,notes FROM ingCategory ORDER BY name ASC");
$res_ingProfiles = mysqli_query($conn, "SELECT id,name FROM ingProfiles ORDER BY id ASC");

$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredients WHERE name = '$ingID'"));

?>
<!doctype html>
<html lang="en">
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
	<script src="/js/bootstrap.min.js"></script>
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
var myIngID = '';
<?php if($ing['id']){ ?>

var myIngID = "<?=$ing['id']?>";
var myCAS = "<?=$ing['cas']?>";
var myPCH = "<?=$settings['pubChem']?>";
<?php } ?>


</script>
</head>

<body>
	<div class="mgm-ing-theme">
		<div class="container mgm-ing-bk">
			<div class="mgm-column mgm-visible-xl mgm-col-xl-5">
				<h1 class="mgmIngHeader mgmIngHeader-with-separator"><?php if($ingID){ echo $ing['name'];?>
				<div class="btn-group">
					<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
					<div class="dropdown-menu">
						<li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#printLabel"><i class="fa-solid fa-print mx-2"></i>Print Label</a></li>
						<li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#cloneIng"><i class="fa-solid fa-copy mx-2"></i>Clone ingredient</a></li>
						<li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#renameIng"><i class="fa-regular fa-pen-to-square mx-2"></i>Rename ingredient</a></li>
                        <li><a class="dropdown-item" href="/pages/export.php?format=json&kind=single-ingredient&id=<?=$ing['id']?>"><i class="fas fa-download mx-2"></i>Export as JSON</a></li>
						<li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#genSDS"><i class="fa-solid fa-file-prescription mx-2"></i>Generate SDS</a></li>
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
	<ul class="nav nav-tabs" role="tablist">
		<li class="active"><a href="#general" role="tab" data-toggle="tab"><icon class="fa fa-table"></icon> General</a></li>
		<?php if($ingID){?>
			<li><a href="#usage_limits" id="usage_tab" role="tab" data-toggle="tab"><i class="fa fa-bong"></i> Usage &amp; Limits</a></li>
			<li><a href="#supply" id="sups_tab" role="tab" data-toggle="tab"><i class="fa fa-shopping-cart"></i> Supply</a></li>
			<li><a href="#tech_data" id="techs_tab" role="tab" data-toggle="tab"><i class="fa fa-cog"></i> Technical Data</a></li>
			<li><a href="#documents" id="docs_tab" role="tab" data-toggle="tab"><i class="fa fa-file-alt"></i> Documents</a></li>
			<li><a href="#synonyms" id="synonyms_tab" role="tab" data-toggle="tab"><i class="fa fa-bookmark"></i> Synonyms</a></li>
			<li><a href="#note_impact" id="impact_tab" role="tab" data-toggle="tab"><i class="fa fa-magic"></i> Note Impact</a></li>
			<li><a href="#tech_composition" id="cmps_tab" ole="tab" data-toggle="tab"><i class="fa fa-th-list"></i> Composition</a></li>
			<li><a href="#safety_info" id="safety_tab" role="tab" data-toggle="tab"><i class="fa fa-biohazard"></i> Safety</a></li>
			<?php if($settings['pubChem'] == '1' && $ing['cas']){?>
				<li><a href="#pubChem" id="pubChem_tab" role="tab" data-toggle="tab"><i class="fa fa-atom"></i> Pub Chem</a></li>
			<?php } ?>  
			<li><a href="#privacy" id="privacy_tab" role="tab" data-toggle="tab"><i class="fa fa-user-secret"></i> Privacy</a></li>   
			<li><a href="#whereUsed" id="whereUsed_tab" role="tab" data-toggle="tab"><i class="fa fa-random"></i> Where used?</a></li>
            <li><a href="#ingRep" id="reps_tab" role="tab" data-toggle="tab"><i class="fa fa-exchange-alt"></i> Replacements</a></li>
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

<!-- Modal Print-->
<div class="modal fade" id="printLabel" tabindex="-1" role="dialog" aria-labelledby="printLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Print Label for <?php echo $ing['name']; ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="msg"></div>
				CAS#:
				<input class="form-control" name="cas" type="text" value="<?php echo $ing['cas']; ?>" />
				<p>
					Dilution %: 
					<input class="form-control" name="dilution" type="text" id="dilution" value="<?php echo $ing['purity']; ?>" />
					<p>
						Dilutant:
						<select class="form-control" name="dilutant" id="dilutant">
							<option selected="selected" value="">None</option>
							<?php
							$res_ing = mysqli_query($conn, "SELECT id, name FROM ingredients WHERE type = 'Solvent' OR type = 'Carrier' ORDER BY name ASC");
							while ($r_ing = mysqli_fetch_array($res_ing)){
								echo '<option value="'.$r_ing['name'].'">'.$r_ing['name'].'</option>';
							}
							?>
						</select>
					</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<input type="submit" name="button" class="btn btn-primary" id="print" value="Print">
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Modal Clone-->
<div class="modal fade" id="cloneIng" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="cloneIng" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Clone ingredient <?php echo $ing['name']; ?></h5>
			</div>
			<div class="modal-body">
				<div id="clone_msg"></div>
				Name
				<input class="form-control" name="cloneIngName" id="cloneIngName" type="text" value="" />            
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" name="button" class="btn btn-primary" id="cloneME" value="Clone">
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Rename-->
<div class="modal fade" id="renameIng" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="renameIng" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Rename ingredient <?php echo $ing['name']; ?></h5>
			</div>
			<div class="modal-body">
            	<div id="warn"><div class="alert alert-warning"><strong>Warning:</strong> If you rename the ingredient, will affect any formulas that using it as well. Please refer to <strong>Where Used</strong> section to get a list of formulas if any.</div></div>
				<div id="rename_msg"></div>
				Name
				<input class="form-control" name="renameIngName" id="renameIngName" type="text" value="" />            
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" name="button" class="btn btn-primary" id="renameME" value="Rename">
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Gen SDS-->
<div class="modal fade" id="genSDS" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="genSDS" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Generate SDS for <?php echo $ing['name']; ?></h5>
			</div>
			<div class="modal-body">
                <div id="warn">
                <div class="alert alert-warning">Please note: This feature its under development and in preview state at the moment.</div>
                	<div class="alert alert-info">A template is required in order to generate an SDS document.
                To create a new template, go to <a href="/?do=settings" target="_blank">settings</a> and create a new one under HTML Templates.
                For the available parameters please refer to the documentation <a href="https://www.perfumersvault.com/knowledge-base/html-templates/" target="_blank">here</a>.</div>
                </div>
				<div id="sds_res"></div>
				Select SDS template:
                <select class="form-control" name="template" id="template">
                <?php
                    $res = mysqli_query($conn, "SELECT id, name FROM templates ORDER BY name ASC");
                    while ($q = mysqli_fetch_array($res)){
                    echo '<option value="'.$q['id'].'">'.$q['name'].'</option>';
                }
                ?>
                </select>
                
               	Select Supplier:
                <select class="form-control" name="ingSupplier" id="ingSupplier">
                <?php
                    $res = mysqli_query($conn, "SELECT id, name FROM ingSuppliers ORDER BY name ASC");
                    while ($q = mysqli_fetch_array($res)){
                    echo '<option value="'.$q['id'].'">'.$q['name'].'</option>';
                }
                ?>
                </select>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" id="dis-genSDS" data-dismiss="modal">Close</button>
					<input type="submit" name="button" class="btn btn-primary" id="generateSDS" value="Generate">
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" language="javascript">
$('#printLabel').on('click', '[id*=print]', function () {
	<?php if(empty($settings['label_printer_addr']) || empty($settings['label_printer_model'])){?>
		$("#msg").html('<div class="alert alert-danger alert-dismissible">Please configure printer details in <a href="?do=settings">settings<a> page</div>');
	<?php }else{ ?>
		$("#msg").html('<div class="alert alert-info alert-dismissible">Printing...</div>');

		$.ajax({ 
			url: 'manageFormula.php', 
			type: 'GET',
			data: {
				action: "printLabel",
				type: "ingredient",
				dilution: $("#dilution").val(),
				cas: $("#cas").val(),
				dilutant: btoa($("#dilutant").val()),
				name: btoa(myIngName)
			},
			dataType: 'html',
			success: function (data) {
				$('#msg').html(data);
			}
		});
	<?php } ?>
});


$(document).ready(function() {
	$('[rel=tipsy]').tooltip({placement: 'auto'});

	$('#general').on('click', '[id*=saveGeneral]', function () {
		<?php if(empty($ing['id'])){ ?>
			if($.trim($("#name").val()) == ''){
				$('#ingMsg').html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Name is required</div>');
				return;
   			}
		<?php } ?>
		$.ajax({ 
			url: 'update_data.php', 
			type: 'POST',
			data: {
				manage: 'ingredient',
				tab: 'general',
				ingID: myIngID,
				
				name: $("#name").val(),
				INCI: $("#INCI").val(),
				cas: $("#cas").val(),
				einecs: $("#einecs").val(),
				reach: $("#reach").val(),
				fema: $("#fema").val(),
				isAllergen: $("#isAllergen").is(':checked'),
				purity: $("#purity").val(),
				solvent: $("#solvent").val(),
				profile: $("#profile").val(),					
				type: $("#type").val(),
				strength: $("#strength").val(),
				category: $("#category").val(),
				physical_state: $("#physical_state").val(),
				odor: $("#odor").val(),
				notes: $("#notes").val(),
				<?php if($ing['name']){?>
					ing: '<?=$ing['name'];?>'
				<?php } ?>
			},
			dataType: 'json',   			
			success: function (data) {
				if(data.success){
					$('#mgmIngHeaderCAS').html($("#cas").val());
					$('#IUPAC').html($("#INCI").val());
					
					var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				}else{
					var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				}
				
				$('#ingMsg').html(msg);
				
				if ($('#name').val()) {
					window.location = 'mgmIngredient.php?id=' + btoa($('#name').val());
				}
			    <?php if($ing['id']){ ?>
				reload_overview();
				<?php } ?>
			}
		});
	});



	$('#purity').bind('input', function() {
		var purity = $(this).val();
		if(purity == 100){
			$("#solvent").prop("disabled", true); 
			$("#solvent").val(''); 
		}else{
			$("#solvent").prop("disabled", false);
		}
		$('.selectpicker').selectpicker('refresh');
	});
	
});//end doc

</script>
<script src="/js/mgmIngredient.js"></script>
<script src="/js/ingredient.tabs.js"></script>

</div>
</div>
</body>
</html>
<?php 
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');
require_once('../func/formatBytes.php');
require_once('../func/validateInput.php');

require_once('../func/searchIFRA.php');

$ingID = mysqli_real_escape_string($conn, $_GET["id"]);
if($ingID){
	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM ingredients WHERE name = '$ingID'")))){
		if(mysqli_query($conn, "INSERT INTO ingredients (name) VALUES ('$ingID')")){
			$msg='<div class="alert alert-info alert-dismissible"><strong>Info:</strong> ingredient '.$ingID.' added</div>';
		}
	}
}

$defCatClass = $settings['defCatClass'];

if($_POST){
	$ing = mysqli_fetch_array( mysqli_query($conn, "SELECT * FROM ingredients WHERE name = '$ingID'"));

	$cas = trim(mysqli_real_escape_string($conn, $_POST["cas"]));
	$fema = trim(mysqli_real_escape_string($conn, $_POST["fema"]));

	$type = mysqli_real_escape_string($conn, $_POST["type"]);
	$strength = mysqli_real_escape_string($conn, $_POST["strength"]);
	$category = mysqli_real_escape_string($conn, $_POST["category"]);
	$supplier = mysqli_real_escape_string($conn, $_POST["supplier"]);
	$supplier_link = mysqli_real_escape_string($conn, $_POST["supplier_link"]);
	
	$profile = mysqli_real_escape_string($conn, $_POST["profile"]);
	$price = validateInput($_POST["price"]);
	$tenacity = mysqli_real_escape_string($conn, $_POST["tenacity"]);
	$formula = mysqli_real_escape_string($conn, $_POST["formula"]);
	$chemical_name = mysqli_real_escape_string($conn, $_POST["chemical_name"]);
	$flash_point = mysqli_real_escape_string($conn, $_POST["flash_point"]);
	$appearance = mysqli_real_escape_string($conn, $_POST["appearance"]);
	$ml = validateInput($_POST["ml"]);
	$solvent = mysqli_real_escape_string($conn, $_POST["solvent"]);
	$odor = ucfirst(trim(mysqli_real_escape_string($conn, $_POST["odor"])));
	$notes = ucfirst(trim(mysqli_real_escape_string($conn, $_POST["notes"])));
	$purity = validateInput($_POST["purity"]);
	$soluble = mysqli_real_escape_string($conn, $_POST["soluble"]);
	$logp = mysqli_real_escape_string($conn, $_POST["logp"]);

	$cat1 = validateInput($_POST["cat1"]);
	$cat2 = validateInput($_POST["cat2"]);
	$cat3 = validateInput($_POST["cat3"]);
	$cat4 = validateInput($_POST["cat4"]);
	$cat5A = validateInput($_POST["cat5A"]);
	$cat5B = validateInput($_POST["cat5B"]);
	$cat5C = validateInput($_POST["cat5C"]);
	$cat5D = validateInput($_POST["cat5D"]);
	$cat6 = validateInput($_POST["cat6"]);
	$cat7A = validateInput($_POST["cat7A"]);
	$cat7B = validateInput($_POST["cat7B"]);
	$cat8 = validateInput($_POST["cat8"]);
	$cat9 = validateInput($_POST["cat9"]);
	$cat10A = validateInput($_POST["cat10A"]);
	$cat10B = validateInput($_POST["cat10B"]);
	$cat11A = validateInput($_POST["cat11A"]);
	$cat11B = validateInput($_POST["cat11B"]);
	$cat12 = validateInput($_POST["cat12"]);
	
	$manufacturer = mysqli_real_escape_string($conn, $_POST["manufacturer"]);
	$impact_top = mysqli_real_escape_string($conn, $_POST["impact_top"]);
	$impact_base = mysqli_real_escape_string($conn, $_POST["impact_base"]);
	$impact_heart = mysqli_real_escape_string($conn, $_POST["impact_heart"]);
	$usage_type = mysqli_real_escape_string($conn, $_POST["usage_type"]);

	if($_POST["isAllergen"]) {
		$allergen = '1';
	}else{
		$allergen = '0';
	}
	if($_POST["flavor_use"]) {
		$flavor_use = '1';
	}else{
		$flavor_use = '0';
	}
	if(empty($ml)){
		$ml = '10';
	}
	
	if(($_FILES['SDS']['name'])){
      $file_name = $_FILES['SDS']['name'];
      $file_size =$_FILES['SDS']['size'];
      $file_tmp =$_FILES['SDS']['tmp_name'];
      $file_type=$_FILES['SDS']['type'];
      $file_ext=strtolower(end(explode('.',$_FILES['SDS']['name'])));
	  
	  if(empty($err)==true){
		 if (file_exists('../'.$uploads_path.'SDS/') === FALSE) {
    		 mkdir('../'.$uploads_path.'SDS/', 0740, true);
	  	 }
	  }
	  
	  $ext = explode(', ', $allowed_ext);
      if(in_array($file_ext,$ext)=== false){
		 $msg.='<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file.</div>';
      }elseif($file_size > $max_filesize){
		 $msg.='<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>File size must not exceed '.formatBytes($max_filesize).'</div>';
      }else{
	  
         move_uploaded_file($file_tmp,'../'.$uploads_path.'SDS/'.base64_encode($file_name));
		 $SDSF = $uploads_path.'SDS/'.base64_encode($file_name);
		 mysqli_query($conn, "UPDATE ingredients SET SDS = '$SDSF' WHERE name='$ingID'");
	  }
   }

	if(empty($_POST['name'])){
		$query = "UPDATE ingredients SET cas = '$cas', FEMA = '$fema', type = '$type', strength = '$strength', category='$category', supplier='$supplier', supplier_link='$supplier_link', profile='$profile', price='$price', tenacity='$tenacity', chemical_name='$chemical_name', flash_point='$flash_point', appearance='$appearance', notes='$notes', ml='$ml', odor='$odor', purity='$purity', allergen='$allergen', formula='$formula', flavor_use='$flavor_use', cat1 = '$cat1', cat2 = '$cat2', cat3 = '$cat3', cat4 = '$cat4', cat5A = '$cat5A', cat5B = '$cat5B', cat5C = '$cat5C', cat5D = '$cat5D', cat6 = '$cat6', cat7A = '$cat7A', cat7B = '$cat7B', cat8 = '$cat8', cat9 = '$cat9', cat10A = '$cat10A', cat10B = '$cat10B', cat11A = '$cat11A', cat11B = '$cat11B', cat12 = '$cat12', soluble = '$soluble', logp = '$logp', manufacturer = '$manufacturer', impact_top = '$impact_top', impact_heart = '$impact_heart', impact_base = '$impact_base', usage_type = '$usage_type', solvent = '$solvent' WHERE name='$ingID'";
		if(mysqli_query($conn, $query)){
			$msg = '<div class="alert alert-success alert-dismissible">Ingredient <strong>'.$ing['name'].'</strong> updated!</div>';
		}else{
			$msg = '<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> Failed to update!</div>';
		}
	}else{
		$name = mysqli_real_escape_string($conn, $_POST["name"]);
		$ingID = $name;
		$query = "INSERT INTO ingredients (name, cas, FEMA, type, strength, SDS, ".$settings['defCatClass'].", category, supplier, supplier_link, profile, price, tenacity, chemical_name, flash_point, appearance, notes, ml, odor, purity, allergen) VALUES ('$name', '$cas', '$fema', '$type', '$strength', '$SDSF', '$cat', '$category', '$supplier', '$supplier_link', '$profile', '$price', '$tenacity', '$chemical_name', '$flash_point', '$appearance', '$notes', '$ml', '$odor', '$purity', '$allergen')";
		if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$name'"))){
			$msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$name.' already exists!</div>';
		}else{
			if(mysqli_query($conn, $query)){
				$msg = '<div class="alert alert-success alert-dismissible">Ingredient <strong>'.$name.'</strong> added!</div>';
			}else{
				$msg = '<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> Failed to add!</div>';
			}
		}
	}
}

$res_ingTypes = mysqli_query($conn, "SELECT id,name FROM ingTypes ORDER BY name ASC");
$res_ingStrength = mysqli_query($conn, "SELECT id,name FROM ingStrength ORDER BY name ASC");
$res_ingCategory = mysqli_query($conn, "SELECT id,name FROM ingCategory ORDER BY name ASC");
$res_ingSupplier = mysqli_query($conn, "SELECT id,name FROM ingSuppliers ORDER BY name ASC");
$res_ingProfiles = mysqli_query($conn, "SELECT id,name FROM ingProfiles ORDER BY name ASC");

$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredients WHERE name = '$ingID'"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit ingredient</title>
<link href="../css/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
<script src="../js/jquery/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/bootstrap-select.js"></script>
<script src="../js/tipsy.js"></script>
<script src="../js/bootstrap-editable.js"></script>

<link rel="stylesheet" type="text/css" href="../css/datatables.min.css"/>
<script type="text/javascript" src="../js/datatables.min.js"></script>
  
<script type='text/javascript'>
$(document).ready(function() {
	
    $('#tdData').DataTable({
	    "paging":   true,
		"info":   true,
		"lengthMenu": [[5, 35, 60, -1], [20, 35, 60, "All"]]
	});
}); 
</script>
<style>
.form-inline .form-control {
    display: inline-block;
    width: 500px;
    vertical-align: middle;
}

</style>
<link href="../css/sb-admin-2.css" rel="stylesheet">
<link href="../css/bootstrap-select.min.css" rel="stylesheet">
<link href="../css/bootstrap.min.css" rel="stylesheet">
<link href="../css/vault.css" rel="stylesheet">
<link href="../css/tipsy.css" rel="stylesheet">
<link href="../css/bootstrap-editable.css" rel="stylesheet">


<style>
.container {
    max-width: 100%;
	width: 950px;
}
#tdData td,
  table.table th {
  white-space: nowrap;
}
</style>

<script>
/*
$(document).ready(function() {
	$('a[rel=tipsy]').tipsy();
});  
*/
function search() {	  
$("#odor").val('Loading...');
$.ajax({ 
    url: 'searchTGSC.php', 
	type: 'get',
    data: {
		name: "<?php if($ing['cas']){ echo $ing['cas']; }else{ echo $ing['name'];}?>"
		},
	dataType: 'html',
    success: function (data) {
	  $('#TGSC').html(data);
    }
  });
};

<?php if($ing['cas'] && $settings['pubChem'] == '1'){ ?>

$.ajax({ 
    url: 'pubChem.php', 
	type: 'get',
    data: {
		cas: "<?php echo $ing['cas']; ?>"
		},
	dataType: 'html',
    success: function (data) {
	  $('#pubChemData').html(data);
    }
  });

<?php } ?>

function printLabel() {
	<?php if(empty($settings['label_printer_addr']) || empty($settings['label_printer_model'])){?>
	$("#msg").html('<div class="alert alert-danger alert-dismissible">Please configure printer details in <a href="?do=settings">settings<a> page</div>');
	<?php }else{ ?>
	$("#msg").html('<div class="alert alert-info alert-dismissible">Printing...</div>');

$.ajax({ 
    url: 'manageFormula.php', 
	type: 'get',
    data: {
		action: "printLabel",
		type: "ingredient",
		dilution: $("#dilution").val(),
		dilutant: $("#dilutant").val(),
		name: "<?php echo $ing['name']; ?>"
		},
	dataType: 'html',
    success: function (data) {
	  $('#msg').html(data);
    }
  });
	<?php } ?>
};


function reload_data() {
$.ajax({ 
    url: 'allergens.php', 
	type: 'get',
    data: {
		id: "<?php echo $ingID ?>"
		},
	dataType: 'html',
    success: function (data) {
	  $('#fetch_allergen').html(data);
    }
  });
}
reload_data();
</script>
</head>

<body>
<div class="container">
		<div class="list-group-item-info">
        <h1 class="badge-primary"><?php if($ingID){ echo $ing['name'];?>
            <div class="btn-group">
              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#printLabel">Print Label</a>
              </div>
            </div>
            <?php }else {?>
            Add ingredient
            <?php } ?>
        </h1>
</div>
<!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li class="active"><a href="#general" role="tab" data-toggle="tab"><icon class="fa fa-table"></icon> General</a></li>
      <li><a href="#usage_limits" role="tab" data-toggle="tab"><i class="fa fa-bong"></i> Usage &amp; Limits</a></li>
      <li><a href="#supply" role="tab" data-toggle="tab"><i class="fa fa-shopping-cart"></i> Supply</a></li>
      <li><a href="#tech_data" role="tab" data-toggle="tab"><i class="fa fa-cog"></i> Technical Data</a></li>
      <li><a href="#note_impact" role="tab" data-toggle="tab"><i class="fa fa-magic"></i> Note Impact</a></li>
      <?php if($ingID){?>
      <li><a href="#tech_allergens" role="tab" data-toggle="tab"><i class="fa fa-allergies"></i> Allergens</a></li>
      <?php } ?>  
      <?php if($settings['pubChem'] == '1' && $ing['cas']){?>
      	<li><a href="#pubChem" role="tab" data-toggle="tab"><i class="fa fa-atom"></i> Pub Chem</a></li>
      <?php } ?>
    </ul>
			<form action="<?php if($ingID){ echo '?id='.$ingID;}?>" method="post" enctype="multipart/form-data" name="edit_ing" target="_self" id="edit_ing">
           	  <div class="tab-content">
     				<div class="tab-pane fade active in" id="general">
                              <h3>General</h3>
							   <hr>
                	         <table width="100%" border="0">
                              <tr>
                                <td colspan="6"><?php echo $msg; ?></td>
                              </tr>
                              <?php if(empty($ingID)){?>
                              <tr>
                                <td>Name:</td>
                                <td colspan="5"><input name="name" type="text" class="form-control" id="name" /></td>
                              </tr>
                              <?php } ?>
                              <tr>
                                <td width="20%">CAS #:</td>
                                <td colspan="5"><input name="cas" type="text" class="form-control" id="cas" value="<?php echo $ing['cas']; ?>"></td>
                              </tr>

                              <tr>
                                <td height="31">FEMA #:</td>
                                <td colspan="5"><input name="fema" type="text" class="form-control" id="fema" value="<?php echo $ing['FEMA']; ?>" /></td>
                              </tr>
                              <tr>
                                <td height="31"><a href="#" rel="tipsy" title="If enabled, ingredient name will be printed in the box label.">Is Allergen:</a></td>
                                <td colspan="5"><input name="isAllergen" type="checkbox" id="isAllergen" value="1" <?php if($ing['allergen'] == '1'){; ?> checked="checked"  <?php } ?>/></td>
                              </tr>
                              <tr>
                                <td height="29">Purity %:</td>
                                <td width="50%"><input name="purity" type="text" class="form-control" id="purity" value="<?php echo $ing['purity']; ?>" /></td>
                                <td width="1%">&nbsp;</td>
                                <td colspan="3"><select name="solvent" id="solvent" class="form-control selectpicker" data-live-search="true" <?php if($ing['purity'] == 100){ ?>disabled<?php }?> >
                                  <option value="" selected disabled>Solvent</option>
                                  <option value="None">None</option>
								  <?php
								   $res_dil = mysqli_query($conn, "SELECT id, name FROM ingredients WHERE type = 'Solvent' OR type = 'Carrier' ORDER BY name ASC");
								   while ($r_dil = mysqli_fetch_array($res_dil)){
									    $selected=($ing['solvent'] == $r_dil['name'])? "selected" : "";
										echo '<option '.$selected.' value="'.$r_dil['name'].'">'.$r_dil['name'].'</option>';
    							   }
								  ?>
                                </select>
                                </td>
                               </tr>
                              <tr>
                                <td height="29">Profile:</td>
                                <td colspan="5">
                                <select name="profile" id="profile" class="form-control">
                                <option value="" selected></option>
                                <?php 	while ($row_ingProfiles = mysqli_fetch_array($res_ingProfiles)){ ?>
								<option value="<?php echo $row_ingProfiles['name'];?>" <?php echo ($ing['profile']==$row_ingProfiles['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingProfiles['name'];?></option>
								<?php } ?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td height="29">Type:</td>
                                <td colspan="5">
                                <select name="type" id="type" class="form-control">
                                <option value="" selected></option>
                                <?php 	while ($row_ingTypes = mysqli_fetch_array($res_ingTypes)){ ?>
								<option value="<?php echo $row_ingTypes['name'];?>" <?php echo ($ing['type']==$row_ingTypes['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingTypes['name'];?></option>
								<?php } ?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td height="28">Strength:</td>
                                <td colspan="5">
                                <select name="strength" id="strength" class="form-control">
                                <option value="" selected></option>
                                <?php while ($row_ingStrength = mysqli_fetch_array($res_ingStrength)){ ?>
								<option value="<?php echo $row_ingStrength['name'];?>" <?php echo ($ing['strength']==$row_ingStrength['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingStrength['name'];?></option>
								<?php } ?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td height="31">Category:</td>
                                <td colspan="5">
                                <select name="category" id="category" class="form-control" data-live-search="true">
                                <option value="" selected></option>
                                  <?php while ($row_ingCategory = mysqli_fetch_array($res_ingCategory)){ ?>
								<option value="<?php echo $row_ingCategory['name'];?>" <?php echo ($ing['category']==$row_ingCategory['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingCategory['name'];?></option>
								  <?php } ?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td height="31" valign="top">Odor:</td>
                                <td colspan="3"><div id='TGSC'><input name="odor" id="odor" type="text" class="form-control" value="<?php echo $ing['odor']; ?>"/></div>
                                </td>
                                <?php if(file_exists('searchTGSC.php')){?>
                                <td width="2%">&nbsp;</td>
                                <td width="12%"><a href="javascript:search()" id="search">Search TGSC</a></td>
                                <?php } ?>
                              </tr>
                              <tr>
                                <td valign="top">Notes:</td>
                                <td colspan="5"><textarea name="notes" id="notes" cols="45" rows="5" class="form-control"><?php echo $ing['notes']; ?></textarea></td>
                              </tr>

                            </table>
                </div>
                <!--general tab-->
                    
                            <div class="tab-pane fade" id="usage_limits">
       						   <h3>Usage &amp; Limits</h3>
                                 <hr>
                               <table width="100%" border="0">
                                <tr>
                                  <td height="32">Flavor use:</td>
                                  <td width="78%" colspan="3"><input name="flavor_use" type="checkbox" id="flavor_use" value="1" <?php if($ing['flavor_use'] == '1'){; ?> checked="checked"  <?php } ?>/></td>
                                </tr>

                                <td width="22%"></td>

                                </tr>
                                <tr>
                                  <td colspan="4"><hr /></td>
                                 </tr>
                                <tr>
                                  <td>Usage classification:</td>
                                  <td colspan="3">
                                <?php if($rType = searchIFRA($ing['cas'],$ing['name'],'type',$conn, $defCatClass)){
										  echo $rType;
									  }else{
								?>

                    <select name="usage_type" id="usage_type" class="form-control">
                      <option value="none" selected="selected">None</option>
					  <option value="1" <?php if($ing['usage_type']=="1") echo 'selected="selected"'; ?> >Recommendation</option>
					  <option value="2" <?php if($ing['usage_type']=="2") echo 'selected="selected"'; ?> >Restriction</option>
					  <option value="2" <?php if($ing['usage_type']=="3") echo 'selected="selected"'; ?> >Specification</option>
					  <option value="2" <?php if($ing['usage_type']=="4") echo 'selected="selected"'; ?> >Prohibition</option>
                    </select>
                    			<?php } ?>
                    </td>
                                </tr>
                                <tr>
                                  <td>Cat1 %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat1')){
										echo $limit;
									}else{
								?>
                                    <input name="cat1" type="text" class="form-control" id="cat1" value="<?php echo $ing['cat1']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat2  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat2')){
										echo $limit;
									}else{
								?>
                                    <input name="cat2" type="text" class="form-control" id="cat2" value="<?php echo $ing['cat2']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat3  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat3')){
										echo $limit;
									}else{
								?>
                                    <input name="cat3" type="text" class="form-control" id="cat3" value="<?php echo $ing['cat3']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat4  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat4')){
										echo $limit;
									}else{
								?>
                                    <input name="cat4" type="text" class="form-control" id="cat4" value="<?php echo $ing['cat4']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat5A  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat5A')){
										echo $limit;
									}else{
								?>
                                    <input name="cat5A" type="text" class="form-control" id="cat5A" value="<?php echo $ing['cat5A']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat5B  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat5B')){
										echo $limit;
									}else{
								?>
                                    <input name="cat5B" type="text" class="form-control" id="cat5B" value="<?php echo $ing['cat5B']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat5C  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat5C')){
										echo $limit;
									}else{
								?>
                                    <input name="cat5C" type="text" class="form-control" id="cat5C" value="<?php echo $ing['cat5C']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat5D  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat5D')){
										echo $limit;
									}else{
								?>
                                    <input name="cat5D" type="text" class="form-control" id="cat5D" value="<?php echo $ing['cat5D']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat6  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat6')){
										echo $limit;
									}else{
								?>
                                    <input name="cat6" type="text" class="form-control" id="cat6" value="<?php echo $ing['cat6']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat7A  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat7A')){
										echo $limit;
									}else{
								?>
                                    <input name="cat7A" type="text" class="form-control" id="cat7A" value="<?php echo $ing['cat7A']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat7B  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat7B')){
										echo $limit;
									}else{
								?>
                                    <input name="cat7B" type="text" class="form-control" id="cat7B" value="<?php echo $ing['cat7B']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat8  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat8')){
										echo $limit;
									}else{
								?>
                                    <input name="cat8" type="text" class="form-control" id="cat8" value="<?php echo $ing['cat8']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat9  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat9')){
										echo $limit;
									}else{
								?>
                                    <input name="cat9" type="text" class="form-control" id="cat9" value="<?php echo $ing['cat9']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat10A  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat10A')){
										echo $limit;
									}else{
								?>
                                    <input name="cat10A" type="text" class="form-control" id="cat10A" value="<?php echo $ing['cat10A']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat10B  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat10B')){
										echo $limit;
									}else{
								?>
                                    <input name="cat10B" type="text" class="form-control" id="cat10B" value="<?php echo $ing['cat10B']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat11A  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat11A')){
										echo $limit;
									}else{
								?>
                                    <input name="cat11A" type="text" class="form-control" id="cat11A" value="<?php echo $ing['cat11A']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat11B  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat11B')){
										echo $limit;
									}else{
								?>
                                    <input name="cat11B" type="text" class="form-control" id="cat11B" value="<?php echo $ing['cat11B']; ?>" />
                                  <?php } ?></td>
                                </tr>
                                <tr>
                                  <td>Cat12  %:</td>
                                  <td colspan="3"><?php
								 	if($limit = searchIFRA($ing['cas'],$ing['name'],null,$conn, 'cat12')){
										echo $limit;
									}else{
								?>
                                    <input name="cat12" type="text" class="form-control" id="cat12" value="<?php echo $ing['cat12']; ?>" />
                                  <?php } ?></td>
                                </tr>
						      </table>

   						  </div>
                            
                  <div class="tab-pane fade" id="supply">
				    <h3>Supply</h3>
                    <hr>
                    <table width="100%" border="0">
                              <tr>
                                <td width="20%">Supplier:</td>
                                <td width="80%" colspan="3">
                                <select name="supplier" id="supplier" class="form-control">
                                <option value="" selected></option>
                                  <?php while ($row_ingSupplier = mysqli_fetch_array($res_ingSupplier)){ ?>
								<option value="<?php echo $row_ingSupplier['name'];?>" <?php echo ($ing['supplier']==$row_ingSupplier['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingSupplier['name'];?></option>
								  <?php	}	?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td>Supplier URL:</td>
                                <td colspan="3"><input name="supplier_link" type="text" class="form-control" id="supplier_link" value="<?php echo $ing['supplier_link']; ?>"></td>
                              </tr>
                              <tr>
								<td>Price (<?php echo $settings['currency']; ?>):</td>
                                <td colspan="3"><input name="price" type="text" class="form-control" id="price" value="<?php echo $ing['price']; ?>"/></td>
                              </tr>
                              <tr>
                                <td>Size (ml):</td>
                                <td colspan="3"><input name="ml" type="text" class="form-control" id="ml" value="<?php echo $ing['ml']; ?>"/></td>
                              </tr>
                              <tr>
                                <td>Manufacturer</td>
                                <td colspan="3"><input name="manufacturer" type="text" class="form-control" id="manufacturer" value="<?php echo $ing['manufacturer']; ?>"/></td>
                              </tr>
                    </table>
                            </div>
                            
                            <div class="tab-pane fade" id="tech_data">
          						 <h3>Techical Data</h3>
                                 <hr>
                             <table width="100%" border="0">
                              <tr>
                                <td width="20%">Tenacity:</td>
                                <td width="80%" colspan="3"><input name="tenacity" type="text" class="form-control" id="tenacity" value="<?php echo $ing['tenacity']; ?>"/></td>
                              </tr>
                              <tr>
                                <td>Flash Point:</td>
                                <td colspan="3"><input name="flash_point" type="text" class="form-control" id="flash_point" value="<?php echo $ing['flash_point']; ?>"/></td>
                              </tr>
                              <tr>
                                <td>Chemical Name:</td>
                                <td colspan="3"><input name="chemical_name" type="text" class="form-control" id="chemical_name" value="<?php echo $ing['chemical_name']; ?>"/></td>
                              </tr>
                              <tr>
                                <td>Formula:</td>
                                <td colspan="3">
								<?php
								 	if($chFormula = searchIFRA($ing['cas'],$ing['name'],'formula',$conn,$defCatClass)){
										echo $chFormula;
									}else{
								?>
                                <input name="formula" type="text" class="form-control" id="formula" value="<?php echo $ing['formula']; ?>">
                                <?php } ?>
                                </td>
                              </tr>
                              <tr>
                                <td>Log/P:</td>
                                <td colspan="3"><input name="logp" type="text" class="form-control" id="logp" value="<?php echo $ing['logp']; ?>"/></td>
                              </tr>
                              <tr>
                                <td>Soluble in:</td>
                                <td colspan="3"><input name="soluble" type="text" class="form-control" id="soluble" value="<?php echo $ing['soluble']; ?>"/></td>
                              </tr>
                              <tr>
                                <td>Appearance:</td>
                                <td colspan="3"><input name="appearance" type="text" class="form-control" id="appearance" value="<?php echo $ing['appearance']; ?>"/></td>
                              </tr>
                              <tr>
                                <td>SDS:</td>
                                <td colspan="3"><input type="file" class="form-control" name="SDS" id="SDS"></td>
                              </tr>
                            </table>
    
      						</div>
              
              <div class="tab-pane fade" id="note_impact">
              <h3>Note Impact</h3>
              <hr>
              <table width="100%" border="0">
                  <tr>
                    <td width="9%" height="40">Top:</td>
                    <td width="19%"><select name="impact_top" id="impact_top" class="form-control">
                      <option value="none" selected="selected">None</option>
					  <option value="100" <?php if($ing['impact_top']=="100") echo 'selected="selected"'; ?> >High</option>
					  <option value="50" <?php if($ing['impact_top']=="50") echo 'selected="selected"'; ?> >Medium</option>						
					  <option value="10" <?php if($ing['impact_top']=="10") echo 'selected="selected"'; ?> >Low</option>						
                    </select></td>
                    <td width="72%">&nbsp;</td>
                  </tr>
                  <tr>
                    <td height="40">Heart:</td>
                    <td><select name="impact_heart" id="impact_heart" class="form-control">
                      <option value="none" selected="selected">None</option>
                      <option value="100" <?php if($ing['impact_heart']=="100") echo 'selected="selected"'; ?> >High</option>
                      <option value="50" <?php if($ing['impact_heart']=="50") echo 'selected="selected"'; ?> >Medium</option>
                      <option value="10" <?php if($ing['impact_heart']=="10") echo 'selected="selected"'; ?> >Low</option>
                    </select></td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td height="40">Base:</td>
                    <td><select name="impact_base" id="impact_base" class="form-control">
                      <option value="none" selected="selected">None</option>
                      <option value="100" <?php if($ing['impact_base']=="100") echo 'selected="selected"'; ?> >High</option>
                      <option value="50" <?php if($ing['impact_base']=="50") echo 'selected="selected"'; ?> >Medium</option>
                      <option value="10" <?php if($ing['impact_base']=="10") echo 'selected="selected"'; ?> >Low</option>
                    </select></td>
                    <td>&nbsp;</td>
                  </tr>
                </table>
			  </div>
              <?php if($ingID){?>
              <div class="tab-pane fade" id="tech_allergens">
                   <div id="fetch_allergen"><div class="loader"></div></div>
              </div>
              <?php } ?>
              <?php if($settings['pubChem'] == '1' && $ing['cas']){?>
              <div class="tab-pane fade" id="pubChem">
				   <h3>Pub Chem Data</h3>
                   <hr>
                   <div id="pubChemData"> <div class="loader"></div> </div>
              </div>
              <?php } ?>
                   <!-- </div> <!--tabs-->
                    <hr>
                    <p><input type="submit" name="save" id="submit" class="btn btn-info" value="Save" /></p>
			</form>
</div>
</body>
</html>
<!-- Modal Print-->
<div class="modal fade" id="printLabel" tabindex="-1" role="dialog" aria-labelledby="printLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="printLabel">Print Label for <?php echo $ing['name']; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="msg"></div>
          <form action="javascript:printLabel()" method="get" name="form1" target="_self" id="form1">
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
        <input type="submit" name="button" class="btn btn-primary" id="button" value="Print">
      </div>
     </form>
    </div>
  </div>
</div>
</div>

<!-- ADD ALLERGEN-->
<div class="modal fade" id="addAllergen" tabindex="-1" role="dialog" aria-labelledby="addAllergen" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addAllergen">Add allergen for <?php echo $ing['name']; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="inf"></div>
          <form action="javascript:addAllergen()" method="get" name="form1" target="_self" id="form1">
            Name: 
            <input class="form-control" name="allgName" type="text" id="allgName" />
            <p>
            CAS: 
            <input class="form-control" name="allgCAS" type="text" id="allgCAS" />
            <p>            
            Percentage %:
            <input class="form-control" name="allgPerc" type="text" id="allgPerc" />
            </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <input type="submit" name="button" class="btn btn-primary" id="button" value="Add">
      </div>
     </form>
    </div>
  </div>
</div>
</div>


<script type="text/javascript" language="javascript" >

function deleteAllergen(allgID) {	  
$.ajax({ 
    url: 'update_data.php', 
	type: 'GET',
    data: {
		allergen: 'delete',
		allgID: allgID,
		ing: '<?=$ing['name'];?>'
		},
	dataType: 'html',
    success: function (data) {
	  	$('#msg').html(data);
		reload_data();
    }
  });
};

function addAllergen() {	  
$.ajax({ 
    url: 'update_data.php', 
	type: 'GET',
    data: {
		allergen: 'add',
		allgName: $("#allgName").val(),
		allgPerc: $("#allgPerc").val(),
		allgCAS: $("#allgCAS").val(),				
		ing: '<?=$ing['name'];?>'
		},
	dataType: 'html',
    success: function (data) {
	  	$('#inf').html(data);
     	$("#allgName").val('');
     	$("#allgCAS").val('');
     	$("#allgPerc").val('');
		reload_data();
    }
  });
};
</script>

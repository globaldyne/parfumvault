<?php 
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');
require_once('../func/formatBytes.php');

require_once('../func/searchIFRA.php');


if($_POST['name']){
	
	$name = mysqli_real_escape_string($conn, $_POST["name"]);
	$cas = mysqli_real_escape_string($conn, $_POST["cas"]);
	$type = mysqli_real_escape_string($conn, $_POST["type"]);
	$strength = mysqli_real_escape_string($conn, $_POST["strength"]);
	$category = mysqli_real_escape_string($conn, $_POST["category"]);
	$IFRA = mysqli_real_escape_string($conn, $_POST["IFRA"]);
	$supplier = mysqli_real_escape_string($conn, $_POST["supplier"]);
	$supplier_link = mysqli_real_escape_string($conn, $_POST["supplier_link"]);
	
	$profile = mysqli_real_escape_string($conn, $_POST["profile"]);
	$price = mysqli_real_escape_string($conn, $_POST["price"]);
	$tenacity = mysqli_real_escape_string($conn, $_POST["tenacity"]);
	$chemical_name = mysqli_real_escape_string($conn, $_POST["chemical_name"]);
	$flash_point = mysqli_real_escape_string($conn, $_POST["flash_point"]);
	$appearance = mysqli_real_escape_string($conn, $_POST["appearance"]);
	$ml = mysqli_real_escape_string($conn, $_POST["ml"]);
	$notes = mysqli_real_escape_string($conn, $_POST["notes"]);
	$odor = mysqli_real_escape_string($conn, $_POST["odor"]);
	$purity = mysqli_real_escape_string($conn, $_POST["purity"]);

	if($_POST["isAllergen"]) {
		$allergen = '1';
	}else{
		$allergen = '0';
	}

	if(!empty($_FILES['SDS']['name'])){
      $file_name = $_FILES['SDS']['name'];
      $file_size = $_FILES['SDS']['size'];
      $file_tmp =  $_FILES['SDS']['tmp_name'];
      $file_type = $_FILES['SDS']['type'];
      $file_ext=strtolower(end(explode('.',$_FILES['SDS']['name'])));
	  
	  if(empty($err)==true){
		 if (file_exists('../'.$uploads_path.'SDS/') === FALSE) {
    		 mkdir('../'.$uploads_path.'SDS/', 0740, true);
	  	 }
	  }
		  
      $ext = explode(', ',$allowed_ext);
 	  if(in_array($file_ext,$ext)=== false){
		 $msg.='<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file.</div>';
      }elseif($file_size > $max_filesize){
		 $msg.='<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>File size must not exceed '.formatBytes($max_filesize).'</div>';
      }else{
		 move_uploaded_file($file_tmp,'../'.$uploads_path.'SDS/'.base64_encode($file_name));
		 $SDSF = $uploads_path.'SDS/'.base64_encode($file_name);
      }
   }

	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$name'"))){
		$msg='<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>Error: </strong>'.$name.' already exists!
		</div>';
	}else{
		
		if(mysqli_query($conn, "INSERT INTO ingredients (name, cas, type, strength, SDS, IFRA, category, supplier, supplier_link, profile, price, tenacity, chemical_name, flash_point, appearance, notes, ml, odor, purity, allergen) VALUES ('$name', '$cas', '$type', '$strength', '$SDSF', '$IFRA', '$category', '$supplier', '$supplier_link', '$profile', '$price', '$tenacity', '$chemical_name', '$flash_point', '$appearance', '$notes', '$ml', '$odor', '$purity', '$allergen')")){
			$msg.='<div class="alert alert-success alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  			Ingredient <strong>'.$name.'</strong> added!
			<div>'.$msgF.'</div>
			</div>';
		}else{
			$msg.='<div class="alert alert-danger alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  			<strong>Error:</strong> Failed to add '.$name.'!
			<div>'.$msgF.'</div>
			</div>';
			print mysqli_error($conn);
		}
	}
}

$res_ingTypes = mysqli_query($conn, "SELECT id,name FROM ingTypes");
$res_ingStrength = mysqli_query($conn, "SELECT id,name FROM ingStrength");
$res_ingCategory = mysqli_query($conn, "SELECT id,name FROM ingCategory");
$res_ingSupplier = mysqli_query($conn, "SELECT id,name FROM ingSuppliers");
$res_ingProfiles = mysqli_query($conn, "SELECT id,name FROM ingProfiles");

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
<link href="../css/sb-admin-2.css" rel="stylesheet">

<link href="../css/bootstrap-select.min.css" rel="stylesheet">
<link href="../css/bootstrap.min.css" rel="stylesheet">
<link href="../css/vault.css" rel="stylesheet">


<style>
.container {
    max-width: 100%;
}
</style>
</head>

<body>
    <div id="wrap">
        <div class="container">
        <h1 class="badge-primary">Add ingredient</h1>
<table width="100%" border="0">
        <tr>
          <td><div class="form-group">
<form action="" method="post" enctype="multipart/form-data" name="add_ing" target="_self" id="add_ing">  
                          <div class="table-responsive">
                            <table width="100%" border="0">
                              <tr>
                                <td colspan="3"><?php echo $msg; ?></td>
                              </tr>
                              <tr>
                                <td width="9%">Name:</td>
                                <td colspan="2"><input name="name" type="text" class="form-control ing_list" id="name"></td>
                              </tr>
                              <tr>
                                <td>CAS #:</td>
                                <td colspan="2"><input name="cas" type="text" class="form-control ing_list" id="cas"></td>
                              </tr>
                              <tr>
                                <td height="31">Is Allergen:</td>
                                <td width="31%"><input name="isAllergen" type="checkbox" id="isAllergen" value="1" /></td>
                                <td width="60%">&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Cat4 Limit %:</td>
                                <td colspan="2"><input name="IFRA" type="text" class="form-control ing_list" id="IFRA"></td>
                              </tr>
                              <tr>
                                <td>Purity %:</td>
                                <td colspan="2"><input name="purity" type="text" class="form-control ing_list" id="purity" value="100" /></td>
                              </tr>
                              <tr>
                                <td>Profile:</td>
                                <td colspan="2"><select name="profile" id="profile" class="form-control ing_list">
                                  <option value="" selected="selected"></option>
                                  <?php
									while ($row_ingProfiles = mysqli_fetch_array($res_ingProfiles)){
										echo '<option value="'.$row_ingProfiles['name'].'">'.$row_ingProfiles['name'].'</option>';
									}
								?>
                                </select></td>
                              </tr>
                              <tr>
                                <td>Type:</td>
                                <td colspan="2">
                                <select name="type" id="type" class="form-control ing_list">
                                <option value="" selected></option>
                                <?php
									while ($row_ingTypes = mysqli_fetch_array($res_ingTypes)){
										echo '<option value="'.$row_ingTypes['name'].'">'.$row_ingTypes['name'].'</option>';
									}
								?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td>Strength:</td>
                                <td colspan="2">
                                <select name="strength" id="strength" class="form-control ing_list">
                                <option value="" selected></option>
                                <?php
									while ($row_ingStrength = mysqli_fetch_array($res_ingStrength)){
										echo '<option value="'.$row_ingStrength['name'].'">'.$row_ingStrength['name'].'</option>';
									}
								?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td>Category:</td>
                                <td colspan="2">
                                <select name="category" id="category" class="form-control ing_list">
                                <option value="" selected></option>
                                  <?php
									while ($row_ingCategory = mysqli_fetch_array($res_ingCategory)){
										echo '<option value="'.$row_ingCategory['name'].'">'.$row_ingCategory['name'].'</option>';
									}
								?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td>Supplier:</td>
                                <td colspan="2">
                                <select name="supplier" id="supplier" class="form-control ing_list">
                                <option value="" selected></option>
                                  <?php
									while ($row_ingSupplier = mysqli_fetch_array($res_ingSupplier)){
										echo '<option value="'.$row_ingSupplier['name'].'">'.$row_ingSupplier['name'].'</option>';
									}
								?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td>Supplier URL:</td>
                                <td colspan="2"><input name="supplier_link" type="text" class="form-control" id="supplier_link"></td>
                              </tr>
                              <tr>
                                <td>Price (<?php echo $settings['currency'];?>):</td>
                                <td colspan="2"><input name="price" type="text" class="form-control" id="price" /></td>
                              </tr>
                              <tr>
                                <td>Tenacity:</td>
                                <td colspan="2"><input name="tenacity" type="text" class="form-control" id="tenacity" /></td>
                              </tr>
                              <tr>
                                <td>Flash Point:</td>
                                <td colspan="2"><input name="flash_point" type="text" class="form-control" id="flash_point" /></td>
                              </tr>
                              <tr>
                                <td>Chemical Name:</td>
                                <td colspan="2"><input name="chemical_name" type="text" class="form-control" id="chemical_name" /></td>
                              </tr>
                              <tr>
                                <td>Appearance:</td>
                                <td colspan="2"><input name="appearance" type="text" class="form-control" id="appearance" /></td>
                              </tr>
                              <tr>
                                <td valign="top">Size (ml):</td>
                                <td colspan="2"><input name="ml" type="text" class="form-control" id="ml" value="10" /></td>
                              </tr>
                              <tr>
                                <td valign="top">Odor:</td>
                                <td colspan="2"><input name="odor" type="text" class="form-control" id="odor" /></td>
                              </tr>
                              <tr>
                                <td valign="top">Notes:</td>
                                <td colspan="2"><textarea name="notes" id="notes" cols="45" rows="5" class="form-control"></textarea></td>
                              </tr>
                              <tr>
                                <td>SDS:</td>
                                <td colspan="2"><input type="file" class="form-control" name="SDS" id="SDS">                                  <a href="#" class="fas fa-question-circle" rel="tipsy" title="<?php echo 'Allowed filetypes: '.$allowed_ext.' Max file size: '.formatBytes($max_filesize); ?>"></a></td>
                              </tr>
                            </table>
                            <p>&nbsp;</p>
                            <p>
                              <input type="submit" name="submit" id="submit" class="btn btn-info" value="Submit" />
                            </p>
          </div>  
</form>
               </div>

</body>
</html>
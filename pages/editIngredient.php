<?php 
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');
require_once('../func/formatBytes.php');

require_once('../func/searchIFRA.php');

$ingID = mysqli_real_escape_string($conn, $_GET["id"]);

if($_POST){
	
	$ing = mysqli_fetch_array( mysqli_query($conn, "SELECT * FROM ingredients WHERE id = '$ingID'"));

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
	$odor = mysqli_real_escape_string($conn, $_POST["odor"]);
	$notes = mysqli_real_escape_string($conn, $_POST["notes"]);
	$purity = mysqli_real_escape_string($conn, $_POST["purity"]);

	if(($_FILES['SDS']['name'])){
      $file_name = $_FILES['SDS']['name'];
      $file_size =$_FILES['SDS']['size'];
      $file_tmp =$_FILES['SDS']['tmp_name'];
      $file_type=$_FILES['SDS']['type'];
      $file_ext=strtolower(end(explode('.',$_FILES['SDS']['name'])));
	  
	  if(empty($err)==true){
		 if (!file_exists("../$SDS_path")) {
    		 mkdir("../$SDS_path", 0740, true);
	  	 }
	  }
	  
	  $ext = explode(', ', $allowed_ext);
      if(in_array($file_ext,$ext)=== false){
		 $msg.='<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file.</div>';
      }elseif($file_size > $max_filesize){
		 $msg.='<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>File size must not exceed '.formatBytes($max_filesize).'</div>';
      }else{
	  
         move_uploaded_file($file_tmp,"../$SDS_path".base64_encode($file_name));
		 $SDSF = "../$SDS_path".base64_encode($file_name);
		 mysqli_query($conn, "UPDATE ingredients SET SDS = '$SDSF' WHERE name='$ingID'");
	  }
   }

	if(mysqli_query($conn, "UPDATE ingredients SET cas = '$cas', type = '$type', strength = '$strength', IFRA = '$IFRA', category='$category', supplier='$supplier', supplier_link='$supplier_link', profile='$profile', price='$price', tenacity='$tenacity', chemical_name='$chemical_name', flash_point='$flash_point', appearance='$appearance', notes='$notes', ml='$ml', odor='$odor', purity='$purity'  WHERE name='$ingID'")){
			$msg.='<div class="alert alert-success alert-dismissible">
  			Ingredient <strong>'.$name.'</strong> updated!
			</div>';
		}else{
			$msg.='<div class="alert alert-danger alert-dismissible">
  			<strong>Error:</strong> Failed to update '.$ing['name'].'!
			</div>';
		}
}

$res_ingTypes = mysqli_query($conn, "SELECT id,name FROM ingTypes");
$res_ingStrength = mysqli_query($conn, "SELECT id,name FROM ingStrength");
$res_ingCategory = mysqli_query($conn, "SELECT id,name FROM ingCategory");
$res_ingSupplier = mysqli_query($conn, "SELECT id,name FROM ingSuppliers");
$res_ingProfiles = mysqli_query($conn, "SELECT id,name FROM ingProfiles");

$sql = mysqli_query($conn, "SELECT * FROM ingredients WHERE name = '$ingID'");
if(empty(mysqli_num_rows($sql))){
	$msg='<div class="alert alert-danger alert-dismissible">
  			<strong>Error:</strong> ingredient not found, please click <a href="/?do=addIngredient">here</a> to add it first!
			</div>';
	die($msg);
}else{
	$ing = mysqli_fetch_array($sql);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit ingredient</title>
<link href="../css/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
<link href="../css/bootstrap.min.css" rel="stylesheet">
<script src="../js/jquery/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/bootstrap-select.js"></script>

<link href="../css/sb-admin-2.css" rel="stylesheet">
<link href="../css/vault.css" rel="stylesheet">
<link href="../css/bootstrap-select.min.css" rel="stylesheet">

<style>
.container {
    max-width: 100%;
}
</style>

<script>
function search() {	  
$("#odor").val('Loading...');
$.ajax({ 
    url: '/pages/searchTGSC.php', 
	type: 'get',
    data: {
		name: "<?php if($ing['cas']){ echo $ing['cas']; }else{ echo $ing['name'];}?>"
		},
    //dataType: 'text',
	dataType: 'html',
    success: function (data) {
      //$("#odor").val(data); 
	  $('#TGSC').html(data);
    }
  });
};

function printLabel() {
	<?php if(empty($settings['label_printer_addr']) || empty($settings['label_printer_model'])){?>
	$("#msg").html('<div class="alert alert-danger alert-dismissible">Please configure printer details in <a href="/?do=settings">settings<a> page</div>');
	<?php }else{ ?>
	$("#msg").html('<div class="alert alert-info alert-dismissible">Printing...</div>');

$.ajax({ 
    url: '/pages/manageFormula.php', 
	type: 'get',
    data: {
		action: "printLabel",
		name: "<?php echo $ing['name']; ?>"
		},
	dataType: 'html',
    success: function (data) {
	  $('#msg').html(data);
    }
  });
	<?php } ?>
};
</script>
</head>

<body>
    <div id="wrap">
        <div class="container">
<div class="list-group-item-info">
        <h1 class="badge-primary"><?php echo $ing['name']; ?><div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="javascript:printLabel();" onclick="return confirm('Print label?');">Print Label</a>
                      </div>
                    </div>
        </div></h1>

<table width="100%" border="0">
        <tr>
          <td><div class="form-group">  
			<form action="/pages/editIngredient.php?id=<?php echo $ingID; ?>" method="post" enctype="multipart/form-data" name="edit_ing" target="_self" id="edit_ing">  
                          <div class="table-responsive">
                            <table width="100%" border="0">
                              <tr>
                                <td colspan="4"><?php echo $msg; ?></td>
                              </tr>
                              <tr>
                                <td width="20%">CAS #:</td>
                                <td colspan="3"><input name="cas" type="text" class="form-control" id="cas" value="<?php echo $ing['cas']; ?>"></td>
                              </tr>
                              <tr>
                                <td> Cat4 Limit %:</td>
                                <td colspan="3">
                                <?php
								 	if($limit = searchIFRA($ing['cas'], $ing['name'],$conn)){
										echo $limit;
									}else{
								?>
                                <input name="IFRA" type="text" class="form-control" id="IFRA" value="<?php echo $ing['IFRA']; ?>">
                                <?php } ?>
                                </td>
                              </tr>
                              <tr>
                                <td>Purity %:</td>
                                <td colspan="3"><input name="purity" type="text" class="form-control" id="purity" value="<?php echo $ing['purity']; ?>" /></td>
                              </tr>
                              <tr>
                                <td>Profile:</td>
                                <td colspan="3">
                                <select name="profile" id="profile" class="form-control">
                                <option value="" selected></option>
                                <?php 	while ($row_ingProfiles = mysqli_fetch_array($res_ingProfiles)){ ?>
								<option value="<?php echo $row_ingProfiles['name'];?>" <?php echo ($ing['profile']==$row_ingProfiles['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingProfiles['name'];?></option>
								<?php } ?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td>Type:</td>
                                <td colspan="3">
                                <select name="type" id="type" class="form-control">
                                <option value="" selected></option>
                                <?php 	while ($row_ingTypes = mysqli_fetch_array($res_ingTypes)){ ?>
								<option value="<?php echo $row_ingTypes['name'];?>" <?php echo ($ing['type']==$row_ingTypes['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingTypes['name'];?></option>
								<?php } ?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td>Strength:</td>
                                <td colspan="3">
                                <select name="strength" id="strength" class="form-control">
                                <option value="" selected></option>
                                <?php while ($row_ingStrength = mysqli_fetch_array($res_ingStrength)){ ?>
								<option value="<?php echo $row_ingStrength['name'];?>" <?php echo ($ing['strength']==$row_ingStrength['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingStrength['name'];?></option>
								<?php } ?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td>Category:</td>
                                <td colspan="3">
                                <select name="category" id="category" class="form-control" data-live-search="true">
                                <option value="" selected></option>
                                  <?php while ($row_ingCategory = mysqli_fetch_array($res_ingCategory)){ ?>
								<option value="<?php echo $row_ingCategory['name'];?>" <?php echo ($ing['category']==$row_ingCategory['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingCategory['name'];?></option>
								  <?php } ?>
                                </select>
                                </td>
                              </tr>
                              <tr>
                                <td>Supplier:</td>
                                <td colspan="3">
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
                                <td>Tenacity:</td>
                                <td colspan="3"><input name="tenacity" type="text" class="form-control" id="tenacity" value="<?php echo $ing['tenacity']; ?>"/></td>
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
                                <td>Appearance:</td>
                                <td colspan="3"><input name="appearance" type="text" class="form-control" id="appearance" value="<?php echo $ing['appearance']; ?>"/></td>
                              </tr>
                              <tr>
                                <td valign="top">Size (ml):</td>
                                <td colspan="3"><input name="ml" type="text" class="form-control" id="ml" value="<?php echo $ing['ml']; ?>"/></td>
                              </tr>
                              <tr>
                                <td valign="top">Odor:</td>
                                <td width="66%"><div id='TGSC'><input name="odor" id="odor" type="text" class="form-control" value="<?php echo $ing['odor']; ?>"/></div>
                                </td>
                                <?php if(file_exists('searchTGSC.php')){?>
                                <td width="2%">&nbsp;</td>
                                <td width="12%"><a href="javascript:search();" id="search">Try TGSC</a></td>
                                <?php } ?>
                              </tr>
                              <tr>
                                <td valign="top">Notes:</td>
                                <td colspan="3"><textarea name="notes" id="notes" cols="45" rows="5" class="form-control"><?php echo $ing['notes']; ?></textarea></td>
                              </tr>
                              <tr>
                                <td>SDS Document:</td>
                                <td colspan="3"><input type="file" class="form-control" name="SDS" id="SDS"></td>
                              </tr>
                            </table>
                            <p>&nbsp;</p>
                            <p>
                              <input type="submit" name="save" id="submit" class="btn btn-info" value="Save" />
                            </p>
          </div>  
     </form>
                </div></td>
        </tr>
</table>
</div>
</div>
</body>
</html>

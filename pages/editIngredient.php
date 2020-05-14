<div class="container-fluid">
<?php require_once('pages/top.php'); ?>
<?php 
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
	$notes = mysqli_real_escape_string($conn, $_POST["notes"]);


	if(! empty($_FILES['SDS']['name'])){
      $file_name = $_FILES['SDS']['name'];
      $file_size =$_FILES['SDS']['size'];
      $file_tmp =$_FILES['SDS']['tmp_name'];
      $file_type=$_FILES['SDS']['type'];
      $file_ext=strtolower(end(explode('.',$_FILES['SDS']['name'])));
      
      $ext= array("pdf","doc","docx");
     
      if(in_array($file_ext,$ext)=== false){
         $msgF.="File upload error: Extension not allowed, please choose a pdf, doc or docx file.";
      }
      
      if($file_size > $max_filesize){
         $msgF.='File upload error: File size must be '.$max_filesize.' Max';
      }
      
      if(empty($msgF)==true){
         move_uploaded_file($file_tmp,"uploads/SDS/".base64_encode($file_name));
		 $SDSF = "uploads/SDS/".base64_encode($file_name);
		 mysqli_query($conn, "UPDATE ingredients SET SDS = '$SDSF' WHERE id='$ingID'");
	  }
   }

	if(mysqli_query($conn, "UPDATE ingredients SET cas = '$cas', type = '$type', strength = '$strength', IFRA = '$IFRA', category='$category', supplier='$supplier', supplier_link='$supplier_link', profile='$profile', price='$price', tenacity='$tenacity', chemical_name='$chemical_name', flash_point='$flash_point', appearance='$appearance', notes='$notes', ml='$ml'  WHERE name='$ingID'")){
			$msg='<div class="alert alert-success alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  			Ingredient <strong>'.$name.'</strong> updated!
			</div>';
		}else{
			$msg='<div class="alert alert-danger alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  			<strong>Error:</strong> Failed to update '.$ing['name'].'!
			</div>';
		}
}

$res_ingTypes = mysqli_query($conn, "SELECT id,name FROM ingTypes");
$res_ingStrength = mysqli_query($conn, "SELECT id,name FROM ingStrength");
$res_ingCategory = mysqli_query($conn, "SELECT id,name FROM ingCategory");
$res_ingSupplier = mysqli_query($conn, "SELECT id,name FROM ingSuppliers");
$res_ingProfiles = mysqli_query($conn, "SELECT id,name FROM ingProfiles");

$ing = mysqli_fetch_array( mysqli_query($conn, "SELECT * FROM ingredients WHERE name = '$ingID'"));

?>

          <h1 class="h3 mb-4 text-gray-800"><?php echo $ing['name']; ?></h1>

        </div>
<table width="100%" border="0">
        <tr>
          <td><div class="form-group">  
			<form action="?do=editIngredient&id=<?php echo $ingID; ?>" method="post" enctype="multipart/form-data" name="edit_ing" target="_self" id="edit_ing">  
                          <div class="table-responsive">
                            <table width="100%" border="0">
                              <tr>
                                <td colspan="3"><?php echo $msg; ?></td>
                              </tr>
                              <tr>
                                <td width="11%">CAS #:</td>
                                <td width="23%"><input name="cas" type="text" class="form-control" id="cas" value="<?php echo $ing['cas']; ?>"></td>
                                <td width="66%">&nbsp;</td>
                              </tr>
                              <tr>
                                <td>IFRA Limit %:</td>
                                <td><input name="IFRA" type="text" class="form-control" id="IFRA" value="<?php echo $ing['IFRA']; ?>"></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Profile:</td>
                                <td>
                                <select name="profile" id="profile" class="form-control">
                                <option value="" selected></option>
                                <?php 	while ($row_ingProfiles = mysqli_fetch_array($res_ingProfiles)){ ?>
								<option value="<?php echo $row_ingProfiles['name'];?>" <?php echo ($ing['profile']==$row_ingProfiles['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingProfiles['name'];?></option>
								<?php } ?>
                                </select>
                                </td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Type:</td>
                                <td>
                                <select name="type" id="type" class="form-control">
                                <option value="" selected></option>
                                <?php 	while ($row_ingTypes = mysqli_fetch_array($res_ingTypes)){ ?>
								<option value="<?php echo $row_ingTypes['name'];?>" <?php echo ($ing['type']==$row_ingTypes['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingTypes['name'];?></option>
								<?php } ?>
                                </select>
                                </td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Strength:</td>
                                <td>
                                <select name="strength" id="strength" class="form-control">
                                <option value="" selected></option>
                                <?php while ($row_ingStrength = mysqli_fetch_array($res_ingStrength)){ ?>
								<option value="<?php echo $row_ingStrength['name'];?>" <?php echo ($ing['strength']==$row_ingStrength['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingStrength['name'];?></option>
								<?php } ?>
                                </select>
                                </td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Category:</td>
                                <td>
                                <select name="category" id="category" class="form-control">
                                <option value="" selected></option>
                                  <?php while ($row_ingCategory = mysqli_fetch_array($res_ingCategory)){ ?>
								<option value="<?php echo $row_ingCategory['name'];?>" <?php echo ($ing['category']==$row_ingCategory['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingCategory['name'];?></option>
								  <?php } ?>
                                </select>
                                </td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Supplier:</td>
                                <td>
                                <select name="supplier" id="supplier" class="form-control">
                                <option value="" selected></option>
                                  <?php while ($row_ingSupplier = mysqli_fetch_array($res_ingSupplier)){ ?>
								<option value="<?php echo $row_ingSupplier['name'];?>" <?php echo ($ing['supplier']==$row_ingSupplier['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingSupplier['name'];?></option>
								  <?php	}	?>
                                </select>
                                </td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Supplier URL:</td>
                                <td><input name="supplier_link" type="text" class="form-control" id="supplier_link" value="<?php echo $ing['supplier_link']; ?>"></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
								<td>Price (<?php echo $settings['currency']; ?>):</td>
                                <td><input name="price" type="text" class="form-control" id="price" value="<?php echo $ing['price']; ?>"/></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Tenacity:</td>
                                <td><input name="tenacity" type="text" class="form-control" id="tenacity" value="<?php echo $ing['tenacity']; ?>"/></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Flash Point:</td>
                                <td><input name="flash_point" type="text" class="form-control" id="flash_point" value="<?php echo $ing['flash_point']; ?>"/></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Chemical Name:</td>
                                <td><input name="chemical_name" type="text" class="form-control" id="chemical_name" value="<?php echo $ing['chemical_name']; ?>"/></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Appearance:</td>
                                <td><input name="appearance" type="text" class="form-control" id="appearance" value="<?php echo $ing['appearance']; ?>"/></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td valign="top">Size (ml):</td>
                                <td><input name="ml" type="text" class="form-control" id="ml" value="<?php echo $ing['ml']; ?>"/></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td valign="top">Notes:</td>
                                <td><textarea name="notes" id="notes" cols="45" rows="5" class="form-control"><?php echo $ing['notes']; ?></textarea></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>SDS Document:</td>
                                <td><input type="file" class="form-control" name="SDS" id="SDS"></td>
                                <td>&nbsp;</td>
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
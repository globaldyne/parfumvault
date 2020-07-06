<?php if (!defined('pvault_panel')){ die('Not Found');}  ?>
<div class="container-fluid">
<?php require_once('pages/top.php'); ?>
<?php 

if($_POST['name']){
	
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ml = mysqli_real_escape_string($conn, $_POST['ml']);
	$price = mysqli_real_escape_string($conn, $_POST['price']);
	$height = mysqli_real_escape_string($conn, $_POST['height']);
	$width = mysqli_real_escape_string($conn, $_POST['width']);
	$diameter = mysqli_real_escape_string($conn, $_POST['diameter']);
	$supplier = mysqli_real_escape_string($conn, $_POST['supplier']);
	$supplier_link = mysqli_real_escape_string($conn, $_POST['supplier_link']);
	$notes = mysqli_real_escape_string($conn, $_POST['notes']);

	if(!empty($_FILES['photo']['name'])){
      $file_name = $_FILES['photo']['name'];
      $file_size = $_FILES['photo']['size'];
      $file_tmp =  $_FILES['photo']['tmp_name'];
      $file_type = $_FILES['photo']['type'];
      $file_ext = strtolower(end(explode('.',$_FILES['photo']['name'])));
      
	  if (!file_exists("uploads/bottles/")) {
		  mkdir("uploads/bottles/", 0740, true);
	  }
	  
      $ext = explode(', ',strtolower($allowed_ext));

 	  if(in_array($file_ext,$ext)=== false){
		 $msg.='<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>Extension '.$file_ext.' not allowed, please choose a '.$allowed_ext.' file.</div>';
      }elseif($file_size > $max_filesize){
		 $msg.='<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>File size must not exceed '.formatBytes($max_filesize).'</div>';
      }else{
         move_uploaded_file($file_tmp, "uploads/bottles/".base64_encode($file_name));
		 $photo = "uploads/bottles/".base64_encode($file_name);
      }
   }

	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM bottles WHERE name = '$name'"))){
		$msg='<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>Error: </strong>'.$name.' already exists!
		</div>';
	}else{
		
		if(mysqli_query($conn, "INSERT INTO bottles (name, ml, price, height, width, diameter, supplier, supplier_link, notes, photo) VALUES ('$name', '$ml', '$price', '$height', '$width', '$diameter', '$supplier', '$supplier_link', '$notes', '$photo')") ){
			$msg.='<div class="alert alert-success alert-dismissible">
			<a href="?do=bottles" class="close" data-dismiss="alert" aria-label="close">x</a>
  			<strong>'.$name.'</strong> added!</div>';
		}else{
			$msg.='<div class="alert alert-danger alert-dismissible">
			<a href="?do=bottles" class="close" data-dismiss="alert" aria-label="close">x</a>
  			<strong>Error:</strong> Failed to add '.$name.' - '.mysqli_error($conn).'</div>';
		}
	}
}

$res_ingSupplier = mysqli_query($conn, "SELECT id,name FROM ingSuppliers");

?>
       <h1 class="h1 mb-4 text-gray-800"><a href="?do=bottles"> New Bottle</a></h1>
       </div>
<table width="100%" border="0">
        <tr>
          <td><div class="form-group">  
<form action="?do=addBottle" method="post" enctype="multipart/form-data" target="_self">  
                          <div class="table-responsive">
                            <table width="100%" border="0">
                              <tr>
                                <td colspan="3"><?php echo $msg; ?></td>
                              </tr>
                              <tr>
                                <td width="6%">Name:</td>
                                <td width="34%"><input name="name" type="text" class="form-control" id="name"></td>
                                <td width="60%">&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Size (ml):</td>
                                <td><input name="ml" type="text" class="form-control" id="ml"></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Price:</td>
                                <td><input name="price" type="text" class="form-control" id="price"></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Height:</td>
                                <td><input name="height" type="text" class="form-control" id="height" /></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Width:</td>
                                <td><input name="width" type="text" class="form-control" id="width" /></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Diameter:</td>
                                <td><input name="diameter" type="text" class="form-control" id="diameter" /></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Supplier:</td>
                                <td>
                                <select name="supplier" id="supplier" class="form-control">
                                <option value="" selected></option>
                                  <?php
									while ($row_ingSupplier = mysqli_fetch_array($res_ingSupplier)){
										echo '<option value="'.$row_ingSupplier['name'].'">'.$row_ingSupplier['name'].'</option>';
									}
								?>
                                </select>
                                </td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Supplier URL:</td>
                                <td><input name="supplier_link" type="text" class="form-control" id="supplier_link"></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td valign="top">Notes:</td>
                                <td><textarea name="notes" id="notes" cols="45" rows="5" class="form-control"></textarea></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Image :</td>
                                <td><input type="file" class="form-control" name="photo" id="photo"></td>
                                <td><a href="#" class="fas fa-question-circle" rel="tipsy" title="<?php echo 'Allowed filetypes: '.$allowed_ext.' Max file size: '.formatBytes($max_filesize); ?>"></a></td>
                              </tr>
                            </table>
                            <p>&nbsp;</p>
                            <p>
                              <input type="submit" name="submit" id="submit" class="btn btn-info" value="Submit" />
                            </p>
          </div>  
</form>
                </div></td>
        </tr>
</table>

<?php if (!defined('pvault_panel')){ die('Not Found');}  ?>
<div class="container-fluid">
<?php require_once('pages/top.php'); ?>
<?php 

if($_POST['style']){
	
	$style = mysqli_real_escape_string($conn, $_POST['style']);
	$colour = mysqli_real_escape_string($conn, $_POST['colour']);
	$price = mysqli_real_escape_string($conn, $_POST['price']);
	$supplier = mysqli_real_escape_string($conn, $_POST['supplier']);
	$supplier_link = mysqli_real_escape_string($conn, $_POST['supplier_link']);

	if(!empty($_FILES['photo']['name'])){
      $file_name = $_FILES['photo']['name'];
      $file_size = $_FILES['photo']['size'];
      $file_tmp =  $_FILES['photo']['tmp_name'];
      $file_type = $_FILES['photo']['type'];
      $file_ext = strtolower(end(explode('.',$_FILES['photo']['name'])));
      
	  if (!file_exists("uploads/lids/")) {
		  mkdir("uploads/lids/", 0740, true);
	  }
	  
      $ext = explode(', ',strtolower($allowed_ext));

 	  if(in_array($file_ext,$ext)=== false){
		 $msg.='<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>Extension '.$file_ext.' not allowed, please choose a '.$allowed_ext.' file.</div>';
      }elseif($file_size > $max_filesize){
		 $msg.='<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>File size must not exceed '.formatBytes($max_filesize).'</div>';
      }else{
         move_uploaded_file($file_tmp, "uploads/lids/".base64_encode($file_name));
		 $photo = "uploads/lids/".base64_encode($file_name);
      }
   }

//	if(mysqli_num_rows(mysqli_query($conn, "SELECT style FROM lids WHERE style = '$style'"))){
//		$msg='<div class="alert alert-danger alert-dismissible">
//		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
// 		<strong>Error: </strong>'.$style.' already exists!
//		</div>';
//	}else{
		
		if(mysqli_query($conn, "INSERT INTO lids (style, colour, price, supplier, supplier_link, photo) VALUES ('$style', '$colour', '$price', '$supplier', '$supplier_link', '$photo')") ){
			$msg.='<div class="alert alert-success alert-dismissible">
			<a href="?do=lids" class="close" data-dismiss="alert" aria-label="close">x</a>
  			<strong>'.$style.'</strong> added!</div>';
		}else{
			$msg.='<div class="alert alert-danger alert-dismissible">
			<a href="?do=lids" class="close" data-dismiss="alert" aria-label="close">x</a>
  			<strong>Error:</strong> Failed to add '.$style.' - '.mysqli_error($conn).'</div>';
		}
	}
//}

$res_ingSupplier = mysqli_query($conn, "SELECT id,name FROM ingSuppliers");

?>
       <h1 class="h1 mb-4 text-gray-800"><a href="?do=lids"> New Bottle Lid</a></h1>
       </div>
<table width="100%" border="0">
        <tr>
          <td><div class="form-group">  
<form action="?do=addLid" method="post" enctype="multipart/form-data" target="_self">  
                          <div class="table-responsive">
                            <table width="100%" border="0">
                              <tr>
                                <td colspan="3"><?php echo $msg; ?></td>
                              </tr>
                              <tr>
                                <td width="6%">Style:</td>
                                <td width="34%"><input name="style" type="text" class="form-control" id="style"></td>
                                <td width="60%">&nbsp;</td>
                              </tr>
                              <tr>
                                <td>Colour:</td>
                                <td><input name="colour" type="text" class="form-control" id="colour"></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>

                                <td>Price:</td>
                                <td><input name="price" type="text" class="form-control" id="price"></td>
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

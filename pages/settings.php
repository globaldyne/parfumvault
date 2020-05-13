<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
<?php 

if(($_POST) && $_GET['update'] == 'printer'){
	$label_printer_addr = mysqli_real_escape_string($conn, $_POST['label_printer_addr']);
	$label_printer_model = mysqli_real_escape_string($conn, $_POST['label_printer_model']);
	$label_printer_size = mysqli_real_escape_string($conn, $_POST['label_printer_size']);
	$label_printer_font_size = mysqli_real_escape_string($conn, $_POST['label_printer_font_size']);

	if(mysqli_query($conn, "UPDATE settings SET label_printer_addr='$label_printer_addr', label_printer_model='$label_printer_model', label_printer_size='$label_printer_size', label_printer_font_size='$label_printer_font_size'")){
		$msg = '<div class="alert alert-success alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Printer settings updated!
		</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Error updating settings!
		</div>';
	}
	require('./inc/settings.php');

}elseif(!empty($_FILES['logo']['name']) && $_GET['update'] == 'general'){
	  $file_name = $_FILES['logo']['name'];
      $file_size =$_FILES['logo']['size'];
      $file_tmp = $_FILES['logo']['tmp_name'];
      $file_type = $_FILES['logo']['type'];
      $file_ext = strtolower(end(explode('.',$_FILES['logo']['name'])));

      $expensions = array("png","jpg","jpeg");
     
      if(in_array($file_ext,$expensions)=== false){
         $err = "File upload error: Extension not allowed, please choose a png, jpg or jpeg file.";
      }
     
      if(empty($err)==true){
         move_uploaded_file($file_tmp,"uploads/logo/".base64_encode($file_name));
		 $logo = "uploads/logo/".base64_encode($file_name);
		 if(mysqli_query($conn, "UPDATE settings SET logo='$logo'")){
			$msg = '<div class="alert alert-success alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Logo updated!
		</div>';
		 }else{
			$msg = '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Error updating logo '.$err.'
		</div>';
		 }

      }

}elseif($_POST['supplier'] && $_GET['update'] == 'suppliers'){
	$sup = mysqli_real_escape_string($conn, $_POST['supplier']);
	$notes = mysqli_real_escape_string($conn, $_POST['sup_notes']);
	
	if(mysqli_query($conn, "INSERT INTO ingSuppliers (name,notes) VALUES ('$sup', '$notes')")){
		
		$msg = '<div class="alert alert-success alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Supplier added!
		</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Error adding supplier
		</div>';
	}

}elseif($_POST['category'] && $_GET['update'] == 'categories'){
	$cat = mysqli_real_escape_string($conn, $_POST['category']);
	$notes = mysqli_real_escape_string($conn, $_POST['cat_notes']);
	
	if(mysqli_query($conn, "INSERT INTO ingCategory (name,notes) VALUES ('$cat', '$notes')")){
		
		$msg = '<div class="alert alert-success alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Category added!
		</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Error adding category
		</div>';
	}
}elseif($_POST['profile'] && $_GET['update'] == 'profiles'){
	$profile = mysqli_real_escape_string($conn, $_POST['profile']);
	$notes = mysqli_real_escape_string($conn, $_POST['prof_notes']);
	
	if(mysqli_query($conn, "INSERT INTO ingProfiles (name,notes) VALUES ('$profile', '$notes')")){
		
		$msg = '<div class="alert alert-success alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Profile added!
		</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Error adding profile
		</div>';
	}	

}elseif($_POST['currency'] && $_GET['update'] == 'general'){
	$currency = utf8_encode(htmlentities($_POST['currency']));
	if(mysqli_query($conn, "UPDATE settings SET currency='$currency'")){
		
		$msg = '<div class="alert alert-success alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Currency updated!
		</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Error updating currency'.mysqli_error($conn).'
		</div>';
	}	

}elseif($_GET['action'] == 'delete' && $_GET['sup_id']){
	$sup_id = mysqli_real_escape_string($conn, $_GET['sup_id']);
	if(mysqli_query($conn, "DELETE FROM ingSuppliers WHERE id = '$sup_id'")){
		
		$msg = '<div class="alert alert-success alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Supplier deleted!
		</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Error deleting supplier.
		</div>';
	}
}elseif($_GET['action'] == 'delete' && $_GET['cat_id']){
	$cat_id = mysqli_real_escape_string($conn, $_GET['cat_id']);
	if(mysqli_query($conn, "DELETE FROM ingCategory WHERE id = '$cat_id'")){
		
		$msg = '<div class="alert alert-success alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Category deleted!
		</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Error deleting category.
		</div>';
	}
}elseif($_GET['action'] == 'delete' && $_GET['prof_id']){
	$prof_id = mysqli_real_escape_string($conn, $_GET['prof_id']);
	if(mysqli_query($conn, "DELETE FROM ingProfiles WHERE id = '$prof_id'")){
		
		$msg = '<div class="alert alert-success alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Profile deleted!
		</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		Error deleting profile.
		</div>';
	}
}												   
 
$cat_q = mysqli_query($conn, "SELECT * FROM ingCategory ORDER BY name ASC");
$sup_q = mysqli_query($conn, "SELECT * FROM ingSuppliers ORDER BY name ASC");
$prof_q = mysqli_query($conn, "SELECT * FROM ingProfiles ORDER BY name ASC");

?>
<link rel="stylesheet" href="../css/jquery-ui.css">
<script src="../js/jquery-ui.js"></script>
<script>
$(function() {
  $("#settings").tabs();
});
</script>
<div class="container-fluid">

<h2 class="m-0 mb-4 text-primary">Settings</h2>
<div id="settings">
     <ul>
         <li><a href="#general"><span>General</span></a></li>
         <li><a href="#suppliers"><span>Suppliers</span></a></li>
         <li><a href="#categories"><span>Categories</span></a></li>
         <li><a href="#profiles"><span>Profiles</span></a></li>
      <li><a href="#print"><span>Printing</span></a></li>
         <li><a href="pages/about.php"><span>About</span></a></li>
     </ul>
     <div id="general">
     <form id="form" name="form" method="post" enctype="multipart/form-data" action="/?do=settings&update=general#general">
     <table width="100%" border="0">
        <tr>
          <td colspan="4"><?php echo $msg; ?></td>
          </tr>
        <tr>
          <td width="6%">Logo:</td>
          <td width="15%"><input type="file" class="form-control" name="logo" id="logo" /></td>
          <td width="1%">&nbsp;</td>
          <td width="78%">&nbsp;</td>
          </tr>
        <tr>
          <td>Currency:</td>
          <td><input name="currency" type="text" class="form-control" id="currency" value="<?php echo utf8_encode($settings['currency']);?>"/></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><input type="submit" name="button" id="button" value="Submit" class="btn btn-info"/></td>
          <td colspan="3">&nbsp;</td>
          </tr>
      </table>
     </form>
	 </div>
     <div id="suppliers">
       <form id="form" name="form" method="post" action="/?do=settings&update=suppliers#suppliers">
      <table width="100%" border="0">
              <tr>
                <td colspan="7"><?php echo $msg; ?></td>
              </tr>
              <tr>
                <td width="4%">Supplier:</td>
                <td width="13%"><input type="text" name="supplier" id="supplier" class="form-control"/></td>
                <td width="1%">&nbsp;</td>
                <td width="5%">Description:</td>
                <td width="13%"><input type="text" name="sup_notes" id="sup_notes" class="form-control"/></td>
                <td width="2%">&nbsp;</td>
                <td width="62%"><input type="submit" name="add_supp" id="add_supp" value="Add" class="btn btn-info"/></td>
              </tr>
              <tr>
                <td colspan="7">
              <div class="card-body">
              <div>
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder">
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="sup_data">
                  <?php while ($sup = mysqli_fetch_array($sup_q)) {
					  echo'
                    <tr>
                      <td data-name="sname" class="sname" data-type="text" align="center" data-pk="'.$sup['id'].'">'.$sup['name'].'</td>
					  <td data-name="snotes" class="snotes" data-type="text" align="center" data-pk="'.$sup['id'].'">'.$sup['notes'].'</td>
                      <td align="center"><a href="/?do=settings&action=delete&sup_id='.$sup['id'].'#suppliers" onclick="return confirm(\'Delete supplier '.$sup['name'].'?\');" class="fas fa-trash"></a></td>
					</tr>';
				  		}
                    ?>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
                </td>
              </tr>
            </table>
          </form>
     </div>
     <div id="categories">
       <form id="form" name="form" method="post" action="/?do=settings&update=categories#categories">
            <table width="100%" border="0">
              <tr>
                <td colspan="8"><?php echo $msg; ?></td>
              </tr>
              <tr>
                <td width="4%"><p>Category:</p></td>
                <td width="12%"><input type="text" name="category" id="category" class="form-control"/></td>
                <td width="1%">&nbsp;</td>
                <td width="6%">Description:</td>
                <td width="13%"><input type="text" name="cat_notes" id="cat_notes" class="form-control"/></td>
                <td width="2%">&nbsp;</td>
                <td width="22%"><input type="submit" name="add_cat" id="add_cat" value="Add" class="btn btn-info" /></td>
                <td width="40%">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="8">
                <div class="card-body">
              <div>
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder">
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="cat_data">
                  <?php while ($cat = mysqli_fetch_array($cat_q)) {
					  echo'
                    <tr>
                      <td data-name="cname" class="cname" data-type="text" align="center" data-pk="'.$cat['id'].'">'.$cat['name'].'</td>
					  <td width="60%" data-name="cnotes" class="cnotes" data-type="text" align="left" data-pk="'.$cat['id'].'"align="left">'.wordwrap($cat['notes'], 150, "<br />\n").'</td>
                      <td align="center"><a href="/?do=settings&action=delete&cat_id='.$cat['id'].'#categories" onclick="return confirm(\'Delete category '.$cat['name'].'?\');" class="fas fa-trash"></a></td>
					</tr>';
				  		}
                    ?>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
                </td>
              </tr>
            </table>
          </form>
     </div> 
     
     <div id="profiles">
       <form id="form" name="form" method="post" action="/?do=settings&update=profiles#profiles">
            <table width="100%" border="0">
              <tr>
                <td colspan="8"><?php echo $msg; ?></td>
              </tr>
              <tr>
                <td width="4%"><p>Profile:</p></td>
                <td width="12%"><input type="text" name="profile" id="profile" class="form-control"/></td>
                <td width="1%">&nbsp;</td>
                <td width="6%">Description:</td>
                <td width="13%"><input type="text" name="prof_notes" id="prof_notes" class="form-control"/></td>
                <td width="2%">&nbsp;</td>
                <td width="22%"><input type="submit" name="add_prof" id="add_prof" value="Add" class="btn btn-info" /></td>
                <td width="40%">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="8">
                <div class="card-body">
              <div>
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder">
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="prof_data">
                  <?php while ($prof = mysqli_fetch_array($prof_q)) {
					  echo'
                    <tr>
                      <td data-name="pname" class="pname" data-type="text" align="center" data-pk="'.$prof['id'].'">'.$prof['name'].'</td>
					  <td data-name="pnotes" class="pnotes" data-type="text" align="center" data-pk="'.$prof['id'].'"align="center">'.$prof['notes'].'</td>
                      <td align="center"><a href="/?do=settings&action=delete&prof_id='.$prof['id'].'#profiles" onclick="return confirm(\'Delete profile '.$prof['name'].'?\');" class="fas fa-trash"></a></td>
					</tr>';
				  		}
                    ?>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
                </td>
              </tr>
            </table>
          </form>
     </div> 
    <div id="print">
        <form id="form1" name="form1" method="post" action="/?do=settings&update=printer#print">
        <table width="100%" border="0">
          <tr>
            <td colspan="4"><?php echo $msg; ?></td>
            </tr>
          <tr>
            <td width="6%">Printer:</td>
            <td width="15%"><input name="label_printer_addr" type="text" class="form-control" id="label_printer_addr" value="<?php echo $settings['label_printer_addr']; ?>" /></td>
            <td width="1%"></td>
            <td width="78%"><a href="#" class="fas fa-question-circle" rel="tipsy" title="Your printer IP/Hostname. eg: 192.168.1.1"></a></td>
          </tr>
          <tr>
            <td>Model:</td>
            <td>
            <select name="label_printer_model" id="label_printer_model" class="form-control">
              <option value="" selected="selected"></option>
			<option value="<?php echo $settings['label_printer_model'];?>" <?php echo ($settins['label_printer_model']==$settins['label_printer_model'])?"selected=\"selected\"":""; ?>><?php echo $settings['label_printer_model'];?></option>
              <option value="QL-500">QL-500</option>
              <option value="QL-550">QL-550</option>
              <option value="QL-560">QL-560</option>
              <option value="QL-570">QL-570</option>
              <option value="QL-850">QL-850</option>
              <option value="QL-650TD">QL-650TD</option>
              <option value="QL-700">QL-700</option>
              <option value="QL-710W">QL-710W</option>
              <option value="QL-720NW">QL-720NW</option>
              <option value="QL-800">QL-800</option>
              <option value="QL-810W">QL-810W</option>
              <option value="QL-820NB">QL-820NB</option>
              <option value="QL-1050">QL-1050</option>
              <option value="QL-1060N">QL-1060N</option>
            </select>
            </td>
            <td></td>
            <td><a href="#" class="fas fa-question-circle" rel="tipsy" title="Your Brother printer model"></a></td>
          </tr>
          <tr>
            <td>Label Size:</td>
            <td>
            <select name="label_printer_size" id="label_printer_size" class="form-control">
              <option value="" selected="selected"></option>
			<option value="<?php echo $settings['label_printer_size'];?>" <?php echo ($settins['label_printer_size']==$settins['label_printer_size'])?"selected=\"selected\"":""; ?>><?php echo $settings['label_printer_size'].' mm';?></option>
              <option value="12">12 mm</option>
              <option value="29">29 mm</option>
              <option value="38">38 mm</option>
              <option value="50">50 mm</option>
              <option value="54">54 mm</option>
              <option value="62">62 mm</option>
              <option value="102">102 mm</option>
              <option value="17x54">17x54 mm</option>
              <option value="17x87">17x87 mm</option>
              <option value="23x23">23x23 mm</option>
              <option value="29x42">29x42 mm</option>
              <option value="29x90">29x90 mm</option>
              <option value="39x90">39x90 mm</option>
              <option value="39x48">39x48 mm</option>
              <option value="52x29">52x29 mm</option>
              <option value="62x29">62x29 mm</option>
              <option value="62x100">62x100 mm</option>
              <option value="102x51">102x51 mm</option>
              <option value="d12">d12</option>
              <option value="d24">d24</option>
              <option value="d58">d58</option>
            </select>
            </td>
            <td></td>
            <td><a href="#" class="fas fa-question-circle" rel="tipsy" title="Choose your tape size"></a>&nbsp;</td>
          </tr>
          <tr>
            <td>Font Size:</td>
            <td><input name="label_printer_font_size" type="text" id="label_printer_font_size" value="<?php echo $settings['label_printer_font_size']; ?>" class="form-control"/></td>
            <td>&nbsp;</td>
            <td><a href="#" class="fas fa-question-circle" rel="tipsy" title="Label font size"></a></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td><input type="submit" name="button" id="button" value="Submit" class="btn btn-info"/></td>
            <td colspan="3">&nbsp;</td>
          </tr>
        </table>
      </form>
</div>
  </div>
      </div>
</div>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
 
  $('#cat_data').editable({
  container: 'body',
  selector: 'td.cname',
  url: "/pages/update_data.php?settings=cat",
  title: 'Category',
  type: "POST",
  dataType: 'json',
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
 });
 
   $('#cat_data').editable({
  container: 'body',
  selector: 'td.cnotes',
  url: "/pages/update_data.php?settings=cat",
  title: 'Description',
  type: "POST",
  dataType: 'json',
  validate: function(value){
  }
 });
 
 
   $('#sup_data').editable({
  container: 'body',
  selector: 'td.sname',
  url: "/pages/update_data.php?settings=sup",
  title: 'Category',
  type: "POST",
  dataType: 'json',
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
 });
 
   $('#sup_data').editable({
  container: 'body',
  selector: 'td.snotes',
  url: "/pages/update_data.php?settings=sup",
  title: 'Description',
  type: "POST",
  dataType: 'json',
  validate: function(value){
  }
 });

   $('#prof_data').editable({
  container: 'body',
  selector: 'td.pname',
  url: "/pages/update_data.php?settings=profile",
  title: 'Profile',
  type: "POST",
  dataType: 'json',
  validate: function(value){
   if($.trim(value) == ''){
    return 'This field is required';
   }
  }
 });
 
   $('#prof_data').editable({
  container: 'body',
  selector: 'td.pnotes',
  url: "/pages/update_data.php?settings=profile",
  title: 'Description',
  type: "POST",
  dataType: 'json',
  validate: function(value){
  }
 });

});
</script>
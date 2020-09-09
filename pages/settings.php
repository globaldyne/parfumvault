<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<div id="content-wrapper" class="d-flex flex-column">
<?php 
require_once('pages/top.php'); 

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


	
//ADD SUPPLIERS
}elseif($_POST['supplier'] && $_GET['update'] == 'suppliers'){
	$sup = mysqli_real_escape_string($conn, $_POST['supplier']);
	$notes = mysqli_real_escape_string($conn, $_POST['sup_notes']);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE name = '$sup'"))){
		$msg='<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>Error: </strong>'.$sup.' already exists!
		</div>';
	}elseif(mysqli_query($conn, "INSERT INTO ingSuppliers (name,notes) VALUES ('$sup', '$notes')")){
		
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
//ADD CATEGORY
}elseif($_POST['category'] && $_GET['update'] == 'categories'){
	$cat = mysqli_real_escape_string($conn, $_POST['category']);
	$notes = mysqli_real_escape_string($conn, $_POST['cat_notes']);
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingCategory WHERE name = '$cat'"))){
		$msg='<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>Error: </strong>'.$cat.' already exists!
		</div>';
	}elseif(mysqli_query($conn, "INSERT INTO ingCategory (name,notes) VALUES ('$cat', '$notes')")){
		
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

//GENERAL SETTINGS
}elseif($_POST['currency'] && $_POST['top_n'] && $_POST['heart_n'] && $_POST['base_n'] && $_GET['update'] == 'general'){
	$currency = utf8_encode(htmlentities($_POST['currency']));
	$top_n = mysqli_real_escape_string($conn, $_POST['top_n']);
	$heart_n = mysqli_real_escape_string($conn, $_POST['heart_n']);
	$base_n = mysqli_real_escape_string($conn, $_POST['base_n']);
	$grp_formula = mysqli_real_escape_string($conn, $_POST['grp_formula']);
	$chem_vs_brand = mysqli_real_escape_string($conn, $_POST['chem_vs_brand']);
	$pubChem = mysqli_real_escape_string($conn, $_POST['pubChem']);
	$chkVersion = mysqli_real_escape_string($conn, $_POST['chkVersion']);
	$pv_maker = mysqli_real_escape_string($conn, $_POST['pv_maker']);

	if(empty($chem_vs_brand)){
		$chem_vs_brand = '0';
	}
	if(empty($grp_formula)){
		$grp_formula = '0';
	}
	if(empty($pubChem)){
		$pubChem = '0';
	}
	if(empty($chkVersion)){
		$chkVersion = '0';
	}
	if(empty($pv_maker)){
		$pv_maker = '0';
	}
	
	if(mysqli_query($conn, "UPDATE settings SET currency = '$currency', top_n = '$top_n', heart_n = '$heart_n', base_n = '$base_n', chem_vs_brand = '$chem_vs_brand', grp_formula = '$grp_formula', pubChem='$pubChem', chkVersion='$chkVersion', pv_maker='$pv_maker'")){
		$msg = '<div class="alert alert-success alert-dismissible">Settings updated!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">An error occured. ('.mysqli_error($conn).')</div>';	
	}
	
//PERFUME TYPES
}elseif($_POST['edp'] && $_POST['edc'] && $_POST['edt'] && $_POST['parfum'] && $_GET['update'] == 'types'){
	$edp = utf8_encode(htmlentities($_POST['edp']));
	$edc = mysqli_real_escape_string($conn, $_POST['edc']);
	$edt = mysqli_real_escape_string($conn, $_POST['edt']);
	$parfum = mysqli_real_escape_string($conn, $_POST['parfum']);
	
	if(mysqli_query($conn, "UPDATE settings SET EDP = '$edp', EDT = '$edt', EDC = '$edc', Parfum = '$parfum'")){
		$msg = '<div class="alert alert-success alert-dismissible">Settings updated!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">An error occured. ('.mysqli_error($conn).')</div>';	
	}
	
//USERS
}elseif($_GET['update'] == 'users' && $_POST['username'] && $_POST['password']){
	$username = mysqli_real_escape_string($conn, $_POST['username']);
	$password = mysqli_real_escape_string($conn, $_POST['password']);
	$fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	if (strlen($password) < '5') {
		$msg='<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>Error: </strong>Password must be at least 5 chars long!</div>';
	}elseif(mysqli_num_rows(mysqli_query($conn, "SELECT username FROM users WHERE username = '$username' OR email = '$email' "))){
		$msg='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$username.' already exists!</div>';
	}elseif(mysqli_query($conn, "INSERT INTO users (username,password,fullName,email) VALUES ('$username', PASSWORD('$password'), '$fullName', '$email')")){
		
		$msg = '<div class="alert alert-success alert-dismissible">User added!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">Error adding user. ('.mysqli_error($conn).')</div>';
	}
	
//CUSTOMERS
}elseif($_GET['update'] == 'customers' && $_POST['cname']){
	$cname = mysqli_real_escape_string($conn, $_POST['cname']);
	$caddress = mysqli_real_escape_string($conn, $_POST['caddress']);
	$cemail = mysqli_real_escape_string($conn, $_POST['cemail']);
	$cweb = mysqli_real_escape_string($conn, $_POST['cweb']);
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM customers WHERE name = '$cname'"))){
		$msg='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$cname.' already exists!</div>';
	}elseif(mysqli_query($conn, "INSERT INTO customers (name,address,email,web) VALUES ('$cname', '$caddress', '$cemail', '$cweb')")){
		$msg = '<div class="alert alert-success alert-dismissible">Customer '.$cname.' added!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">Error adding customer. ('.mysqli_error($conn).')</div>';
	}



//BRAND
}elseif($_GET['update'] == 'brand' && $_POST['brandName']){
	$brandName = mysqli_real_escape_string($conn, $_POST['brandName']);
	$brandAddress = mysqli_real_escape_string($conn, $_POST['brandAddress']);
	$brandEmail = mysqli_real_escape_string($conn, $_POST['brandEmail']);
	$brandPhone = mysqli_real_escape_string($conn, $_POST['brandPhone']);

	if(mysqli_query($conn, "UPDATE settings SET brandName = '$brandName', brandAddress = '$brandAddress', brandEmail = '$brandEmail', brandPhone = '$brandPhone'")){
		
		$msg = '<div class="alert alert-success alert-dismissible">Brand details updated!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">Error updating brand info: ('.mysqli_error($conn).')</div>';
	}
	
//PV MAKER
}elseif($_GET['update'] == 'pvmaker'){
	$pv_maker = mysqli_real_escape_string($conn, $_POST['pv_maker']);
	$pv_maker_host = mysqli_real_escape_string($conn, $_POST['pv_maker_host']);

	if(empty($pv_maker)){
		$pv_maker = '0';
	}
	if(mysqli_query($conn, "UPDATE settings SET pv_maker = '$pv_maker', pv_maker_host = '$pv_maker_host'")){
		$msg = '<div class="alert alert-success alert-dismissible">PV Maker details updated!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">Error updating PV Maker info: ('.mysqli_error($conn).')</div>';
	}

//DELETE ACTIONS
}elseif($_GET['action'] == 'delete' && $_GET['sup_id']){
	$sup_id = mysqli_real_escape_string($conn, $_GET['sup_id']);
	if(mysqli_query($conn, "DELETE FROM ingSuppliers WHERE id = '$sup_id'")){
		$msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Supplier deleted!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error deleting supplier.</div>';
	}
}elseif($_GET['action'] == 'delete' && $_GET['cat_id']){
	$cat_id = mysqli_real_escape_string($conn, $_GET['cat_id']);
	if(mysqli_query($conn, "DELETE FROM ingCategory WHERE id = '$cat_id'")){
		$msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Category deleted!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error deleting category.</div>';
	}

}elseif($_GET['action'] == 'delete' && $_GET['user_id']){
	$user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
	if(mysqli_query($conn, "DELETE FROM users WHERE id = '$user_id'")){
		$msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>User deleted!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error deleting user.</div>';
	}


}elseif($_GET['action'] == 'delete' && $_GET['customer_id']){
	$customer_id = mysqli_real_escape_string($conn, $_GET['customer_id']);
	if(mysqli_query($conn, "DELETE FROM customers WHERE id = '$customer_id'")){
		$msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Customer deleted!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error deleting customer.</div>';
	}

}												   
 
$cat_q = mysqli_query($conn, "SELECT * FROM ingCategory ORDER BY name ASC");
$sup_q = mysqli_query($conn, "SELECT * FROM ingSuppliers ORDER BY name ASC");
$users_q = mysqli_query($conn, "SELECT * FROM users ORDER BY username ASC");
$customers_q = mysqli_query($conn, "SELECT * FROM customers ORDER BY name ASC");

require('./inc/settings.php');

?>
<script>
$(function() {
  $("#settings").tabs();
  $("#username").val('');
  $("#password").val('');
  $("#fname").val('');
  $("#email").val('');

});


</script>
<div class="container-fluid">

<h2 class="m-0 mb-4 text-primary"><a href="?do=settings">Settings</a></h2>
<div id="settings">
     <ul>
         <li><a href="#general"><span>General</span></a></li>
         <li><a href="#suppliers"><span>Suppliers</span></a></li>
         <li><a href="#categories"><span>Categories</span></a></li>
         <li><a href="#types">Perfume Types</a></li>
         <li><a href="#print"><span>Printing</span></a></li>
         <li><a href="#users"><span>Users</span></a></li>
         <li><a href="#customers"><span>Customers</span></a></li>
         <li><a href="#brand"><span>My Brand</span></a></li>
         <li><a href="#maintenance"><span>Maintenance</span></a></li>
         <!-- <li><a href="#pvmaker">PV Maker</a></li> -->
        <li><a href="pages/about.php"><span>About</span></a></li>
     </ul>
     <div id="general">
     <form id="form" name="form" method="post" enctype="multipart/form-data" action="?do=settings&update=general#general">
     <table width="100%" border="0">
        <tr>
          <td colspan="4"><?php echo $msg; ?></td>
          </tr>
        <tr>
          <td width="10%" height="29">Currency:</td>
          <td colspan="2"><input name="currency" type="text" class="form-control" id="currency" value="<?php echo utf8_encode($settings['currency']);?>"/></td>
          <td width="74%">&nbsp;</td>
          </tr>
        <tr>
          <td height="28"><a href="#" rel="tipsy" title="If enabled, ingredients in formula view will be grouped by type. eg: Top,Heart,Base notes">Group Formula:</a></td>
          <td colspan="2"><input name="grp_formula" type="checkbox" id="grp_formula" value="1" <?php if($settings['grp_formula'] == '1'){ ?> checked="checked" <?php } ?>/></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="If enabled, PV will query PubChem to fetch ingredient data. Please note, the CAS number of the ingredient will be send to the PubChem servers.">Use PubChem:</a></td>
          <td colspan="2"><input name="pubChem" type="checkbox" id="pubChem" value="1" <?php if($settings['pubChem'] == '1'){ ?> checked="checked" <?php } ?>/></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="Auto check for new PV version. If enabled, your ip, current PV version and browser details will be send to our servers but will not be stored.">Check for updates:</a></td>
          <td colspan="2"><input name="chkVersion" type="checkbox" id="chkVersion" value="1" <?php if($settings['chkVersion'] == '1'){ ?> checked="checked" <?php } ?>/></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="If enabled, formula will display the chemical names of ingredients, where available, instead of the commercial name">Chem. names</a></td>
          <td colspan="2"><input name="chem_vs_brand" type="checkbox" id="chem_vs_brand" value="1" <?php if($settings['chem_vs_brand'] == '1'){ ?> checked="checked" <?php } ?>/></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4">&nbsp;</td>
          </tr>
        <tr>
          <td colspan="3"><h4 class="m-0 mb-4 text-primary">Pyramid View</h4>
            <hr></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Top notes:</td>
          <td width="9%"><input name="top_n" type="text" class="form-control" id="top_n" value="<?php echo $settings['top_n'];?>"/></td>
          <td width="10%">%</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Heart notes:</td>
          <td><input name="heart_n" type="text" class="form-control" id="heart_n" value="<?php echo $settings['heart_n'];?>"/></td>
          <td>%</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Base notes:</td>
          <td><input name="base_n" type="text" class="form-control" id="base_n" value="<?php echo $settings['base_n'];?>"/></td>
          <td>%</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2">&nbsp;</td>
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
       <form id="form" name="form" method="post" action="?do=settings&update=suppliers#suppliers">
      <table width="100%" border="0" class="table table-striped table-sm">
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
                      <td data-name="name" class="name" data-type="text" align="center" data-pk="'.$sup['id'].'">'.$sup['name'].'</td>
					  <td data-name="notes" class="notes" data-type="text" align="center" data-pk="'.$sup['id'].'">'.$sup['notes'].'</td>
                      <td align="center"><a href="?do=settings&action=delete&sup_id='.$sup['id'].'#suppliers" onclick="return confirm(\'Delete supplier '.$sup['name'].'?\');" class="fas fa-trash"></a></td>
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
       <form id="form" name="form" method="post" action="?do=settings&update=categories#categories">
            <table width="100%" border="0" class="table table-striped table-sm">
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
                <table class="table table-bordered" width="100%" cellspacing="0">
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
                      <td data-name="name" class="name" data-type="text" align="center" data-pk="'.$cat['id'].'">'.$cat['name'].'</td>
					  <td width="60%" data-name="notes" class="notes" data-type="text" align="left" data-pk="'.$cat['id'].'"align="left">'.wordwrap($cat['notes'], 150, "<br />\n").'</td>
                      <td align="center"><a href="?do=settings&action=delete&cat_id='.$cat['id'].'#categories" onclick="return confirm(\'Delete category '.$cat['name'].'?\');" class="fas fa-trash"></a></td>
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
     
          <div id="types">
     <form id="form" name="form" method="post" enctype="multipart/form-data" action="?do=settings&update=types#types">
     <table width="100%" border="0">
        <tr>
          <td colspan="4"><?php echo $msg; ?></td>
          </tr>
        <tr>
          <td colspan="3"><h4 class="m-0 mb-4 text-primary">&nbsp;</h4></td>
          <td width="77%" rowspan="9"><div class="bg-ptypes-image"></div></td>
        </tr>
        <tr>
          <td width="6%">EDP:</td>
          <td width="10%"><input name="edp" type="text" class="form-control" id="edp" value="<?php echo $settings['EDP'];?>"/></td>
          <td width="7%">%</td>
          </tr>
        <tr>
          <td>EDT:</td>
          <td><input name="edt" type="text" class="form-control" id="edt" value="<?php echo $settings['EDT'];?>"/></td>
          <td>%</td>
          </tr>
        <tr>
          <td>EDC:</td>
          <td><input name="edc" type="text" class="form-control" id="edc" value="<?php echo $settings['EDC'];?>"/></td>
          <td>%</td>
          </tr>
        <tr>
          <td>Parfum:</td>
          <td><input name="parfum" type="text" class="form-control" id="parfum" value="<?php echo $settings['Parfum'];?>"/></td>
          <td>%</td>
          </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td><input type="submit" name="button2" id="button2" value="Submit" class="btn btn-info"/></td>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2">&nbsp;</td>
          </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="3">&nbsp;</td>
          </tr>
      </table>
     </form>
	 </div>
      
    <div id="print">
        <form id="form1" name="form1" method="post" action="?do=settings&update=printer#print">
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
              <option value="62 --red">62 RED mm</option>
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

<div id="users">
       <form action="?do=settings&update=users#users" method="post" enctype="multipart/form-data" name="form" id="form">
       <table width="100%" border="0">
  <tr>
    <td width="16%"><input name="username" placeholder="Username" type="text" class="form-control" id="username" /></td>
    <td width="1%">&nbsp;</td>
    <td width="16%"><input name="password" placeholder="Password" type="password" class="form-control" id="password" /></td>
    <td width="1%">&nbsp;</td>
    <td width="16%"><input name="fullName" placeholder="Full Name" type="text" class="form-control" id="fullName" /></td>
    <td width="1%">&nbsp;</td>
    <td width="16%"><input name="email" placeholder="Email" type="text" class="form-control" id="email" /></td>
    <td width="1%">&nbsp;</td>
    <td width="16%"><input type="submit" name="add_user" id="add_user" value="Add" class="btn btn-info" /></td>
  </tr>
  <tr>
    <td colspan="9">&nbsp;</td>
    </tr>
  <tr>
    <td colspan="9"><?php echo $msg; ?></td>
    </tr>
</table>

              <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder">
                    </tr>
                    <tr>
                      <th>Username</th>
                      <th>Full Name</th>
                      <th>Email</th>
                      <th>Actions</th>
                    </tr>
                </thead>
                  <tbody id="users">
                  <?php while ($users = mysqli_fetch_array($users_q)) {
					  echo'
                    <tr>
					  <td align="center">'.$users['username'].'</td>
					  <td align="center">'.$users['fullName'].'</td>
					  <td align="center">'.$users['email'].'</td>

                      <td align="center"><a href="pages/editUser.php?id='.$users['id'].'" class="fas fa-edit popup-link"></a> <a href="?do=settings&action=delete&user_id='.$users['id'].'#users" onclick="return confirm(\'Delete user '.$users['fullName'].'?\');" class="fas fa-trash"></a></td>
					</tr>';
				  		}
                    ?>
                    </tr>
                  </tbody>
          </table>
        </form>
     </div> 

     <div id="customers">
       <form action="?do=settings&update=customers#customers" method="post" enctype="multipart/form-data" name="form" id="form">
       <table width="100%" border="0">
  <tr>
    <td width="16%"><input name="cname" placeholder="Name" type="text" class="form-control" id="cname" /></td>
    <td width="1%">&nbsp;</td>
    <td width="16%"><input name="caddress" placeholder="Address" type="text" class="form-control" id="caddress" /></td>
    <td width="1%">&nbsp;</td>
    <td width="16%"><input name="cemail" placeholder="Email" type="text" class="form-control" id="cemail" /></td>
    <td width="1%">&nbsp;</td>
    <td width="16%"><input name="cweb" placeholder="Web Site" type="text" class="form-control" id="cweb" /></td>
    <td width="1%">&nbsp;</td>
    <td width="16%"><input type="submit" name="add_customer" id="add_customer" value="Add Customer" class="btn btn-info" /></td>
  </tr>
  <tr>
    <td colspan="9">&nbsp;</td>
    </tr>
  <tr>
    <td colspan="9"><?php echo $msg; ?></td>
    </tr>
</table>
              <table class="table table-bordered" id="tdData" width="100%" cellspacing="0">
                  <thead>
                    <tr class="noBorder">
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th>Address</th>
                      <th>Email</th>
                      <th>Web Site</th>
                      <th>Actions</th>
                    </tr>
                </thead>
                  <tbody id="customers">
                  <?php while ($customers = mysqli_fetch_array($customers_q)) {
					  echo'
                    <tr>
					  <td align="center">'.$customers['name'].'</td>
					  <td align="center">'.$customers['address'].'</td>
					  <td align="center">'.$customers['email'].'</td>
					  <td align="center">'.$customers['web'].'</td>
                      <td align="center"><a href="pages/editCustomer.php?id='.$customers['id'].'" class="fas fa-edit popup-link"></a> <a href="?do=settings&action=delete&customer_id='.$customers['id'].'#customers" onclick="return confirm(\'Delete user '.$customers['name'].'?\');" class="fas fa-trash"></a></td>
					</tr>';
				  		}
                    ?>
                    </tr>
                  </tbody>
          </table>
        </form>
     </div> 
     
     <div id="brand">
       <form action="?do=settings&update=brand#brand" method="post" enctype="multipart/form-data" name="form" id="form">
         <table width="100%" border="0">
           <tr>
             <td colspan="2"><?php echo $msg; ?></td>
            </tr>
           <tr>
             <td width="7%">Brand Name:</td>
             <td width="93%"><input name="brandName" type="text" class="form-control" id="brandName" value="<?php echo $settings['brandName'];?>" /></td>
           </tr>
           <tr>
             <td>Address:</td>
             <td><input name="brandAddress" type="text" class="form-control" id="brandAddress" value="<?php echo $settings['brandAddress'];?>"/></td>
           </tr>
           <tr>
             <td>Email:</td>
             <td><input name="brandEmail" type="text" class="form-control" id="brandEmail" value="<?php echo $settings['brandEmail'];?>" /></td>
           </tr>
           <tr>
             <td>Contact No:</td>
             <td><input name="brandPhone" type="text" class="form-control" id="brandPhone" value="<?php echo $settings['brandPhone'];?>" /></td>
           </tr>
           <tr>
             <td>Logo:</td>
             <td><input type="file" name="brandLogo" id="brandLogo" class="form-control"/></td>
           </tr>
           <tr>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
           </tr>
           <tr>
             <td colspan="2"><input type="submit" name="button3" id="button3" value="Submit" class="btn btn-info"/></td>
            </tr>
         </table>
       </form>
     </div>
     <!--
     <div id="pvmaker">
        <form id="form2" name="form2" method="post" action="?do=settings&update=pvmaker#pvmaker">
        <table width="100%" border="0">
          <tr>
            <td colspan="3"><div id="pvm_r"><?php echo $msg; ?></div></td>
            </tr>
          <tr>
            <td width="9%" height="29"><a href="#" rel="tipsy" title="Enable's integration with the PV Maker device">Enable PV Maker</a></td>
            <td width="9%"><input name="pv_maker" type="checkbox" id="pv_maker" value="1" <?php if($settings['pv_maker'] == '1'){ ?> checked="checked" <?php } ?>/></td>
            <td width="82%">&nbsp;</td>
          </tr>
          <tr>
            <td height="30"><a href="#" rel="tipsy" title="Please enter the IP shown in the device">Host:</a></td>
            <td><input name="pv_maker_host" type="text" class="form-control" id="pv_maker_host" value="<?php echo $settings['pv_maker_host'];?>" /></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="24"><a  href="javascript:initPVM()" onclick="return confirm('Initialize PV Maker?')" rel="tipsy" title="Click here to auto configure the device">Initialize device</a></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="24"><a href="javascript:resetPVM()" onclick="return confirm('Restore PV Maker defaults?')" rel="tipsy" title="Click here to reset device settings to its defaults.">Restore defaults</a></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><input type="submit" name="button4" id="button4" value="Submit" class="btn btn-info"/></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
		</table>
        </form>
     </div>
     -->
<div id="maintenance">
  <table width="100%" border="0">
    <tr>
      <td width="13%">&nbsp;</td>
      <td width="87%">&nbsp;</td>
    </tr>
    <tr>
      <td><ul>
        <li><a href="pages/maintenance.php?do=IFRA" class="popup-link">Import IFRA Library</a></li>
      </ul></td>
      <td><ul>
        <li><a href="https://ifrafragrance.org/safe-use/standards-guidance" target="_blank">IFRA web site</a></li>
      </ul></td>
    </tr>
    <tr>
      <td><ul>
        <li><a href="pages/maintenance.php?do=backupDB">Backup DB</a></li>
      </ul></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><ul>
        <li><a href="pages/maintenance.php?do=restoreDB" class="popup-link">Restore DB</a></li>
      </ul></td>
      <td>&nbsp;</td>
    </tr>
  </table>
</div>

  </div>
      </div>
</div>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
 
  $('#cat_data').editable({
  container: 'body',
  selector: 'td.name',
  url: "pages/update_data.php?settings=cat",
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
  selector: 'td.notes',
  url: "pages/update_data.php?settings=cat",
  title: 'Description',
  type: "POST",
  dataType: 'json',
  validate: function(value){
  }
 });
 
 
   $('#sup_data').editable({
  container: 'body',
  selector: 'td.name',
  url: "pages/update_data.php?settings=sup",
  title: 'Supplier',
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
  selector: 'td.notes',
  url: "pages/update_data.php?settings=sup",
  title: 'Description',
  type: "POST",
  dataType: 'json',
  validate: function(value){
  }
 });
  
});

function initPVM() {	  
$.ajax({ 
    url: 'pages/pvm.php', 
	type: 'GET',
    data: {
		setup: '1',
		ip: '192.168.1.83'
		},
	dataType: 'html',
    success: function (data) {
		//location.reload();
	  	$('#pvm_r').html(data);
    }
  });
};

function resetPVM() {	  
$.ajax({ 
    url: 'pages/pvm.php', 
	type: 'GET',
    data: {
		setup: 'rfd'
		},
	dataType: 'html',
    success: function (data) {
		//location.reload();
	  	$('#pvm_r').html(data);
    }
  });
};
</script>

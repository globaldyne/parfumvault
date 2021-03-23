<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<div id="content-wrapper" class="d-flex flex-column">
<?php 
require_once(__ROOT__.'/pages/top.php'); 

if(($_POST) && $_GET['update'] == 'printer'){
	$label_printer_addr = mysqli_real_escape_string($conn, $_POST['label_printer_addr']);
	$label_printer_model = mysqli_real_escape_string($conn, $_POST['label_printer_model']);
	$label_printer_size = mysqli_real_escape_string($conn, $_POST['label_printer_size']);
	$label_printer_font_size = mysqli_real_escape_string($conn, $_POST['label_printer_font_size']);

	if(mysqli_query($conn, "UPDATE settings SET label_printer_addr='$label_printer_addr', label_printer_model='$label_printer_model', label_printer_size='$label_printer_size', label_printer_font_size='$label_printer_font_size'")){
		$msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Printer settings updated!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error updating settings!</div>';
	}
	require(__ROOT__.'/inc/settings.php');


	

//ADD CATEGORY
}elseif($_POST['category'] && $_GET['update'] == 'categories'){
	$cat = mysqli_real_escape_string($conn, $_POST['category']);
	$notes = mysqli_real_escape_string($conn, $_POST['cat_notes']);
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingCategory WHERE name = '$cat'"))){
		$msg='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$cat.' already exists!</div>';
	}elseif(mysqli_query($conn, "INSERT INTO ingCategory (name,notes) VALUES ('$cat', '$notes')")){
		
		$msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Category added!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error adding category</div>';
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
	$qStep = mysqli_real_escape_string($conn, $_POST['qStep']);
	$defCatClass = mysqli_real_escape_string($conn, $_POST['defCatClass']);
	$pubchem_view = mysqli_real_escape_string($conn, $_POST['pubchem_view']);


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
	
	if(mysqli_query($conn, "UPDATE settings SET currency = '$currency', top_n = '$top_n', heart_n = '$heart_n', base_n = '$base_n', chem_vs_brand = '$chem_vs_brand', grp_formula = '$grp_formula', pubChem='$pubChem', chkVersion='$chkVersion', pv_maker='$pv_maker', qStep = '$qStep', defCatClass = '$defCatClass', pubchem_view = '$pubchem_view'")){
		$msg = '<div class="alert alert-success alert-dismissible">Settings updated!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">An error occured.</div>';	
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
		$msg='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>Password must be at least 5 chars long!</div>';
	}elseif(mysqli_num_rows(mysqli_query($conn, "SELECT username FROM users WHERE username = '$username' OR email = '$email' "))){
		$msg='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$username.' already exists!</div>';
	}elseif(mysqli_query($conn, "INSERT INTO users (username,password,fullName,email) VALUES ('$username', PASSWORD('$password'), '$fullName', '$email')")){
		
		$msg = '<div class="alert alert-success alert-dismissible">User added!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">Error adding user. ('.mysqli_error($conn).')</div>';
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
	

//PV ONLINE
}elseif($_GET['update'] == 'pvonline' && $_POST['pv_online_email'] && $_POST['pv_online_pass']){
	$pv_online_email = mysqli_real_escape_string($conn, $_POST['pv_online_email']);
	$pv_online_pass = mysqli_real_escape_string($conn, $_POST['pv_online_pass']);
	
	$valAcc = pvOnlineValAcc($pvOnlineAPI, $pv_online_email, $pv_online_pass, $ver);

    if($valAcc == 'Failed'){
       $msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Invalid credentials or your PV Online account is inactive.</div>';
	}else{
		if(mysqli_query($conn, "INSERT pv_online (id,email,password) VALUES ('1','$pv_online_email', '$pv_online_pass') ON DUPLICATE KEY UPDATE id = '1', email = '$pv_online_email', password = '$pv_online_pass'")){
			$msg = '<div class="alert alert-success alert-dismissible">PV Online details updated!</div>';
		}else{
			$msg = '<div class="alert alert-danger alert-dismissible">Error updating PV Online info: ('.mysqli_error($conn).')</div>';
		}
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
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users")) <= 1){
		$msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error, at least one user needs to exist.</div>';
	}else{
		if(mysqli_query($conn, "DELETE FROM users WHERE id = '$user_id'")){
			$msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>User deleted!</div>';
		}else{
			$msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error deleting user.</div>';
		}
	}

}												   
 
$cat_q = mysqli_query($conn, "SELECT * FROM ingCategory ORDER BY name ASC");
$users_q = mysqli_query($conn, "SELECT * FROM users ORDER BY username ASC");
$pv_online = mysqli_fetch_array(mysqli_query($conn, "SELECT email FROM pv_online"));
$cats_q = mysqli_query($conn, "SELECT id,name,description,type FROM IFRACategories ORDER BY id ASC");

while($cats_res = mysqli_fetch_array($cats_q)){
    $cats[] = $cats_res;
}
require(__ROOT__.'/inc/settings.php');

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
         <li><a href="#categories"><span>Ingredient Categories</span></a></li>
         <li><a href="#types">Perfume Types</a></li>
         <li><a href="#print"><span>Printing</span></a></li>
         <li><a href="#users"><span>Users</span></a></li>
         <li><a href="#brand"><span>My Brand</span></a></li>
         <li><a href="#maintenance"><span>Maintenance</span></a></li>
         <li><a href="#pvonline"><span>PV Online</span></a></li>
        <li><a href="pages/about.php"><span>About</span></a></li>
     </ul>
     <div id="general">
     <form id="form" name="form" method="post" enctype="multipart/form-data" action="?do=settings&update=general#general">
     <table width="100%" border="0">
        <tr>
          <td colspan="4"><?php echo $msg; ?></td>
          </tr>
        <tr>
          <td width="9%" height="29">Currency:</td>
          <td colspan="2"><input name="currency" type="text" class="form-control" id="currency" value="<?php echo utf8_encode($settings['currency']);?>"/></td>
          <td width="73%">&nbsp;</td>
          </tr>
        <tr>
          <td height="28"><a href="#" rel="tipsy" title="If enabled, ingredients in formula view will be grouped by type. eg: Top,Heart,Base notes">Group Formula:</a></td>
          <td colspan="2"><input name="grp_formula" type="checkbox" id="grp_formula" value="1" <?php if($settings['grp_formula'] == '1'){ ?> checked="checked" <?php } ?>/></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="If enabled, PV will query PubChem to fetch ingredient data. Please note, the CAS number of the ingredient will be send to the PubChem servers.">Enable PubChem:</a></td>
          <td colspan="2"><input name="pubChem" type="checkbox" id="pubChem" value="1" <?php if($settings['pubChem'] == '1'){ ?> checked="checked" <?php } ?>/></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="Select the image type for PubChem, 2D or 3D. Default: 2D">PubChem view:</a></td>
          <td colspan="2"><select name="pubchem_view" id="pubchem_view" class="form-control">
			  <option value="2d" <?php if($settings['pubchem_view']=="2d") echo 'selected="selected"'; ?> >2D</option>
			  <option value="3d" <?php if($settings['pubchem_view']=="3d") echo 'selected="selected"'; ?> >3D</option>
          </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="Auto check for new PV version. If enabled, your ip, current PV version and browser info will be send to our servers and or GitHub servers. Please make sure you have read our and GitHub's T&C and Privacy Policy before enable this.">Check for updates:</a></td>
          <td colspan="3"><input name="chkVersion" type="checkbox" id="chkVersion" value="1" <?php if($settings['chkVersion'] == '1'){ ?> checked="checked" <?php } ?>/>
            <?php require('privacy_note.php');?></td>
          </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="Defines the decimal in formula quantity.">Quantity Decimal:</a></td>
          <td colspan="2"><select name="qStep" id="qStep" class="form-control">
			  <option value="1" <?php if($settings['qStep']=="1") echo 'selected="selected"'; ?> >0.0</option>
			  <option value="2" <?php if($settings['qStep']=="2") echo 'selected="selected"'; ?> >0.00</option>
			  <option value="3" <?php if($settings['qStep']=="3") echo 'selected="selected"'; ?> >0.000</option>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="Select the default category class. This will be used to calculate limits in formulas">Default Category:</a></td>
          <td colspan="2"><select name="defCatClass" id="defCatClass" class="form-control">
		<?php foreach ($cats as $IFRACategories) {?>
				<option value="cat<?php echo $IFRACategories['name'];?>" <?php echo ($settings['defCatClass']=='cat'.$IFRACategories['name'])?"selected=\"selected\"":""; ?>><?php echo 'Cat '.$IFRACategories['name'];?></option>
		  <?php	}	?>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="32"><a href="#" rel="tipsy" title="If enabled, formula will display the chemical names of ingredients, where available, instead of the commercial name">Chemical names</a>:</td>
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
          <td width="17%"><input name="top_n" type="text" class="form-control" id="top_n" value="<?php echo $settings['top_n'];?>"/></td>
          <td width="1%">%</td>
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
                <table class="table table-bordered" id="tdDataCat" width="100%" cellspacing="0">
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
					  <td width="60%" data-name="notes" class="notes" data-type="text" align="center" data-pk="'.$cat['id'].'">'.wordwrap($cat['notes'], 150, "<br />\n").'</td>
                      <td align="center"><a href="?do=settings&action=delete&cat_id='.$cat['id'].'#categories" onclick="return confirm(\'Delete category '.$cat['name'].'?\');" class="fas fa-trash"></a></td>
					</tr>';
				  		}
                    ?>
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
			  <option value="QL-500" <?php if($settings['label_printer_model']=="QL-500") echo 'selected="selected"'; ?> >QL-500</option>
			  <option value="QL-550" <?php if($settings['label_printer_model']=="QL-550") echo 'selected="selected"'; ?> >QL-5500</option>
			  <option value="QL-560" <?php if($settings['label_printer_model']=="QL-560") echo 'selected="selected"'; ?> >QL-560</option>
			  <option value="QL-570" <?php if($settings['label_printer_model']=="QL-570") echo 'selected="selected"'; ?> >QL-570</option>
			  <option value="QL-850" <?php if($settings['label_printer_model']=="QL-850") echo 'selected="selected"'; ?> >QL-850</option>
			  <option value="QL-650TD" <?php if($settings['label_printer_model']=="QL-650TD") echo 'selected="selected"'; ?> >QL-650TD</option>
			  <option value="QL-700" <?php if($settings['label_printer_model']=="QL-700") echo 'selected="selected"'; ?> >QL-700</option>
			  <option value="QL-710W" <?php if($settings['label_printer_model']=="QL-710W") echo 'selected="selected"'; ?> >QL-710W</option>
			  <option value="QL-720NW" <?php if($settings['label_printer_model']=="QL-720NW") echo 'selected="selected"'; ?> >QL-720NW</option>
			  <option value="QL-800" <?php if($settings['label_printer_model']=="QL-800") echo 'selected="selected"'; ?> >QL-800</option>
			  <option value="QL-810W" <?php if($settings['label_printer_model']=="QL-810W") echo 'selected="selected"'; ?> >QL-810W</option>
			  <option value="QL-820NB" <?php if($settings['label_printer_model']=="QL-820NB") echo 'selected="selected"'; ?> >QL-820NB</option>
			  <option value="QL-1050" <?php if($settings['label_printer_model']=="QL-1050") echo 'selected="selected"'; ?> >QL-1050</option>
			  <option value="QL-1060N" <?php if($settings['label_printer_model']=="QL-1060N") echo 'selected="selected"'; ?> >QL-1060N</option>
            </select>
            </td>
            <td></td>
            <td><a href="#" class="fas fa-question-circle" rel="tipsy" title="Your Brother printer model"></a></td>
          </tr>
          <tr>
            <td>Label Size:</td>
            <td>
            <select name="label_printer_size" id="label_printer_size" class="form-control">   
			  <option value="12" <?php if($settings['label_printer_size']=="12") echo 'selected="selected"'; ?> >12 mm</option>
              <option value="29" <?php if($settings['label_printer_size']=="29") echo 'selected="selected"'; ?> >29 mm</option>
			  <option value="38" <?php if($settings['label_printer_size']=="38") echo 'selected="selected"'; ?> >38 mm</option>
			  <option value="50" <?php if($settings['label_printer_size']=="50") echo 'selected="selected"'; ?> >50 mm</option>
			  <option value="62" <?php if($settings['label_printer_size']=="62") echo 'selected="selected"'; ?> >62 mm</option>
			  <option value="62 --red" <?php if($settings['label_printer_size']=="62 --red") echo 'selected="selected"'; ?> >62 mm (RED)</option>
			  <option value="102" <?php if($settings['label_printer_size']=="102") echo 'selected="selected"'; ?> >102 mm</option>
			  <option value="17x54" <?php if($settings['label_printer_size']=="17x54") echo 'selected="selected"'; ?> >17x54 mm</option>
			  <option value="17x87" <?php if($settings['label_printer_size']=="17x87") echo 'selected="selected"'; ?> >17x87 mm</option>
			  <option value="23x23" <?php if($settings['label_printer_size']=="23x23") echo 'selected="selected"'; ?> >23x23 mm</option>
			  <option value="29x42" <?php if($settings['label_printer_size']=="29x42") echo 'selected="selected"'; ?> >29x42 mm</option>
			  <option value="29x90" <?php if($settings['label_printer_size']=="29x90") echo 'selected="selected"'; ?> >29x90 mm</option>
			  <option value="39x90" <?php if($settings['label_printer_size']=="39x90") echo 'selected="selected"'; ?> >39x90 mm</option>
			  <option value="39x48" <?php if($settings['label_printer_size']=="39x48") echo 'selected="selected"'; ?> >39x48 mm</option>
			  <option value="52x29" <?php if($settings['label_printer_size']=="52x29") echo 'selected="selected"'; ?> >52x29 mm</option>
			  <option value="62x29" <?php if($settings['label_printer_size']=="62x29") echo 'selected="selected"'; ?> >62x29 mm</option>
			  <option value="62x100" <?php if($settings['label_printer_size']=="62x100") echo 'selected="selected"'; ?> >62x100 mm</option>
			  <option value="102x51" <?php if($settings['label_printer_size']=="102x51") echo 'selected="selected"'; ?> >102x51 mm</option>
			  <option value="d12" <?php if($settings['label_printer_size']=="d12") echo 'selected="selected"'; ?> >D12</option>
			  <option value="d24" <?php if($settings['label_printer_size']=="d24") echo 'selected="selected"'; ?> >D24</option>
			  <option value="d58" <?php if($settings['label_printer_size']=="d58") echo 'selected="selected"'; ?> >D58</option>
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

              <table class="table table-bordered" id="tdDataUsers" width="100%" cellspacing="0">
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
                  <?php while ($users = mysqli_fetch_array($users_q)) { ?>
                    <tr>
					  <td align="center"><?php echo $users['username'];?></td>
					  <td align="center"><?php echo $users['fullName'];?></td>
					  <td align="center"><?php echo $users['email'];?></td>
                      <td align="center"><a href="pages/editUser.php?id=<?php echo $users['id']; ?>" class="fas fa-edit popup-link"></a> <a href="?do=settings&action=delete&user_id=<?php echo $users['id'];?>#users" onclick="return confirm('Delete user <?php echo $users['username'];?>?')" class="fas fa-trash"></a></td>
					</tr>
				  		<?php } ?>
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
     
     <div id="pvonline">
        <form id="form" name="form" method="post" action="?do=settings&update=pvonline#pvonline">
        <table width="100%" border="0">
          <tr>
            <td colspan="3"><div id="pvm_r"><?php echo $msg; ?></div></td>
            </tr>
          <tr>
            <td width="9%" height="30"><a href="#" rel="tipsy" title="Please enter your PV Online email">Email:</a></td>
            <td width="9%"><input name="pv_online_email" type="text" class="form-control" id="pv_online_email" value="<?php echo $pv_online['email'];?>" /></td>
            <td width="82%">&nbsp;</td>
          </tr>
          <tr>
            <td height="24"><a href="#" rel="tipsy" title="Your PV Online password.">Password:</a></td>
            <td><input name="pv_online_pass" type="password" class="form-control" id="pv_online_pass" /></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="29"><a href="https://online.jbparfum.com/register.php" target="_blank">Create an account</a></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="31"><a href="https://online.jbparfum.com/forgotpass.php" target="_blank">Forgot Password?</a></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><?php require('privacy_note.php');?></td>
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

</script>

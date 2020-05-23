<?php if (!defined('pvault_panel')){ die('Not Found');}?>

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
	
	if(mysqli_query($conn, "UPDATE settings SET currency = '$currency', top_n = '$top_n', heart_n = '$heart_n', base_n = '$base_n'")){
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
		$msg='<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>Error: </strong>'.$username.' already exists!
		</div>';
	}elseif(mysqli_query($conn, "INSERT INTO users (username,password,fullName,email) VALUES ('$username', PASSWORD('$password'), '$fullName', '$email')")){
		
		$msg = '<div class="alert alert-success alert-dismissible">User added!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">Error adding user. ('.mysqli_error($conn).')</div>';
	}
	

//DELETE ACTIONS
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

}elseif($_GET['action'] == 'delete' && $_GET['user_id']){
	$user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
	if(mysqli_query($conn, "DELETE FROM users WHERE id = '$user_id'")){
		$msg = '<div class="alert alert-success alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>User deleted!</div>';
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Error deleting user.</div>';
	}



}												   
 
$cat_q = mysqli_query($conn, "SELECT * FROM ingCategory ORDER BY name ASC");
$sup_q = mysqli_query($conn, "SELECT * FROM ingSuppliers ORDER BY name ASC");
$users_q = mysqli_query($conn, "SELECT * FROM users ORDER BY username ASC");

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

<h2 class="m-0 mb-4 text-primary">Settings</h2>
<div id="settings">
     <ul>
         <li><a href="#general"><span>General</span></a></li>
         <li><a href="#suppliers"><span>Suppliers</span></a></li>
         <li><a href="#categories"><span>Categories</span></a></li>
         <li><a href="#print"><span>Printing</span></a></li>
         <li><a href="#users"><span>Users</span></a></li>
         <li><a href="#maintenance"><span>Maintenance</span></a></li>
        <li><a href="pages/about.php"><span>About</span></a></li>
     </ul>
     <div id="general">
     <form id="form" name="form" method="post" enctype="multipart/form-data" action="/?do=settings&update=general#general">
     <table width="100%" border="0">
        <tr>
          <td colspan="4"><?php echo $msg; ?></td>
          </tr>
        <tr>
          <td width="6%">Currency:</td>
          <td colspan="2"><input name="currency" type="text" class="form-control" id="currency" value="<?php echo utf8_encode($settings['currency']);?>"/></td>
          <td width="77%">&nbsp;</td>
          </tr>
        <tr>
          <td colspan="4">&nbsp;</td>
          </tr>
        <tr>
          <td colspan="3"><h4 class="m-0 mb-4 text-primary">Pyramid View</h4></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Top notes:</td>
          <td width="10%"><input name="top_n" type="text" class="form-control" id="top_n" value="<?php echo $settings['top_n'];?>"/></td>
          <td width="7%">%</td>
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
       <form id="form" name="form" method="post" action="/?do=settings&update=suppliers#suppliers">
      <table width="100%" border="0"  class="table table-striped table-sm">
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
                      <td data-name="name" class="name" data-type="text" align="center" data-pk="'.$cat['id'].'"><a href="#">'.$cat['name'].'</a></td>
					  <td width="60%" data-name="notes" class="notes" data-type="text" align="left" data-pk="'.$cat['id'].'"align="left"><a href="#">'.wordwrap($cat['notes'], 150, "<br />\n").'</a></td>
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

     <div id="users">
       <form action="/?do=settings&update=users#users" method="post" enctype="multipart/form-data" name="form" id="form">
       <table width="100%" border="0">
  <tr>
    <td width="16%"><input name="username" placeholder="Username" type="text" class="form-control" id="username" /></td>
    <td width="1%">&nbsp;</td>
    <td width="16%"><input name="password" placeholder="Password" type="password" class="form-control" id="password" /></td>
    <td width="1%">&nbsp;</td>
    <td width="16%"><input name="fullName" placeholder="Full Name" type="text" class="form-control" id="Full Name" /></td>
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

                      <td align="center"><a href="pages/editUser.php?id='.$users['id'].'" class="fas fa-edit popup-link"></a> <a href="/?do=settings&action=delete&user_id='.$users['id'].'#users" onclick="return confirm(\'Delete user '.$users['fullName'].'?\');" class="fas fa-trash"></a></td>
					</tr>';
				  		}
                    ?>
                    </tr>
                  </tbody>
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
        <li><a href="/pages/maintenance.php?do=IFRA" class="popup-link">Import IFRA Library</a></li>
      </ul></td>
      <td><ul>
        <li><a href="https://ifrafragrance.org/safe-use/standards-guidance" target="_blank">IFRA web site</a></li>
      </ul></td>
    </tr>
    <tr>
      <td><ul>
        <li><a href="/pages/maintenance.php?do=backupDB">Backup DB</a></li>
      </ul></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><ul>
        <li><a href="/pages/maintenance.php?do=restoreDB" class="popup-link">Restore DB</a></li>
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
  selector: 'td.notes',
  url: "/pages/update_data.php?settings=cat",
  title: 'Description',
  type: "POST",
  dataType: 'json',
  validate: function(value){
  }
 });
 
 
   $('#sup_data').editable({
  container: 'body',
  selector: 'td.name',
  url: "/pages/update_data.php?settings=sup",
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
  url: "/pages/update_data.php?settings=sup",
  title: 'Description',
  type: "POST",
  dataType: 'json',
  validate: function(value){
  }
 });

 
  
});
</script>
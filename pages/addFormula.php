<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<div id="content-wrapper" class="d-flex flex-column">
<?php require_once('pages/top.php'); ?>
<div class="container-fluid">
<?php 

if($_POST['fname']){
	$fname = mysqli_real_escape_string($conn, $_POST['fname']);
	$notes = mysqli_real_escape_string($conn, $_POST['notes']);
	$profile = mysqli_real_escape_string($conn, $_POST['profile']);
	$fid = base64_encode($fname);
	
if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE fid = '$fid'"))){
		$msg='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$fname.' already exists! Click <a href="?do=Formula&name='.$fname.'">here</a> to view/edit!
		</div>';
	}else{
		$q = mysqli_query($conn, "INSERT INTO formulasMetaData (fid, name, notes, profile, image) VALUES ('$fid', '$fname', '$notes', '$profile', '$def_app_img')");
			if($q){
				$msg='<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong><a href="?do=Formula&name='.$fname.'">'.$fname.'</a></strong> added!</div>';
			}else{
				echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> '.mysqli_error($conn).'</div>';
			}
	}

}
?>
<h2 class="m-0 mb-4 text-primary">New Formula</h2>
<?php echo $msg;?>
        </div>
<table width="94%" border="0" align="center">
        <tr>
          <td>
          <div class="form-group">
            <form action="?do=addFormula" method="post" enctype="multipart/form-data" name="add_formula" target="_self" id="add_formula">  
				<div class="table-responsive">  
                               <table width="764" class="table table-bordered" id="dynamic_field">  
                                    <tr>
                                      <td>Formula name</td>
                                      <td><input name="fname" type="text" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                      <td>Profile:</td>
                                      <td>
                                      <select name="profile" id="profile" class="form-control">
                                        <option value="oriental">Oriental</option>
                                        <option value="woody">Woody</option>
                                        <option value="floral">Floral</option>
                                        <option value="fresh">Fresh</option>
                                        <option value="other">Other</option>
                                      </select>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td>Notes:</td>
                                      <td><textarea name="notes" id="notes" cols="45" rows="5" class="form-control"></textarea></td>
                                    </tr>  
                               </table>  
                               <input type="submit" name="submit" id="submit" class="btn btn-info" value="Submit" />  
              </div>  
     		</form>  
          </div></td>
       	</tr>
</table>
      </div>
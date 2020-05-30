<?php
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');

if($_GET['id']){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	$info = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE id = '$id'"));
?>
<link href="../css/sb-admin-2.css" rel="stylesheet">
<link href="../css/bootstrap-select.min.css" rel="stylesheet">
<link href="../css/bootstrap-editable.css" rel="stylesheet">

<script src="../js/jquery/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
  
<link href="../css/bootstrap.min.css" rel="stylesheet">
  
<script src="../js/bootstrap-select.js"></script>
<script src="../js/bootstrap-editable.js"></script>


<style>
.form-inline .form-control {
    display: inline-block;
    width: 500px;
    vertical-align: middle;
}

</style>



<table class="table table-bordered" id="formula_metadata" cellspacing="0">
  <tr>
    <td colspan="2"><h1 class="badge-primary"><?php echo $info['name'];?></h1></td>
  </tr>
  <tr>
    <td width="20%">Created:</td>
    <td width="80%"><?php echo $info['created'];?></td>
  </tr>
  <tr>
    <td>Profile:</td>
    <td><a href="#" id="profile" data-type="select" data-pk="profile" data-title="<?php echo $info['profile'];?>"></a></td>
  </tr>
  <tr>
    <td>Sex:</td>
    <td><a href="#" id="sex" data-type="select" data-pk="sex" data-title="<?php echo $info['sex'];?>"></a></td>
  </tr>
  <tr>
    <td>Picture:</td>
    <td data-name="notes" class="notes" data-type="textarea" align="left" data-pk="notes"><form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
      <input type="file" name="fileField" id="fileField" />
    </form></td>
  </tr>
  <tr>
    <td>Notes:</td>
    <td data-name="notes" class="notes" data-type="textarea" align="left" data-pk="notes"><?php echo $info['notes'];?></td>
  </tr>
</table>

<?php
}else{
	
	header('Location: /');
}
?>

<script type="text/javascript" language="javascript" >
$(document).ready(function(){
 
  $('#formula_metadata').editable({
  container: 'body',
  selector: 'td.notes',
  url: "/pages/update_data.php?formulaMeta=<?php echo $info['name']; ?>",
  title: 'Notes',
  type: "POST",
  mode: 'inline',
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg; 
    },

 });
  
  $('#profile').editable({
	value: "<?php echo $info['profile'];?>",
  	title: 'Profile',
  	url: "/pages/update_data.php?formulaMeta=<?php echo $info['name']; ?>",
    source: [
             {value: 'oriental', text: 'Oriental'},
             {value: 'woody', text: 'Woody'},
             {value: 'floral', text: 'Floral'},
             {value: 'fresh', text: 'Fresh'},
             {value: 'other', text: 'Other'},
          ]
    });
  
    $('#sex').editable({
	value: "<?php echo $info['sex'];?>",
  	url: "/pages/update_data.php?formulaMeta=<?php echo $info['name']; ?>",
    source: [
             {value: 'unisex', text: 'Unisex'},
             {value: 'men', text: 'Men'},
             {value: 'women', text: 'Women'},
          ]
    });
  })
</script>
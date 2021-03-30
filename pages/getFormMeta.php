<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if($_GET['id']){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	$info = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE id = '$id'"));

if($_FILES["file"]["tmp_name"]){
	$filename=$_FILES["file"]["tmp_name"];    

	if(empty($err)==true){
		 if (!file_exists("../uploads/formulas")) {
    		 mkdir("../uploads/formulas", 0740, true);
	  	 }
	  }
	  
	  $maxDim = 400;
	  list($width, $height, $type, $attr) = getimagesize( $filename );
	  if ($width > $maxDim || $height > $maxDim) {
    	$targetfilename = $filename;
    	$ratio = $width/$height;
    	if( $ratio > 1) {
        	$new_width = $maxDim;
        	$new_height = $maxDim/$ratio;
    	}else {
        	$new_width = $maxDim*$ratio;
        	$new_height = $maxDim;
    	}
    	$src = imagecreatefromstring( file_get_contents( $filename ) );
    	$dst = imagecreatetruecolor( $new_width, $new_height );
    	imagecopyresampled( $dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
    	imagedestroy( $src );
    	imagepng( $dst, $targetfilename ); // adjust format as needed
	 
		//if($_FILES["file"]["size"] > 0){
			move_uploaded_file($targetfilename,"../uploads/formulas/".base64_encode($targetfilename));
			$image = "uploads/formulas/".base64_encode($targetfilename);
			if(mysqli_query($conn, "UPDATE formulasMetaData SET image = '$image' WHERE id = '$id'")){
				$msg = '<div class="alert alert-success alert-dismissible">Image uploaded!</div>';
			}else{
				$msg = '<div class="alert alert-danger alert-dismissible">Error uploading the image</div>';
			}
		imagedestroy( $dst );		
			
		}
	 }

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
    <td colspan="2"><div id="msg"><?php echo $msg; ?></div></td>
  </tr>
  <tr>
    <td width="20%">Formula Name:</td>
    <td data-name="name" class="name" data-type="text" align="left" data-pk="name" width="80%"><?php echo $info['name'];?></td>
  </tr>
  <tr>
    <td>Product Name:</td>
    <td data-name="product_name" class="product_name" data-type="text" align="left" data-pk="product_name"><?php echo $info['product_name'];?></td>
  </tr>
  <tr>
    <td>Protected:</td>
    <td><input name="isProtected" type="checkbox" id="isProtected" value="1" <?php if($info['isProtected'] == '1'){; ?> checked="checked"  <?php } ?>/></td>
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
    <td><form action="?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data" name="form1" id="form1">
      <input type="file" name="file" id="file" />
      <input type="submit" name="button" id="button" value="Submit" />
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
  selector: 'td.name',
  url: "update_data.php?rename=<?php echo $info['name']; ?>",
  title: 'Name',
  type: "POST",
  mode: 'inline',
  dataType: 'json',
      success: function(response) {				
	  	$('#msg').html(response);        
    },

 });
  
  $('#formula_metadata').editable({
  container: 'body',
  selector: 'td.notes',
  url: "update_data.php?formulaMeta=<?php echo $info['name']; ?>",
  title: 'Notes',
  type: "POST",
  mode: 'inline',
  dataType: 'json',
      success: function(response, newValue) {
        if(response.status == 'error') return response.msg; 
    },

 });
  
 $('#formula_metadata').editable({
  container: 'body',
  selector: 'td.product_name',
  url: "update_data.php?formulaMeta=<?php echo $info['name']; ?>",
  title: 'Product Name',
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
  	url: "update_data.php?formulaMeta=<?php echo $info['name']; ?>",
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
  	url: "update_data.php?formulaMeta=<?php echo $info['name']; ?>",
    source: [
             {value: 'unisex', text: 'Unisex'},
             {value: 'men', text: 'Men'},
             {value: 'women', text: 'Women'},
          ]
    });
  });

  $("#isProtected").change(function() {
	  $.ajax({ 
			url: 'update_data.php', 
			type: 'GET',
			data: {
				protect: '<?=$info['fid']?>',
				isProtected: $("#isProtected").is(':checked'),
				},
			dataType: 'html',
			success: function (data) {
				$('#msg').html(data);
			}
		  });
  });

</script>

<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


$rs = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM inventory_compounds WHERE id = '".$_GET['id']."'")); 
$q = mysqli_query($conn, "SELECT id,name FROM documents WHERE type = '5' AND isBatch = '1'");
while($res = mysqli_fetch_array($q)){
    $data[] = $res;
}  

?>

<div class="modal-body">
	<div id="cmp_edit_inf"></div>
    <div class="row">
          <div class="mb-3">
            <label for="cmp_edit_name" class="form-label">Compound name</label>
            <input name="cmp_edit_name" type="cmp_edit_name" class="form-control" id="cmp_edit_name" value="<?php echo $rs['name'];?>">
          </div>
          
          <div class="mb-3">
            <label for="cmp_edit_batch" class="form-label">Batch</label>
            <select name="cmp_edit_batch" id="cmp_edit_batch" class="form-control" data-live-search="true" >
            <?php foreach($data as $b) { ?>
            	<option value="<?php echo $b['id'];?>" <?php echo ($rs['batch_id']==$b['id'])?"selected=\"selected\"":""; ?>><?php echo $b['name'];?></option>
            <?php } ?>
            </select>
          </div>
         
          <div class="mb-3">
            <label for="cmp_edit_size" class="form-label">Bottle size (<?php echo $settings['mUnit']; ?>)</label>
            <input name="cmp_edit_size" type="text" class="form-control" id="cmp_edit_size" value="<?php echo $rs['size'];?>">
          </div>
          
          <div class="mb-3">
            <label for="cmp_edit_location" class="form-label">Location</label>
            <input name="cmp_edit_location" type="text" class="form-control" id="cmp_edit_location" value="<?php echo $rs['location'];?>">
          </div>
          
          
          <div class="mb-3">
            <label for="cmp_edit_desc" class="form-label">Short Description</label>
            <input name="cmp_edit_desc" type="text" class="form-control" id="cmp_edit_desc" value="<?php echo $rs['description'];?>">
          </div>
          
         <div class="col-sm">
           <label for="cmp_edit_label_info" class="form-label">Label info</label>
           <textarea class="form-control" name="cmp_edit_label_info" id="cmp_edit_label_info" rows="5"><?php echo $rs['label_info'];?></textarea>
         </div>
    
    </div>

    <div class="dropdown-divider mb-3"></div>
    
      <div class="modal-footer">
        <input type="submit" class="btn btn-primary" id="cmp_edit_save" value="Save">
      </div>

</div>  
<script>
$(document).ready(function() {

	$('#cmp_edit_save').click(function() {
		$.ajax({ 
			url: '/pages/update_data.php', 
			type: 'POST',
			data: {
				update_inv_compound_data: 1,
				cmp_id:  "<?=$rs['id']?>",
				name: $("#cmp_edit_name").val(),
				batch_id: $("#cmp_edit_batch").val(),
				description: $("#cmp_edit_desc").val(),
				size: $("#cmp_edit_size").val(),
				location: $("#cmp_edit_location").val(),
				label_info: $("#cmp_edit_label_info").val(),
			},
			dataType: 'json',
			success: function (data) {
				if(data.success){
					var msg = '<div class="alert alert-success">'+data.success+'</div>';
				}else if( data.error){
					var msg = '<div class="alert alert-danger">'+data.error+'</div>';
				}
				$('#cmp_edit_inf').html(msg);
			}
		  });
	
	});
	
});

</script>
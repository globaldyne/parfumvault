<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


$q = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM customers WHERE id = '".$_GET['id']."' AND owner_id = '$userID'")); 

?>

<style>

.container {
  max-width: 100%;
  width: 100%;

}
</style>
<div class="container">
    <div id="inf"></div>
    <div class="mb-3">
        <label for="customer-name" class="form-label">Name</label>
        <input class="form-control" name="customer-name" type="text" id="customer-name" value="<?=$q['name']?>" />
    </div>
    <div class="mb-3">
        <label for="customer-address" class="form-label">Address</label>
        <input class="form-control" name="customer-address" type="text" id="customer-address" value="<?=$q['address']?>" />
    </div>
    <div class="mb-3">
        <label for="customer-email" class="form-label">Email</label>
        <input class="form-control" name="customer-email" type="text" id="customer-email" value="<?=$q['email']?>" />
    </div>
    <div class="mb-3">
        <label for="customer-web" class="form-label">Web Site</label>
        <input class="form-control" name="customer-web" type="text" id="customer-web" value="<?=$q['web']?>" />
    </div>
    <div class="mb-3">
        <label for="customer-phone" class="form-label">Phone</label>
        <input class="form-control" name="customer-phone" type="text" id="customer-phone" value="<?=$q['phone']?>" />
    </div>
    <div class="modal-footer">
        <input type="submit" name="button" class="btn btn-primary" id="save" value="Save">
    </div>
</div>


<script>
$(document).ready(function() {

$('#save').click(function() {
	$.ajax({ 
		url: '/core/core.php', 
		type: 'POST',
		data: {
			update_customer_data: 1,
			customer_id: "<?=$q['id']?>",
			name: $("#customer-name").val(),
			address: $("#customer-address").val(),
			email: $("#customer-email").val(),
			web: $("#customer-web").val(),
			phone: $("#customer-phone").val(),
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success">'+data.success+'</div>';
			}else if( data.error){
				var msg = '<div class="alert alert-danger">'+data.error+'</div>';
			}
			$('#inf').html(msg);
		}
	  });

	});
});

</script>

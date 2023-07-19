<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');


$q = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM customers WHERE id = '".$_GET['id']."'")); 

?>

<style>

.container {
  max-width: 100%;
  width: 100%;

}
</style>
<div class="container">
	<div id="inf"></div>
     <p>
        Name: 
        <input class="form-control" name="customer-name" type="text"  id="customer-name" value="<?=$q['name']?>" />
        </p>
        <p>            
        Address:
        <input class="form-control" name="customer-address" type="text" id="customer-address" value="<?=$q['address']?>"/>
        </p>
        <p>
        Email:
        <input class="form-control" name="customer-email" type="text" id="customer-email" value="<?=$q['email']?>"/>
        </p>
        <p>
        Web Site:
        <input class="form-control" name="customer-web" type="text" id="customer-web" value="<?=$q['web']?>"/>
        </p>        

        <p>
        Phone:
        <input class="form-control" name="customer-phone" type="text" id="customer-phone" value="<?=$q['phone']?>"/>
        </p>
           
</div>
      <div class="modal-footer">
        <input type="submit" name="button" class="btn btn-primary" id="save" value="Save">
      </div>
    </div>  

<script>
$(document).ready(function() {

$('#save').click(function() {
	$.ajax({ 
		url: '/pages/update_data.php', 
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
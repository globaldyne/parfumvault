<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require(__ROOT__.'/inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');


$q = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM customers WHERE id = '".$_GET['id']."'")); 

?>
<script src="/js/jquery/jquery.min.js"></script>

<link href="/css/sb-admin-2.css" rel="stylesheet">
<link href="/css/bootstrap.min.css" rel="stylesheet">
<link href="/css/vault.css" rel="stylesheet">

<style>

.container {
  max-width: 100%;
  width: 100%;

}
</style>
<div class="container">
    <h1 class="text-primary"><?=$q['name']?></h1>
      <hr>
       <div class="text-center">
       </div>
       <div id="inf"></div>
        <p>
        Name: 
          <input class="form-control" name="name" type="text"  id="name" value="<?=$q['name']?>" />
        </p>
        <p>            
        Address:
          <input class="form-control" name="address" type="text" id="address" value="<?=$q['address']?>"/>
        </p>
        <p>
        Email:
          <input class="form-control" name="email" type="text" id="email" value="<?=$q['email']?>"/>
        </p>
        <p>
        Web Site:
          <input class="form-control" name="web" type="text" id="web" value="<?=$q['web']?>"/>
        </p>        

        <p>
        Phone:
          <input class="form-control" name="phone" type="text" id="phone" value="<?=$q['phone']?>"/>
        </p>
           
        <div class="dropdown-divider"></div>
</div>
      <div class="modal-footer">
        <input type="submit" name="button" class="btn btn-primary" id="save" value="Save">
      </div>
    </div>  
<hr>
<script>

$('#save').click(function() {
	$.ajax({ 
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			update_customer_data: 1,
			customer_id:  "<?=$q['id']?>",
			name: $("#name").val(),
			address: $("#address").val(),
			email: $("#email").val(),
			web: $("#web").val(),
			phone: $("#phone").val(),
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
</script>
<?php 
if (!defined('pvault_panel')){ die('Not Found');}
define('__ROOT__', dirname(__FILE__)); 

require_once(__ROOT__.'/inc/product.php');
$first_time = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="<?php echo $product.' - '.$ver;?>">
  <meta name="author" content="<?php echo $product.' - '.$ver;?>">
  <title><?php echo $product;?> - First time setup!</title>
  <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
  <link href="/css/sb-admin-2.css" rel="stylesheet">
  <link href="/css/vault.css" rel="stylesheet">
  <script src="/js/jquery/jquery.min.js"></script>
  <script src="/js/bootstrap.min.js"></script>
</head>

<body class="bg-gradient-primary">
  <div class="container">
      <div class="col">
              <div class="col-lg-6 d-none d-lg-block bg-install-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">First time setup</h1>
                  </div>
                  <div id="msg"></div>
                  
				  <div id="install_form" class="user">
                    <hr>
                    <strong>Database Settings:</strong>
                    <hr>
                    <div class="form-group">
                      <label for="dbhost" class="form-label">Database Hostname or IP</label>
                      <input type="text" class="form-control form-control-user" id="dbhost">
                    </div>
                    <div class="form-group">
                      <label for="dbuser" class="form-label">Database username</label>
                      <input type="text" class="form-control form-control-user" id="dbuser">
                    </div>
                    <div class="form-group">
                      <label for="dbpass" class="form-label">Database password</label>
                      <input type="text" class="form-control form-control-user" id="dbpass">
                    </div>
                     <div class="form-group">
                      <label for="dbname" class="form-label">Database name</label>
                      <input type="text" class="form-control form-control-user" id="dbname">
                    </div>
                    <hr>
                    <strong>User Settings:</strong>
                    <hr>
                    <div class="form-group">
                      <label for="fullName" class="form-label">Full name</label>
                      <input type="text" class="form-control form-control-user" id="fullName">
                    </div>      
                    <div class="form-group">
                      <label for="email" class="form-label">Email</label>
                      <input type="text" class="form-control form-control-user" id="email">
                    </div>
                    <div class="form-group">
                      <label for="password" class="form-label">Password</label>
                      <input type="text" class="form-control form-control-user" id="password">
                    </div>
                    <div class="form-group"></div>
                    <hr>
					<button name="save" id="saveInstallData" class="btn btn-primary btn-user btn-block">
                      Save!
                    </button>
                    <p>&nbsp;</p>
                    <p>*All fields required</p>
                  </div>
                  
        </div>
      </div>
    </div>
  </div>
 </body>
</html>
<script>
$(document).ready(function() {

	$('#install_form').on('click', '[id*=saveInstallData]', function () {
		$('#saveInstallData').prop('disabled', true);
		$('#msg').html('<div class="alert alert-info"><img src="/img/loading.gif"/> Please wait, configuring the system...<p><strong>Please do not close, refresh or navigate away from this page. You will be automatically redirected upon a succesfull installation.</strong></p></div>');
		$("#install_form").hide();
		
		$.ajax({ 
			url: '/core/configureSystem.php', 
			type: 'POST',
			data: {
				action: 'install',
				dbhost: $("#dbhost").val(),
				dbuser: $("#dbuser").val(),
				dbpass: $("#dbpass").val(),
				dbname: $("#dbname").val(),
				fullName: $("#fullName").val(),
				email: $("#email").val(),
				password: $("#password").val(),
			},
			dataType: 'json',
			success: function (data) {
				if (data.success){ 
				    window.location = '/'
				}
				if(data.error){
					var msg = '<div class="alert alert-danger">'+data.error+'</div>';
					$("#install_form").show();
					$('#saveInstallData').prop('disabled', false);
				}
				$('#msg').html(msg);
			}
		});
	});
    
});//end doc

</script>

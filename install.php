<?php 
if (!defined('pvault_panel')){ die('Not Found');}
require_once('inc/product.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="<?php echo $product.' - '.$ver;?>">
  <meta name="author" content="JBPARFUM">
  <title><?php echo $product;?> - First time setup!</title>

  <link href="/css/sb-admin-2.css" rel="stylesheet">
  <link href="/css/vault.css" rel="stylesheet">
  <script src="/js/jquery/jquery.min.js"></script>
  <script src="/js/bootstrap.min.js"></script>
</head>

<body class="bg-gradient-primary">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
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
                      <input type="text" class="form-control form-control-user" id="dbhost" placeholder="Database Hostname or IP...">
                    </div>
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="dbuser" placeholder="Database Username...">
                    </div>
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="dbpass" placeholder="Database Password...">
                    </div>
                     <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="dbname" placeholder="Database Name...">
                    </div>
                    <hr>
                    <strong>User Settings:</strong>
                    <hr>
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="fullName" placeholder="Your full name...">
                    </div>      
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="email" placeholder="Your email...">
                    </div>
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="password" placeholder="Password...">
                    </div>
					<div class="form-group">
                      <input name="createPVOnline" type="checkbox" class="form-control-user" id="createPVOnline" value="true" checked>
                      <label>Create a PV Online account</label>
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
				createPVOnline: $("#createPVOnline").is(":checked"),
			},
			dataType: 'html',
			success: function (data) {
				if (data.includes("Success")){ 
				    window.location='/'
				}
				$("#install_form").show();
				$('#saveInstallData').prop('disabled', false);
				$('#msg').html(data);
			}
		});
	});
    
});//end doc

</script>

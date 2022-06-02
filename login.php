<?php
define('pvault_panel', TRUE);
define('__ROOT__', dirname(__FILE__)); 

if(file_exists('./inc/config.php') == FALSE){

	require 'install.php';
	
}else{

session_start();
if(isset($_SESSION['parfumvault'])){
	header('Location: index.php');
}

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/db/prepare/prepare.php');

$installed_ver = mysqli_fetch_array(mysqli_query($conn,"SELECT app_ver FROM pv_meta"));

if($_POST['email'] && $_POST['password']){
	$_POST['email'] = mysqli_real_escape_string($conn,strtolower($_POST['email']));
	$_POST['password'] = mysqli_real_escape_string($conn,$_POST['password']);
	
	$row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM users WHERE email='".$_POST['email']."' AND password='".$_POST['password']."'"));

	if($row['id']){	// If everything is OK login
			$_SESSION['parfumvault'] = true;
			$_SESSION['userID'] = $row['id'];
			header('Location: index.php');
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">Email or password error</div>';
	}
}


?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <script type='text/javascript'>
	if ((navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i))){
			if(screen.height>=1080)
				document.write('<meta name="viewport" content="width=device-width, initial-scale=2.0, minimum-scale=1.0, maximum-scale=3.0, target-densityDpi=device-dpi, user-scalable=yes">');
			else	
				document.write('<meta name="viewport" content="width=device-width, initial-scale=0.5, minimum-scale=0.5, maximum-scale=3.0, target-densityDpi=device-dpi, user-scalable=yes">');
	}
  </script>
  <meta name="description" content="<?php echo $product.' - '.$ver;?>">
  <meta name="author" content="JBPARFUM">
  <title><?php echo $product;?> - Login</title>
  
  <script src="js/jquery/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
 
  <link href="css/sb-admin-2.css" rel="stylesheet">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/vault.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
             <?php if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users")) == 0){ $first_time = 1; ?>
              <div class="col-lg-6 d-none d-lg-block bg-register-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Please register a user!</h1>
                  </div>
                  <div id="msg"></div>
                   <div class="user" id="reg_form">
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
                    <button class="btn btn-primary btn-user btn-block" id="registerSubmit">
                      Register
                    </button>
                  </div>
                  <?php }else{ ?>
                  <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                  <div class="col-lg-6">
                    <div class="p-5">
                      <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">Welcome back!</h1>
                      </div>
                      <?php echo $msg; ?>
                        <form method="post" enctype="multipart/form-data" class="user" id="login">
                        <div class="form-group">
                          <input type="text" class="form-control form-control-user" name="email"  placeholder="Email...">
                        </div>
                        <div class="form-group">
                          <input type="password" class="form-control form-control-user" name="password" placeholder="Password...">
                        </div>
                        <div class="form-group"></div>
                        <button class="btn btn-primary btn-user btn-block">
                          Login
                        </button>
                      </form>
                      <hr>
                      <div class="text-center">
                        <a class="small" href="#" data-toggle="modal" data-target="#forgot_pass">Forgot Password?</a>
                      </div>
                  <?php } ?>		 		 
                  <hr>
                  <div class="copyright text-center my-auto">
				  <label class="small">Version: <?php echo $ver; ?> | <?php echo $product; ?></label>
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

<!--PRE UPGRADE NEEDED-->
<div class="modal fade" id="pre_warn" tabindex="-1" role="dialog" aria-labelledby="pre_warn" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">WARNING</h5>
      </div>
      <div id="upgrademsg"></div>
      <div class="modal-body">
		<?php echo prepare($installed_ver['app_ver'], $ver); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="confUpgrade" data-dismiss="modal">Upgrade</button>
      </div>
    </div>
  </div>
</div>

<!--FORGOT PASS INFO-->
<div class="modal fade" id="forgot_pass" tabindex="-1" role="dialog" aria-labelledby="forgot_pass" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Forgot Password</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		
         After installing <strong><?=$product?></strong> for the first time, you asked to set a password. This password cannot be retrieved later on as its stored in the database in encrypted format.
      	<?php if(file_exists('/config/.DOCKER') == TRUE){ ?>
         To set a new password, you need to execute the command bellow: 
      <p></p>
      <pre>reset_pass.sh</pre>
      <?php }else{ ?>
      		To set a new password, you need manually access your database and set a new password there or remove the user. This will force the system to ask you to create a new user.
      <?php } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php } ?>

<script>
$(document).ready(function() {
<?php if($installed_ver['app_ver'] < $ver && !$first_time){ ?> 
    $('#pre_warn').modal('show');
	$("#pre_warn").on('hide.bs.modal', function () {  
  		return false
	});
	$('#pre_warn').on('click', '[id*=confUpgrade]', function () {
		
		$.ajax({ 
			url: '/db/prepare/prepare.php', 
			type: 'POST',
			data: {
				action: 'upgrade',
				version: <?=$ver?>,
			},
			dataType: 'json',
			success: function (data) {
				if (data.success){ 
				    window.location='/'
				}
				if(data.error){
					var msg = '<div class="alert alert-danger">'+data.error+'</div>';
				}
				
				$('#upgrademsg').html(msg);
			}
		});
																 
	});
<?php } ?>
	$('#reg_form').on('click', '[id*=registerSubmit]', function () {
		$('#registerSubmit').prop('disabled', true);
		$('#msg').html('<div class="alert alert-info"><img src="/img/loading.gif"/> Please wait, configuring the system...<p><strong>Please do not close, refresh or navigate away from this page. You will be automatically redirected upon a succesfull installation.</strong></p></div>');
		$("#reg_form").hide();
		
		$.ajax({ 
			url: '/core/configureSystem.php', 
			type: 'POST',
			data: {
				action: 'register',
				fullName: $("#fullName").val(),
				email: $("#email").val(),
				password: $("#password").val(),
				createPVOnline: $("#createPVOnline").is(":checked"),
			},
			dataType: 'json',
			success: function (data) {
				if (data.success){ 
				    window.location='/'
				}
				if(data.error){
					var msg = '<div class="alert alert-danger">'+data.error+'</div>';
				}
				
				$("#reg_form").show();
				$('#registerSubmit').prop('disabled', false);
				$('#msg').html(msg);
			}
		});
	});
    
});//end doc

</script>
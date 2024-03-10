<?php
define('pvault_panel', TRUE);
define('__ROOT__', dirname(__FILE__));

require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/product.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION['parfumvault'])){
	header('Location: /index.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="description" content="<?php echo $product.' - '.$ver;?>">
  <meta name="author" content="perfumersvault">
  <title><?php echo $product;?> - Login</title>
  <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
  <script src="/js/jquery/jquery.min.js"></script>
  <script src="/js/bootstrap.bundle.min.js"></script>
 
  <link href="/css/sb-admin-2.css" rel="stylesheet">
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/vault.css" rel="stylesheet">
  <link href="/css/fontawesome-free/css/all.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
            <?php
			if(file_exists(__ROOT__.'/inc/config.php') == FALSE && !getenv('DB_HOST') && !getenv('DB_USER') && !getenv('DB_PASS') && !getenv('DB_NAME')){
        		require 'install.php';
				return;
			}
            if (mysqli_num_rows(mysqli_query($conn,"SELECT 1 FROM information_schema.tables WHERE table_schema = '".getenv('DB_NAME')."' AND table_name = 'pv_meta' LIMIT 1")) == 0 && getenv('DB_HOST') && getenv('DB_USER') && getenv('DB_PASS') && getenv('DB_NAME') ){
				$cmd = 'mysql -u'.getenv('DB_USER').' -p'.getenv('DB_PASS').' -h'.getenv('DB_HOST').' '.getenv('DB_NAME').' < '.__ROOT__.'/db/pvault.sql';
				passthru($cmd,$e);
				if(!$e){
					$app_ver = trim(file_get_contents(__ROOT__.'/VERSION.md'));
					$db_ver  = trim(file_get_contents(__ROOT__.'/db/schema.ver'));
					mysqli_query($conn,"INSERT INTO pv_meta (schema_ver,app_ver) VALUES ('$db_ver','$app_ver')");
					header('Location: /');
				}else{
					$response['error'] = 'DB Schema Creation error. Make sure the database '.getenv('DB_NAME').' exists in your mysql server '.getenv('DB_HOST').', user '.getenv('DB_USER').' has full permissions on it and its empty.';
					echo json_encode($response);
					return;
				}
			}
			?>
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
               <button class="btn btn-primary btn-user btn-block" id="registerSubmit">
                 Register
               </button>
            </div>
            <?php }else{ ?>
              <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Please login</h1>
                  </div>
                  <div id="msg"></div>
                  <div class="user" id="login">
                  
                    <div class="form-group">
                      <label for="login_email" class="form-label">Email</label>
                      <input type="text" class="form-control form-control-user" name="login_email" id="login_email">
                    </div>
                    <div class="form-group">
                      <label for="login_pass" class="form-label">Password</label>
                      <input type="password" class="form-control form-control-user" name="login_pass" id="login_pass">
                    </div>
                    <div class="form-group"></div>
                    <button class="btn btn-primary btn-user btn-block" id="login_btn">
                      Login
                    </button>
                  </div>
                 <?php if(strtoupper(getenv('PASS_RESET_INFO') ?: $PASS_RESET_INFO) != "DISABLED"){ ?>

                  <hr />
                  <div class="text-center">
                    <a class="small" href="#" data-bs-toggle="modal" data-bs-target="#forgot_pass">Forgot Password?</a>
                  </div>
            <?php
				 }
			 } 
			?>		 		 
                  <hr />
                  <div class="copyright text-center my-auto">
				  <label class="small">Version: <?php echo $ver; ?> |<a href="https://www.perfumersvault.com/" class="link-dark mx-1" target="_blank"><?php echo $product; ?></a></label>
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

<?php if(strtoupper(getenv('PASS_RESET_INFO') ?: $PASS_RESET_INFO) != "DISABLED"){ ?>

<!--FORGOT PASS INFO-->
<div class="modal fade" id="forgot_pass" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="forgot_pass" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Forgot Password</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		
         After installing <strong><?=$product?></strong> for the first time, you asked to set a password. This password cannot be retrieved later on as its stored in the database in encrypted format.
      	<?php if(strtoupper(getenv('PLATFORM')) === 'CLOUD'){ ?>
         To set a new password for a user, you need to execute the command bellow followed by the user's email you want its password reset.
         If the user don't exist, will created automatically and the system will generate a random password. 
      <p></p>
      <pre>reset_pass.sh example@example.com</pre>
      <?php }else{ ?>
      		To set a new password, you need manually to access your database and set a new password there for the user you want its password reset.
            If the user don't exist, will created automatically and the system will generate a random password.
      <?php } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php  } ?>

<script>
$(document).ready(function() {

	$('#reg_form').on('click', '[id*=registerSubmit]', function () {
		$('#registerSubmit').prop('disabled', true);
		$('#msg').html('<div class="alert alert-info mx-2"><img src="/img/loading.gif"/>Please wait, configuring the system...<p><strong>Please do not close, refresh or navigate away from this page. You will be automatically redirected upon a succesfull installation.</strong></p></div>');
		$("#reg_form").hide();
		
		$.ajax({ 
			url: '/core/configureSystem.php', 
			type: 'POST',
			data: {
				action: 'register',
				fullName: $("#fullName").val(),
				email: $("#email").val(),
				password: $("#password").val(),
			},
			dataType: 'json',
			success: function (data) {
				if (data.success){ 
				    window.location='/'
				}
				if(data.error){
					var msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>'+data.error+'</div>';
				}
				
				$("#reg_form").show();
				$('#registerSubmit').prop('disabled', false);
				$('#msg').html(msg);
			}
		});
	});
    
	$('#login_btn').click(function() {
		$("#login_btn").prop("disabled", true);
 		$('#login_btn').append('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
		$("#login_email").prop("disabled", true);
		$("#login_pass").prop("disabled", true);
		$.ajax({ 
			url: '/core/auth.php', 
			type: 'POST',
			data: {
				action: "login",
				email: $("#login_email").val(),
				password: $("#login_pass").val(),
				do: "<?=$_GET['do']?>",
				url: "<?=$_GET['url']?>"
			},
			dataType: 'json',
			success: function (data) {
				if(data.auth.success){
					
					window.location = data.auth.redirect ;
					
				}else if( data.auth.error){
					msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>'+data.auth.msg+'</div>';
					$("#login_btn").prop("disabled", false);
					$("span").remove();
					$("#login_email").prop("disabled", false);
					$("#login_pass").prop("disabled", false);
				}
				
				$('#msg').html(msg);
			},
			error: function (request, status, error) {
        		$('#msg').html('<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Unable to handle request, server returned an error: '+request.status+'</div>');
				$("span").remove();
				$("#login_email").prop("disabled", false);
				$("#login_pass").prop("disabled", false);
				$("#login_btn").prop("disabled", false);
    		},
			
	  });
	});
	
});//end doc

</script>
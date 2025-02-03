<?php
define('pvault_panel', TRUE);
define('__ROOT__', dirname(__FILE__));

require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/inc/settings.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION['parfumvault'])){
	header('Location: /index.php');
}

?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>

    <meta charset="utf-8">
    <meta name="description" content="<?php echo $product.' - '.$ver;?>">
    <meta name="author" content="perfumersvault">
    <title><?php echo $product;?> - Sign In</title>
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
            // Check if the `pv_meta` table exists
            $schemaCheckQuery = "
                SELECT 1 
                FROM information_schema.tables 
                WHERE table_schema = ? 
                  AND table_name = 'pv_meta' 
                LIMIT 1";
            $schemaExistsStmt = $conn->prepare($schemaCheckQuery);
            
            if ($schemaExistsStmt) {
                $dbName = getenv('DB_NAME');
                $schemaExistsStmt->bind_param('s', $dbName);
                $schemaExistsStmt->execute();
                $schemaExistsStmt->store_result();
            
                if ($schemaExistsStmt->num_rows === 0 && getenv('DB_HOST') && getenv('DB_USER') && getenv('DB_PASS') && getenv('DB_NAME')) {
                    // Run schema creation script
                    $cmd = sprintf(
                        'mysql -u%s -p%s -h%s %s < %s/db/pvault.sql',
                        escapeshellarg(getenv('DB_USER')),
                        escapeshellarg(getenv('DB_PASS')),
                        escapeshellarg(getenv('DB_HOST')),
                        escapeshellarg(getenv('DB_NAME')),
                        escapeshellarg(__ROOT__)
                    );
                    passthru($cmd, $exitCode);
            
                    if ($exitCode === 0) {
                        // Insert schema and app versions
                        $app_ver = trim(file_get_contents(__ROOT__ . '/VERSION.md'));
                        $db_ver = trim(file_get_contents(__ROOT__ . '/db/schema.ver'));
            
                        $insertMetaQuery = "
                            INSERT INTO pv_meta (schema_ver, app_ver) 
                            VALUES (?, ?)";
                        $metaStmt = $conn->prepare($insertMetaQuery);
                        $metaStmt->bind_param('ss', $db_ver, $app_ver);
                        $metaStmt->execute();
						            header('Location: /');
                    } else {
                        // Handle schema creation error
                        $response = [
                            'error' => sprintf(
                                'DB Schema Creation error. Make sure the database %s exists on your MySQL server %s, user %s has full permissions on it, and it is empty.',
                                getenv('DB_NAME'),
                                getenv('DB_HOST'),
                                getenv('DB_USER')
                            )
                        ];
                        echo json_encode($response);
                        return;
                    }
                }
            }
				// Check and manage user creation or update
				$userCheckQuery = "SELECT id FROM users LIMIT 1";
				$userResult = $conn->query($userCheckQuery);
				
				if ($userResult) {
					$userExists = $userResult->num_rows > 0;
					if (getenv('USER_EMAIL') && getenv('USER_NAME') && getenv('USER_PASSWORD')) {
						$userEmail = getenv('USER_EMAIL');
						$userName = getenv('USER_NAME');
						$userPassword = getenv('USER_PASSWORD');
				
						// Check if the password is already hashed by looking at the format
						$isHashed = preg_match('/^\$2[ayb]\$.{56}$/', $userPassword); // Matches bcrypt format
						$hashedPassword = $isHashed ? $userPassword : password_hash($userPassword, PASSWORD_DEFAULT);
				
						if ($userExists) {
							// Update existing user
							$updateUserQuery = "
								UPDATE users 
								SET email = ?, fullName = ?, password = ? 
								WHERE id = (SELECT id FROM users LIMIT 1)";
							$updateStmt = $conn->prepare($updateUserQuery);
							$updateStmt->bind_param('sss', $userEmail, $userName, $hashedPassword);
							if (!$updateStmt->execute()) {
								error_log("Failed to update user: " . $updateStmt->error);
								$error_msg = "User query failed: " . $updateStmt->error;
								require_once(__ROOT__.'/pages/error.php');
								return;
							}
						} else {
							// Insert new user
							$insertUserQuery = "
								INSERT INTO users (email, fullName, password) 
								VALUES (?, ?, ?)";
							$insertStmt = $conn->prepare($insertUserQuery);
							$insertStmt->bind_param('sss', $userEmail, $userName, $hashedPassword);
				
							if (!$insertStmt->execute()) {
								error_log("Failed to insert user: " . $insertStmt->error);
								$error_msg = "User query failed: " . $insertStmt->error;
								require_once(__ROOT__.'/pages/error.php');
								return;
							}
						}
					}
			}
    ?>

<?php if ($conn->query("SELECT id FROM users LIMIT 1")->num_rows == 0) { ?>
<div class="col-lg-6 d-none d-lg-block bg-register-image"></div>
<div class="col-lg-6">
<div class="p-5">
  <div class="text-center">
      <h1 class="h4 text-gray-900 mb-4">Please register a user</h1>
  </div>
  <div id="msg"></div>
  <div class="user" id="reg_form">
      <hr>
      <div class="form-floating mb-3">
          <input type="text" class="form-control" id="fullName"
              placeholder="Full name">
          <label for="fullName">Full name</label>
      </div>
      <div class="form-floating mb-3">
          <input type="email" class="form-control" id="email" placeholder="Email">
          <label for="email">Email</label>
      </div>
      <div class="form-floating mb-3">
          <input type="password" class="form-control password-input" id="password"
              placeholder="Password">
          <label for="password">Password</label>
          <i class="toggle-password fa fa-eye"></i>
      </div>
      <div class="form-group"></div>
      <button class="btn btn-primary btn-user btn-block" id="registerSubmit">
          Register
      </button>
  </div>

  <?php }elseif($_GET['do'] == 'reset-password' && $_GET['token']){ ?>
  <div class="col-lg-6 d-none d-lg-block bg-register-image"></div>
  <div class="col-lg-6">
      <div class="p-5">
          <div class="text-center">
              <h1 class="h4 text-gray-900 mb-4">Reset Password</h1>
          </div>
          <div id="msg"></div>
          <div class="user" id="reset_pass">
              <hr>
              <div class="form-floating mb-3">
                  <input type="password" class="form-control password-input"
                      id="password" placeholder="Password">
                  <label for="password">Password</label>
              </div>
              <div class="form-floating mb-3">
                  <input type="password" class="form-control password-input"
                      id="confirm_password" placeholder="Confirm Password">
                  <label for="confirm_password">Confirm Password</label>
              </div>
              <div class="form-group"></div>
              <button class="btn btn-primary btn-user btn-block" id="reset_pass_btn">
                  Reset Password
              </button>
          </div>
      </div>

      <script>
      $(document).ready(function() {

          $('#reset_pass_btn').click(function() {
              var password = $('#password').val();
              var confirmPassword = $('#confirm_password').val();

              if (password !== confirmPassword) {
                  $('#msg').html(
                      '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Passwords do not match.</div>'
                      );
                  return;
              }

              $('#reset_pass_btn').prop('disabled', true);
              $('#msg').html(
                  '<div class="alert alert-info"><img src="/img/loading.gif"/> Please wait...</div>'
                  );

              $.ajax({
                  url: '/core/configureSystem.php',
                  type: 'POST',
                  data: {
                      action: 'resetPassword',
                      token: '<?php echo $_GET['token']; ?>',
                      newPassword: password
                  },
                  dataType: 'json',
                  success: function(data) {
                      if (data.success) {
                          //$('#msg').html('<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>');
                          window.location = '/';
                      } else {
                          $('#msg').html(
                              '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>' +
                              data.error + '</div>');
                      }
                      $('#reset_pass_btn').prop(
                          'disabled', false);
                  },
                  error: function(xhr, status, error) {
                      $('#msg').html(
                          '<div class="alert alert-danger">Server error: ' +
                          error + '</div>');
                      $('#reset_pass_btn').prop(
                          'disabled', false);
                  }
              });
          });
      });
      </script>
      <?php 
      }elseif($_GET['do'] == 'confirm-email' && $_GET['token']){
          // CONFIRM EMAIL
            $token = mysqli_real_escape_string($conn, $_GET['token']);
            $checkTokenQuery = "SELECT email FROM users WHERE token = '$token' AND isVerified = 0";
            $result = mysqli_query($conn, $checkTokenQuery);
            if (mysqli_num_rows($result) == 0) {
                $response['error'] = 'Invalid or expired token';
                echo json_encode($response);
                return;
            }
            $row = mysqli_fetch_assoc($result);
            $email = $row['email'];
            $updateUserQuery = "UPDATE users SET isVerified = 1, isActive = 1, token = NULL WHERE email = '$email'";
            if (mysqli_query($conn, $updateUserQuery)) {
                $response['success'] = 'Email has been confirmed successfully';
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['temp_response'] = $response['success'];
                header('Location: /login.php');
            } else {
              $_SESSION['temp_response'] = $response['success'];
              header('Location: /login.php');
            }
        }else{
          if (isset($_SESSION['temp_response'])) {
            error_log('Temp response: ' . print_r($_SESSION['temp_response'], true));

            if (isset($_SESSION['temp_response']['error'])) {
                echo '<script>$(document).ready(function() { $("#msg").html("<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\"><i class=\"fa-solid fa-circle-exclamation mx-2\"></i>' . $_SESSION['temp_response']['error'] . '<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button></div>"); });</script>';
            } else {
                echo '<script>$(document).ready(function() { $("#msg").html("<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\"><i class=\"fa-solid fa-circle-check mx-2\"></i>' . $_SESSION['temp_response'] . '<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button></div>"); });</script>';
            }
            
            unset($_SESSION['temp_response']);
          }

  ?>
  <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
  <div class="col-lg-6">
      <div class="p-5">
          <div class="text-center">
              <h1 class="h4 mb-4">Sign In</h1>
          </div>
          <div id="msg"></div>
          <div class="user" id="login_form">
              <?php if ($system_settings['SSO_status'] === '1') { ?>
              <div class="text-center">
                  <button type="button" class="btn btn-danger me-2" id="login_sso">Sign in with Google</button>
              </div>
              <div class="separator mt-2 mb-2">or</div>
              <?php } ?>
              <div class="form-floating mb-3">
                  <input type="email" class="form-control" id="login_email"
                      placeholder="name@example.com">
                  <label for="login_email">Email address</label>
              </div>
              <div class="form-floating mb-3">
                  <input type="password" class="form-control" id="login_pass"
                      placeholder="Password">
                  <label for="login_pass">Password</label>
              </div>
              <div class="form-group"></div>
              <button class="btn btn-primary btn-user btn-block" id="login_btn">
                  Sign In
              </button>
          </div>
          <?php if(getenv('PASS_RESET_INFO' ?: $PASS_RESET_INFO) !== "DISABLED"){ ?>
          <hr />
          <div class="text-center">
              <a class="small" href="#" data-bs-toggle="modal"
                  data-bs-target="#forgot_pass">Forgot Password?</a>
          </div>
          <?php } ?>
          <?php if ($system_settings['USER_selfRegister'] == '1') { ?>
          <hr />
          <div class="text-center">
              <a class="small" href="/register.php">Create an Account!</a>
          </div>
          <?php
				 }
			 } 
    
			?>
        <hr />
        <div class="copyright text-center my-auto">
            <label class="small">Version: <?php echo $ver; ?> |<a
                    href="https://www.perfumersvault.com/" class="mx-1"
                    target="_blank"><?php echo $product; ?></a></label>
        </div>
        <?php if ($system_settings['USER_selfRegister'] == '1' && !empty($system_settings['USER_privacy_url'])) { ?>
        <div class="text-center">
            <a class="small"
                href="<?php echo $system_settings['USER_privacy_url']; ?>"
                target="_blank">Privacy Policy</a>
        </div>
        <?php } ?>
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

<?php 
if(getenv('PASS_RESET_INFO' ?: $PASS_RESET_INFO) !== "DISABLED"){
if($system_settings['EMAIL_isEnabled'] == 1){ ?>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgot_pass" data-bs-backdrop="static" tabindex="-1" aria-labelledby="forgot_pass_label"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgot_pass_label">Forgot Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="forgot_msg"></div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="forgot_email" placeholder="name@example.com">
                    <label for="forgot_email">Email address</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="forgot_submit">Reset Password</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#forgot_submit').click(function() {
    var email = $('#forgot_email').val();
    $('#forgot_submit').prop('disabled', true);
    $('#forgot_msg').html(
        '<div class="alert alert-info"><img src="/img/loading.gif"/> Please wait...</div>');

      $.ajax({
          url: '/core/configureSystem.php',
          type: 'POST',
          data: {
              action: 'resetPassword',
              email: email
          },
          dataType: 'json',
          success: function(data) {
              if (data.success) {
                  $('#forgot_msg').html(
                      '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' +
                      data.success + '</div>');
                  $('#forgot_email').hide();
                  $('#forgot_submit').hide();
              } else {
                  $('#forgot_msg').html(
                      '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>' +
                      data.error + '</div>');
              }
              $('#forgot_submit').prop('disabled', false);
          },
          error: function(xhr, status, error) {
              $('#forgot_msg').html('<div class="alert alert-danger">Server error: ' +
                  error + '</div>');
              $('#forgot_submit').prop('disabled', false);
          }
      });
  });
});
</script>

<?php } else { ?>

<!--FORGOT PASS INFO-->
<div class="modal fade" id="forgot_pass" data-bs-backdrop="static" tabindex="-1" aria-labelledby="forgot_pass_label"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgot_pass_label">Forgot Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($conn->query("SELECT id FROM users LIMIT 1")->num_rows == 0) { ?>

                <p>
                    When you first installed <strong><?=$product?></strong>, you were prompted to set a password.
                    This password is stored securely in an encrypted format and cannot be retrieved later.
                </p>
                <?php if (getenv('PLATFORM') === 'CLOUD') { ?>
                <p>
                    To reset a user's password, run the command below with the email of the user whose password you want
                    to reset.
                    If the user does not exist, a new account will be created automatically with a randomly generated
                    password.
                </p>
                <pre><code>reset_pass.sh example@example.com</code></pre>
                <?php } else { ?>
                <p>
                    To reset a password, you will need to manually access your database and update the password for the
                    desired user.
                    If the user does not exist, create a new record with a randomly generated password.
                </p>
                <?php } ?>
                <?php } else { ?>
                <p>
                    If you have forgotten your password, please contact your system administrator for assistance.
                </p>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<?php
  }
} 
?>

<script>
$(document).ready(function() {

<?php if ($system_settings['SSO_status'] === '1') { ?>
    $('#login_form #login_sso').click(function() {
    console.log('SSO AUTH');
	
    $('#login_form :input, #login_form button').prop('disabled', true);
 	$('#login_sso').append('<span class="spinner-border spinner-border-sm mx-1" role="status" aria-hidden="true"></span>');
	$.ajax({ 
		url: '/core/auth.php', 
		type: 'POST',
		data: {
           action: "auth_sso",
		},
		dataType: 'json',
		success: function (data) {
		    if(data.auth.success){
				window.location = data.auth.redirect ;			
			}else if( data.auth.error){
				$('#msg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill mx-2"></i>'+data.auth.msg+'</div>');
				$("#login_form .spinner-border").remove();
				$('#login_form :input, #login_form button').prop('disabled', false);
			}				
		},
		error: function (request, status, error) {
        	$('#msg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill mx-2"></i>Unable to handle request, server returned an error: '+request.status+'</div>');
			$("#login_form .spinner-border").remove();
			$('#login_form :input, #login_form button').prop('disabled', false);
    	},		
	});
 });
<?php } ?>
    $(".toggle-password").click(function() {
        var passwordInput = $($(this).siblings(".password-input"));
        var icon = $(this);
        if (passwordInput.attr("type") == "password") {
            passwordInput.attr("type", "text");
            icon.removeClass("fa-eye").addClass("fa-eye-slash");
        } else {
            passwordInput.attr("type", "password");
            icon.removeClass("fa-eye-slash").addClass("fa-eye");
        }
    });

    $('#reg_form').on('click', '[id*=registerSubmit]', function() {
        $('#registerSubmit').prop('disabled', true);
        $('#msg').html(
            '<div class="alert alert-info mx-2"><img src="/img/loading.gif"/>Please wait, configuring the system...<p><strong>Please do not close, refresh or navigate away from this page. You will be automatically redirected upon a succesfull installation.</strong></p></div>'
            );
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
            success: function(data) {
                if (data.success) {
                    window.location = '/';
                }
                if (data.error) {
                    var msg =
                        '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' +
                        data.error + '</div>';
                    $('#msg').html(msg);
                }
                $("#reg_form").show();
                $('#registerSubmit').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                var msg =
                    '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' +
                    error + '</div>';
                $('#msg').html(msg);
                $("#reg_form").show();
                $('#registerSubmit').prop('disabled', false);
            }
        });
    });

    $('#login_btn').click(function() {
        $("#login_btn").prop("disabled", true);
        $('#login_btn').append(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
            );
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
            success: function(data) {
                if (data.auth.success) {

                    window.location = data.auth.redirect;

                } else if (data.auth.error) {
                    msg =
                        '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' +
                        data.auth.msg + '</div>';
                    $("#login_btn").prop("disabled", false);
                    $("#login_btn span").remove();
                    $("#login_email").prop("disabled", false);
                    $("#login_pass").prop("disabled", false);
                }

                $('#msg').html(msg);
            },
            error: function(request, status, error) {
                $('#msg').html(
                    '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Unable to handle request, server returned an error: ' +
                    request.status + ' - ' + error + '</div>');
                $("#login_btn span").remove();
                $("#login_email").prop("disabled", false);
                $("#login_pass").prop("disabled", false);
                $("#login_btn").prop("disabled", false);
            },

        });
    });

}); //end doc
</script>
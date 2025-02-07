<?php
define('pvault_panel', TRUE);
define('__ROOT__', dirname(__FILE__));

require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/inc/settings.php');

if ($system_settings['USER_selfRegister'] == '0') {
    header('Location: /');
    exit;
}

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
                            <div class="col-lg-6 d-none d-lg-block bg-register-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 mb-4">Create an Account!</h1>
                                    </div>
                                    <div id="msg"></div>
                                    <div class="user" id="register">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="register_name" placeholder="Full Name">
                                            <label for="register_name">Full Name</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="register_email" placeholder="name@example.com">
                                            <label for="register_email">Email address</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="register_pass" placeholder="Password">
                                            <label for="register_pass">Password</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="register_confirm_pass" placeholder="Confirm Password">
                                            <label for="register_confirm_pass">Confirm Password</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input type="checkbox" class="form-check-input" id="terms_checkbox">
                                            <label class="form-check-label" for="terms_checkbox">I agree to the <a href="<?php echo $system_settings['USER_terms_url']; ?>" target="_blank">terms and conditions</a></label>
                                        </div>
                                        <button class="btn btn-primary btn-user btn-block" id="register_btn">
                                            Register Account
                                        </button>
                                    </div>
                                    <hr />
                                    <div class="text-center">
                                        <a class="small" href="/login.php">Already have an account? Login!</a>
                                    </div>
                                    <div class="copyright text-center my-auto">
                                        <label class="small">Version: <?php echo $ver; ?> |<a href="https://www.perfumersvault.com/" class="mx-1" target="_blank"><?php echo $product; ?></a></label>
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
    $('#register_btn').click(function(e) {
        e.preventDefault();
        var name = $('#register_name').val().trim();
        var email = $('#register_email').val().trim();
        var password = $('#register_pass').val().trim();
        var confirmPassword = $('#register_confirm_pass').val().trim();
        var termsChecked = $('#terms_checkbox').is(':checked');
        
        if (name === '') {
            $('#register_name').focus();
            $('#msg').html('<div class="alert alert-danger">Please enter your full name.</div>');
            return;
        }
        if (email === '') {
            $('#register_email').focus();
            $('#msg').html('<div class="alert alert-danger">Please enter your email address.</div>');
            return;
        }
        if (!validateEmail(email)) {
            $('#register_email').focus();
            $('#msg').html('<div class="alert alert-danger">Please enter a valid email address.</div>');
            return;
        }
        if (password === '') {
            $('#register_pass').focus();
            $('#msg').html('<div class="alert alert-danger">Please enter your password.</div>');
            return;
        }
        if (confirmPassword === '') {
            $('#register_confirm_pass').focus();
            $('#msg').html('<div class="alert alert-danger">Please confirm your password.</div>');
            return;
        }
        if (password !== confirmPassword) {
            $('#register_confirm_pass').focus();
            $('#msg').html('<div class="alert alert-danger">Passwords do not match.</div>');
            return;
        }
        if (!termsChecked) {
            $('#terms_checkbox').focus();
            $('#msg').html('<div class="alert alert-danger">You must agree to the terms and conditions.</div>');
            return;
        }

    $.ajax({ 
        url: '/core/configureSystem.php', 
        type: 'POST',
        data: {
            action: 'selfregister',
            fullName: name,
            email: email,
            password: password,
        },
        dataType: 'json',
        success: function (data) {
            if (data.success) { 
                window.location = '/';
            } 
            if (data.error) {
                var msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
                $('#msg').html(msg);
            }
            $('#register_btn').prop('disabled', false);
        },
        error: function (xhr, status, error) {
            var msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + error + '</div>';
            $('#msg').html(msg);
            $('#register_btn').prop('disabled', false);
        }
    });
        
    });

    function validateEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});
</script>



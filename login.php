<?php
define('pvault_panel', TRUE);
define('__ROOT__', dirname(__FILE__));

require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/inc/settings.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is already logged in
if (isset($_SESSION['parfumvault'])) {
    header('Location: /index.php');
    exit;
}

// Load the template
$template = file_get_contents(__ROOT__ . '/template.html');

// Define placeholders and their replacements
$placeholders = [
    '{{lang}}' => 'en',
    '{{theme}}' => 'light',
    '{{meta_description}}' => htmlspecialchars($product . ' - ' . $ver),
    '{{author}}' => 'perfumersvault',
    '{{title}}' => htmlspecialchars($product . ' - Log In or Sign Up'),
    '{{favicon_32}}' => '/img/favicon-32x32.png',
    '{{favicon_16}}' => '/img/favicon-16x16.png',
    '{{jquery_js}}' => '/js/jquery/jquery.min.js',
    '{{bootstrap_js}}' => '/js/bootstrap.bundle.min.js',
    '{{custom_js}}' => '/js/custom.js', // Add your custom JavaScript file
    '{{sb_admin_css}}' => '/css/sb-admin-2.css',
    '{{bootstrap_css}}' => '/css/bootstrap.min.css',
    '{{vault_css}}' => '/css/vault.css',
    '{{fontawesome_css}}' => '/css/fontawesome-free/css/all.min.css',
    '{{body_class}}' => 'bg-gradient-primary',
    '{{content}}' => generateContent($conn), // Dynamically generated content
    '{{product_url}}' => 'https://www.perfumersvault.com',
    '{{product_name}}' => htmlspecialchars($product),
    '{{version}}' => htmlspecialchars($ver . " " . $commit),
    '{{discord_url}}' => 'https://discord.gg/WxNE8kR8ug',
    '{{appstore_pv}}' => 'https://apps.apple.com/us/app/perfumers-vault/id1525381567',
    '{{appstore_pv_img}}' => '/img/appstore/get_pv.png',
    '{{appstore_aroma}}' => 'https://apps.apple.com/us/app/aromatrack/id6742348411',
    '{{appstore_aroma_img}}' => '/img/appstore/get_aroma_track.png',
    '{{copyright_year}}' => date('Y'),
];

// Escape curly braces for preg_replace
$escaped_placeholders = array_map(function ($key) {
    return '/' . preg_quote($key, '/') . '/';
}, array_keys($placeholders));

// Replace placeholders in the template
$output = preg_replace($escaped_placeholders, array_values($placeholders), $template);

// Output the final HTML
echo $output;

/**
 * Generate dynamic content based on conditions
 */
/**
 * Generate dynamic content based on conditions
 * @param mysqli $conn - Database connection
 * @return string - HTML content to be injected into the template
 */
function generateContent($conn) {
    // Check if there are no users in the database
    if ($conn->query("SELECT id FROM users LIMIT 1")->num_rows == 0) {
        return <<<HTML
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
                <input type="text" class="form-control" id="fullName" placeholder="Full name">
                <label for="fullName">Full name</label>
            </div>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" placeholder="Email">
                <label for="email">Email</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control password-input" id="password" placeholder="Password">
                <label for="password">Password</label>
                <i class="toggle-password fa fa-eye"></i>
            </div>
            <button class="btn btn-primary btn-user btn-block" id="registerSubmit">
                Register
            </button>
        </div>
    </div>
</div>
HTML;
    }

    // If a password reset is requested
    if (isset($_GET['do']) && $_GET['do'] === 'reset-password' && isset($_GET['token'])) {
        return <<<HTML
<div class="col-lg-6 d-none d-lg-block bg-reset-password-image"></div>
<div class="col-lg-6">
    <div class="p-5">
        <div class="text-center">
            <h1 class="h4 text-gray-900 mb-4">Reset Password</h1>
        </div>
        <div id="msg"></div>
        <div id="reset_pass">
            <hr>
            <div class="form-floating mb-3">
                <input type="password" class="form-control password-input" id="password" placeholder="Password">
                <label for="password">Password</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control password-input" id="confirm_password" placeholder="Confirm Password">
                <label for="confirm_password">Confirm Password</label>
            </div>
            <button class="btn btn-primary btn-user btn-block" id="reset_pass_btn">
                Reset Password
            </button>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#reset_pass_btn').click(function() {
        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();

        if (password !== confirmPassword) {
            $('#msg').html('<div class="alert alert-danger">Passwords do not match.</div>');
            return;
        }

        $('#reset_pass_btn').prop('disabled', true);
        $('#msg').html('<div class="alert alert-info">Please wait...</div>');

        $.ajax({
            url: '/core/configureSystem.php',
            type: 'POST',
            data: {
                action: 'resetPassword',
                token: '{$_GET['token']}',
                newPassword: password
            },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    window.location = '/';
                } else {
                    $('#msg').html('<div class="alert alert-danger">' + data.error + '</div>');
                }
                $('#reset_pass_btn').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                $('#msg').html('<div class="alert alert-danger">Server error: ' + error + '</div>');
                $('#reset_pass_btn').prop('disabled', false);
            }
        });
    });
});
</script>
HTML;
    }

    // If email confirmation is requested
    if (isset($_GET['do']) && $_GET['do'] === 'confirm-email' && isset($_GET['token'])) {
        $token = mysqli_real_escape_string($conn, $_GET['token']);
        $checkTokenQuery = "SELECT email FROM users WHERE token = '$token' AND isVerified = 0";
        $result = $conn->query($checkTokenQuery);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $email = $row['email'];

            $updateUserQuery = "UPDATE users SET isVerified = 1, isActive = 1, token = NULL WHERE email = '$email'";
            if ($conn->query($updateUserQuery)) {
                $_SESSION['temp_response'] = 'Email has been confirmed successfully';
                header('Location: /login.php');
                exit;
            }
        }

        return '<div class="alert alert-danger">Invalid or expired token.</div>';
    }

    // Default to login form
    return <<<HTML
<div class="row">
    <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
    <div class="col-lg-6">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Log In or Sign Up</h1>
            </div>
            <div id="msg"></div>
            <div class="user" id="login_form">
                <hr />
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="login_email" placeholder="name@example.com">
                    <label for="login_email">Email address</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="login_pass" placeholder="Password">
                    <label for="login_pass">Password</label>
                </div>
                <button class="btn btn-primary btn-user btn-block" id="login_btn">
                    Sign In
                </button>
            </div>
            <hr/>
            <div class="text-center">
                    <a class="small" href="#" data-bs-toggle="modal" data-bs-target="#forgot_pass">Forgot Password?</a>
            </div>
            <div class="text-center">
                <a class="small" href="/register.php">Create an Account</a>
            </div>
        </div>
    </div>
</div>
HTML;
}
?>
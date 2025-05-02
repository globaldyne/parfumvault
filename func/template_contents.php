<?php
/**
 * @file template_contents.php
 * @brief Generates dynamic content for the login page based on conditions.
 * 
 * This file contains a function that generates HTML content for the login page.
 * It checks various conditions such as user registration, password reset, and email confirmation,
 * and returns the appropriate HTML content to be injected into the template.
 */
/**
 * Generate dynamic content based on conditions
 *
 * @param mysqli $conn Database connection
 * @return string HTML content to be injected into the template
 */
function generateContent($conn) {
    global $product, $system_settings;

    // Load language file
    $langFile = __ROOT__ . '/pvTemplates/lang/en.php';
    if (file_exists($langFile)) {
        $lang = include($langFile);
    } else {
        $lang = []; // Fallback to an empty array if the language file is missing
    }

    // Show registration form if no users exist
    if ($conn->query("SELECT id FROM users LIMIT 1")->num_rows == 0) {
        return <<<HTML
<!-- Registration HTML -->
<div class="col-lg-6 d-none d-lg-block bg-register-image"></div>
<div class="col-lg-6">
    <div class="p-5">
        <div class="text-center">
            <h1 class="h4 text-gray-900 mb-4">{$lang['register_user_title']}</h1>
        </div>
        <div id="msg"></div>
        <div class="user" id="reg_form">
            <hr>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="fullName" placeholder="{$lang['full_name_placeholder']}">
                <label for="fullName">{$lang['full_name_label']}</label>
            </div>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" placeholder="{$lang['email_placeholder']}">
                <label for="email">{$lang['email_label']}</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control password-input" id="password" placeholder="{$lang['password_placeholder']}">
                <label for="password">{$lang['password_label']}</label>
                <i class="toggle-password fa fa-eye"></i>
            </div>
            <button class="btn btn-primary btn-user btn-block" id="registerSubmit">
                {$lang['register_button']}
            </button>
        </div>
    </div>
</div>
HTML;
    }

    // Handle password reset
    if (isset($_GET['do']) && $_GET['do'] === 'reset-password' && isset($_GET['token'])) {
        $token = htmlspecialchars($_GET['token']);
        return <<<HTML
<div class="row">
    <div class="col-lg-6 d-none d-lg-block bg-register-image"></div>
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
                token: '{$token}',
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

    // Handle email confirmation
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
    $forgotPasswordModal = '';
    if (getenv('PASS_RESET_INFO') !== "DISABLED") {
        if ($system_settings['EMAIL_isEnabled'] == '1') {
            $forgotPasswordModal = <<<HTML
<div class="modal fade" id="forgot_pass" data-bs-backdrop="static" tabindex="-1" aria-labelledby="forgot_pass_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgot_pass_label">{$lang['forgot_password_title']}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="forgot_msg"></div>
                <div class="form-floating mb-3" id="forgot_email_form">
                    <input type="email" class="form-control" id="forgot_email" placeholder="{$lang['email_placeholder']}">
                    <label for="forgot_email">{$lang['email_label']}</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{$lang['close_button']}</button>
                <button type="button" class="btn btn-primary" id="forgot_submit">{$lang['reset_password_button']}</button>
            </div>
        </div>
    </div>
</div>
HTML;
        } else {
            $msg = $conn->query("SELECT id FROM users LIMIT 1")->num_rows == 0
                ? "<p>When you first installed <strong>{$product}</strong>, you were prompted to set a password...</p>"
                : "<p>If you have forgotten your password, please contact your system administrator for assistance.</p>";

            $forgotPasswordModal = <<<HTML
<div class="modal fade" id="forgot_pass" data-bs-backdrop="static" tabindex="-1" aria-labelledby="forgot_pass_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgot_pass_label">{$lang['forgot_password_title']}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">{$msg}</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{$lang['close_button']}</button>
            </div>
        </div>
    </div>
</div>
HTML;
        }
    }

    $registerLink = '';
    if ($system_settings['USER_selfRegister'] == '1') {
        $registerLink = <<<HTML
        <div class="text-center">
            <a class="small" href="/register.php">Create an Account!</a>
        </div>
        HTML;
    }
    
    $googleAnalytics = '';
    if (isset($system_settings['GOOGLE_analytics_status']) && $system_settings['GOOGLE_analytics_status'] == 1) {
        $googleAnalytics = <<<HTML
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$system_settings['GOOGLE_analytics_key']}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '{$system_settings['GOOGLE_analytics_key']}');

    function acceptCookies() {
        document.getElementById('cookieConsent').style.display = 'none';
        // Set a cookie to remember the user's consent
        document.cookie = "cookieConsent=true; max-age=" + 60*60*24*365 + "; path=/";
    }

    // Check if the user has already accepted cookies
    if (document.cookie.indexOf('cookieConsent=true') === -1) {
        document.getElementById('cookieConsent').style.display = 'block';
    }
</script>
<!-- Cookie Consent Banner -->
<div id="cookieConsent" class="bg-danger text-white p-3">
    <div class="cookieConsentContainer">
        <div class="cookieTitle">
            <a class="text-white">Cookies Notice</a>
        </div>
        <div class="cookieDesc">
            <p>We use cookies to enhance your browsing experience and provide personalized content. By continuing to use our site, you accept our use of cookies.</p>
        </div>
        <div class="cookieButton">
            <a class="btn btn-light" onclick="acceptCookies();">I Understand</a>
        </div>
    </div>
</div>
HTML;
    }

    $ssoScript = '';
    if ($system_settings['SSO_status'] == '1') {
        $ssoScript = <<<HTML
<script>
$(document).ready(function() {
    $('#login_form #login_sso').click(function() {
        console.log('SSO AUTH');
        $('#login_form :input, #login_form button').prop('disabled', true);
        $('#login_sso').append('<span class="spinner-border spinner-border-sm mx-1" role="status" aria-hidden="true"></span>');
        $.ajax({ 
            url: '/core/auth.php', 
            type: 'POST',
            data: {
                action: "auth_sso",
                provider: $(this).data('provider'),
            },
            dataType: 'json',
            success: function (data) {
                if (data.auth.success) {
                    window.location = data.auth.redirect;			
                } else if (data.auth.error) {
                    $('#msg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.auth.msg + '</div>');
                    $("#login_form .spinner-border").remove();
                    $('#login_form :input, #login_form button').prop('disabled', false);
                }				
            },
            error: function (request, status, error) {
                $('#msg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>Unable to handle request, server returned an error: ' + request.status + '</div>');
                $("#login_form .spinner-border").remove();
                $('#login_form :input, #login_form button').prop('disabled', false);
            },		
        });
    });
});
</script>
HTML;
    }

    // Pre-render SSO button block if needed
    $ssoBlock = '';
    if ($system_settings['SSO_status'] == '1') {
        if (isset($_SESSION['temp_response'])) {
            error_log("PV error: Temp response: " . print_r($_SESSION['temp_response']['error'], true));
            $errorMsg = htmlspecialchars($_SESSION['temp_response']['error'], ENT_QUOTES);
            $ssoBlock .= <<<HTML
    <script>
    $(document).ready(function() {
        $("#msg").html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa-solid fa-circle-exclamation mx-2"></i>{$errorMsg}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
    });
    </script>
    HTML;
            unset($_SESSION['temp_response']);
        }
    
        $ssoBlock .= <<<HTML
    <div class="text-center">
        <button type="button" class="btn btn-danger me-2" data-provider="google" id="login_sso">{$lang['continue_with_sso']}</button>
    </div>
    <div class="separator mt-2 mb-2">{$lang['or_separator']}</div>
    HTML;
    }
    
    // Forgot password block
    $forgotPassBlock = '';
    if (getenv('PASS_RESET_INFO') !== "DISABLED") {
        $forgotPassBlock = <<<HTML
    <hr />
    <div class="text-center">
        <a class="small" href="#" data-bs-toggle="modal" data-bs-target="#forgot_pass">{$lang['forgot_password_link']}</a>
    </div>
    HTML;
    }
    
    // Registration block
    $registerBlock = '';
    if ($system_settings['USER_selfRegister'] == '1') {
        $registerBlock = <<<HTML
    <div class="text-center">
        <a class="small" href="/register.php">{$lang['create_account_link']}</a>
    </div>
    HTML;
    }
    
    // Final HTML output
    return <<<HTML
    {$googleAnalytics}
    <div class="row">
        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
        <div class="col-lg-6">
            <div class="p-5">
                <div class="text-center">
                    <h1 class="h4 mb-4">{$lang['sign_in_title']}</h1>
                </div>
                <div id="msg"></div>
                <div class="user" id="login_form">
                    {$ssoBlock}
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="login_email" placeholder="{$lang['email_placeholder']}">
                        <label for="login_email">{$lang['email_label']}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="login_pass" placeholder="{$lang['password_placeholder']}">
                        <label for="login_pass">{$lang['password_label']}</label>
                    </div>
                    <div class="form-group"></div>
                    <button class="btn btn-primary btn-user btn-block" id="login_btn">
                        {$lang['sign_in_button']}
                    </button>
                </div>
                {$forgotPassBlock}
                {$registerBlock}
            </div>
        </div>
    </div>
    {$forgotPasswordModal}
    {$ssoScript}
    HTML;
    
}
?>


<?php
if (!defined('pvault_panel')){ die('Not Found');}

require_once ('Mail.php');


function sendMail($destination, $subject, $content) {
    global $system_settings;
    
   $smtp_host = $system_settings['EMAIL_smtp_host'];
    $smtp_port = $system_settings['EMAIL_smtp_port'];
    $smtp_user = $system_settings['EMAIL_smtp_user'];
    $smtp_pass = $system_settings['EMAIL_smtp_pass'];
    $from = $system_settings['EMAIL_from'];
    $display_name = $system_settings['EMAIL_from_display_name'];
    $smtp_secure = ($system_settings['EMAIL_smtp_secure'] == 1) ? 'ssl' : null; // Enable SSL if 1, disable if 0

    $headers = array(
        'From' => "$display_name <$from>",
        'Reply-To' => $from,
        'Subject' => $subject,
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/html; charset=UTF-8'
    );

    $smtp_params = array(
        'host' => $smtp_host,
        'port' => $smtp_port,
        'secure' => $smtp_secure
    );

    if (!empty($smtp_user) && !empty($smtp_pass)) {
        $smtp_params['auth'] = true;
        $smtp_params['username'] = $smtp_user;
        $smtp_params['password'] = $smtp_pass;
    } else {
        $smtp_params['auth'] = false;
    }

    $smtp = Mail::factory('smtp', $smtp_params);

    $mail = $smtp->send($destination, $headers, $content);

    if (PEAR::isError($mail)) {
        error_log($mail->getMessage());
        return false;
    } else {
        return true;
    }
}


function welcomeNewUser($userName, $userEmail, $userToken){
    global $conn, $system_settings;
    define('__ROOT__', dirname(dirname(__FILE__)));

    $content = file_get_contents(__ROOT__.'/emailTemplates/newUserWelcome.html');

    $regLink1 = "https://www.perfumersvault.com/";
    $regLink2 = $system_settings['USER_terms_url'];
    $regLink3 = "https://www.perfumersvault.com/privacy-policy/";
    $regConfirm = $system_settings['SYSTEM_server_url']."/login.php?do=confirm-email&token=$userToken"; ;

    $serverurl = $system_settings['SYSTEM_server_url'];
    $brandinglogo = 'data:image/png;base64,' . base64_encode(file_get_contents(__ROOT__.'/img/logo_def.png'));

    $mail = str_replace('__NAME__', $userName, $content);
    $mail = str_replace('__CONFIRMATION_LINK__', $regConfirm, $mail);
    $mail = str_replace('__LINK1__', $regLink1, $mail);
    $mail = str_replace('__LINK2__', $regLink2, $mail);
    $mail = str_replace('__LINK3__', $regLink3, $mail);
    $mail = str_replace('__SERVER_URL__', $serverurl, $mail);
    $mail = str_replace('__BRANDING_LOGO__', $brandinglogo, $mail);

    $sendMail = sendMail($userEmail, "Welcome to Perfumers Vault", $mail);
	
	return $sendMail;
}

function sendPasswordResetEmail($userEmail, $userToken){
    global $conn, $system_settings;

    $userID = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM users WHERE email = '$userEmail'"))['id'];
    $branding = mysqli_fetch_array(mysqli_query($conn, "SELECT brandLogo FROM branding WHERE owner_id = '$userID'"));
    $brandinglogo = !empty($branding['brandLogo']) ? $branding['brandLogo'] : 'data:image/png;base64,' . base64_encode(file_get_contents(__ROOT__.'/img/logo_def.png')); 
 
    $resetLink = $system_settings['SYSTEM_server_url'].'/login.php?do=reset-password&token='.$userToken;
    
    $mail = file_get_contents(__ROOT__ . '/emailTemplates/userPasswordReset.html');
    $mail = str_replace('__NAME__', $userEmail, $mail);
    $mail = str_replace('__RESET_LINK__', $resetLink, $mail);
    $mail = str_replace('__BRANDING_LOGO__', $brandinglogo, $mail);

    // Send the email with the reset link
    $sendMail = sendMail($userEmail, "Password Reset Request", $mail);

    return $sendMail;
}

function notifyAdminForNewUser($userName, $userEmail, $userAction) {
    global $conn, $system_settings;

    $brandinglogo = 'data:image/png;base64,' . base64_encode(file_get_contents(__ROOT__.'/img/logo_def.png'));

    $mail = file_get_contents(__ROOT__.'/emailTemplates/newUserAdmin.html');
    $mail = str_replace('__USER_NAME__', $userName, $mail);
    $mail = str_replace('__USER_EMAIL__', $userEmail, $mail);
    $mail = str_replace('__USER_ACTION__', $userAction, $mail);
    $mail = str_replace('__BRANDING_LOGO__', $brandinglogo, $mail);

    $adminQuery = mysqli_query($conn, "SELECT email FROM users WHERE role = 1");
    while ($admin = mysqli_fetch_assoc($adminQuery)) {
        $adminEmail = $admin['email'];
        sendMail($adminEmail, "New user just $userAction", $mail);
    }
}

function userGoodbye($userName, $userEmail){
    global $conn, $system_settings;
    $brandinglogo = 'data:image/png;base64,' . base64_encode(file_get_contents(__ROOT__.'/img/logo_def.png'));

	$mail = file_get_contents(__ROOT__.'/emailTemplates/userGoodbye.html');
	$mail = str_replace('__NAME__', $userName, $mail);
    $mail = str_replace('__BRANDING_LOGO__', $brandinglogo, $mail);

	$sendMail = sendMail($userEmail, "PV Online account deleted", $mail);
	
    return $sendMail;
}
?>
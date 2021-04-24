<?php
define('pvault_panel', TRUE);

if(file_exists('./inc/config.php') == FALSE){

	require 'install.php';
	
}else{

session_start();
if(isset($_SESSION['parfumvault'])){
	header('Location: index.php');
}

require_once('inc/config.php');
require_once('inc/opendb.php');
require_once('inc/product.php');
if($_GET['register'] && $_POST['regUser'] && $_POST['regPass'] && $_POST['regFullName'] && $_POST['regEmail']){
	$ruser = mysqli_real_escape_string($conn,$_POST['regUser']);
	$rpass = mysqli_real_escape_string($conn,$_POST['regPass']);
	$rfname = mysqli_real_escape_string($conn,$_POST['regFullName']);
	$remail = mysqli_real_escape_string($conn,$_POST['regEmail']);
	if(strlen($_POST['regPass']) < '5'){
		$msg ='<div class="alert alert-danger alert-dismissible"><strong>Error: </strong>Password must be at least 5 characters long!</div>';
	}else{
		if(mysqli_query($conn,"INSERT INTO users (username,password,fullName,email) VALUES ('$ruser', PASSWORD('$rpass'),'$rfname','$remail')")){
			header('Location: login.php');
		}else{
			$msg = '<div class="alert alert-danger alert-dismissible">Failed to register the user</div>';
		}
	}
	
}
if($_POST['username'] && $_POST['password']){
	$_POST['username'] = mysqli_real_escape_string($conn,$_POST['username']);
	$_POST['password'] = mysqli_real_escape_string($conn,$_POST['password']);
	
	$row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM users WHERE username='".$_POST['username']."' AND password=PASSWORD('".$_POST['password']."')"));

	if($row['id']){	// If everything is OK login
			$_SESSION['parfumvault'] = true;
			$_SESSION['userID'] = $row['id'];
			header('Location: index.php');
	}else{
		$msg = '<div class="alert alert-danger alert-dismissible">Username or password error</div>';
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

  <link href="css/sb-admin-2.css" rel="stylesheet">
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
             <?php if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users")) == 0){?>
              <div class="col-lg-6 d-none d-lg-block bg-register-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Please register a user!</h1>
                  </div>
                  <?php echo $msg; ?>
                   <form action="?register=1" method="post" enctype="multipart/form-data" target="_self" class="user" id="register">
                    <hr>
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" name="regFullName"  value="<?php echo $_POST['regFullName'];?>" placeholder="Your full name...">
                    </div>      
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" name="regEmail"  value="<?php echo $_POST['regEmail'];?>" placeholder="Your email...">
                    </div>  
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" name="regUser"  value="<?php echo $_POST['regUser'];?>" placeholder="Username...">
                    </div>
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" name="regPass" placeholder="Password...">
                    </div>
                    <div class="form-group"></div>
                    <button class="btn btn-primary btn-user btn-block">
                      Register
                    </button>
                  </form>
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
                      <input type="text" class="form-control form-control-user" name="username"  placeholder="Username...">
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" name="password" placeholder="Password...">
                    </div>
                    <div class="form-group"></div>
                    <button class="btn btn-primary btn-user btn-block">
                      Login
                    </button>
                  </form>
                  <?php } ?>
		 		  <hr>
                  <div class="text-center">
				  <label class="badge">Version: <?php echo $ver; ?> | <?php echo $product; ?></label>
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
<?php } ?>

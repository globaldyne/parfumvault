<?php
define('pvault_panel', TRUE);

if(file_exists('./inc/config.php') == FALSE){

	require 'install.php';
	
}else{

session_start();
if(isset($_SESSION['parfumvault'])){
	header('Location: /');
}

require_once('inc/config.php');
require_once('inc/opendb.php');
require_once('inc/product.php');

if($_POST['username'] && $_POST['password']){
	$_POST['username'] = mysqli_real_escape_string($conn,$_POST['username']);
	$_POST['password'] = mysqli_real_escape_string($conn,$_POST['password']);
	
	$row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM users WHERE username='{$_POST['username']}' AND password=PASSWORD('".$_POST['password']."')"));

	if($row['id']){	// If everything is OK login
			$_SESSION['parfumvault'] = true;
			$_SESSION['userID'] = $row['id'];
			header('Location: /');
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
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
              <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                  </div>
                  <?php echo $msg; ?>
                  <form method="post" enctype="multipart/form-data" class="user">
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
		 <hr>
		<label>Version: <?php echo $ver; ?> | <?php echo $product; ?></label>
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

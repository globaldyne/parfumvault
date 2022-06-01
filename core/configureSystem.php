<?php
define('__ROOT__', dirname(dirname(__FILE__)));

function pvPost($url, $data){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    return $response = curl_exec($curl);
}
$pvOnlineAPI = 'https://online.jbparfum.com/api2.php';

if($_POST['action'] == 'create_pv_account'){
	define('__ROOT__', dirname(dirname(__FILE__))); 
	define('pvault_panel', TRUE);

	require_once(__ROOT__.'/inc/config.php');
	require_once(__ROOT__.'/inc/opendb.php');
	
	if(!$_POST['password'] || !$_POST['fullName'] || !$_POST['email']){
		$response['error'] = "Missing required data";
		echo json_encode($response);
		return;
	}
	
	$password = mysqli_real_escape_string($conn,$_POST['password']);
	$fullName = mysqli_real_escape_string($conn,$_POST['fullName']);
	$email = mysqli_real_escape_string($conn,$_POST['email']);
	$app_ver = trim(file_get_contents(__ROOT__.'/VERSION.md'));

	$data = [ 'do' => 'regUser','email' => strtolower($_POST['email']), 'fullName' => $_POST['fullName'], 'userPass' => base64_encode($_POST['password']), 'ver'=> $app_ver];
	$r = json_decode(pvPost($pvOnlineAPI, $data));
	if($r->error){			
		$response['error'] = $r->error;
		echo json_encode($response);
		return;
	}
		
	if($r->success){
		mysqli_query($conn,"DELETE FROM pv_online");
		mysqli_query($conn,"INSERT INTO pv_online (enabled) VALUES ('1')");
		$response['success'] = $r->success;
		echo json_encode($response);
		return;
	}

	return;
}

if($_POST['action'] == 'register'){
	define('__ROOT__', dirname(dirname(__FILE__))); 
	define('pvault_panel', TRUE);

	require_once(__ROOT__.'/inc/config.php');
	require_once(__ROOT__.'/inc/opendb.php');
	
	if(!$_POST['password'] || !$_POST['fullName'] || !$_POST['email']){
		$response['error'] = "All fields required";
		echo json_encode($response);
		return;
	}
	
	$password = mysqli_real_escape_string($conn,$_POST['password']);
	$fullName = mysqli_real_escape_string($conn,$_POST['fullName']);
	$email = mysqli_real_escape_string($conn,$_POST['email']);
	$app_ver = trim(file_get_contents(__ROOT__.'/VERSION.md'));

	if($_POST['createPVOnline'] == 'true'){
		$data = [ 'do' => 'regUser','email' => strtolower($_POST['email']), 'fullName' => $_POST['fullName'], 'userPass' => base64_encode($_POST['password']), 'ver'=> $app_ver];
		$r = json_decode(pvPost('https://online.jbparfum.com/api2.php', $data));
		if($r->error){			
			$response['error'] = 'Error creating a PV Online account '.$r->error;
			echo json_encode($response);
			return;
		}
		
		if($r->success){
			mysqli_query($conn,"DELETE FROM pv_online");
			mysqli_query($conn,"INSERT INTO pv_online (enabled) VALUES ('1')");
		}
	}
	
	if(strlen($_POST['password']) < '5'){
		$response['error'] = "Password must be at least 5 characters long!";
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn,"INSERT INTO users (email,password,fullName) VALUES ('$email', '$password','$fullName')")){
		$db_ver  = trim(file_get_contents(__ROOT__.'/db/schema.ver'));
		mysqli_query($conn,"INSERT INTO pv_meta (schema_ver,app_ver) VALUES ('$db_ver','$app_ver')");
		
		$response['success'] = "User created";
		echo json_encode($response);
	}else{
		$response['error'] = 'Failed to register local user '.mysqli_error($conn);
		echo json_encode($response);
	}
	
	return;
}

if($_POST['action']=='install'){
	
	if(file_exists(__ROOT__.'/inc/config.php') == TRUE){
		echo '<div class="alert alert-info alert-dismissible"><strong>System is already configured!</strong></div>';
		return;
	}

	if(strlen($_POST['password']) < '5'){
		echo '<div class="alert alert-danger alert-dismissible"><strong>Error: </strong>Password must be at least 5 characters long!</div>';
		return;
	}
	
	if(!$_POST['dbhost'] || !$_POST['dbuser'] || !$_POST['dbpass'] || !$_POST['dbname'] || !$_POST['fullName'] || !$_POST['email']){
		echo '<div class="alert alert-danger alert-dismissible"><strong>Error: </strong>All fields are required.</div>';
		return;
	}
	
	if ( ! is_writable(dirname(__FILE__))) {
		echo '<div class="alert alert-danger alert-dismissible"><strong>Error: </strong>Home directory isn\'t writable.<p>Please refer to our <a href="https://www.jbparfum.com/knowledge-base/" target="_blank">KB</a> for help.</p></div>';
		return;
	}
	
		
	if(!$link = mysqli_connect($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpass'], $_POST['dbname'])){
		echo '<div class="alert alert-danger alert-dismissible">Error connecting to the database, make sure the details provided are correct, the database exists and the user has full permissions on it.</div>';
		return;
	}
	

	if($_POST['createPVOnline'] == 'true'){
		$data = [ 'do' => 'regUser','email' => strtolower($_POST['email']), 'fullName' => $_POST['fullName'], 'userPass' => base64_encode($_POST['password']) ];
		$r = json_decode(pvPost('https://online.jbparfum.com/api2.php', $data));
		if($r->error){
			
			echo '<div class="alert alert-danger alert-dismissible">Error creating a PV Online account '.$r->error.'</div>';
		}
		if($r->success){
			$pvOnMsg = '<div class="alert alert-success alert-dismissible">PV Online account created</div>';
		}
		return;
	}
	
	$cmd = 'mysql -u'.$_POST['dbuser'].' -p'.$_POST['dbpass'].' -h'.$_POST['dbhost'].' '.$_POST['dbname'].' < ../db/pvault.sql'; 
	passthru($cmd,$e);
	if(!$e){
		mysqli_query($link,"INSERT INTO users (id,email,password,fullName) VALUES ('1','".strtolower($_POST['email'])."','".$_POST['password']."','".$_POST['fullName']."')");
		
		$conf = '<?php
//AUTO GENERATED BY INSTALLATION WIZARD
if (!defined("pvault_panel")){ die("Not Found");}
$dbhost = "'.$_POST['dbhost'].'"; //MySQL Hostname
$dbuser = "'.$_POST['dbuser'].'"; //MySQL Username
$dbpass = "'.$_POST['dbpass'].'"; //MySQL Password
$dbname = "'.$_POST['dbname'].'"; //MySQL DB name


$uploads_path = "uploads/";
$tmp_path = "tmp/";
$allowed_ext = "pdf, doc, docx, xls, csv, xlsx, png, jpg, jpeg, gif";
$max_filesize = "4194304"; //in bytes
?>
';
	}else{
		echo '<div class="alert alert-danger alert-dismissible">DB Creation error. Make sure the database exists in your mysql server and its empty.</div>';
		return;
	}
	
	
	if(file_exists('/config/.DOCKER') == TRUE){
		$cfg = '/config/config.php';	
	}else{
		$cfg = __ROOT__.'/inc/config.php';
	}

	if(file_put_contents($cfg, $conf) == FALSE){
		echo '<div class="alert alert-danger alert-dismissible">Error: failed to create config file <strong>'.$cfg.'</strong><p> Make sure your web server has write permissions to the install directory.</p></div>';
		return;
	}
	
	$app_ver = trim(file_get_contents(__ROOT__.'/VERSION.md'));
	$db_ver  = trim(file_get_contents(__ROOT__.'/db/schema.ver'));
	mysqli_query($link,"INSERT INTO pv_meta (schema_ver,app_ver) VALUES ('$db_ver','$app_ver')");
		
	echo '<div class="alert alert-success alert-dismissible">System configured, '.$pvOnMsg.'</div>';
	return;
}
?>

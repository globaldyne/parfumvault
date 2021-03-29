<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/fixIFRACas.php');

if($_GET['do'] == 'backupDB'){
	
	$file = 'backup-'.date("d-m-Y").'.sql.gz';
	$mime = "application/x-gzip";
	
	header( 'Content-Type: '.$mime );
	header( 'Content-Disposition: attachment; filename="' .$file. '"' );
	
	$cmd = "mysqldump -u $dbuser --password=$dbpass $dbname | gzip --best";   
	passthru($cmd);
	
}elseif($_GET['do'] == 'backupFILES'){
	
	$file = 'backup-'.date("d-m-Y").'.files.gz';
	$mime = "application/x-gzip";
	
	$cmd = "tar -czvf ../$tmp_path$file ../$uploads_path";   
	shell_exec($cmd);
	
	header( 'Content-Type: '.$mime );
	header( 'Content-Disposition: attachment; filename="' ."tmp/".$file. '"' );
	
	
	
}elseif(isset($_POST['restore'])){
	if($_FILES["file"]["size"] > 0){

		$file_tmp = escapeshellcmd($_FILES["file"]["tmp_name"]);   
		$cmd = "mysql -u$dbuser -p$dbpass $dbname < $file_tmp"; 
		passthru($cmd,$e);
				
		if(!$e){
			if(isset($_SESSION['parfumvault'])) {
				unset($_SESSION['parfumvault']);
			}
			session_unset();
			echo 'Database has been restored. Please close the window and login again for the changes to take effect.';
			return;
			//header('Location: login.php');
		}else{
			header("Location: maintenance.php?do=restoreDB&err=1");
		}
	}else{

		header("Location: maintenance.php?do=restoreDB&err=2");
	}
		  
}elseif($_GET['do'] == 'restoreDB'){ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Import CSV file</title>
<link href="../css/sb-admin-2.css" rel="stylesheet">
  
<link href="../css/bootstrap-select.min.css" rel="stylesheet">
</head>

<body>
    <div id="wrap">
        <div class="container">
      
<form action="" method="post" enctype="multipart/form-data" name="restoredb" target="_self">
             <table width="100%" border="0">
                              <tr>
                                <td colspan="2" class="badge-primary">Restore DB</td>
                              </tr>
                              <tr>
                                <td colspan="2"><?php 
								if($_GET['err'] == '0'){
									echo '<div class="alert alert-success alert-dismissible"><strong>Success: </strong>Database restored!</div>';
								}elseif($_GET['err'] == '1'){
									echo '<div class="alert alert-danger alert-dismissible"><strong>Error: </strong>import failed, check your sql file!</div>';
								}elseif($_GET['err'] == '2'){
									echo '<div class="alert alert-danger alert-dismissible"><strong>Error: </strong>invalid file!</div>';
								}
		
								?></td>
                              </tr>
                              <tr>
                                <td width="21%">Choose file:</td>
                                <td width="79%"><span class="col-md-4">
                                  <input type="file" name="file" id="file" class="input-large" />
                                </span></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td><input type="submit" name="restore" id="restore" value="Restore" /></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td colspan="2"><p class="alert-link">Notes:</p>
                                <p class="bg-gray-100">Its <strong>important</strong> the backup you are restoring is the same version as your PV version<br/>
                                After a succesfull backup you will be automatically logged out</p></td>
                              </tr>
                            </table>
</form>
<?php }elseif($_GET['do'] == 'IFRA'){ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Maintenance</title>
<link href="../css/sb-admin-2.css" rel="stylesheet">
  
<link href="../css/bootstrap-select.min.css" rel="stylesheet">
</head>

<body>
    <div id="wrap">
        <div class="container">
<form action="" method="post" enctype="multipart/form-data" name="importIFRA" target="_self">
             <table width="100%" border="0">
                              <tr>
                                <td colspan="2" class="badge-primary">Import IFRA Library</td>
                              </tr>
                              <tr>
                                <td colspan="2"><?php 
								if($_GET['err'] == '0'){
									echo '<div class="alert alert-success alert-dismissible"><strong>Success: </strong>Library imported!</div>';
								}elseif($_GET['err'] == '1'){
									echo '<div class="alert alert-danger alert-dismissible"><strong>Error: </strong>import failed!</div>';
								}elseif($_GET['err'] == '2'){
									echo '<div class="alert alert-danger alert-dismissible"><strong>Error: </strong>invalid file!</div>';
								}
		
								?></td>
                              </tr>
                              <tr>
                                <td width="21%">Choose file:</td>
                                <td width="79%"><span class="col-md-4">
                                  <input type="file" name="IFRAfile" id="IFRAfile" class="input-large" />
                                </span></td>
                              </tr>
                              <tr>
                                <td height="46">Manipulate file:</td>
                                <td><input name="updateCAS" type="checkbox" id="updateCAS" value="1" />
                                  <span class="font-italic">*this is required if you are importing the original IFRA file</span></td>
                              </tr>
                              <tr>
                                <td><input type="submit" name="import_ifra" id="import" value="Import" /></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td colspan="2"><hr>
                                  <p class="alert-link">IMPORTANT:</p>
<p class="alert-link"> This operation will wipe out any data already in your IFRA Library, so please make sure the file you uploading is in the right format and have taken a <a href="maintenance.php?do=backupDB">backup</a> before.</p>
                                <p class="alert-link">The IFRA xls can be downloaded from its official <a href="https://ifrafragrance.org/safe-use/standards-guidance" target="_blank">web site</a></p></td>
                 </tr>
                            </table>
</form>
 </div>
</div>
</body>
</html>
<?php }?>

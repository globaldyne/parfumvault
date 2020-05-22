<?php require('../inc/sec.php');?>

<?php
require_once('../inc/config.php');
require_once('../inc/opendb.php');
if($_GET['do'] == 'backupDB'){
	
	$file = 'backup-'.date("d-m-Y").'.sql';
	$mime = "text/sql";
	
	header( 'Content-Type: '.$mime );
	header( 'Content-Disposition: attachment; filename="' .$file. '"' );
	
	$cmd = "mysqldump -u $dbuser --password=$dbpass $dbname";   
	passthru($cmd);
	
}elseif(isset($_POST['restore'])){
	if($_FILES["file"]["size"] > 0){

		$file_tmp = escapeshellcmd($_FILES["file"]["tmp_name"]);   
		$cmd = "mysql -u$dbuser -p$dbpass $dbname < $file_tmp"; 
		passthru($cmd,$e);
				
		if(!$e){
			header("Location: /pages/maintenance.php?do=restoreDB&err=0");
		}else{
			header("Location: /pages/maintenance.php?do=restoreDB&err=1");
		}
	}else{

		header("Location: /pages/maintenance.php?do=restoreDB&err=2");
	}

}elseif(isset($_POST['import_ifra'])){
	$filename=$_FILES["file"]["tmp_name"];    
	if($_FILES["file"]["size"] > 0){
	require_once('../func/SimpleXLSX.php');

	$xlsx = SimpleXLSX::parse($filename);

	try {
       $conn = new PDO( "mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
       $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
        echo $sql . "<br>" . $e->getMessage();
    }

	$f = 'ifra_key,image,amendment,prev_pub,last_pub,deadline_existing ,deadline_new,name,cas,cas_comment,synonyms,formula,flavor_use,prohibited_notes,restricted_photo_notes,restricted_notes,specified_notes,type,risk,contrib_others,contrib_others_notes,cat1,cat2,cat3,cat4,cat5A ,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12';
	//$fe = explode(',' , $f);
	//$fn = count($fe);
	$v = substr(str_repeat('?,', count(explode(',' , $f))), 0 , strlen($x) - 1);



	$stmt = $conn->prepare( "INSERT INTO IFRALibrary3 ($f) VALUES ($v)");
    
		
	$stmt->bindParam( 1, $ifra_key);
	$stmt->bindParam( 2, $image);
	$stmt->bindParam( 3, $amendment);
	$stmt->bindParam( 4, $prev_pub);
	$stmt->bindParam( 5, $last_pub);
	$stmt->bindParam( 6, $deadline_existing);
	$stmt->bindParam( 7, $deadline_new);
	$stmt->bindParam( 8, $name);
	$stmt->bindParam( 9, $cas);
	$stmt->bindParam( 10, $cas_comment);
	$stmt->bindParam( 11, $synonyms);
	$stmt->bindParam( 12, $formula);
	$stmt->bindParam( 13, $flavor_use);
	$stmt->bindParam( 14, $prohibited_notes);
	$stmt->bindParam( 15, $restricted_photo_notes);
	$stmt->bindParam( 16, $restricted_notes);
	$stmt->bindParam( 17, $specified_notes);
	$stmt->bindParam( 18, $type);
	$stmt->bindParam( 19, $risk);
	$stmt->bindParam( 20, $contrib_others);
	$stmt->bindParam( 21, $contrib_others_notes);
	$stmt->bindParam( 22, $cat1);
	$stmt->bindParam( 23, $cat2);
	$stmt->bindParam( 24, $cat3);
	$stmt->bindParam( 25, $cat4);
	$stmt->bindParam( 26, $cat5A);
	$stmt->bindParam( 27, $cat5B);
	$stmt->bindParam( 28, $cat5C);
	$stmt->bindParam( 29, $cat5D);
	$stmt->bindParam( 30, $cat6);
	$stmt->bindParam( 31, $cat7A);
	$stmt->bindParam( 32, $cat7B);
	$stmt->bindParam( 33, $cat8);
	$stmt->bindParam( 34, $cat9);
	$stmt->bindParam( 35, $cat10A);
	$stmt->bindParam( 36, $cat10B);
	$stmt->bindParam( 37, $cat11A);
	$stmt->bindParam( 38, $cat11B);
	$stmt->bindParam( 39, $cat12);

    foreach ($xlsx->rows() as $fields){
		$x[] = $fields;
		$ifra_key = $fields[0];
        $image = $fields[1];
        $amendment = $fields[2];
        $prev_pub = $fields[3];
        $last_pub = $fields[4];
        $deadline_existing = $fields[5];
        $deadline_new = $fields[6];
        $name = $fields[7];
        $cas = $fields[8];
        $cas_comment = $fields[9];
        $synonyms = $fields[10];
        $formula = $fields[11];
        $flavor_use = $fields[12];
        $prohibited_notes = $fields[13];
        $restricted_photo_notes = $fields[14];
        $restricted_notes = $fields[15];
        $specified_notes = $fields[16];
        $type = $fields[17];
        $risk = $fields[18];
		$contrib_others = $fields[19];
        $contrib_others_notes = $fields[20];
        $cat1 = $fields[21];
        $cat2 = $fields[22];
        $cat3 = $fields[23];
        $cat4 = $fields[24];
        $cat5A = $fields[25];
        $cat5B = $fields[26];
        $cat5C = $fields[27];
        $cat5D = $fields[28];
        $cat6 = $fields[29];
        $cat7A = $fields[30];
        $cat7B = $fields[31];
        $cat8 = $fields[32];
        $cat9 = $fields[33];
        $cat10A = $fields[34];
        $cat10B = $fields[35];
        $cat11A = $fields[36];
        $cat11B = $fields[37];
        $cat12 = $fields[38];

		$stmt->execute();

       // $stmt->debugDumpParams();
    }
	
if($stm){
		//header("Location: /pages/maintenance.php?do=IFRA&err=0");
	}else{
		//header("Location: /pages/maintenance.php?do=IFRA&err=1");
	}

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
                                <p class="bg-gray-100">File must be a valid sql backup</p></td>
                              </tr>
                            </table>
</form>
<?php }elseif($_GET['do'] == 'IFRA'){ ?>
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
                                  <input type="file" name="file" id="file" class="input-large" />
                                </span></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
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
                                <td colspan="2"><p class="alert-link">Notes:</p>
                                <p class="bg-gray-100">&nbsp;</p></td>
                              </tr>
                            </table>
</form>
 </div>
</div>
</body>
</html>
<?php }?>
<?php
require('../inc/sec.php');

require '../inc/config.php';
require '../inc/opendb.php';
require '../inc/product.php';

if(isset($_POST["import"])){
	$i = 0;
	$filename=$_FILES["file"]["tmp_name"];    
	if($_FILES["file"]["size"] > 0){
		$file = fopen($filename, "r");
		while (($data = fgetcsv($file, 10000, ",")) !== FALSE){
			if(!mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '".trim(ucwords($data['0']))."'"))){
				if(mysqli_query($conn, "INSERT INTO ingredients (name, cas, odor, profile, category, IFRA, supplier) VALUES ('".trim(ucwords($data['0']))."', '".trim($data['1'])."', '".trim($data['2'])."', '".trim($data['3'])."', '".trim($data['4'])."', '".preg_replace("/[^0-9.]/", "", $data['5'])."', '".trim($data['6'])."')")){
					$i++;
					$msg='<div class="alert alert-success alert-dismissible">'.$i.' Ingredients imported</div>';
				}else{
					$msg='<div class="alert alert-danger alert-dismissible">Failed to import the ingredients list.</div>';
				}
			}
		}		
	}
	fclose($file);  


}  
?>
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
           <form action="" method="post" enctype="multipart/form-data" name="upload_csv" target="_self">
                <table width="100%" border="0">
                              <tr>
                                <td colspan="2" class="badge-primary">Import ingredients CSV file</td>
                              </tr>
                              <tr>
                                <td colspan="2"><?php echo $msg; ?></td>
                              </tr>
                              <tr>
                                <td width="21%">Choose file:</td>
                                <td width="79%"><span class="col-md-4">
                                  <input type="file" name="file" id="file" class="form-control" />
                                </span></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td><input type="submit" name="import" id="import" value="Import" /></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td colspan="2"><p class="alert-link">Make sure your CSV file follows the guidelines as documented <a href="https://www.jbparfum.com/knowledge-base/3-ingredients-import-csv" target="_blank">here</a>. </p>
                                <p class="alert-link">Its highly recommended to take a backup of your database before you attempt any import.</p></td>
                              </tr>
                            </table>
    </form>
 </div>
</div>
</body>
</html>

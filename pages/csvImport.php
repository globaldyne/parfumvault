<?php
require '../inc/config.php';
require '../inc/opendb.php';

if(isset($_POST["import"]) && ($_POST['name'])){
	$name = mysqli_real_escape_string($conn,$_POST['name']);
	$profile = mysqli_real_escape_string($conn,$_POST['profile']);
	 if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulas WHERE name = '$name'"))){
		 $msg='<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
				<strong>Error: </strong>'.$name.' already exists! Click <a href="/?do=Formula&name='.$name.'">here</a> to view/edit!</div>';
	  }else{

		$filename=$_FILES["file"]["tmp_name"];    
		if($_FILES["file"]["size"] > 0){
			$file = fopen($filename, "r");
			while (($data = fgetcsv($file, 10000, ",")) !== FALSE){
				if(!mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$data[0]'"))){
					mysqli_query($conn, "INSERT INTO ingredients (name, ml) VALUES ('$data[0]', '10')");
				}
				if(empty($data['1'])){
					$data['1'] = '100';
				}
				$sql = "INSERT INTO formulas (name,ingredient,concentration,quantity) VALUES ('$name','$data[0]','$data[1]','$data[2]')";
				$res = mysqli_query($conn, $sql);
					
			}
			if($res){
				mysqli_query($conn, "INSERT INTO formulasMetaData (name,notes,profile) VALUES ('$name','Imported via csv','$profile')");
				$msg='<div class="alert alert-success alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
				<strong>'.$name.'</strong> added!</div>';
			}else{
				$msg='<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
				<strong>Error adding: </strong>'.$name;
			}
			fclose($file);  
		}
	 }
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
                                <td colspan="2" class="badge-primary">Import CSV</td>
                              </tr>
                              <tr>
                                <td colspan="2"><?php echo $msg; ?></td>
                              </tr>
                              <tr>
                                <td>Name:</td>
                                <td><input type="text" name="name" id="name" class="form-control"/></td>
                              </tr>
                              <tr>
                                <td>Profile:</td>
                                <td>
                                <select name="profile" id="profile" class="form-control">
                                        <option value="oriental">Oriental</option>
                                        <option value="woody">Woody</option>
                                        <option value="floral">Floral</option>
                                        <option value="fresh">Fresh</option>
                                 </select>
                                </td>
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
                                <td colspan="2"><p class="alert-link">Notes:</p>
                                <p>*If your CSV file contains an ingredient which is not already in your inventory, then will automatically create it using the minimum values.</p>
                                <p>*CSV delimeter: ','</p>
                                <p>*CSV format:</p>
                                <p>ingrdedient,concentration,quantity</p>
                                <p>eg: </p>
                                <p>Ambroxan,10,0.15</p></td>
                              </tr>
                            </table>
    </form>
 </div>
</div>
</body>
</html>
<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/inc/settings.php');


if($_GET['type'] == 'SDS' && $_GET['ingredient_id']){
	
	$ingID = mysqli_real_escape_string($conn, $_GET['ingredient_id']);
	
	if(isset($_FILES['SDS']['name'])){
      $file_name = $_FILES['SDS']['name'];
      $file_size = $_FILES['SDS']['size'];
      $file_tmp = $_FILES['SDS']['tmp_name'];
      $file_type = $_FILES['SDS']['type'];
      $file_ext = strtolower(end(explode('.',$_FILES['SDS']['name'])));
	  
	  if (file_exists('../'.$uploads_path.'SDS/') === FALSE) {
    	mkdir('../'.$uploads_path.'SDS/', 0740, true);
	  }

	  $ext = explode(', ', $allowed_ext);
	  
      if(in_array($file_ext,$ext)=== false){
		 echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file.</div>';
      }elseif($file_size > $max_filesize){
		 echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>File upload error: </strong>File size must not exceed '.formatBytes($max_filesize).'</div>';
      }else{
	  
         if(move_uploaded_file($file_tmp,'../'.$uploads_path.'SDS/'.base64_encode($file_name))){
		 	$SDSF = $uploads_path.'SDS/'.base64_encode($file_name);
		 	if(mysqli_query($conn, "UPDATE ingredients SET SDS = '$SDSF' WHERE name='$ingID'")){
		 		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>SDS File uploaded</strong></div>';
			}
		 }
	  }
   }
	
	return;	
}


if($_GET['type'] == 'brand'){
		
	if(isset($_FILES['brandLogo']['name'])){
      $file_name = $_FILES['brandLogo']['name'];
      $file_size = $_FILES['brandLogo']['size'];
      $file_tmp = $_FILES['brandLogo']['tmp_name'];
      $file_type = $_FILES['brandLogo']['type'];
      $file_ext = strtolower(end(explode('.',$_FILES['brandLogo']['name'])));
	  
	  if (file_exists('../'.$uploads_path.'logo/') === FALSE) {
    	mkdir('../'.$uploads_path.'logo/', 0740, true);
	  }

	  $ext = explode(', ', $allowed_ext);
	  
      if(in_array($file_ext,$ext)=== false){
		 echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file.</div>';
      }elseif($file_size > $max_filesize){
		 echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>File upload error: </strong>File size must not exceed '.formatBytes($max_filesize).'</div>';
      }else{
	  
         if(move_uploaded_file($file_tmp,'../'.$uploads_path.'logo/'.base64_encode($file_name))){
		 	$brandLogoF = $uploads_path.'logo/'.base64_encode($file_name);
		 	if(mysqli_query($conn, "UPDATE settings SET brandLogo = '$brandLogoF'")){
		 		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Brand logo uploaded</strong></div>';
			}
		 }
	  }
   }
	
	return;	
}

if($_GET['type'] == 'ingCSVImport'){
	$defCatClass = $settings['defCatClass'];

	if(isset($_FILES['ingCSV']['name'])){
		$i = 0;
		$filename=$_FILES['ingCSV']['tmp_name'];    
		if($_FILES['ingCSV']['size'] > 0){
			$file = fopen($filename, "r");
			while (($data = fgetcsv($file, 10000, ",")) !== FALSE){
				if(!mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '".trim(ucwords($data['0']))."'"))){
					if(mysqli_query($conn, "INSERT INTO ingredients (name, cas, odor, profile, category, $defCatClass, supplier) VALUES ('".trim(ucwords($data['0']))."', '".trim($data['1'])."', '".trim($data['2'])."', '".trim($data['3'])."', '".trim($data['4'])."', '".preg_replace("/[^0-9.]/", "", $data['5'])."', '".trim($data['6'])."')")){
						$i++;
						echo '<div class="alert alert-success alert-dismissible">'.$i.' Ingredients imported</div>';
					}else{
						echo '<div class="alert alert-danger alert-dismissible">Failed to import the ingredients list.</div>';
					}
				}
			}		
		}
		fclose($file);  
	
	}  

	return;
}

if($_GET['type'] == 'frmCSVImport'){
	$name = mysqli_real_escape_string($conn,trim($_GET['name']));
	$fid = base64_encode($name);

	$profile = mysqli_real_escape_string($conn,$_GET['profile']);
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$name.' already exists! Click <a href="?do=Formula&name='.$name.'">here</a> to view/edit!</div>';
	  	return;
	 }

		$filename=$_FILES["CSVFile"]["tmp_name"];    
		if($_FILES["CSVFile"]["size"] > 0){
			$file = fopen($filename, "r");
			while (($data = fgetcsv($file, 10000, ",")) !== FALSE){
				if(!mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '".trim(ucwords($data['0']))."'"))){
					
					mysqli_query($conn, "INSERT INTO ingredients (name, ml) VALUES ('".trim(ucwords($data['0']))."', '10')");
				}
				if(empty($data['1'])){
					$data['1'] = '100';
				}
				$sql = "INSERT INTO formulas (fid,name,ingredient,concentration,dilutant,quantity) VALUES ('$fid', '$name','".trim(ucwords($data['0']))."','".$data['1']."','".$data['2']."','".$data['3']."')";
				$res = mysqli_query($conn, $sql);
			}
			
			if($res){
				mysqli_query($conn, "INSERT INTO formulasMetaData (fid,name,notes,profile,image) VALUES ('$fid','$name','Imported via csv','$profile','$def_app_img')");
				echo '<div class="alert alert-success alert-dismissible"><strong><a href="?do=Formula&name='.$name.'">'.$name.'</a></strong> added!</div>';
			}else{
				echo '<div class="alert alert-danger alert-dismissible"><strong>Error adding: </strong>'.$name.'</div>';
			}
			fclose($file);  
		}
	 
	 
	return;
}
?>
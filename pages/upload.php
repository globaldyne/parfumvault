<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/fixIFRACas.php');
require_once(__ROOT__.'/func/formatBytes.php');
require_once(__ROOT__.'/func/create_thumb.php');

if($_GET['type'] == 'bottle' && $_GET['name']){
	$name = base64_decode($_GET['name']);
	$ml = $_GET['size']?:0;
	$price = $_GET['price']?:0;
	$height = $_GET['height']?:0;
	$width = $_GET['width']?:0;
	$diameter = $_GET['diameter']?:0;
	$supplier = base64_decode($_GET['supplier']);
	$supplier_link = base64_decode($_GET['supplier_link']);
	$notes = base64_decode($_GET['notes']);
	$pieces = $_GET['pieces']?:0;

	if(isset($_FILES['pic_file']['name'])){
      $file_name = $_FILES['pic_file']['name'];
      $file_size = $_FILES['pic_file']['size'];
      $file_tmp = $_FILES['pic_file']['tmp_name'];
      $file_type = $_FILES['pic_file']['type'];
      $file_ext = strtolower(end(explode('.',$_FILES['pic_file']['name'])));
	  
		if (!file_exists(__ROOT__."/uploads/tmp/")) {
			mkdir(__ROOT__."/uploads/tmp/", 0740, true);
		}

		$allowed_ext = "png, jpg, jpeg, gif, bmp";
	  	$ext = explode(', ', $allowed_ext);
	  
      	if(in_array($file_ext,$ext)===false){
			$response["error"] = '<strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file';
			echo json_encode($response);
			return;
		}
	  	if($file_size > $max_filesize){
			 $response["error"] = 'File size must not exceed '.formatBytes($max_filesize);
			 echo json_encode($response);
			 return;
     	 }
		 if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM bottles WHERE name = '$name'"))){
			$response["error"] = $name.' already exists!';
			echo json_encode($response);
			return;
		  }
		  
      if(move_uploaded_file($file_tmp,__ROOT__."/uploads/tmp/".base64_encode($file_name))){
			$photo = "/uploads/tmp/".base64_encode($file_name);
			create_thumb(__ROOT__.$photo,250,250); 
			$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents(__ROOT__.$photo));
		
			if(mysqli_query($conn, "INSERT INTO bottles (name, ml, price, height, width, diameter, supplier, supplier_link, notes, pieces) VALUES ('$name', '$ml', '$price', '$height', '$width', '$diameter', '$supplier', '$supplier_link', '$notes', '$pieces')") ){
				$bottle_id = mysqli_insert_id($conn);
				mysqli_query($conn, "INSERT INTO documents (ownerID,name,type,notes,docData) VALUES ('".$bottle_id."','$name','4','-','$docData')");
				unlink(__ROOT__.$photo);
				$response["success"] = $name.' added!';
			}else{
				$response["error"] =  'Failed to add '.$name.' - '.mysqli_error($conn);
			}
		}
	  }
	echo json_encode($response);  
	return;
}

if($_GET['type'] == 'lid' && $_GET['style']){
	
	$style = base64_decode($_GET['style']);
	$color = base64_decode($_GET['color']);
	$price = base64_decode($_GET['price']);
	$supplier = base64_decode($_GET['supplier']);
	$supplier_link = base64_decode($_GET['supplier_link']);


	if(isset($_FILES['pic_file']['name'])){
      $file_name = $_FILES['pic_file']['name'];
      $file_size = $_FILES['pic_file']['size'];
      $file_tmp = $_FILES['pic_file']['tmp_name'];
      $file_type = $_FILES['pic_file']['type'];
      $file_ext = strtolower(end(explode('.',$_FILES['pic_file']['name'])));
	  
	  if (file_exists('../'.$uploads_path.'lids/') === FALSE) {
    	mkdir('../'.$uploads_path.'lids/', 0740, true);
	  }

	  $ext = explode(', ', $allowed_ext);
	  
      if(in_array($file_ext,$ext)=== false){
		 echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file.</div>';
      }elseif($file_size > $max_filesize){
		 echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>File upload error: </strong>File size must not exceed '.formatBytes($max_filesize).'</div>';
      }else{
	  
         if(move_uploaded_file($file_tmp,'../'.$uploads_path.'lids/'.base64_encode($file_name))){
		 	$photo = $uploads_path.'lids/'.base64_encode($file_name);
			if(mysqli_query($conn, "INSERT INTO lids (style, colour, price, supplier, supplier_link, photo) VALUES ('$style', '$color', '$price', '$supplier', '$supplier_link', '$photo')")){
		 		echo '<div class="alert alert-success alert-dismissible"><a href="?do=lids" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$style.'</strong> added!</div>';
			}else{
				echo '<div class="alert alert-danger alert-dismissible"><a href="?do=lids" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Failed to add '.$style.' - '.mysqli_error($conn).'</div>';
			}
		 }
	  }
   }
	return;
}

if($_GET['type'] && $_GET['id']){
	
	$ownerID = mysqli_real_escape_string($conn, $_GET['id']);
	$type = mysqli_real_escape_string($conn, $_GET['type']);
	$name = base64_decode($_GET['doc_name']);
	$notes = base64_decode($_GET['doc_notes']);

	$field = 'doc_file';
	
	if(isset($_FILES[$field]['name'])){
		$file_name = $_FILES[$field]['name'];
     	$file_size = $_FILES[$field]['size'];
     	$file_tmp = $_FILES[$field]['tmp_name'];
     	$file_type = $_FILES[$field]['type'];
     	$file_ext = strtolower(end(explode('.',$_FILES[$field]['name'])));
	
		$tmp_path = __ROOT__.'/tmp/';
	
		if (!file_exists($tmp_path)) {
			mkdir($tmp_path, 0740, true);
		}
	
	  	$ext = explode(', ', $allowed_ext);
	  
      	if(in_array($file_ext,$ext)=== false){
			 echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file.</div>';
      	}elseif($file_size > $max_filesize){
			 echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>File upload error: </strong>File size must not exceed '.formatBytes($max_filesize).'</div>';
      	}else{
	         if(move_uploaded_file($file_tmp, $tmp_path.$file_name)){
				if($type == '2'){
					mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '$ownerID' AND type = '2'");
				}
				$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents($tmp_path.$file_name));
				if(mysqli_query($conn, "INSERT INTO documents (ownerID,type,name,notes,docData) VALUES ('$ownerID','$type','$name','$notes','$docData')")){
					unlink($tmp_path.$file_name);
					echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>File uploaded</strong></div>';
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

if($_GET['type'] == 'cmpCSVImport'){
	$ing = base64_decode($_GET['ingID']);
	
	if(isset($_FILES['CSVFile']['name'])){
		$i = 0;
		$filename=$_FILES['CSVFile']['tmp_name'];    
		if($_FILES['CSVFile']['size'] > 0){
			$file = fopen($filename, "r");
			while (($data = fgetcsv($file, 10000, ",")) !== FALSE){
				if(!mysqli_num_rows(mysqli_query($conn, "SELECT name FROM allergens WHERE ing = '$ing' AND name = '".trim(ucwords($data['0']))."'"))){
					$r = mysqli_query($conn, "INSERT INTO allergens (ing, name, cas, ec, percentage) VALUES ('$ing','".trim(ucwords($data['0']))."', '".trim($data['1'])."', '".trim($data['2'])."', '".rtrim($data['3'],'%')."')");
						$i++;
				}
			}
			if($r){
				echo '<div class="alert alert-success alert-dismissible">'.$i.' Items imported</div>';
			}else{
				echo '<div class="alert alert-danger alert-dismissible">Failed to import the CSV file. Please check syntax is correct.</div>';
			}
		}
		fclose($file);  
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
					$r = mysqli_query($conn, "INSERT INTO ingredients (name, cas, odor, profile, $defCatClass) VALUES ('".trim(ucwords($data['0']))."', '".trim($data['1'])."', '".trim($data['2'])."', '".trim($data['3'])."', '".preg_replace("/[^0-9.]/", "", $data['4'])."')");
						$i++;
				}
			}
			if($r){
				echo '<div class="alert alert-success alert-dismissible">'.$i.' Ingredients imported</div>';
			}else{
				echo '<div class="alert alert-danger alert-dismissible">Failed to import the ingredients list.'.mysqli_error($conn).'</div>';
			}
		}
		fclose($file);  
	
	}  
	return;
}

if($_GET['type'] == 'frmCSVImport'){
	$name = mysqli_real_escape_string($conn,trim($_GET['name']));
	
	if(empty($name)){
		echo '<div class="alert alert-danger alert-dismissible"><strong>Error:</strong> Name is required.</div>';
		return;
	}

	$fid = base64_encode($name);

	$profile = mysqli_real_escape_string($conn,$_GET['profile']);
	
	if($chk = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE fid = '$fid'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$name.' already exists! Click <a href="?do=Formula&id='.$chk['id'].'"  target="_blank">here</a> to view/edit!</div>';
	  	return;
	 }

		$filename=$_FILES["CSVFile"]["tmp_name"];    
		if($_FILES["CSVFile"]["size"] > 0){
			$file = fopen($filename, "r");
			while (($data = fgetcsv($file, 10000, ",")) !== FALSE){
				if($_GET['addMissIng'] == 'true'){
					if(!mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '".trim(ucwords(preg_replace('/[[:^print:]]/', '',$data['0'])))."'"))){
						mysqli_query($conn, "INSERT INTO ingredients (name, ml) VALUES ('".trim(ucwords(preg_replace('/[[:^print:]]/', '',$data['0'])))."', '10')");
					}
				}
				if(empty($data['1'])){
					$data['1'] = '100';
				}
				$sql = "INSERT INTO formulas (fid,name,ingredient,concentration,dilutant,quantity) VALUES ('$fid', '$name','".trim(ucwords(preg_replace('/[[:^print:]]/', '',$data['0'])))."','".$data['1']."','".$data['2']."','".$data['3']."')";
				$res = mysqli_query($conn, $sql);
			}
			
			if($res){
				if(mysqli_query($conn, "INSERT INTO formulasMetaData (fid,name,notes,profile) VALUES ('$fid','$name','Imported via csv','$profile')")){
					$iID = mysqli_insert_id($conn);
				echo '<div class="alert alert-success alert-dismissible"><strong><a href="?do=Formula&id='.$iID.'" target="_blank">'.$name.'</a></strong> added!</div>';
				}else{
					echo '<div class="alert alert-danger alert-dismissible"><strong>Error in: </strong>'.mysqli_error($conn).'</div>';
				}
			}else{
				echo '<div class="alert alert-danger alert-dismissible"><strong>Error adding: </strong>'.$name.'</div>';
			}
			fclose($file);  
		}
	 
	 
	return;
}

if($_GET['type'] == 'IFRA'){
	if(isset($_FILES['ifraXLS'])){
		$filename = $_FILES["ifraXLS"]["tmp_name"];  
		$file_ext = strtolower(end(explode('.',$_FILES['ifraXLS']['name'])));
		$all_ext = "xls,xlsx";
		$ext = explode(",",$all_ext);
	
		if(in_array($file_ext,$ext)=== false){
			echo '<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>Extension not allowed, please choose a '.$all_ext.' file.</div>';
			return;
		}
		
		if($_FILES["ifraXLS"]["size"] > 0){
			require_once(__ROOT__.'/func/SimpleXLSX.php');
			mysqli_query($conn, "TRUNCATE IFRALibrary");
		
			$xlsx = SimpleXLSX::parse($filename);
		
			try {
			   $link = new PDO( "mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			   $link->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}catch(PDOException $e){
				echo $sql . "<br>" . $e->getMessage();
			}
		
			$fields = 'ifra_key,image,amendment,prev_pub,last_pub,deadline_existing ,deadline_new,name,cas,cas_comment,synonyms,formula,flavor_use,prohibited_notes,restricted_photo_notes,restricted_notes,specified_notes,type,risk,contrib_others,contrib_others_notes,cat1,cat2,cat3,cat4,cat5A ,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12';
			$values = substr(str_repeat('?,', count(explode(',' , $fields))), 0 , strlen($x) - 1);
			$stmt = $link->prepare( "INSERT INTO IFRALibrary ($fields) VALUES ($values)");
			$cols = $xlsx->dimension()[0];//$dim[0];
				foreach ( $xlsx->rows() as $k => $r ) {
					for ( $i = 0; $i < $cols; $i ++ ) {
						$l = $i+1;
						$stmt->bindValue( $l, $r[ $i]);
					}
					try {
						$stmt->execute();
						$err = '0';
					} catch (Exception $e) {
						$err = '1';
					}
				}
				if($_GET['updateCAS'] == '1'){
					fixIFRACas($conn);
				}
				echo '<div class="alert alert-success alert-dismissible">Import success.</div>';
		}
	}
	return;
}
?>

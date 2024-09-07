<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/fixIFRACas.php');
require_once(__ROOT__.'/func/formatBytes.php');
require_once(__ROOT__.'/func/create_thumb.php');

//UPLOAD ING PROFILE
if($_GET['upload_ing_prof_pic'] && $_GET['profID']){

	$id = (int)$_GET['profID'];
	$allowed_ext = "png, jpg, jpeg, gif, bmp";

	$filename = $_FILES["prof-pic-file"]["tmp_name"];  
    $file_ext = strtolower(end(explode('.',$_FILES['prof-pic-file']['name'])));
	$file_tmp = $_FILES['prof-pic-file']['tmp_name'];
    $ext = explode(', ',strtolower($allowed_ext));

	
	if(!$filename){
		$response["error"] = 'Please choose a file to upload...';
		echo json_encode($response);
		return;
	}	
	
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0740, true);
	}
		
	if(in_array($file_ext,$ext)===false){
		$response["error"] = 'Extension not allowed, please choose a '.$allowed_ext.' file';
		echo json_encode($response);
		return;
	}
		
	if($_FILES["prof-pic-file"]["size"] > 0){
		move_uploaded_file($file_tmp,$tmp_path.base64_encode($filename));
		$pic = base64_encode($filename);		
		create_thumb($tmp_path.$pic,250,250); 
		$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents($tmp_path.$pic));
		
		if(mysqli_query($conn, "UPDATE ingProfiles SET image = '".$docData."' WHERE id = '$id'")){	
			unlink($tmp_path.$pic);
			$response["success"] = array( "msg" => "Profile image updated", "pic" => $docData);
			echo json_encode($response);
			return;
		}
	}

	return;
}

if($_GET['upload_ing_cat_pic'] && $_GET['catID']){

	$id = $_GET['catID'];
	$allowed_ext = "png, jpg, jpeg, gif, bmp";

	$filename = $_FILES["cat-pic-file"]["tmp_name"];  
    $file_ext = strtolower(end(explode('.',$_FILES['cat-pic-file']['name'])));
	$file_tmp = $_FILES['cat-pic-file']['tmp_name'];
    $ext = explode(', ',strtolower($allowed_ext));

	
	if(!$filename){
		$response["error"] = 'Please choose a file to upload...';
		echo json_encode($response);
		return;
	}	
	
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0740, true);
	}
		
	if(in_array($file_ext,$ext)===false){
		$response["error"] = '<strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file';
		echo json_encode($response);
		return;
	}
		
	if($_FILES["cat-pic-file"]["size"] > 0){
		move_uploaded_file($file_tmp,$tmp_path.base64_encode($filename));
		$pic = base64_encode($filename);		
		create_thumb($tmp_path.$pic,250,250); 
		$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents($tmp_path.$pic));
		
		if(mysqli_query($conn, "UPDATE ingCategory SET image = '".$docData."' WHERE id = '$id'")){	
			unlink($tmp_path.$pic);
			$response["success"] = array( "msg" => "Category pic updated!", "pic" => $docData);
			echo json_encode($response);
			return;
		}
	}

	return;
}

if($_GET['type'] == 'bottle'){
	
	if(empty($_GET['name'])){
		$response["error"] = 'Name is required';
		echo json_encode($response);
		return;
	}
	
	$n =  base64_decode($_GET['name']);
	
	if (!ctype_alnum($n)) {
		$response["error"] = 'Name is invalid';
		echo json_encode($response);
		return;
	}
	
	if(!is_numeric($_GET['size']) || !is_numeric($_GET['price']) || !is_numeric($_GET['height']) || !is_numeric($_GET['width']) || !is_numeric($_GET['diameter']) || !is_numeric($_GET['pieces']) || !is_numeric($_GET['weight'])){
		$response["error"] = 'Form contains invalid values';
		echo json_encode($response);
		return;
	}
	
	
	
	$name = mysqli_real_escape_string($conn, $n);
	$ml = $_GET['size'];
	$price = $_GET['price'];
	$height = $_GET['height'] ?: 0;
	$width = $_GET['width'] ?: 0;
	$diameter = $_GET['diameter'] ? :0;
	$supplier = mysqli_real_escape_string($conn, base64_decode($_GET['supplier']));
	$supplier_link = mysqli_real_escape_string($conn, base64_decode($_GET['supplier_link']));
	$notes = mysqli_real_escape_string($conn, base64_decode($_GET['notes']));
	$pieces = $_GET['pieces'] ?: 0;
	$weight = $_GET['weight'] ?: 0;

	
	if(isset($_FILES['pic_file']['name'])){
      $file_name = $_FILES['pic_file']['name'];
      $file_size = $_FILES['pic_file']['size'];
      $file_tmp = $_FILES['pic_file']['tmp_name'];
      $file_type = $_FILES['pic_file']['type'];
      $file_ext = strtolower(end(explode('.',$_FILES['pic_file']['name'])));
	  
		if (!file_exists($tmp_path)) {
			mkdir($tmp_path, 0740, true);
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
		  
      if(move_uploaded_file($file_tmp,$tmp_path.base64_encode($file_name))){
			$photo = base64_encode($file_name);
			create_thumb($tmp_path.$photo,250,250); 
			$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents($tmp_path.$photo));
		
			if(mysqli_query($conn, "INSERT INTO bottles (name, ml, price, height, width, diameter, supplier, supplier_link, notes, pieces, weight) VALUES ('$name', '$ml', '$price', '$height', '$width', '$diameter', '$supplier', '$supplier_link', '$notes', '$pieces', '$weight')") ){
				$bottle_id = mysqli_insert_id($conn);
				mysqli_query($conn, "INSERT INTO documents (ownerID,name,type,notes,docData) VALUES ('".$bottle_id."','$name','4','-','$docData')");
				unlink($tmp_path.$photo);
				$response["success"] = $name.' added!';
			}else{
				$response["error"] =  'Failed to add '.$name.' - '.mysqli_error($conn);
			}
		} else {
			$response["error"] =  'Failed to add '.$name.' - '.mysqli_error($conn);
		}
	  }
	echo json_encode($response);  
	return;
}

if($_GET['type'] == 'lid' && $_GET['style']){
	
	$style = base64_decode($_GET['style']);
	$color = base64_decode($_GET['color']);
	$price = $_GET['price']?:0;
	$supplier = base64_decode($_GET['supplier']);
	$supplier_link = base64_decode($_GET['supplier_link']);
	$pieces = $_GET['pieces']?:0;
	$colour = $_GET['colour'];


	if(isset($_FILES['pic_file']['name'])){
      $file_name = $_FILES['pic_file']['name'];
      $file_size = $_FILES['pic_file']['size'];
      $file_tmp = $_FILES['pic_file']['tmp_name'];
      $file_type = $_FILES['pic_file']['type'];
      $file_ext = strtolower(end(explode('.',$_FILES['pic_file']['name'])));
	  
	  	if (!file_exists($tmp_path)) {
			mkdir($tmp_path, 0740, true);
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
	   	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM lids WHERE style = '$style'"))){
			$response["error"] = $style.' already exists!';
			echo json_encode($response);
			return;
		}
		if(move_uploaded_file($file_tmp,$tmp_path.base64_encode($file_name))){
			$photo = base64_encode($file_name);
			create_thumb($tmp_path.$photo,250,250); 
			$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents($tmp_path.$photo));
		
			if(mysqli_query($conn, "INSERT INTO lids (style, colour, price, supplier, supplier_link, pieces) VALUES ('$style', '$colour', '$price', '$supplier', '$supplier_link', '$pieces')") ){
				$lid_id = mysqli_insert_id($conn);
				mysqli_query($conn, "INSERT INTO documents (ownerID,name,type,notes,docData) VALUES ('".$lid_id."','$style','5','-','$docData')");
				unlink($tmp_path.$photo);
				$response["success"] = $style.' added!';
			}else{
				$response["error"] =  'Failed to add '.$style.' - '.mysqli_error($conn);
			}
		}
	  }
	echo json_encode($response);  
	return;
}

if($_GET['type'] && $_GET['id']){
	
	$ownerID = mysqli_real_escape_string($conn, $_GET['id']);
	$type = mysqli_real_escape_string($conn, $_GET['type']);
	$name = base64_decode($_GET['doc_name']);
	$notes = base64_decode($_GET['doc_notes']);
	$isBatch = $_GET['isBatch'] ?: 0;

	$field = 'doc_file';
	
	if(isset($_FILES[$field]['name'])){
		$file_name = $_FILES[$field]['name'];
     	$file_size = $_FILES[$field]['size'];
     	$file_tmp = $_FILES[$field]['tmp_name'];
     	$file_type = $_FILES[$field]['type'];
     	$file_ext = strtolower(end(explode('.',$_FILES[$field]['name'])));
	
	
		if (!file_exists($tmp_path)) {
			mkdir($tmp_path, 0740, true);
		}
	
	  	$ext = explode(', ', $allowed_ext);
	  
      	if(in_array($file_ext,$ext)=== false){
      		$response['error'] = '<strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file';
			echo json_encode($response);
			return;
		}
		
		if($file_size > $max_filesize){
			$response['error'] = 'File upload error: </strong>File size must not exceed '.formatBytes($max_filesize);
			echo json_encode($response);
			return;
      	}
		
		if(move_uploaded_file($file_tmp, $tmp_path.$file_name)){
			if($type == '2'){
				mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '$ownerID' AND type = '2'");
			}
			$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents($tmp_path.$file_name));
			if(mysqli_query($conn, "INSERT INTO documents (ownerID,type,name,notes,docData,isBatch) VALUES ('$ownerID','$type','$name','$notes','$docData','$isBatch')")){
				unlink($tmp_path.$file_name);
				$response['success'] = 'File uploaded';
			}else {
				$response['error'] = 'File upload error '.mysqli_error($conn);
			}
			
	  	}
   }
	echo json_encode($response);
	return;	
}


if($_GET['type'] == 'brand'){
		
	if(isset($_FILES['brandLogo']['name'])){
     	$file_name = $_FILES['brandLogo']['name'];
      	$file_size = $_FILES['brandLogo']['size'];
      	$file_tmp = $_FILES['brandLogo']['tmp_name'];
      	$file_type = $_FILES['brandLogo']['type'];
      	$file_ext = strtolower(end(explode('.',$_FILES['brandLogo']['name'])));
	  
		if (file_exists($tmp_path) === FALSE) {
			mkdir($tmp_path, 0740, true);
		}
	
		$ext = explode(', ', $allowed_ext);
		  
		if(in_array($file_ext,$ext)=== false){
			$response['error'] = '<strong>File upload error: </strong>Extension not allowed, please choose a '.$allowed_ext.' file';
			echo json_encode($response);
			return;
		}
			
		if($file_size > $max_filesize){
			$response['error'] = 'File upload error: </strong>File size must not exceed '.formatBytes($max_filesize);
			echo json_encode($response);
			return;
		}
	  
         if(move_uploaded_file($file_tmp,$tmp_path.base64_encode($file_name))){
			$pic = base64_encode($file_name);		
			create_thumb($tmp_path.$pic,250,250); 
			$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents($tmp_path.$pic));

		 	$brandLogoF = $tmp_path.base64_encode($file_name);
		 	if(mysqli_query($conn, "UPDATE settings SET brandLogo = '$docData'")){
				unlink($tmp_path.$file_name);
				$response["success"] = array( "msg" => "Pic updated!", "pic" => $docData);
				echo json_encode($response);
				return;
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
				if(!mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredient_compounds WHERE ing = '$ing' AND name = '".trim(ucwords($data['0']))."'"))){
					$r = mysqli_query($conn, "INSERT INTO ingredient_compounds (ing, name, cas, ec, min_percentage, max_percentage, GHS) VALUES ('$ing','".trim(ucwords($data['0']))."', '".trim($data['1'])."', '".trim($data['2'])."', '".rtrim($data['3'])."', '".rtrim($data['4'])."', '".rtrim($data['5'])."' )");
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
	session_start();
	if($_GET['step'] == 'upload'){
	
		$file_array = explode(".", $_FILES['CSVFile']['name']);
		$extension = end($file_array);
		if($extension != 'csv') {
			echo '<div class="alert alert-danger">Error: Invalid csv file.</div>';
			return; 
		}
		$csv_file_data = fopen($_FILES['CSVFile']['tmp_name'], 'r');

		$file_header = fgetcsv($csv_file_data, 10000, ",");
		echo '<table class="jj table table-bordered"><thead><tr class="csv_upload_header">';
		for($count = 0; $count < count($file_header); $count++) {
			echo   '<th>
					<select name="set_column_data" class="form-control set_column_data" data-column_number="'.$count.'">
					 <option value="">Assign to</option>
					 <option value="">None</option>
					 <option value="ingredient_name">Name</option>
					 <option value="iupac">IUPAC</option>
					 <option value="cas">CAS</option>
					 <option value="fema">FEMA</option>
					 <option value="type">Type (AC, EO)</option>
					 <option value="strength">Strength (High, Medium, Low)</option>
					 <option value="profile">Profile (Top, Heart, Base, Solvent)</option>
					 <option value="physical_state">Physical state (Liquid = 1, Solid = 2)</option>
					 <option value="allergen">Is allergen (Yes = 1, No = 0)</option>
					 <option value="odor">Odor Description</option>
					 <option value="impact_top">Impact top note (0 - 100)</option>
					 <option value="impact_heart">Impact heart note (0 - 100)</option>
					 <option value="impact_base">Impact base note (0 - 100)</option>
					</select>
			   </th>';
		   } 
		echo '</tr></thead><tbody>';
		$i = 0;
		while(($row = fgetcsv($csv_file_data, 100000, ",")) !== FALSE)  {
			echo '<tr id="'.$i.'">';
			for($count = 0; $count < count($row); $count++) {
				if($row[$count]){
					echo '<td>'.$row[$count].'</td>';
				}else{
					echo '<td>-</td>';
				}
			}
			echo '</tr>';
			$temp_data[] = $row;
			$i++;
		}
	
			$_SESSION['csv_file_data'] = $temp_data;
			echo '</tbody></table>';
	
	}  
	if( $_GET['step'] == 'import'){

		$i = 0;
		$csv_file_data = $_SESSION['csv_file_data'];			
		foreach($csv_file_data as $row) {
			if(!mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '".trim(ucwords($row[$_POST["ingredient_name"]]))."'"))){
				$data[] = '("'.$row[$_POST["ingredient_name"]].'","'.$row[$_POST["iupac"]].'","'.$row[$_POST["cas"]].'","'.$row[$_POST["fema"]].'", "'.$row[$_POST["type"]].'", "'.$row[$_POST["strength"]].'", "'.$row[$_POST["profile"]].'", "'.$row[$_POST["physical_state"]].'", "'.$row[$_POST["allergen"]].'", "'.$row[$_POST["odor"]].'", "'.$row[$_POST["impact_top"]].'", "'.$row[$_POST["impact_heart"]].'", "'.$row[$_POST["impact_base"]].'" )';
				$i++;
			}

		}
		if($data){
			$query = "INSERT INTO ingredients (name,INCI,cas,FEMA,type,strength, profile,physical_state,allergen,odor,impact_top,impact_heart,impact_base) VALUES ".implode(",", $data)."";
			$res =  mysqli_query($conn,$query);
			if($res){
				echo '<div class="alert alert-success alert-dismissible">'.$i.' Ingredients imported</div>';;
			}else{
				echo '<div class="alert alert-danger">Error: Incorrect CSV data '.$query.'</div>';
			}
		}else{
			echo '<div class="alert alert-info">Nothing to import, data already exists</div>';
		}
	}
	
	return;
}

if($_GET['type'] == 'frmCSVImport'){
	session_start();
	if($_GET['step'] == 'upload'){
		$file_array = explode(".", $_FILES['CSVFile']['name']);
		$extension = end($file_array);
		if($extension != 'csv') {
			echo '<div class="alert alert-danger">Error: Invalid csv file.</div>';
			return; 
		}
		$csv_file_data = fopen($_FILES['CSVFile']['tmp_name'], 'r');

		$file_header = fgetcsv($csv_file_data, 1000, ",");
		echo '<table class="jj table table-bordered"><thead><tr class="csv_upload_header">';
		for($count = 0; $count < count($file_header); $count++) {
			echo   '<th>
					<select name="set_column_data" class="form-control set_column_data" data-column_number="'.$count.'">
					 <option value="">Assign to</option>
					 <option value="">None</option>
					 <option value="ingredient">Ingredient</option>
					 <option value="concentration">Concentration</option>
					 <option value="dilutant">Dilutant</option>
					 <option value="quantity">Quantity</option>
					</select>
			   </th>';
		   } 
		echo '</tr></thead><tbody>';
		$i = 0;
		while(($row = fgetcsv($csv_file_data, 1000, ",")) !== FALSE)  {
			echo '<tr id="'.$i.'">';
			for($count = 0; $count < count($row); $count++) {
				if($row[$count]){
					echo '<td>'.$row[$count].'</td>';
				}else{
					echo '<td>-</td>';
				}
			}
			echo '</tr>';
			$temp_data[] = $row;
			$i++;
		}
	
			$_SESSION['csv_file_data'] = $temp_data;
			echo '</tbody></table>';
	}
	
	if( $_GET['step'] == 'import'){

		$name = mysqli_real_escape_string($conn,trim($_POST['formula_name']));
		$profile = $_POST['formula_profile'];
		
		if(empty($name)){
			echo '<div class="alert alert-danger">Error: Name field cannot be empty</div>';
			return;
		}
		require_once(__ROOT__.'/func/genFID.php');
		$fid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');

		if($chk = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM formulasMetaData WHERE name = '$name'"))){
			echo '<div class="alert alert-danger">Error: '.$name.' already exists! Click <a href="?do=Formula&id='.$chk['id'].'"  target="_blank">here</a> to view/edit!</div>';
			return;
		 }
	
		$csv_file_data = $_SESSION['csv_file_data'];
				
		foreach($csv_file_data as $row) {
			$data[] = '("'.$fid.'","'.$name.'","'.$row[$_POST["ingredient"]].'", "'.preg_replace("/[^0-9.]/", "", $row[$_POST["concentration"]]?:'100').'", "'.preg_replace("/[0-9.]/", "",$row[$_POST["dilutant"]]?:'None').'", "'.preg_replace("/[^0-9.]/", "", $row[$_POST["quantity"]]).'")';
		}
		$query = "INSERT INTO formulas (fid,name,ingredient, concentration, dilutant, quantity) VALUES ".implode(",", $data)."";
		$res =  mysqli_query($conn,$query);
			
		if($res){
			if(mysqli_query($conn, "INSERT INTO formulasMetaData (fid,name,notes,profile) VALUES ('$fid','$name','Imported via csv','$profile')")){
				echo '<div class="alert alert-success alert-dismissible"><strong><a href="?do=Formula&id='.mysqli_insert_id($conn).'" target="_blank">Formula '.$name.'</a></strong> has been imported!</div>';
			}
		}else{
			echo '<div class="alert alert-danger">Error: Incorrect CSV data '.$query.'</div>';
		}
		
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
			$response['error'] = '<strong>File upload error: </strong>Extension not allowed, please choose a '.$all_ext.' file';
			echo json_encode($response);
			return;
		}
		
		if($_FILES["ifraXLS"]["size"] > 0){
			require_once(__ROOT__.'/func/SimpleXLSX.php');
			
			if($_GET['overwrite'] == 'true'){
				mysqli_query($conn, "TRUNCATE IFRALibrary");
			}
			
			$xlsx = SimpleXLSX::parse($filename);
		
			try {
			   $link = new PDO( "mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			   $link->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}catch(PDOException $e){
				echo $sql . "<br>" . $e->getMessage();
			}
			
			switch ($_GET['IFRAVer']) {
				case 0:
					$response['error'] =  '<strong>Please select IFRA amendment</strong>'.$e;
					echo json_encode($response);
					return;
					break;
				case 49:
					$fields = 'ifra_key,image,amendment,prev_pub,last_pub,deadline_existing ,deadline_new,name,cas,cas_comment,synonyms,formula,flavor_use,prohibited_notes,restricted_photo_notes,restricted_notes,specified_notes,type,risk,contrib_others,contrib_others_notes,cat1,cat2,cat3,cat4,cat5A ,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12';
					break;
				case 51:
					$fields = 'ifra_key,amendment,prev_pub,last_pub,deadline_existing ,deadline_new,name,cas,cas_comment,synonyms,type,risk,flavor_use,prohibited_notes,restricted_photo_notes,restricted_notes,specified_notes,contrib_others,contrib_others_notes,cat1,cat2,cat3,cat4,cat5A ,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12';
					break;
			}
		

			$values = substr(str_repeat('?,', count(explode(',' , $fields))), 0 , strlen($x) - 1);
			$stmt = $link->prepare( "INSERT INTO IFRALibrary ($fields) VALUES ($values)");
			$cols = $xlsx->dimension()[0];//$dim[0];
				foreach ( $xlsx->rows() as $k => $r ) {
					for ( $i = 0; $i < $cols; $i ++ ) {
						$l = $i+1;
						//$stmt->bindValue( $l, $r[ $i]);
						$columnName = explode(',', $fields)[$i];
                    
						// Check if the category columns contains non-numeric data
						if (strpos($columnName, 'cat') === 0 && !is_numeric($r[$i])) {
							$stmt->bindValue($l, 100);
						} else {
							$stmt->bindValue($l, $r[$i]);
						}
					}
					try {
						$stmt->execute();
						$err = '0';
					} catch (Exception $e) {
						$err = '1';
					}
				}
				if($err){
					$response['error'] =  '<strong>Import error: </strong>'.$e;
					echo json_encode($response);
					return;
				}
				if($_GET['updateCAS'] == 'true'){
					fixIFRACas($conn);
				}
				$response['success'] =  '<strong>Import success </strong>';
				echo json_encode($response);
				return;

		}
	}
	return;
}
?>

<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/fixIFRACas.php');
require_once(__ROOT__.'/func/formatBytes.php');
require_once(__ROOT__.'/func/create_thumb.php');

//UPLOAD INGREDIENT CATEGORY PIC
if (isset($_GET['upload_ing_cat_pic'], $_GET['catID'])) {

    $id = $_GET['catID'];
    $allowed_ext = ["png", "jpg", "jpeg", "gif", "bmp"];

    // Validate file upload
    if (!isset($_FILES["cat-pic-file"]) || !is_uploaded_file($_FILES["cat-pic-file"]["tmp_name"])) {
        $response["error"] = 'Please choose a file to upload.';
        echo json_encode($response);
        return;
    }

    // Extract file details
    $filename = $_FILES["cat-pic-file"]["name"];
    $file_tmp = $_FILES["cat-pic-file"]["tmp_name"];
    $file_size = $_FILES["cat-pic-file"]["size"];
    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    // Check allowed extensions
    if (!in_array($file_ext, $allowed_ext)) {
        $response["error"] = 'Extension not allowed. Please choose a file with one of the following extensions: ' . implode(', ', $allowed_ext) . '.';
        echo json_encode($response);
        return;
    }

    // Check if file size is greater than 0
    if ($file_size <= 0) {
        $response["error"] = 'The uploaded file is empty.';
        echo json_encode($response);
        return;
    }

    // Ensure temporary path exists
    if (!file_exists($tmp_path) && !mkdir($tmp_path, 0740, true)) {
        $response["error"] = 'Failed to create temporary directory.';
        echo json_encode($response);
        return;
    }

    // Generate a unique temporary file name
    $encoded_filename = base64_encode($filename);
    $temp_file_path = $tmp_path . $encoded_filename;

    // Move uploaded file to temporary directory
    if (!move_uploaded_file($file_tmp, $temp_file_path)) {
        $response["error"] = 'Failed to upload the file.';
        echo json_encode($response);
        return;
    }

    // Create a thumbnail
    create_thumb($temp_file_path, 250, 250);

    // Encode file contents to Base64
    $docData = 'data:image/' . $file_ext . ';base64,' . base64_encode(file_get_contents($temp_file_path));

    // Update the database
    $query = "UPDATE ingCategory SET image = ? WHERE id = ? AND owner_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $docData, $id, $userID);

    if (mysqli_stmt_execute($stmt)) {
        // Clean up the temporary file
        unlink($temp_file_path);

        // Success response
        $response["success"] = [
            "msg" => "Category pic updated successfully.",
            "pic" => $docData
        ];
    } else {
        $response["error"] = 'Failed to update the category image.';
    }

    mysqli_stmt_close($stmt);
    echo json_encode($response);
    return;
}

//UPLOAD BOTTLE
if (isset($_GET['type']) && $_GET['type'] === 'bottle') {

    // Check for required parameters
    if (empty($_GET['name'])) {
        $response["error"] = 'Name is required.';
        echo json_encode($response);
        return;
    }

    $n = base64_decode($_GET['name']);

    // Validate name
    if (!ctype_alnum($n)) {
        $response["error"] = 'Name is invalid.';
        echo json_encode($response);
        return;
    }

    // Validate numerical inputs
    $numericFields = ['size', 'price', 'height', 'width', 'diameter', 'pieces', 'weight'];
    foreach ($numericFields as $field) {
        if (!isset($_GET[$field]) || !is_numeric($_GET[$field]) || $_GET[$field] <= 0) {
            $response["error"] = 'Form contains invalid values. No 0 or empty values are allowed for ' . $field . '.';
            echo json_encode($response);
            return;
        }
    }

    // Sanitize inputs
    $name = mysqli_real_escape_string($conn, $n);
    $ml = $_GET['size'];
    $price = $_GET['price'];
    $height = $_GET['height'] ?: 0;
    $width = $_GET['width'] ?: 0;
    $diameter = $_GET['diameter'] ?: 0;
    $supplier = mysqli_real_escape_string($conn, base64_decode($_GET['supplier']));
    $supplier_link = mysqli_real_escape_string($conn, base64_decode($_GET['supplier_link']));
    $notes = mysqli_real_escape_string($conn, base64_decode($_GET['notes']));
    $pieces = $_GET['pieces'] ?: 0;
    $weight = $_GET['weight'] ?: 0;

    // Handle file upload
    if (isset($_FILES['pic_file']['name'])) {
        $file_name = $_FILES['pic_file']['name'];
        $file_tmp = $_FILES['pic_file']['tmp_name'];
        $file_size = $_FILES['pic_file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['png', 'jpg', 'jpeg', 'gif', 'bmp'];

        // Validate file extension
        if (!in_array($file_ext, $allowed_ext)) {
            $response["error"] = 'Invalid file extension. Allowed: ' . implode(', ', $allowed_ext) . '.';
            echo json_encode($response);
            return;
        }

        // Validate file size
        if ($file_size > $upload_max_filesize) {
            $response["error"] = 'File size must not exceed ' . formatBytes($upload_max_filesize) . '.';
            echo json_encode($response);
            return;
        }

        // Check if bottle name already exists
        $existingBottleQuery = "SELECT name FROM bottles WHERE name = '$name' AND owner_id = '$userID'";
        if (mysqli_num_rows(mysqli_query($conn, $existingBottleQuery))) {
            $response["error"] = $name . ' already exists.';
            echo json_encode($response);
            return;
        }

        // Ensure temporary path exists
        if (!file_exists($tmp_path) && !mkdir($tmp_path, 0740, true)) {
            $response["error"] = 'Failed to create temporary directory.';
            echo json_encode($response);
            return;
        }

        // Process file
        $encoded_file_name = base64_encode($file_name);
        $temp_file_path = $tmp_path . $encoded_file_name;

        if (move_uploaded_file($file_tmp, $temp_file_path)) {
            $photo = $encoded_file_name;
            create_thumb($temp_file_path, 250, 250);
            $docData = 'data:image/' . $file_ext . ';base64,' . base64_encode(file_get_contents($temp_file_path));

            // Insert bottle details into database
            $insertBottleQuery = "INSERT INTO bottles (name, ml, price, height, width, diameter, supplier, supplier_link, notes, pieces, weight, owner_id) 
                                  VALUES ('$name', '$ml', '$price', '$height', '$width', '$diameter', '$supplier', '$supplier_link', '$notes', '$pieces', '$weight', '$userID')";
            if (mysqli_query($conn, $insertBottleQuery)) {
                $bottle_id = mysqli_insert_id($conn);

                // Insert document details into database
                $insertDocumentQuery = "INSERT INTO documents (ownerID, name, type, notes, docData, owner_id) 
                                        VALUES ('$bottle_id', '$name', '4', '-', '$docData', '$userID')";
                mysqli_query($conn, $insertDocumentQuery);

                // Clean up temporary file
                unlink($temp_file_path);

                $response["success"] = $name . ' added successfully.';
            } else {
                $response["error"] = 'Failed to add ' . $name . ' - ' . mysqli_error($conn);
            }
        } else {
            $response["error"] = 'Failed to upload the file.';
        }
    }

    echo json_encode($response);
    return;
}

//UPLOAD ACCESSORY PIC
if ($_GET['type'] == 'accessory') {
    $name = base64_decode($_GET['name']);
    $accessory = base64_decode($_GET['accessory']);
    $price = $_GET['price'];
    $supplier = base64_decode($_GET['supplier']);
    $supplier_link = base64_decode($_GET['supplier_link']);
    $pieces = $_GET['pieces'] ?: 0;
    $allowed_ext = "png, jpg, jpeg, gif, bmp";


    if (!$_GET['name']) {
        $response["error"] = 'Name cannot be empty';
        echo json_encode($response);
        return;
    }

    if (!is_numeric($price) || $price <= 0) {
        $response["error"] = 'Price cannot be empty or 0';
        echo json_encode($response);
        return;
    }

    // Check if file is uploaded
    if (isset($_FILES['pic_file']['name'])) {
        $file_name = $_FILES['pic_file']['name'];
        $file_size = $_FILES['pic_file']['size'];
        $file_tmp = $_FILES['pic_file']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext_array = explode(', ', strtolower($allowed_ext));

        // Ensure the temporary directory exists
        if (!file_exists($tmp_path)) {
            if (!mkdir($tmp_path, 0740, true) && !is_dir($tmp_path)) {
                error_log("PV error: Failed to create directory at $tmp_path");
                $response["error"] = "Server error. Unable to create directory.";
                echo json_encode($response);
                return;
            }
        }

        // Validate file extension
        if (!in_array($file_ext, $allowed_ext_array, true)) {
            $response["error"] = 'Extension not allowed, please choose a ' . $allowed_ext . ' file.';
            echo json_encode($response);
            return;
        }

        // Validate file size
        if ($file_size > $upload_max_filesize) {
            $response["error"] = 'File size must not exceed ' . formatBytes($upload_max_filesize);
            echo json_encode($response);
            return;
        }

        // Check if accessory already exists
        $stmtCheck = $conn->prepare("SELECT id FROM inventory_accessories WHERE name = ? AND owner_id = ?");
        $stmtCheck->bind_param("ss", $name, $userID);
        if (!$stmtCheck->execute()) {
            error_log("PV error: Failed to check for existing accessory. " . $stmtCheck->error);
            $response["error"] = "Failed to verify accessory existence.";
            echo json_encode($response);
            return;
        }
        if ($stmtCheck->get_result()->num_rows > 0) {
            $response["error"] = $name . ' already exists';
            echo json_encode($response);
            return;
        }
        $stmtCheck->close();

        // Process uploaded file
        $encoded_filename = base64_encode($file_name);
        $upload_path = $tmp_path . $encoded_filename;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            create_thumb($upload_path, 250, 250);
            $docData = 'data:image/' . $file_ext . ';base64,' . base64_encode(file_get_contents($upload_path));

            // Insert accessory details
            $stmtInsert = $conn->prepare("INSERT INTO inventory_accessories (name, accessory, price, supplier, supplier_link, pieces, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtInsert->bind_param("ssdssis", $name, $accessory, $price, $supplier, $supplier_link, $pieces, $userID);

            if ($stmtInsert->execute()) {
                $accessory_id = $stmtInsert->insert_id;
                $stmtInsertDoc = $conn->prepare("INSERT INTO documents (ownerID, name, type, notes, docData, owner_id) VALUES (?, ?, '5', '-', ?, ?)");
                $stmtInsertDoc->bind_param("isss", $accessory_id, $name, $docData, $userID);

                if (!$stmtInsertDoc->execute()) {
                    error_log("PV error: Failed to insert document. " . $stmtInsertDoc->error);
                }
                $stmtInsertDoc->close();

                unlink($upload_path); // Clean up temporary file
                $response["success"] = $name . ' added';
            } else {
                error_log("PV error: Failed to insert accessory. " . $stmtInsert->error);
                $response["error"] = 'Failed to add ' . $name;
            }
            $stmtInsert->close();
        } else {
            error_log("PV error: Failed to move uploaded file to $upload_path");
            $response["error"] = "Failed to upload file.";
        }
    } else {
        $response["error"] = "No file uploaded.";
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
      		$response['error'] = 'Extension not allowed, please choose a '.$allowed_ext.' file';
			echo json_encode($response);
			return;
		}
		
		if($file_size > $upload_max_filesize){
			$response['error'] = 'File size must not exceed '.formatBytes($upload_max_filesize);
			echo json_encode($response);
			return;
      	}
		
		if(move_uploaded_file($file_tmp, $tmp_path.$file_name)){
			if($type == '2'){
				mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '$ownerID' AND type = '2' AND owner_id = '$userID'");
			}
			$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents($tmp_path.$file_name));
			if(mysqli_query($conn, "INSERT INTO documents (ownerID,type,name,notes,docData,isBatch,owner_id) VALUES ('$ownerID','$type','$name','$notes','$docData','$isBatch','$userID')")){
				unlink($tmp_path.$file_name);
				$response["success"] = array( "msg" => "File uploaded", "file" => $docData);

			}else {
				$response['error'] = 'File upload error '.mysqli_error($conn);
			}
			
	  	}
   }
	echo json_encode($response);
	return;	
}

//UPLOAD BRAND LOGO
if ($_GET['type'] == 'brand') {
    if (isset($_FILES['brandLogo']['name'])) {
        $file_name = $_FILES['brandLogo']['name'];
        $file_size = $_FILES['brandLogo']['size'];
        $file_tmp = $_FILES['brandLogo']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!file_exists($tmp_path)) {
            mkdir($tmp_path, 0740, true);
        }

        $allowed_ext_array = explode(', ', $allowed_ext);
        if (!in_array($file_ext, $allowed_ext_array)) {
            $response['error'] = 'Extension not allowed, please choose a ' . $allowed_ext . ' file';
            echo json_encode($response);
            return;
        }

        if ($file_size > $upload_max_filesize) {
            $response['error'] = 'File size must not exceed ' . formatBytes($upload_max_filesize);
            echo json_encode($response);
            return;
        }

        $encoded_file_name = base64_encode($file_name);
        $upload_path = $tmp_path . $encoded_file_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            create_thumb($upload_path, 250, 250);
            $docData = 'data:image/' . $file_ext . ';base64,' . base64_encode(file_get_contents($upload_path));

            $checkQuery = "SELECT id FROM branding WHERE owner_id = '$userID'";
            $result = mysqli_query($conn, $checkQuery);

            if (mysqli_num_rows($result) > 0) {
                $query = "UPDATE branding SET brandLogo = '$docData' WHERE owner_id = '$userID'";
            } else {
                $query = "INSERT INTO branding (brandLogo, owner_id) VALUES ('$docData', '$userID')";
            }

            if (mysqli_query($conn, $query)) {
                unlink($upload_path);
                $response["success"] = array("msg" => "Pic updated", "pic" => $docData);
            } else {
                $response['error'] = 'Failed to update branding information.';
            }
        } else {
            $response['error'] = 'Failed to upload the file.';
        }
    } else {
        $response['error'] = 'No file uploaded.';
    }

    echo json_encode($response);
    return;
}

//IMPORT COMPOSITIONS FROM CSV
if (isset($_GET['type']) && $_GET['type'] === 'cmpCSVImport') {
    // Decode and validate ingredient ID
    $ing = base64_decode($_GET['ingID']);
    if (empty($ing)) {
        echo '<div class="alert alert-danger">Invalid ingredient ID.</div>';
        return;
    }

    if (isset($_FILES['CSVFile']['name']) && $_FILES['CSVFile']['size'] > 0) {
        $filename = $_FILES['CSVFile']['tmp_name'];
        $i = 0;

        // Open the CSV file for reading
        if (($file = fopen($filename, "r")) !== FALSE) {
            while (($data = fgetcsv($file, 10000, ",")) !== FALSE) {
                // Sanitize and validate input data
                $name = trim(ucwords($data[0] ?? ''));
                $cas = trim($data[1] ?? '');
                $ec = trim($data[2] ?? '');
                $min_percentage = rtrim($data[3] ?? '');
                $max_percentage = rtrim($data[4] ?? '');
                $GHS = rtrim($data[5] ?? '');

                // Skip rows with empty name or invalid data
                if (empty($name)) {
                    continue;
                }

                // Check if the compound already exists
                $checkQuery = "SELECT name FROM ingredient_compounds WHERE ing = '$ing' AND name = '$name' AND owner_id = '$userID'";
                if (!mysqli_num_rows(mysqli_query($conn, $checkQuery))) {
                    // Insert new compound into the database
                    $insertQuery = "
                        INSERT INTO ingredient_compounds 
                        (ing, name, cas, ec, min_percentage, max_percentage, GHS, owner_id) 
                        VALUES ('$ing', '$name', '$cas', '$ec', '$min_percentage', '$max_percentage', '$GHS', '$userID')
                    ";
                    $result = mysqli_query($conn, $insertQuery);

                    if ($result) {
                        $i++;
                    }
                }
            }

            fclose($file);

            // Display the result of the import
            if ($i > 0) {
                echo '<div class="alert alert-success">' . $i . ' Items imported successfully.</div>';
            } else {
                echo '<div class="alert alert-warning">No new items were imported. All items may already exist.</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Failed to open the CSV file.</div>';
        }
    } else {
        echo '<div class="alert alert-danger">No CSV file uploaded or the file is empty.</div>';
    }
    return;
}

//IMPORT INGREDIENTS FROM CSV
if (isset($_GET['type']) && $_GET['type'] === 'ingCSVImport') {
    // Default category class
    $defCatClass = $settings['defCatClass'];
    session_start();

    if (isset($_GET['step']) && $_GET['step'] === 'upload') {
        // Validate file extension
        $fileArray = explode(".", $_FILES['CSVFile']['name']);
        $extension = strtolower(end($fileArray));
        if ($extension !== 'csv') {
            echo '<div class="alert alert-danger">Invalid CSV file format. Please upload a valid CSV file.</div>';
            return;
        }

        // Read the CSV file
        $csvFileData = fopen($_FILES['CSVFile']['tmp_name'], 'r');
        if (!$csvFileData) {
            echo '<div class="alert alert-danger">Unable to open the uploaded file.</div>';
            return;
        }

        // Process the header row
        $fileHeader = fgetcsv($csvFileData, 10000, ",");
        echo '<table class="jj table table-bordered"><thead><tr class="csv_upload_header">';
        foreach ($fileHeader as $index => $header) {
            echo '<th>
                    <select name="set_column_data" class="form-control set_column_data" data-column_number="' . $index . '">
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

        // Process the remaining rows
        $tempData = [];
        $rowIndex = 0;
        while (($row = fgetcsv($csvFileData, 100000, ",")) !== FALSE) {
            echo '<tr id="' . $rowIndex . '">';
            foreach ($row as $cell) {
                echo '<td>' . (!empty($cell) ? htmlspecialchars($cell) : '-') . '</td>';
            }
            echo '</tr>';
            $tempData[] = $row;
            $rowIndex++;
        }
        fclose($csvFileData);

        // Store data in session
        $_SESSION['csv_file_data'] = $tempData;
        echo '</tbody></table>';
    }

    if (isset($_GET['step']) && $_GET['step'] === 'import') {
        // Import the CSV data
        if (!isset($_SESSION['csv_file_data'])) {
            echo '<div class="alert alert-danger">No uploaded CSV data found in the session.</div>';
            return;
        }

        $csvFileData = $_SESSION['csv_file_data'];
        $insertData = [];
        $importedCount = 0;

        foreach ($csvFileData as $row) {
            $ingredientName = trim(ucwords($row[$_POST["ingredient_name"] ?? ''] ?? ''));
            if (empty($ingredientName)) {
                continue; // Skip rows with no name
            }

            // Check for duplicate ingredients
            $checkQuery = "SELECT name FROM ingredients WHERE name = '" . mysqli_real_escape_string($conn, $ingredientName) . "' AND owner_id = '$userID'";
            if (!mysqli_num_rows(mysqli_query($conn, $checkQuery))) {
                $insertData[] = "(
                    '" . mysqli_real_escape_string($conn, $row[$_POST["ingredient_name"]]) . "',
                    '" . mysqli_real_escape_string($conn, $row[$_POST["iupac"]]) . "',
                    '" . mysqli_real_escape_string($conn, $row[$_POST["cas"]]) . "',
                    '" . mysqli_real_escape_string($conn, $row[$_POST["fema"]]) . "',
                    '" . mysqli_real_escape_string($conn, $row[$_POST["type"]]) . "',
                    '" . mysqli_real_escape_string($conn, $row[$_POST["strength"]]) . "',
                    '" . mysqli_real_escape_string($conn, $row[$_POST["profile"]]) . "',
                    '" . mysqli_real_escape_string($conn, $row[$_POST["physical_state"]]) . "',
                    '" . mysqli_real_escape_string($conn, $row[$_POST["allergen"]]) . "',
                    '" . mysqli_real_escape_string($conn, $row[$_POST["odor"]]) . "',
                    '" . mysqli_real_escape_string($conn, $row[$_POST["impact_top"]]) . "',
                    '" . mysqli_real_escape_string($conn, $row[$_POST["impact_heart"]]) . "',
                    '" . mysqli_real_escape_string($conn, $row[$_POST["impact_base"]]) . "',
                    '$userID'
                )";
                $importedCount++;
            }
        }

        // Insert new records into the database
        if (!empty($insertData)) {
            $query = "INSERT INTO ingredients (name, INCI, cas, FEMA, type, strength, profile, physical_state, allergen, odor, impact_top, impact_heart, impact_base, owner_id) VALUES " . implode(", ", $insertData);
            $result = mysqli_query($conn, $query);

            if ($result) {
                echo '<div class="alert alert-success">' . $importedCount . ' ingredients imported successfully.</div>';
            } else {
                echo '<div class="alert alert-danger">Failed to import ingredients. Error: ' . mysqli_error($conn) . '</div>';
            }
        } else {
            echo '<div class="alert alert-info">No new data to import; all entries already exist.</div>';
        }
    }
    return;
}

//IMPORT FORMULAS CSV
if (isset($_GET['type']) && $_GET['type'] === 'frmCSVImport') {
    session_start();

    if (isset($_GET['step']) && $_GET['step'] === 'upload') {
        // Validate file extension
        $fileArray = explode(".", $_FILES['CSVFile']['name']);
        $extension = strtolower(end($fileArray));
        if ($extension !== 'csv') {
            echo '<div class="alert alert-danger">Invalid CSV file format. Please upload a valid CSV file.</div>';
            return;
        }

        // Read the CSV file
        $csvFileData = fopen($_FILES['CSVFile']['tmp_name'], 'r');
        if (!$csvFileData) {
            echo '<div class="alert alert-danger">Unable to open the uploaded file.</div>';
            return;
        }

        // Process the header row
        $fileHeader = fgetcsv($csvFileData, 1000, ",");
        echo '<table class="jj table table-bordered"><thead><tr class="csv_upload_header">';
        foreach ($fileHeader as $index => $header) {
            echo '<th>
                    <select name="set_column_data" class="form-control set_column_data" data-column_number="' . $index . '">
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

        // Process the remaining rows
        $tempData = [];
        $rowIndex = 0;
        while (($row = fgetcsv($csvFileData, 1000, ",")) !== FALSE) {
            echo '<tr id="' . $rowIndex . '">';
            foreach ($row as $cell) {
                echo '<td>' . (!empty($cell) ? htmlspecialchars($cell) : '-') . '</td>';
            }
            echo '</tr>';
            $tempData[] = $row;
            $rowIndex++;
        }
        fclose($csvFileData);

        // Store data in session
        $_SESSION['csv_file_data'] = $tempData;
        echo '</tbody></table>';
    }

    if (isset($_GET['step']) && $_GET['step'] === 'import') {
        // Validate form inputs
        $name = mysqli_real_escape_string($conn, trim($_POST['formula_name'] ?? ''));
        $profile = $_POST['formula_profile'] ?? '';

        if (empty($name)) {
            echo '<div class="alert alert-danger">The formula name field cannot be empty.</div>';
            return;
        }

        // Generate a unique FID
        require_once(__ROOT__ . '/func/genFID.php');
        $fid = random_str(40, '1234567890abcdefghijklmnopqrstuvwxyz');

        // Check for duplicate formula names
        $checkQuery = "SELECT id FROM formulasMetaData WHERE name = '$name' AND owner_id = '$userID'";
        if ($chk = mysqli_fetch_assoc(mysqli_query($conn, $checkQuery))) {
            echo '<div class="alert alert-danger">Error: The formula <strong>' . htmlspecialchars($name) . '</strong> already exists! Click <a href="/?do=Formula&id=' . $chk['id'] . '" target="_blank">here</a> to view/edit.</div>';
            return;
        }

        // Retrieve CSV data from session
        if (!isset($_SESSION['csv_file_data'])) {
            echo '<div class="alert alert-danger">No uploaded CSV data found in the session.</div>';
            return;
        }

        $csvFileData = $_SESSION['csv_file_data'];
        $insertData = [];

        foreach ($csvFileData as $row) {
            $ingredient = mysqli_real_escape_string($conn, trim($row[$_POST['ingredient']] ?? ''));
            $concentration = preg_replace("/[^0-9.]/", "", $row[$_POST['concentration']] ?? '100');
            $dilutant = preg_replace("/[^a-zA-Z ]/", "", $row[$_POST['dilutant']] ?? 'None');
            $quantity = preg_replace("/[^0-9.]/", "", $row[$_POST['quantity']] ?? '0');

            if (!empty($ingredient)) {
                $insertData[] = "('$fid', '$name', '$ingredient', '$concentration', '$dilutant', '$quantity', '$userID')";
            }
        }

        // Insert formula data
        if (!empty($insertData)) {
            $query = "INSERT INTO formulas (fid, name, ingredient, concentration, dilutant, quantity, owner_id) VALUES " . implode(",", $insertData);
            $result = mysqli_query($conn, $query);

            if ($result) {
                $metaQuery = "INSERT INTO formulasMetaData (fid, name, notes, profile, owner_id) VALUES ('$fid', '$name', 'Imported via CSV', '$profile', '$userID')";
                if (mysqli_query($conn, $metaQuery)) {
                    echo '<div class="alert alert-success"><strong><a href="/?do=Formula&id=' . mysqli_insert_id($conn) . '" target="_blank">Formula ' . htmlspecialchars($name) . '</a></strong> has been successfully imported!</div>';
                } else {
                    echo '<div class="alert alert-danger">Failed to save metadata for the formula.</div>';
                }
            } else {
                echo '<div class="alert alert-danger">Failed to import formula data. Error: ' . mysqli_error($conn) . '</div>';
            }
        } else {
            echo '<div class="alert alert-info">No data to import; all rows are empty or invalid.</div>';
        }
    }
    return;
}

//IMPORT IFRA XLSX
if ($_GET['type'] == 'IFRA') {
    if (isset($_FILES['ifraXLS'])) {
        $filename = $_FILES["ifraXLS"]["tmp_name"];
        $fileExt = strtolower(pathinfo($_FILES['ifraXLS']['name'], PATHINFO_EXTENSION));
        $allowedExt = ['xls', 'xlsx'];

        // Check file extension
        if (!in_array($fileExt, $allowedExt)) {
            $response['error'] = 'Extension not allowed, please choose an xls or xlsx file.';
            echo json_encode($response);
            return;
        }

        // Check file size
        if ($_FILES["ifraXLS"]["size"] > 0) {
            require_once(__ROOT__ . '/func/SimpleXLSX.php');

            // Truncate table if overwrite is enabled
            if ($_GET['overwrite'] == 'true') {
                mysqli_query($conn, "DELETE FROM IFRALibrary WHERE owner_id = '$userID'");
            }

            $xlsx = SimpleXLSX::parse($filename);
            $dbHost = mysqli_real_escape_string($conn, $dbhost);
            $dbName = mysqli_real_escape_string($conn, $dbname);
            $dbUser = mysqli_real_escape_string($conn, $dbuser);
            $dbPass = mysqli_real_escape_string($conn, $dbpass);

            // Establish PDO connection
            try {
                $link = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
                $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                error_log("PV error: Failed to connect to the database: " . $e->getMessage());
                $response['error'] = 'Database connection error.';
                echo json_encode($response);
                return;
            }

            // Determine fields based on IFRA version
            switch ($_GET['IFRAVer']) {
                case 49:
                    $fields = 'ifra_key,image,amendment,prev_pub,last_pub,deadline_existing,deadline_new,name,cas,cas_comment,synonyms,formula,flavor_use,prohibited_notes,restricted_photo_notes,restricted_notes,specified_notes,type,risk,contrib_others,contrib_others_notes,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12,owner_id';
                    break;
                case 51:
                    $fields = 'ifra_key,amendment,prev_pub,last_pub,deadline_existing,deadline_new,name,cas,cas_comment,synonyms,type,risk,flavor_use,prohibited_notes,restricted_photo_notes,restricted_notes,specified_notes,contrib_others,contrib_others_notes,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12,owner_id';
                    break;
                default:
                    $response['error'] = 'Invalid IFRA version selected.';
                    echo json_encode($response);
                    return;
            }

            // Prepare insert statement
            $valuesPlaceholder = implode(',', array_fill(0, count(explode(',', $fields)), '?'));
            $stmt = $link->prepare("INSERT INTO IFRALibrary ($fields) VALUES ($valuesPlaceholder)");
            $columns = explode(',', $fields);
            $colsCount = count($columns);

            foreach ($xlsx->rows() as $rowIndex => $row) {
                if ($rowIndex === 0) continue; // Skip header row

                foreach ($columns as $colIndex => $columnName) {
                    $value = $row[$colIndex] ?? null;

                    // Handle category columns for invalid numeric data
                    if (strpos($columnName, 'cat') === 0 && !is_numeric($value)) {
                        $value = 100;
                    }

                    $stmt->bindValue($colIndex + 1, $value);
                }

                // Add owner_id
                $stmt->bindValue($colsCount, $userID);

                try {
                    $stmt->execute();
                } catch (Exception $e) {
                    error_log("PV error: Failed to insert row into IFRALibrary: " . $e->getMessage());
                    $response['error'] = 'Error importing data. Please check the file format.';
                    echo json_encode($response);
                    return;
                }
            }

            // Update CAS if enabled
            if ($_GET['updateCAS'] == 'true') {
                fixIFRACas($conn);
            }

            $response['success'] = 'Import successful.';
            echo json_encode($response);
            return;
        }
    }
}

?>

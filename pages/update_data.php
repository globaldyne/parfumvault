<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');

require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/validateInput.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/sanChar.php');
require_once(__ROOT__.'/func/priceScrape.php');
require_once(__ROOT__.'/func/create_thumb.php');
require_once(__ROOT__.'/func/pvFileGet.php');

//DELETE BATCH
if($_POST['action'] == 'batch' && $_POST['bid'] && $_POST['remove'] == true){
	$id = mysqli_real_escape_string($conn, $_POST['bid']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "DELETE FROM batchIDHistory WHERE id = '$id'")){
	
		$response["success"] = 'Batch '.$id.' for product '.$name.' deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//UPDATE SDS DISCLAIMER
if($_POST['settings'] == 'sds'){
	$sds_disc_content = mysqli_real_escape_string($conn, $_POST['sds_disc_content']);
	
	if(empty($sds_disc_content)){
		$response["error"] = 'Disclaimer text is required.';
		echo json_encode($response);
		return;
	}


	if(mysqli_query($conn, "UPDATE settings SET  sds_disclaimer = '$sds_disc_content'")){
		$response["success"] = 'SDS Disclaimer text updated';
	}else{
		$response["error"] = 'Error '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//DELETE SDS
if($_POST['action'] == 'delete' && $_POST['SDSID'] && $_POST['type'] == 'SDS'){
	$id = mysqli_real_escape_string($conn, $_POST['SDSID']);
	
	if(mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '$id' AND isSDS = '1'")){
		mysqli_query($conn, "DELETE FROM sds_data WHERE id = '$id'");
		
		$response["success"] = 'SDS deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//ADD INVENTORY COMPOUND
if($_POST['action'] == 'add' && $_POST['type'] == 'invCmp'){
	$name = mysqli_real_escape_string($conn, $_POST['cmp_name']);
	$size = mysqli_real_escape_string($conn, $_POST['cmp_size']);
	
	if(empty($name)){
		$response["error"] = 'Name is required.';
		echo json_encode($response);
		return;
	}
	if(!is_numeric($size)){
		$response["error"] = 'Size can only be numeric';
		echo json_encode($response);
		return;
	}
	$batch_id = mysqli_real_escape_string($conn, $_POST['cmp_batch']);
	$location = mysqli_real_escape_string($conn, $_POST['cmp_location']);
	$description = mysqli_real_escape_string($conn, $_POST['cmp_desc']);
	$label_info = mysqli_real_escape_string($conn, $_POST['cmp_label_info']);

	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM inventory_compounds WHERE name = '$name'"))){
		$response["error"] = 'Error: '.$name.' already exists!';
		
	}elseif(mysqli_query($conn, "INSERT INTO inventory_compounds (name,description,batch_id,size,owner_id,location,label_info) VALUES ('$name', '$description', '$batch_id', '$size', '".$user['id']."', '$location', '$label_info' )")){
		$response["success"] = 'Compound '.$name.' added!';
	}else{
		$response["error"] = 'Error adding compound '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//UPDATE COMPOUND DATA
if($_POST['update_inv_compound_data']){
	
	if(!$_POST['name']){
		$response["error"] = "Name is required";
		echo json_encode($response);
		return;
	}
	
	$id = $_POST['cmp_id'];
	$name = $_POST['name'];
	$description = $_POST['description'];
	$batch_id = $_POST['batch_id'];
	$size = $_POST['size'];
	$location  = $_POST['location'] ?: '-';
	$label_info  = $_POST['label_info'] ?: '-';

	$q = mysqli_query($conn,"UPDATE inventory_compounds SET name = '$name', description = '$description', batch_id = '$batch_id', size = '$size', location = '$location', label_info = '$label_info' WHERE id = '$id'");
	

	if($q){
		$response['success'] = "Compound updated";
	}else{
		$response['error'] = "Error updating data ".mysqli_error($conn);
	}
	
	echo json_encode($response);
	
	

	return;
}

//DELETE COMPOUND
if($_POST['action'] == 'delete' && $_POST['compoundId'] && $_POST['type'] == 'invCmp'){
	$id = mysqli_real_escape_string($conn, $_POST['compoundId']);
	
	if(mysqli_query($conn, "DELETE FROM inventory_compounds WHERE id = '$id'")){
		$response["success"] = 'Compound deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}



//WIPE OUT FROMULAS
if($_POST['formulas_wipe'] == 'true'){
	
	if(mysqli_query($conn, "TRUNCATE formulas")){
		mysqli_query($conn, "TRUNCATE formulasMetaData");
		mysqli_query($conn, "TRUNCATE formula_history");
		mysqli_query($conn, "TRUNCATE formulasTags");
		mysqli_query($conn, "TRUNCATE ingredient_compounds");
		mysqli_query($conn, "TRUNCATE formulaCategories");
		mysqli_query($conn, "TRUNCATE formulasRevisions");
		mysqli_query($conn, "TRUNCATE makeFormula");

		$response["success"] = 'Formulas and related data deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//WIPE OUT INGREDIENTS
if($_POST['ingredient_wipe'] == 'true'){
	
	if(mysqli_query($conn, "TRUNCATE ingredients")){
		mysqli_query($conn, "TRUNCATE ingCategory");
		mysqli_query($conn, "TRUNCATE ingProfiles");
		mysqli_query($conn, "TRUNCATE ingReplacements");
		mysqli_query($conn, "TRUNCATE ingSafetyInfo");
		mysqli_query($conn, "TRUNCATE suppliers");
		mysqli_query($conn, "TRUNCATE synonyms");

		$response["success"] = 'Ingredients and related data deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}


//UPDATE CAS IFRA ENTRY
if($_REQUEST['IFRA'] == 'edit'){
	$type = $_REQUEST['type'];
	if(mysqli_query($conn, "UPDATE IFRALibrary SET $type = '".$_REQUEST['value']."' WHERE id = '".$_REQUEST['pk']."'")){
		$response["success"] = 'IFRA entry updated';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//DELETE IFRA ENTRY
if($_POST['IFRA'] == 'delete' && $_POST['ID'] && $_POST['type'] == 'IFRA'){
	
	if(mysqli_query($conn, "DELETE FROM IFRALibrary WHERE id = '".$_POST['ID']."'")){
		$response["success"] = 'IFRA entry deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//Merge ingredients
if($_POST['merge'] && $_POST['ingSrcID'] &&  $_POST['ingSrcName']  && $_POST['fid']){
	if(!$_POST['dest']){
		$response['error'] = 'Please select ingedient';
		echo json_encode($response);
    	return;
	}
	
	$dest = mysqli_fetch_array(mysqli_query($conn,"SELECT ingredient FROM formulas WHERE id = '".$_POST['dest']."'"));
	
	if($dest['ingredient'] == $_POST['ingSrcName']){
		$response['error'] = 'Source and destination ingredients cannot be the same';
		echo json_encode($response);
    	return;
	}
	
	$mq = "UPDATE formulas SET quantity = quantity + (SELECT quantity FROM formulas WHERE id ='".$_POST['ingSrcID']."' AND fid = '".$_POST['fid']."') WHERE id = '".$_POST['dest']."' AND fid = '".$_POST['fid']."'";
	
	if(mysqli_query($conn,$mq)){
		mysqli_query($conn,"DELETE FROM formulas WHERE id = '".$_POST['ingSrcID']."' AND fid = '".$_POST['fid']."'");
		$response['success'] = $_POST['ingSrcName'].' merged with '.$dest['ingredient'];
	}else{
		$response['error'] = 'Something went wrong..'.mysqli_error($conn);
	}
	
	echo json_encode($response);
    return;

}

//PVOnline Single Import						
if($_POST['action'] == 'import' && $_POST['source'] == 'PVOnline' && $_POST['kind'] == 'ingredient' && $_POST['ing_id']){
	$id = mysqli_real_escape_string($conn, $_POST['ing_id']);
	
	$jAPI = $pvOnlineAPI.'?request=ingredients&src=PV_PRO&id='.$id;
    $jsonData = json_decode(pv_file_get_contents($jAPI), true);

    if($jsonData['error']){
		$response['error'] = 'Error: '.$jsonData['error']['msg'];
		echo json_encode($response);
        return;
    }

    $array_data = $jsonData['ingredients'];
	
    foreach ($array_data as $id=>$row) {
      	$insertPairs = array();
		unset($row['structure']);
		unset($row['techData']);
		unset($row['ifra']);
		unset($row['IUPAC']);
		unset($row['id']);
			
         foreach ($row as $key=>$val){ 
		 
          	$insertPairs[addslashes($key)] = addslashes($val);
         }
		 
         $insertKeys = '`' . implode('`,`', array_keys($insertPairs)) . '`';
         $insertVals = '"' . implode('","', array_values($insertPairs)) . '"';
    
       	 $query = "SELECT name FROM ingredients WHERE name = '".$insertPairs['name']."'";
    
         if(!mysqli_num_rows(mysqli_query($conn, $query))){
           	$jsql = "INSERT INTO ingredients ({$insertKeys}) VALUES ({$insertVals});";
            if( $qIns = mysqli_query($conn,$jsql)){
				
             	$response["success"] = 'Ingredient data imported';
			}else{
				$response["error"] = 'Error: '.mysqli_error($conn);
			}
		 }else{
			 $response["error"] = 'Error: ingredient already exists';
		 }
	}
	

	
	echo json_encode($response);
	return;
}


//UPDATE BK PROVIDER
if ($_REQUEST['bkProv'] == 'update') {
    if ( empty($_POST['creds']) || empty($_POST['schedule']) || empty($_POST['bkDesc']) || empty($_POST['gdrive_name'])) {
        $response["error"] = 'Missing fields';
        echo json_encode($response);
        return;
    }
    
    $enabled = mysqli_real_escape_string($conn, $_POST['enabled']);
    $schedule = mysqli_real_escape_string($conn, $_POST['schedule']);
    $bkDesc = mysqli_real_escape_string($conn, $_POST['bkDesc']);
    $creds = mysqli_real_escape_string($conn, $_POST['creds']);
    $gdrive_name = mysqli_real_escape_string($conn, $_POST['gdrive_name']);
	$bk_srv_host = $_POST['bk_srv_host'];


    if (mysqli_query($conn, "UPDATE backup_provider SET credentials = '$creds', enabled = '$enabled', schedule = '$schedule', description = '$bkDesc', gdrive_name = '$gdrive_name' WHERE id = '1'")) {
		mysqli_query($conn, "UPDATE settings SET bk_srv_host = '$bk_srv_host'");
        $response["success"] = 'Settings updated';
    } else {
        $response["error"] = 'Error: ' . mysqli_error($conn);
    }

    echo json_encode($response);
    return;
}




//UPDATE HTML TEMPLATE
if($_REQUEST['tmpl'] == 'update'){
	$value = mysqli_real_escape_string($conn,$_POST['value']);
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE templates SET $name = '$value' WHERE id = '$id'")){
		$response["success"] = 'Template updated';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

//DELETE HTML TEMPLATE
if($_POST['tmpl'] == 'delete' && $_POST['tmplID']){
	$id = $_POST['tmplID'];
	$name = $_POST['tmplName'];

	if(mysqli_query($conn, "DELETE FROM templates WHERE id = '$id'")){
		$response["success"] = 'Template '.$name.' deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//ADD NEW TEMPLATE
if($_POST['tmpl'] == 'add'){
	
	if(empty($_POST['tmpl_name'])){
		$response["error"] = 'Name is required';
		echo json_encode($response);
		return;
	}
	
	if(empty($_POST['tmpl_content'])){
		$response["error"] = 'HTML Content is required';
		echo json_encode($response);
		return;
	}

	if(empty($_POST['tmpl_desc'])){
		$response["error"] = 'Description is required.';
		echo json_encode($response);
		return;
	}
	
	$name = mysqli_real_escape_string($conn,$_POST['tmpl_name']);
	$html = mysqli_real_escape_string($conn,$_POST['tmpl_content']);
	$desc = mysqli_real_escape_string($conn,$_POST['tmpl_desc']);

	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM templates WHERE name = '$name'"))){
		$response["error"] = $name.' already exists!';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn, "INSERT INTO templates (name,content,description) VALUES ('$name','$html','$desc')")){
		$response["success"] = $name.' created!';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}



//UPDATE PERFUME TYPES
if($_GET['perfType'] == 'update'){
	$value = trim(mysqli_real_escape_string($conn, $_POST['value']));
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE perfumeTypes SET $name = '$value' WHERE id = '$id'")){
		$response["success"] = 'Perfume type updated';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}

//DELETE PERFUME TYPE
if($_POST['perfType'] == 'delete' && $_POST['pID']){
	$id = $_POST['pID'];
	$name = $_POST['pName'];

	if(mysqli_query($conn, "DELETE FROM perfumeTypes WHERE id = '$id'")){
		$response["success"] = 'Pefume type '.$name.' deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//ADD PERFUME TYPE
if($_POST['perfType'] == 'add'){
	
	if(empty($_POST['perfType_name'])){
		$response["error"] = 'Name is required';
		echo json_encode($response);
		return;
	}
	
	if(empty($_POST['perfType_conc'])){
		$response["error"] = 'Concentration is required';
		echo json_encode($response);
		return;
	}

	if(!is_numeric($_POST['perfType_conc'])){
		$response["error"] = 'Concentration must be integer.';
		echo json_encode($response);
		return;
	}
	
	$name = $_POST['perfType_name'];
	$conc = $_POST['perfType_conc'];
	$desc = $_POST['perfType_desc'];

	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM perfumeTypes WHERE name = '$name'"))){
		$response["error"] = $name.' already exists!';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn, "INSERT INTO perfumeTypes (name,concentration,description) VALUES ('$name','$conc','$desc')")){
		$response["success"] = $name.' created!';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//UPDATE LID PIC
if($_GET['update_lid_pic']){
	$allowed_ext = "png, jpg, jpeg, gif, bmp";

	$filename = $_FILES["lid_pic"]["tmp_name"];  
    $file_ext = strtolower(end(explode('.',$_FILES['lid_pic']['name'])));
	$file_tmp = $_FILES['lid_pic']['tmp_name'];
    $ext = explode(', ',strtolower($allowed_ext));

	if(!$filename){
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
	$lid = $_GET['lid_id'];
	if($_FILES["lid_pic"]["size"] > 0){
		move_uploaded_file($file_tmp,$tmp_path.base64_encode($filename));
		$pic = base64_encode($filename);		
		create_thumb($tmp_path.$pic,250,250); 
		$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents($tmp_path.$pic));
		
		mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '".$lid."' AND type = '5'");
		if(mysqli_query($conn, "INSERT INTO documents (ownerID,name,type,notes,docData) VALUES ('".$lid."','-','5','-','$docData')")){	
			unlink($tmp_path.$pic);
			$response["success"] = array( "msg" => "Pic updated!", "lid_pic" => $docData);
			echo json_encode($response);
			return;
		}
		$response["error"] = mysqli_error($conn);
		echo json_encode($response);
		return;
	}

	return;
}

//UPDATE BOTTLE PIC
if($_GET['update_bottle_pic']){
	$allowed_ext = "png, jpg, jpeg, gif, bmp";

	$filename = $_FILES["bottle_pic"]["tmp_name"];  
    $file_ext = strtolower(end(explode('.',$_FILES['bottle_pic']['name'])));
	$file_tmp = $_FILES['bottle_pic']['tmp_name'];
    $ext = explode(', ',strtolower($allowed_ext));

	
	if(!$filename){
		//$response["error"] = 'Please choose a file to upload';
		//echo json_encode($response);
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
	$bottle = $_GET['bottle_id'];
	if($_FILES["bottle_pic"]["size"] > 0){
		move_uploaded_file($file_tmp,$tmp_path.base64_encode($filename));
		$bottle_pic = base64_encode($filename);		
		create_thumb($tmp_path.$bottle_pic,250,250); 
		$docData = 'data:application/' . $file_ext . ';base64,' . base64_encode(file_get_contents($tmp_path.$bottle_pic));
		
		mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '".$bottle."' AND type = '4'");
		if(mysqli_query($conn, "INSERT INTO documents (ownerID,name,type,notes,docData) VALUES ('".$bottle."','-','4','-','$docData')")){	
			unlink($tmp_path.$bottle_pic);
			$response["success"] = array( "msg" => "Pic updated!", "bottle_pic" => $docData);
			echo json_encode($response);
			return;
		}
		$response["error"] = mysqli_error($conn);
		echo json_encode($response);
		return;
	}

	return;
}

//UPDATE BOTTLE DATA
if($_POST['update_bottle_data']){
	
	if(!$_POST['name']){
		$response["error"] = "Name is required";
		echo json_encode($response);
		return;
	}
	
	if(!is_numeric($_POST['size'])){
		$response["error"] = "Size is invalid";
		echo json_encode($response);
		return;
	}
	
	if(!is_numeric($_POST['price'])){
		$response["error"] = "Price is invalid";
		echo json_encode($response);
		return;
	}
		
	$id = $_POST['bottle_id'];
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ml = $_POST['size'];
	$price = $_POST['price'];
	$height = $_POST['height'];
	$width = $_POST['width'];
	$diameter = $_POST['diameter'];
	$supplier = $_POST['supplier'];
	$supplier_link = $_POST['supplier_link'];
	$notes = $_POST['notes'];
	$pieces = $_POST['pieces']?:0;
	$weight = $_POST['weight']?:0;

	$q = mysqli_query($conn,"UPDATE bottles SET name= '$name', ml = '$ml', price = '$price', height = '$height', width = '$width', diameter = '$diameter', supplier = '$supplier', supplier_link = '$supplier_link', notes = '$notes', pieces = '$pieces', weight = '$weight' WHERE id = '$id'");
	

	if($q){
		$response['success'] = "Bottle updated";
	}else{
		$response['error'] = "Error updating bottle data ".mysqli_error($conn);
	}
	
	echo json_encode($response);
	
	

	return;
}

//UPDATE LID DATA
if($_POST['update_lid_data']){
	
	if(!$_POST['style']){
		$response["error"] = "Style is required";
		echo json_encode($response);
		return;
	}
	$id = $_POST['lid_id'];
	$style = $_POST['style'];
	$colour = $_POST['colour'];
	$price = $_POST['price'];
	$supplier = $_POST['supplier'];
	$supplier_link = $_POST['supplier_link'];
	$pieces = $_POST['pieces']?:0;
	
	$q = mysqli_query($conn,"UPDATE lids SET style= '$style', colour = '$colour', price = '$price', supplier = '$supplier', supplier_link = '$supplier_link', pieces = '$pieces' WHERE id = '$id'");
	

	if($q){
		$response['success'] = "Lid updated";
	}else{
		$response['error'] = "Error updating lid data ".mysqli_error($conn);
	}
	
	echo json_encode($response);
	
	

	return;
}
//DELETE BOTTLE
if($_POST['action'] == 'delete' && $_POST['btlId'] && $_POST['type'] == 'bottle'){
	$id = mysqli_real_escape_string($conn, $_POST['btlId']);
	
	if(mysqli_query($conn, "DELETE FROM bottles WHERE id = '$id'")){
		mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '$id' AND type = '4'");
		$response["success"] = 'Bottle deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//DELETE LID
if($_POST['action'] == 'delete' && $_POST['lidId'] && $_POST['type'] == 'lid'){
	$id = mysqli_real_escape_string($conn, $_POST['lidId']);
	
	if(mysqli_query($conn, "DELETE FROM lids WHERE id = '$id'")){
		mysqli_query($conn, "DELETE FROM documents WHERE ownerID = '$id' AND type = '5'");
		$response["success"] = 'Lid deleted';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;	
}

//IMPORT IMAGES FROM PUBCHEM
if(isset($_GET['IFRA_PB']) && $_GET['IFRA_PB'] === 'import'){
    require_once(__ROOT__.'/func/pvFileGet.php');
    
    $i = 0;
    $response = [];

    $qCas = mysqli_query($conn, "SELECT cas FROM IFRALibrary WHERE image IS NULL OR image = '' OR image = '-'");

    if(!$qCas || mysqli_num_rows($qCas) == 0){
        $response["error"] = 'IFRA Database is currently empty or there was a problem with the query.';
        echo json_encode($response);
        return;
    }

    $view = $settings['pubchem_view'];

    while($cas = mysqli_fetch_assoc($qCas)){
        $casNumber = trim($cas['cas']);
        $imageUrl = $pubChemApi.'/pug/compound/name/'.$casNumber.'/PNG?record_type='.$view.'&image_size=small';
        $imageContent = pv_file_get_contents($imageUrl);
        
        if($imageContent !== false){
            $image = base64_encode($imageContent);
            $stmt = $conn->prepare("UPDATE IFRALibrary SET image = ? WHERE cas = ?");
            $stmt->bind_param('ss', $image, $casNumber);

            if($stmt->execute()){
                $i++;
            } else {
                $response["error"] = 'Error updating image for CAS: '.$casNumber.' - '.mysqli_error($conn);
                echo json_encode($response);
                return;
            }

            $stmt->close();
        }

        usleep(100000); // 0.1 seconds delay
    }

    $response["success"] = $i.' images updated!';
    echo json_encode($response);
    return;
}


//Update data FROM PubChem
if($_POST['pubChemData'] == 'update' && $_POST['cas']){
	$cas = trim($_POST['cas']);
	$molecularWeight = $_POST['molecularWeight'];
	$logP = trim($_POST['logP']);
	$molecularFormula = $_POST['molecularFormula'];
	$InChI = $_POST['InChI'];
	$CanonicalSMILES = $_POST['CanonicalSMILES'];
	$ExactMass = trim($_POST['ExactMass']);
	
	if($molecularWeight){
		$q = mysqli_query($conn, "UPDATE ingredients SET molecularWeight = '$molecularWeight' WHERE cas='$cas'");
	}
	if($logP){
		$q.= mysqli_query($conn, "UPDATE ingredients SET logp = '$logP' WHERE cas='$cas'");
	}
	if($molecularFormula){
		$q.= mysqli_query($conn, "UPDATE ingredients SET formula = '$molecularFormula' WHERE cas='$cas'");
	}
	if($InChI){
		$q.= mysqli_query($conn, "UPDATE ingredients SET INCI = '$InChI' WHERE cas='$cas'");
	}
	if($q){
		$response["success"] = 'Local data updated';
	}else{
		$response["error"] = 'Unable to update data';
	}
	echo json_encode($response);
	return;
}

//IMPORT SYNONYMS FROM PubChem
if($_POST['synonym'] == 'import' && $_POST['method'] == 'pubchem'){
	$ing = base64_decode($_POST['ing']);
	$cas = trim($_POST['cas']);

	$u = $pubChemApi.'/pug/compound/name/'.$cas.'/synonyms/JSON';
	$json = file_get_contents($u);
	$json = json_decode($json);
	$data = $json->InformationList->Information[0]->Synonym;
	$cid = $json->InformationList->Information[0]->CID;
	$source = 'PubChem';
	if(empty($data)){
		$response["error"] = 'No data found';
		echo json_encode($response);
		return;
	}
	$i=0;
	foreach($data as $d){
		$einecs = explode('EINECS ',$d);
		if($einecs['1']){
			mysqli_query($conn, "UPDATE ingredients SET einecs = '".$einecs['1']."' WHERE cas = '$cas'");
		}
		$fema = explode('FEMA ',$d);
		if($fema['1']){
			mysqli_query($conn, "UPDATE ingredients SET FEMA = '".preg_replace("/[^0-9]/", "", $fema['1'])."' WHERE cas = '$cas'");
		}
		$sql = mysqli_query($conn, "SELECT synonym FROM synonyms WHERE synonym = '$d' AND ing = '$ing'");
		if(!$sql || !mysqli_num_rows($sql)){
			$r = mysqli_query($conn, "INSERT INTO synonyms (ing,cid,synonym,source) VALUES ('$ing','$cid','$d','$source')");		
		 	$i++;
		}
	}
	if($r){
		$response["success"] = $i.' synonym(s) imported';
	}else{
		$response["error"] = 'Data already in sync';
	}
	echo json_encode($response);
	return;
}

//ADD SYNONYM
if($_POST['synonym'] == 'add'){
	$synonym = mysqli_real_escape_string($conn, $_POST['sName']);
	$source = mysqli_real_escape_string($conn, $_POST['source']);
	
	$ing = base64_decode($_POST['ing']);

	if(empty($synonym)){
		$response["error"] = 'Synonym name is required';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT synonym FROM synonyms WHERE synonym = '$synonym' AND ing = '$ing'"))){
		$response["error"] = $synonym.' already exists!';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn, "INSERT INTO synonyms (synonym,source,ing) VALUES ('$synonym','$source','$ing')")){
		$response["success"] = $synonym.' added to the list!';
		echo json_encode($response);
	}
	
	return;
}


//UPDATE SYNONYM
if($_GET['synonym'] == 'update'){
	$value = trim(mysqli_real_escape_string($conn, $_POST['value']));
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ing = base64_decode($_GET['ing']);

	mysqli_query($conn, "UPDATE synonyms SET $name = '$value' WHERE id = '$id' AND ing='$ing'");
	return;
}


//DELETE ING SYNONYM	
if($_GET['synonym'] == 'delete'){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	mysqli_query($conn, "DELETE FROM synonyms WHERE id = '$id'");
	return;
}

//ADD REPLACEMENT
if($_POST['replacement'] == 'add'){
	
	$ing_name = base64_decode($_POST['ing_name']);
	$ing_cas = trim($_POST['ing_cas']);

	if(empty($_POST['rName'])){
		$response["error"] = 'Name is required';
		echo json_encode($response);
		return;
	}
	
	if(empty($_POST['rCAS'])){
		$response["error"] = 'CAS is required';
		echo json_encode($response);
		return;
	}

	if(mysqli_num_rows(mysqli_query($conn, "SELECT ing_rep_name FROM ingReplacements WHERE ing_name = '$ing_name' AND ing_rep_name = '".$_POST['rName']."'"))){
		$response["error"] = $_POST['rName'].' already exists!';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn, "INSERT INTO ingReplacements (ing_id,ing_name,ing_cas,ing_rep_id,ing_rep_name,ing_rep_cas,notes) VALUES ('".$_POST['ing_id']."','$ing_name','$ing_cas','".$_POST['rIngId']."','".$_POST['rName']."','".$_POST['rCAS']."','".$_POST['rNotes']."')")){
		$response["success"] = $_POST['rName'].' added to the list!';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//UPDATE ING REPLACEMENT
if($_GET['replacement'] == 'update'){
	$value = trim(mysqli_real_escape_string($conn, $_POST['value']));
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ing = base64_decode($_GET['ing']);

	if(mysqli_query($conn, "UPDATE ingReplacements SET $name = '$value' WHERE id = '$id' AND ing_name='$ing'")){
		$response["success"] = $ing.' replacement updated';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}


//DELETE ING REPLACEMENT	
if($_POST['replacement'] == 'delete'){
	$id = mysqli_real_escape_string($conn, $_POST['id']);
	if(mysqli_query($conn, "DELETE FROM ingReplacements WHERE id = '$id'")){
		$response["success"] = $_POST['name'].' replacement removed';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//UPDATE ING DOCUMENT
if($_GET['ingDoc'] == 'update'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ownerID = mysqli_real_escape_string($conn, $_GET['ingID']);

	mysqli_query($conn, "UPDATE documents SET $name = '$value' WHERE ownerID = '$ownerID' AND id='$id'");
	return;
}


//DELETE DOCUMENT	
if($_GET['doc'] == 'delete'){

	$id = mysqli_real_escape_string($conn, $_GET['id']);
	$ownerID = mysqli_real_escape_string($conn, $_GET['ownerID']);
							
	mysqli_query($conn, "DELETE FROM documents WHERE id = '$id' AND ownerID='$ownerID'");
	return;
}

//GET SUPPLIER PRICE
if($_POST['ingSupplier'] == 'getPrice'){
	$ingID = mysqli_real_escape_string($conn, $_POST['ingID']);
	$ingSupplierID = mysqli_real_escape_string($conn, $_POST['ingSupplierID']);
	$size = mysqli_real_escape_string($conn, $_POST['size']);
	$supplier_link = urldecode($_POST['sLink']);
	
	$supp_data = mysqli_fetch_array(mysqli_query($conn, "SELECT price_tag_start,price_tag_end,add_costs,price_per_size FROM ingSuppliers WHERE id = '$ingSupplierID'"));
	
	if($newPrice = priceScrape($supplier_link,$size,$supp_data['price_tag_start'],$supp_data['price_tag_end'],$supp_data['add_costs'],$supp_data['price_per_size'])){
		if(mysqli_query($conn, "UPDATE suppliers SET price = '$newPrice' WHERE ingSupplierID = '$ingSupplierID' AND ingID='$ingID'")){
			$response["success"] = '<strong>Price updated</strong>';
			echo json_encode($response);
		}
	}else{
	 	$response["error"] = '<strong>Error getting the price from the supplier. Previous value has been retained.</strong>';
		echo json_encode($response);
	}
	return;
}

//ADD ING SUPPLIER
if($_POST['ingSupplier'] == 'add'){
	if(empty($_POST['supplier_id']) || empty($_POST['supplier_link']) || empty($_POST['supplier_size']) || empty($_POST['supplier_price'])){
		$response["error"] = 'Error: Missing fields!';
		echo json_encode($response);
		return;
	}
	
	if(!is_numeric($_POST['supplier_size']) || !is_numeric($_POST['stock']) || !is_numeric($_POST['supplier_price'])){
		$response["error"] = 'Error: Only numeric values allowed in size, stock and price fields!';
		echo json_encode($response);
		return;
	}
	
	$ingID = mysqli_real_escape_string($conn, $_POST['ingID']);
	$supplier_id = mysqli_real_escape_string($conn, $_POST['supplier_id']);
	$supplier_link = mysqli_real_escape_string($conn, $_POST['supplier_link']);	
	$supplier_size = mysqli_real_escape_string($conn, $_POST['supplier_size']);
	$supplier_price = mysqli_real_escape_string($conn, $_POST['supplier_price']);
	$supplier_manufacturer = mysqli_real_escape_string($conn, $_POST['supplier_manufacturer']);
	$supplier_name = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '$supplier_id'"));
	$supplier_batch = mysqli_real_escape_string($conn, $_POST['supplier_batch']);
	$purchased = mysqli_real_escape_string($conn, $_POST['purchased'] ?: date('Y-m-d'));
	$stock = mysqli_real_escape_string($conn, $_POST['stock'] ?: 0);
	$mUnit = $_POST['mUnit'];
	$status = $_POST['status'];
	$supplier_sku = mysqli_real_escape_string($conn, $_POST['supplier_sku']);
	$internal_sku = mysqli_real_escape_string($conn, $_POST['internal_sku']);
	$storage_location = mysqli_real_escape_string($conn, $_POST['storage_location']);


	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingSupplierID FROM suppliers WHERE ingSupplierID = '$supplier_id' AND ingID = '$ingID'"))){
		$response["error"] = '<strong>Error: </strong>'.$supplier_name['name'].' already exists!';
		echo json_encode($response);
		return;
	}
		
	if(!mysqli_num_rows(mysqli_query($conn, "SELECT ingSupplierID FROM suppliers WHERE ingID = '$ingID'"))){
	   $preferred = '1';
	}else{
		$preferred = '0';
	}
		
	if(mysqli_query($conn, "INSERT INTO suppliers (ingSupplierID,ingID,supplierLink,price,size,manufacturer,preferred,batch,purchased,stock,mUnit,status,supplier_sku,internal_sku,storage_location) VALUES ('$supplier_id','$ingID','$supplier_link','$supplier_price','$supplier_size','$supplier_manufacturer','$preferred','$supplier_batch','$purchased','$stock','$mUnit','$status','$supplier_sku','$internal_sku','$storage_location')")){
		$response["success"] = $supplier_name['name'].' added.';
		echo json_encode($response);
	}else{
		$response["error"] = mysqli_error($conn);
		echo json_encode($response);
	}
	return;
}

//UPDATE ING SUPPLIER
if($_GET['ingSupplier'] == 'update'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$ingID = mysqli_real_escape_string($conn, $_GET['ingID']);

	mysqli_query($conn, "UPDATE suppliers SET $name = '$value' WHERE id = '$id' AND ingID='$ingID'");
	return;
}

//UPDATE PREFERRED SUPPLIER
if($_GET['ingSupplier'] == 'preferred'){
	$sID = mysqli_real_escape_string($conn, $_GET['sID']);
	$ingID = mysqli_real_escape_string($conn, $_GET['ingID']);
	$status = mysqli_real_escape_string($conn, $_GET['status']);
	
	mysqli_query($conn, "UPDATE suppliers SET preferred = '0' WHERE ingID='$ingID'");
	mysqli_query($conn, "UPDATE suppliers SET preferred = '$status' WHERE ingSupplierID = '$sID' AND ingID='$ingID'");
	return;
}

//DELETE ING SUPPLIER	
if($_GET['ingSupplier'] == 'delete'){

	$sID = mysqli_real_escape_string($conn, $_GET['sID']);
	$ingID = mysqli_real_escape_string($conn, $_GET['ingID']);
	/*
	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM suppliers WHERE id = '$sID' AND ingID = '$ingID' AND preferred = '1'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>Preferred supplier cannot be removed. Set as preferred another one first!</div>';
		return;
	}
	*/							
	mysqli_query($conn, "DELETE FROM suppliers WHERE id = '$sID' AND ingID='$ingID'");
	return;
}

//FORMULA QUANTITY MANAGEMENT
if($_POST['updateQuantity'] && $_POST['ingQuantityID'] &&  $_POST['ingQuantityName']  && $_POST['fid']){
	$fid = $_POST['fid'];
	$value = $_POST['ingQuantity'];
	$ingredient = $_POST['ingQuantityID'];
	$ing_name = $_POST['ingQuantityName'];
	
	if(empty($_POST['ingQuantity'])){
		$response["error"] = 'Quantity cannot be empty.';
		echo json_encode($response);
		return;
	}
	if(!is_numeric($_POST['ingQuantity'])){
		$response["error"] = 'Quantity must be numeric only.';
		echo json_encode($response);
		return;
	}
	
	if($_POST['curQuantity'] == $_POST['ingQuantity']){
		$response["error"] = 'Quantity is already the same.';
		echo json_encode($response);
		return;
	}
	
	
	if($_POST['ingReCalc'] == 'true'){
		$ingID = $_POST['ingID'];
		if(!$_POST['formulaSolventID']){
			$response["error"] = 'Please select a solvent';
			echo json_encode($response);
			return;
		}
		$formulaSolventID = $_POST['formulaSolventID'];
		
		if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM ingredients WHERE id = '".$ingID."' AND profile='solvent'"))){
			$response["error"] = 'You cannot exchange a solvent with a solvent';
			echo json_encode($response);
			return;
		}
		
		$slv = mysqli_fetch_array(mysqli_query($conn,"SELECT quantity FROM formulas WHERE ingredient_id = '".$formulaSolventID."' AND fid = '".$fid."'"));
		
		$fq = $_POST['ingQuantity'] - $_POST['curQuantity'];
		
		if($slv['quantity'] < $fq){
			$response["error"] = 'Not enough solvent, available: '.number_format($slv['quantity'],$settings['qStep']).$settings['mUnit'].', Requested: '.number_format($fq,$settings['qStep']).$settings['mUnit'];
			echo json_encode($response);
			return;
		}
		
		//UPDATE SOLVENT
		function formatVal($num){
    		return sprintf("%+.4f",$num);
		}
		
		$curV = mysqli_fetch_array(mysqli_query($conn, "SELECT quantity FROM formulas WHERE fid = '$fid' AND id = '".$ingredient."'"));
		$diff = number_format($curV['quantity'] -  $value  , 4);
		$v = formatVal($diff);
		
		$qs ="UPDATE formulas SET quantity = quantity $v WHERE fid = '$fid' AND ingredient_id = '".$formulaSolventID."'";
		if(!mysqli_query($conn, $qs)){
			$response["error"] = 'Error updating solvent: '.mysqli_error($conn);
			$response["query"] = $qs;
			echo json_encode($response);
			return;
		}
	}
		
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '".$_POST['fid']."'"));
	
	if($meta['isProtected'] == FALSE){
		
		if(mysqli_query($conn, "UPDATE formulas SET quantity = '$value' WHERE fid = '$fid' AND id = '$ingredient'")){
			
			$lg = "CHANGED: ".$ing_name." Set $name to $value";
			mysqli_query($conn, "INSERT INTO formula_history (fid,ing_id,change_made,user) VALUES ('".$meta['id']."','$ingredient','$lg','".$user['fullName']."')");
			
			$response["success"] = 'Quantity updated';
			echo json_encode($response);
		
		}else{
			$response["error"] = mysqli_error($conn);
			echo json_encode($response);
		}
		
	}
	return;
}

if($_POST['value'] && $_GET['formula'] && $_POST['pk']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, $_GET['formula']);
	$ingredient = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	
	$ing_name =  mysqli_fetch_array(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE id = '$ingredient' AND fid = '".$_GET['formula']."'"));
	
	$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT id,isProtected FROM formulasMetaData WHERE fid = '".$_GET['formula']."'"));
	if($meta['isProtected'] == FALSE){
					
		mysqli_query($conn, "UPDATE formulas SET $name = '$value' WHERE fid = '$formula' AND id = '$ingredient'");
		$lg = "CHANGED: ".$ing_name['ingredient']." Set $name to $value";
		mysqli_query($conn, "INSERT INTO formula_history (fid,ing_id,change_made,user) VALUES ('".$meta['id']."','$ingredient','$lg','".$user['fullName']."')");
echo mysqli_error($conn);
	}
	return;
}

if($_GET['formulaMeta']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$formula = mysqli_real_escape_string($conn, base64_decode($_GET['formulaMeta']));
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	if(mysqli_query($conn, "UPDATE formulasMetaData SET $name = '$value' WHERE id = '$id'")){
		$response["success"] = true;
		$response["msg"] = 'Formula meta updated';
	}
	echo json_encode($response);
	return;
}

if($_GET['createRev'] == 'man'){
	require_once(__ROOT__.'/func/createFormulaRevision.php');
	$fid = $_GET['fid'];
	
	if($l = createFormulaRevision($fid,'Manually',$conn)){
		$response["success"] = 'Revision created (If changes detected)';
		echo json_encode($response);
	}else{
		$response["error"] = 'Unable to create revision, please make sure formula exists and contains at least one ingredient.';
		echo json_encode($response);
	}
	return;
}

if($_GET['protect']){
	require_once(__ROOT__.'/func/createFormulaRevision.php');
	$fid = mysqli_real_escape_string($conn, $_GET['protect']);
	
	if($_GET['isProtected'] == 'true'){
		$isProtected = '1';
		$l = 'locked';
		createFormulaRevision($fid,'Automatic',$conn);
	}else{
		$isProtected = '0';
		$l = 'unlocked';
	}
	if(mysqli_query($conn, "UPDATE formulasMetaData SET isProtected = '$isProtected' WHERE fid = '$fid'")){
		$response["success"] = 'Formula '.$l;
		echo json_encode($response);
	}else{
		$response["error"] = 'Something went wrong';
		echo json_encode($response);
	}
	return;
}

if($_POST['formulaSettings'] &&  $_POST['set']){
	$fid = mysqli_real_escape_string($conn, $_POST['fid']);
	$set = mysqli_real_escape_string($conn, $_POST['set']);
	$val = mysqli_real_escape_string($conn, $_POST['val']);

	if(mysqli_query($conn, "UPDATE formulasMetaData SET $set = '$val' WHERE fid = '$fid'")){
		$response["success"] = "Formula settings updated";
		echo json_encode($response);
	}else{
		$response["error"] = 'Something went wrong';
		echo json_encode($response);
	}
	return;
}



if($_GET['action'] == 'rename' && $_GET['fid']){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$fid = mysqli_real_escape_string($conn, $_GET['fid']);
	$id = $_POST['pk'];
	if(!$value){
		$response["error"] = 'Formula name cannot be empty';
		echo json_encode($response);
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM formulasMetaData WHERE name = '$value'"))){
		$response["error"] = 'Name already exists';
	}else{
		mysqli_query($conn, "UPDATE formulasMetaData SET name = '$value' WHERE id = '$id'");
		if(mysqli_query($conn, "UPDATE formulas SET name = '$value' WHERE fid = '$fid'")){
			$response["success"] = 'Formula renamed.';
			$response["msg"] = $value;
		}
	
	}
	echo json_encode($response);
	return;	
}

if($_GET['settings'] == 'cat'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$cat_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	mysqli_query($conn, "UPDATE ingCategory SET $name = '$value' WHERE id = '$cat_id'");
	return;
}

if($_GET['settings'] == 'prof'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	mysqli_query($conn, "UPDATE ingProfiles SET $name = '$value' WHERE id = '$id'");
	return;
}

if($_GET['settings'] == 'fcat'){
	$value = mysqli_real_escape_string($conn, $_POST['value']);
	$cat_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	mysqli_query($conn, "UPDATE formulaCategories SET $name = '$value' WHERE id = '$cat_id'");
	return;
}

if($_GET['settings'] == 'sup'){
	$value = htmlentities($_POST['value']);
	$sup_id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);

	mysqli_query($conn, "UPDATE ingSuppliers SET $name = '$value' WHERE id = '$sup_id'");
	return;	
}

if($_POST['supp'] == 'edit'){
	$id = $_POST['id'];

	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$address = mysqli_real_escape_string($conn, $_POST['address']);
	$po = mysqli_real_escape_string($conn, $_POST['po']);
	$country = mysqli_real_escape_string($conn, $_POST['country']);
	$telephone = mysqli_real_escape_string($conn, $_POST['telephone']);
	$url = mysqli_real_escape_string($conn, $_POST['url']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	
	
	if(mysqli_query($conn, "UPDATE ingSuppliers SET address = '$address', po='$po', country='$country', telephone='$telephone', url='$url', email='$email' WHERE id = '$id'")){
		$response["success"] = 'Supplier '.$name.' updated!';
		echo json_encode($response);
	}else{
		$response["error"] = 'Something went wrong: '.mysqli_error($conn);
		echo json_encode($response);
	}
	return;
}


if($_POST['supp'] == 'add'){
	if(!is_numeric($_POST['min_ml']) || !is_numeric($_POST['min_gr'])){
		$response["error"] = 'Only numeric values allowed in ml and grams fields!';
		echo json_encode($response);
		return;
	}
	$description = mysqli_real_escape_string($conn, $_POST['description']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$address = mysqli_real_escape_string($conn, $_POST['address']);
	$po = mysqli_real_escape_string($conn, $_POST['po']);
	$country = mysqli_real_escape_string($conn, $_POST['country']);
	$telephone = mysqli_real_escape_string($conn, $_POST['telephone']);
	$url = mysqli_real_escape_string($conn, $_POST['url']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$platform = mysqli_real_escape_string($conn, $_POST['platform']);
	$price_tag_start = htmlentities($_POST['price_tag_start']);
	$price_tag_end = htmlentities($_POST['price_tag_end']);
	$add_costs = is_numeric($_POST['add_costs']);
	$min_ml = mysqli_real_escape_string($conn, $_POST['min_ml']);
	$min_gr = mysqli_real_escape_string($conn, $_POST['min_gr']);

	if(empty($min_ml)){
		$min_ml = 0;
	}
	if(empty($min_gr)){
		$min_gr = 0;
	}		 
	
	if(empty($name)){
		$response["error"] = '<strong>Error: </strong> Supplier name required';
		echo json_encode($response);
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE name = '$name'"))){
		$response["error"] = '<strong>'.$name.'</strong> Supplier already exists!';
		echo json_encode($response);
		return;
	}

	if(mysqli_query($conn, "INSERT INTO ingSuppliers (name,address,po,country,telephone,url,email,platform,price_tag_start,price_tag_end,add_costs,notes,min_ml,min_gr) VALUES ('$name','$address','$po','$country','$telephone','$url','$email','$platform','$price_tag_start','$price_tag_end','$add_costs','$description','$min_ml','$min_gr')")){
		$response["success"] = 'Supplier '.$name.' added!';
		echo json_encode($response);
	}else{
		$response["error"] = 'Something went wrong: '.mysqli_error($conn);
		echo json_encode($response);
	}
	return;
}

if($_GET['supp'] == 'delete' && $_GET['ID']){
	$ID = mysqli_real_escape_string($conn, $_GET['ID']);
	$supplier = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingSuppliers WHERE id = '$ID'"));

	if(mysqli_query($conn, "DELETE FROM ingSuppliers WHERE id = '$ID'")){
		$response["success"] = 'Supplier <strong>'.$supplier['name'].'</strong> deleted!';
	}else{
		$response["error"] = 'Something went wrong: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//ADD composition
if($_POST['composition'] == 'add'){
	$allgName = mysqli_real_escape_string($conn, $_POST['allgName']);
	$allgCAS = mysqli_real_escape_string($conn, $_POST['allgCAS']);
	$allgEC = mysqli_real_escape_string($conn, $_POST['allgEC']);	
	$minPerc = rtrim(mysqli_real_escape_string($conn, $_POST['minPerc']),'%');
	$maxPerc = rtrim(mysqli_real_escape_string($conn, $_POST['maxPerc']),'%');
	$GHS = rtrim(mysqli_real_escape_string($conn, $_POST['GHS']));

	$ing = base64_decode($_POST['ing']);
	
	if($_POST['addToDeclare'] == 'true'){
		$declare = '1';
	}else{
		$declare = '0';
	}
	
	if(empty($allgName)){
		$response["error"] = '<strong>Error:</strong> Name is required!';
		echo json_encode($response);
		return;
	}
	
	if(empty($allgCAS)){
		$response["error"] = '<strong>Error:</strong> CAS number is required!';
		echo json_encode($response);
		return;
	}
	
	if(empty($minPerc)){
		$response["error"] = 'Minimum percentage is required';
		echo json_encode($response);
		return;
	}
	
	if(empty($maxPerc)){
		$response["error"] = 'Max percentage is required';
		echo json_encode($response);
		return;
	}
	
	if(!is_numeric($minPerc)){
		$response["error"] = 'Minimum percentage value needs to be numeric!';
		echo json_encode($response);
		return;
	}
	
	if(!is_numeric($maxPerc)){
		$response["error"] = 'Maximum percentage value needs to be numeric!';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredient_compounds WHERE name = '$allgName' AND ing = '$ing'"))){
		$response["error"] = '<strong>Error: </strong>'.$allgName.' already exists!';
		echo json_encode($response);
		return;
	}
	
	if(mysqli_query($conn, "INSERT INTO ingredient_compounds (name, cas, ec, min_percentage, max_percentage, GHS, toDeclare, ing) VALUES ('$allgName','$allgCAS','$allgEC','$minPerc','$maxPerc','$GHS','$declare','$ing')")){
		$response["success"] = '<strong>'.$allgName.'</strong> added to the composition';
		echo json_encode($response);
	}else{
		$response["error"] = mysqli_error($conn);
		echo json_encode($response);
	}
	
	if($_POST['addToIng'] == 'true'){
		if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$allgName'")))){
			mysqli_query($conn, "INSERT INTO ingredients (name,cas,einecs) VALUES ('$allgName','$allgCAS','$allgEC')");		
		}
	}

	return;
}

//UPDATE composition
if($_GET['composition'] == 'update'){
	$value = rtrim(mysqli_real_escape_string($conn, $_POST['value']),'%');
	$id = mysqli_real_escape_string($conn, $_POST['pk']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	//$ing = base64_decode($_GET['ing']);

//	mysqli_query($conn, "UPDATE ingredient_compounds SET $name = '$value' WHERE id = '$id' AND ing='$ing'");
	mysqli_query($conn, "UPDATE ingredient_compounds SET $name = '$value' WHERE id = '$id'");

	return;
}

//DELETE composition	
if($_POST['composition'] == 'delete'){

	$id = mysqli_real_escape_string($conn, $_POST['allgID']);
	//$ing = base64_decode($_POST['ing']);

	$delQ = mysqli_query($conn, "DELETE FROM ingredient_compounds WHERE id = '$id'");	
	if($delQ){
		$response["success"] = '<strong>'.$ing.'</strong> removed!';
		echo json_encode($response);
	}
	
	return;
}

//DELETE INGREDIENT	
if($_POST['ingredient'] == 'delete' && $_POST['ing_id']){

	$id = mysqli_real_escape_string($conn, $_POST['ing_id']);
	$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingredients WHERE id = '$id'"));
	
	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '".$ing['name']."'"))){
		$response["error"] = '<strong>'.$ing['name'].'</strong> is in use by at least one formula and cannot be removed!</div>';
	}elseif(mysqli_query($conn, "DELETE FROM ingredients WHERE id = '$id'")){
		mysqli_query($conn,"DELETE FROM ingredient_compounds WHERE ing = '".$ing['name']."'");
		$response["success"] = 'Ingredient <strong>'.$ing['name'].'</strong> removed from the database!';
	}
	
	echo json_encode($response);
	return;

}

//CUSTOMERS - ADD
if($_POST['customer'] == 'add'){
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	if(empty($name)){
		$response["error"] = 'Customer name is required.';
		echo json_encode($response);
		return;
	}
	$address = mysqli_real_escape_string($conn, $_POST['address']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$web = mysqli_real_escape_string($conn, $_POST['web']);
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM customers WHERE name = '$name'"))){
		$response["error"] = 'Error: '.$name.' already exists!';
	}elseif(mysqli_query($conn, "INSERT INTO customers (name,address,email,web,owner_id) VALUES ('$name', '$address', '$email', '$web', '".$user['id']."')")){
		$response["success"] = 'Customer '.$name.' added!';
	}else{
		$response["error"] = 'Error adding customer.';
	}
	echo json_encode($response);
	return;
}

//CUSTOMERS - DELETE
if($_POST['action'] == 'delete' && $_POST['type'] == 'customer' && $_POST['customer_id']){
	$customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
	if(mysqli_query($conn, "DELETE FROM customers WHERE id = '$customer_id'")){
		$response["success"] = 'Customer deleted!';
	}else{
		$response["error"] = 'Error deleting customer '.mysqli_error($conn);
	}
	
	echo json_encode($response);
	return;
}
	
//CUSTOMERS - UPDATE
if($_POST['update_customer_data'] && $_POST['customer_id']){
	$id = $_POST['customer_id'];
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	if(empty($name)){
		$response["error"] = 'Name cannot be empty ';
		echo json_encode($response);
		return;
	}
	$address = mysqli_real_escape_string($conn, $_POST['address'])?:'N/A';
	$email = mysqli_real_escape_string($conn, $_POST['email'])?:'N/A';
	$web = mysqli_real_escape_string($conn, $_POST['web'])?:'N/A';
	$phone = mysqli_real_escape_string($conn, $_POST['phone'])?:'N/A';

	if(mysqli_query($conn, "UPDATE customers SET name = '$name', address = '$address', email = '$email', web = '$web', phone = '$phone' WHERE id = '$id'")){
		$response["success"] = 'Customer details updated!';
	}else{
		$response["error"] = 'Error updating customer '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;	
}

//MGM INGREDIENT
if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'general'){
	$ing = mysqli_real_escape_string($conn, $_POST['ing']);

	$INCI = trim(mysqli_real_escape_string($conn, $_POST["INCI"]));
	$cas = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["cas"])));
	$einecs = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["einecs"])));
	$reach = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["reach"])));
	$fema = preg_replace('/\s+/', '', trim(mysqli_real_escape_string($conn, $_POST["fema"])));
	$purity = validateInput($_POST["purity"]);
	$profile = mysqli_real_escape_string($conn, $_POST["profile"]);
	$type = mysqli_real_escape_string($conn, $_POST["type"]);	
	$strength = mysqli_real_escape_string($conn, $_POST["strength"]);
	$category = mysqli_real_escape_string($conn, $_POST["category"] ? $_POST['category']: '1');
	$physical_state = mysqli_real_escape_string($conn, $_POST["physical_state"]);
	$odor = ucfirst(trim(mysqli_real_escape_string($conn, $_POST["odor"])));
	$notes = ucfirst(trim(mysqli_real_escape_string($conn, $_POST["notes"])));
	
//	if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '".$_POST['name']."'"))){
	if(empty($_POST['name'])){
	
		$query = "UPDATE ingredients SET INCI = '$INCI',cas = '$cas',solvent='".$_POST["solvent"]."', einecs = '$einecs', reach = '$reach',FEMA = '$fema',purity='$purity',profile='$profile',type = '$type',strength = '$strength', category='$category',physical_state = '$physical_state',odor = '$odor',notes = '$notes' WHERE name='$ing'";
		
		if(mysqli_query($conn, $query)){
			$response["success"] = 'General details have been updated!';
		}else{
			$response["error"] = 'Unable to update database: '.mysqli_error($conn);
		}
	}else{
		$name = sanChar(mysqli_real_escape_string($conn, $_POST["name"]));

		$query = "INSERT INTO ingredients (name, INCI, cas, einecs, reach, FEMA, type, strength, category, profile, notes, odor, purity, solvent, physical_state) VALUES ('$name', '$INCI', '$cas', '$einecs', '$reach', '$fema', '$type', '$strength', '$category', '$profile',  '$notes', '$odor', '$purity', '$solvent', '1')";
		
		if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$name'"))){
			$response["error"] = $name.' already exists!';
		}else{
			if(mysqli_query($conn, $query)){
				$response["success"] = 'Ingredient '.$name.' created';
				$response["ingid"] = mysqli_insert_id($conn);
			}else{
				$response["error"] = 'Failed to create ingredient';
			}
		}
	}
	echo json_encode($response);
	return;	
}


if ($_POST['manage'] === 'ingredient' && $_POST['tab'] === 'usage_limits') {
    $ingID = (int) $_POST['ingID'];

    $flavor_use = ($_POST['flavor_use'] === 'true') ? 1 : 0;
    $noUsageLimit = ($_POST['noUsageLimit'] === 'true') ? 1 : 0;
    $byPassIFRA = ($_POST['byPassIFRA'] === 'true') ? 1 : 0;
    $allergen = ($_POST['isAllergen'] === 'true') ? 1 : 0;

    $usage_type = mysqli_real_escape_string($conn, trim($_POST['usage_type']));

    $categories = [
        'cat1' => (float) $_POST['cat1'],
        'cat2' => (float) $_POST['cat2'],
        'cat3' => (float) $_POST['cat3'],
        'cat4' => (float) $_POST['cat4'],
        'cat5A' => (float) $_POST['cat5A'],
        'cat5B' => (float) $_POST['cat5B'],
        'cat5C' => (float) $_POST['cat5C'],
        'cat5D' => (float) $_POST['cat5D'],
        'cat6' => (float) $_POST['cat6'],
        'cat7A' => (float) $_POST['cat7A'],
        'cat7B' => (float) $_POST['cat7B'],
        'cat8' => (float) $_POST['cat8'],
        'cat9' => (float) $_POST['cat9'],
        'cat10A' => (float) $_POST['cat10A'],
        'cat10B' => (float) $_POST['cat10B'],
        'cat11A' => (float) $_POST['cat11A'],
        'cat11B' => (float) $_POST['cat11B'],
        'cat12' => (float) $_POST['cat12'],
    ];

    $stmt = $conn->prepare(
        "UPDATE ingredients SET byPassIFRA = ?, noUsageLimit = ?, flavor_use = ?, 
        usage_type = ?, allergen = ?, cat1 = ?, cat2 = ?, cat3 = ?, cat4 = ?, 
        cat5A = ?, cat5B = ?, cat5C = ?, cat5D = ?, cat6 = ?, cat7A = ?, cat7B = ?, 
        cat8 = ?, cat9 = ?, cat10A = ?, cat10B = ?, cat11A = ?, cat11B = ?, 
        cat12 = ? WHERE id = ?"
    );

    $stmt->bind_param(
        'iiisiddddddddddddddddddi',
        $byPassIFRA, $noUsageLimit, $flavor_use, $usage_type, $allergen, 
        $categories['cat1'], $categories['cat2'], $categories['cat3'], 
        $categories['cat4'], $categories['cat5A'], $categories['cat5B'], 
        $categories['cat5C'], $categories['cat5D'], $categories['cat6'], 
        $categories['cat7A'], $categories['cat7B'], $categories['cat8'], 
        $categories['cat9'], $categories['cat10A'], $categories['cat10B'], 
        $categories['cat11A'], $categories['cat11B'], $categories['cat12'], $ingID
    );

    if ($stmt->execute()) {
        $response["success"] = 'Usage limits have been updated!';
    } else {
        $response["error"] = 'Something went wrong: ' . $stmt->error;
    }

    $stmt->close();

    echo json_encode($response);
    return;
}


if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'tech_data'){
	$ingID = (int)$_POST['ingID'];
	$tenacity = mysqli_real_escape_string($conn, $_POST["tenacity"]);
	$flash_point = mysqli_real_escape_string($conn, $_POST["flash_point"]);
	$chemical_name = mysqli_real_escape_string($conn, $_POST["chemical_name"]);
	$formula = mysqli_real_escape_string($conn, $_POST["formula"]);
	$logp = mysqli_real_escape_string($conn, $_POST["logp"]);
	$soluble = mysqli_real_escape_string($conn, $_POST["soluble"]);
	$molecularWeight = mysqli_real_escape_string($conn, $_POST["molecularWeight"]);
	$appearance = mysqli_real_escape_string($conn, $_POST["appearance"]);
	$rdi = (int)$_POST["rdi"]?:0;

	
	$query = "UPDATE ingredients SET tenacity='$tenacity',flash_point='$flash_point',chemical_name='$chemical_name',formula='$formula',logp = '$logp',soluble = '$soluble',molecularWeight = '$molecularWeight',appearance='$appearance',rdi='$rdi' WHERE id='$ingID'";
	if(mysqli_query($conn, $query)){
		$response["success"] = 'Technical data has been updated!';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}	
	echo json_encode($response);
	return;
}

if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'note_impact'){
	$ingID = (int)$_POST['ingID'];
	$impact_top = mysqli_real_escape_string($conn, $_POST["impact_top"]);
	$impact_base = mysqli_real_escape_string($conn, $_POST["impact_base"]);
	$impact_heart = mysqli_real_escape_string($conn, $_POST["impact_heart"]);

	$query = "UPDATE ingredients SET impact_top = '$impact_top',impact_heart = '$impact_heart',impact_base = '$impact_base' WHERE id='$ingID'";
	if(mysqli_query($conn, $query)){
		$response["success"] = 'Note impact has been updated';
	}else{
		$response["error"] = 'Error: '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'privacy'){
	$ingID = (int)$_POST['ingID'];
	if($_POST['isPrivate'] == 'true'){ $isPrivate = '1'; }else{ $isPrivate = '0'; }
	
	$query = "UPDATE ingredients SET isPrivate = '$isPrivate' WHERE id='$ingID'";
	if(mysqli_query($conn, $query)){
		$response["success"] = 'Privacy settings has been updated!';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}

//ADD PICTOGRAM
if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'safety_info' && $_POST['action'] == 'add'){
	$ingID = (int)$_POST['ingID'];
	$GHS = (int)$_POST['pictogram'];

	if(mysqli_query($conn, "INSERT INTO ingSafetyInfo (GHS, ingID) VALUES ('$GHS','$ingID') ON DUPLICATE KEY UPDATE GHS = VALUES(GHS), ingID = VALUES(ingID)")){
		$response["success"] = 'Safety data has been updated!';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


//REMOVE PICTOGRAM
if($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'safety_info' && $_POST['pictogram_id'] && $_POST['action'] == 'remove'){
	$ingID = (int)$_POST['ingID'];
	$GHS = (int)$_POST['pictogram_id'];
	
	if(mysqli_query($conn, "DELETE FROM ingSafetyInfo WHERE GHS = '$GHS' AND ingID = '$ingID'")){
		$response["success"] = 'Safety data has been updated!';
	}else{
		$response["error"] = 'Something went wrong '.mysqli_error($conn);
	}
	echo json_encode($response);
	return;
}


if($_GET['import'] == 'ingredient'){
		$name = sanChar(mysqli_real_escape_string($conn, base64_decode($_GET["name"])));
		$query = "INSERT INTO ingredients (name, INCI, cas, notes, odor) VALUES ('$name', '$INCI', '$cas', 'Auto Imported', '$odor')";
		
		if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$name'"))){
			echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>'.$name.' already exists!</div>';
		}else{
			if(mysqli_query($conn, $query)){
				echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>Ingredient <strong>'.$name.'</strong> added!</div>';
			}else{
				echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Failed to add '.mysqli_error($conn).'</div>';
			}
		}	
	return;
}

//CLONE INGREDIENT
if($_POST['action'] == 'clone' && $_POST['old_ing_name'] && $_POST['ing_id']){
	$ing_id = mysqli_real_escape_string($conn, $_POST['ing_id']);

	$old_ing_name = mysqli_real_escape_string($conn, $_POST['old_ing_name']);
	$new_ing_name = mysqli_real_escape_string($conn, $_POST['new_ing_name']);
	if(empty($new_ing_name)){
		$response['error'] = 'Please provide a name';
		echo json_encode($response);
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$new_ing_name'"))){
		$response['error'] = $new_ing_name.' already exists';
		echo json_encode($response);
		return;
	}
	
	$sql.=mysqli_query($conn, "INSERT INTO ingredient_compounds (ing,name,cas,min_percentage,max_percentage) SELECT '$new_ing_name',name,cas,min_percentage,max_percentage FROM ingredient_compounds WHERE ing = '$old_ing_name'");

	$sql.=mysqli_query($conn, "INSERT INTO ingredients (name,INCI,type,strength,category,purity,cas,FEMA,reach,tenacity,chemical_name,formula,flash_point,appearance,notes,profile,solvent,odor,allergen,flavor_use,soluble,logp,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12,impact_top,impact_heart,impact_base,created,usage_type,noUsageLimit,isPrivate,molecularWeight,physical_state) SELECT '$new_ing_name',INCI,type,strength,category,purity,cas,FEMA,reach,tenacity,chemical_name,formula,flash_point,appearance,notes,profile,solvent,odor,allergen,flavor_use,soluble,logp,cat1,cat2,cat3,cat4,cat5A,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12,impact_top,impact_heart,impact_base,created,usage_type,noUsageLimit,isPrivate,molecularWeight,physical_state FROM ingredients WHERE id = '$ing_id'");

	if($nID = mysqli_fetch_array(mysqli_query($conn, "SELECT id,name FROM ingredients WHERE name = '$new_ing_name'"))){
		
		$response['success'] = $old_ing_name.' duplicated as <a href="/pages/mgmIngredient.php?id='.$nID['id'].'" >'.$new_ing_name.'</a>';
		echo json_encode($response);
		return;
	}
	
	return;
}



//RENAME INGREDIENT
if($_POST['action'] == 'rename' && $_POST['old_ing_name'] && $_POST['ing_id']){
	$ing_id = mysqli_real_escape_string($conn, $_POST['ing_id']);

	$old_ing_name = mysqli_real_escape_string($conn, $_POST['old_ing_name']);
	$new_ing_name = mysqli_real_escape_string($conn, $_POST['new_ing_name']);
	if(empty($new_ing_name)){
		$response['error'] = 'Please provide a name';
		echo json_encode($response);
		return;
	}
	if(mysqli_num_rows(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '$new_ing_name'"))){
		$response['error'] = $new_ing_name.' already exists';
		echo json_encode($response);
		return;
	}
	
	$sql.=mysqli_query($conn, "UPDATE ingredient_compounds SET ing = '$new_ing_name' WHERE ing = '$old_ing_name'");

	$sql.=mysqli_query($conn, "UPDATE ingredients SET name = '$new_ing_name' WHERE name = '$old_ing_name' AND id = '$ing_id'");
	$sql.=mysqli_query($conn, "UPDATE formulas SET ingredient = '$new_ing_name' WHERE ingredient = '$old_ing_name' AND ingredient_id = '$ing_id'");

	if($nID = mysqli_fetch_array(mysqli_query($conn, "SELECT id,name FROM ingredients WHERE name = '$new_ing_name'"))){
		
		$response['success']['msg'] = $old_ing_name.' renamed to <a href="/pages/mgmIngredient.php?id='.$nID['id'].'" >'.$new_ing_name.'</a>';
		$response['success']['id'] = $nID['id'];
		echo json_encode($response);
		return;
	}
	
	return;
}


//FIRST AID INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'faid_info') {
    $ingID = (int)$_POST['ingID'];
    
    $first_aid_general = $_POST['first_aid_general'];
    $first_aid_inhalation = $_POST['first_aid_inhalation'];
    $first_aid_skin = $_POST['first_aid_skin'];
    $first_aid_eye = $_POST['first_aid_eye'];
    $first_aid_ingestion = $_POST['first_aid_ingestion'];
    $first_aid_self_protection = $_POST['first_aid_self_protection'];
    $first_aid_symptoms = $_POST['first_aid_symptoms'];
    $first_aid_dr_notes = $_POST['first_aid_dr_notes'];
    
	 // Check if all fields are populated
    if (
        empty($ingID) || empty($first_aid_general) || empty($first_aid_inhalation) ||
        empty($first_aid_skin) || empty($first_aid_eye) || empty($first_aid_ingestion) ||
        empty($first_aid_self_protection) || empty($first_aid_symptoms) || empty($first_aid_dr_notes)
    ) {
        $response["error"] = 'All fields are required.';
        echo json_encode($response);
        return;
    }
	
    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, first_aid_general, first_aid_inhalation, first_aid_skin, first_aid_eye, 
            first_aid_ingestion, first_aid_self_protection, first_aid_symptoms, first_aid_dr_notes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            first_aid_general = VALUES(first_aid_general),
            first_aid_inhalation = VALUES(first_aid_inhalation),
            first_aid_skin = VALUES(first_aid_skin),
            first_aid_eye = VALUES(first_aid_eye),
            first_aid_ingestion = VALUES(first_aid_ingestion),
            first_aid_self_protection = VALUES(first_aid_self_protection),
            first_aid_symptoms = VALUES(first_aid_symptoms),
            first_aid_dr_notes = VALUES(first_aid_dr_notes)"
    );
    
    // Bind the parameters
    $stmt->bind_param(
        'issssssss', $ingID, $first_aid_general, $first_aid_inhalation, $first_aid_skin, $first_aid_eye, 
        $first_aid_ingestion, $first_aid_self_protection, $first_aid_symptoms, $first_aid_dr_notes
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'First aid data has been updated!';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }
    
    // Close the statement
    $stmt->close();
    
    echo json_encode($response);
    return;
}

//FIREFIGHTING
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'fire_info') {
    $ingID = (int)$_POST['ingID'];
    
    $firefighting_suitable_media = $_POST['firefighting_suitable_media'];
    $firefighting_non_suitable_media = $_POST['firefighting_non_suitable_media'];
    $firefighting_special_hazards = $_POST['firefighting_special_hazards'];
    $firefighting_advice = $_POST['firefighting_advice'];
    $firefighting_other_info = $_POST['firefighting_other_info'];
    
    // Check if all fields are populated
    if (
        empty($ingID) || empty($firefighting_suitable_media) || empty($firefighting_non_suitable_media) ||
        empty($firefighting_special_hazards) || empty($firefighting_advice) || empty($firefighting_other_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }
    
    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, firefighting_suitable_media, firefighting_non_suitable_media, 
            firefighting_special_hazards, firefighting_advice, firefighting_other_info
        ) VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            firefighting_suitable_media = VALUES(firefighting_suitable_media),
            firefighting_non_suitable_media = VALUES(firefighting_non_suitable_media),
            firefighting_special_hazards = VALUES(firefighting_special_hazards),
            firefighting_advice = VALUES(firefighting_advice),
            firefighting_other_info = VALUES(firefighting_other_info)"
    );
    
    // Bind the parameters
    $stmt->bind_param(
        'isssss', $ingID, $firefighting_suitable_media, $firefighting_non_suitable_media, 
        $firefighting_special_hazards, $firefighting_advice, $firefighting_other_info
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Firefighting data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }
    
    // Close the statement
    $stmt->close();
    
    echo json_encode($response);
    return;
}


//ACCIDENTAL RELEASE
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'save_acc_rel') {
    $ingID = (int)$_POST['ingID'];
    
    $accidental_release_per_precautions = $_POST['accidental_release_per_precautions'];
    $accidental_release_env_precautions = $_POST['accidental_release_env_precautions'];
    $accidental_release_cleaning = $_POST['accidental_release_cleaning'];
	$accidental_release_refs =  $_POST['accidental_release_refs'];
    $accidental_release_other_info = $_POST['accidental_release_other_info'];
    
    // Check if all fields are populated
    if (
        empty($ingID) || empty($accidental_release_per_precautions) || empty($accidental_release_env_precautions) ||
        empty($accidental_release_cleaning) ||  empty($accidental_release_refs) || empty($accidental_release_other_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }
    
    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, accidental_release_per_precautions, accidental_release_env_precautions, 
            accidental_release_cleaning, accidental_release_refs, accidental_release_other_info
        ) VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            accidental_release_per_precautions = VALUES(accidental_release_per_precautions),
            accidental_release_env_precautions = VALUES(accidental_release_env_precautions),
            accidental_release_cleaning = VALUES(accidental_release_cleaning),
			accidental_release_refs = VALUES(accidental_release_refs),
            accidental_release_other_info = VALUES(accidental_release_other_info)"
    );
    
    // Bind the parameters
    $stmt->bind_param(
        'isssss', $ingID, $accidental_release_per_precautions, $accidental_release_env_precautions, 
        $accidental_release_cleaning, $accidental_release_refs, $accidental_release_other_info
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Accidental release data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }
    
    // Close the statement
    $stmt->close();
    
    echo json_encode($response);
    return;
}

//HANDLING & STORAGE
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'HS') {
    $ingID = (int)$_POST['ingID'];
    
    $handling_protection = $_POST['handling_protection'];
    $handling_hygiene = $_POST['handling_hygiene'];
    $handling_safe_storage = $_POST['handling_safe_storage'];
	$handling_joint_storage =  $_POST['handling_joint_storage'];
    $handling_specific_uses = $_POST['handling_specific_uses'];
    
    // Check if all fields are populated
    if (
        empty($ingID) || empty($handling_protection) || empty($handling_hygiene) ||
        empty($handling_safe_storage) ||  empty($handling_joint_storage) || empty($handling_specific_uses)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }
    
    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, handling_protection, handling_hygiene, 
            handling_safe_storage, handling_joint_storage, handling_specific_uses
        ) VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            handling_protection = VALUES(handling_protection),
            handling_hygiene = VALUES(handling_hygiene),
            handling_safe_storage = VALUES(handling_safe_storage),
			handling_joint_storage = VALUES(handling_joint_storage),
            handling_specific_uses = VALUES(handling_specific_uses)"
    );
    
    // Bind the parameters
    $stmt->bind_param(
        'isssss', $ingID, $handling_protection, $handling_hygiene, 
        $handling_safe_storage, $handling_joint_storage, $handling_specific_uses
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Handling and storage data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }
    
    // Close the statement
    $stmt->close();
    
    echo json_encode($response);
    return;
}

//EXPOSURE DATA
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'exposure_data') {
    $ingID = (int)$_POST['ingID'];

    // New fields for exposure data
    $exposure_occupational_limits = $_POST['exposure_occupational_limits'];
    $exposure_biological_limits = $_POST['exposure_biological_limits'];
    $exposure_intented_use_limits = $_POST['exposure_intented_use_limits'];
    $exposure_other_remarks = $_POST['exposure_other_remarks'];
    $exposure_face_protection = $_POST['exposure_face_protection'];
    $exposure_skin_protection = $_POST['exposure_skin_protection'];
    $exposure_respiratory_protection = $_POST['exposure_respiratory_protection'];
    $exposure_env_exposure = $_POST['exposure_env_exposure'];
    $exposure_consumer_exposure = $_POST['exposure_consumer_exposure'];
    $exposure_other_info = $_POST['exposure_other_info'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($exposure_occupational_limits) || empty($exposure_biological_limits) || 
        empty($exposure_intented_use_limits) || empty($exposure_other_remarks) || empty($exposure_face_protection) || 
        empty($exposure_skin_protection) || empty($exposure_respiratory_protection) || empty($exposure_env_exposure) || 
        empty($exposure_consumer_exposure) || empty($exposure_other_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, exposure_occupational_limits, exposure_biological_limits, 
            exposure_intented_use_limits, exposure_other_remarks, 
            exposure_face_protection, exposure_skin_protection, 
            exposure_respiratory_protection, exposure_env_exposure, 
            exposure_consumer_exposure, exposure_other_info
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            exposure_occupational_limits = VALUES(exposure_occupational_limits),
            exposure_biological_limits = VALUES(exposure_biological_limits),
            exposure_intented_use_limits = VALUES(exposure_intented_use_limits),
            exposure_other_remarks = VALUES(exposure_other_remarks),
            exposure_face_protection = VALUES(exposure_face_protection),
            exposure_skin_protection = VALUES(exposure_skin_protection),
            exposure_respiratory_protection = VALUES(exposure_respiratory_protection),
            exposure_env_exposure = VALUES(exposure_env_exposure),
            exposure_consumer_exposure = VALUES(exposure_consumer_exposure),
            exposure_other_info = VALUES(exposure_other_info)"
    );

    // Bind the parameters
    $stmt->bind_param(
        'issssssssss', $ingID, $exposure_occupational_limits, $exposure_biological_limits, 
        $exposure_intented_use_limits, $exposure_other_remarks, 
        $exposure_face_protection, $exposure_skin_protection, 
        $exposure_respiratory_protection, $exposure_env_exposure, 
        $exposure_consumer_exposure, $exposure_other_info
    );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Exposure data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//PCP
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'pcp') {
    $ingID = (int)$_POST['ingID'];

    $odor_threshold = $_POST['odor_threshold'];
    $pH = $_POST['pH'];
    $melting_point = $_POST['melting_point'];
    $boiling_point = $_POST['boiling_point'];
    $flash_point = $_POST['flash_point'];
    $evaporation_rate = $_POST['evaporation_rate'];
    $solubility = $_POST['solubility'];
    $auto_infl_temp = $_POST['auto_infl_temp'];
    $decomp_temp = $_POST['decomp_temp'];
    $viscosity = $_POST['viscosity'];
    $explosive_properties = $_POST['explosive_properties'];
    $oxidising_properties = $_POST['oxidising_properties'];
    $particle_chars = $_POST['particle_chars'];
    $flammability = $_POST['flammability'];
    $logP = $_POST['logP'];
    $soluble = $_POST['soluble'];
    $color = $_POST['color'];
    $low_flammability_limit = $_POST['low_flammability_limit'];
    $vapour_pressure = $_POST['vapour_pressure'];
    $vapour_density = $_POST['vapour_density'];
    $relative_density = $_POST['relative_density'];
    $pcp_other_info = $_POST['pcp_other_info'];
    $pcp_other_sec_info = $_POST['pcp_other_sec_info'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($odor_threshold) || empty($pH) || 
        empty($melting_point) || empty($boiling_point) || empty($flash_point) || 
        empty($evaporation_rate) || empty($solubility) || empty($auto_infl_temp) || 
        empty($decomp_temp) || empty($viscosity) || empty($explosive_properties) || 
        empty($oxidising_properties) || empty($particle_chars) || empty($flammability) || 
        empty($logP) || empty($soluble) || empty($color) || empty($low_flammability_limit) || 
        empty($vapour_pressure) || empty($vapour_density) || empty($relative_density) || 
        empty($pcp_other_info) || empty($pcp_other_sec_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, odor_threshold, pH, melting_point, boiling_point, flash_point, 
            evaporation_rate, solubility, auto_infl_temp, decomp_temp, viscosity, 
            explosive_properties, oxidising_properties, particle_chars, flammability, 
            logP, soluble, color, low_flammability_limit, vapour_pressure, vapour_density, 
            relative_density, pcp_other_info, pcp_other_sec_info
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            odor_threshold = VALUES(odor_threshold),
            pH = VALUES(pH),
            melting_point = VALUES(melting_point),
            boiling_point = VALUES(boiling_point),
            flash_point = VALUES(flash_point),
            evaporation_rate = VALUES(evaporation_rate),
            solubility = VALUES(solubility),
            auto_infl_temp = VALUES(auto_infl_temp),
            decomp_temp = VALUES(decomp_temp),
            viscosity = VALUES(viscosity),
            explosive_properties = VALUES(explosive_properties),
            oxidising_properties = VALUES(oxidising_properties),
            particle_chars = VALUES(particle_chars),
            flammability = VALUES(flammability),
            logP = VALUES(logP),
            soluble = VALUES(soluble),
            color = VALUES(color),
            low_flammability_limit = VALUES(low_flammability_limit),
            vapour_pressure = VALUES(vapour_pressure),
            vapour_density = VALUES(vapour_density),
            relative_density = VALUES(relative_density),
            pcp_other_info = VALUES(pcp_other_info),
            pcp_other_sec_info = VALUES(pcp_other_sec_info)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param(
        'isssssssssssssssssssssss', $ingID, $odor_threshold, $pH, $melting_point, $boiling_point, 
        $flash_point, $evaporation_rate, $solubility, $auto_infl_temp, $decomp_temp, 
        $viscosity, $explosive_properties, $oxidising_properties, $particle_chars, 
        $flammability, $logP, $soluble, $color, $low_flammability_limit, $vapour_pressure, 
        $vapour_density, $relative_density, $pcp_other_info, $pcp_other_sec_info
    );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Ingredient safety data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}

//SR INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'sr_info') {
    $ingID = (int)$_POST['ingID'];


    $stabillity_reactivity = $_POST['stabillity_reactivity'];
    $stabillity_chemical = $_POST['stabillity_chemical'];
    $stabillity_reactions = $_POST['stabillity_reactions'];
    $stabillity_avoid = $_POST['stabillity_avoid'];
    $stabillity_incompatibility = $_POST['stabillity_incompatibility'];
   

    // Check if all fields are populated
    if (
        empty($ingID) || empty($stabillity_reactivity) || empty($stabillity_chemical) || 
        empty($stabillity_reactions) || empty($stabillity_avoid) || empty($stabillity_incompatibility)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, stabillity_reactivity, stabillity_chemical, stabillity_reactions, stabillity_avoid, stabillity_incompatibility
        ) VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            stabillity_reactivity = VALUES(stabillity_reactivity),
            stabillity_chemical = VALUES(stabillity_chemical),
            stabillity_reactions = VALUES(stabillity_reactions),
            stabillity_avoid = VALUES(stabillity_avoid),
            stabillity_incompatibility = VALUES(stabillity_incompatibility)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param( 'isssss', $ingID, $stabillity_reactivity, $stabillity_chemical, $stabillity_reactions, $stabillity_avoid, $stabillity_incompatibility );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Stability and reactivity data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//TOXICOLOGICAL INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'tx_info') {
    $ingID = (int)$_POST['ingID'];

    $toxicological_acute_oral = $_POST['toxicological_acute_oral'];
    $toxicological_acute_dermal = $_POST['toxicological_acute_dermal'];
    $toxicological_acute_inhalation = $_POST['toxicological_acute_inhalation'];
    $toxicological_skin = $_POST['toxicological_skin'];
    $toxicological_eye = $_POST['toxicological_eye'];
    $toxicological_sensitisation = $_POST['toxicological_sensitisation'];
    $toxicological_organ_repeated = $_POST['toxicological_organ_repeated'];
    $toxicological_organ_single = $_POST['toxicological_organ_single'];
    $toxicological_carcinogencity = $_POST['toxicological_carcinogencity'];
    $toxicological_reproductive = $_POST['toxicological_reproductive'];
    $toxicological_cell_mutation = $_POST['toxicological_cell_mutation'];
    $toxicological_resp_tract = $_POST['toxicological_resp_tract'];
    $toxicological_other_info = $_POST['toxicological_other_info'];
    $toxicological_other_hazards = $_POST['toxicological_other_hazards'];


    // Check if all fields are populated
    if (
        empty($ingID) || empty($toxicological_acute_oral) || empty($toxicological_acute_dermal) || 
        empty($toxicological_acute_inhalation) || empty($toxicological_skin) || empty($toxicological_eye) || 
        empty($toxicological_sensitisation) || empty($toxicological_organ_repeated) || empty($toxicological_organ_single) || 
        empty($toxicological_carcinogencity) || empty($toxicological_reproductive) || empty($toxicological_cell_mutation) || 
        empty($toxicological_resp_tract) || empty($toxicological_other_info) || empty($toxicological_other_hazards)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, toxicological_acute_oral, toxicological_acute_dermal, toxicological_acute_inhalation, toxicological_skin, toxicological_eye, toxicological_sensitisation, toxicological_organ_repeated, toxicological_organ_single, toxicological_carcinogencity, toxicological_reproductive, toxicological_cell_mutation, toxicological_resp_tract, toxicological_other_info, toxicological_other_hazards
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            toxicological_acute_oral = VALUES(toxicological_acute_oral),
            toxicological_acute_dermal = VALUES(toxicological_acute_dermal),
            toxicological_acute_inhalation = VALUES(toxicological_acute_inhalation),
            toxicological_skin = VALUES(toxicological_skin),
            toxicological_eye = VALUES(toxicological_eye),
            toxicological_sensitisation = VALUES(toxicological_sensitisation),
            toxicological_organ_repeated = VALUES(toxicological_organ_repeated),
            toxicological_organ_single = VALUES(toxicological_organ_single),
            toxicological_carcinogencity = VALUES(toxicological_carcinogencity),
            toxicological_reproductive = VALUES(toxicological_reproductive),
            toxicological_cell_mutation = VALUES(toxicological_cell_mutation),
            toxicological_resp_tract = VALUES(toxicological_resp_tract),
            toxicological_other_info = VALUES(toxicological_other_info),
            toxicological_other_hazards = VALUES(toxicological_other_hazards)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param(
        'issssssssssssss', $ingID, $toxicological_acute_oral, $toxicological_acute_dermal, $toxicological_acute_inhalation, $toxicological_skin, $toxicological_eye, $toxicological_sensitisation, $toxicological_organ_repeated, $toxicological_organ_single, $toxicological_carcinogencity, $toxicological_reproductive, $toxicological_cell_mutation, $toxicological_resp_tract, $toxicological_other_info, 
        $toxicological_other_hazards
    );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Toxicology data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//ECOLOGICAL INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'ec_info') {
    $ingID = (int)$_POST['ingID'];

    $ecological_toxicity = $_POST['ecological_toxicity'];
    $ecological_persistence = $_POST['ecological_persistence'];
    $ecological_bioaccumulative = $_POST['ecological_bioaccumulative'];
    $ecological_soil_mobility = $_POST['ecological_soil_mobility'];
    $ecological_PBT_vPvB = $_POST['ecological_PBT_vPvB'];
    $ecological_endocrine_properties = $_POST['ecological_endocrine_properties'];
    $ecological_other_adv_effects = $_POST['ecological_other_adv_effects'];
    $ecological_additional_ecotoxicological_info = $_POST['ecological_additional_ecotoxicological_info'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($ecological_toxicity) || empty($ecological_persistence) || empty($ecological_bioaccumulative) || empty($ecological_soil_mobility) || empty($ecological_PBT_vPvB) || 
        empty($ecological_endocrine_properties) || empty($ecological_other_adv_effects) || empty($ecological_additional_ecotoxicological_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, ecological_toxicity, ecological_persistence, ecological_bioaccumulative, ecological_soil_mobility, ecological_PBT_vPvB, ecological_endocrine_properties, ecological_other_adv_effects, ecological_additional_ecotoxicological_info
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            ecological_toxicity = VALUES(ecological_toxicity),
            ecological_persistence = VALUES(ecological_persistence),
            ecological_bioaccumulative = VALUES(ecological_bioaccumulative),
            ecological_soil_mobility = VALUES(ecological_soil_mobility),
            ecological_PBT_vPvB = VALUES(ecological_PBT_vPvB),
            ecological_endocrine_properties = VALUES(ecological_endocrine_properties),
            ecological_other_adv_effects = VALUES(ecological_other_adv_effects),
            ecological_additional_ecotoxicological_info = VALUES(ecological_additional_ecotoxicological_info)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param(
        'issssssss', $ingID, $ecological_toxicity, $ecological_persistence, $ecological_bioaccumulative, $ecological_soil_mobility, $ecological_PBT_vPvB, $ecological_endocrine_properties, $ecological_other_adv_effects, $ecological_additional_ecotoxicological_info
    );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Ecology data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}


//DISPOSE INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'dis_info') {
    $ingID = (int)$_POST['ingID'];


    $disposal_product = $_POST['disposal_product'];
    $disposal_remarks = $_POST['disposal_remarks'];


    // Check if all fields are populated
    if ( empty($ingID) || empty($disposal_product) || empty($disposal_remarks) ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data ( ingID, disposal_product, disposal_remarks ) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            disposal_product = VALUES(disposal_product),
            disposal_remarks = VALUES(disposal_remarks)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param( 'iss', $ingID, $disposal_product, $disposal_remarks );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Dispose data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}




//TRANSPORTATION INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'trans_info') {
    $ingID = (int)$_POST['ingID'];

    $transport_un_number = $_POST['transport_un_number'];
    $transport_shipping_name = $_POST['transport_shipping_name'];
    $transport_hazard_class = $_POST['transport_hazard_class'];
    $transport_packing_group = $_POST['transport_packing_group'];
    $transport_env_hazards = $_POST['transport_env_hazards'];
    $transport_precautions = $_POST['transport_precautions'];
    $transport_bulk_shipping = $_POST['transport_bulk_shipping'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($transport_un_number) || empty($transport_shipping_name) || empty($transport_hazard_class) || empty($transport_packing_group) || empty($transport_env_hazards) || 
        empty($transport_precautions) || empty($transport_bulk_shipping)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, transport_un_number, transport_shipping_name, transport_hazard_class, transport_packing_group, transport_env_hazards, transport_precautions, transport_bulk_shipping
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            transport_un_number = VALUES(transport_un_number),
            transport_shipping_name = VALUES(transport_shipping_name),
            transport_hazard_class = VALUES(transport_hazard_class),
            transport_packing_group = VALUES(transport_packing_group),
            transport_env_hazards = VALUES(transport_env_hazards),
            transport_precautions = VALUES(transport_precautions),
            transport_bulk_shipping = VALUES(transport_bulk_shipping)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param(
        'isssssss', $ingID, $transport_un_number, $transport_shipping_name, $transport_hazard_class, $transport_packing_group, $transport_env_hazards, $transport_precautions, $transport_bulk_shipping
    );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Transportation data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}

//LEGISLATION INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'leg_info') {
    $ingID = (int)$_POST['ingID'];

    $legislation_safety = $_POST['legislation_safety'];
    $legislation_eu = $_POST['legislation_eu'];
    $legislation_chemical_safety_assessment = $_POST['legislation_chemical_safety_assessment'];
    $legislation_other_info = $_POST['legislation_other_info'];


    // Check if all fields are populated
    if (
        empty($ingID) || empty($legislation_safety) || empty($legislation_eu) || empty($legislation_chemical_safety_assessment) || empty($legislation_other_info)
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, legislation_safety, legislation_eu, legislation_chemical_safety_assessment, legislation_other_info
        ) VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            legislation_safety = VALUES(legislation_safety),
            legislation_eu = VALUES(legislation_eu),
            legislation_chemical_safety_assessment = VALUES(legislation_chemical_safety_assessment),
            legislation_other_info = VALUES(legislation_other_info)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param( 'issss', $ingID, $legislation_safety, $legislation_eu, $legislation_chemical_safety_assessment, $legislation_other_info );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Legislation data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}



//ADDITIONAL INFO
if ($_POST['manage'] == 'ingredient' && $_POST['tab'] == 'add_info') {
    $ingID = (int)$_POST['ingID'];

    $add_info_changes = $_POST['add_info_changes'];
    $add_info_acronyms = $_POST['add_info_acronyms'];
    $add_info_references = $_POST['add_info_references'];
    $add_info_HazCom = $_POST['add_info_HazCom'];
	$add_info_GHS = $_POST['add_info_GHS'];
	$add_info_training = $_POST['add_info_training'];
	$add_info_other = $_POST['add_info_other'];

    // Check if all fields are populated
    if (
        empty($ingID) || empty($add_info_changes) || empty($add_info_acronyms) || empty($add_info_references) || empty($add_info_HazCom) || empty($add_info_GHS) || empty($add_info_training) || empty($add_info_other) 
    ) {
        $response["error"] = 'All fields are required';
        echo json_encode($response);
        return;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO ingredient_safety_data (
            ingID, add_info_changes, add_info_acronyms, add_info_references, add_info_HazCom, add_info_GHS, add_info_training, add_info_other
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            add_info_changes = VALUES(add_info_changes),
            add_info_acronyms = VALUES(add_info_acronyms),
            add_info_references = VALUES(add_info_references),
            add_info_HazCom = VALUES(add_info_HazCom),
            add_info_GHS = VALUES(add_info_GHS),
            add_info_training = VALUES(add_info_training),
            add_info_other = VALUES(add_info_other)"
    );

    // Check if the statement was prepared successfully
    if (!$stmt) {
        $response["error"] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        return;
    }

    // Bind the parameters
    $stmt->bind_param( 'isssssss', $ingID, $add_info_changes, $add_info_acronyms, $add_info_references, $add_info_HazCom, $add_info_GHS , $add_info_training , $add_info_other );

    // Execute the statement
    if ($stmt->execute()) {
        $response["success"] = 'Additional info data has been updated';
    } else {
        $response["error"] = 'Something went wrong ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    echo json_encode($response);
    return;
}

header('Location: /');
exit;

?>

<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');

$defCatClass = $settings['defCatClass'];
$defPercentage = $settings['defPercentage'];


if ($_POST['do'] = 'genSDS') {
	$supplier_id = $_POST['supplier_id'];
	$name = $_POST['name'];
	$ingID = $_POST['ingID'];
	$sds_tmpl = $_POST['tmplID'];
	$supplier = $_POST['supplier_name'];
	$address = $_POST['address'];
	$email = $_POST['email'];
	$country = $_POST['country'];
	$po = $_POST['po'];

	$notes = "SDS wizard generated";
	$prodNot = $_POST['prodNot'] ?: 'No data available';
  
	$language = $_POST['language'];
	$product_type = $_POST['product_type'];
	$sttype = $_POST['state_type'];
	$telephone = $_POST['telephone'];
  
  	$url = $_POST['url'];
  	$sdsCountry = $_POST['sdsCountry'];
  	$prodUse = $_POST['prodUse'];
  	$sdsLang = $_POST['sdsLang'];
  	$productType = $_POST['productType'];
  	$productState = $_POST['productState'];


	$brand_name = $settings['brandName'];
	$disclaimer = 'The information given in this Safety Data Sheet is based on our present knowledge and on european and national
regulations.This Safety Data Sheet describes safety requirements relative to identified uses, it doesn\'t guarantee all the
product properties particularly in the case of non identified uses.The product mustn\'t be used for any uses other than
those identified under heading 1.Since the user\'s working conditions are not known by us, it is the responsability of the
user to take all necessary measures to comply with legal requirements for specific uses and avoid negative health
effects.';
	
	$qHtml = mysqli_fetch_array(mysqli_query($conn, "SELECT id, content FROM templates WHERE id = '$sds_tmpl'"));
	$htmlContent =  $qHtml['content'];


	$ingSafetyInfo = mysqli_query($conn, "SELECT id,ingID,GHS FROM ingSafetyInfo WHERE ingID = '$ingID'");
	while($safety_res = mysqli_fetch_array($ingSafetyInfo)){
		$safety[] = $safety_res;
	}
	
	$ingFaidInfo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredient_safety_data WHERE ingID = '$ingID'"));

	if ( empty($settings['brandLogo']) ){ 
		$logo = "/img/logo.png";
	}else{
		$logo = $settings['brandLogo'];
	}

$search  = array(
	'%LOGO%',
	'%SDS_PRODUCT_NAME%',
	'%SDS_LANGUAGE%',
	'%SDS_PRODUCT_USE%',
	'%SDS_PRODUCT_ADA%',
	'%SDS_SUPPLIER_NAME%',
	'%SDS_SUPPLIER_ADDRESS%',
	'%SDS_SUPPLIER_EMAIL%',
	'%SDS_SUPPLIER_PHONE%',
	'%SDS_SUPPLIER_WEB%',
	'%SDS_SUPPLIER_COUNTRY%',
	'%SDS_SUPPLIER_PO%',
	'%PRODUCT_TYPE%',
	'%BRAND_NAME%',
	
	'%FIRST_AID_GENERAL%',
	'%FIRST_AID_INHALATION%',
	'%FIRST_AID_SKIN%',
	'%FIRST_AID_EYE%',
	'%FIRST_AID_INGESTION%',
	'%FIRST_AID_SELF_PROTECTION%',
	'%FIRST_AID_SYMPTOMS%',
	'%FIRST_AID_DR_NOTES%',

	'%SDS_DISCLAIMER%',
	'%CURRENT_DATE%'
);

$replace = array(
	$logo,
	$name,
	$sdsLang, 
	$prodUse,
	$prodNot,
	$supplier,
	$address,
	$email,
	$telephone,
	$url,
	$country,
	$po,	
	$type,
	$brand_name,					
					
	$ingFaidInfo['first_aid_general'],
	$ingFaidInfo['first_aid_inhalation'],
	$ingFaidInfo['first_aid_skin'],
	$ingFaidInfo['first_aid_eye'],
	$ingFaidInfo['first_aid_ingestion'],
	$ingFaidInfo['first_aid_self_protection'],
	$ingFaidInfo['first_aid_symptoms'],
	$ingFaidInfo['first_aid_dr_notes'],

	$disclaimer,
	date('d/M/Y')
);
	foreach($safety as $pictogram){
    	$y .= '<img class="img-fluid mx-2" style="width: 100px; height: 100px;" src="/img/Pictograms/GHS0'.$pictogram['GHS'].'.png">';
    }

	if($qCMP = mysqli_query($conn, "SELECT *  FROM ingredient_compounds WHERE ing = '".$name."' ORDER BY name ")){
		while($cmp = mysqli_fetch_array($qCMP)){
			$x .='<tr>
					<td align="center">'.$cmp['name'].'</td>
					<td align="center">'.$cmp['cas'].'</td>
					<td align="center">'.$cmp['ec'].'</td>
					<td align="center">'.$cmp['min_percentage'].'</td>
					<td align="center">'.$cmp['min_percentage'].'</td>
					<td align="center">'.$cmp['GHS'].'</td>
				</tr>';
		}
	
	}
	
$contents = str_replace(
    $search,
    $replace,
   		preg_replace('#(%CMP_MATERIALS_LIST%)#ms', $x,
   	 	preg_replace('#(%GHS_LABEL_LIST%)#ms', $y,
			$qHtml['content']
		)
    )
);


// Insert into sds_data table
$insert_sds_data = mysqli_query($conn, "INSERT INTO sds_data (product_name,product_use,country,language,product_type,state_type,supplier_id,docID) VALUES ('$name','$product_use','$country','$language','$product_type','$state_type','$supplier_id','0')");

if ($insert_sds_data) {
    $ownerID = mysqli_insert_id($conn);

    // Prepare and bind the documents table insert statement
    $stmt = $conn->prepare("INSERT INTO documents (docData, isSDS, name, type, notes, ownerID) VALUES (?, '1', ?, '0', ?, ?)");
    $stmt->bind_param("bssi", $contents, $name, $notes, $ownerID);

    // Send HTML content as BLOB
    $stmt->send_long_data(0, $contents);

    // Execute the query
    $sds = $stmt->execute();

    if ($sds) {
        $result['success'] = '<a href="/pages/viewDoc.php?type=sds&id='.$ownerID.'" target="_blank">Download file '.$ownerID.'</a>';
    } else {
        $result['error'] = "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    $result['error'] = "Error: " . mysqli_error($conn);
}

$conn->close();

echo json_encode($result);
  return;
}



?>
<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');

$defCatClass = $settings['defCatClass'];
$defPercentage = $settings['defPercentage'];


if ($_POST['do'] = 'genSDS') {
  $name = $_POST['name'];
  $sds_tmpl = $_POST['tmplID'];

  $notes = "SDS wizard generated";
  $product_use = $_POST['product_use'];
  $country = $_POST['country'];
  $language = $_POST['language'];
  $product_type = $_POST['product_type'];
  $sttype = $_POST['state_type'];
  $supplier_id = $_POST['supplier_id'];
  $po = $_POST['po'];
  $address = $_POST['address'];
  $telephone = $_POST['telephone'];
  $email = $_POST['email'];
  $url = $_POST['url'];
  $sdsCountry = $_POST['sdsCountry'];
  $prodUse = $_POST['prodUse'];
  $sdsLang = $_POST['sdsLang'];
  $productType = $_POST['productType'];
  $productState = $_POST['productState'];


	$qHtml = mysqli_fetch_array(mysqli_query($conn, "SELECT id, content FROM templates WHERE id = '$sds_tmpl'"));
	$htmlContent =  $qHtml['content'];

if ( empty($settings['brandLogo']) ){ 
	$logo = "/img/logo.png";
}else{
	$logo = $settings['brandLogo'];
}

$search  = array(
	'%LOGO%',
	'%SDS_LANGUAGE%',
	'%BRAND_ADDRESS%',
	'%BRAND_EMAIL%',
	'%BRAND_PHONE%',
	'%CUSTOMER_NAME%',
	'%CUSTOMER_ADDRESS%',
	'%CUSTOMER_EMAIL%',
	'%CUSTOMER_WEB%',
	'%SDS_PRODUCT_NAME%',
	'%PRODUCT_SIZE%',
	'%PRODUCT_CONCENTRATION%',
	'%IFRA_AMENDMENT%',
	'%IFRA_AMENDMENT_DATE%',
	'%PRODUCT_CAT_CLASS%',
	'%PRODUCT_TYPE%',
	
	'%CURRENT_DATE%'
	
	);

$replace = array(
	$logo,
	$sdsLang, 
	$settings['brandAddress'], 
	$settings['brandEmail'], 
	$settings['brandPhone'], 
	$customers['name'],
	$customers['address'],
	$customers['email'],
	$customers['web'],
	$name,
	$bottle,
	$type,'xs','h',strtoupper($defCatClass),
	$type,date('d/M/Y')
);

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
	
$contents =  str_replace( $search, $replace, preg_replace('#(%IFRA_MATERIALS_LIST%)#ms', $x, $qHtml['content']) );
//echo $contents;


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
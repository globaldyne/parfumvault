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
  //$disclaimer = $settings['sds_disclaimer'];
  
  $disclaimer = nl2br(htmlspecialchars_decode($settings['sds_disclaimer'], ENT_QUOTES));

  $qHtml = mysqli_fetch_array(mysqli_query($conn, "SELECT id, content FROM templates WHERE id = '$sds_tmpl'"));
  $htmlContent =  $qHtml['content'];


  $ingSafetyInfo = mysqli_query($conn, "SELECT id,ingID,GHS FROM ingSafetyInfo WHERE ingID = '$ingID'");
  while($safety_res = mysqli_fetch_array($ingSafetyInfo)){
    $safety[] = $safety_res;
  }
  
  $ingSafetyInfo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredient_safety_data WHERE ingID = '$ingID'"));
  $ingAllInfo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredients WHERE id = '$ingID'"));

  if ( empty($settings['brandLogo']) ){ 
    $logo = "/img/logo.png";
  }else{
    $logo = $settings['brandLogo'];
  }

  $search  = array(
  //General
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

  //Section 4
    '%FIRST_AID_GENERAL%',
    '%FIRST_AID_INHALATION%',
    '%FIRST_AID_SKIN%',
    '%FIRST_AID_EYE%',
    '%FIRST_AID_INGESTION%',
    '%FIRST_AID_SELF_PROTECTION%',
    '%FIRST_AID_SYMPTOMS%',
    '%FIRST_AID_DR_NOTES%',

  //Section 5
    '%FIRE_SUIT_MEDIA%',
    '%FIRE_NONSUIT_MEDIA%',
    '%FIRE_SPECIAL_HAZARDS%',
    '%FIRE_ADVICE%',
    '%FIRE_OTHER_INFO%',

  //Section 6
    '%ACC_REL_PERSONAL_CAUTIONS%',
    '%ACC_REL_ENV_CAUTIONS%',
    '%ACC_REL_CLEANING%',
    '%ACC_REL_REFERENCES%',
    '%ACC_REL_OTHER_INFO%',

  //Section 7
    '%HS_PROTECTION%',
    '%HS_HYGIENE%',
    '%HS_SAFE_STORE%',
    '%HS_JOINT_STORE%',
    '%HS_SPECIFIC_USES%',

  //Section 8
    '%EXPOSURE_OCC_LIMIT%',
    '%EXPOSURE_BIO_LIMIT%',
    '%EXPOSURE_USE_LIMIT%',
    '%EXPOSURE_OTHER_REM%',
    '%EXPOSURE_FACE_PROTECTION%',
    '%EXPOSURE_SKIN_PROTECTION%',
    '%EXPOSURE_RESP_PROTECTION%',
    '%EXPOSURE_ENV_EXPOSURE%',
    '%EXPOSURE_CONS_EXPOSURE%',
    '%EXPOSURE_OTHER_INFO%',

  //Section 9
    '%PHYSICAL_STATE%',
    '%COLOR%',
    '%ODOR%',
    '%ODOR_THRESHOLD%',
    '%PH%',
    '%MELTING_POINT%',
    '%BOILING_POINT%',
    '%FLASH_POINT%',
    '%EVAPORATION_RATE%',
    '%FLAMMABILITY%',
    '%LOWER_FLAMMABILITY_LIMIT%',
    '%VAPOUR_PRESSURE%',
    '%VAPOUR_DENSITY%',
    '%RELATIVE_DENSITY%',
    '%SOLUBILITY%',
    '%LOGP%',
    '%AUTO_INFL_TEMP%',
    '%DECOMP_TEMP%',
    '%VISCOSITY%',
    '%EXPLOSIVE_PROPERTIES%',
    '%OXIDISING_PROPERTIES%',
    '%SOLVENTS%',
    '%PARTICLE_CHARACTERISTICS%',
    '%PCP_OTHER_INFO%',
    '%PCP_OTHER_SEC_INFO%',

  //Section 10
    '%STABILLITY_REACTIVITY%',
    '%STABILLITY_CHEMICAL%',
    '%STABILLITY_REACTIONS%',
    '%STABILLITY_AVOID%',
    '%STABILLITY_INCOMPATIBILITY%',




  //Misc
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

  //Section 4
    $ingSafetyInfo['first_aid_general'],
    $ingSafetyInfo['first_aid_inhalation'],
    $ingSafetyInfo['first_aid_skin'],
    $ingSafetyInfo['first_aid_eye'],
    $ingSafetyInfo['first_aid_ingestion'],
    $ingSafetyInfo['first_aid_self_protection'],
    $ingSafetyInfo['first_aid_symptoms'],
    $ingSafetyInfo['first_aid_dr_notes'],

  //Section 5
    $ingSafetyInfo['firefighting_suitable_media'],
    $ingSafetyInfo['firefighting_non_suitable_media'],
    $ingSafetyInfo['firefighting_special_hazards'],
    $ingSafetyInfo['firefighting_advice'],
    $ingSafetyInfo['firefighting_other_info'],

  //Section 6
    $ingSafetyInfo['accidental_release_per_precautions'],
    $ingSafetyInfo['accidental_release_env_precautions'],
    $ingSafetyInfo['accidental_release_cleaning'],
    $ingSafetyInfo['accidental_release_refs'],
    $ingSafetyInfo['accidental_release_other_info'],

  //Section 7
    $ingSafetyInfo['handling_protection'],
    $ingSafetyInfo['handling_hygiene'],
    $ingSafetyInfo['handling_safe_storage'],
    $ingSafetyInfo['handling_joint_storage'],
    $ingSafetyInfo['handling_specific_uses'],

  //Section 8
    $ingSafetyInfo['exposure_occupational_limits'],
    $ingSafetyInfo['exposure_biological_limits'],
    $ingSafetyInfo['exposure_intented_use_limits'],
    $ingSafetyInfo['exposure_other_remarks'],
    $ingSafetyInfo['exposure_face_protection'],
    $ingSafetyInfo['exposure_skin_protection'],
    $ingSafetyInfo['exposure_respiratory_protection'],
    $ingSafetyInfo['exposure_env_exposure'],
    $ingSafetyInfo['exposure_consumer_exposure'],
    $ingSafetyInfo['exposure_other_info'],

  //Section 9
  //$ingAllInfo['physical_state'],
    $productState,
    $ingSafetyInfo['color'],
    $ingAllInfo['odor'],
    $ingSafetyInfo['odor_threshold'],
    $ingSafetyInfo['pH'],
    $ingSafetyInfo['melting_point'],
    $ingSafetyInfo['boiling_point'],
    $ingSafetyInfo['flash_point'],
    $ingSafetyInfo['evaporation_rate'],
    $ingSafetyInfo['flammability'],
    $ingSafetyInfo['low_flammability_limit'],
    $ingSafetyInfo['vapour_presure'],
    $ingSafetyInfo['vapour_density'],
    $ingSafetyInfo['relative_density'],
    $ingSafetyInfo['solubility'],
    $ingSafetyInfo['logP'],
    $ingSafetyInfo['auto_infl_temp'],
    $ingSafetyInfo['decomp_temp'],
    $ingSafetyInfo['viscosity'],
    $ingSafetyInfo['explosive_properties'],
    $ingSafetyInfo['oxidising_properties'],
    $ingSafetyInfo['soluble'],
    $ingSafetyInfo['particle_chars'],
    $ingSafetyInfo['pcp_other_info'],
    $ingSafetyInfo['pcp_other_sec_info'],

  //Section 10
    $ingSafetyInfo['stabillity_reactivity'],
    $ingSafetyInfo['stabillity_chemical'],
    $ingSafetyInfo['stabillity_reactions'],
    $ingSafetyInfo['stabillity_avoid'],
    $ingSafetyInfo['stabillity_incompatibility'],


  //Section 11
  //Section 12
  //Section 13
  //Section 14
  //Section 15
  //Section 16




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
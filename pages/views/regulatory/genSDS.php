<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/inc/product.php');

$defCatClass = $settings['defCatClass'];
$branding = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM branding WHERE owner_id = '$userID'"));
$sds_settings = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM sdsSettings WHERE owner_id = '$userID'"));


if ($_POST['do'] = 'genSDS') {
	
  if(empty($_POST['tmplID'])){
	 $result['error'] = "Please select a template";
	 echo json_encode($result);
	 return;
  }
  
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


  $brand_name = $branding['brandName'];
  
  $disclaimer = nl2br(htmlspecialchars_decode($sds_settings['sds_disclaimer'], ENT_QUOTES));

  $qHtml = mysqli_fetch_array(mysqli_query($conn, "SELECT id, content FROM templates WHERE id = '$sds_tmpl' AND owner_id = '$userID'"));
  if (!is_array($qHtml) || empty($qHtml['content'])) {
    $result['error'] = "Template not found or invalid.";
    echo json_encode($result);
    return;
  }
  $htmlContent =  $qHtml['content'];


  $ingSafetyInfo = mysqli_query($conn, "SELECT id,ingID,GHS FROM ingSafetyInfo WHERE ingID = '$ingID' AND owner_id = '$userID'");
  while($safety_res = mysqli_fetch_array($ingSafetyInfo)){
    $safety[] = $safety_res;
  }
  
  $ingSafetyInfo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredient_safety_data WHERE ingID = '$ingID' AND owner_id = '$userID'"));
  $ingAllInfo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredients WHERE id = '$ingID' AND owner_id = '$userID'"));

  if ( empty($branding['brandLogo']) ){ 
    $logo = "/img/logo.png";
  }else{
    $logo = $branding['brandLogo'];
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
    '%NOTES%',
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

	//SECTION 11
    '%TOXICOLOGICAL_ACUTE_ORAL%',
    '%TOXICOLOGICAL_ACUTE_DERMAL%',
    '%TOXICOLOGICAL_ACUTE_INHALATION%',
    '%TOXICOLOGICAL_SKIN%',
    '%TOXICOLOGICAL_EYE%',
    '%TOXICOLOGICAL_SENSITISATION%',
    '%TOXICOLOGICAL_ORGAN_REPEATED%',
    '%TOXICOLOGICAL_ORGAN_SINGLE%',
    '%TOXICOLOGICAL_CARCINOGENCITY%',
    '%TOXICOLOGICAL_REPRODUCTIVE%',
    '%TOXICOLOGICAL_CELL_MUTATION%',
    '%TOXICOLOGICAL_RESP_TRACT%',
    '%TOXICOLOGICAL_OTHER_INFO%',
    '%TOXICOLOGICAL_OTHER_HAZARDS%',

  	//Section 12
    '%ECOLOGICAL_TOXICITY%',
    '%ECOLOGICAL_PERSISTENCE%',
    '%ECOLOGICAL_BIOACCUMULATIVE%',
    '%ECOLOGICAL_SOIL_MOBILITY%',
    '%ECOLOGICAL_PBT_VPVB%',
    '%ECOLOGICAL_ENDOCRINE_PROPERTIES%',
    '%ECOLOGICAL_OTHER_ADV_EFFECTS%',
    '%ECOLOGICAL_ADDITIONAL_ECOTOXICOLOGICAL_INFO%',

  	//Section 13
    '%DISPOSAL_PRODUCT%',
    '%DISPOSAL_REMARKS%',

  	//Section 14
    '%TRANSPORT_UN_NUMBER%',
    '%TRANSPORT_SHIPPING_NAME%',
    '%TRANSPORT_HAZARD_CLASS%',
    '%TRANSPORT_PACKING_GROUP%',
    '%TRANSPORT_ENV_HAZARDS%',
    '%TRANSPORT_PRECAUTIONS%',
    '%TRANSPORT_BULK_SHIPPING%',

  	//Section 15
    '%LEGISLATION_SAFETY%',
    '%LEGISLATION_EU%',
    '%LEGISLATION_CHEMICAL_SAFETY_ASSESSMENT%',
    '%LEGISLATION_OTHER_INFO%',

	//Section 16
    '%ADD_INFO_CHANGES%',
    '%ADD_INFO_ACRONYMS%',
    '%ADD_INFO_REFERENCES%',
    '%ADD_INFO_HAZCOM%',
    '%ADD_INFO_GHS%',
    '%ADD_INFO_TRAINING%',
    '%ADD_INFO_OTHER%',

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
    $ingSafetyInfo['first_aid_general'] ?: 'N/A',
    $ingSafetyInfo['first_aid_inhalation'] ?: 'N/A',
    $ingSafetyInfo['first_aid_skin'] ?: 'N/A',
    $ingSafetyInfo['first_aid_eye'] ?: 'N/A',
    $ingSafetyInfo['first_aid_ingestion'] ?: 'N/A',
    $ingSafetyInfo['first_aid_self_protection'] ?: 'N/A',
    $ingSafetyInfo['first_aid_symptoms'] ?: 'N/A',
    $ingSafetyInfo['first_aid_dr_notes'] ?: 'N/A',

  	//Section 5
    $ingSafetyInfo['firefighting_suitable_media'] ?: 'N/A',
    $ingSafetyInfo['firefighting_non_suitable_media'] ?: 'N/A',
    $ingSafetyInfo['firefighting_special_hazards'] ?: 'N/A',
    $ingSafetyInfo['firefighting_advice'] ?: 'N/A',
    $ingSafetyInfo['firefighting_other_info'] ?: 'N/A',

  	//Section 6
    $ingSafetyInfo['accidental_release_per_precautions'] ?: 'N/A',
    $ingSafetyInfo['accidental_release_env_precautions'] ?: 'N/A',
    $ingSafetyInfo['accidental_release_cleaning'] ?: 'N/A',
    $ingSafetyInfo['accidental_release_refs'] ?: 'N/A',
    $ingSafetyInfo['accidental_release_other_info'] ?: 'N/A',

  	//Section 7
    $ingSafetyInfo['handling_protection'] ?: 'N/A',
    $ingSafetyInfo['handling_hygiene'] ?: 'N/A',
    $ingSafetyInfo['handling_safe_storage'] ?: 'N/A',
    $ingSafetyInfo['handling_joint_storage'] ?: 'N/A',
    $ingSafetyInfo['handling_specific_uses'] ?: 'N/A',

 	//Section 8
    $ingSafetyInfo['exposure_occupational_limits'] ?: 'N/A',
    $ingSafetyInfo['exposure_biological_limits'] ?: 'N/A',
    $ingSafetyInfo['exposure_intented_use_limits'] ?: 'N/A',
    $ingSafetyInfo['exposure_other_remarks'] ?: 'N/A',
    $ingSafetyInfo['exposure_face_protection'] ?: 'N/A',
    $ingSafetyInfo['exposure_skin_protection'] ?: 'N/A',
    $ingSafetyInfo['exposure_respiratory_protection'] ?: 'N/A',
    $ingSafetyInfo['exposure_env_exposure'] ?: 'N/A',
    $ingSafetyInfo['exposure_consumer_exposure'] ?: 'N/A',
    $ingSafetyInfo['exposure_other_info'] ?: 'N/A',

  	//Section 9
    $productState,
    $ingSafetyInfo['color'] ?: 'N/A',
    $ingAllInfo['notes'] ?: 'N/A',
    $ingSafetyInfo['odor_threshold'] ?: 'N/A',
    $ingSafetyInfo['pH'] ?: 'N/A',
    $ingSafetyInfo['melting_point'] ?: 'N/A',
    $ingSafetyInfo['boiling_point'] ?: 'N/A',
    $ingSafetyInfo['flash_point'] ?: 'N/A',
    $ingSafetyInfo['evaporation_rate'] ?: 'N/A',
    $ingSafetyInfo['flammability'] ?: 'N/A',
    $ingSafetyInfo['low_flammability_limit'] ?: 'N/A',
    $ingSafetyInfo['vapour_pressure'] ?: 'N/A',
    $ingSafetyInfo['vapour_density'] ?: 'N/A',
    $ingSafetyInfo['relative_density'] ?: 'N/A',
    $ingSafetyInfo['solubility'] ?: 'N/A',
    $ingSafetyInfo['logP'] ?: 'N/A',
    $ingSafetyInfo['auto_infl_temp'] ?: 'N/A',
    $ingSafetyInfo['decomp_temp'] ?: 'N/A',
    $ingSafetyInfo['viscosity'] ?: 'N/A',
    $ingSafetyInfo['explosive_properties'] ?: 'N/A',
    $ingSafetyInfo['oxidising_properties'] ?: 'N/A',
    $ingSafetyInfo['soluble'] ?: 'N/A',
    $ingSafetyInfo['particle_chars'] ?: 'N/A',
    $ingSafetyInfo['pcp_other_info'] ?: 'N/A',
    $ingSafetyInfo['pcp_other_sec_info'] ?: 'N/A',

  	//Section 10
    $ingSafetyInfo['stabillity_reactivity'] ?: 'N/A',
    $ingSafetyInfo['stabillity_chemical'] ?: 'N/A',
    $ingSafetyInfo['stabillity_reactions'] ?: 'N/A',
    $ingSafetyInfo['stabillity_avoid'] ?: 'N/A',
    $ingSafetyInfo['stabillity_incompatibility'] ?: 'N/A',

	//Section 11
    $ingSafetyInfo['toxicological_acute_oral'] ?: 'N/A',
    $ingSafetyInfo['toxicological_acute_dermal'] ?: 'N/A',
    $ingSafetyInfo['toxicological_acute_inhalation'] ?: 'N/A',
    $ingSafetyInfo['toxicological_skin'] ?: 'N/A',
    $ingSafetyInfo['toxicological_eye'] ?: 'N/A',
    $ingSafetyInfo['toxicological_sensitisation'] ?: 'N/A',
    $ingSafetyInfo['toxicological_organ_repeated'] ?: 'N/A',
    $ingSafetyInfo['toxicological_organ_single'] ?: 'N/A',
    $ingSafetyInfo['toxicological_carcinogencity'] ?: 'N/A',
    $ingSafetyInfo['toxicological_reproductive'] ?: 'N/A',
    $ingSafetyInfo['toxicological_cell_mutation'] ?: 'N/A',
    $ingSafetyInfo['toxicological_resp_tract'] ?: 'N/A',
    $ingSafetyInfo['toxicological_other_info'] ?: 'N/A',
    $ingSafetyInfo['toxicological_other_hazards'] ?: 'N/A',

  	//Section 12
    $ingSafetyInfo['ecological_toxicity'] ?: 'N/A',
    $ingSafetyInfo['ecological_persistence'] ?: 'N/A',
    $ingSafetyInfo['ecological_bioaccumulative'] ?: 'N/A',
    $ingSafetyInfo['ecological_soil_mobility'] ?: 'N/A',
    $ingSafetyInfo['ecological_PBT_vPvB'] ?: 'N/A',
    $ingSafetyInfo['ecological_endocrine_properties'] ?: 'N/A',
    $ingSafetyInfo['ecological_other_adv_effects'] ?: 'N/A',
    $ingSafetyInfo['ecological_additional_ecotoxicological_info'] ?: 'N/A',
    
  	//Section 13
    $ingSafetyInfo['disposal_product'] ?: 'N/A',
    $ingSafetyInfo['disposal_remarks'] ?: 'N/A',
    
  	//Section 14
    $ingSafetyInfo['transport_un_number'] ?: 'N/A',
    $ingSafetyInfo['transport_shipping_name'] ?: 'N/A',
    $ingSafetyInfo['transport_hazard_class'] ?: 'N/A',
    $ingSafetyInfo['transport_packing_group'] ?: 'N/A',
    $ingSafetyInfo['transport_env_hazards'] ?: 'N/A',
    $ingSafetyInfo['transport_precautions'] ?: 'N/A',
    $ingSafetyInfo['transport_bulk_shipping'] ?: 'N/A',

  	//Section 15
    $ingSafetyInfo['legislation_safety'] ?: 'N/A',
    $ingSafetyInfo['legislation_eu'] ?: 'N/A',
    $ingSafetyInfo['legislation_chemical_safety_assessment'] ?: 'N/A',
    $ingSafetyInfo['legislation_other_info'] ?: 'N/A',

	//Section 16
    $ingSafetyInfo['add_info_changes'] ?: 'N/A',
    $ingSafetyInfo['add_info_acronyms'] ?: 'N/A',
    $ingSafetyInfo['add_info_references'] ?: 'N/A',
    $ingSafetyInfo['add_info_HazCom'] ?: 'N/A',
    $ingSafetyInfo['add_info_GHS'] ?: 'N/A',
    $ingSafetyInfo['add_info_training'] ?: 'N/A',
    $ingSafetyInfo['add_info_other'] ?: 'N/A',


    $disclaimer,
    date('d/M/Y')
  );
  
  foreach($safety as $pictogram){
    $y .= '<img class="img-fluid mx-2" style="width: 100px; height: 100px;" src="/img/Pictograms/GHS0'.$pictogram['GHS'].'.png">';
  }

  if($qCMP = mysqli_query($conn, "SELECT *  FROM ingredient_compounds WHERE ing = '".$name."' AND owner_id = '$userID' ORDER BY name ")){
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


  $insert_sds_data = mysqli_query($conn, "INSERT INTO sds_data (product_name,product_use,country,language,product_type,state_type,supplier_id,docID,owner_id) VALUES ('$name','$prodUse','$country','$language','$product_type','$state_type','$supplier_id','0','$userID')");

  if ($insert_sds_data) {
    $ownerID = mysqli_insert_id($conn);
    $result = [];

    $stmt = $conn->prepare("INSERT INTO documents (docData, isSDS, name, type, notes, ownerID, owner_id) VALUES (?, '1', ?, '0', ?, ?, ?)");
    $stmt->bind_param("bssis", $contents, $name, $notes, $ownerID, $userID);
    $stmt->send_long_data(0, $contents);
    $sds = $stmt->execute();

    if ($sds) {
      $result['success'] = '<a href="/pages/viewDoc.php?type=sds&id='.$ownerID.'" target="_blank">View the SDS file</a>';
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

<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
if(!$_GET['ingID']){
  echo 'Invalid ID';
  return;
}

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');


$ingSafetyInfo = mysqli_query($conn, "SELECT id,ingID,GHS FROM ingSafetyInfo WHERE ingID = '".$_GET['ingID']."'");
while($safety_res = mysqli_fetch_array($ingSafetyInfo)){
  $safety[] = $safety_res;
}

$pictograms = mysqli_query($conn, "SELECT id,name,code FROM pictograms");
while($pictograms_res = mysqli_fetch_array($pictograms)){
  $pictogram[] = $pictograms_res;
}

$ingSafetyInfo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredient_safety_data WHERE ingID = '".$_GET['ingID']."'"));


?>
<style>
  .image-container {
    position: relative;
    display: inline-block;
  }
  .remove-icon {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: var( --bs-body-bg);
    border-radius: 50%;
    padding: 5px;
    cursor: pointer;
    z-index: 10;
  }
  body {
    overflow-y: visible;
  }
</style>
<h3>Safety Information</h3>
<div class="container">

  <div class="accordion" id="accordionPanelsSafetyInfo">
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseGENI" aria-expanded="false" aria-controls="panelsStayOpen-collapseGENI">
          1. Identification of the substance/mixture and of the company/undertaking
        </button>
      </h2>
      <div id="panelsStayOpen-collapseGENI" class="accordion-collapse collapse">
        <div class="accordion-body">

          <div class="alert alert-info mt-4"><i class="fa-solid fa-info mx-2"></i>
            This section will be auto-populated from data provided in previous stages
          </div>

        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseGHS" aria-expanded="false" aria-controls="panelsStayOpen-collapseGHS">
          2. Hazards identification
        </button>
      </h2>
      <div id="panelsStayOpen-collapseGHS" class="accordion-collapse collapse">
        <div class="accordion-body">
          <div class="row g-3 align-items-center">
            <div class="mt-5 mb-5 col-auto">
              <select name="pictogram" id="pictogram" class="form-control selectpicker" data-live-search="true">
                <option value="" disabled selected="selected">Choose a pictogram to add</option>
                <?php foreach($pictograms as $pictogram){?>
                  <option data-content="<img class='img_ing_sel' src='/img/Pictograms/GHS0<?=$pictogram['code'];?>.png'><?=$pictogram['name'];?>" value="<?=$pictogram['code'];?>"></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div  id="img_pictograms">
            
            <?php
            $column_count = 0;
            $total_columns = 9;
            
            echo '<div class="row">';
            foreach ($safety as $pict) {
              if ($column_count == $total_columns) {
                echo '</div><div class="row">';
                $column_count = 0;
              }
              echo '<div class="col-auto">';
              echo '<div class="image-container" id="image-container-' . $pict['GHS'] . '">';
              echo '<img src="/img/Pictograms/GHS0' . $pict['GHS'] . '.png" class="img-fluid" style="width: 150px; height: 150px;">';
              echo '<span class="remove-icon" id="removeImage-' . $pict['GHS'] . '" data-id="' . $pict['GHS'] . '">&times;</span>';
              echo '</div>';
              echo '</div>';
              $column_count++;
            }
            echo '</div>';
            ?>
          </div>
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseCMPS" aria-expanded="false" aria-controls="panelsStayOpen-collapseCMPS">
          3. Composition/information on ingredients
        </button>
      </h2>
      <div id="panelsStayOpen-collapseCMPS" class="accordion-collapse collapse">
        <div class="accordion-body">
          
          <div class="alert alert-info mt-4"><i class="fa-solid fa-info mx-2"></i>
            This section will be populated from the data added in the Compositions tab
          </div>
          
        </div>
      </div>
    </div>

    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFaid" aria-expanded="false" aria-controls="panelsStayOpen-collapseFaid">
         4. First aid measures
       </button>
     </h2>
     <div id="panelsStayOpen-collapseFaid" class="accordion-collapse collapse">
      <div class="accordion-body">
        <div id="FaidMsg"></div>
        <div class="row g-3">
          <!-- First Aid Section -->
          <div class="col-md-6">
            <div class="form-group">
              <label for="first_aid_general">General</label>
              <input name="first_aid_general" type="text" class="form-control" id="first_aid_general" value="<?=$ingSafetyInfo['first_aid_general']?>">
            </div>
            <div class="form-group">
              <label for="first_aid_inhalation">Inhalation</label>
              <input name="first_aid_inhalation" type="text" class="form-control" id="first_aid_inhalation" value="<?=$ingSafetyInfo['first_aid_inhalation']?>">
            </div>
            <div class="form-group">
              <label for="first_aid_skin">Skin contact</label>
              <input name="first_aid_skin" type="text" class="form-control" id="first_aid_skin" value="<?=$ingSafetyInfo['first_aid_skin']?>">
            </div>
            <div class="form-group">
              <label for="first_aid_eye">Eye contact</label>
              <input name="first_aid_eye" type="text" class="form-control" id="first_aid_eye" value="<?=$ingSafetyInfo['first_aid_eye']?>">
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-group">
              <label for="first_aid_ingestion">Ingestion</label>
              <input name="first_aid_ingestion" type="text" class="form-control" id="first_aid_ingestion" value="<?=$ingSafetyInfo['first_aid_ingestion']?>">
            </div>
            <div class="form-group">
              <label for="first_aid_self_protection">Self Protection</label>
              <input name="first_aid_self_protection" type="text" class="form-control" id="first_aid_self_protection" value="<?=$ingSafetyInfo['first_aid_self_protection']?>">
            </div>
            <div class="form-group">
              <label for="first_aid_symptoms">Symptoms</label>
              <input name="first_aid_symptoms" type="text" class="form-control" id="first_aid_symptoms" value="<?=$ingSafetyInfo['first_aid_symptoms']?>">
            </div>
            <div class="form-group">
              <label for="first_aid_dr_notes">Doctor's Notes</label>
              <input name="first_aid_dr_notes" type="text" class="form-control" id="first_aid_dr_notes" value="<?=$ingSafetyInfo['first_aid_dr_notes']?>">
            </div>
          </div>
        </div>
        <div class="row g-3 mt-3">
          <div class="col-12">
            <button type="submit" class="btn btn-primary" id="save_faid">Save data</button>
          </div>
        </div>        
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFire" aria-expanded="false" aria-controls="panelsStayOpen-collapseFire">
        5. Firefighting measures
      </button>
    </h2>
    <div id="panelsStayOpen-collapseFire" class="accordion-collapse collapse">
      <div class="accordion-body">
       <div id="FireMsg"></div>
       <div class="row mb-3">
        <div class="col-md-6">
          <label for="firefighting_suitable_media" class="form-label">Suitable Media</label>
          <input name="firefighting_suitable_media" type="text" class="form-control" id="firefighting_suitable_media" value="<?=$ingSafetyInfo['firefighting_suitable_media']?>">
        </div>
        <div class="col-md-6">
          <label for="firefighting_non_suitable_media" class="form-label">Non-Suitable Media</label>
          <input name="firefighting_non_suitable_media" type="text" class="form-control" id="firefighting_non_suitable_media" value="<?=$ingSafetyInfo['firefighting_non_suitable_media']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="firefighting_special_hazards" class="form-label">Special Hazards</label>
          <input name="firefighting_special_hazards" type="text" class="form-control" id="firefighting_special_hazards" value="<?=$ingSafetyInfo['firefighting_special_hazards']?>">
        </div>
        <div class="col-md-6">
          <label for="firefighting_advice" class="form-label">Advice</label>
          <input name="firefighting_advice" type="text" class="form-control" id="firefighting_advice" value="<?=$ingSafetyInfo['firefighting_advice']?>">
        </div>
      </div>
      <div class="mb-3">
        <label for="firefighting_other_info" class="form-label">Other Information</label>
        <textarea class="form-control" id="firefighting_other_info" name="firefighting_other_info" rows="4"><?=$ingSafetyInfo['firefighting_other_info']?></textarea>
      </div>
      <button type="submit" class="btn btn-primary" id="save_fire">Save data</button>
    </div>
  </div>
</div>

<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseAccRel" aria-expanded="false" aria-controls="panelsStayOpen-collapseAccRel">
      6. Accidental release measures
    </button>
  </h2>
  <div id="panelsStayOpen-collapseAccRel" class="accordion-collapse collapse">
    <div class="accordion-body">
      <div id="AccRelMsg"></div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="accidental_release_per_precautions" class="form-label">Personal Precautions</label>
          <input name="accidental_release_per_precautions" type="text" class="form-control" id="accidental_release_per_precautions" value="<?=$ingSafetyInfo['accidental_release_per_precautions']?>">
        </div>
        <div class="col-md-6">
          <label for="accidental_release_env_precautions" class="form-label">Environmental Precautions</label>
          <input name="accidental_release_env_precautions" type="text" class="form-control" id="accidental_release_env_precautions" value="<?=$ingSafetyInfo['accidental_release_env_precautions']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="accidental_release_cleaning" class="form-label">Cleaning Methods</label>
          <input name="accidental_release_cleaning" type="text" class="form-control" id="accidental_release_cleaning" value="<?=$ingSafetyInfo['accidental_release_cleaning']?>">
        </div>
        <div class="col-md-6">
          <label for="accidental_release_refs" class="form-label">References</label>
          <input name="accidental_release_refs" type="text" class="form-control" id="accidental_release_refs" value="<?=$ingSafetyInfo['accidental_release_refs']?>">
        </div>
      </div>
      <div class="mb-3">
        <label for="accidental_release_other_info" class="form-label">Other Information</label>
        <textarea class="form-control" id="accidental_release_other_info" name="accidental_release_other_info" rows="4"><?=$ingSafetyInfo['accidental_release_other_info']?></textarea>
      </div>
      <button type="submit" class="btn btn-primary" id="save_acc_rel">Save data</button>
    </div>
  </div>
</div>

<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseHS" aria-expanded="false" aria-controls="panelsStayOpen-collapseHS">
      7. Handling and Storage
    </button>
  </h2>
  <div id="panelsStayOpen-collapseHS" class="accordion-collapse collapse">
    <div class="accordion-body">
      <div id="HSMsg"></div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="handling_protection" class="form-label">Protection</label>
          <input name="handling_protection" type="text" class="form-control" id="handling_protection" value="<?=$ingSafetyInfo['handling_protection']?>">
        </div>
        <div class="col-md-6">
          <label for="handling_hygiene" class="form-label">Hygiene</label>
          <input name="handling_hygiene" type="text" class="form-control" id="handling_hygiene" value="<?=$ingSafetyInfo['handling_hygiene']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="handling_safe_storage" class="form-label">Safe Storage</label>
          <input name="handling_safe_storage" type="text" class="form-control" id="handling_safe_storage" value="<?=$ingSafetyInfo['handling_safe_storage']?>">
        </div>
        <div class="col-md-6">
          <label for="handling_joint_storage" class="form-label">Joint Storage</label>
          <input name="handling_joint_storage" type="text" class="form-control" id="handling_joint_storage" value="<?=$ingSafetyInfo['handling_joint_storage']?>">
        </div>
      </div>
      <div class="mb-3">
        <label for="specificUses" class="form-label">Specific Uses</label>
        <textarea class="form-control" id="handling_specific_uses" name="handling_specific_uses" rows="2"><?=$ingSafetyInfo['handling_specific_uses']?></textarea>
      </div>
      <button type="submit" class="btn btn-primary" id="save_HS">Save data</button>
    </div>
  </div>
</div>




<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseExposure" aria-expanded="false" aria-controls="panelsStayOpen-collapseExposure">
      8. Exposure controls/personal protection
    </button>
  </h2>
  <div id="panelsStayOpen-collapseExposure" class="accordion-collapse collapse">
    <div class="accordion-body">
      <div id="ExposureMsg"></div>
      
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="exposure_occupational_limits" class="form-label">Occupational Limits</label>
          <input name="exposure_occupational_limits" type="text" class="form-control" id="exposure_occupational_limits" value="<?=$ingSafetyInfo['exposure_occupational_limits']?>">
        </div>
        <div class="col-md-6">
          <label for="exposure_biological_limits" class="form-label">Biological Limits</label>
          <input name="exposure_biological_limits" type="text" class="form-control" id="exposure_biological_limits" value="<?=$ingSafetyInfo['exposure_biological_limits']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="exposure_intented_use_limits" class="form-label">Intended Use Limits</label>
          <input name="exposure_intented_use_limits" type="text" class="form-control" id="exposure_intented_use_limits" value="<?=$ingSafetyInfo['exposure_intented_use_limits']?>">
        </div>
        <div class="col-md-6">
          <label for="exposure_other_remarks" class="form-label">Other Remarks</label>
          <input name="exposure_other_remarks" type="text" class="form-control" id="exposure_other_remarks" value="<?=$ingSafetyInfo['exposure_other_remarks']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="exposure_face_protection" class="form-label">Face Protection</label>
          <input name="exposure_face_protection" type="text" class="form-control" id="exposure_face_protection" value="<?=$ingSafetyInfo['exposure_face_protection']?>">
        </div>
        <div class="col-md-6">
          <label for="exposure_skin_protection" class="form-label">Skin Protection</label>
          <input name="exposure_skin_protection" type="text" class="form-control" id="exposure_skin_protection" value="<?=$ingSafetyInfo['exposure_skin_protection']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="exposure_respiratory_protection" class="form-label">Respiratory protection</label>
          <input name="exposure_respiratory_protection" type="text" class="form-control" id="exposure_respiratory_protection" value="<?=$ingSafetyInfo['exposure_respiratory_protection']?>">
        </div>
        <div class="col-md-6">
          <label for="exposure_env_exposure" class="form-label">Environmental exposure controls</label>
          <input name="exposure_env_exposure" type="text" class="form-control" id="exposure_env_exposure" value="<?=$ingSafetyInfo['exposure_env_exposure']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="exposure_consumer_exposure" class="form-label">Consumer exposure controls</label>
          <input name="exposure_consumer_exposure" type="text" class="form-control" id="exposure_consumer_exposure" value="<?=$ingSafetyInfo['exposure_consumer_exposure']?>">
        </div>
        <div class="col-md-6">
          <label for="exposure_other_info" class="form-label">Additional information</label>
          <input name="exposure_other_info" type="text" class="form-control" id="exposure_other_info" value="<?=$ingSafetyInfo['exposure_other_info']?>">
        </div>
      </div>
      <button type="submit" class="btn btn-primary" id="save_exposure">Save data</button>
      
    </div>
  </div>
</div>



<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsePCProperties" aria-expanded="false" aria-controls="panelsStayOpen-collapsePCProperties">
      9. Physical and chemical Properties
    </button>
  </h2>
  <div id="panelsStayOpen-collapsePCProperties" class="accordion-collapse collapse">
    <div class="accordion-body">
      <div id="PCPropertiesMsg"></div>
      <div class="alert alert-info mt-4"><i class="fa-solid fa-info mx-2"></i>
        Some variables for this section are provided by the General and Technical Data of the ingredient
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="odor_threshold" class="form-label">Odor threshold</label>
          <input name="odor_threshold" type="text" class="form-control" id="odor_threshold" value="<?=$ingSafetyInfo['odor_threshold']?>">
        </div>
        <div class="col-md-6">
          <label for="pH" class="form-label">pH</label>
          <input name="pH" type="text" class="form-control" id="pH" value="<?=$ingSafetyInfo['pH']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="melting_point" class="form-label">Melting/Freezing point</label>
          <input name="melting_point" type="text" class="form-control" id="melting_point" value="<?=$ingSafetyInfo['melting_point']?>">
        </div>
        <div class="col-md-6">
          <label for="boiling_point" class="form-label">Boiling point</label>
          <input name="boiling_point" type="text" class="form-control" id="boiling_point" value="<?=$ingSafetyInfo['boiling_point']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="flash_point" class="form-label">Flash point</label>
          <input name="flash_point" type="text" class="form-control" id="flash_point" value="<?=$ingSafetyInfo['flash_point']?>">
        </div>
        <div class="col-md-6">
          <label for="evaporation_rate" class="form-label">Evaporation rate</label>
          <input name="evaporation_rate" type="text" class="form-control" id="evaporation_rate" value="<?=$ingSafetyInfo['evaporation_rate']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="flammability" class="form-label">Flammability</label>
          <input name="flammability" type="text" class="form-control" id="flammability" value="<?=$ingSafetyInfo['flammability']?>">
        </div>
        <div class="col-md-6">
          <label for="low_flammability_limit" class="form-label">Lower limit of flammability</label>
          <input name="low_flammability_limit" type="text" class="form-control" id="low_flammability_limit" value="<?=$ingSafetyInfo['low_flammability_limit']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="vapour_pressure" class="form-label">Vapour pressure</label>
          <input name="vapour_pressure" type="text" class="form-control" id="vapour_pressure" value="<?=$ingSafetyInfo['vapour_pressure']?>">
        </div>
        <div class="col-md-6">
          <label for="vapour_density" class="form-label">Vapour density</label>
          <input name="vapour_density" type="text" class="form-control" id="vapour_density" value="<?=$ingSafetyInfo['vapour_density']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="relative_density" class="form-label">Relative density</label>
          <input name="relative_density" type="text" class="form-control" id="relative_density" value="<?=$ingSafetyInfo['relative_density']?>">
        </div>
      </div>
      
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="oxidising_properties" class="form-label">Oxidising properties</label>
          <input name="oxidising_properties" type="text" class="form-control" id="oxidising_properties" value="<?=$ingSafetyInfo['oxidising_properties']?>">
        </div>        
        <div class="col-md-6">
          <label for="solubility" class="form-label">Solubility</label>
          <input name="solubility" type="text" class="form-control" id="solubility" value="<?=$ingSafetyInfo['solubility']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="auto_infl_temp" class="form-label">Auto-inflammability temperature</label>
          <input name="auto_infl_temp" type="text" class="form-control" id="auto_infl_temp" value="<?=$ingSafetyInfo['auto_infl_temp']?>">
        </div>        
        <div class="col-md-6">
          <label for="decomp_temp" class="form-label">Decomposition temperature</label>
          <input name="decomp_temp" type="text" class="form-control" id="decomp_temp" value="<?=$ingSafetyInfo['decomp_temp']?>">
        </div>
      </div> 
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="viscosity" class="form-label">Viscosity</label>
          <input name="viscosity" type="text" class="form-control" id="viscosity" value="<?=$ingSafetyInfo['viscosity']?>">
        </div>        
        <div class="col-md-6">
          <label for="explosive_properties" class="form-label">Explosive properties</label>
          <input name="explosive_properties" type="text" class="form-control" id="explosive_properties" value="<?=$ingSafetyInfo['explosive_properties']?>">
        </div>
      </div> 
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="particle_chars" class="form-label">Particle characteristics</label>
          <input name="particle_chars" type="text" class="form-control" id="particle_chars" value="<?=$ingSafetyInfo['particle_chars']?>">
        </div>        
        <div class="col-md-6">
          <label for="logP" class="form-label">Partition coefficient, n-octanol/water(log Pow)</label>
          <input name="logP" type="text" class="form-control" id="logP" value="<?=$ingSafetyInfo['logP']?>">
        </div>
      </div>        
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="soluble" class="form-label">Water solubility</label>
          <input name="soluble" type="text" class="form-control" id="soluble" value="<?=$ingSafetyInfo['soluble']?>">
        </div>        
        <div class="col-md-6">
          <label for="color" class="form-label">Color</label>
          <input name="color" type="text" class="form-control" id="color" value="<?=$ingSafetyInfo['color']?>">
        </div>
      </div>
      
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="pcp_other_info" class="form-label">Other safety information</label>
          <input name="pcp_other_info" type="text" class="form-control" id="pcp_other_info" value="<?=$ingSafetyInfo['pcp_other_info']?>">
        </div>        
        <div class="col-md-6">
          <label for="pcp_other_sec_info" class="form-label">Other security characteristics</label>
          <input name="pcp_other_sec_info" type="text" class="form-control" id="pcp_other_sec_info" value="<?=$ingSafetyInfo['pcp_other_sec_info']?>">
        </div>
      </div>               
      <button type="submit" class="btn btn-primary" id="save_pcp">Save data</button>
      
    </div>
  </div>
</div>




<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseSR" aria-expanded="false" aria-controls="panelsStayOpen-collapseSR">
      10. Stability and Reactivity
    </button>
  </h2>
  <div id="panelsStayOpen-collapseSR" class="accordion-collapse collapse">
    <div class="accordion-body">
      <div id="SRMsg"></div>

      <!-- Stability and Reactivity Section -->
      <h4 class="mb-3">Stability and Reactivity</h4>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="stabillity_reactivity" class="form-label">Reactivity</label>
          <input name="stabillity_reactivity" type="text" class="form-control" id="stabillity_reactivity" value="<?=$ingSafetyInfo['stabillity_reactivity']?>">
        </div>
        <div class="col-md-6">
          <label for="stabillity_chemical" class="form-label">Chemical Stability</label>
          <input name="stabillity_chemical" type="text" class="form-control" id="stabillity_chemical" value="<?=$ingSafetyInfo['stabillity_chemical']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="stabillity_reactions" class="form-label">Possibility of Hazardous Reactions</label>
          <input name="stabillity_reactions" type="text" class="form-control" id="stabillity_reactions" value="<?=$ingSafetyInfo['stabillity_reactions']?>">
        </div>
        <div class="col-md-6">
          <label for="stabillity_avoid" class="form-label">Conditions to Avoid</label>
          <input name="stabillity_avoid" type="text" class="form-control" id="stabillity_avoid" value="<?=$ingSafetyInfo['stabillity_avoid']?>">
        </div>
      </div>
      <div class="mb-3">
        <label for="stabillity_incompatibility" class="form-label">Incompatible Materials</label>
        <textarea class="form-control" id="stabillity_incompatibility" name="stabillity_incompatibility" rows="4"><?=$ingSafetyInfo['stabillity_incompatibility']?></textarea>
      </div>

      <button type="submit" class="btn btn-primary" id="save_sr">Save data</button>
    </div>
  </div>
</div> 


<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTX" aria-expanded="false" aria-controls="panelsStayOpen-collapseTX">
      11. Toxicological information
    </button>
  </h2>
  <div id="panelsStayOpen-collapseTX" class="accordion-collapse collapse">
    <div class="accordion-body">
      <div id="TXMsg"></div>

      <!-- Toxicological Information Section -->
      <h4 class="mb-3">Toxicological Information</h4>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="toxicological_acute_oral" class="form-label">Acute Oral Toxicity</label>
          <input name="toxicological_acute_oral" type="text" class="form-control" id="toxicological_acute_oral" value="<?=$ingSafetyInfo['toxicological_acute_oral']?>">
        </div>
        <div class="col-md-6">
          <label for="toxicological_acute_dermal" class="form-label">Acute Dermal Toxicity</label>
          <input name="toxicological_acute_dermal" type="text" class="form-control" id="toxicological_acute_dermal" value="<?=$ingSafetyInfo['toxicological_acute_dermal']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="toxicological_acute_inhalation" class="form-label">Acute Inhalation Toxicity</label>
          <input name="toxicological_acute_inhalation" type="text" class="form-control" id="toxicological_acute_inhalation" value="<?=$ingSafetyInfo['toxicological_acute_inhalation']?>">
        </div>
        <div class="col-md-6">
          <label for="toxicological_skin" class="form-label">Skin Corrosion/Irritation</label>
          <input name="toxicological_skin" type="text" class="form-control" id="toxicological_skin" value="<?=$ingSafetyInfo['toxicological_skin']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="toxicological_eye" class="form-label">Serious Eye Damage/Irritation</label>
          <input name="toxicological_eye" type="text" class="form-control" id="toxicological_eye" value="<?=$ingSafetyInfo['toxicological_eye']?>">
        </div>
        <div class="col-md-6">
          <label for="toxicological_sensitisation" class="form-label">Respiratory or Skin Sensitisation</label>
          <input name="toxicological_sensitisation" type="text" class="form-control" id="toxicological_sensitisation" value="<?=$ingSafetyInfo['toxicological_sensitisation']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="toxicological_organ_repeated" class="form-label">STOT - Repeated Exposure</label>
          <input name="toxicological_organ_repeated" type="text" class="form-control" id="toxicological_organ_repeated" value="<?=$ingSafetyInfo['toxicological_organ_repeated']?>">
        </div>
        <div class="col-md-6">
          <label for="toxicological_organ_single" class="form-label">STOT - Single Exposure</label>
          <input name="toxicological_organ_single" type="text" class="form-control" id="toxicological_organ_single" value="<?=$ingSafetyInfo['toxicological_organ_single']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="toxicological_carcinogencity" class="form-label">Carcinogenicity</label>
          <input name="toxicological_carcinogencity" type="text" class="form-control" id="toxicological_carcinogencity" value="<?=$ingSafetyInfo['toxicological_carcinogencity']?>">
        </div>
        <div class="col-md-6">
          <label for="toxicological_reproductive" class="form-label">Reproductive Toxicity</label>
          <input name="toxicological_reproductive" type="text" class="form-control" id="toxicological_reproductive" value="<?=$ingSafetyInfo['toxicological_reproductive']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="toxicological_cell_mutation" class="form-label">Germ Cell Mutagenicity</label>
          <input name="toxicological_cell_mutation" type="text" class="form-control" id="toxicological_cell_mutation" value="<?=$ingSafetyInfo['toxicological_cell_mutation']?>">
        </div>
        <div class="col-md-6">
          <label for="toxicological_resp_tract" class="form-label">Respiratory Tract Irritation</label>
          <input name="toxicological_resp_tract" type="text" class="form-control" id="toxicological_resp_tract" value="<?=$ingSafetyInfo['toxicological_resp_tract']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="toxicological_other_info" class="form-label">Other Information</label>
          <input name="toxicological_other_info" type="text" class="form-control" id="toxicological_other_info" value="<?=$ingSafetyInfo['toxicological_other_info']?>">
        </div>
        <div class="col-md-6">
          <label for="toxicological_other_hazards" class="form-label">Other Hazards</label>
          <input name="toxicological_other_hazards" type="text" class="form-control" id="toxicological_other_hazards" value="<?=$ingSafetyInfo['toxicological_other_hazards']?>">
        </div>
      </div>


      <button type="submit" class="btn btn-primary" id="save_tx">Save data</button>
    </div>
  </div>
</div>         



<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseEC" aria-expanded="false" aria-controls="panelsStayOpen-collapseEC">
      12. Ecological information
    </button>
  </h2>
  <div id="panelsStayOpen-collapseEC" class="accordion-collapse collapse">
    <div class="accordion-body">
      <div id="ECMsg"></div>

      <!-- Ecological Information Section -->
      <h4 class="mb-3">Ecological Information</h4>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="ecological_toxicity" class="form-label">Toxicity</label>
          <input name="ecological_toxicity" type="text" class="form-control" id="ecological_toxicity" value="<?=$ingSafetyInfo['ecological_toxicity']?>">
        </div>
        <div class="col-md-6">
          <label for="ecological_persistence" class="form-label">Persistence and Degradability</label>
          <input name="ecological_persistence" type="text" class="form-control" id="ecological_persistence" value="<?=$ingSafetyInfo['ecological_persistence']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="ecological_bioaccumulative" class="form-label">Bioaccumulative Potential</label>
          <input name="ecological_bioaccumulative" type="text" class="form-control" id="ecological_bioaccumulative" value="<?=$ingSafetyInfo['ecological_bioaccumulative']?>">
        </div>
        <div class="col-md-6">
          <label for="ecological_soil_mobility" class="form-label">Soil Mobility</label>
          <input name="ecological_soil_mobility" type="text" class="form-control" id="ecological_soil_mobility" value="<?=$ingSafetyInfo['ecological_soil_mobility']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="ecological_PBT_vPvB" class="form-label">PBT and vPvB Assessment</label>
          <input name="ecological_PBT_vPvB" type="text" class="form-control" id="ecological_PBT_vPvB" value="<?=$ingSafetyInfo['ecological_PBT_vPvB']?>">
        </div>
        <div class="col-md-6">
          <label for="ecological_endocrine_properties" class="form-label">Endocrine Disrupting Properties</label>
          <input name="ecological_endocrine_properties" type="text" class="form-control" id="ecological_endocrine_properties" value="<?=$ingSafetyInfo['ecological_endocrine_properties']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="ecological_other_adv_effects" class="form-label">Other Adverse Effects</label>
          <input name="ecological_other_adv_effects" type="text" class="form-control" id="ecological_other_adv_effects" value="<?=$ingSafetyInfo['ecological_other_adv_effects']?>">
        </div>
        <div class="col-md-6">
          <label for="ecological_additional_ecotoxicological_info" class="form-label">Additional Ecotoxicological Information</label>
          <input name="ecological_additional_ecotoxicological_info" type="text" class="form-control" id="ecological_additional_ecotoxicological_info" value="<?=$ingSafetyInfo['ecological_additional_ecotoxicological_info']?>">
        </div>
      </div>

      <button type="submit" class="btn btn-primary" id="save_ec">Save data</button>
    </div>
  </div>
</div>        


<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseDIS" aria-expanded="false" aria-controls="panelsStayOpen-collapseDIS">
      13. Disposal considerations
    </button>
  </h2>
  <div id="panelsStayOpen-collapseDIS" class="accordion-collapse collapse">
    <div class="accordion-body">
      <div id="DISMsg"></div>

      <!-- Disposal Section -->
      <h4 class="mb-3">Disposal</h4>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="disposal_product" class="form-label">Product Disposal</label>
          <input name="disposal_product" type="text" class="form-control" id="disposal_product" value="<?=$ingSafetyInfo['disposal_product']?>">
        </div>
        <div class="col-md-6">
          <label for="disposal_remarks" class="form-label">Remarks</label>
          <input name="disposal_remarks" type="text" class="form-control" id="disposal_remarks" value="<?=$ingSafetyInfo['disposal_remarks']?>">
        </div>
      </div>

      <button type="submit" class="btn btn-primary" id="save_dis">Save data</button>
    </div>
  </div>
</div>


<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTRANS" aria-expanded="false" aria-controls="panelsStayOpen-collapseTRANS">
      14. Transport information
    </button>
  </h2>
  <div id="panelsStayOpen-collapseTRANS" class="accordion-collapse collapse">
    <div class="accordion-body">
      <div id="TRANSMsg"></div>

      <!-- Transport Section -->
      <h4 class="mb-3">Transport</h4>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="transport_un_number" class="form-label">UN Number</label>
          <input name="transport_un_number" type="text" class="form-control" id="transport_un_number" value="<?=$ingSafetyInfo['transport_un_number']?>">
        </div>
        <div class="col-md-6">
          <label for="transport_shipping_name" class="form-label">Shipping Name</label>
          <input name="transport_shipping_name" type="text" class="form-control" id="transport_shipping_name" value="<?=$ingSafetyInfo['transport_shipping_name']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="transport_hazard_class" class="form-label">Hazard Class</label>
          <input name="transport_hazard_class" type="text" class="form-control" id="transport_hazard_class" value="<?=$ingSafetyInfo['transport_hazard_class']?>">
        </div>
        <div class="col-md-6">
          <label for="transport_packing_group" class="form-label">Packing Group</label>
          <input name="transport_packing_group" type="text" class="form-control" id="transport_packing_group" value="<?=$ingSafetyInfo['transport_packing_group']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="transport_env_hazards" class="form-label">Environmental Hazards</label>
          <input name="transport_env_hazards" type="text" class="form-control" id="transport_env_hazards" value="<?=$ingSafetyInfo['transport_env_hazards']?>">
        </div>
        <div class="col-md-6">
          <label for="transport_precautions" class="form-label">Transport Precautions</label>
          <input name="transport_precautions" type="text" class="form-control" id="transport_precautions" value="<?=$ingSafetyInfo['transport_precautions']?>">
        </div>
      </div>
      <div class="mb-3">
        <label for="transport_bulk_shipping" class="form-label">Bulk Shipping</label>
        <textarea class="form-control" id="transport_bulk_shipping" name="transport_bulk_shipping" rows="4"><?=$ingSafetyInfo['transport_bulk_shipping']?></textarea>
      </div>

      <button type="submit" class="btn btn-primary" id="save_trans">Save data</button>
    </div>
  </div>
</div>



<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseLEG" aria-expanded="false" aria-controls="panelsStayOpen-collapseLEG">
      15. Regulatory information
    </button>
  </h2>
  <div id="panelsStayOpen-collapseLEG" class="accordion-collapse collapse">
    <div class="accordion-body">
      <div id="LEGMsg"></div>
      <!-- Legislation Section -->
      <h4 class="mb-3">Legislation</h4>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="legislation_safety" class="form-label">Safety, Health and Environmental Regulations/Legislation</label>
          <input name="legislation_safety" type="text" class="form-control" id="legislation_safety" value="<?=$ingSafetyInfo['legislation_safety']?>">
        </div>
        <div class="col-md-6">
          <label for="legislation_eu" class="form-label">EU Regulations</label>
          <input name="legislation_eu" type="text" class="form-control" id="legislation_eu" value="<?=$ingSafetyInfo['legislation_eu']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="legislation_chemical_safety_assessment" class="form-label">Chemical Safety Assessment</label>
          <input name="legislation_chemical_safety_assessment" type="text" class="form-control" id="legislation_chemical_safety_assessment" value="<?=$ingSafetyInfo['legislation_chemical_safety_assessment']?>">
        </div>
        <div class="col-md-6">
          <label for="legislation_other_info" class="form-label">Other Information</label>
          <input name="legislation_other_info" type="text" class="form-control" id="legislation_other_info" value="<?=$ingSafetyInfo['legislation_other_info']?>">
        </div>
      </div>
      
      <button type="submit" class="btn btn-primary" id="save_leg">Save data</button>
    </div>
  </div>
</div>



<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseADDINF" aria-expanded="false" aria-controls="panelsStayOpen-collapseADDINF">
      16. Other information
    </button>
  </h2>
  <div id="panelsStayOpen-collapseADDINF" class="accordion-collapse collapse">
    <div class="accordion-body">
      <div id="ADDINFMsg"></div>

      <!-- Additional Information Section -->
      <h4 class="mb-3">Additional Information</h4>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="add_info_changes" class="form-label">Changes from Previous Version</label>
          <input name="add_info_changes" type="text" class="form-control" id="add_info_changes" value="<?=$ingSafetyInfo['add_info_changes']?>">
        </div>
        <div class="col-md-6">
          <label for="add_info_acronyms" class="form-label">Acronyms and Abbreviations</label>
          <input name="add_info_acronyms" type="text" class="form-control" id="add_info_acronyms" value="<?=$ingSafetyInfo['add_info_acronyms']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="add_info_references" class="form-label">References</label>
          <input name="add_info_references" type="text" class="form-control" id="add_info_references" value="<?=$ingSafetyInfo['add_info_references']?>">
        </div>
        <div class="col-md-6">
          <label for="add_info_HazCom" class="form-label">Hazard Communication</label>
          <input name="add_info_HazCom" type="text" class="form-control" id="add_info_HazCom" value="<?=$ingSafetyInfo['add_info_HazCom']?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="add_info_GHS" class="form-label">GHS Classification</label>
          <input name="add_info_GHS" type="text" class="form-control" id="add_info_GHS" value="<?=$ingSafetyInfo['add_info_GHS']?>">
        </div>
        <div class="col-md-6">
          <label for="add_info_training" class="form-label">Training Advice</label>
          <input name="add_info_training" type="text" class="form-control" id="add_info_training" value="<?=$ingSafetyInfo['add_info_training']?>">
        </div>
      </div>
      <div class="mb-3">
        <label for="add_info_other" class="form-label">Other Information</label>
        <textarea class="form-control" id="add_info_other" name="add_info_other" rows="4"><?=$ingSafetyInfo['add_info_other']?></textarea>
      </div>

      <button type="submit" class="btn btn-primary" id="save_addinf">Save data</button>
    </div>
  </div>
</div>

</div>



</div>
<script>
  $(document).ready(function() {

    $('.selectpicker').selectpicker('refresh');

    $('#save_addinf').on('click',  function () {
      $.ajax({ 
        url: "/core/core.php", 
        type: "POST",
        data: {
          manage: "ingredient",
          tab: "add_info",
          ingID: "<?=$_GET['ingID'];?>",
          add_info_changes: $("#add_info_changes").val(),
          add_info_acronyms: $("#add_info_acronyms").val(),
          add_info_references: $("#add_info_references").val(),
          add_info_HazCom: $("#add_info_HazCom").val(),
          add_info_GHS: $("#add_info_GHS").val(),
          add_info_training: $("#add_info_training").val(),
          add_info_other: $("#add_info_other").val()

        },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';

          }else{
            msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#ADDINFMsg').html(msg);
        }
      });
    });
    
    $('#save_leg').on('click',  function () {
      $.ajax({ 
        url: "/core/core.php", 
        type: "POST",
        data: {
          manage: "ingredient",
          tab: "leg_info",
          ingID: "<?=$_GET['ingID'];?>",
          legislation_safety: $("#legislation_safety").val(),
          legislation_eu: $("#legislation_eu").val(),
          legislation_chemical_safety_assessment: $("#legislation_chemical_safety_assessment").val(),
          legislation_other_info: $("#legislation_other_info").val()
        },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';

          }else{
            msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#LEGMsg').html(msg);
        }
      });
    });
    
    $('#save_trans').on('click',  function () {
      $.ajax({ 
        url: "/core/core.php", 
        type: "POST",
        data: {
          manage: "ingredient",
          tab: "trans_info",
          ingID: "<?=$_GET['ingID'];?>",
          transport_un_number: $("#transport_un_number").val(),
          transport_shipping_name: $("#transport_shipping_name").val(),
          transport_hazard_class: $("#transport_hazard_class").val(),
          transport_packing_group: $("#transport_packing_group").val(),
          transport_env_hazards: $("#transport_env_hazards").val(),
          transport_precautions: $("#transport_precautions").val(),
          transport_bulk_shipping: $("#transport_bulk_shipping").val()
        },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';

          }else{
            msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#TRANSMsg').html(msg);
        }
      });
    });

    $('#save_dis').on('click',  function () {
      $.ajax({ 
        url: "/core/core.php", 
        type: "POST",
        data: {
          manage: "ingredient",
          tab: "dis_info",
          ingID: "<?=$_GET['ingID'];?>",
          disposal_product: $("#disposal_product").val(),
          disposal_remarks: $("#disposal_remarks").val()
        },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';

          }else{
            msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#DISMsg').html(msg);
        }
      });
    });

    $('#save_ec').on('click',  function () {
      $.ajax({ 
        url: "/core/core.php", 
        type: "POST",
        data: {
          manage: "ingredient",
          tab: "ec_info",
          ingID: "<?=$_GET['ingID'];?>",
          ecological_toxicity: $("#ecological_toxicity").val(),
          ecological_persistence: $("#ecological_persistence").val(),
          ecological_bioaccumulative: $("#ecological_bioaccumulative").val(),
          ecological_soil_mobility: $("#ecological_soil_mobility").val(),
          ecological_PBT_vPvB: $("#ecological_PBT_vPvB").val(),
          ecological_endocrine_properties: $("#ecological_endocrine_properties").val(),
          ecological_other_adv_effects: $("#ecological_other_adv_effects").val(),
          ecological_additional_ecotoxicological_info: $("#ecological_additional_ecotoxicological_info").val()
        },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';

          }else{
            msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#ECMsg').html(msg);
        }
      });
    });
    
    
    $('#save_tx').on('click',  function () {
      $.ajax({ 
        url: "/core/core.php", 
        type: "POST",
        data: {
          manage: "ingredient",
          tab: "tx_info",
          ingID: "<?=$_GET['ingID'];?>",
          toxicological_acute_oral: $("#toxicological_acute_oral").val(),
          toxicological_acute_dermal: $("#toxicological_acute_dermal").val(),
          toxicological_acute_inhalation: $("#toxicological_acute_inhalation").val(),

          toxicological_skin: $("#toxicological_skin").val(),
          toxicological_eye: $("#toxicological_eye").val(),
          toxicological_sensitisation: $("#toxicological_sensitisation").val(),
          toxicological_organ_repeated: $("#toxicological_organ_repeated").val(),
          toxicological_organ_single: $("#toxicological_organ_single").val(),
          toxicological_carcinogencity: $("#toxicological_carcinogencity").val(),
          toxicological_reproductive: $("#toxicological_reproductive").val(),
          toxicological_cell_mutation: $("#toxicological_cell_mutation").val(),
          toxicological_resp_tract: $("#toxicological_resp_tract").val(),
          toxicological_other_info: $("#toxicological_other_info").val(),
          toxicological_other_hazards: $("#toxicological_other_hazards").val()

        },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';

          }else{
            msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#TXMsg').html(msg);
        }
      });
    });
    
    $('#save_sr').on('click',  function () {
      $.ajax({ 
        url: "/core/core.php", 
        type: "POST",
        data: {
          manage: "ingredient",
          tab: "sr_info",
          ingID: "<?=$_GET['ingID'];?>",
          stabillity_reactivity: $("#stabillity_reactivity").val(),
          stabillity_chemical: $("#stabillity_chemical").val(),
          stabillity_reactions: $("#stabillity_reactions").val(),
          stabillity_avoid: $("#stabillity_avoid").val(),
          stabillity_incompatibility: $("#stabillity_incompatibility").val(),
        },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';

          }else{
            msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#SRMsg').html(msg);
        }
      });
    }); 
    
    $('#safety_info').on('changed.bs.select',  function () {
      $.ajax({ 
        url: "/core/core.php", 
        type: "POST",
        data: {
          manage: "ingredient",
          tab: "safety_info",
          ingID: "<?=$_GET['ingID'];?>",
          pictogram: $("#pictogram").val(),
          action: "add"
        },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            fetch_safety();
          }else{
            var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#ingMsg').html(msg);
        }
      });
    });
    
    $('#img_pictograms').on('click', '[id*=removeImage]', function() {
      var imageId = $(this).data('id');
      $.ajax({
        type: "POST",
        url: "/core/core.php",
        data: { 
          manage: "ingredient",
          tab: "safety_info",
          ingID: "<?=$_GET['ingID'];?>",
          pictogram_id: imageId,
          action: "remove"
        },
        dataType: 'json',
        success: function(data) {
          if (data.success) {
            $('#image-container-' + imageId).remove();
          } else {
            var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#ingMsg').html(msg);
        }
      });
    });
    
    $('#save_faid').on('click',  function () {
      $.ajax({ 
        url: "/core/core.php", 
        type: "POST",
        data: {
          manage: "ingredient",
          tab: "faid_info",
          ingID: "<?=$_GET['ingID'];?>",
          first_aid_general: $("#first_aid_general").val(),
          first_aid_inhalation: $("#first_aid_inhalation").val(),
          first_aid_skin: $("#first_aid_skin").val(),
          first_aid_eye: $("#first_aid_eye").val(),
          first_aid_ingestion: $("#first_aid_ingestion").val(),
          first_aid_self_protection: $("#first_aid_self_protection").val(),
          first_aid_symptoms: $("#first_aid_symptoms").val(),
          first_aid_dr_notes: $("#first_aid_dr_notes").val(),
        },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';

          }else{
            msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#FaidMsg').html(msg);
        }
      });
    });
    
    
    $('#save_fire').on('click',  function () {
      $.ajax({ 
        url: "/core/core.php", 
        type: "POST",
        data: {
          manage: "ingredient",
          tab: "fire_info",
          ingID: "<?=$_GET['ingID'];?>",
          firefighting_suitable_media: $("#firefighting_suitable_media").val(),
          firefighting_non_suitable_media: $("#firefighting_non_suitable_media").val(),
          firefighting_special_hazards: $("#firefighting_special_hazards").val(),
          firefighting_advice: $("#firefighting_advice").val(),
          firefighting_other_info: $("#firefighting_other_info").val()
        },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';

          }else{
            msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#FireMsg').html(msg);
        }
      });
    });
    
    $('#save_acc_rel').on('click',  function () {
      $.ajax({ 
        url: "/core/core.php", 
        type: "POST",
        data: {
          manage: "ingredient",
          tab: "save_acc_rel",
          ingID: "<?=$_GET['ingID'];?>",
          accidental_release_per_precautions: $("#accidental_release_per_precautions").val(),
          accidental_release_env_precautions: $("#accidental_release_env_precautions").val(),
          accidental_release_cleaning: $("#accidental_release_cleaning").val(),
          accidental_release_refs: $("#accidental_release_refs").val(),
          accidental_release_other_info: $("#accidental_release_other_info").val()
        },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';

          }else{
            msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#AccRelMsg').html(msg);
        }
      });
    });
    
    
    $('#save_HS').on('click',  function () {
      $.ajax({ 
        url: "/core/core.php", 
        type: "POST",
        data: {
          manage: "ingredient",
          tab: "HS",
          ingID: "<?=$_GET['ingID'];?>",
          handling_protection: $("#handling_protection").val(),
          handling_hygiene: $("#handling_hygiene").val(),
          handling_safe_storage: $("#handling_safe_storage").val(),
          handling_joint_storage: $("#handling_joint_storage").val(),
          handling_specific_uses: $("#handling_specific_uses").val()
        },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';

          }else{
            msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#HSMsg').html(msg);
        }
      });
    });
    
    $('#save_exposure').on('click',  function () {
      $.ajax({ 
        url: "/core/core.php", 
        type: "POST",
        data: {
          manage: "ingredient",
          tab: "exposure_data",
          ingID: "<?=$_GET['ingID'];?>",
          exposure_occupational_limits: $("#exposure_occupational_limits").val(),
          exposure_biological_limits: $("#exposure_biological_limits").val(),
          exposure_intented_use_limits: $("#exposure_intented_use_limits").val(),
          exposure_other_remarks: $("#exposure_other_remarks").val(),
          exposure_face_protection: $("#exposure_face_protection").val(),
          exposure_skin_protection: $("#exposure_skin_protection").val(),
          exposure_respiratory_protection: $("#exposure_respiratory_protection").val(),
          exposure_env_exposure: $("#exposure_env_exposure").val(),
          exposure_consumer_exposure: $("#exposure_consumer_exposure").val(),
          exposure_other_info: $("#exposure_other_info").val()

        },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';

          }else{
            msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#ExposureMsg').html(msg);
        }
      });
    });
    
    $('#save_pcp').on('click',  function () {
      $.ajax({ 
        url: "/core/core.php", 
        type: "POST",
        data: {
          manage: "ingredient",
          tab: "pcp",
          ingID: "<?=$_GET['ingID'];?>",
          odor_threshold: $("#odor_threshold").val(),
          pH: $("#pH").val(),
          melting_point: $("#melting_point").val(),
          boiling_point: $("#boiling_point").val(),
          flash_point: $("#flash_point").val(),
          evaporation_rate: $("#evaporation_rate").val(),
          solubility: $("#solubility").val(),
          auto_infl_temp: $("#auto_infl_temp").val(),
          decomp_temp: $("#decomp_temp").val(),
          viscosity: $("#viscosity").val(),
          explosive_properties: $("#explosive_properties").val(),
          oxidising_properties: $("#oxidising_properties").val(),
          particle_chars: $("#particle_chars").val(),
          flammability: $("#flammability").val(),
          logP: $("#logP").val(),
          soluble: $("#soluble").val(),
          color: $("#color").val(),
          low_flammability_limit: $("#low_flammability_limit").val(),
          vapour_pressure: $("#vapour_pressure").val(),
          vapour_density: $("#vapour_density").val(),
          relative_density: $("#relative_density").val(),
          pcp_other_info: $("#pcp_other_info").val(),
          pcp_other_sec_info: $("#pcp_other_sec_info").val()

        },
        dataType: 'json',
        success: function (data) {
          if (data.success) {
            msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';

          }else{
            msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
          }
          
          $('#PCPropertiesMsg').html(msg);
        }
      });
    }); 
    
    
    
    
  });
</script>

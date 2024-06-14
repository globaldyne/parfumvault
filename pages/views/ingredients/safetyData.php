<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
if(!$_POST['ingID']){
	echo 'Invalid ID';
	return;
}

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');


$ingSafetyInfo = mysqli_query($conn, "SELECT id,ingID,GHS FROM ingSafetyInfo WHERE ingID = '".$_POST['ingID']."'");
while($safety_res = mysqli_fetch_array($ingSafetyInfo)){
	$safety[] = $safety_res;
}

$pictograms = mysqli_query($conn, "SELECT id,name,code FROM pictograms");
while($pictograms_res = mysqli_fetch_array($pictograms)){
	$pictogram[] = $pictograms_res;
}

$ingSafetyInfo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredient_safety_data WHERE ingID = '".$_POST['ingID']."'"));


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
		background-color: white;
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
  
  
  
</div>






  
</div>
<script>
$(document).ready(function() {

	$('.selectpicker').selectpicker('refresh');
	
	$('#safety_info').on('changed.bs.select',  function () {
		$.ajax({ 
			url: "/pages/update_data.php", 
			type: "POST",
			data: {
				manage: "ingredient",
				tab: "safety_info",
				ingID: "<?=$_POST['ingID'];?>",
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
			url: "/pages/update_data.php",
			data: { 
				manage: "ingredient",
				tab: "safety_info",
				ingID: "<?=$_POST['ingID'];?>",
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
			url: "/pages/update_data.php", 
			type: "POST",
			data: {
				manage: "ingredient",
				tab: "faid_info",
				ingID: "<?=$_POST['ingID'];?>",
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
			url: "/pages/update_data.php", 
			type: "POST",
			data: {
				manage: "ingredient",
				tab: "fire_info",
				ingID: "<?=$_POST['ingID'];?>",
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
			url: "/pages/update_data.php", 
			type: "POST",
			data: {
				manage: "ingredient",
				tab: "save_acc_rel",
				ingID: "<?=$_POST['ingID'];?>",
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
			url: "/pages/update_data.php", 
			type: "POST",
			data: {
				manage: "ingredient",
				tab: "HS",
				ingID: "<?=$_POST['ingID'];?>",
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
	
	
	
	
	
	
	
	
});
</script>
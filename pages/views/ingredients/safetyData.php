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

$ingFaidInfo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredient_safety_data WHERE ingID = '".$_POST['ingID']."'"));


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
	<div class="accordion" id="accordionPanelsStayOpenExample">
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseGHS" aria-expanded="true" aria-controls="panelsStayOpen-collapseGHS">
        Hazard Pictograms
      </button>
    </h2>
    <div id="panelsStayOpen-collapseGHS" class="accordion-collapse collapse show">
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
        First aid measures
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
                  <input name="first_aid_general" type="text" class="form-control" id="first_aid_general" value="<?=$ingFaidInfo['first_aid_general']?>">
                </div>
                <div class="form-group">
                  <label for="first_aid_inhalation">Inhalation</label>
                  <input name="first_aid_inhalation" type="text" class="form-control" id="first_aid_inhalation" value="<?=$ingFaidInfo['first_aid_inhalation']?>">
                </div>
                <div class="form-group">
                  <label for="first_aid_skin">Skin contact</label>
                  <input name="first_aid_skin" type="text" class="form-control" id="first_aid_skin" value="<?=$ingFaidInfo['first_aid_skin']?>">
                </div>
                <div class="form-group">
                  <label for="first_aid_eye">Eye contact</label>
                  <input name="first_aid_eye" type="text" class="form-control" id="first_aid_eye" value="<?=$ingFaidInfo['first_aid_eye']?>">
                </div>
              </div>
    
              <div class="col-md-6">
                <div class="form-group">
                  <label for="first_aid_ingestion">Ingestion</label>
                  <input name="first_aid_ingestion" type="text" class="form-control" id="first_aid_ingestion" value="<?=$ingFaidInfo['first_aid_ingestion']?>">
                </div>
                <div class="form-group">
                  <label for="first_aid_self_protection">Self Protection</label>
                  <input name="first_aid_self_protection" type="text" class="form-control" id="first_aid_self_protection" value="<?=$ingFaidInfo['first_aid_self_protection']?>">
                </div>
                <div class="form-group">
                  <label for="first_aid_symptoms">Symptoms</label>
                  <input name="first_aid_symptoms" type="text" class="form-control" id="first_aid_symptoms" value="<?=$ingFaidInfo['first_aid_symptoms']?>">
                </div>
                <div class="form-group">
                  <label for="first_aid_dr_notes">Doctor's Notes</label>
                  <input name="first_aid_dr_notes" type="text" class="form-control" id="first_aid_dr_notes" value="<?=$ingFaidInfo['first_aid_dr_notes']?>">
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
        Firefighting measures
      </button>
    </h2>
    <div id="panelsStayOpen-collapseFire" class="accordion-collapse collapse">
      <div class="accordion-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="suitableMedia" class="form-label">Suitable Media</label>
                    <input type="text" class="form-control" id="suitableMedia" name="firefighting_suitable_media">
                </div>
                <div class="col-md-6">
                    <label for="nonSuitableMedia" class="form-label">Non-Suitable Media</label>
                    <input type="text" class="form-control" id="nonSuitableMedia" name="firefighting_non_suitable_media">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="specialHazards" class="form-label">Special Hazards</label>
                    <input type="text" class="form-control" id="specialHazards" name="firefighting_special_hazards">
                </div>
                <div class="col-md-6">
                    <label for="advice" class="form-label">Advice</label>
                    <input type="text" class="form-control" id="advice" name="firefighting_advice">
                </div>
            </div>
            <div class="mb-3">
                <label for="otherInfo" class="form-label">Other Information</label>
                <textarea class="form-control" id="otherInfo" name="firefighting_other_info" rows="4"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" id="save_fire">Save data</button>
      </div>
    </div>
  </div>
  
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseAccRel" aria-expanded="false" aria-controls="panelsStayOpen-collapseAccRel">
        Accidental release measures
      </button>
    </h2>
    <div id="panelsStayOpen-collapseAccRel" class="accordion-collapse collapse">
      <div class="accordion-body">
            <div class="row mb-3">
            <div class="col-md-6">
                <label for="precautions" class="form-label">Precautions</label>
                <input type="text" class="form-control" id="precautions" name="accidental_release_per_precautions">
            </div>
            <div class="col-md-6">
                <label for="envPrecautions" class="form-label">Environmental Precautions</label>
                <input type="text" class="form-control" id="envPrecautions" name="accidental_release_env_precautions">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="cleaning" class="form-label">Cleaning Methods</label>
                <input type="text" class="form-control" id="cleaning" name="accidental_release_cleaning">
            </div>
            <div class="col-md-6">
                <label for="refs" class="form-label">References</label>
                <input type="text" class="form-control" id="refs" name="accidental_release_refs">
            </div>
        </div>
        <div class="mb-3">
            <label for="otherInfo" class="form-label">Other Information</label>
            <textarea class="form-control" id="otherInfo" name="accidental_release_other_info" rows="4"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
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
	
});
</script>
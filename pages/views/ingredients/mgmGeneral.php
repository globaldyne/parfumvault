<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

require_once(__ROOT__.'/func/profileImg.php');


$ingID = $_GET["id"];
$res_ingTypes = mysqli_query($conn, "SELECT id,name FROM ingTypes ORDER BY name ASC"); //PUBLIC
$res_ingStrength = mysqli_query($conn, "SELECT id,name FROM ingStrength ORDER BY name ASC"); //PUBLIC
$res_ingCategory = mysqli_query($conn, "SELECT id,image,name,notes FROM ingCategory WHERE owner_id = '$userID' ORDER BY name ASC");
$res_ingProfiles = mysqli_query($conn, "SELECT id,name FROM ingProfiles ORDER BY id ASC"); //PUBLIC
$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredients WHERE id = '$ingID' AND owner_id = '$userID'"));

// Generate array with labels data 
$labelsData = array(); 
$labelsQ = mysqli_query($conn,"SELECT label_name FROM ingredientLabels WHERE ingredient_id = '$ingID' AND owner_id = '$userID'");
while($qlabels = mysqli_fetch_array($labelsQ)){
	
	$labels = $qlabels['label_name'];
	array_push($labelsData, $labels); 
	error_log("Label: " . $labels);
}


if($_GET["newIngName"]){
	$newIngName = mysqli_real_escape_string($conn, base64_decode($_GET["newIngName"]));

	if(empty(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ingredients WHERE name = '$ingName' AND owner_id = '$userID'")))){
		$ing['cas'] = mysqli_real_escape_string($conn, $_GET["newIngCAS"]);
	}
}
?>
<h3>General</h3>
<hr>

<div class="row g-3">
  
 <?php if(empty($ingID)){?>
 <div class="form-floating mt-3 col-12">
    <input name="name" type="text" class="form-control" id="name" placeholder="Name" value="<?=$newIngName?>">
    <label for="name">Name</label>
</div>
 <?php } ?>
  
  <div class="form-floating mt-3 col-12">
    <input name="INCI" type="text" class="form-control" id="INCI" placeholder="IUPAC" value="<?php echo htmlspecialchars($ing['INCI']); ?>" />
    <label class="mx-2" for="INCI">IUPAC</label>
  </div>
  <div class="form-floating mt-3 col-md-6">
    <input name="cas" type="text" class="form-control" id="cas" placeholder="CAS" value="<?php echo $ing['cas']; ?>">
    <label class="mx-2" for="cas">CAS</label>
    
  </div>
  <div class="form-floating mt-3 col-md-6">
    <input name="einecs" type="text" class="form-control" id="einecs" placeholder="EINECS" value="<?php echo $ing['einecs']; ?>">
    <label class="mx-2" for="einecs">EINECS</label>
  </div>
  <div class="form-floating mt-3 col-md-6">
    <input name="reach" type="text" class="form-control" id="reach" placeholder="REACH" value="<?php echo $ing['reach']; ?>">
    <label class="mx-2" for="reach">REACH</label>
  </div>

  <div class="form-floating mt-3 col-md-6">
    <input name="fema" type="text" class="form-control" id="fema" placeholder="FEMA" value="<?php echo $ing['FEMA']; ?>">
    <label class="mx-2" for="fema">FEMA</label>
  </div>
  
  <div class="mt-3 col-md-6">
     <label for="purity" class="form-label">Purity</label>
     <div class="input-group">
    	<input name="purity" type="text" class="form-control" id="purity" value="<?php echo $ing['purity']?: '100'; ?>"  aria-label="purity" aria-describedby="purity-addon">
        <span class="input-group-text" id="purity-addon">%</span>
  	</div>
  </div>
  <div class="mt-3 col-md-6">
    <label for="solvent" class="form-label">Solvent</label>
    <select name="solvent" id="solvent" class="form-control selectpicker" data-live-search="true" <?php if($ing['purity'] == 100){ ?>disabled<?php } ?> >
        <option value="" selected disabled>Solvent</option>
        <option value="None">None</option>
        <?php
        $res_dil = mysqli_query($conn, "SELECT id, name FROM ingredients WHERE (type = 'Solvent' OR type = 'Carrier') AND owner_id = '$userID' ORDER BY name ASC");
        while ($r_dil = mysqli_fetch_array($res_dil)){
            $selected=($ing['solvent'] == $r_dil['name'])? "selected" : "";
            echo '<option '.$selected.' value="'.$r_dil['name'].'">'.$r_dil['name'].'</option>';
        }
        ?>
    </select>
  </div>
  
  <div class="mt-3 col-md-6">
  	<label for="profile" class="form-label">Profile</label>
   	<select name="profile" id="profile" class="form-control selectpicker" data-live-search="true">
  	  <option value="" selected></option>
     	 <?php while ($row_ingProfiles = mysqli_fetch_array($res_ingProfiles)){ ?>
         <option data-content="<img class='img_ing_sel' src='<?=profileImg($row_ingProfiles['name'])?>'> <?php echo $row_ingProfiles['name'];?>" value="<?php echo $row_ingProfiles['name'];?>" <?php echo ($ing['profile']==$row_ingProfiles['name'])?"selected=\"selected\"":""; ?>></option>
         <?php } ?>
    </select>
  </div>

  <div class="mt-3 col-md-6">
  	<label for="type" class="form-label">Type</label>
    <select name="type" id="type" class="form-control selectpicker" data-live-search="true">
        <option value="" selected></option>
        <?php 	while ($row_ingTypes = mysqli_fetch_array($res_ingTypes)){ ?>
            <option value="<?php echo $row_ingTypes['name'];?>" <?php echo ($ing['type']==$row_ingTypes['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingTypes['name'];?></option>
        <?php } ?>
    </select>
  </div>
  <div class="mt-3 col-md-6">
  	<label for="strength" class="form-label">Strength</label>
    <select name="strength" id="strength" class="form-control selectpicker" data-live-search="true">
    	<option value="" selected></option>
        <?php while ($row_ingStrength = mysqli_fetch_array($res_ingStrength)){ ?>
        <option value="<?php echo $row_ingStrength['name'];?>" <?php echo ($ing['strength']==$row_ingStrength['name'])?"selected=\"selected\"":""; ?>><?php echo $row_ingStrength['name'];?></option>
        <?php } ?>
    </select>
  </div>
  <div class="mt-3 col-md-6">
  	<label for="physical_state" class="form-label">Physical State</label>
    <select name="physical_state" id="physical_state" class="form-control selectpicker">
        <option data-content="<img class='img_ing_sel' src='/img/liquid.png'>Liquid" value="1" <?php if($ing['physical_state']=="1") echo 'selected="selected"'; ?> ></option>
        <option data-content="<img class='img_ing_sel' src='/img/solid.png'>Solid" value="2" <?php if($ing['physical_state']=="2") echo 'selected="selected"'; ?> ></option>
    </select>
  </div>
  
  <div class="mt-3 col-md-6">
  	<label for="category" class="form-label">Olfactive family</label>
    <select name="category" id="category" class="form-control selectpicker" data-live-search="true">
        <option value="" selected></option>
        <?php while ($row_ingCategory = mysqli_fetch_array($res_ingCategory)){ ?>
            <option data-content="<img class='img_ing_sel' src='<?php if($row_ingCategory['image']){ echo $row_ingCategory['image']; }else{ echo '/img/molecule.png';}?>'><?php echo $row_ingCategory['name'];?>" value="<?php echo $row_ingCategory['id'];?>" <?php echo ($ing['category']==$row_ingCategory['id'])?"selected=\"selected\"":""; ?>></option>
        <?php } ?>
    </select>  
  </div>
  <div class="form-floating mt-3 col-md-6">
	<input type="text" class="form-control" id="labelsinput" placeholder="Start typing to add a label..." data-role="labelsinput" value="<?= implode(",", $labelsData) ?>">
  </div>

  <div class="form-floating mt-3 col-12 position-relative">
    <textarea name="notes" id="notes" class="form-control" placeholder="Notes" style="height: 100px;"><?php echo $ing['notes']; ?></textarea>
    <label class="mx-2" for="notes">Notes</label>
    <button type="button" id="ai-notes-btn" class="btn btn-outline-secondary ai-border position-absolute" style="top:10px; right:10px; z-index:2;">
      <i class="fa fa-robot"></i> AI Fill
    </button>
  </div>
  <div class="col-sm dropdown-divider"></div>  
  <div class="mt-3 col-12">
    <button type="submit" name="save" class="btn btn-primary" id="saveGeneral">Save</button>
  </div>
</div>

        
        
<script>
$(document).ready(function() {
	$('[rel=tip]').tooltip({placement: 'auto'});

	$('#general').on('click', '[id*=saveGeneral]', function () {
		<?php if(empty($ing['id'])){ ?>
			if($.trim($("#name").val()) == ''){
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>Ingredient name is required');
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
				return;
   			}
		<?php } ?>
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				manage: 'ingredient',
				tab: 'general',
				ingID: myIngID,
				name: $("#name").val(),
				INCI: $("#INCI").val(),
				cas: $("#cas").val(),
				einecs: $("#einecs").val(),
				reach: $("#reach").val(),
				fema: $("#fema").val(),
				isAllergen: $("#isAllergen").is(':checked'),
				purity: $("#purity").val(),
				solvent: $("#solvent").val(),
				profile: $("#profile").val(),					
				type: $("#type").val(),
				strength: $("#strength").val(),
				category: $("#category").val(),
				physical_state: $("#physical_state").val(),
				notes: $("#notes").val(),
				<?php if($ing['name']){?>
					ing: '<?=$ing['name'];?>'
				<?php } ?>
			},
			dataType: 'json',   			
			success: function (data) {
				if(data.success){
					$('#mgmIngHeaderCAS').html($("#cas").val());
					$('#IUPAC').html($("#INCI").val());
					
					$('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
					$('.toast-header').removeClass().addClass('toast-header alert-success');
				}else{
					$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.error);
					$('.toast-header').removeClass().addClass('toast-header alert-danger');
				}
				$('.toast').toast('show');
						
				if ($('#name').val()) {
					window.location = '/pages/mgmIngredient.php?id=' + data.ingid;
				}
			    <?php if($ing['id']){ ?>
				reload_overview();
				<?php } ?>
			},
			error: function (xhr, status, error) {
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		});
	});

	
	$('.selectpicker').selectpicker();

	$('#purity').bind('input', function() {
		var purity = $(this).val();
		if(purity >= 100){
			$("#solvent").prop("disabled", true); 
			$("#solvent").val(''); 
		}else{
			$("#solvent").prop("disabled", false);
		}
		$('.selectpicker').selectpicker('refresh');
	});

	$('#labelsinput').on('beforeItemAdd', function(event) {
	   var label = event.item;   
	   $.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				action: "inglabeladd",
				id: myIngID,
				label: label
			},
			dataType: 'json',
			success: function (data) {
				if(data.error){
					$('#labelsinput').tagsinput('remove', label, {preventPost: true});
					//$('#set_msg').html(data.error);
				}
			},
			error: function (xhr, status, error) {
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		});
	});
	
	$('#labelsinput').val('<?= implode(",", $labelsData) ?>');
	$('#labelsinput').tagsinput('refresh');

	$('#labelsinput').on('beforeItemRemove', function(event) {
	var label = event.item;

	$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				action: "inglabelremove",
				id: myIngID,
				label: label
			},
			dataType: 'json',
			success: function (data) {
				if (data.error) {
					$('#labelsinput').tagsinput('add', label, { preventPost: true });
					$('#set_msg').html('<div class="alert alert-danger mx-2"><i class="fa-solid fa-circle-exclamation mx-2"></i> ' + data.error + '</div>');
				}
			},
			error: function (xhr, status, error) {
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
				$('.toast').toast('show');
			}
		});
	});

	$('#ai-notes-btn').on('click', function() {
    var ingName = $("#name").val() || "<?= htmlspecialchars($ing['name'] ?? '', ENT_QUOTES) ?>";
    if (!ingName) {
        $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>Enter the ingredient name first.');
        $('.toast-header').removeClass().addClass('toast-header alert-danger');
        $('.toast').toast('show');
        return;
    }
    var $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fa fa-robot fa-spin"></i> AI Fill');
    $.post('/core/core.php', { action: 'aiChat', message: "Get ingredient description for: " + ingName }, function(resp) {
        try {
            var parsed = JSON.parse(resp);
            if (parsed.success) {
                // Try to get the first description from the response
                var desc = '';
                if (parsed.success[0] && parsed.success[0].description) {
                    desc = parsed.success[0].description;
                } else if (parsed.success.description) {
                    desc = parsed.success.description;
                } else {
                    // fallback: try to find any description in the object
                    for (var k in parsed.success) {
                        if (parsed.success[k] && parsed.success[k].description) {
                            desc = parsed.success[k].description;
                            break;
                        }
                    }
                }
                if (desc) {
                    $('#notes').val(desc);
                } else {
                    $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>No description found in AI response.');
                    $('.toast-header').removeClass().addClass('toast-header alert-danger');
                    $('.toast').toast('show');
                }
            } else if (parsed.error) {
                $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>' + parsed.error);
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
                $('.toast').toast('show');
            }
        } catch (e) {
            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>AI response error.');
            $('.toast-header').removeClass().addClass('toast-header alert-danger');
            $('.toast').toast('show');
        }
        $btn.prop('disabled', false).html('<i class="fa fa-robot"></i> AI Fill');
    });
});

}); 

</script>

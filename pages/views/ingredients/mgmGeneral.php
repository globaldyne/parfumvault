<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/profileImg.php');


$ingID = mysqli_real_escape_string($conn, base64_decode($_GET["id"]));


$res_ingTypes = mysqli_query($conn, "SELECT id,name FROM ingTypes ORDER BY name ASC");
$res_ingStrength = mysqli_query($conn, "SELECT id,name FROM ingStrength ORDER BY name ASC");
$res_ingCategory = mysqli_query($conn, "SELECT id,image,name,notes FROM ingCategory ORDER BY name ASC");
$res_ingProfiles = mysqli_query($conn, "SELECT id,name FROM ingProfiles ORDER BY id ASC");

$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM ingredients WHERE name = '$ingID'"));

?>
<h3>General</h3>
<hr>

<div class="row g-3">
  
 <?php if(empty($ingID)){?>
 <div class="mt-3 col-12">
    <label for="name" class="form-label">Name</label>
    <input name="name" type="text" class="form-control" id="name" />
 </div>
 <?php } ?>
  
  <div class="mt-3 col-12">
    <label for="INCI" class="form-label">IUPAC</label>
    <input name="INCI" type="text" class="form-control" id="INCI" value="<?php echo htmlspecialchars($ing['INCI']); ?>" />
  </div>
  <div class="mt-3 col-md-6">
    <label for="cas" class="form-label">CAS</label><i class="fa-solid fa-circle-info mx-2 pv_point_gen" rel="tipsy" title="If your material contains multiple CAS, then use Mixture or Blend instead."></i>
    <input name="cas" type="text" class="form-control" id="cas" value="<?php echo $ing['cas']; ?>">
    
  </div>
  <div class="mt-3 col-md-6">
    <label for="einecs" class="form-label">EINECS</label>
    <input name="einecs" type="text" class="form-control" id="einecs" value="<?php echo $ing['einecs']; ?>" />
  </div>
  <div class="mt-3 col-md-6">
    <label for="reach" class="form-label">REACH</label>
    <input name="reach" type="text" class="form-control" id="reach" value="<?php echo $ing['reach']; ?>" />
  </div>

  <div class="mt-3 col-md-6">
    <label for="fema" class="form-label">FEMA</label>
    <input name="fema" type="text" class="form-control" id="fema" value="<?php echo $ing['FEMA']; ?>" />
  </div>
  
  <div class="mt-3 col-md-6">
    <label for="purity" class="form-label">Purity</label>
    <input name="purity" type="text" class="form-control" id="purity" value="<?php echo $ing['purity']?: '100'; ?>" />
  </div>
  <div class="mt-3 col-md-6">
    <label for="solvent" class="form-label">Solvent</label>
    <select name="solvent" id="solvent" class="form-control selectpicker" data-live-search="true" <?php if($ing['purity'] == 100){ ?>disabled<?php } ?> >
        <option value="" selected disabled>Solvent</option>
        <option value="None">None</option>
        <?php
        $res_dil = mysqli_query($conn, "SELECT id, name FROM ingredients WHERE type = 'Solvent' OR type = 'Carrier' ORDER BY name ASC");
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
  <div class="mt-3 col-md-6">
  	<label for="odor" class="form-label">Odor</label>
    <input name="odor" id="odor" type="text" class="form-control" value="<?php echo $ing['odor']; ?>"/>
  </div>
  <div class="mt-3 col-12">
      <label class="form-check-label" for="isAllergen" >To Declare</label>
      <input name="isAllergen" type="checkbox" id="isAllergen" value="1" <?php if($ing['allergen'] == '1'){; ?> checked="checked"  <?php } ?>/><i class="fa-solid fa-circle-info mx-2 pv_point_gen" rel="tipsy" title="If enabled, ingredient name will be printed in the box label."></i>
  </div>

  <div class="mt-3 col-12">
  	<label for="notes" class="form-label">Notes</label>
    <textarea name="notes" id="notes" cols="45" rows="3" class="form-control"><?php echo $ing['notes']; ?></textarea>
  </div>
  <div class="col-sm dropdown-divider"></div>  
  <div class="mt-3 col-12">
    <button type="submit" name="save" class="btn btn-primary" id="saveGeneral">Save</button>
  </div>
</div>

        
        
<script>
$(document).ready(function() {
	$('[rel=tipsy]').tooltip({placement: 'auto'});

	$('#general').on('click', '[id*=saveGeneral]', function () {
		<?php if(empty($ing['id'])){ ?>
			if($.trim($("#name").val()) == ''){
				$('#msg_general').html('<div class="alert alert-danger mx-2"><strong>Error:</strong>Name is required</div>');
				return;
   			}
		<?php } ?>
		$.ajax({ 
			url: 'update_data.php', 
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
				odor: $("#odor").val(),
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
					
					var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
				}else{
					var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				}
				
				$('#msg_general').html(msg);
				
				if ($('#name').val()) {
					window.location = 'mgmIngredient.php?id=' + btoa($('#name').val());
				}
			    <?php if($ing['id']){ ?>
				reload_overview();
				<?php } ?>
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
});//end doc
</script>
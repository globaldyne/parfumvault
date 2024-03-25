<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
$cats_q = mysqli_query($conn, "SELECT id,name,description,type FROM IFRACategories ORDER BY id ASC");

while($cats_res = mysqli_fetch_array($cats_q)){
    $cats[] = $cats_res;
}
?>
<div class="card-body row">
	<div class="col-sm-6">
      <div class="form-row">
        <div class="form-group col-md-6">
            <label for="currency">Currency</label>
            <input name="currency" type="text" class="form-control" id="currency" value="<?php echo utf8_encode($settings['currency']);?>"/>
        </div>
        <div class="form-group col-md-6">
          <label for="user_pref_eng">User preferences engine</label>
          <select name="user_pref_eng" id="user_pref_eng" class="form-control">
            <option value="1" <?php if($settings['user_pref_eng']=="1") echo 'selected="selected"'; ?> >PHP SESSION</option>
            <option value="2" <?php if($settings['user_pref_eng']=="2") echo 'selected="selected"'; ?> >DB Backend</option>
          </select>
        </div>
      </div>
      
      <div class="form-row">
          <div class="form-group col-md-6">
            <label for="grp_formula">Group formula</label>
            <select name="grp_formula" id="grp_formula" class="form-control">
              <option value="0" <?php if($settings['grp_formula']=="0") echo 'selected="selected"'; ?> >Plain</option>
              <option value="1" <?php if($settings['grp_formula']=="1") echo 'selected="selected"'; ?> >By notes</option>
              <option value="2" <?php if($settings['grp_formula']=="2") echo 'selected="selected"'; ?> >By category</option>
            </select>
          </div>
          <div class="form-group col-md-6">
          <label for="pubchem_view">PubChem view</label>
          <select name="pubchem_view" id="pubchem_view" class="form-control">
            <option value="2d" <?php if($settings['pubchem_view']=="2d") echo 'selected="selected"'; ?> >2D</option>
            <option value="3d" <?php if($settings['pubchem_view']=="3d") echo 'selected="selected"'; ?> >3D</option>
          </select>
          </div>
      </div>
      
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="qStep">Quantity Decimal</label>
          <select name="qStep" id="qStep" class="form-control">
            <option value="1" <?php if($settings['qStep']=="1") echo 'selected="selected"'; ?> >0.0</option>
            <option value="2" <?php if($settings['qStep']=="2") echo 'selected="selected"'; ?> >0.00</option>
            <option value="3" <?php if($settings['qStep']=="3") echo 'selected="selected"'; ?> >0.000</option>
            <option value="4" <?php if($settings['qStep']=="4") echo 'selected="selected"'; ?> >0.0000</option>
          </select>
        </div>
    
        <div class="form-group col-md-6">
          <label for="defCatClass">Default Category</label>
          <select name="defCatClass" id="defCatClass" class="form-control">
            <?php foreach ($cats as $IFRACategories) {?>
            <option value="cat<?php echo $IFRACategories['name'];?>" <?php echo ($settings['defCatClass']=='cat'.$IFRACategories['name'])?"selected=\"selected\"":""; ?>><?php echo 'Cat '.$IFRACategories['name'].' - '.$IFRACategories['description'];?>
            </option>
              <?php	}	?>
          </select>
        </div>
      </div>
    
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="mUnit">Measurement Unit</label>
          <select name="mUnit" id="mUnit" class="form-control">
            <option value="ml" <?php if($settings['mUnit']=="ml") echo 'selected="selected"'; ?> >Milliliter</option>
            <option value="g" <?php if($settings['mUnit']=="g") echo 'selected="selected"'; ?> >Grams</option>
            <option value="L" <?php if($settings['mUnit']=="L") echo 'selected="selected"'; ?> >Liter</option>
            <option value="fl. oz." <?php if($settings['mUnit']=="fl. oz.") echo 'selected="selected"'; ?> >Fluid ounce (fl. oz.)</option>
          </select>
        </div>
        <div class="form-group col-md-6">
          <label for="editor">Formula editor</label>
          <select name="editor" id="editor" class="form-control">
            <option value="1" <?php if($settings['editor']=="1") echo 'selected="selected"'; ?> >Standard</option>
            <option value="2" <?php if($settings['editor']=="2") echo 'selected="selected"'; ?> >Advanced</option>
          </select>
        </div>
        
        <div class="form-group col-md-6">
          <label for="pvHost">PV URL</label>
          <input name="pvHost" type="text" class="form-control" id="pvHost" value="<?php echo $settings['pv_host'];?>"/>
        </div>
        
      </div>
      

</div>
    
    <div class="col-sm-2">
     <h4 class="m-0 mb-4 text-primary">Pyramid View</h4>
     <div class="form-row">
        <div class="form-group col-md-auto">
          <label for="top_n" id="top">Top notes %</label>
          <input name="top_n" type="range" class="form-range" id="top_n" min="0" max="100" value="<?php echo $settings['top_n'];?>" />
        </div>
      </div>
      <div class="form-row">
          <div class="form-group  col-md-auto">
            <label for="heart_n" id="heart">Heart notes %</label>
            <input name="heart_n" type="range" class="form-range" id="heart_n" min="0" max="100" value="<?php echo $settings['heart_n'];?>"/>
          </div>
      </div>
      <div class="form-row">
          <div class="form-group col-md-auto">
            <label for="base_n" id="base">Base notes %</label>
            <input name="base_n" type="range" class="form-range" id="base_n" min="0" max="100" value="<?php echo $settings['base_n'];?>"/>
          </div>
      </div>
    </div>
    
    <div class="col-sm-3">
        <div class="form-group col-md-auto">
            <input name="pubChem" type="checkbox" id="pubChem" value="1" <?php if($settings['pubChem'] == '1'){ ?> checked="checked" <?php } ?>/>
            <label class="form-check-label" for="pubChem">Enable PubChem</label>
        </div>
        <div class="form-group col-md-auto">
            <input name="chkVersion" type="checkbox" id="chkVersion" value="1" <?php if($settings['chkVersion'] == '1'){ ?> checked="checked" <?php } ?>/>
            <label class="form-check-label" for="chkVersion">Check for updates</label>
        </div>
        <div class="form-group col-md-auto">
            <input name="chem_vs_brand" type="checkbox" id="chem_vs_brand" value="1" <?php if($settings['chem_vs_brand'] == '1'){ ?> checked="checked" <?php } ?>/>
            <label class="form-check-label" for="chem_vs_brand">Show chemical names</label>
        </div>
        
        <div class="form-group col-md-auto">
            <input name="multi_dim_perc" type="checkbox" id="multi_dim_perc" value="1" <?php if($settings['multi_dim_perc'] == '1'){ ?> checked="checked" <?php } ?>/>
            <label class="form-check-label" for="multi_dim_perc">Multi-dimensional lookup</label>
            <a href="#" class="fas fa-question-circle" rel="tip" title="Enable to include into formulas limits calculation the ingredient's sub materials if exists."></a>
        </div>
   </div>
     
    <div class="col dropdown-divider"></div>
    <div class="form-row">
      <div class="col-sm-12">
         <div class="text-left">
            <input type="submit" name="save-general" id="save-general" value="Save" class="btn btn-info"/>
         </div>
      </div>
    </div>
 </div>
</div>

<script>
$('#save-general').click(function() {
	$.ajax({ 
		url: '/pages/update_settings.php', 
		type: 'POST',
		data: {
			manage: 'general',
			currency: $("#currency").val(),
			top_n: $("#top_n").val(),
			heart_n: $("#heart_n").val(),
			base_n: $("#base_n").val(),
			qStep: $("#qStep").val(),
			defCatClass: $("#defCatClass").val(),
			pubchem_view: $("#pubchem_view").val(),
			grp_formula: $("#grp_formula").val(),
			chem_vs_brand: $("#chem_vs_brand").is(':checked'),
			pubChem: $("#pubChem").is(':checked'),
			chkVersion: $("#chkVersion").is(':checked'),
			multi_dim_perc: $("#multi_dim_perc").is(':checked'),
			mUnit: $("#mUnit").val(),
			editor: $("#editor").val(),
			user_pref_eng: $("#user_pref_eng").val(),
			pv_host: $("#pvHost").val()

	},
	dataType: 'json',
	success: function (data) {
		if(data.success){
			$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
			$('.toast-header').removeClass().addClass('toast-header alert-success');
		} else if(data.error) {
			$('#toast-title').html('<i class="fa-solid fa-warning mr-2"></i>' + data.error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
		}
		$('.toast').toast('show');
	}
  });
});

$('#top').text('Top notes ' + $('#top_n').val() + '%');
$('#heart').text('Heart notes ' + $('#heart_n').val() + '%');
$('#base').text('Base notes ' + $('#base_n').val() + '%');

$('#top_n').on('input', function(){
    $('#top').text('Top notes ' + $('#top_n').val() + '%');
});

$('#heart_n').on('input', function(){
    $('#heart').text('Heart notes ' + $('#heart_n').val() + '%');
});

$('#base_n').on('input', function(){
    $('#base').text('Base notes ' + $('#base_n').val() + '%');
});
</script>
<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/convertTime.php');

$session_validity_calc = convertTime($session_timeout);

$cats_q = mysqli_query($conn, "SELECT id,name,description,type FROM IFRACategories ORDER BY id ASC"); //PUBLIC

while($cats_res = mysqli_fetch_array($cats_q)){
    $cats[] = $cats_res;
}
?>
<div class="card-body row">
    <div class="col-sm-6">
        <div class="row">
            <div class="mb-3 col-md-6">
                <label for="currency" class="form-label">Currency</label>
                <select name="currency" id="currency" class="form-select">
                    <?php
                    $json = file_get_contents(__ROOT__.'/inc/currencies.json');
                    $currencies = json_decode($json, true);
                    foreach ($currencies as $code => $details) {
                        $symbol = $details['symbol'];
                        $selected = ($settings['currency_code'] == $code) ? 'selected' : '';
                        $name = $details['name'];
                        echo "<option value=\"$symbol|$code\" $selected>$name ($symbol) [$code]</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3 col-md-6">
                <label for="user_pref_eng" class="form-label">User preferences engine</label>
                <select name="user_pref_eng" id="user_pref_eng" class="form-select">
                    <option value="1" <?= $settings['user_pref_eng'] == "1" ? 'selected' : '' ?>>PHP SESSION</option>
                    <option value="2" <?= $settings['user_pref_eng'] == "2" ? 'selected' : '' ?>>DB Backend</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="mb-3 col-md-6">
                <label for="grp_formula" class="form-label">Group formula</label>
                <select name="grp_formula" id="grp_formula" class="form-select">
                    <option value="0" <?= $settings['grp_formula'] == "0" ? 'selected' : '' ?>>Plain</option>
                    <option value="1" <?= $settings['grp_formula'] == "1" ? 'selected' : '' ?>>By notes</option>
                    <option value="2" <?= $settings['grp_formula'] == "2" ? 'selected' : '' ?>>By category</option>
                </select>
            </div>
            <div class="mb-3 col-md-6">
                <label for="pubchem_view" class="form-label">PubChem view</label>
                <select name="pubchem_view" id="pubchem_view" class="form-select">
                    <option value="2d" <?= $settings['pubchem_view'] == "2d" ? 'selected' : '' ?>>2D</option>
                    <option value="3d" <?= $settings['pubchem_view'] == "3d" ? 'selected' : '' ?>>3D</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="mb-3 col-md-6">
                <label for="qStep" class="form-label">Quantity Decimal</label>
                <select name="qStep" id="qStep" class="form-select">
                    <option value="1" <?= $settings['qStep'] == "1" ? 'selected' : '' ?>>0.0</option>
                    <option value="2" <?= $settings['qStep'] == "2" ? 'selected' : '' ?>>0.00</option>
                    <option value="3" <?= $settings['qStep'] == "3" ? 'selected' : '' ?>>0.000</option>
                    <option value="4" <?= $settings['qStep'] == "4" ? 'selected' : '' ?>>0.0000</option>
                </select>
            </div>

            <div class="mb-3 col-md-6">
                <label for="defCatClass" class="form-label">Default Category</label>
                <select name="defCatClass" id="defCatClass" class="form-select">
                    <?php foreach ($cats as $IFRACategories) {?>
                    <option value="cat<?= htmlspecialchars($IFRACategories['name']) ?>" <?= $settings['defCatClass'] == 'cat' . $IFRACategories['name'] ? 'selected' : '' ?>>
                        <?= 'Cat ' . htmlspecialchars($IFRACategories['name']) . ' - ' . htmlspecialchars($IFRACategories['description']) ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="mb-3 col-md-6">
                <label for="mUnit" class="form-label">Measurement Unit</label>
                <select name="mUnit" id="mUnit" class="form-select">
                    <option value="ml" <?= $settings['mUnit'] == "ml" ? 'selected' : '' ?>>Milliliter</option>
                    <option value="g" <?= $settings['mUnit'] == "g" ? 'selected' : '' ?>>Grams</option>
                    <option value="L" <?= $settings['mUnit'] == "L" ? 'selected' : '' ?>>Liter</option>
                    <option value="fl. oz." <?= $settings['mUnit'] == "fl. oz." ? 'selected' : '' ?>>Fluid ounce (fl. oz.)</option>
                </select>
            </div>
            <div class="mb-3 col-md-6">
                <label for="editor" class="form-label">Formula editor</label>
                <select name="editor" id="editor" class="form-select">
                    <option value="1" <?= $settings['editor'] == "1" ? 'selected' : '' ?>>Standard</option>
                    <option value="2" <?= $settings['editor'] == "2" ? 'selected' : '' ?>>Advanced</option>
                </select>
            </div>

            <div class="mb-3 col-md-6">
                <label for="pvHost" class="form-label">PV URL</label>
                <input name="pvHost" type="text" class="form-control" id="pvHost" value="<?= htmlspecialchars($settings['pv_host']) ?>"/>
            </div>

            <div class="mb-3 col-md-6">
                <label for="defPercentage" class="form-label">Calculate sub materials as</label>
                <select name="defPercentage" id="defPercentage" class="form-select">
                    <option value="min_percentage" <?= $settings['defPercentage'] == "min_percentage" ? 'selected' : '' ?>>Minimum value</option>
                    <option value="max_percentage" <?= $settings['defPercentage'] == "max_percentage" ? 'selected' : '' ?>>Maximum value</option>
                    <!-- <option value="avg_percentage" <?= $settings['defPercentage'] == "avg_percentage" ? 'selected' : '' ?>>Average value</option> -->
                </select>
            </div>

            <div class="mb-3 col-md-6">
                <label for="bs_theme" class="form-label">Theme</label>
                <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="A page refresh may be required for the changes to take effect fully"></a>
                <select name="bs_theme" id="bs_theme" class="form-select">
                    <option value="light" <?= $settings['bs_theme'] == "light" ? 'selected' : '' ?>>Light</option>
                    <option value="dark" <?= $settings['bs_theme'] == "dark" ? 'selected' : '' ?>>Dark</option>
                </select>
            </div>
            
            <div class="mb-3 col-md-6">
               <label for="temp_sys" class="form-label">Temprature unit</label>
               <select name="temp_sys" id="temp_sys" class="form-select">
                   <option value="°C" <?= $settings['temp_sys'] == "°C" ? 'selected' : '' ?>>Celsius (°C)</option>
                   <option value="°F" <?= $settings['temp_sys'] == "°F" ? 'selected' : '' ?>>Fahrenheit (°F)</option>
                   <option value="K" <?= $settings['temp_sys'] == "K" ? 'selected' : '' ?>>Kelvin (K)</option>
               </select>
            </div>
            
        </div>
    </div>

    <div class="col-sm-2">
        <h4 class="m-0 mb-4">Pyramid View</h4>
        <div class="mb-3">
            <label for="top_n" class="form-label" id="top">Top notes %</label>
            <input name="top_n" type="range" class="form-range" id="top_n" min="0" max="100" value="<?= htmlspecialchars($settings['top_n']) ?>" />
        </div>
        <div class="mb-3">
            <label for="heart_n" class="form-label" id="heart">Heart notes %</label>
            <input name="heart_n" type="range" class="form-range" id="heart_n" min="0" max="100" value="<?= htmlspecialchars($settings['heart_n']) ?>"/>
        </div>
        <div class="mb-3">
            <label for="base_n" class="form-label" id="base">Base notes %</label>
            <input name="base_n" type="range" class="form-range" id="base_n" min="0" max="100" value="<?= htmlspecialchars($settings['base_n']) ?>"/>
        </div>
    </div>

    <div class="col-sm-3">
        <div class="form-check mb-3">
            <input name="pubChem" type="checkbox" class="form-check-input" id="pubChem" value="1" <?= $settings['pubChem'] == '1' ? 'checked' : '' ?>/>
            <label class="form-check-label" for="pubChem">Enable PubChem</label>
        </div>
        
        <div class="form-check mb-3">
          <?php if (isset($disable_updates) && $disable_updates == 'true') { ?>
              <input name="chkVersion" type="checkbox" disabled class="form-check-input" id="chkVersion" />
              <label class="form-check-label" for="chkVersion">Check for updates (Disabled)</label>
          <?php } else { ?>
              <input name="chkVersion" type="checkbox" class="form-check-input" id="chkVersion" value="1" <?= isset($settings['chkVersion']) && $settings['chkVersion'] == '1' ? 'checked' : '' ?>/>
              <label class="form-check-label" for="chkVersion">Check for updates</label>
          <?php } ?>
        </div>
        
        <div class="form-check mb-3">
            <input name="chem_vs_brand" type="checkbox" class="form-check-input" id="chem_vs_brand" value="1" <?= $settings['chem_vs_brand'] == '1' ? 'checked' : '' ?>/>
            <label class="form-check-label" for="chem_vs_brand">Show chemical names</label>
        </div>

        <div class="form-check mb-3">
            <input name="multi_dim_perc" type="checkbox" class="form-check-input" id="multi_dim_perc" value="1" <?= $settings['multi_dim_perc'] == '1' ? 'checked' : '' ?>/>
            <label class="form-check-label" for="multi_dim_perc">Multi-dimensional lookup</label>
            <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Enable to include into formulas limits calculation the ingredient's sub materials if exists."></a>
        </div>
       <hr />
       <div class="col-sm-auto">
			User session validity: <?php echo "Hours: " . $session_validity_calc['hours'] . ", Minutes: " . $session_validity_calc['minutes']; ?>
            <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Please refer to the KB article how to modify this if needed"></a>

       </div>
    </div>


    <div class="row">
        <div class="col-sm-12 text-start">
            <input type="submit" name="save-general" id="save-general" value="Save" class="btn btn-primary"/>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {

	$('[data-bs-toggle=tooltip]').tooltip();
	$('#save-general').click(function() {
		var selectedCurrency = $("#currency").val();
        var currencyData = selectedCurrency.split('|');
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				manage: 'general',
				currency: currencyData[0], // Symbol
            	currency_code: currencyData[1], // Code
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
				pv_host: $("#pvHost").val(),
				defPercentage: $("#defPercentage").val(),
				bs_theme: $("#bs_theme").val(),
				temp_sys: $("#temp_sys").val()
	
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
				$('.toast-header').removeClass().addClass('toast-header alert-success');
				document.documentElement.setAttribute('data-bs-theme',$("#bs_theme").val())
			} else if(data.error) {
				$('#toast-title').html('<i class="fa-solid fa-warning mr-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
			}
			$('.toast').toast('show');
		},
		error: function (xhr, status, error) {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error);
			$('.toast-header').removeClass().addClass('toast-header alert-danger');
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
	
});

</script>

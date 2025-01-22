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
            <div class="mb-3 col-md-6 form-floating">
                <select name="currency" id="currency" class="form-select">
                    <?php
                    $json = file_get_contents(__ROOT__.'/inc/currencies.json');
                    $currencies = json_decode($json, true);
                    foreach ($currencies as $code => $details) {
                        $symbol = $details['symbol'];
                        $selected = ($user_settings ['currency_code'] == $code) ? 'selected' : '';
                        $name = $details['name'];
                        echo "<option value=\"$symbol|$code\" $selected>$name ($symbol) [$code]</option>";
                    }
                    ?>
                </select>
                <label for="currency" class="form-label">Currency</label>
            </div>
            <div class="mb-3 col-md-6 form-floating">
                <select name="user_pref_eng" id="user_pref_eng" class="form-select">
                    <option value="1" <?= $user_settings ['user_pref_eng'] == "1" ? 'selected' : '' ?>>PHP SESSION</option>
                    <option value="2" <?= $user_settings ['user_pref_eng'] == "2" ? 'selected' : '' ?>>DB Backend</option>
                </select>
                <label for="user_pref_eng" class="form-label">User preferences engine</label>
            </div>
        </div>

        <div class="row">
            <div class="mb-3 col-md-6 form-floating">
                <select name="grp_formula" id="grp_formula" class="form-select">
                    <option value="0" <?= $user_settings ['grp_formula'] == "0" ? 'selected' : '' ?>>Plain</option>
                    <option value="1" <?= $user_settings ['grp_formula'] == "1" ? 'selected' : '' ?>>By notes</option>
                    <option value="2" <?= $user_settings ['grp_formula'] == "2" ? 'selected' : '' ?>>By category</option>
                </select>
                <label for="grp_formula" class="form-label">Group formula</label>
            </div>
            <div class="mb-3 col-md-6 form-floating">
                <?php if ($system_settings['SYSTEM_pubChem'] == '1') { ?>
                <select name="pubchem_view" id="pubchem_view" class="form-select">
                    <option value="2d" <?= $user_settings ['pubchem_view'] == "2d" ? 'selected' : '' ?>>2D</option>
                    <option value="3d" <?= $user_settings ['pubchem_view'] == "3d" ? 'selected' : '' ?>>3D</option>
                </select>
                <?php } else { ?>
                <select name="pubchem_view" id="pubchem_view" class="form-select" disabled>
                    <option value="">Disabled by admin</option>
                </select>
                <?php } ?>
                <label for="pubchem_view" class="form-label">PubChem view</label>
            </div>
        </div>

        <div class="row">
            <div class="mb-3 col-md-6 form-floating">
                <select name="qStep" id="qStep" class="form-select">
                    <option value="1" <?= $user_settings ['qStep'] == "1" ? 'selected' : '' ?>>0.0</option>
                    <option value="2" <?= $user_settings ['qStep'] == "2" ? 'selected' : '' ?>>0.00</option>
                    <option value="3" <?= $user_settings ['qStep'] == "3" ? 'selected' : '' ?>>0.000</option>
                    <option value="4" <?= $user_settings ['qStep'] == "4" ? 'selected' : '' ?>>0.0000</option>
                </select>
                <label for="qStep" class="form-label">Quantity Decimal</label>
            </div>

            <div class="mb-3 col-md-6 form-floating">
                <select name="defCatClass" id="defCatClass" class="form-select">
                    <?php foreach ($cats as $IFRACategories) {?>
                    <option value="cat<?= htmlspecialchars($IFRACategories['name']) ?>" <?= $user_settings ['defCatClass'] == 'cat' . $IFRACategories['name'] ? 'selected' : '' ?>>
                        <?= 'Cat ' . htmlspecialchars($IFRACategories['name']) . ' - ' . htmlspecialchars($IFRACategories['description']) ?>
                    </option>
                    <?php } ?>
                </select>
                <label for="defCatClass" class="form-label">Default Category</label>
            </div>
        </div>

        <div class="row">
            <div class="mb-3 col-md-6 form-floating">
                <select name="mUnit" id="mUnit" class="form-select">
                    <option value="ml" <?= $user_settings ['mUnit'] == "ml" ? 'selected' : '' ?>>Milliliter</option>
                    <option value="g" <?= $user_settings ['mUnit'] == "g" ? 'selected' : '' ?>>Grams</option>
                    <option value="L" <?= $user_settings ['mUnit'] == "L" ? 'selected' : '' ?>>Liter</option>
                    <option value="fl. oz." <?= $user_settings ['mUnit'] == "fl. oz." ? 'selected' : '' ?>>Fluid ounce (fl. oz.)</option>
                </select>
                <label for="mUnit" class="form-label">Measurement Unit</label>
            </div>
            <div class="mb-3 col-md-6 form-floating">
                <select name="editor" id="editor" class="form-select">
                    <option value="1" <?= $user_settings ['editor'] == "1" ? 'selected' : '' ?>>Standard</option>
                    <option value="2" <?= $user_settings ['editor'] == "2" ? 'selected' : '' ?>>Advanced</option>
                </select>
                <label for="editor" class="form-label">Formula editor</label>
            </div>

            <div class="mb-3 col-md-6 form-floating">
                <select name="defPercentage" id="defPercentage" class="form-select">
                    <option value="min_percentage" <?= $user_settings ['defPercentage'] == "min_percentage" ? 'selected' : '' ?>>Minimum value</option>
                    <option value="max_percentage" <?= $user_settings ['defPercentage'] == "max_percentage" ? 'selected' : '' ?>>Maximum value</option>
                    <!-- <option value="avg_percentage" <?= $user_settings ['defPercentage'] == "avg_percentage" ? 'selected' : '' ?>>Average value</option> -->
                </select>
                <label for="defPercentage" class="form-label">Calculate sub materials as</label>
            </div>

            <div class="mb-3 col-md-6 form-floating">
                <select name="bs_theme" id="bs_theme" class="form-select">
                    <option value="light" <?= $user_settings ['bs_theme'] == "light" ? 'selected' : '' ?>>Light</option>
                    <option value="dark" <?= $user_settings ['bs_theme'] == "dark" ? 'selected' : '' ?>>Dark</option>
                </select>
                <label for="bs_theme" class="form-label">Theme</label>
            </div>
            
            <div class="mb-3 col-md-6 form-floating">
               <select name="temp_sys" id="temp_sys" class="form-select">
                   <option value="°C" <?= $user_settings ['temp_sys'] == "°C" ? 'selected' : '' ?>>Celsius (°C)</option>
                   <option value="°F" <?= $user_settings ['temp_sys'] == "°F" ? 'selected' : '' ?>>Fahrenheit (°F)</option>
                   <option value="K" <?= $user_settings ['temp_sys'] == "K" ? 'selected' : '' ?>>Kelvin (K)</option>
               </select>
               <label for="temp_sys" class="form-label">Temperature unit</label>
            </div>
            
        </div>
    </div>

    <div class="col-sm-2">
        <h4 class="m-0 mb-4">Pyramid View</h4>
        <div class="mb-3">
            <label for="top_n" class="form-label" id="top">Top notes %</label>
            <input name="top_n" type="range" class="form-range" id="top_n" min="0" max="100" value="<?= htmlspecialchars($user_settings ['top_n']) ?>" />
        </div>
        <div class="mb-3">
            <label for="heart_n" class="form-label" id="heart">Heart notes %</label>
            <input name="heart_n" type="range" class="form-range" id="heart_n" min="0" max="100" value="<?= htmlspecialchars($user_settings ['heart_n']) ?>"/>
        </div>
        <div class="mb-3">
            <label for="base_n" class="form-label" id="base">Base notes %</label>
            <input name="base_n" type="range" class="form-range" id="base_n" min="0" max="100" value="<?= htmlspecialchars($user_settings ['base_n']) ?>"/>
        </div>
    </div>

    <div class="col-sm-3">       
        <div class="form-check mb-3">
            <input name="chem_vs_brand" type="checkbox" class="form-check-input" id="chem_vs_brand" value="1" <?= $user_settings ['chem_vs_brand'] == '1' ? 'checked' : '' ?>/>
            <label class="form-check-label" for="chem_vs_brand">Show chemical names</label>
        </div>

        <div class="form-check mb-3">
            <input name="multi_dim_perc" type="checkbox" class="form-check-input" id="multi_dim_perc" value="1" <?= $user_settings ['multi_dim_perc'] == '1' ? 'checked' : '' ?>/>
            <label class="form-check-label" for="multi_dim_perc">Multi-dimensional lookup</label>
            <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Enable to include into formulas limits calculation the ingredient's sub materials if exists."></a>
        </div>
       <hr />
       <div class="col-sm-auto">
            <a href="#" data-bs-toggle="modal" data-bs-target="#clear_search_pref">Clear search preferences</a>
       </div>
       <div class="col-sm-auto">
            <a href="#" data-bs-toggle="modal" data-bs-target="#clear_my_settings">Reset all settings to default</a>
       </div>
       <?php if ($role === 1){ ?>
       <hr />
       <div class="col-sm-auto">
            User session validity: <?php echo "Hours: " . $session_validity_calc['hours'] . ", Minutes: " . $session_validity_calc['minutes']; ?>
            <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Please refer to the KB article how to modify this if needed"></a>
       </div>
       <?php } ?>
    </div>


    <div class="row">
        <div class="col-sm-12 text-start">
            <input type="submit" name="save-general" id="save-general" value="Save" class="btn btn-primary"/>
        </div>
    </div>
</div>


<div class="modal fade" id="clear_search_pref" tabindex="-1" aria-labelledby="clearSearchPrefLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearSearchPrefLabel">Clear Search Preferences</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to clear your search preferences? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-clear-search-pref">Clear Preferences</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="clear_my_settings" tabindex="-1" aria-labelledby="clearMySettingsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearMySettingsLabel">Reset All Settings to Default</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to reset all settings to default? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-clear-my-settings">Reset Settings</button>
            </div>
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
				action: 'update_user_settings',
				currency: currencyData[0], // Symbol
            	currency_code: currencyData[1], // Code
				top_n: $("#top_n").val(),
				heart_n: $("#heart_n").val(),
				base_n: $("#base_n").val(),
				qStep: $("#qStep").val(),
				defCatClass: $("#defCatClass").val(),
                <?php if($system_settings['SYSTEM_pubChem'] == '1'){ ?>
				pubchem_view: $("#pubchem_view").val(),
                <?php } ?>
				grp_formula: $("#grp_formula").val(),
                chem_vs_brand: $("#chem_vs_brand").is(':checked') ? 1 : 0,
                multi_dim_perc: $("#multi_dim_perc").is(':checked') ? 1 : 0,
				mUnit: $("#mUnit").val(),
				editor: $("#editor").val(),
				user_pref_eng: $("#user_pref_eng").val(),
				defPercentage: $("#defPercentage").val(),
				bs_theme: $("#bs_theme").val(),
				temp_sys: $("#temp_sys").val()
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
				$('.toast-header').removeClass().addClass('toast-header alert-success');
				document.documentElement.setAttribute('data-bs-theme',$("#bs_theme").val())
			} else if(data.error) {
				$('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
			}
			$('.toast').toast('show');
		},
		error: function (xhr, status, error) {
			$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error);
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

$('#confirm-clear-search-pref').click(function() {
    $.ajax({
        url: '/core/core.php',
        type: 'GET',
        data: {
            action: 'userPerfClear'
        },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                $('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
                $('.toast-header').removeClass().addClass('toast-header alert-success');
            } else if (data.error) {
                $('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
            }
            $('.toast').toast('show');
            $('#clear_search_pref').modal('hide');
        },
        error: function(xhr, status, error) {
            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. ' + error);
            $('.toast-header').removeClass().addClass('toast-header alert-danger');
            $('.toast').toast('show');
            $('#clear_search_pref').modal('hide');
        }
    });
});


$('#confirm-clear-my-settings').click(function() {
    $.ajax({
        url: '/core/core.php',
        type: 'GET',
        data: {
            action: 'reset_user_settings'
        },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                $('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
                $('.toast-header').removeClass().addClass('toast-header alert-success');
                get_general();
            } else if (data.error) {
                $('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
            }
            $('.toast').toast('show');
            $('#clear_my_settings').modal('hide');
        },
        error: function(xhr, status, error) {
            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. ' + error);
            $('.toast-header').removeClass().addClass('toast-header alert-danger');
            $('.toast').toast('show');
            $('#clear_my_settings').modal('hide');
        }
    });
});

function get_general(){
    $.ajax({ 
        url: '/pages/views/settings/general.php', 
        dataType: 'html',
        success: function (data) {
            $('#general').html(data);
        }
    });
};

</script>

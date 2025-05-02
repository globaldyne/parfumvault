<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

?>
<div class="card-body row">
    <div class="row">
        <div class="col-sm-6" id="openai-settings">
            <div class="row">
                <div class="col-sm-6">       
                    <div class="form-check mb-3">
                        <input name="use_ai_service" type="checkbox" class="form-check-input" id="use_ai_service" value="true" <?= $user_settings ['use_ai_service'] == '1' ? 'checked' : '' ?>/>
                        <label class="form-check-label" for="use_ai_service">Use OpenAI</label>
                        <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Generate AI based formulas"></a>
                    </div>

                    <div class="mb-3">
                        <label for="openai_api_key" class="form-label">API Key</label>
                        <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Enter your OpenAI key here"></a>
                        <input name="openai_api_key" type="text" class="form-control" id="openai_api_key" value="<?= $user_settings['openai_api_key'] ?>" />
                    </div>

                    <div class="mb-3">
                        <label for="openai_model" class="form-label">Model</label>
                        <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Select the OpenAI model to use"></a>
                        <select name="openai_model" id="openai_model" class="form-control">
                            <option value="gpt-4" <?= $user_settings['openai_model'] == 'gpt-4' ? 'selected' : '' ?>>gpt-4</option>
                            <option value="gpt-4-32k" <?= $user_settings['openai_model'] == 'gpt-4-32k' ? 'selected' : '' ?>>gpt-4-32k</option>
                            <option value="gpt-3.5-turbo" <?= $user_settings['openai_model'] == 'gpt-3.5-turbo' ? 'selected' : '' ?>>gpt-3.5-turbo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="openai_temperature" class="form-label">Temperature</label>
                        <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Set the temperature for the AI model (0.0 to 1.0)"></a>
                        <input type="range" name="openai_temperature" id="openai_temperature" class="form-range" min="0" max="1" step="0.1" value="<?= $user_settings['openai_temperature'] ?>" />
                        <div class="form-text">Current value: <span id="temperature_value"><?= $user_settings['openai_temperature'] ?></span></div>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="col-sm-6" id="openai-help">
            <h5>OpenAI Settings</h5>
            <div class="mb-2">OpenAI is a powerful AI service that can generate formulas based on your input. You can use it to create complex formulas quickly and easily.</div>
            <div class="mb-2">To use OpenAI, you need to have an API key. You can get one by signing up for an account on the OpenAI website.</div>
            <div class="mb-2">Once you have your API key, enter it in the field above and check the "Use OpenAI" box to enable the service.</div>
            <div class="mb-2">Note: OpenAI is a paid service, and you will be charged for the API calls made by the system. Make sure to monitor your usage to avoid unexpected charges.</div>
            <hr />
            <h5>OpenAI Models</h5>
            <div class="mb-2">The model determines the AI's capabilities and performance:</div>
            <ul>
                <li><strong>gpt-4:</strong> The most advanced model, capable of handling complex tasks with high accuracy.</li>
                <li><strong>gpt-4-32k:</strong> Similar to gpt-4 but with a larger context window for handling longer inputs.</li>
                <li><strong>gpt-3.5-turbo:</strong> A faster and cheaper alternative to gpt-4, suitable for most tasks.</li>
            </ul>
            <hr />
            <h5>Temperature</h5>
            <div class="mb-2">The temperature controls the randomness of the AI's responses:</div>
            <ul>
                <li><strong>0.0:</strong> The AI will produce deterministic and focused outputs.</li>
                <li><strong>1.0:</strong> The AI will produce more creative and diverse outputs.</li>
            </ul>
            <div class="mb-2">Adjust the temperature based on your needs. Lower values are better for precise tasks, while higher values are better for creative tasks.</div>
            <hr />
            <h5>OpenAI API Key</h5>
            <div class="mb-2">Your OpenAI API key is used to authenticate your requests to the OpenAI service. Make sure to keep it secure and do not share it with anyone.</div>
            <div class="mb-2">To regenerate your API key, go to the <a href="https://platform.openai.com/account/api-keys" target="_blank" class="link-info">OpenAI dashboard</a>, click on "API Keys", and then click on "Regenerate" next to your key.</div>
            <div class="mb-2">Note: Regenerating your API key will invalidate your old key, so make sure to update it in all the places where you are using it.</div>
        </div>
    </div>
    <div class="row">
        <hr />
        <div class="col-sm-auto text-start">
            <button type="submit" name="save-openai" id="save-openai" value="Save" class="btn btn-primary btn-lg">Save</button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {

	$('[data-bs-toggle=tooltip]').tooltip();
	$('#save-openai').click(function() {
        // Validate openai_api_key is not empty
        if (!$("#openai_api_key").val().trim()) {
            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>API Key cannot be empty.');
            $('.toast-header').removeClass().addClass('toast-header alert-danger');
            $('.toast').toast('show');
            return;
        }

		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				action: 'update_openai_settings',
                openai_api_key: $("#openai_api_key").val(),
                use_ai_service: $("#use_ai_service").is(':checked') ? true : false,
                openai_model: $("#openai_model").val(),
                openai_temperature: $("#openai_temperature").val()
    		},
    		dataType: 'json',
	    	success: function (data) {
		    	if(data.success){
			    	$('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
				    $('.toast-header').removeClass().addClass('toast-header alert-success');
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
	
    // Disable openai_api_key if use_ai_service is unchecked
    $('#use_ai_service').change(function() {
        $('#openai_api_key').prop('disabled', !$(this).is(':checked'));
    }).trigger('change'); // Trigger change on page load to set initial state

    // Update temperature value display
    $('#openai_temperature').on('input', function() {
        $('#temperature_value').text($(this).val());
    });
	
});


</script>

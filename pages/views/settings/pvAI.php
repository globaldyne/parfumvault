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
                        <label class="form-check-label" for="use_ai_service">Enable Perfumers AI</label>
                        <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Generate AI based formulas"></a>
                    </div>
                    <div class="form-check mb-3">
                        <input name="use_ai_chat" type="checkbox" class="form-check-input" id="use_ai_chat" value="true" <?= $user_settings ['use_ai_chat'] == '1' ? 'checked' : '' ?>/>
                        <label class="form-check-label" for="use_ai_chat">Enable Perfumers AI Chat</label>
                        <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Use Perfumers AI Chat to help you during formulations or request ingredients info"></a>
                    </div>
                    <div class="form-check mb-3 ms-4">
                        <input name="making_ai_chat" type="checkbox" class="form-check-input" id="making_ai_chat" value="true" <?= $user_settings['making_ai_chat'] == '1' ? 'checked' : '' ?> <?= $user_settings['use_ai_chat'] != '1' ? 'disabled' : '' ?>/>
                        <label class="form-check-label" for="making_ai_chat">Show in making</label>
                        <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Display the chatbox in the formula-making page"></a>
                    </div>
                    <hr />
                    <div class="mb-3">
                        <label for="ai_service_provider" class="form-label">AI Service Provider</label>
                        <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Select the AI service provider"></a>
                        <select name="ai_service_provider" id="ai_service_provider" class="form-control">
                            <option value="openai" <?= $user_settings['ai_service_provider'] == 'openai' ? 'selected' : '' ?>>OpenAI</option>
                            <option value="google_gemini" <?= $user_settings['ai_service_provider'] == 'google_gemini' ? 'selected' : '' ?>>Google Gemini</option>
                            <option value="pedro_perfumer" <?= $user_settings['ai_service_provider'] == 'pedro_perfumer' ? 'selected' : '' ?>>Pedro Perfumer</option>
                        </select>
                    </div>

                    <div id="openai-fields" class="provider-fields">
                        <div class="mb-3">
                            <label for="openai_api_key" class="form-label">OpenAI API Key</label>
                            <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Enter your OpenAI key here"></a>
                            <input name="openai_api_key" type="text" class="form-control" id="openai_api_key" value="<?= $user_settings['openai_api_key'] ?>" />
                        </div>
                        <div class="mb-3">
                            <label for="openai_model" class="form-label">Model</label>
                            <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Select the OpenAI model to use"></a>
                            <select name="openai_model" id="openai_model" class="form-control">
                                <option value="gpt-4.1" <?= $user_settings['openai_model'] == 'gpt-4.1' ? 'selected' : '' ?>>gpt-4.1</option>
                                <option value="o4-mini" <?= $user_settings['openai_model'] == 'o4-mini' ? 'selected' : '' ?>>o4-mini</option>
                                <option value="o3" <?= $user_settings['openai_model'] == 'o3' ? 'selected' : '' ?>>o3</option>
                                <option value="gpt-3.5-turbo" <?= $user_settings['openai_model'] == 'gpt-3.5-turbo' ? 'selected' : '' ?>>gpt-3.5-turbo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="openai_temperature" class="form-label">Temperature</label>
                            <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Set the temperature for the AI model (0.0 to 1.0)"></a>
                            <input type="range" name="openai_temperature" id="openai_temperature" class="form-range" min="0" max="1" step="0.1" value="<?= $user_settings['openai_temperature'] ?: 0.5 ?>" />
                            <div class="form-text">Current value: <span id="temperature_value"><?= $user_settings['openai_temperature'] ?: 0.5 ?></span></div>
                        </div>
                    </div>

                    <div id="google-gemini-fields" class="provider-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="google_gemini_api_key" class="form-label">Google Gemini API Key</label>
                            <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Enter your Google Gemini API key here"></a>
                            <input name="google_gemini_api_key" type="text" class="form-control" id="google_gemini_api_key" value="<?= $user_settings['google_gemini_api_key'] ?>" />
                        </div>
                        <div class="mb-3">
                            <label for="google_gemini_model" class="form-label">Model</label>
                            <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Select the Google Gemini model to use"></a>
                            <select name="google_gemini_model" id="google_gemini_model" class="form-control">
                                <option value="gemini-2.0-flash-lite" <?= $user_settings['google_gemini_model'] == 'gemini-2.0-flash-lite' ? 'selected' : '' ?>>Gemini 2.0 Flash-Lite</option>
                                <option value="gemini-2.0-flash" <?= $user_settings['google_gemini_model'] == 'gemini-2.0-flash' ? 'selected' : '' ?>>Gemini 2.0 Flash</option>
                                <option value="gemini-1.5-pro" <?= $user_settings['google_gemini_model'] == 'gemini-1.5-pro' ? 'selected' : '' ?>>Gemini 1.5 Pro</option>
                            </select>
                        </div>
                    </div>

                    <div id="pedro-perfumer-fields" class="provider-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="pedro_perfumer_api_key" class="form-label">Pedro Perfumer API Key</label>
                            <a href="#" class="ms-2 fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Enter your Pedro Perfumer API key here"></a>
                            <input name="pedro_perfumer_api_key" type="text" class="form-control" id="pedro_perfumer_api_key" value="<?= $user_settings['pedro_perfumer_api_key'] ?? '' ?>" />
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="col-sm-6" id="provider-help">
            <div id="openai-help" class="provider-help">
                <h5>OpenAI Settings</h5>
                <div class="mb-2">OpenAI is a powerful AI service that can generate formulas based on your input. You can use it to create complex formulas quickly and easily.</div>
                <div class="mb-2">To use OpenAI, you need to have an API key. You can get one by signing up for an account on the OpenAI website.</div>
                <div class="mb-2">Once you have your API key, enter it in the field above and check the "Use OpenAI" box to enable the service.</div>
                <div class="mb-2">Note: OpenAI is a paid service, and you will be charged for the API calls made by the system. Make sure to monitor your usage to avoid unexpected charges.</div>
                <hr />
                <h5>OpenAI Models</h5>
                <div class="mb-2">The model determines the AI's capabilities and performance:</div>
                <ul>
                    <li><strong>gpt-4.1:</strong> The most advanced model, capable of handling complex tasks with high accuracy.</li>
                    <li><strong>o4-mini:</strong> A smaller version of gpt-4, suitable for less demanding tasks.</li>
                    <li><strong>o3</strong> is a well-rounded and powerful model across domains. It sets a new standard for math, science, coding, and visual reasoning tasks.</li>                
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
            <div id="google-gemini-help" class="provider-help" style="display: none;">
                <h5>Google Gemini Settings</h5>
                <div class="mb-2">Google Gemini is an advanced AI service that provides cutting-edge capabilities for generating formulas and other tasks.</div>
                <div class="mb-2">To use Google Gemini, you need an API key. You can obtain one by signing up on the Google Cloud Platform.</div>
                <div class="mb-2">Once you have your API key, enter it in the field above and select the appropriate model to enable the service.</div>
                <div class="mb-2">To generate an API key, visit the <a href="https://ai.google.dev/gemini-api/docs/api-key" target="_blank" class="link-info">Google AI Studio API Credentials</a> page.</div>
                <div class="mb-2">Note: Google Gemini is a paid service, and you will be charged for API calls. Monitor your usage to avoid unexpected charges.</div>
                <hr />
                <h5>Google Gemini Models</h5>
                <div class="mb-2">The model determines the AI's capabilities and performance:</div>
                <ul>
                    <li><strong>Gemini 2.0 Flash:</strong> A high-performance model optimized for speed and accuracy, suitable for complex tasks requiring quick responses.</li>
                    <li><strong>Gemini 2.0 Flash Lite:</strong> A lightweight version of Gemini 2.0 Flash, designed for less demanding tasks while maintaining good performance.</li>
                    <li><strong>Gemini 1.5 Pro:</strong> A robust model offering balanced performance and versatility, ideal for a wide range of applications.</li>
                </ul>
            </div>
            <div id="pedro-perfumer-help" class="provider-help" style="display: none;">
                <h5>Pedro Perfumer Settings</h5>
                <div class="mb-2">
                    Pedro Perfumer is an AI service specialized in perfumery. It focuses on perfume formulas, ingredients, and suggestions for creating or improving fragrances.
                    Enter your Pedro Perfumer API key to enable tailored assistance for all your perfumery formulation needs.
                    <br>
                    <span class="text-warning fw-semibold d-block mt-2">
                        Note: This service is currently <strong>experimental</strong> and comes without any warranty.
                    </span>
                    <span class="text-danger d-block mt-1" style="font-size: 0.95em;">
                        Important: All user prompts and interactions with Pedro Perfumer may be used for model training and service improvement.
                    </span>
                    <button id="pedro-create-api-key-btn" type="button" class="btn btn-outline-info btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#pedroApiKeyModal">
                        Create Pedro Perfumer API Key
                    </button>
                </div>
            </div>

            <!-- Modal for Pedro API Key creation -->
            <div class="modal fade" id="pedroApiKeyModal" tabindex="-1" aria-labelledby="pedroApiKeyModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form id="pedro-api-key-form" autocomplete="off">
                    <div class="modal-header">
                      <h5 class="modal-title" id="pedroApiKeyModalLabel">Create Pedro Perfumer API Key</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="pedro-user-email" name="user" placeholder="user@example.com" required>
                        <label for="pedro-user-email">Your Email</label>
                      </div>
                      <div id="pedro-api-key-result" class="alert d-none"></div>
                    </div>
                    <div class="modal-footer flex-column">
                      <button type="submit" class="btn btn-primary mb-2">Request API Key</button>
                      <small>
                        <a href="https://www.perfumersvault.com/privacy" target="_blank" class="link-secondary me-3">Privacy Policy</a>
                        <a href="https://www.perfumersvault.com/terms" target="_blank" class="link-secondary me-3">Terms of Service</a>
                        <a href="https://www.perfumersvault.com/pedro-ai" target="_blank" class="link-secondary">Find more about Pedro AI</a>
                      </small>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-auto text-start">
            <button type="submit" name="save-openai" id="save-openai" value="Save" class="btn btn-primary btn-lg">Save</button>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {

	$('[data-bs-toggle=tooltip]').tooltip();
	$('#save-openai').click(function() {
        const provider = $("#ai_service_provider").val();
        let apiKeyField, modelField;

        
        if (provider === 'openai') {
            apiKeyField = $("#openai_api_key");
            modelField = $("#openai_model");
        } else if (provider === 'google_gemini') {
            apiKeyField = $("#google_gemini_api_key");
            modelField = $("#google_gemini_model");
        } else if (provider === 'pedro_perfumer') {
            apiKeyField = $("#pedro_perfumer_api_key");
        }

        // Validate API key is not empty
        if (!apiKeyField || !apiKeyField.val().trim()) {
            $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>API Key cannot be empty.');
            $('.toast-header').removeClass().addClass('toast-header alert-danger');
            $('.toast').toast('show');
            return;
        }

        const data = {
            action: 'update_openai_settings',
            use_ai_service: $("#use_ai_service").is(':checked') ? true : false,
            use_ai_chat: $("#use_ai_chat").is(':checked') ? true : false,
            making_ai_chat: $("#making_ai_chat").is(':checked') ? true : false,
            ai_service_provider: provider
        };

        if (provider === 'openai') {
            data.openai_api_key = apiKeyField.val();
            data.openai_model = modelField.val();
            data.openai_temperature = $("#openai_temperature").val();
        } else if (provider === 'google_gemini') {
            data.google_gemini_api_key = apiKeyField.val();
            data.google_gemini_model = modelField.val();
        } else if (provider === 'pedro_perfumer') {
            data.pedro_perfumer_api_key = apiKeyField.val();
        }

        $.ajax({
            url: '/core/core.php',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
                    $('.toast-header').removeClass().addClass('toast-header alert-success');
                    if ($("#use_ai_service").is(':checked') && $("#use_ai_chat").is(':checked')) {
                        $('#chatbot').show();
                    } else {
                        $('#chatbot').hide();
                    }
                } else if (data.error) {
                    $('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
                    $('.toast-header').removeClass().addClass('toast-header alert-danger');
                }
                $('.toast').toast('show');
            },
            error: function (xhr, status, error) {
                $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. ' + error);
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
                $('.toast').toast('show');
            }
        });
    });
	
    // Disable openai_api_key, openai_model, and openai_temperature if use_ai_service is unchecked
    $('#use_ai_service').change(function() {
        const isChecked = $(this).is(':checked');
        $('#ai_service_provider').prop('disabled', !isChecked);
        $('.provider-fields input, .provider-fields select, #use_ai_chat, #making_ai_chat').prop('disabled', !isChecked);
        $('.provider-help').toggle(isChecked);
        if (isChecked) {
            $('#ai_service_provider').trigger('change'); // Trigger change to show relevant fields
        }
    }).trigger('change'); // Trigger change on page load to set initial state

    // Update temperature value display
    $('#openai_temperature').on('input', function() {
        $('#temperature_value').text($(this).val());
    });

    $('#ai_service_provider').change(function() {
        const provider = $(this).val();
        $('.provider-fields').hide();
        $('.provider-help').hide();
        $('#pedro-status-badge').remove(); // Remove any previous badge

        // Clear interval if switching away from Pedro
        if (window.pedroStatusInterval) {
            clearInterval(window.pedroStatusInterval);
            window.pedroStatusInterval = null;
        }

        if (provider === 'openai') {
            $('#openai-fields').show();
            $('#openai-help').show();
        } else if (provider === 'google_gemini') {
            $('#google-gemini-fields').show();
            $('#google-gemini-help').show();
        } else if (provider === 'pedro_perfumer') {
            $('#pedro-perfumer-fields').show();
            $('#pedro-perfumer-help').show();

            function checkPedroStatus() {
                let $badge = $('#pedro-status-badge');
                $.ajax({
                    url: '/components/pedro_status.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(resp) {
                        let newBadge;
                        if (resp && resp.status && resp.status.toLowerCase() === 'ok') {
                            newBadge = '<span id="pedro-status-badge" class="badge bg-success align-middle ms-2">Service Available</span>';
                        } else {
                            let msg = (resp && resp.message) ? resp.message : 'Service Unavailable';
                            newBadge = '<span id="pedro-status-badge" class="badge bg-danger align-middle ms-2" data-bs-toggle="tooltip" title="' + $('<div>').text(msg).html() + '">Unavailable</span>';
                        }
                        // Only update if different
                        if ($badge.length === 0 || $badge.prop('outerHTML') !== newBadge) {
                            $badge.remove();
                            $('#pedro-perfumer-help h5').append(newBadge);
                            $('[data-bs-toggle="tooltip"]').tooltip();
                        }
                    },
                    error: function(xhr, status, error) {
                        let $badge = $('#pedro-status-badge');
                        let newBadge = '<span id="pedro-status-badge" class="badge bg-danger align-middle ms-2" data-bs-toggle="tooltip" title="' + $('<div>').text(error).html() + '">Unavailable</span>';
                        if ($badge.length === 0 || $badge.prop('outerHTML') !== newBadge) {
                            $badge.remove();
                            $('#pedro-perfumer-help h5').append(newBadge);
                            $('[data-bs-toggle="tooltip"]').tooltip();
                        }
                    }
                });
            }

            checkPedroStatus();
            window.pedroStatusInterval = setInterval(function() {
                if ($('#ai_service_provider').val() === 'pedro_perfumer') {
                    checkPedroStatus();
                } else if (window.pedroStatusInterval) {
                    clearInterval(window.pedroStatusInterval);
                    window.pedroStatusInterval = null;
                }
            }, 5000);
        }
    }).trigger('change'); // Trigger change on page load to set initial state
	
    $('#use_ai_chat').change(function() {
        $('#making_ai_chat').prop('disabled', !$(this).is(':checked') || !$('#use_ai_service').is(':checked'));
    }).trigger('change'); // Trigger change on page load to set initial state

    $('#making_ai_chat').change(function() {
        if ($(this).is(':checked')) {
            // Logic to show chatbox in formula-making page
            $('#chatbox').show();
        } else {
            $('#chatbox').hide();
        }
    }).trigger('change'); // Trigger change on page load to set initial state

    // Pedro API Key Modal logic
$('#pedro-api-key-form').on('submit', function(e) {
    e.preventDefault();
    var $result = $('#pedro-api-key-result');
    $result.removeClass('alert-success alert-danger').addClass('d-none').text('');
    var email = $('#pedro-user-email').val().trim();

    if (!email) {
        $result.removeClass('d-none').addClass('alert alert-danger').text('Please enter your email.');
        return;
    }

    $result.removeClass('d-none').addClass('alert alert-info').text('Requesting API key...');

        $.ajax({
            url: '/components/pedro_api_proxy.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ user: email }),
            dataType: 'json',
            success: function(resp) {
                if (resp.success && resp.api_key) {
                    $result
                        .removeClass('alert-info alert-danger')
                        .addClass('alert alert-success')
                        .html(
                            'Your API Key: <code id="pedro-api-key-value">' + resp.api_key + '</code>' +
                            '<button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="copy-pedro-api-key">Copy</button>' +
                            '<button type="button" class="btn btn-outline-primary btn-sm ms-2" id="fill-pedro-api-key">Auto Fill</button>'
                        );
                    // Copy button logic
                    $('#copy-pedro-api-key').off('click').on('click', function() {
                        const apiKey = $('#pedro-api-key-value').text();
                        navigator.clipboard.writeText(apiKey).then(function() {
                            $('#copy-pedro-api-key').text('Copied!').removeClass('btn-outline-secondary').addClass('btn-success');
                            setTimeout(function() {
                                $('#copy-pedro-api-key').text('Copy').removeClass('btn-success').addClass('btn-outline-secondary');
                            }, 1500);
                        });
                    });
                    // Auto fill button logic
                    $('#fill-pedro-api-key').off('click').on('click', function() {
                        $('#pedro_perfumer_api_key').val(resp.api_key).focus();
                    });
                } else {
                    $result.removeClass('alert-info alert-success').addClass('alert alert-danger')
                        .text(resp.error || 'Failed to create API key.');
                }
            },
            error: function(xhr, status, error) {
                $result.removeClass('alert-info alert-success').addClass('alert alert-danger')
                    .text('Error: ' + error);
            }
        });
    });
});

// Prevent Pedro API Key modal if service is unavailable
$('#pedro-create-api-key-btn').on('click', function(e) {
    // Check if the badge is present and is not green
    var $badge = $('#pedro-status-badge');
    if ($badge.length && $badge.hasClass('bg-danger')) {
        e.preventDefault();
        // Optionally show a toast or alert
        $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>Pedro Perfumer service is unavailable. Please try again later.');
        $('.toast-header').removeClass().addClass('toast-header alert-danger');
        $('.toast').toast('show');
        return false;
    }
    // Otherwise, allow modal to open
});
</script>

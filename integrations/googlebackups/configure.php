<?php 
define('__ROOT__', dirname(dirname(dirname(__FILE__)))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if ($role !== 1){
  echo json_encode(['success' => false, 'error' => 'Not authorised']);
  return;
}

$schedule_time = isset($integrations_settings['googlebackups_schedule']) && is_numeric($integrations_settings['googlebackups_schedule']) ? date('H:i', $integrations_settings['googlebackups_schedule']) : '00:00';

?>
<div class="card-body">
    <div id="bk-inf"></div>

    <div class="alert alert-info">
        <i class="fa-solid fa-circle-info mx-2"></i>
        Follow the official
        <a class="link-primary" href="https://developers.google.com/workspace/guides/create-credentials"
            target="_blank">Google docs</a>
        on how to create credentials.
    </div>

    <div class="row">
        <div class="col-sm-6 mb-3">
            <label for="googlebackups_credentials" class="form-label">Credentials (JSON)</label>
            <textarea class="form-control" name="googlebackups_credentials" id="googlebackups_credentials"
                rows="20"><?=$integrations_settings['googlebackups_credentials']?></textarea>
        </div>

        <div class="col-sm-6">
            <div class="mb-3">
              <label for="googlebackups_agent_srv_host" class="form-label">Backup agent hostname or IP
              <i class="fa-solid fa-circle-info mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Defaults to localhost"></i>
              </label>
              <input name="googlebackups_agent_srv_host" type="text" class="form-control" id="googlebackups_agent_srv_host" value="<?=$integrations_settings['googlebackups_agent_srv_host'] ?: 'gbkagent'?>">
            </div>

            <div class="mb-3">
              <label for="googlebackups_agent_srv_port" class="form-label">Backup agent TCP port
              <i class="fa-solid fa-circle-info mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Defaults to 3000"></i>
              </label>
              <input name="googlebackups_agent_srv_port" type="text" class="form-control" id="googlebackups_agent_srv_port" value="<?=$integrations_settings['googlebackups_agent_srv_port'] ?: 3000?>">
            </div>

            <div class="mb-3">
                <label for="googlebackups_schedule" class="form-label">Scheduled Time</label>
                <input name="googlebackups_schedule" type="time" class="form-control" id="googlebackups_schedule" value="<?= $schedule_time ?>">
            </div>
            <div class="mb-3">
                <label for="googlebackups_prefix" class="form-label">Prefix
                <i class="fa-solid fa-circle-info mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="This will be added as a prefix to backups"></i>
                </label>
                <input name="googlebackups_prefix" type="text" class="form-control" id="googlebackups_prefix" value="<?=$integrations_settings['googlebackups_prefix']?>">
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="googlebackups_enabled" name="googlebackups_enabled"
                    <?php if ($integrations_settings['googlebackups_enabled'] === '1') echo 'checked'; ?>>
                <label class="form-check-label" for="googlebackups_enabled">Enabled</label>
            </div>
        </div>
    </div>

    <div class="dropdown-divider"></div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="googlebackups_save" value="Save changes">
    </div>
</div>



<script>
$(document).ready(function() {
  $('[data-bs-toggle="tooltip"]').tooltip();
  $('#googlebackups_prefix').on('input', function() {
    this.value = this.value.toLowerCase().replace(/[^a-z]/g, '');
  });
  $('#googlebackups_save').click(function() {
      var googlebackups_enabled = $('#googlebackups_enabled').is(':checked') ? '1' : '0';
      try {
          JSON.parse($("#googlebackups_credentials").val());
      } catch (error) {
          var msg =
            '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Credentials must be a valid JSON string</div>';
            $('#bk-inf').html(msg);
            return;
      }
      $.ajax({
          url: '/integrations/googlebackups/manage.php',
          type: 'POST',
          data: {
            action: 'googlebackups_update',
            googlebackups_credentials: JSON.stringify(JSON.parse($("#googlebackups_credentials").val())),
            googlebackups_enabled: googlebackups_enabled,
            googlebackups_schedule: $("#googlebackups_schedule").val(),
            googlebackups_prefix: $("#googlebackups_prefix").val(),
            googlebackups_gdrive_name: $("#googlebackups_gdrive_name").val(),
            googlebackups_agent_srv_host: $("#googlebackups_agent_srv_host").val(),
            googlebackups_agent_srv_port: $("#googlebackups_agent_srv_port").val()
          },
          dataType: 'json',
          success: function(data) {
              if (data.success) {
                  var msg =
                      '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' +
                      data.success + '</div>';
              } else {
                  var msg =
                      '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' +
                      data.error + '</div>';
              }
              $('#bk-inf').html(msg);
          },
          error: function(xhr, status, error) {
              $('#bk-inf').html(
                  '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>' +
                  status + ', check server logs for more info. ' + error + '</div>');
          }
      });
  });
});
</script>
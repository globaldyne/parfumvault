<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if ($role !== 1){
  echo json_encode(['success' => false, 'error' => 'Not authorised']);
  return;
}

$bk = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM backup_provider WHERE id = '1' AND owner_id = '$userID'"));

?>
<div class="card-body">
  <div id="bk-inf"></div>

  <div class="alert alert-warning">
    <i class="fa-solid fa-triangle-exclamation mx-2"></i>
    The services must be restarted for the changes to take effect.
  </div>
  
  <div class="alert alert-info">
    <i class="fa-solid fa-circle-info mx-2"></i>
    Follow the official 
    <a class="link-primary" href="https://developers.google.com/workspace/guides/create-credentials" target="_blank">Google docs</a> 
    on how to create credentials.
  </div>

  <div class="row">
    <div class="col-sm-6 mb-3">
      <label for="bk-creds" class="form-label">Credentials (JSON)</label>
      <textarea class="form-control" name="bk-creds" id="bk-creds" rows="20"><?=$bk['credentials']?></textarea>
    </div>

    <div class="col-sm-6">
      <div class="mb-3">
        <label for="bk_srv_host" class="form-label">Backup service host</label>
        <input name="bk_srv_host" type="text" class="form-control" id="bk_srv_host" value="<?=$settings['bk_srv_host']?>">
      </div>

      <div class="mb-3">
        <label for="gdrive_name" class="form-label">Backup folder</label>
        <input name="gdrive_name" type="text" class="form-control" id="gdrive_name" value="<?=$bk['gdrive_name']?>">
      </div>

      <div class="mb-3">
        <label for="time" class="form-label">Scheduled Time</label>
        <input name="time" type="time" class="form-control" id="time" value="<?=$bk['schedule']?>">
      </div>

      <div class="mb-3">
        <label for="desc" class="form-label">Short Description</label>
        <input name="desc" type="text" class="form-control" id="desc" value="<?=$bk['description']?>">
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="enabled" name="enabled" <?php if ($bk['enabled'] == '1') echo 'checked'; ?>>
        <label class="form-check-label" for="enabled">Enabled</label>
      </div>
    </div>
  </div>

  <div class="dropdown-divider"></div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <input type="submit" name="button" class="btn btn-primary" id="bk-save" value="Save changes">
  </div>
</div>



<script>

$('#bk-save').click(function() {
  var enabled = $('#enabled').is(':checked') ? '1' : '0';
  try {
    JSON.parse($("#bk-creds").val());
  } catch (error) {
    var msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Credentials must be a valid JSON string</div>';
	  $('#bk-inf').html(msg);
    return;
  }
  $.ajax({
    url: '/core/core.php',
    type: 'POST',
    data: {
      bkProv: 'update',
      creds: $("#bk-creds").val(),
      enabled: enabled,
      schedule: $("#time").val(),
      bkDesc: $("#desc").val(),
			gdrive_name: $("#gdrive_name").val(),
			bk_srv_host: $("#bk_srv_host").val()
    },
    dataType: 'json',
    success: function(data) {
      if (data.success) {
        var msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
      } else {
        var msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
      }
      $('#bk-inf').html(msg);
    },
		error: function (xhr, status, error) {
		  $('#bk-inf').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>' + status + ', check server logs for more info. '+ error + '</div>');
		}
  });
});

</script>

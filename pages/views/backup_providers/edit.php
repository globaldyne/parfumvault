<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

$bk = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM backup_provider WHERE id = '".$_GET['id']."'"));

?>
<div class="card-body">
  <div id="bk-inf"></div>
  <div class="row">
    <div class="col-sm">
      <label for="bk-creds" class="form-label">Credentials (JSON)</label>
      <textarea class="form-control" name="bk-creds" id="bk-creds" rows="20"><?=$bk['credentials']?></textarea>
    </div>
    <div class="col-sm">
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
        url: '/pages/update_data.php',
        type: 'POST',
        data: {
            bkProv: 'update',
            id: <?=$_GET['id']?>,
            creds: $("#bk-creds").val(),
            enabled: enabled,
            schedule: $("#time").val(),
            bkDesc: $("#desc").val()
        },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                var msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' + data.success + '</div>';
            } else {
                var msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' + data.error + '</div>';
            }
            $('#bk-inf').html(msg);
        }
    });
});


</script>

<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


?>
<div class="card-body">
  <div id="bk-inf"></div>
  <div class="row">
    <div class="col-sm mb-3 mt-3">
      <label for="bk_srv_host" class="form-label">Backup service host</label>
      <input name="bk_srv_host" type="bk_srv_host" class="form-control" id="bk_srv_host" value="<?=$settings['bk_srv_host']?>">

    </div>
    
  </div>
  <div class="dropdown-divider"></div>
  <div class="modal-footer">
    <input type="submit" name="button" class="btn btn-primary" id="bk-save" value="Save changes">
  </div>
</div>
 

<script>
$('#bk-save').click(function() {
    $.ajax({
        url: '/pages/update_data.php',
        type: 'POST',
        data: {
            bkHost: 'update',
            bk_srv_host: $("#bk_srv_host").val(),
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
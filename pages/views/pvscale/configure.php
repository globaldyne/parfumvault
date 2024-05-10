<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


?>
<div class="card-body">
  <div id="scmsg"></div>
  <div class="row g-4">
 
    <div class="col-sm-4">
      <div class="mb-3">
        <label for="pv_scale_host" class="form-label">Scale IP</label>
        <input name="pv_scale_host" type="pv_scale_host" class="form-control" id="pv_scale_host" value="<?=$settings['pv_scale_host']?>">
      </div> 
      <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="pv_scale_enabled" name="pv_scale_enabled" <?php if ($settings['pv_scale_enabled'] == '1') echo 'checked'; ?>>
        <label class="form-check-label" for="pv_scale_enabled">Enabled</label>
      </div>
      <div id="sysData"></div>
    </div>
 
    <div class="row col-sm g-2 gap-4" id="controlScale">
        <div class="col-sm-2">
            <div class="d-grid gap-2 col-6 mx-auto mb-2" >
                <input type="submit" name="scaleCal" class="btn btn-info" id="scaleCal" value="Calibrate">
                <input type="submit" name="btnFirm" class="btn btn-info" id="chkFirm" value="Firmware update">
            </div>
        </div>
        <div class="col-sm-2">
            <div class="d-grid gap-2 col-6 mx-auto mb-2" >
                <input type="submit" name="scaleScreenOn" class="btn btn-info" id="scaleScreenOn" value="Turn Screen ON">
                <input type="submit" name="scaleScreenOff" class="btn btn-info" id="scaleScreenOff" value="Turn Screen Off">
            </div>
        </div>
    </div>
    
  </div>
  <div class="dropdown-divider"></div>
  <div class="modal-footer">
    <input type="submit" name="chkConn" class="btn btn-warning" id="chkConn" value="Validate connection">
    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="connSpinner"></span>
    <input type="submit" name="subScale" class="btn btn-primary" id="subScale" value="Save changes">
  </div>
</div>


<script>
$(document).ready(function() {



});

</script>
<script src="/js/pvScale.js"></script>

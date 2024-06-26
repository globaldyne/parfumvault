<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


?>
<div class="card-body">
  <div id="scmsg"></div>
  <div class="row g-2">
 
    <div class="col-sm">
      <div class="mb-3">
        <img src="/img/pvScaleSP1.png" />
      </div>
    </div>
    
    <div class="col-sm">
     
    
      <div class="mb-3">
       PV Scale is a specially designed formulation scale which will weight, guide you and automatically update your inventory when you formulating.
       <br>
       Its currently not available to buy but you can use discussions page in github to request/discuss features and to be notified when is available.
      </div>
      
    </div>
    
  </div>
  <div class="dropdown-divider"></div>
  <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
  </div>
</div>
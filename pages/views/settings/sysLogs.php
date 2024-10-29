<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

require_once(__ROOT__.'/inc/settings.php');

?>

<h3>System logs</h3>
<hr>
<div class="card-body">
<?php if(strtoupper(getenv('PLATFORM')) !== "CLOUD"){ ?>
	<div class="mt-4 alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>This is only available in cloud / docker installations</div>
<?php } else if ($sysLogs === FALSE){ ?>
<div class="mt-4 alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>System logs are disabled by configuration</div>

    <div class="mb-3 col mt-2">
        <div class="row mb-2">
        
          <div class="col">
            <li><a href="/core/getSysLogs.php?log=access" target="_blank">Access Log</a></li>
          </div>
        </div>
        
        <div class="row mb-2">
            <div class="col">
                <li><a href="/core/getSysLogs.php?log=error" target="_blank">Error Log</a></li>
            </div>
        </div>
        
    </div>
<?php } ?>
</div>


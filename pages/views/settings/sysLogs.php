<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');


?>

<h3>System logs</h3>
<hr>
<div class="card-body">
<?php
   	$platform = strtoupper(getenv('PLATFORM'));

    if ($platform !== "CLOUD") {
        echo '<div class="mt-4 alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>This feature is only available in cloud or Docker installations.</div>';
    } elseif (!$sysLogsEnabled) {
        echo '<div class="mt-4 alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>System logs access is disabled by configuration. See our <a href="https://www.perfumersvault.com/knowledge-base/howto-docker/" target="_blank">KB article</a> for more information</div>';
    } else {
?>
        <div class="mb-3 col mt-2">
            <div class="row mb-2">
                <div class="col">
                    <li><a href="/core/getSysLogs.php?log=access" target="_blank">Access Logs</a></li>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <li><a href="/core/getSysLogs.php?log=error" target="_blank">Error Logs</a></li>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <li><a href="/core/getSysLogs.php?log=fpm" target="_blank">FPM Logs</a></li>
                </div>
            </div>            
        </div>
<?php } ?>
</div>



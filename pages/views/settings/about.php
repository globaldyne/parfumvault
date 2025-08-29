<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

?>
<div class="row">
    <div class="col">
        <strong>Perfumers Vault Web</strong>
    </div>
</div>
<div class="row my-4">
    <div class="col"></div>
</div>
<div class="row">
    <div class="col">
        <p>A sophisticated tool to help perfumers organize their formulas, ingredients, and inventory.</p>
        <pre><?php echo file_get_contents(__ROOT__.'/LICENSE.txt');?></pre>
    </div>
</div>
<div class="row">
    <div class="col">
        <p>Version: <strong><?php echo file_get_contents(__ROOT__.'/VERSION.md');?></strong> Web</p>
        <p>DB Schema Version: <strong><?php echo $pv_meta['schema_ver'];?></strong></p>
        <p>Release <a href="CHANGELOG.md" target="_blank"><strong>CHANGELOG</strong></a></p>
        <p>Theme based in <a href="https://startbootstrap.com/theme/sb-admin-2/" target="_blank"><strong>SB Admin 2</strong></a></p>
    </div>
</div>

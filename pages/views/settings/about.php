<?php 
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

require_once(__ROOT__.'/inc/settings.php');

?>
<table width="100%" border="0">
  <tr>
    <td><strong>Perfumer's Vault Pro</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><p>A sophisticated tool to help perfumers organize their formulas, ingredients and inventory.
    </p>
      <pre><?php echo file_get_contents(__ROOT__.'/LICENSE.txt');?></pre>
</td>
  </tr>
  <tr>
    <td>
    <p>Version: <strong><?php echo file_get_contents(__ROOT__.'/VERSION.md');?></strong> PRO</p>
    <p>DB Schema Version: <strong><?php echo $pv_meta['schema_ver'];?></strong></p>
    <p><a href="https://www.jbparfum.com" target="_blank">https://www.jbparfum.com</a></p>
    <p>Theme by <a href="https://startbootstrap.com/theme/sb-admin-2/" target="_blank"><strong>SB Admin 2</strong></a></p>
    </td>
  </tr>
</table>

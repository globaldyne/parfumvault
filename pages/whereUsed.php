<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
//require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/inc/opendb.php');
//require_once(__ROOT__.'/inc/settings.php');
$ingID = mysqli_real_escape_string($conn, base64_decode($_GET["id"]));

$ingUsage = mysqli_query($conn,"SELECT name,fid FROM formulas WHERE ingredient = '".$ingID."'");
while($used_res = mysqli_fetch_array($ingUsage)){
	$used[] = $used_res;
}
?>


<h3><?=$ingID?> is used in <?=count($used)?> formulas</h3>
              <hr>
              <div class="where_used">
              <table width="100%" border="0">
              <?php 
			       foreach ($used as $used){
					$gFMD = mysqli_fetch_array(mysqli_query($conn, "SELECT id,product_name FROM formulasMetaData WHERE fid = '".$used['fid']."'"));
			   ?>
                  <tr>
                    <td width="19%">
                   <a href="/?do=Formula&id=<?=$gFMD['id']?>" target="_blank"><?=$used['name']?></a>
                    </td>
                    <td width="19%">
                   <a href="/?do=Formula&id=<?=$gFMD['id']?>" target="_blank"><?=$gFMD['product_name']?></a>
                    </td>
                  </tr>
                  <?php } ?>
                </table>
                </div>
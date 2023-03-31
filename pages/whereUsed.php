<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
$ingID = mysqli_real_escape_string($conn, base64_decode($_POST["id"]));

$ingUsage = mysqli_query($conn,"SELECT name,fid FROM formulas WHERE ingredient = '".$ingID."'");
while($used_res = mysqli_fetch_array($ingUsage)){
	$used[] = $used_res;
}
$ingUsageCmp = mysqli_query($conn,"SELECT ing,percentage FROM allergens WHERE name = '".$ingID."'");
while($used_cmp = mysqli_fetch_array($ingUsageCmp)){
	$usedCmp[] = $used_cmp;
}

if(count((array)$used) == 0 && count((array)$usedCmp) == 0){
	echo '<div class="alert alert-info"><strong>'.$ingID.' isn\'t currently used in any formulas or ingredient compositions.</strong></div>';
	return;
}
?>
<h3><?=$ingID?> is used in <?=count($used)?> formulas</h3>
<hr>
<div class="where_used">
    <table width="100%" class="table table-bordered dataTable no-footer"> 
        <tr>
            <th>Formula name</th>
            <th>Product name</th>
        </tr>
        <?php 
           foreach ($used as $used){
            $gFMD = mysqli_fetch_array(mysqli_query($conn, "SELECT id,product_name FROM formulasMetaData WHERE fid = '".$used['fid']."'"));
        ?>
          <tr>
            <td width="19%">
           <a href="/?do=Formula&id=<?=$gFMD['id']?>&search=<?=$ingID?>" target="_blank"><?=$used['name']?></a>
            </td>
            <td width="19%">
           <a href="/?do=Formula&id=<?=$gFMD['id']?>&search=<?=$ingID?>" target="_blank"><?=$gFMD['product_name']?:'N/A'?></a>
            </td>
          </tr>
          <?php } ?>
    </table>
</div>

<h3><?=$ingID?> is used in <?=count($usedCmp)?> ingredient compositions</h3>
<hr>
<div class="where_used">
    <table width="100%" class="table table-bordered dataTable no-footer"> 
        <tr>
            <th>Ingredient name</th>
            <th>Percentage</th>
        </tr>
        <?php foreach ($usedCmp as $used){ ?>
          <tr>
            <td width="19%">
           		<a href="/pages/mgmIngredient.php?id=<?=base64_encode($used['ing'])?>"><?=$used['ing']?></a>
            </td>
            <td width="19%">
           		<a href="#"><?=$used['percentage']?:'N/A'?></a>
            </td>
          </tr>
          <?php } ?>
    </table>
</div>

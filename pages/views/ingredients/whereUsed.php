<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

$defPercentage = $settings['defPercentage'];

$ingName = mysqli_real_escape_string($conn, base64_decode($_GET["ingName"]));
$ingID = $_GET["ingID"];

$ingUsage = mysqli_query($conn,"SELECT name,fid FROM formulas WHERE ingredient = '".$ingName."' AND owner_id = '$userID'");
while($used_res = mysqli_fetch_array($ingUsage)){
	$used[] = $used_res;
}
$ingUsageCmp = mysqli_query($conn,"SELECT ing,min_percentage,max_percentage FROM ingredient_compounds WHERE name = '".$ingName."' AND owner_id = '$userID'");
while($used_cmp = mysqli_fetch_array($ingUsageCmp)){
	$usedCmp[] = $used_cmp;
}

if(count((array)$used) == 0 && count((array)$usedCmp) == 0){
	echo '<div class="alert alert-info"><strong>'.$ingName.' isn\'t currently used in any formulas or ingredient compositions.</strong></div>';
	return;
}
?>
<h3><?=$ingName?> is used in <?=count((array)$used)?> formulas</h3>
<?php if(count((array)$used)){ ?>
<div class="where_used mb-3">
    <table class="table table-striped"> 
        <thead>
            <tr>
                <th>Formula name</th>
                <th>Product name</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($used as $used){
                $gFMD = mysqli_fetch_array(mysqli_query($conn, "SELECT id, product_name FROM formulasMetaData WHERE fid = '".$used['fid']."' AND owner_id = '$userID'"));
            ?>
            <tr>
                <td>
                    <a href="/?do=Formula&id=<?=$gFMD['id']?>&search=<?= $ingName ?>" target="_blank"><?=$used['name']?></a>
                </td>
                <td>
                    <a href="/?do=Formula&id=<?=$gFMD['id']?>&search=<?= $ingName ?>" target="_blank"><?=$gFMD['product_name'] ?: 'N/A' ?></a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } ?>
<h3 class="mt-3"><?= $ingName ?> is used in <?=count((array)$usedCmp)?> ingredient compositions</h3>
<?php if(count((array)$usedCmp)){?>
<div class="where_used">
    <table class="table table-bordered dataTable no-footer"> 
        <thead>
            <tr>
                <th>Ingredient name</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usedCmp as $used) { ?>
            <tr>
                <td>
                    <a href="/pages/mgmIngredient.php?id=<?=$ingID?>"><?=$used['ing']?></a>
                </td>
                <td>
                    <?php
                    if ($defPercentage === 'avg_percentage') {
                        $min = isset($used['min_percentage']) ? (float)$used['min_percentage'] : 0.0;
                        $max = isset($used['max_percentage']) ? (float)$used['max_percentage'] : 0.0;
                        if ($min > 0 && $max > 0) {
                            $avg = ($min + $max) / 2.0;
                        } elseif ($max > 0) {
                            $avg = $max;
                        } elseif ($min > 0) {
                            $avg = $min;
                        } else {
                            $avg = 0.0;
                        }
                        echo $avg;
                    } else {
                        echo $used[$defPercentage] ?: 'N/A';
                    }
                    ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } ?>

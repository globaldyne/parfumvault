<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
if(!$_GET['ingID']){
	echo 'Invalid ID';
	return;
}
$id = $_GET['ingID'];
require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/inc/settings.php');

$genIng = mysqli_fetch_array(mysqli_query($conn, "SELECT name,cas,notes,odor FROM ingredients WHERE id  = '$id'"));
$getIFRA = mysqli_fetch_array(mysqli_query($conn, "SELECT image,amendment,cas_comment,formula,synonyms,cat4,risk FROM IFRALibrary WHERE cas = '".$genIng['cas']."'"));
$reps = mysqli_query($conn, "SELECT ing_rep_name, notes FROM ingReplacements WHERE ing_name = '".$genIng['name']."'");

while($replacements = mysqli_fetch_array($reps)){
		$replacement[] = $replacements;
}
?>

<div class="card shadow mb-4">
	<?php if($getIFRA['risk']){ ?>
    <div class="card-header py-3">
        <p class="mb-0"><div class="alert alert-warning"><strong>WARNING: </strong><p>This material is IFRA regulated for maximum usage at <strong><?=$getIFRA['cat4']?>%</strong> due to <strong><?=$getIFRA['risk']?></strong></p></div></p>
    </div>
    <?php } ?>
    <div class="card-body">
        <p><strong>Description: </strong><?=$genIng['notes']?></p>
        <p><strong>Odor: </strong><?=$genIng['odor']?></p>
        <p><strong>Possible replacements: </strong><?php 
		if ($replacement){
			foreach ($replacement as $r){
				echo $r['ing_rep_name'].', ';
			}
		}else{
			echo 'None';
		}?>
        </p>
    </div>
</div>

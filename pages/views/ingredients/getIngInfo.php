<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
if(!$_GET['ingID']){
	
	echo '<div class="alert alert-danger">Ingredient is missing from your ingredients inventory. Please <a href="/?do=ingredients" target="_blank">create</a> it first.</div>';
	
	return;
}
$id = $_GET['ingID'];
require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/searchIFRA.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/getIngStock.php');

$genIng = mysqli_fetch_array(mysqli_query($conn, "SELECT name,cas,notes,odor FROM ingredients WHERE id  = '$id'"));
$getIFRA = mysqli_fetch_array(mysqli_query($conn, "SELECT image,amendment,cas_comment,formula,synonyms,cat4,risk FROM IFRALibrary WHERE cas = '".$genIng['cas']."'"));

$reps = mysqli_query($conn,"SELECT ing_rep_name,ing_rep_id FROM ingReplacements WHERE ing_name = '".$genIng['name']."'");
	if (!mysqli_num_rows($reps)) { 
		$reps = mysqli_query($conn,"SELECT ing_name,ing_id FROM ingReplacements WHERE ing_rep_name = '".$genIng['name']."'");
	}
while($replacements = mysqli_fetch_array($reps)){
		$replacement[] = $replacements;
}

if($_GET['replacementsOnly']){
	$i = 0;
	foreach ($replacement as $rep) { 
		$r['id'] = (int)$rep['ing_rep_id']?:$rep['ing_id'];
		$r['name'] = (string)$rep['ing_rep_name']?:$rep['ing_name'];
		$r['stock'] = getIngStock($rep['ing_rep_id']?:$rep['ing_id'],0,$conn);
		
		$rx[]=$r;
		$i++;
	}
	
	$response = array(
  		"data" => $rx
	);
	
	if(empty($rx)){
		$response['data'] = array("No results");
	}
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response);
	return;
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
        <p></p>
        <strong>Possible replacements: </strong><?php 
		if ($replacement){ 
			foreach ($replacement as $r){ ?>
        	<li>
			<?php 
				echo $r['ing_rep_name']?:$r['ing_name'];
				echo getIngStock($r['ing_rep_id']?:$r['ing_id'],1,$conn);
			?>
            </li>
			<?php }
		}else{
			echo 'None';
		}
		?>
    </div>
</div>

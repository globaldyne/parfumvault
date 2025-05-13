<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/getCatByID.php');
require_once(__ROOT__.'/func/profileImg.php');
require_once(__ROOT__.'/func/getIngState.php');


if(empty($_GET["id"])){
	$response["error"] = 'Invalid ID';
	echo json_encode($response);
	return;
}
$ingID = mysqli_real_escape_string($conn, $_GET["id"]);

$ingredient = mysqli_fetch_array(mysqli_query($conn, "SELECT einecs,category,profile,type,physical_state,FEMA,INCI,reach FROM ingredients WHERE id = '$ingID' AND owner_id = '$userID'"));
if(empty($ingredient['category'])){
	return;
}

?>


<div class="sub-2-container sub-2-header mb-4">
	<div class="sub-2-container">
        <span class="coh-inline-element sub-2-inci">IUPAC</span> 
        <span class="coh-inline-element sub-2-fema"><div id="IUPAC"><?=$ingredient['INCI']?:"Not Available"?></div></span>  
    </div>
	<div class="sub-2-container">
        <span class="coh-inline-element sub-2-einecs">EINECS</span> 
        <span class="coh-inline-element sub-2-fema"><?=$ingredient['einecs']?:"Not Available"?></span>  
    </div>    
    <div class="sub-2-container">
        <span class="sub-2-inci">FEMA#</span>
        <span class="sub-2-fema"><?=$ingredient['FEMA']?:"Not Available"?></span>
    </div>
    <div class="sub-2-container">
        <span class="sub-2-inci">REACH#</span>
        <span class="sub-2-fema"><?=$ingredient['reach']?:"Not Available"?></span>
    </div>
</div>

  <div class="row text-center mb-3">
    <div class="col-md-4">
      <h3 class="mgm-cat-in">Olfactive family</h3>
    </div>
    <div class="col-md-4">
      <h3 class="mgm-cat-in"><?php echo $ingredient['profile'].' note'; ?></h3>
    </div>
    <div class="col-md-4">
      <h3 class="mgm-cat-in">Physical State</h3>
    </div>
  </div>
  <div class="row text-center">
    <div class="col-md-4">
      <?=getCatByID($ingredient['category'], TRUE, 'img_ing_overview')?>
    </div>
    <div class="col-md-4">
      <img src="<?=profileImg($ingredient['profile'])?>" class="img_ing_overview"/>
    </div>
    <div class="col-md-4">
      <?=getIngState($ingredient['physical_state'], 'img_ing_overview')?>
    </div>
  </div>

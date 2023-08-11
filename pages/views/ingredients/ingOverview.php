<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/getCatByID.php');
require_once(__ROOT__.'/func/profileImg.php');
require_once(__ROOT__.'/func/getIngState.php');


if(empty($_GET["id"])){
	return;
}
$ingID = mysqli_real_escape_string($conn, $_GET["id"]);

$ingredient = mysqli_fetch_array(mysqli_query($conn, "SELECT einecs,category,profile,type,odor,physical_state,FEMA,INCI,reach FROM ingredients WHERE id = '$ingID'"));
if(empty($ingredient['category'])){
	return;
}

?>

<style>

.img_ing {
    max-height: 100px;
}
</style>
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
<table width="100%" border="0">
  <tr>
    <td width="33%" align="center"><h3 class="mgm-cat-in">Olfactive family</h3></td>
    <td width="33%" align="center"><h3 class="mgm-cat-in"><?php echo $ingredient['profile'].' note'; ?></h3></td>
    <td width="33%" align="center"><h3 class="mgm-cat-in">Physical State</h3></td>
  </tr>
  <tr>
    <td align="center"><?=getCatByID($ingredient['category'],TRUE,$conn)?></td>
    <td align="center"><img src="<?=profileImg($ingredient['profile'])?>" class="img_ing"/></td>
    <td align="center"><?=getIngState($ingredient['physical_state'],'img_ing')?></td>
  </tr>
</table>


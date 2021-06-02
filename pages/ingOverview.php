<?php

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/getCatByID.php');

if(empty($_GET["id"])){
	return;
}
$ingID = mysqli_real_escape_string($conn, $_GET["id"]);

$ingredient = mysqli_fetch_array(mysqli_query($conn, "SELECT category,profile,type,odor,physical_state FROM ingredients WHERE id = '$ingID'"));
if(empty($ingredient['category'])){
	return;
}
if($ingredient['profile'] == 'Top'){
	$profile_img = '../img/Pyramid/Pyramid_Slice_Top.png';
}elseif($ingredient['profile'] == 'Heart'){
	$profile_img = '../img/Pyramid/Pyramid_Slice_Heart.png';
}elseif($ingredient['profile'] == 'Base'){
	$profile_img = '../img/Pyramid/Pyramid_Slice_Base.png';
}

if($ingredient['physical_state'] == '1'){ 
		$physical_state = 'Liquid'; 
	}else{ 
		$physical_state = 'Solid'; 
}
?>
<html>
<head>
<link href="../css/vault.css" rel="stylesheet">
<style>

.img_ing {
    max-height: 100px;
}
</style>
</head>

<table width="100%" border="0">
  <tr>
    <td width="33%" align="center"><h3 class="mgm-cat-in">Olfactive family</h3></td>
    <td width="33%" align="center"><h3 class="mgm-cat-in"><?php echo $ingredient['profile'].' note'; ?></h3></td>
    <td width="33%" align="center"><h3 class="mgm-cat-in">Odor prodile</h3></td>
  </tr>
  <tr>
    <td align="center"><?=getCatByID($ingredient['category'],TRUE,$conn)?></td>
    <td align="center"><img src="<?=$profile_img?>" class="img_ing"/></td>
    <td align="center"><div class="odor_profile_overview"><li><?=$physical_state?></li>
    	 <li><?=$ingredient['odor']?></li>
         </div></td>
  </tr>
</table>

</html>
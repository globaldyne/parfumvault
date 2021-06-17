<?php

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');
require_once(__ROOT__.'/func/getCatByID.php');
require_once(__ROOT__.'/func/profileImg.php');


if(empty($_GET["id"])){
	return;
}
$ingID = mysqli_real_escape_string($conn, $_GET["id"]);

$ingredient = mysqli_fetch_array(mysqli_query($conn, "SELECT category,profile,type,odor,physical_state FROM ingredients WHERE id = '$ingID'"));
if(empty($ingredient['category'])){
	return;
}


if($ingredient['physical_state'] == '1'){ 
	$physical_state = '<img src="../img/liquid.png" class="img_ing"/>';
}elseif($ingredient['physical_state'] == '2'){ 
	$physical_state = '<img src="../img/solid.png" class="img_ing"/>';
}else{ 
	$physical_state = 'N/A'; 
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
    <td width="33%" align="center"><h3 class="mgm-cat-in">Physical State</h3></td>
  </tr>
  <tr>
    <td align="center"><?=getCatByID($ingredient['category'],TRUE,$conn)?></td>
    <td align="center"><img src="<?=profileImg($ingredient['profile'])?>" class="img_ing"/></td>
    <td align="center"><?=$physical_state?></td>
  </tr>
</table>

</html>
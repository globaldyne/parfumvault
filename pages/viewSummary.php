<?php

define('__ROOT__', dirname(dirname(__FILE__))); 
define('pvault_panel', TRUE);

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/arrFilter.php');
require_once(__ROOT__.'/func/getCatByID.php');


if(!$_GET['id']){
	echo 'Formula id is missing.';
	return;
}
	
$fid = mysqli_real_escape_string($conn, $_GET['id']);

if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulas WHERE fid = '$fid'")) == 0){
	echo '<div class="alert alert-info alert-dismissible">Incomplete formula. Please add ingredients.</div>';
	return;
}

$description = mysqli_fetch_array(mysqli_query($conn, "SELECT notes FROM formulasMetaData WHERE fid = '$fid'"));

$formula_q = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE fid = '$fid'");
while ($formula = mysqli_fetch_array($formula_q)){
	    $form[] = $formula;
}

					
foreach ($form as $formula){
	$top_ing = mysqli_fetch_array(mysqli_query($conn, "SELECT category FROM ingredients WHERE name = '".$formula['ingredient']."' AND profile = 'Top' AND category IS NOT NULL"));
	$heart_ing = mysqli_fetch_array(mysqli_query($conn, "SELECT category FROM ingredients WHERE name = '".$formula['ingredient']."' AND profile = 'Heart' AND category IS NOT NULL"));
	$base_ing = mysqli_fetch_array(mysqli_query($conn, "SELECT category FROM ingredients WHERE name = '".$formula['ingredient']."' AND profile = 'Base' AND category IS NOT NULL"));

	$top_cat[] = mysqli_fetch_array(mysqli_query($conn, "SELECT image,name FROM ingCategory WHERE id = '".$top_ing['category']."' AND image IS NOT NULL"));
	$heart_cat[] = mysqli_fetch_array(mysqli_query($conn, "SELECT image,name FROM ingCategory WHERE id = '".$heart_ing['category']."' AND image IS NOT NULL"));
	$base_cat[] = mysqli_fetch_array(mysqli_query($conn, "SELECT  image,name FROM ingCategory WHERE id = '".$base_ing['category']."' AND image IS NOT NULL"));
}
$top_cat = arrFilter(array_filter($top_cat));
$heart_cat = arrFilter(array_filter($heart_cat));
$base_cat = arrFilter(array_filter($base_cat));


?>
<link href="<?=$_SERVER['REQUEST_SCHEME']?>://<?=$_SERVER['SERVER_NAME']?>/css/vault.css" rel="stylesheet">
<?php if($_GET['text_colour']){ ?>
<style>
html {
	color: <?=$_GET['text_colour']?>;
}
</style>
<?php } ?>
<div id="notes_summary_view">
<?php if($top_cat){ ?>
<table border="0">
  <tr>
    <td height="30" colspan="2" align="left"><strong>Top Notes</strong></td>
  </tr>
  <tr>
    <?php foreach ($top_cat as $x){ ?>
		<td><figure><img class="img_ing" src="<?=$x['image']?>" />
		<figcaption><?=$x['name']?></figcaption></figure></td>
	<?php }?>  
    </tr>
</table>
<?php } ?>
<?php if($heart_cat){ ?>
<table border="0">
  <tr>
    <td height="30" align="left"><strong>Heart Notes</strong></td>
  </tr>
  <tr>
    <?php foreach ($heart_cat as $x){ ?>
		<td><figure><img class="img_ing" src="<?=$x['image']?>" />
		<figcaption><?=$x['name']?></figcaption></figure></td>
	<?php }?>
  </tr>
</table>
<?php } ?>
<?php if($base_cat){ ?>
<table border="0">
  <tr>
    <td height="30" colspan="2" align="left"><strong>Base Notes</strong></td>
  </tr>
  <tr>
    <?php foreach ($base_cat as $x){ ?>
		<td><figure><img class="img_ing" src="<?=$x['image']?>" />
		<figcaption><?=$x['name']?></figcaption></figure></td>
	<?php }?>
  </tr>
</table>
<?php } ?>
<p>&nbsp;</p>
<?php if($description['notes'] && $_GET['no_description'] != '1'){ ?>
<table width="50%" border="0">
  <tr>
    <td width="831"><?=$description['notes']?></td>
  </tr>
</table>
  <?php } ?>
  </div>

<p>&nbsp;</p>


<?php

define('__ROOT__', dirname(dirname(__FILE__))); 
define('pvault_panel', TRUE);

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/arrFilter.php');
require_once(__ROOT__.'/func/getCatByID.php');
require_once(__ROOT__.'/func/get_formula_notes.php');

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

$top_cat = get_formula_notes($conn, $fid, 'top');
$heart_cat = get_formula_notes($conn, $fid, 'heart');
$base_cat = get_formula_notes($conn, $fid, 'base');

$top_ex = get_formula_excludes($conn, $fid, 'top');
$heart_ex = get_formula_excludes($conn, $fid, 'heart');
$base_ex = get_formula_excludes($conn, $fid, 'base');

?>
<style>
.img_ing {
    max-height: 40px;
}

.img_ing_sel {
    max-height: 30px;
	max-width: 30px;
	padding: 0 10px 0 0;
}

figure {
    display: inline;
    border: none;
    margin: 25px;
}

figure img {
    vertical-align: top;
}
figure figcaption {
    border: none;
    text-align: center;
}

formula td, table.table th {
	white-space: revert;
}

#notes_summary_view td {
	display: inline-block;	
}
</style>
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
    <?php foreach ($top_cat as $x){ 
	if($top_ex){
		if (array_search($x['name'],$top_ex) !== false){
			unset($x['name']);
			unset($x['image']);
		}
	}
	?>
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
    <?php foreach ($heart_cat as $x){ 
	if($heart_ex) {
		if (array_search($x['name'],$heart_ex) !== false){
			unset($x['name']);
			unset($x['image']);
		}
	}
	?>
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
    <?php foreach ($base_cat as $x){
		if($base_ex) {
			if (array_search($x['name'],$base_ex) !== false){
				unset($x['name']);
				unset($x['image']);
			}
		}
	?>
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


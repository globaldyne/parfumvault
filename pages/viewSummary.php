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
<?php if(!$_GET['embed']){?>
<p>&nbsp;</p>

<!--Configure View-->

<div class="modal fade" id="conf_view" tabindex="-1" role="dialog" aria-labelledby="conf_view" aria-hidden="true">
  <div class="modal-dialog modal-conf-view" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Choose which notes will be displayed</h5>
      </div>
      <div class="modal-body">
   	    <div id="confViewMsg"></div>
          <form action="javascript:update_view()">   
           <div class="conf_tbl">
            <table width="100%" border="0">
              <tr>
                <td colspan="2"><strong>Top notes</strong><hr /></td>
              </tr>
              <?php foreach ($top_cat as $x){
				  if (!is_numeric(array_search($x['name'],$top_ex ))){
					//   echo '<pre>'; print_r($x); echo '</pre>';
			  ?>
              <tr>
				<td width="54%" height="29" ex_top_ing_name="<?=$x['name']?>"><?=$x['name']?></td>
                <td width="46%"><input name="ex_top_ing" class="ex_ing" type="checkbox" id="<?=str_replace(' ', '_',$x['ing'])?>" value="<?=str_replace(' ', '_',$x['ing'])?>" checked="checked" /></td>
              </tr>
              <?php }else{ ?>
			  <tr>
				<td width="54%" ex_top_ing_name="<?=$x['name']?>"><?=$x['name']?></td>
                <td width="46%"><input name="ex_top_ing" class="ex_ing" type="checkbox" id="<?=str_replace(' ', '_',$x['ing'])?>" value="<?=str_replace(' ', '_',$x['ing'])?>" /></td>
              </tr>
			 <?php 
			 	}
			  }
			  ?>
             </table>
            </div>
             
           <div class="conf_tbl">
            <table width="100%" border="0">
              <tr>
                <td colspan="2"><p><strong>Heart notes</strong></p><hr /></td>
              </tr>
              <?php foreach ($heart_cat as $x){
						if (!is_numeric(array_search($x['name'],$heart_ex ))){
			   ?>
              <tr>
				<td width="40%" height="29"><?=$x['name']?></td>
                <td width="51%"><input name="ex_heart_ing" class="ex_ing" type="checkbox" id="<?=str_replace(' ', '_',$x['ing'])?>" value="<?=str_replace(' ', '_',$x['ing'])?>" checked="checked" /></td>
              </tr>
              <?php }else{ ?>
              <tr>
				<td><?=$x['name']?></td>
                <td width="51%"><input name="ex_heart_ing" class="ex_ing" type="checkbox" id="<?=str_replace(' ', '_',$x['ing'])?>" value="<?=str_replace(' ', '_',$x['ing'])?>" /></td>
              </tr>
              <?php 
			 	}
			  }
			  ?>
             </table> 
            </div>

            <div class="conf_tbl">
             <table width="100%" border="0">
              <tr>
                <td colspan="2"><p><strong>Base notes</strong></p><hr /></td>
              </tr>
              <?php foreach ($base_cat as $x){
						if (!is_numeric(array_search($x['name'],$base_ex ))){
			  ?>
              <tr>
				<td width="40%" height="29"><?=$x['name']?></td>
                <td width="60%"><input name="ex_base_ing" class="ex_ing" type="checkbox" id="<?=str_replace(' ', '_',$x['ing'])?>" value="<?=str_replace(' ', '_',$x['ing'])?>" checked="checked" /></td>
              </tr>
             <?php }else{ ?>
              <tr>
				<td><?=$x['name']?></td>
                <td width="60%"><input name="ex_base_ing" class="ex_ing" type="checkbox" id="<?=str_replace(' ', '_',$x['ing'])?>" value="<?=str_replace(' ', '_',$x['ing'])?>" /></td>
              </tr>
              <?php 
			 	}
			  }
			  ?>
            </table>
            </div>
            <table width="100%" border="0">
              <tr>
                <td>            
  					<div class="modal-footer">
     	  				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	 	  				<input type="submit" name="button" class="btn btn-primary" id="btnUpdate" value="Save">
   		  			</div>
           		 </td>
    			</tr>
			</table>
          </form>
    </div>
  </div>
  </div>
</div>

<script>


function update_view(){
	
	$('.ex_ing').each(function(){
		$.ajax({ 
			url: '/pages/manageFormula.php', 
			type: 'GET',
			data: {
				fid: '<?=$fid?>',
				manage_view: '1',
				ex_status: $("#" + $(this).val()).is(':checked'),
				ex_ing: $(this).val()
				},
			dataType: 'json',
				success: function (data) {
					if ( data.success ) {
						var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
						fetch_summary();
						$('#conf_view').modal('hide');
					} else {
						var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>' + data.error + '</strong></div>';
					}
					$('#confViewMsg').html(msg);
				}
		});
	});

}

</script>
<?php } ?>
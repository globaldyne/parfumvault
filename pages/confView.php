<?php
require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
//require_once(__ROOT__.'/inc/settings.php');
//require_once(__ROOT__.'/inc/product.php');
require(__ROOT__.'/func/arrFilter.php');
require(__ROOT__.'/func/get_formula_notes.php');

$fid = $_GET['fid'];
$top_cat = get_formula_notes($conn, $fid, 'top');
$heart_cat = get_formula_notes($conn, $fid, 'heart');
$base_cat = get_formula_notes($conn, $fid, 'base');

$top_ex = get_formula_excludes($conn, $fid, 'top');
$heart_ex = get_formula_excludes($conn, $fid, 'heart');
$base_ex = get_formula_excludes($conn, $fid, 'base');

?>
<div class="modal fade" id="conf_view" tabindex="-1" role="dialog" aria-labelledby="conf_view" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="conf_view">Choose which notes will be displayed</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
   	    <div id="confViewMsg"></div>
          <form action="javascript:update_view()" id="form1">
            <table width="100%" border="0">
              <tr>
                <td colspan="2"><strong>Top notes</strong><hr /></td>
              </tr>
              <?php foreach ($top_cat as $x){
				  //echo array_search($x['name'],$top_ex) ;
					if (array_search($x['name'],$top_ex ) !== true){
					  $chk = 'checked="checked"';
				  	}
				?>
              <tr>
				<td width="20%" ex_top_ing_name="<?=$x['name']?>"><?=$x['name']?></td>
                <td width="80%"><input name="ex_top_ing" class="ex_ing" type="checkbox" id="<?=$x['name']?>" value="<?=$x['name']?>" <?=$chk?> /></td>
              </tr>
              <?php } ?>
              <tr>
                <td colspan="2"><p>&nbsp;</p>
                <strong>Heart notes</strong><hr /></td>
              </tr>
              <?php foreach ($heart_cat as $x){
					if (array_search($x['name'],$heart_ex ) !== true){
					  $chk1 = 'checked="checked"';
					}  
				?>
              <tr>
				<td><?=$x['name']?></td>
                <td width="80%"><input name="ex_heart_ing" class="ex_ing" type="checkbox" id="ex_heart_ing" value="<?=$x['name']?>" <?=$chk1?> /></td>
              </tr>
              <?php } ?>
              <tr>
                <td colspan="2"><p>&nbsp;</p>
                <strong>Base notes</strong><hr /></td>
              </tr>
              <?php foreach ($base_cat as $x){
					if (array_search($x['name'],$base_ex ) !== true){
					  $chk2 = 'checked="checked"';
					}	  
				?>
              <tr>
				<td><?=$x['name']?></td>
                <td width="80%"><input name="ex_base_ing" class="ex_ing" type="checkbox" id="<?=$x['name']?>" value="<?=$x['name']?>" <?=$chk2?> /></td>
              </tr>
              <?php } ?>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
            </table>
    		<div class="modal-footer">
     	  		<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
	 	  		<input type="submit" name="button" class="btn btn-primary" id="btnUpdate" value="Save">
   		  	</div>
          </form>
   	  </div>
  	</div>
  </div>
</div>

<script>

function update_view(){
	
	$('.ex_ing:checked').each(function(){
	
		$.ajax({ 
			url: 'pages/manageFormula.php', 
			type: 'get',
			data: {
				fid: "<?=urlencode($fid)?>",
				manage_view: '1',
				ex_ing: $(this).val()
				},
			dataType: 'html',
				success: function (data) {
					$('#confViewMsg').html(data);
				}
		});
	});

}
</script>
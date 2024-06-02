<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
if(!$_POST['ingID']){
	echo 'Invalid ID';
	return;
}

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');


$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT id,impact_top,impact_heart,impact_base FROM ingredients WHERE id = '".$_POST['ingID']."'"));

?>

<h3>Note Impact</h3>
<hr>
<div class="container">
    <div class="row mb-3">
        <label for="impact_top" class="col-sm-1 col-form-label">Top</label>
        <div class="col-sm-3">
            <select name="impact_top" id="impact_top" class="form-control">
                <option value="none" selected="selected">None</option>
                <option value="100" <?php if($ing['impact_top']=="100") echo 'selected="selected"'; ?> >High</option>
                <option value="50" <?php if($ing['impact_top']=="50") echo 'selected="selected"'; ?> >Medium</option>						
                <option value="10" <?php if($ing['impact_top']=="10") echo 'selected="selected"'; ?> >Low</option>						
            </select>
        </div>
        <div class="col-sm-8"></div>
    </div>
    <div class="row mb-3">
        <label for="impact_heart" class="col-sm-1 col-form-label">Heart</label>
        <div class="col-sm-3">
            <select name="impact_heart" id="impact_heart" class="form-control">
                <option value="none" selected="selected">None</option>
                <option value="100" <?php if($ing['impact_heart']=="100") echo 'selected="selected"'; ?> >High</option>
                <option value="50" <?php if($ing['impact_heart']=="50") echo 'selected="selected"'; ?> >Medium</option>
                <option value="10" <?php if($ing['impact_heart']=="10") echo 'selected="selected"'; ?> >Low</option>
            </select>
        </div>
        <div class="col-sm-8"></div>
    </div>
    <div class="row mb-3">
        <label for="impact_base" class="col-sm-1 col-form-label">Base</label>
        <div class="col-sm-3">
            <select name="impact_base" id="impact_base" class="form-control">
                <option value="none" selected="selected">None</option>
                <option value="100" <?php if($ing['impact_base']=="100") echo 'selected="selected"'; ?> >High</option>
                <option value="50" <?php if($ing['impact_base']=="50") echo 'selected="selected"'; ?> >Medium</option>
                <option value="10" <?php if($ing['impact_base']=="10") echo 'selected="selected"'; ?> >Low</option>
            </select>
        </div>
        <div class="col-sm-8"></div>
    </div>
    <hr />
    <input type="submit" name="save" class="btn btn-primary" id="saveNoteImpact" value="Save" />
</div>

<script>
$('#note_impact').on('click', '[id*=saveNoteImpact]', function () {
	$.ajax({ 
		url: 'update_data.php', 
		type: 'POST',
		data: {
			manage: 'ingredient',
			tab: 'note_impact',
			ingID: '<?=$ing['id'];?>',
			impact_top: $("#impact_top").val(),
			impact_base: $("#impact_base").val(),
			impact_heart: $("#impact_heart").val(),
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$('#toast-title').html('<i class="fa-solid fa-circle-check mr-2"></i>' + data.success);
				$('.toast-header').removeClass().addClass('toast-header alert-success');
			}else{
				$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>' + data.error);
				$('.toast-header').removeClass().addClass('toast-header alert-danger');
			}
			$('.toast').toast('show');
		}
	});
});
</script>
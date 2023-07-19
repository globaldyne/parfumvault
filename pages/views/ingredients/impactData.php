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
<table width="100%" border="0">
    <tr>
        <td width="9%" height="40">Top:</td>
        <td width="19%"><select name="impact_top" id="impact_top" class="form-control">
            <option value="none" selected="selected">None</option>
            <option value="100" <?php if($ing['impact_top']=="100") echo 'selected="selected"'; ?> >High</option>
            <option value="50" <?php if($ing['impact_top']=="50") echo 'selected="selected"'; ?> >Medium</option>						
            <option value="10" <?php if($ing['impact_top']=="10") echo 'selected="selected"'; ?> >Low</option>						
        </select></td>
        <td width="72%">&nbsp;</td>
    </tr>
    <tr>
        <td height="40">Heart:</td>
        <td><select name="impact_heart" id="impact_heart" class="form-control">
            <option value="none" selected="selected">None</option>
            <option value="100" <?php if($ing['impact_heart']=="100") echo 'selected="selected"'; ?> >High</option>
            <option value="50" <?php if($ing['impact_heart']=="50") echo 'selected="selected"'; ?> >Medium</option>
            <option value="10" <?php if($ing['impact_heart']=="10") echo 'selected="selected"'; ?> >Low</option>
        </select></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td height="40">Base:</td>
        <td><select name="impact_base" id="impact_base" class="form-control">
            <option value="none" selected="selected">None</option>
            <option value="100" <?php if($ing['impact_base']=="100") echo 'selected="selected"'; ?> >High</option>
            <option value="50" <?php if($ing['impact_base']=="50") echo 'selected="selected"'; ?> >Medium</option>
            <option value="10" <?php if($ing['impact_base']=="10") echo 'selected="selected"'; ?> >Low</option>
        </select></td>
        <td>&nbsp;</td>
    </tr>
</table>
<hr />
<p><input type="submit" name="save" class="btn btn-info" id="saveNoteImpact" value="Save" /></p>
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
		dataType: 'html',
		success: function (data) {
			$('#ingMsg').html(data);
		}
	});
});
</script>
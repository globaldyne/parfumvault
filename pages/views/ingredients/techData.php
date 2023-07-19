<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 


require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

if(!$_POST['ingID']){
	echo 'Invalid ID';
	return;
}

$ing = mysqli_fetch_array(mysqli_query($conn, "SELECT id,tenacity,flash_point,chemical_name,formula,logp,soluble,molecularWeight,appearance,rdi FROM ingredients WHERE id = '".$_POST['ingID']."'"));


?>
<h3>Techical Data</h3>
	<hr>
	<table width="100%" border="0">
		<tr>
			<td width="20%">Tenacity:</td>
			<td width="80%" colspan="3"><input name="tenacity" type="text" class="form-control" id="tenacity" value="<?php echo $ing['tenacity']; ?>"/></td>
		</tr>
		<tr>
		  <td>Relative Odor Impact:</td>
		  <td colspan="3"><input name="rdi" type="text" class="form-control" id="rdi" value="<?php echo $ing['rdi']; ?>"/></td>
	  </tr>
		<tr>
			<td>Flash Point:</td>
			<td colspan="3"><input name="flash_point" type="text" class="form-control" id="flash_point" value="<?php echo $ing['flash_point']; ?>"/></td>
		</tr>
		<tr>
			<td>Chemical Name:</td>
			<td colspan="3"><input name="chemical_name" type="text" class="form-control" id="chemical_name" value="<?php echo $ing['chemical_name']; ?>"/></td>
		</tr>
		<tr>
			<td>Molecular Formula:</td>
			<td colspan="3">
            <input name="formula" type="text" class="form-control" id="molecularFormula" value="<?php echo $ing['formula']; ?>">
			</td>
		</tr>
		<tr>
			<td>Log/P:</td>
			<td colspan="3"><input name="logp" type="text" class="form-control" id="logp" value="<?php echo $ing['logp']; ?>"/></td>
		</tr>
		<tr>
			<td>Soluble in:</td>
			<td colspan="3"><input name="soluble" type="text" class="form-control" id="soluble" value="<?php echo $ing['soluble']; ?>"/></td>
		</tr>
		<tr>
			<td>Molecular Weight:</td>
			<td colspan="3"><input name="molecularWeight" type="text" class="form-control" id="molecularWeight" value="<?php echo $ing['molecularWeight']; ?>"/></td>
		</tr>
		<tr>
			<td>Appearance:</td>
			<td colspan="3"><input name="appearance" type="text" class="form-control" id="appearance" value="<?php echo $ing['appearance']; ?>"/></td>
		</tr>
	</table>
<hr />
	<p><input type="submit" name="save" class="btn btn-info" id="saveTechData" value="Save" /></p>
    
<script>

$('#saveTechData').click(function() {
	$.ajax({ 
		url: 'update_data.php', 
		type: 'POST',
		data: {
			manage: 'ingredient',
			tab: 'tech_data',
			ingID: '<?=$ing['id'];?>',
			tenacity: $("#tenacity").val(),
			flash_point: $("#flash_point").val(),
			chemical_name: $("#chemical_name").val(),
			formula: $("#molecularFormula").val(),
			logp: $("#logp").val(),
			soluble: $("#soluble").val(),
			molecularWeight: $("#molecularWeight").val(),
			appearance: $("#appearance").val(),
			rdi: $("#rdi").val()
		},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>';
			}else{
				var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
			}
			$('#ingMsg').html(msg);
		}
	});
});

</script>

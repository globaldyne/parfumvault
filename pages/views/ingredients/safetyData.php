<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
if(!$_POST['ingID']){
	echo 'Invalid ID';
	return;
}

require(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');


$ingSafetyInfo = mysqli_query($conn, "SELECT GHS FROM ingSafetyInfo WHERE ingID = '".$_POST['ingID']."'");
while($safety_res = mysqli_fetch_array($ingSafetyInfo)){
	$safety[] = $safety_res;
}
$pictograms = mysqli_query($conn, "SELECT name,code FROM pictograms");
while($pictograms_res = mysqli_fetch_array($pictograms)){
	$pictogram[] = $pictograms_res;
}
?>

<h3>Safety Information</h3>
	<hr />
	<table width="100%" border="0">
		<tr>
			<td width="20%">Pictograms:</td>
			<td width="80%" colspan="3">
				<select name="pictogram" id="pictogram" class="form-control selectpicker" data-live-search="true">
					<option value="" disabled selected="selected">Choose Pictogram</option>
					<?php foreach($pictograms as $pictogram){?>
						<option data-content="<img class='img_ing_sel' src='/img/Pictograms/GHS0<?=$pictogram['code'];?>.png'><?=$pictogram['name'];?>" value="<?=$pictogram['code'];?>" <?php if($safety[0]['GHS']==$pictogram['code']) echo 'selected="selected"'; ?>></option>
					<?php } ?>
				</select>
			</td>
		</tr>
	</table>
	<hr />
	<p><input type="submit" name="save" class="btn btn-info" id="saveSafetyData" value="Save" /></p>

<script>
$('.selectpicker').selectpicker('refresh');

$('#safety_info').on('click', '[id*=saveSafetyData]', function () {
	$.ajax({ 
		url: 'update_data.php', 
		type: 'POST',
		data: {
			manage: 'ingredient',
			tab: 'safety_info',
			ingID: '<?=$_POST['ingID'];?>',
			pictogram: $("#pictogram").val(),
		},
		dataType: 'html',
		success: function (data) {
			$('#ingMsg').html(data);
		}
	});
});
</script>
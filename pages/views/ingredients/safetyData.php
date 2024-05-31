<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
if(!$_POST['ingID']){
	echo 'Invalid ID';
	return;
}

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');


$ingSafetyInfo = mysqli_query($conn, "SELECT id,ingID,GHS FROM ingSafetyInfo WHERE ingID = '".$_POST['ingID']."'");
while($safety_res = mysqli_fetch_array($ingSafetyInfo)){
	$safety[] = $safety_res;
}

$pictograms = mysqli_query($conn, "SELECT id,name,code FROM pictograms");
while($pictograms_res = mysqli_fetch_array($pictograms)){
	$pictogram[] = $pictograms_res;
}
?>
<style>
	.image-container {
		position: relative;
		display: inline-block;
	}
	.remove-icon {
		position: absolute;
		top: 5px;
		right: 5px;
		background-color: white;
		border-radius: 50%;
		padding: 5px;
		cursor: pointer;
		z-index: 10;
	}
</style>
<h3>Safety Information</h3>
<div class="row g-3 align-items-center">
    <div class="mt-5 mb-5 col-auto">
        <select name="pictogram" id="pictogram" class="form-control selectpicker" data-live-search="true">
            <option value="" disabled selected="selected">Choose a pictogram to add</option>
            <?php foreach($pictograms as $pictogram){?>
                <option data-content="<img class='img_ing_sel' src='/img/Pictograms/GHS0<?=$pictogram['code'];?>.png'><?=$pictogram['name'];?>" value="<?=$pictogram['code'];?>"></option>
            <?php } ?>
        </select>
    </div>
</div>
<div class="container" id="img_pictograms">

	<?php
        $column_count = 0;
        $total_columns = 9;
        
        echo '<div class="row">';
        foreach ($safety as $pict) {
            if ($column_count == $total_columns) {
                echo '</div><div class="row">';
                $column_count = 0;
            }
            echo '<div class="col-auto">';
			echo '<div class="image-container" id="image-container-' . $pict['GHS'] . '">';
            echo '<img src="/img/Pictograms/GHS0' . $pict['GHS'] . '.png" class="img-fluid" style="width: 150px; height: 150px;">';
            echo '<span class="remove-icon" id="removeImage-' . $pict['GHS'] . '" data-id="' . $pict['GHS'] . '">&times;</span>';
            echo '</div>';
            echo '</div>';
            $column_count++;
        }
        echo '</div>';
     ?>
        
      
     
  
</div>
<script>
$(document).ready(function() {

	$('.selectpicker').selectpicker('refresh');
	
	$('#safety_info').on('changed.bs.select',  function () {
		$.ajax({ 
			url: "update_data.php", 
			type: "POST",
			data: {
				manage: "ingredient",
				tab: "safety_info",
				ingID: "<?=$_POST['ingID'];?>",
				pictogram: $("#pictogram").val(),
				action: "add"
			},
			dataType: 'json',
			success: function (data) {
				if (data.success) {
					fetch_safety();
				}else{
					var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				}
		
				$('#ingMsg').html(msg);
			}
		});
	});
	
	$('#img_pictograms').on('click', '[id*=removeImage]', function() {
		var imageId = $(this).data('id');
		$.ajax({
			type: "POST",
			url: "update_data.php",
			data: { 
				manage: "ingredient",
				tab: "safety_info",
				ingID: "<?=$_POST['ingID'];?>",
				pictogram_id: imageId,
				action: "remove"
			},
			dataType: 'json',
			success: function(data) {
				if (data.success) {
					$('#image-container-' + imageId).remove();
				} else {
					var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
				}
		
				$('#ingMsg').html(msg);
			}
		});
	});
	
});
</script>
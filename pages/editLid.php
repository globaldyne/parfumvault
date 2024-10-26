<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');


$lid = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM lids WHERE id = '".$_GET['id']."'")); 
$doc = mysqli_fetch_array(mysqli_query($conn,"SELECT docData AS photo FROM documents WHERE ownerID = '".$lid['id']."' AND type = '5'"));
$sup = mysqli_query($conn, "SELECT id,name FROM ingSuppliers ORDER BY name ASC");
while ($suppliers = mysqli_fetch_array($sup)){
	    $supplier[] = $suppliers;
}
?>

<style>

.container {
  max-width: 100%;
  width: 100%;

}
</style>
<div class="container">
    <div class="text-center">
        <div id="lid_pic">
            <div class="loader"></div>
        </div>
    </div>
    <div id="lid-inf"></div>
    <p>
        Style
        <input class="form-control" name="lid-style" type="text" id="lid-style" value="<?=$lid['style']?>" />
    </p>
    <p>
        Colour
        <input class="form-control" name="lid-colour" type="text" id="lid-colour" value="<?=$lid['colour']?>" />
    </p>
    <p>
        Price
        <input class="form-control" name="lid-price" type="text" id="lid-price" value="<?=$lid['price']?>" />
    </p>
    <p>
        Pieces in stock
        <input class="form-control" name="lid-pieces" type="text" id="lid-pieces" value="<?=$lid['pieces']?>" />
    </p>
    <p>
        Supplier
        <select name="lid-supplier" id="lid-supplier" class="form-select"> <!-- form-control updated to form-select -->
            <option value="" selected></option>
            <?php foreach($supplier as $sup) { ?>
                <option value="<?php echo $sup['name'];?>" <?php echo ($lid['supplier']==$sup['name'])?"selected=\"selected\"":""; ?>>
                    <?php echo $sup['name'];?>
                </option>
            <?php } ?>
        </select>
    </p>
    <p>
        Supplier URL
        <input class="form-control" name="lid-supplier_link" type="text" id="lid-supplier_link" value="<?=$lid['supplier_link']?>" />
    </p>
    <p>
        Image
        <input type="file" name="lid_pic_file" id="lid_pic_file" class="form-control" />
    </p>
</div>
<div class="modal-footer">
    <input type="submit" name="button" class="btn btn-primary" id="lid-save" value="Save">
</div>

<script>
$(document).ready(function() {

	$('#lid_pic').html('<img class="img-profile-avatar" src="<?=$doc['photo']?: '/img/logo_def.png'; ?>">');
	
	$('#lid-save').click(function() {
		$.ajax({ 
			url: '/pages/update_data.php', 
			type: 'POST',
			data: {
				update_lid_data: 1,
				lid_id:  "<?=$lid['id']?>",
				style: $("#lid-style").val(),
				price: $("#lid-price").val(),
				colour: $("#lid-colour").val(),
				pieces: $("#lid-pieces").val(),
				supplier: $("#lid-supplier").val(),
				supplier_link: $("#lid-supplier_link").val(),
			},
			dataType: 'json',
			success: function (data) {
				if(data.success){
					var msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>'+data.success+'</div>';
				}else if( data.error){
					var msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>'+data.error+'</div>';
				}
				$('#lid-inf').html(msg);
			},
			error: function (xhr, status, error) {
				$('#lid-inf').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error+'</div>');
				
			}
		  });
	
		var fd = new FormData();
		var files = $('#lid_pic_file')[0].files;
	
		if(files.length > 0 ){
			fd.append('lid_pic',files[0]);
		
			$.ajax({ 
				url: '/pages/update_data.php?update_lid_pic=1&lid_id=<?=$lid['id']?>', 
				type: 'POST',
				data: fd,
				contentType: false,
				processData: false,
				cache: false,
				dataType: 'json',
				success: function (data) {
					if(data.success){
						$('#lid_pic').html('<img class="img-profile-avatar" src="'+data.success.lid_pic+'">');
					}else if( data.error){
						$('#lid-inf').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>'+data.error+'</div>');
					}
				},
				error: function (xhr, status, error) {
					$('#lid-inf').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error+'</div>');
				}
			  });
		}
	});
});

</script>
<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');


$accessory = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM inventory_accessories WHERE id = '".$_GET['id']."' AND owner_id = '$userID'")); 
$doc = mysqli_fetch_array(mysqli_query($conn,"SELECT docData AS photo FROM documents WHERE ownerID = '".$accessory['id']."' AND type = '5' AND owner_id = '$userID'"));
$sup = mysqli_query($conn, "SELECT id,name FROM ingSuppliers WHERE owner_id = '$userID' ORDER BY name ASC");
while ($suppliers = mysqli_fetch_array($sup)){
	    $supplier[] = $suppliers;
}
?>

<div class="container">
    <div class="text-center mb-4">
        <div id="accessory_pic">
            <div class="spinner-border" role="status"></div>
        </div>
    </div>
    
    <div id="accessory-inf"></div>
    
    <div class="mb-3">
        <label for="accessory-name" class="form-label">Name</label>
        <input type="text" class="form-control" name="accessory-name" id="accessory-name" value="<?=$accessory['name']?>" />
    </div>
    
	<div class="mb-3">
    	<label for="accessory" class="form-label">Accessory</label>
		<select name="accessory" id="accessory-type" class="form-select">
			<option value="Lid" <?= isset($accessory['accessory']) && $accessory['accessory'] == "Lid" ? 'selected' : '' ?>>Bottle lid</option>
			<option value="Ribbon" <?= isset($accessory['accessory']) && $accessory['accessory'] == "Ribbon" ? 'selected' : '' ?>>Ribbon</option>
			<option value="Packaging" <?= isset($accessory['accessory']) && $accessory['accessory'] == "Packaging" ? 'selected' : '' ?>>Packaging</option>
			<option value="Other" <?= isset($accessory['accessory']) && $accessory['accessory'] == "Other" ? 'selected' : '' ?>>Other</option>
		</select>
	</div>

    
    <div class="mb-3">
        <label for="accessory-price" class="form-label">Price</label>
        <input type="text" class="form-control" name="accessory-price" id="accessory-price" value="<?=$accessory['price']?>" />
    </div>
    
    <div class="mb-3">
        <label for="accessory-pieces" class="form-label">Pieces in stock</label>
        <input type="text" class="form-control" name="accessory-pieces" id="accessory-pieces" value="<?=$accessory['pieces']?>" />
    </div>
    
    <div class="mb-3">
        <label for="accessory-supplier" class="form-label">Supplier</label>
        <select name="accessory-supplier" id="accessory-supplier" class="form-select">
            <option value="" selected></option>
            <?php foreach($supplier as $sup) { ?>
                <option value="<?php echo $sup['name'];?>" <?php echo ($accessory['supplier']==$sup['name'])?"selected=\"selected\"":""; ?>>
                    <?php echo $sup['name'];?>
                </option>
            <?php } ?>
        </select>
    </div>
    
    <div class="mb-3">
        <label for="accessory-supplier_link" class="form-label">Supplier URL</label>
        <input type="text" class="form-control" name="accessory-supplier_link" id="accessory-supplier_link" value="<?=$accessory['supplier_link']?>" />
    </div>
    
    <div class="mb-3">
        <label for="accessory_pic_file" class="form-label">Image</label>
        <input type="file" name="accessory_pic_file" id="accessory_pic_file" class="form-control" />
    </div>
    
    <div class="modal-footer">
        <input type="submit" name="button" class="btn btn-primary" id="accessory-save" value="Save">
    </div>
</div>


<script>
$(document).ready(function() {

	$('#accessory_pic').html('<img class="img-profile-avatar" src="<?=$doc['photo']?: '/img/logo.png'; ?>">');
	
	$('#accessory-save').click(function() {
		$.ajax({ 
			url: '/core/core.php', 
			type: 'POST',
			data: {
				action: 'update_accessory_data',
				accessory_id:  "<?=$accessory['id']?>",
				name: $("#accessory-name").val(),
				price: $("#accessory-price").val(),
				accessory: $("#accessory-type").val(),
				pieces: $("#accessory-pieces").val(),
				supplier: $("#accessory-supplier").val(),
				supplier_link: $("#accessory-supplier_link").val(),
			},
			dataType: 'json',
			success: function (data) {
				if(data.success){
					var msg = '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>'+data.success+'</div>';
					$('#tdDataAccessories').DataTable().ajax.reload(null, true);
				}else if( data.error){
					var msg = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>'+data.error+'</div>';
				}
				$('#accessory-inf').html(msg);
			},
			error: function (xhr, status, error) {
				$('#accessory-inf').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error+'</div>');
				
			}
		  });
	
		var fd = new FormData();
		var files = $('#accessory_pic_file')[0].files;
	
		if(files.length > 0 ){
			fd.append('accessory_pic',files[0]);
		
			$.ajax({ 
				url: '/core/core.php?action=update_accessory_pic&accessory_id=<?=$accessory['id']?>', 
				type: 'POST',
				data: fd,
				contentType: false,
				processData: false,
				cache: false,
				dataType: 'json',
				success: function (data) {
					if(data.success){
						$('#accessory_pic').html('<img class="img-profile-avatar" src="'+data.success.accessory_pic+'">');
						$('#tdDataAccessories').DataTable().ajax.reload(null, true);
					}else if( data.error){
						$('#accessory-inf').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>'+data.error+'</div>');
					}
				},
				error: function (xhr, status, error) {
					$('#accessory-inf').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>An ' + status + ' occurred, check server logs for more info. '+ error+'</div>');
				}
			  });
		}
	});
});

</script>

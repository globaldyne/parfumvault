<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');


$bottle = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM bottles WHERE id = '".$_GET['id']."'")); 
$doc = mysqli_fetch_array(mysqli_query($conn,"SELECT docData AS photo FROM documents WHERE ownerID = '".$bottle['id']."' AND type = '4'"));
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
          <div id="bottle_pic"><div class="loader"></div></div>
       </div>
       <div id="bottle-inf"></div>
        <p>
        Name: 
          <input class="form-control" name="bottle-name" type="text"  id="bottle-name" value="<?=$bottle['name']?>" />
        </p>
        <p>            
        Size (ml):
          <input class="form-control" name="bottle-size" type="text" id="bottle-size" value="<?=$bottle['ml']?>"/>
        </p>
        <p>
        Price:
          <input class="form-control" name="bottle-price" type="text" id="bottle-price" value="<?=$bottle['price']?>"/>
        </p>
 		<p>
        Height:
          <input class="form-control" name="bottle-height" type="text" id="bottle-height" value="<?=$bottle['height']?>"/>
        </p>
        <p>
        Width:
          <input class="form-control" name="bottle-width" type="text" id="bottle-width" value="<?=$bottle['width']?>"/>
        </p>
        <p>
        Diameter:
          <input class="form-control" name="bottle-diameter" type="text" id="bottle-diameter" value="<?=$bottle['diameter']?>"/>
        </p>
        <p>
        Stock (pieces):
          <input class="form-control" name="bottle-pieces" type="text" id="bottle-pieces" value="<?=$bottle['pieces']?>"/>
        </p>        
        <p>
        Notes:
          <input class="form-control" name="bottle-notes" type="text" id="bottle-notes" value="<?=$bottle['notes']?>"/>
        </p>
        <p>
        Supplier:
          <select name="bottle-supplier" id="bottle-supplier" class="form-control">
            <option value="" selected></option>
            <?php foreach($supplier as $sup) { ?>
				<option value="<?php echo $sup['name'];?>" <?php echo ($bottle['supplier']==$sup['name'])?"selected=\"selected\"":""; ?>><?php echo $sup['name'];?></option>
		    <?php } ?>
          </select>
        </p>
        <p>
        Supplier URL:
          <input class="form-control" name="bottle-supplier_link" type="text" id="bottle-supplier_link" value="<?=$bottle['supplier_link']?>"/>
        </p>
        <p>
        Image:
        <input type="file" name="bottle_pic_file" id="bottle_pic_file" class="form-control" />
    	</p>            
        <div class="dropdown-divider"></div>
</div>
      <div class="modal-footer">
        <input type="submit" name="button" class="btn btn-primary" id="bottle-save" value="Save">
      </div>
    </div>  
<hr>
<script>
$('#bottle_pic').html('<img class="img-profile-avatar" src="<?=$doc['photo']?: '/img/logo_def.png'; ?>">');

$('#bottle-save').click(function() {
	$.ajax({ 
		url: '/pages/update_data.php', 
		type: 'POST',
		data: {
			update_bottle_data: 1,
			bottle_id:  "<?=$bottle['id']?>",
			name: $("#bottle-name").val(),			
			size: $("#bottle-size").val(),
			price: $("#bottle-price").val(),
			height: $("#bottle-height").val(),
			width: $("#bottle-width").val(),
			diameter: $("#bottle-diameter").val(),
			pieces: $("#bottle-pieces").val(),
			notes: $("#bottle-notes").val(),
			supplier: $("#bottle-supplier").val(),
			supplier_link: $("#bottle-supplier_link").val(),
			},
		dataType: 'json',
		success: function (data) {
			if(data.success){
				var msg = '<div class="alert alert-success">'+data.success+'</div>';
			}else if( data.error){
				var msg = '<div class="alert alert-danger">'+data.error+'</div>';
			}
			$('#bottle-inf').html(msg);
		}
	  });

	var fd = new FormData();
    var files = $('#bottle_pic_file')[0].files;

    if(files.length > 0 ){
		fd.append('bottle_pic',files[0]);
	}
	$.ajax({ 
		url: '/pages/update_data.php?update_bottle_pic=1&bottle_id=<?=$bottle['id']?>', 
		type: 'POST',
		data: fd,
		contentType: false,
      	processData: false,
		cache: false,
		dataType: 'json',
		success: function (data) {
			if(data.success){
				$('#bottle_pic').html('<img class="img-profile-avatar" src="'+data.success.bottle_pic+'">');
			}else if( data.error){
				$('#bottle-inf').html('<div class="alert alert-danger">'+data.error+'</div>');
			}
		}
	  });
});
</script>